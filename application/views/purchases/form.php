<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
<ul id="error_message_box"></ul>
<?php
echo form_open('purchases/save/'.$purchase_info->purchase_id,array('id'=>'purchase_form'));
?>
<fieldset id="purchase_info">
<legend><?php echo $this->lang->line("purchases_info"); ?></legend>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('purchases_id').':', 'timestamp',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'value'=>$purchase_info->po_number!=''?$purchase_info->po_number:'[P.O. REQUEST]',
		'disabled'=>'disabled')
	);?>
	</div>
</div>

<div class="field_row clearfix">
<?php if ($this->config->item('purchase_timestamp_override')==1) { ?>
	<?php echo form_label($this->lang->line('purchases_date').':', 'timestamp',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'id'=>'timestamp',
		'name'=>'timestamp',
		'type'=>'datetime',
		'value'=>$purchase_info->purchase_date!=''?$purchase_info->purchase_date:date("Y-m-d H:i:s"),
		)
	);?>
	</div>
<?php } else { ?>
	<?php echo form_label($this->lang->line('purchases_date').':', 'timestamp',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'value'=>$purchase_info->purchase_date!=''?$purchase_info->purchase_date:date("Y-m-d H:i:s"),
		'disabled'=>'disabled')
	);?>
	<?php echo form_hidden('timestamp',$purchase_info->purchase_date!=''?$purchase_info->purchase_date:date("Y-m-d H:i:s")); ?>
	</div>
<?php } ?>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('purchases_supplier_name').':', 'supplier',array('class'=>'wide required')); ?>
	<div class='form_field'>
	<?php if($purchase_info->status==0) { ?>
	<?php if($this->Employee->has_permission('purchases_request',$this->Employee->get_logged_in_employee_info()->person_id)) {echo form_input(array('name'=>'supplier','id'=>'supplier')).'<br />';}
	?>
	<?php } ?>
	<?php echo form_hidden('supplier_id" id="supplier_id',$purchase_info->person_id!=''?$purchase_info->person_id:''); ?>
	<?php echo form_input(array(
		'name'=>'supplier_name',
		'id'=>'supplier_name',
		'value'=>(isset($purchase_info->first_name)?($purchase_info->company_name):''),
		'disabled'=>'disabled'
	));?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('purchases_comments').':', 'comments',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_textarea(array(
		'name'=>'comments',
		'id'=>'comments',
		'value'=>$purchase_info->purchase_comments,
		'rows'=>'5',
		'cols'=>'18')
	);?>
	</div>
</div>

<?php if($purchase_info->status==0 && $this->Employee->has_permission('purchases_request',$this->Employee->get_logged_in_employee_info()->person_id)) { ?>
<div class="field_row clearfix">
<?php echo form_label($this->lang->line('purchases_add_item').':', 'item',array('class'=>'wide')); ?>
	<div class='form_field'>
		<?php echo form_input(array(
			'name'=>'item',
			'id'=>'item'
		));?>
	</div>
</div>
<?php } ?>

