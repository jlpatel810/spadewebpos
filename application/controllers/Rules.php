<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Persons.php");

class Rules extends Persons
{
	public function __construct()
	{
		parent::__construct('Rules');
		//
	}
	
	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_rules_manage_table_headers()); 
		

		$this->load->view('people/manage', $data);
	}
	
	/*
	Returns customer table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		 $search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$rules = $this->Rule->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Rule->get_found_rows($search);

		$data_rows = array();
		foreach($rules->result() as $row)
		{
			
			
			$data_rows[] = get_rule_data_row($row, $this); 
		}
		
		$data_rows = $this->xss_clean($data_rows);

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	public function suggest()
	{
		$suggestions = $this->xss_clean($this->Customer->get_search_suggestions($this->input->get('term'), TRUE));

		echo json_encode($suggestions);
	}

	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Customer->get_search_suggestions($this->input->post('term'), FALSE));

		echo json_encode($suggestions);
	}
	
	/*
	Loads the customer edit form
	*/
	public function view($customer_id = -1)
	{
		$info='';
		if($customer_id !=-1){
				$info = $this->Rule->get_info($customer_id);
	
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}
			}
		$data['rule_data'] = $info;

		$data['total'] = $this->xss_clean($this->Rule->get_totals($customer_id)->total);

		$this->load->view("rules/form", $data);
	}
	
	/*
	Inserts/updates a customer
	*/
	public function save($rule_id = -1)
	{
		
		
		//echo in_array('Customer', $this->_ci_models, TRUE);
		
		
		$person_data = array(
			'rule_name' => $this->input->post('rule_name'),			
			'apply' => $this->input->post('apply'),
			'discount_amount' => $this->input->post('discount_amount'),
			'rule_discount_qty' => 0,
			'x_discount_qty' => $this->input->post('x_discount_qty'),						
			'items_on_deals' => $this->input->post('items_on_deals'),						
			'status' => $this->input->post('status')	 					
		);
		
	//echo $rule_id; print_r($person_data); die;
	
	
		if($this->Rule->save_data($person_data,$rule_id))
		{
			$person_data = $this->xss_clean($person_data);
			
			
			//New customer
			if($rule_id == -1)
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('customers_successful_adding').' '.
								$person_data['first_name'].' '.$person_data['last_name'], 'id' => $customer_data['person_id']));
			}
			else //Existing customer
			{
				echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('customers_successful_updating').' '.
								$person_data['first_name'].' '.$person_data['last_name'], 'id' => $rule_id));
			}
		}
		else//failure
		{
			$person_data = $this->xss_clean($person_data);

			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_error_adding_updating').' '.
							$person_data['first_name'].' '.$person_data['last_name'], 'id' => -1));
		} 
	}  
	
	public function check_account_number()
	{
		$exists = $this->Customer->account_number_exists($this->input->post('account_number'), $this->input->post('person_id'));

		echo !$exists ? 'true' : 'false';
	}
	
	/*
	This deletes customers from the customers table
	*/
	public function delete()
	{
		$customers_to_delete = $this->xss_clean($this->input->post('ids'));

		if($this->Rule->delete_list($customers_to_delete)) 
		{
			echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('customers_successful_deleted').' '.
							count($customers_to_delete).' '.$this->lang->line('customers_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_cannot_be_deleted')));
		}
	}

	/*
	Customers import from excel spreadsheet
	*/
	public function excel()
	{
		$name = 'import_customers.csv';
		$data = file_get_contents('../' . $name);
		force_download($name, $data);
	}
	
	public function excel_import()
	{
		$this->load->view('rules/form_excel_import', NULL);
	}

	public function do_excel_import()
	{
		if($_FILES['file_path']['error'] != UPLOAD_ERR_OK)
		{
			echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_excel_import_failed')));
		}
		else
		{
			if(($handle = fopen($_FILES['file_path']['tmp_name'], 'r')) !== FALSE)
			{
                // Skip the first row as it's the table description
				fgetcsv($handle);
				$i = 1;

				$failCodes = array();

				while(($data = fgetcsv($handle)) !== FALSE) 
				{
					// XSS file data sanity check
					$data = $this->xss_clean($data);

					if(sizeof($data) >= 15)
					{
						$person_data = array(
							'first_name'	=> $data[0],
							'last_name'		=> $data[1],
							'gender'		=> $data[2],
							'email'			=> $data[3],
							'phone_number'	=> $data[4],
							'address_1'		=> $data[5],
							'address_2'		=> $data[6],
							'city'			=> $data[7],
							'state'			=> $data[8],
							'zip'			=> $data[9],
							'country'		=> $data[10],
							'comments'		=> $data[11]
						);
						
						$customer_data = array(
							'company_name'		=> $data[12],
							'discount_percent'	=> $data[14],
							'taxable'			=> $data[15] == '' ? 0 : 1
						);
						
						$account_number = $data[13];
						$invalidated = FALSE;
						if($account_number != '') 
						{
							$customer_data['account_number'] = $account_number;
							$invalidated = $this->Customer->account_number_exists($account_number);
						}
					}
					else 
					{
						$invalidated = TRUE;
					}

					if($invalidated || !$this->Customer->save_customer($person_data, $customer_data))
					{	
						$failCodes[] = $i;
					}
					
					++$i;
				}
				
				if(count($failCodes) > 0)
				{
					$message = $this->lang->line('customers_excel_import_partially_failed') . ' (' . count($failCodes) . '): ' . implode(', ', $failCodes);
					
					echo json_encode(array('success' => FALSE, 'message' => $message));
				}
				else
				{
					echo json_encode(array('success' => TRUE, 'message' => $this->lang->line('customers_excel_import_success')));
				}
			}
			else 
			{
                echo json_encode(array('success' => FALSE, 'message' => $this->lang->line('customers_excel_import_nodata_wrongformat')));
			}
		}
	}
}
?>