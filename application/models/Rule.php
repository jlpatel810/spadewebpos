<?php
class Rule extends CI_Model
{	
	/*
	Determines if a given person_id is a customer
	*/
	public function exists($person_id)
	{
		$this->db->from('rules');	
		
		$this->db->where('rules.rule_id', $person_id);
		
		return ($this->db->get()->num_rows() == 1);
	}
	
	public function save_data(&$person_data, $rule_id = FALSE)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
	
			if(!$rule_id || !$this->exists($rule_id))
			{				
				$success = $this->db->insert('rules', $person_data);
			}
			else
			{
				$this->db->where('rule_id', $rule_id);
				$success = $this->db->update('rules', $person_data);
			}
		
		
		$this->db->trans_complete();
		
		$success &= $this->db->trans_status();

		return $success;
	}

	/*
	Checks if account number exists
	*/
	public function account_number_exists($account_number, $person_id = '')
	{
		$this->db->from('rules');
		$this->db->where('account_number', $account_number);

		if(!empty($person_id))
		{
			$this->db->where('person_id !=', $person_id);
		}

		return ($this->db->get()->num_rows() == 1);
	}	

	/*
	Gets total of rows
	*/
	public function get_total_rows()
	{
		$this->db->from('rules');
		$this->db->where('deleted', 0);

		return $this->db->count_all_results();
	}
	
	/*
	Returns all the rules
	*/
	public function get_all($rows = 0, $limit_from = 0)
	{
		$this->db->from('rules');
		//$this->db->join('people', 'rules.person_id = people.person_id');			
		$this->db->where('deleted', 0);
		//$this->db->order_by('last_name', 'asc');

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();		
	}
	
	/*
	Gets information about a particular customer
	*/
	public function get_info($rule_id)
	{
		
		
		
		$this->db->select('*');
		$this->db->from('rules');		
		
		$this->db->where('rules.rule_id', $rule_id);
		
		
		$query = $this->db->get();
		
		if($query->num_rows() == 1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $customer_id is NOT a customer
			$person_obj = parent::get_info(-1);
			
			//Get all the fields from customer table
			//append those fields to base parent object, we we have a complete empty object
			foreach($this->db->list_fields('rules') as $field)
			{
				$person_obj->$field = '';
			}
			
			return $person_obj;
		}
	}
	
	/*
	Gets total about a particular customer
	*/
	public function get_totals($customer_id)
	{
		$this->db->select('SUM(payment_amount) AS total');
		$this->db->from('sales');
		$this->db->join('sales_payments', 'sales.sale_id = sales_payments.sale_id');
		$this->db->where('sales.customer_id', $customer_id);

		return $this->db->get()->row();
	}
	
	/*
	Gets information about multiple rules
	*/
	public function get_multiple_info($customer_ids)
	{
		$this->db->from('rules');
		//$this->db->join('people', 'people.person_id = rules.person_id');		
		$this->db->where_in('rules.rule_id', $customer_ids);
		//$this->db->order_by('last_name', 'asc');

		return $this->db->get();
	}
	
	/*
	Inserts or updates a customer
	*/
	public function save_customer(&$person_data, &$customer_data, $customer_id = FALSE)
	{
		$success = FALSE;

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		
		if(parent::save($person_data, $customer_id))
		{
			if(!$customer_id || !$this->exists($customer_id))
			{
				$customer_data['person_id'] = $person_data['person_id'];
				$success = $this->db->insert('rules', $customer_data);
			}
			else
			{
				$this->db->where('person_id', $customer_id);
				$success = $this->db->update('rules', $customer_data);
			}
		}
		
		$this->db->trans_complete();
		
		$success &= $this->db->trans_status();

		return $success;
	}
	
	/*
	Deletes one customer
	*/
	public function delete($customer_id)
	{
		$this->db->where('rule_id', $customer_id);

		return $this->db->update('rules', array('deleted' => 1));
	}
	
	/*
	Deletes a list of rules
	*/
	public function delete_list($customer_ids)
	{
		$this->db->where_in('rule_id', $customer_ids);

		return $this->db->delete('rules');   
 	}
 	
 	/*
	Get search suggestions to find rules
	*/
	public function get_search_suggestions($search, $unique = TRUE, $limit = 25)
	{
		$suggestions = array();
		
		$this->db->from('rules');
		$this->db->join('people', 'rules.person_id = people.person_id');
		$this->db->group_start();		
			$this->db->like('first_name', $search);
			$this->db->or_like('last_name', $search); 
			$this->db->or_like('CONCAT(first_name, " ", last_name)', $search);
		$this->db->group_end();
		$this->db->where('deleted', 0);
		$this->db->order_by('last_name', 'asc');
		foreach($this->db->get()->result() as $row)
		{
			$suggestions[] = array('value' => $row->person_id, 'label' => $row->first_name.' '.$row->last_name);
		}

		if(!$unique)
		{
			$this->db->from('customers');
			$this->db->join('people', 'customers.person_id = people.person_id');
			$this->db->where('deleted', 0);
			$this->db->like('email', $search);
			$this->db->order_by('email', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->email);
			}

			$this->db->from('customers');
			$this->db->join('people', 'customers.person_id = people.person_id');
			$this->db->where('deleted', 0);
			$this->db->like('phone_number', $search);
			$this->db->order_by('phone_number', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->phone_number);
			}

			$this->db->from('customers');
			$this->db->join('people', 'customers.person_id = people.person_id');
			$this->db->where('deleted', 0);
			$this->db->like('account_number', $search);
			$this->db->order_by('account_number', 'asc');
			foreach($this->db->get()->result() as $row)
			{
				$suggestions[] = array('value' => $row->person_id, 'label' => $row->account_number);
			}
		}
		
		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0, $limit);
		}

		return $suggestions;
	}

 	/*
	Gets rows
	*/
	public function get_found_rows($search)
	{
		$this->db->select('*');
		$this->db->from('rules');		
		$this->db->like('rule_name', $search);
		$this->db->where('deleted', 0);

		return $this->db->get()->num_rows();
	}
	
	/*
	Performs a search on customers
	*/
	public function search($search, $rows = 0, $limit_from = 0, $sort = 'last_name', $order = 'asc')
	{
		$this->db->select('*');
		$this->db->from('rules');		
		
			$this->db->like('rule_name', $search);
			
		
		$this->db->where('deleted', 0);
		$this->db->order_by($sort, $order);

		if($rows > 0)
		{
			$this->db->limit($rows, $limit_from);
		}

		return $this->db->get();	
	}
	
	public function loaddata()
	{
		$this->db->select('*');
		$this->db->from('rules');		
		
			//$this->db->like('rule_name', $search);
			
		
		$this->db->where('deleted', 0);
		$this->db->where('status', 1);
		$this->db->order_by('rule_name', 'asc');
		

		return $this->db->get()->result();	
	}
	
	public function loadselecteddata($item_id=0)
	{
		$this->db->select('*');
		$this->db->from('items_rule');	
		$this->db->where('item_id', $item_id);			
		return $this->db->get()->result();	
		
	}
}
?>