<center><table id="purchase_items" border="1" cellpadding="0" cellspacing="0">
	<tr>
		<?php
		if($purchase_info->status==0 && $this->Employee->has_permission('purchases_request',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			echo "<th>".$this->lang->line('common_delete')."</th>";
		}
		
		echo "<th>".$this->lang->line('purchases_item_number')."</th>";
		echo "<th>".$this->lang->line('purchases_item_name')."</th>";
		echo "<th>".$this->lang->line('purchases_quantity')."</th>";
		
		if($purchase_info->status>0)
		{
			echo "<th>".$this->lang->line('common_price')."</th>";
			echo "<th>".$this->lang->line('sales_total')."</th>";
			echo "<th>Received</th>";
		}
		
		if($purchase_info->status==1)
		{
			echo "<th>".$this->lang->line('common_discount')."</th>";
			echo "<th>%</th>";
			echo "<th>Php</th>";
		}
		else if($purchase_info->status==2)
		{
			echo "<th>".$this->lang->line('sales_total')."</th>";
			echo "<th>".$this->lang->line('common_discount')."</th>";
			echo "<th>".$this->lang->line('sales_total')."</th>";
		}
		?>
	</tr>
	
	<?php foreach ($this->Purchase_items->get_info($purchase_info->purchase_id) as $purchase_item)
	{
		echo "<tr>";
		$item_info = $this->Item->get_info($purchase_item['item_id']);
		
		if($purchase_info->status==0 && $this->Employee->has_permission('purchases_request',$this->Employee->get_logged_in_employee_info()->person_id))
		{ 
			echo "<td><a href=\"#\" onclick='return deleteItemKitRow(this);'>X</a></td>";
		}
		
		echo "<td>$item_info->item_number</td>";
		echo "<td>$item_info->name";
		if($purchase_info->status==0)
		{
			echo "<br /><sup>[".(int)$item_info->quantity." in stock]</sup>";
		}
		echo "</td>";
		
		if($purchase_info->status==0)
		{
			echo "<td>";
			if($purchase_info->status==0 && $this->Employee->has_permission('purchases_request',$this->Employee->get_logged_in_employee_info()->person_id))
			{
				echo "<input class='quantity_request' id='purchase_item_".$purchase_item['item_id']."' type='text' size='3' name=purchase_item[".$purchase_item['item_id']."] value='".(int)$purchase_item['quantity_request']."' />";
			}
			else
			{
				echo (int)$purchase_item['quantity_request'];
			}
			echo "</td>";
		}
		else
		{
			echo "<td>".(int)$purchase_item['quantity_request']."</td>";
			echo "<td>".$purchase_item['item_cost_price']."</td>";
			echo "<td>".number_format($purchase_item['quantity_request']*$purchase_item['item_cost_price'],4)."</td>";
		}
		
		if($purchase_info->status==1)
		{
			echo "<td><input class='quantity_request' id='purchase_item_receive_".$purchase_item['item_id']."' type='text' size='3' name=purchase_item_receive[".$purchase_item['item_id']."] value='".(int)$purchase_item['quantity_received']."'/></td>";
		}
		else if($purchase_info->status==2)
		{
			echo "<td>".(int)$purchase_item['quantity_received']."</td>";
			echo "<td>".number_format($purchase_item['item_cost_price']*$purchase_item['quantity_received'],4)."</td>";
		}
		
		if($purchase_info->status==1)
		{
			echo "<td><input class='quantity_request' id='purchase_item_discount_".$purchase_item['item_id']."' type='text' size='3' name=purchase_item_discount[".$purchase_item['item_id']."] value='".$purchase_item['discount_percent']."'/></td>";
			echo "<td><input type='radio' name=purchase_item_discount_type[".$purchase_item['item_id']."] value='0' ".($purchase_item['discount_type']==0?"checked":"")." /></td>";
			echo "<td><input type='radio' name=purchase_item_discount_type[".$purchase_item['item_id']."] value='1' ".($purchase_item['discount_type']==1?"checked":"")." /></td>";
		}
		else if($purchase_info->status==2)
		{
			echo "<td>".($purchase_item['discount_type']==0?$purchase_item['discount_percent'].'%':$purchase_item['discount_percent'].'P')."</td>";
			echo "<td>".number_format($purchase_item['discount_type']==0?($purchase_item['item_cost_price']*$purchase_item['quantity_received'])*(100-$purchase_item['discount_percent'])/100:($purchase_item['item_cost_price']*$purchase_item['quantity_received'])-$purchase_item['discount_percent'],4)."</td>";
		}
		
		echo "</tr>";
	} ?>
</table></center>
<?php
if($purchase_info->status==0 && $this->Employee->has_permission('purchases_approve_request',$this->Employee->get_logged_in_employee_info()->person_id))
{
	echo form_checkbox(array('name'=>'approve_request','id'=>'approve_request','value'=>'checked','class'=>'float_left'));
	echo "Approve Purchase Order";
	echo "<br />";
}
else if($purchase_info->status==1 && $this->Employee->has_permission('purchases_receive_discount',$this->Employee->get_logged_in_employee_info()->person_id))
{
	echo form_checkbox(array('name'=>'finalize','id'=>'finalize','value'=>'checked','class'=>'float_left'));
	echo "Finalize Receives/Discounts";
	echo "<br />";
}

if($purchase_info->status>0 && $this->Employee->has_permission('purchases_undo',$this->Employee->get_logged_in_employee_info()->person_id))
{
	echo form_checkbox(array('name'=>'undo','id'=>'undo','value'=>'checked','class'=>'float_left'));
	echo "Undo Purchase";
}

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
if($purchase_info->status>=1)
{
	echo $this->lang->line("common_print").': ';
	echo anchor("$controller_name/cpo/".$purchase_info->purchase_id,'C.P.O.',array('id'=>'print','target'=>'_blank'));
}
?>

<?php if($purchase_info->status==2 && $this->Employee->has_permission('purchases_payments',$this->Employee->get_logged_in_employee_info()->person_id)) { ?>

<ul id="error_message_box2"></ul>
<?php
echo form_open('purchases/add_payment/'.$purchase_info->purchase_id.'/0/',array('id'=>'payment_form'));
?>
<center>
<fieldset id="purchase_info" style="width:340px">
<legend><?php echo $this->lang->line("purchases_payment_info"); ?></legend>

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
<?php echo form_label($this->lang->line('purchases_comments').':', 'comments',array('class'=>'wide')); ?>
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
	'value'=>$this->lang->line('purchases_payment_add'),
	'class'=>'submit_button float_right')
);
?><br /><br /><br />
<center><table id="purchase_payments" border="1" cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->lang->line('purchases_date');?></th>
		<th><?php echo $this->lang->line('common_amount');?></th>
		<th><?php echo $this->lang->line('purchases_comments');?></th>
	</tr>
	
	<?php foreach ($this->Purchase_payments->get_info($purchase_info->purchase_id,0) as $purchase_payment) { ?>
		<tr>
			<td><?php echo $purchase_payment['payment_date']; ?></td>
			<td style="text-align:right"><?php echo $purchase_payment['payment_amount']; ?></td>
			<td><?php echo $purchase_payment['comments']; ?></td>
		</tr>
	<?php } ?>
