<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
<ul id="error_message_box"></ul>

<fieldset id="receivable_info">
<legend><?php echo $this->lang->line("receivables_info"); ?></legend>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_id').':', 'id',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'value'=>$receivable_info->itr_number!=''?$receivable_info->itr_number:'[S.O. REQUEST]',
		'disabled'=>'disabled')
	);?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_date').':', 'timestamp',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'value'=>$receivable_info->receivable_date!=''?$receivable_info->receivable_date:date("Y-m-d H:i:s"),
		'disabled'=>'disabled')
	);?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_customer_name').':', 'customer',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'value'=>(isset($receivable_info->first_name)?($receivable_info->first_name.' '.$receivable_info->last_name):''),
		'disabled'=>'disabled')
	);?>
	</div>
</div>

</fieldset>
<?php if($receivable_info->status>0 && $this->Employee->has_permission('receivables_payments',$this->Employee->get_logged_in_employee_info()->person_id)) { ?>

<ul id="error_message_box2"></ul>
<?php
echo form_open('receivables/add_payment/'.$receivable_info->receivable_id.'/0/',array('id'=>'payment_form'));
?>
<fieldset id="receivable_info">
<legend><?php echo $this->lang->line("receivables_payment_info"); ?></legend>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('common_amount').':', 'amount',array('class'=>'wide required')); ?>
	<div class='form_field'>
		<?php echo form_input(array(
			'name'=>'amount',
			'id'=>'amount'
		));?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label('O.R.#:', 'or_number',array('class'=>'wide required')); ?>
	<div class='form_field'>
		<?php echo form_input(array(
			'name'=>'or_number',
			'id'=>'or_number'
		));?>
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
	'name'=>'submit_payment',
	'id'=>'submit_payment',
	'value'=>$this->lang->line('receivables_payment_add'),
	'class'=>'submit_button float_right')
);
?><br /><br /><br />
<center><table id="receivable_payments" border="1" cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->lang->line('receivables_date');?></th>
		<th><?php echo $this->lang->line('common_amount');?></th>
		<th>O.R.#</th>
		<th><?php echo $this->lang->line('receivables_comments');?></th>
	</tr>
	
	<?php foreach ($this->Receivable_payments->get_info($receivable_info->receivable_id,0) as $receivable_payment) { ?>
		<tr>
			<td><?php echo $receivable_payment['payment_date']; ?></td>
			<td style="text-align:right"><?php echo number_format($receivable_payment['payment_amount'],4,'.',','); ?></td>
			<td><?php echo $receivable_payment['or_number']; ?></td>
			<td><?php echo $receivable_payment['comments']; ?></td>
		</tr>
	<?php } ?>
</table>
<?php
$total_receivable_amount = $this->Receivable_items->get_total_receivable_amount($receivable_info->receivable_id,date("U")+86400)-$this->Receivable_items->get_total_discount_receivable_amount($receivable_info->receivable_id,date("U")+86400);
$total_payments = $this->Receivable_payments->get_total_payments($receivable_info->receivable_id,0);
$tax_amount = $receivable_info->tax_amount;
?>
<br />
<table>
<tr><td><b>Gross Amount:</b></td><td style="text-align:right"><?php echo to_currency($total_receivable_amount) ?></td></tr>
<tr><td><b>Tax Amount:</b></td><td style="text-align:right"><?php echo to_currency($tax_amount) ?></td></tr>
<tr><td><b>Net Amount:</b></td><td style="text-align:right"><?php echo to_currency($total_receivable_amount-$tax_amount) ?></td></tr>
<tr><td><b>Total Payment:</b></td><td style="text-align:right"><?php echo to_currency($total_payments) ?></td></tr>
<tr><td><b>Remaining Balance:</b></td><td style="text-align:right"><?php echo to_currency($total_receivable_amount-$total_payments-$tax_amount) ?></td></tr>
</table>
</center>
</fieldset>
</center>
<?php
echo form_close();
} ?>

<script type='text/javascript'>

$("#item").autocomplete('<?php echo site_url("receivables/item_search"); ?>',
{
	minChars:0,
	max:100,
	selectFirst: false,
   	delay:10,
	formatItem: function(row) {
		return row[1];
	}
});

$("#item").result(function(event, data, formatted)
{
	$("#item").val("");
	
	if ($("#receivable_item_"+data[0]).length==1)
	{
		if($("#item_price_"+data[0]).val()==data[3])
			$("#receivable_item_"+data[0]).val(parseFloat($("#receivable_item_"+data[0]).val()) + 1);
		else
			alert("Item with different prices can't co-exist in a single transaction.\n\nItem not added.");
	}
	else
	{
		$("#receivable_items").append("<tr><td><a href='#' onclick='return deleteItemKitRow(this);'>X</a></td><td>"+data[1].split('<br />')[0]+"</td><td>"+data[1].split('<br />')[1]+"<br /><sup>["+data[2]+" in stock] [Php "+data[3]+"]</sup></td><td><input class='quantity_transferred' id='receivable_item_"+data[0]+"' type='text' size='3' name=receivable_item["+data[0]+"] value='1'/><input type='hidden' name=item_price["+data[0]+"] id='item_price_"+data[0]+"' value='"+data[3]+"' /></td></tr>");
	}
});

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
			$('#submit').val('Please Wait...');
			$('#submit').attr('disabled',true);
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_receivable_form_submit(response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules:
		{
			customer_id:"required",
			terms:"required"
		},
		messages:
		{
			customer_id:"<?php echo $this->lang->line('receivables_customer_required'); ?>",
			terms:"<?php echo $this->lang->line('receivables_terms_required'); ?>"
		}
	});
	$('#payment_form').validate({
		submitHandler:function(form)
		{
			$('#submit_payment').val('Please Wait...');
			$('#submit_payment').attr('disabled',true);
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_receivable_form_submit(response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box2",
 		wrapper: "li",
		rules:
		{
			amount:"required",
			or_number:"required"
		},
		messages:
		{
			customer_id:"<?php echo $this->lang->line('receivables_customer_required'); ?>"
		}
	});
});

function deleteItemKitRow(link)
{
	$(link).parent().parent().remove();
	return false;
}
</script>