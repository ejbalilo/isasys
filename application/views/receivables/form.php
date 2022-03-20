<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
<ul id="error_message_box"></ul>
<?php
echo form_open('receivables/save/'.$receivable_info->receivable_id,array('id'=>'receivable_form'));
?>
<fieldset id="receivable_info">
<legend><?php echo $this->lang->line("receivables_info"); ?></legend>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_id').':', 'timestamp',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'value'=>$receivable_info->status==0?'[S.O. REQUEST]':($receivable_info->itr_number>0?$receivable_info->itr_number:'[S.O. DENIED]'),
		'disabled'=>'disabled')
	);?>
	</div>
</div>

<div class="field_row clearfix">
<?php if ($this->config->item('receivable_timestamp_override')==1) { ?>
	<?php echo form_label($this->lang->line('receivables_date').':', 'timestamp',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'id'=>'timestamp',
		'name'=>'timestamp',
		'type'=>'datetime',
		'value'=>$receivable_info->receivable_date!=''?$receivable_info->receivable_date:date("Y-m-d H:i:s"),
		)
	);?>
	</div>
<?php } else { ?>
	<?php echo form_label($this->lang->line('receivables_date').':', 'timestamp',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'value'=>$receivable_info->receivable_date!=''?$receivable_info->receivable_date:date("Y-m-d H:i:s"),
		'disabled'=>'disabled')
	);?>
	<?php echo form_hidden('timestamp',$receivable_info->receivable_date!=''?$receivable_info->receivable_date:date("Y-m-d H:i:s")); ?>
	</div>
<?php } ?>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_customer_name').':', 'customer',array('class'=>'wide required')); ?>
	<div class='form_field'>
	<?php if($receivable_info->status==0) { ?>
	<?php if($this->Employee->has_permission('receivables_request',$this->Employee->get_logged_in_employee_info()->person_id)) {echo form_input(array('name'=>'customer','id'=>'customer')).'<br />';}
	?>
	<?php } ?>
	<?php echo form_hidden('customer_id" id="customer_id',$receivable_info->person_id!=''?$receivable_info->person_id:''); ?>
	<?php echo form_input(array(
		'name'=>'customer_name',
		'id'=>'customer_name',
		'value'=>(isset($receivable_info->first_name)?($receivable_info->first_name.' '.$receivable_info->last_name):''),
		'disabled'=>'disabled'
	));?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_terms').':', 'terms',array('class'=>'wide required')); ?>
	<div class='form_field'>
	<?php
		if($receivable_info->status==0 && $this->Employee->has_permission('receivables_request',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			$input = array(
			'name'=>'terms',
			'id'=>'terms',
			'value'=>$receivable_info->receivable_id!=''?$receivable_info->terms:'30');
			echo form_input($input);
		}
		else
		{
			$input = array('value'=>$receivable_info->receivable_id!=''?$receivable_info->terms:'30','disabled'=>'disabled');
			echo form_input($input);
			echo form_hidden('terms',$receivable_info->receivable_id!=''?$receivable_info->terms:'30');
		}
	?>
	</div>
</div>

<?php if($receivable_info->receivable_id>0) { ?>
<div class="field_row clearfix">
<?php echo form_label('Discount: ', 'discount',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php
		$input = array('name'=>'discount','id'=>'discount','value'=>($receivable_info->status==0?$receivable_info->customer_discount:$receivable_info->discount),'disabled'=>'disabled','style'=>'width:50px');
		echo form_input($input);
	?>%
	</div>
</div>
<?php } ?>

<div class="field_row clearfix">
<?php echo form_label('Issued To:', 'issued_to',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'name'=>'issued_to',
		'id'=>'issued_to',
		'value'=>$receivable_info->issued_to)
	);?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label('Destination:', 'destination',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'name'=>'destination',
		'id'=>'destination',
		'value'=>$receivable_info->destination)
	);?>
	</div>
</div>