</table>
<?php
$total_purchase_amount = $this->Purchase_items->get_total_purchase_amount($purchase_info->purchase_id,date("U")+86400)-$this->Purchase_items->get_total_discount_purchase_amount($purchase_info->purchase_id,date("U")+86400);
$total_payments = $this->Purchase_payments->get_total_payments($purchase_info->purchase_id,0);
?>
<br />
<table>
<tr><td><b>Total Purchase Amount:</b></td><td style="text-align:right"><?php echo to_currency($total_purchase_amount) ?></td></tr>
<tr><td><b>Total Payment:</b></td><td style="text-align:right"><?php echo to_currency($total_payments) ?></td></tr>
<tr><td><b>Remaining Balance:</b></td><td style="text-align:right"><?php echo to_currency($total_purchase_amount-$total_payments) ?></td></tr>
</table>
</center>
</fieldset>
</center>
<?php
echo form_close();
} ?>

<script type='text/javascript'>

$("#item").autocomplete('<?php echo site_url("purchases/item_search"); ?>',
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
	
	if ($("#purchase_item_"+data[0]).length==1)
	{
		$("#purchase_item_"+data[0]).val(parseFloat($("#purchase_item_"+data[0]).val()) + 1);
	}
	else
	{
		$("#purchase_items").append("<tr><td><a href='#' onclick='return deleteItemKitRow(this);'>X</a></td><td>"+data[1].split('<br />')[0]+"</td><td>"+data[1].split('<br />')[1]+"<br /><sup>["+data[2]+" in stock]</sup></td><td><input class='quantity_request' id='purchase_item_"+data[0]+"' type='text' size='3' name=purchase_item["+data[0]+"] value='1'/></td></tr>");
	}
});

$("#supplier").autocomplete('<?php echo site_url("purchases/supplier_search"); ?>',
{
	minChars:0,
	delay:10,
	max:100,
	formatItem: function(row) {
		return row[1];
	}
});

$("#supplier").result(function(event, data, formatted)
{
	$("#supplier").val("");
	$("#supplier_id").val(data[0]);
	$("#supplier_name").val(data[1]);
});

$('#supplier').blur(function()
{
	$(this).attr('value','');
});

$('#supplier').click(function()
{
	$(this).attr('value','');
});

//validation and submit handling
$(document).ready(function()
{
	$('#purchase_form').validate({
		submitHandler:function(form)
		{
			$('#submit').val('Please Wait...');
			$('#submit').attr('disabled',true);
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_purchase_form_submit(response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules:
		{
			supplier_id:"required"
		},
		messages:
		{
			supplier_id:"<?php echo $this->lang->line('purchases_supplier_required'); ?>"
		}
	});
	$('#payment_form').validate({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_purchase_form_submit(response);
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
			supplier_id:"<?php echo $this->lang->line('purchases_supplier_required'); ?>"
		}
	});
});

function deleteItemKitRow(link)
{
	$(link).parent().parent().remove();
	return false;
}
</script>