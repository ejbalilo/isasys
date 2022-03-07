<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
<ul id="error_message_box"></ul>
<?php
echo form_open('receivables/save2/'.$receivable_info->receivable_id,array('id'=>'receivable_form'));
?>
<fieldset id="receivable_info">
<legend><?php echo $this->lang->line("receivables_info"); ?></legend>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_period').':','period',array('class'=>'wide required')); ?>
	<div class='form_field'><?php
	echo form_input(array('name'=>'period','id'=>'period','value'=>$receivable_info->period));
	?></div>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_customer_name').':', 'customer',array('class'=>'wide required')); ?>
	<div class='form_field'>
	<?php if($receivable_info->customer_id=='') { ?>
	<?php echo form_input(array(
		'name'=>'customer',
		'id'=>'customer'
	)); ?>
	<br />
	<?php echo form_hidden('customer_id" id="customer_id',$receivable_info->customer_id!=''?$receivable_info->customer_id:''); } ?>
	<?php echo form_input(array(
		'name'=>'customer_name',
		'id'=>'customer_name',
		'value'=>(isset($receivable_info->first_name)?($receivable_info->first_name.' '.$receivable_info->last_name):''),
		'disabled'=>'disabled'
	));?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_balance').':','balance',array('class'=>'wide required')); ?>
	<div class='form_field'>
	<?php
		$input = array('name'=>'balance','id'=>'balance');
		if($receivable_info->receivable_id!='')
			$input = array_merge($input,array('value'=>$receivable_info->balance,'disabled'=>'disabled'));
		echo form_input($input);
	?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_comments').':', 'comments',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_textarea(array(
		'name'=>'comments',
		'id'=>'comments',
		'value'=>$receivable_info->comments,
		'rows'=>'5',
		'cols'=>'18')
	);?>
	</div>
</div>

<?php
echo form_submit(array(
	'name'=>'submit',
	'id'=>'submit',
	'value'=>$this->lang->line('common_save'),
	'class'=>'submit_button float_right')
);
?>
</fieldset>

<?php
echo form_close();
if($receivable_info->receivable_id!='') { ?>

<ul id="error_message_box2"></ul>
<?php
echo form_open('receivables/add_payment/'.$receivable_info->receivable_id.'/1/',array('id'=>'payment_form'));
?>
<center>
<fieldset id="receivable_info" style="width:340px">
<legend><?php echo $this->lang->line("receivables_payment_info"); ?></legend>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('common_amount').':', 'amount',array('class'=>'wide required')); ?>
	<div class='form_field'>
		<?php echo form_input(array('name'=>'amount','id'=>'amount'));?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_comments').':', 'comments',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_textarea(array(
		'name'=>'comments',
		'id'=>'comments',
		'rows'=>'2',
		'cols'=>'18')
	);?>
	</div>
</div>
<?php
echo form_submit(array(
	'name'=>'submit',
	'id'=>'submit',
	'value'=>$this->lang->line('receivables_payment_add'),
	'class'=>'submit_button float_right')
);
?><br /><br /><br />
<center><table id="receivable_payments" border="1" cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->lang->line('receivables_date');?></th>
		<th><?php echo $this->lang->line('common_amount');?></th>
		<th><?php echo $this->lang->line('receivables_comments');?></th>
	</tr>
	
	<?php foreach ($this->Receivable_payments->get_info($receivable_info->receivable_id,1) as $receivable_payment) { ?>
		<tr>
			<td><?php echo $receivable_payment['payment_date']; ?></td>
			<td style="text-align:right"><?php echo $receivable_payment['payment_amount']; ?></td>
			<td><?php echo $receivable_payment['comments']; ?></td>
		</tr>
	<?php } ?>
</table>
<?php
$total_receivable_amount = $this->Receivable_items->get_total_receivable_amount($receivable_info->receivable_id,date("U")+86400);
$total_payments = $this->Receivable_payments->get_total_payments($receivable_info->receivable_id,1);
?>
<br />
<table>
<tr><td><b>Total Receivable Amount:</b></td><td style="text-align:right"><?php echo to_currency($receivable_info->balance) ?></td></tr>
<tr><td><b>Total Payment:</b></td><td style="text-align:right"><?php echo to_currency($total_payments) ?></td></tr>
<tr><td><b>Remaining Balance:</b></td><td style="text-align:right"><?php echo to_currency($receivable_info->balance-$total_payments) ?></td></tr>
</table>
</center>
</fieldset>
</center>
<?php
echo form_close();
} ?>

<script type='text/javascript'>
$("#customer").autocomplete('<?php echo site_url("receivables/customer_search"); ?>',
{
	minChars:0,
	delay:10,
	max:100,
	formatItem: function(row) {
		return row[1];
	}
});

$("#customer").result(function(event, data, formatted)
{
	$("#customer").val("");
	$("#customer_id").val(data[0]);
	$("#customer_name").val(data[1]);
});

$('#customer').blur(function()
{
	$(this).attr('value','');
});

$('#customer').click(function()
{
	$(this).attr('value','');
});

//validation and submit handling
$(document).ready(function()
{
	$('#receivable_form').validate({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_receivable_form_submit2(response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules:
		{
			period:"required",
			customer_id:"required",
			balance:"required"
		},
		messages:
		{
			period:"<?php echo $this->lang->line('receivables_period_required'); ?>",
			customer_id:"<?php echo $this->lang->line('receivables_customer_required'); ?>",
			balance:"<?php echo $this->lang->line('receivables_balance_required'); ?>"
		}
	});
	$('#payment_form').validate({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_receivable_form_submit2(response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box2",
 		wrapper: "li",
		rules:
		{
			amount:"required"
		},
		messages:
		{
			amount:"<?php echo $this->lang->line('receivables_amount_required'); ?>"
		}
	});
});

function deleteItemKitRow(link)
{
	$(link).parent().parent().remove();
	return false;
}
</script>