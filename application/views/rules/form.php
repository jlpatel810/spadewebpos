<?php $this->load->view("partial/header"); ?>
<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('rules/save/'.$rule_data->rule_id, array('id'=>'rules_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="customer_basic_info" class='cl'>
		<?php // $this->load->view("form/form_basic_info"); ?>
 
 
<div class="form-group form-group-sm"> 
			
				<?php 
				//$applydata['by_percent']='Percent of product price discount';
				$applydata['by_fixed']='Fixed amount discount';
				$applydata['buy_x_get_y']='Buy X for Y (qty is X and amount is Y)';
				
				?>
				<label for="Apply" class=" control-label col-xs-3" aria-required="true">Apply</label>
				
				<div class='col-xs-8'>
					<?php echo form_dropdown('apply', $applydata, $rule_data->apply, array('class'=>'form-control','id'=>'apply')); ?>
				</div>

		</div>
		
		
		 
		<div class="form-group form-group-sm"> 
		
			<label for="rule_name" class="required control-label col-xs-3" aria-required="true">Rule Name *</label>
			
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'rule_name',
						'class'=>'form-control input-sm',
						'value'=>$rule_data->rule_name)
						);?>
			</div>
		</div>
		
		<div class="form-group form-group-sm"> 
		
		<label id='discount_amount1' for="discount_amount" class="required control-label col-xs-3" aria-required="true">Discount Amount ($) *</label>
		<label id='discount_amount2' for="discount_amount" class="required control-label col-xs-3" aria-required="true">Total Amount (Y) *</label>
			
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'discount_amount',
						'class'=>'form-control input-sm',
						'value'=>$rule_data->discount_amount)
						);?>
			</div>
		</div>
		
		<!--<div class="form-group form-group-sm"> 
		
			<label for="discount_amount" class="control-label col-xs-3" aria-required="true">Maximum Qty Discount *</label>
			
			<div class='col-xs-8'>
				 <?php 
				/*echo form_input(array(
						'name'=>'rule_discount_qty',
						'class'=>'form-control input-sm',
						'value'=>$rule_data->rule_discount_qty)
						);*/
				?>
			</div>
		</div>-->
		
		
		<div class="form-group form-group-sm"> 
		
			<label id='noofqty_1' for="discount_amount" class="required control-label col-xs-3" aria-required="true">Discount Qty Step (Buy X)</label>
			<label id='noofqty_2' for="discount_amount" class="required control-label col-xs-3" aria-required="true">No of QTY (X)</label>
			
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'x_discount_qty',
						'class'=>'form-control input-sm',
						'value'=>$rule_data->x_discount_qty)
						);?>
			</div>
		</div>
		
		<div class="form-group form-group-sm" id='deals_items' style='display:none;' > 
		
			<label for="rule_name" class="control-label col-xs-3" aria-required="true">Items on deals</label>
			
			<div class='col-xs-8'>
				<input type="text" name="items_on_deals" value="<?php  echo $rule_data->items_on_deals; ?>" class="form-control input-sm">
				<p>items id is in comma seperated. EX: 101,999,201</p>
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
		<a class="btn btn-primary" href='<?php echo site_url($controller_name."/"); ?>'>back</a>
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
			rule_name: "required",
			discount_amount: "required",
			x_discount_qty: "required"
			
			
    		
    		
   		},
		messages: 
		{
     		rule_name: "Please enter rule name",
     		discount_amount: "Please enter amount",
     		x_discount_qty: "Please enter Qty "
     		
		}
	}, form_support.error));
});




$('#apply').on('change', function() {
	if( this.value == 'buy_x_get_y' )
	{
		$('#deals_items').show();
		
		$('#discount_amount2').show();
		$('#discount_amount1').hide();
		
		
$('#noofqty_2').show();
$('#noofqty_1').hide();

		
	}
	else 
	{
		$('#deals_items').hide();
		$('#discount_amount2').hide();
		$('#discount_amount1').show();
		
		$('#noofqty_2').hide();
		$('#noofqty_1').show();


	}
})


<?php if($rule_data->apply=='buy_x_get_y'){ ?>  

$('#deals_items').show();
$('#discount_amount2').show();
$('#discount_amount1').hide();


$('#noofqty_2').show();
$('#noofqty_1').hide();




<?php } else { ?>

$('#deals_items').hide();
$('#discount_amount2').hide();
$('#discount_amount1').show();

$('#noofqty_2').hide();
$('#noofqty_1').show();







<?php }  ?>


</script>

<?php $this->load->view("partial/footer"); ?>