<?php if($receivable_info->receivable_id>0) { ?>
<div class="field_row clearfix">
<?php echo form_label('Tax Amount: ', 'tax_amount',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php
		$input = array('name'=>'tax_amount','id'=>'tax_amount','value'=>($receivable_info->status==0?$receivable_info->tax_amount:$receivable_info->tax_amount));
		echo form_input($input);
	?>
	</div>
</div>
<?php } ?>

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

<?php if($receivable_info->status==0 && $this->Employee->has_permission('receivables_request',$this->Employee->get_logged_in_employee_info()->person_id)) { ?>
<div class="field_row clearfix">
<?php echo form_label($this->lang->line('receivables_add_item').':', 'item',array('class'=>'wide')); ?>
	<div class='form_field'>
		<?php echo form_input(array(
			'name'=>'item',
			'id'=>'item'
		));?>
	</div>
</div>
<?php } ?>

<center><table id="receivable_items" border="1" cellpadding="0" cellspacing="0">
	<tr>
		<?php
		if($receivable_info->status==0 && $this->Employee->has_permission('receivables_request',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			echo "<th>".$this->lang->line('common_delete')."</th>";
		}
		
		echo "<th>".$this->lang->line('receivables_item_number')."</th>";
		echo "<th>".$this->lang->line('receivables_item_name')."</th>";
		echo "<th>".$this->lang->line('receivables_quantity')."</th>";
		
		if($receivable_info->status>0)
		{
			echo "<th>".$this->lang->line('common_price')."</th>";
			echo "<th>".$this->lang->line('sales_total')."</th>";
		}
		
		if($receivable_info->status>0)
		{
			echo "<th>".$this->lang->line('common_return')."</th>";
		}
		
		if($receivable_info->status==1)
		{
			echo "<th>Discount?</th>";
		}
		
		if($receivable_info->status==2)
		{
			echo "<th>".$this->lang->line('sales_total')."</th>";
			echo "<th>".$this->lang->line('receivables_sold')."</th>";
			echo "<th>".$this->lang->line('sales_total')."</th>";
			echo "<th title='with Discount'>Disc</th>";
		}
		
		if($receivable_info->status==2)
		{
			//echo "<th>".$this->lang->line('sales_total')."</th>";
		} ?>
	</tr>
	
	<?php foreach ($this->Receivable_items->get_info($receivable_info->receivable_id) as $receivable_item)
	{
		echo "<tr>";
		$item_info = $this->Item->get_info($receivable_item['item_id']);
		
		if($receivable_info->status==0 && $this->Employee->has_permission('receivables_request',$this->Employee->get_logged_in_employee_info()->person_id))
		{ 
			echo "<td><a href=\"#\" onclick='return deleteItemKitRow(this);'>X</a></td>";
		}
		
		echo "<td>$item_info->item_number</td>";
		echo "<td>".($item_info->selling_name==''?$item_info->name:$item_info->selling_name);
		if($receivable_info->status==0)
		{
			echo "<br /><sup>[".(int)$item_info->quantity." in stock] [Php ".$receivable_item['item_unit_price']."]</sup>";
		}
		echo "</td>";
		
		if($receivable_info->status==0)
		{
			echo "<td>";
			if($receivable_info->status==0 && $this->Employee->has_permission('receivables_request',$this->Employee->get_logged_in_employee_info()->person_id))
			{
				echo "<input class='quantity_transferred' id='receivable_item_".$receivable_item['item_id']."' type='text' size='3' name=receivable_item[".$receivable_item['item_id']."] value='".(int)$receivable_item['quantity_transferred']."' /><input type='hidden' name=item_price[".$receivable_item['item_id']."] id='item_price_".$receivable_item['item_id']."' value='".$receivable_item['item_unit_price']."' />";
			}
			else
			{
				echo (int)$receivable_item['quantity_transferred'];
			}
			echo "</td>";
		}
		else
		{
			echo "<td>".(int)$receivable_item['quantity_transferred']."</td>";
			echo "<td>".$receivable_item['item_unit_price']."</td>";
			echo "<td>".number_format($receivable_item['quantity_transferred']*$receivable_item['item_unit_price'],4)."</td>";
		}
		
		if($receivable_info->status==1)
		{
			echo "<td><input class='quantity_transferred' id='receivable_item_return_".$receivable_item['item_id']."' type='text' size='3' name=receivable_item_return[".$receivable_item['item_id']."] value='".(int)$receivable_item['quantity_returned']."'/></td>";
			echo "<td>";
			if($item_info->is_discountable==1)
				echo "<input type='checkbox' name=receivable_item_discount[".$receivable_item['item_id']."] ".($receivable_item['discount_percent']==$receivable_info->discount?"checked":"")."/>";
			else
				echo "<input type='hidden' name=receivable_item_discount[".$receivable_item['item_id']."] value='' />";
			echo "</td>";
		}
		else if($receivable_info->status==2)
		{
			echo "<td>".(int)$receivable_item['quantity_returned']."</td>";
			echo "<td>".number_format($receivable_item['item_unit_price']*$receivable_item['quantity_returned'],4)."</td>";
			echo "<td>".($receivable_item['quantity_transferred']-$receivable_item['quantity_returned'])."</td>";
			echo "<td>".number_format($receivable_item['item_unit_price']*($receivable_item['quantity_transferred']-$receivable_item['quantity_returned']),4)."</td>";
			echo "<td>".($receivable_item['discount_percent']==$receivable_info->discount?'Yes':'No')."</td>";
		}
		
		if($receivable_info->status==2)
		{
			//echo "<td>".number_format(($receivable_item['item_unit_price']*($receivable_item['quantity_transferred']-$receivable_item['quantity_returned']))*(100-$receivable_item['discount_percent'])/100,4)."</td>";
		}
		
		echo "</tr>";
	} ?>
</table></center>
<?php
if($receivable_info->status==0 && $this->Employee->has_permission('receivables_approve_request',$this->Employee->get_logged_in_employee_info()->person_id))
{
	echo form_checkbox(array('name'=>'approve_request','id'=>'approve_request','value'=>'checked','class'=>'float_left'));
	echo "Approve Sales Order";
	if($receivable_info->receivable_id>0)
	{
		echo "<br />";
		echo form_checkbox(array('name'=>'deny_request','id'=>'deny_request','value'=>'checked','class'=>'float_left'));
		echo "Deny Sales Order";
	}
}
else if($receivable_info->status==1 && $this->Employee->has_permission('receivables_return',$this->Employee->get_logged_in_employee_info()->person_id))
{
	echo form_checkbox(array('name'=>'finalize','id'=>'finalize','value'=>'checked','class'=>'float_left'));
	echo "Finalize Returns/Discounts";
	if($this->Employee->has_permission('receivables_undo',$this->Employee->get_logged_in_employee_info()->person_id))
	{
		echo "<br />";
		echo form_checkbox(array('name'=>'undo_approve','id'=>'undo_approve','value'=>'checked','class'=>'float_left'));
		echo "Undo Approve";
	}
}
else if($receivable_info->status==2 && $this->Employee->has_permission('receivables_undo',$this->Employee->get_logged_in_employee_info()->person_id))
{
	echo form_checkbox(array('name'=>'undo','id'=>'undo','value'=>'checked','class'=>'float_left'));
	echo "Undo Finalizing Returns/Discounts";
}

echo form_submit(array(
	'name'=>'submit',
	'id'=>'submit',
	'value'=>$this->lang->line('common_save'),
	'class'=>'submit_button float_right'
	)
);
?>
</fieldset>
<?php
echo form_close();
if($receivable_info->status>0)
{
	echo $this->lang->line("common_print").': ';
	echo anchor("$controller_name/cso/".$receivable_info->receivable_id,'C.S.O.',array('id'=>'print','target'=>'_blank'));
	echo ' ';
	echo anchor("$controller_name/dr/".$receivable_info->receivable_id,'D.R. (without SA Commission)',array('id'=>'print','target'=>'_blank'));
	echo ' ';
	echo anchor("$controller_name/dr2/".$receivable_info->receivable_id,'D.R. (with SA Commission)',array('id'=>'print','target'=>'_blank'));
}
else
{
	if($receivable_info->receivable_id>0)
	{
		echo $this->lang->line("common_print").': ';
		echo anchor("$controller_name/sor/".$receivable_info->receivable_id,'S.O.R.',array('id'=>'print','target'=>'_blank'));
	}
}
?>

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

$('#approve_request').click(function()
{
	if($(this).attr('checked'))
	{
		if($('#deny_request').attr('checked'))
		{
			$('#deny_request').attr('checked',false);
		}
	}
});

$('#deny_request').click(function()
{
	if($(this).attr('checked'))
	{
		if($('#approve_request').attr('checked'))
		{
			$('#approve_request').attr('checked',false);
		}
	}
});

//validation and submit handling
$(document).ready(function()
{
	$('#receivable_form').validate({
		submitHandler:function(form)
		{
			if($('#approve_request').attr('checked') && $('#deny_request').attr('checked'))
			{
				alert('Cannot approve and deny a transaction. Choose only one.');
			}
			else
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
			}
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