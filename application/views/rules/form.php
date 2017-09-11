<?php $this->load->view("partial/header"); ?>
<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('rules/save/'.$rule_data->rule_id, array('id'=>'rules_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="customer_basic_info" class='cl'>
		<?php // $this->load->view("form/form_basic_info"); ?>
 
		<div class="form-group form-group-sm"> 
		
			<label for="rule_name" class="required control-label col-xs-3" aria-required="true">Rule Name</label>
			
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'rule_name',
						'class'=>'form-control input-sm',
						'value'=>$rule_data->rule_name)
						);?>
			</div>
		</div>
		<div class="form-group form-group-sm"> 
			
				<?php 
				$applydata['by_percent']='Percent of product price discount';
				$applydata['by_fixed']='Fixed amount discount';
				$applydata['buy_x_get_y']='Buy X get Y discount (discount amount is Y)';
				
				?>
				<label for="Apply" class="required control-label col-xs-3" aria-required="true">Apply</label>
				
				<div class='col-xs-8'>
					<?php echo form_dropdown('apply', $applydata, $rule_data->apply, array('class'=>'form-control')); ?>
				</div>

		</div>
		
		<div class="form-group form-group-sm"> 
		
			<label for="discount_amount" class="control-label col-xs-3" aria-required="true">Discount Amount</label>
			
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'discount_amount',
						'class'=>'form-control input-sm',
						'value'=>$rule_data->discount_amount)
						);?>
			</div>
		</div>
		
		<div class="form-group form-group-sm"> 
		
			<label for="discount_amount" class="control-label col-xs-3" aria-required="true">Maximum Qty Discount</label>
			
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'rule_discount_qty',
						'class'=>'form-control input-sm',
						'value'=>$rule_data->rule_discount_qty)
						);?>
			</div>
		</div>
		
		
		<div class="form-group form-group-sm"> 
		
			<label for="discount_amount" class="control-label col-xs-3" aria-required="true">Discount Qty Step (Buy X)</label>
			
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'x_discount_qty',
						'class'=>'form-control input-sm',
						'value'=>$rule_data->x_discount_qty)
						);?>
			</div>
		</div>
		
		
		
		
		<div class="form-group form-group-sm"> 
			
				<?php 
				$statusdata['1']='Active';
				$statusdata['0']='Inactive'; 
				
				
				?>
				<label for="status" class="control-label col-xs-3" aria-required="true">Status</label>
				
				<div class='col-xs-8'>
					<?php echo form_dropdown('status', $statusdata, $rule_data->status, array('class'=>'form-control')); ?>
				</div>

		</div>
		
		
		<div class="form-group form-group-sm"> 
		<label for="rule_name" class="required control-label col-xs-3" aria-required="true"></label>
		<div class='col-xs-8'>
		<button class="btn btn-primary" id="submit">Submit</button>
		<a class="btn btn-primary" href=''>back</a>
		</div>
		</div>
		
	</fieldset>
<?php echo form_close(); ?>

<script type="text/javascript"> 

//validation and submit handling
$(document).ready(function()
{
	$('#rules_form').validate($.extend({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
				success:function(response)
				{
					<?php if($rule_data->rule_id){ ?>
					alert("Deal has been updated");
					<?php } else { ?>
					alert("Deal has been added");
					<?php }  ?>
					//dialog_support.hide();
					//table_support.handle_submit('<?php echo site_url($controller_name); ?>', response);
					window.open('<?php echo site_url($controller_name); ?>','_self');
				},
				dataType:'json'
			});
		},
		rules:
		{
			rule_name: "required"
			
    		
    		
   		},
		messages: 
		{
     		rule_name: "Please enter rule name" 
     		
		}
	}, form_support.error));
});
</script>

<?php $this->load->view("partial/footer"); ?>
