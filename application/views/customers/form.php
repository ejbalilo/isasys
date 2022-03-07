<?php if($this->Employee->has_permission('customers_edit',$this->Employee->get_logged_in_employee_info()->person_id)) $canedit=true; else $canedit=false; ?>
<?php
if($canedit || $person_id==-1) echo form_open('customers/save/'.$person_info->person_id,array('id'=>'customer_form'));
?>
<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
<ul id="error_message_box"></ul>
<fieldset id="customer_basic_info">
<legend><?php echo $this->lang->line("customers_basic_information"); ?></legend>
<div class="field_row clearfix">
<?php echo form_label('Classification:', 'classification', array('class'=>'required')); ?>
	<div class='form_field'>
	<?php echo form_dropdown('classification_id', $classifications, $person_info->classification_id);?>
	</div>
</div>

<?php $this->load->view("people/form_basic_info"); ?>

<div class="field_row clearfix">	
<?php echo form_label($this->lang->line('customers_account_number').':', 'account_number'); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'name'=>'account_number',
		'id'=>'account_number',
		'value'=>$person_info->account_number)
	);?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label('Account Officer:', 'account_officer'); ?>
	<div class='form_field'>
	<?php echo form_dropdown('account_officer_id', $all_customers, $person_info->account_officer_id);?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label('Sales Agent:', 'sales_agent'); ?>
	<div class='form_field'>
	<?php echo form_dropdown('sales_agent_id', $all_customers, $person_info->sales_agent_id);?>
	</div>
</div>

<div class="field_row clearfix">	
<?php echo form_label('Sales Agent Commission:', 'sales_agent_commission'); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'name'=>'sales_agent_commission',
		'id'=>'sales_agent_commission',
		'value'=>$person_info->sales_agent_commission)
	);?>
	</div>
</div>

<div class="field_row clearfix">	
<?php echo form_label('Discount %:', 'customer_discount'); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'name'=>'customer_discount',
		'id'=>'customer_discount',
		'value'=>$person_info->customer_discount)
	);?>
	</div>
</div>

<div class="field_row clearfix">	
<?php echo form_label($this->lang->line('customers_taxable').':', 'taxable'); ?>
	<div class='form_field'>
	<?php echo form_checkbox('taxable', '1', $person_info->taxable == '' ? TRUE : (boolean)$person_info->taxable);?>
	</div>
</div>

<?php
if($canedit || $person_id==-1) echo form_submit(array('name'=>'submit','id'=>'submit','value'=>$this->lang->line('common_submit'),'class'=>'submit_button float_right'));
?>
</fieldset>
<?php 
if($canedit || $person_id==-1) echo form_close();
?>
<script type='text/javascript'>

//validation and submit handling
$(document).ready(function()
{
	$('#customer_form').validate({
		submitHandler:function(form)
		{
			$('#submit').val('Please Wait...');
			$('#submit').attr('disabled',true);
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_person_form_submit(response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules: 
		{
			first_name: "required",
			last_name: "required",
    		email: "email"
   		},
		messages: 
		{
     		first_name: "<?php echo $this->lang->line('common_first_name_required'); ?>",
     		last_name: "<?php echo $this->lang->line('common_last_name_required'); ?>",
     		email: "<?php echo $this->lang->line('common_email_invalid_format'); ?>"
		}
	});
});
</script>