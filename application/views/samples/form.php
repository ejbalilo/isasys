<?php if($this->Employee->has_permission('samples_edit',$this->Employee->get_logged_in_employee_info()->person_id)) $canedit=true; else $canedit=false; ?>
<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
<ul id="error_message_box"></ul>
<?php
if(($canedit && $sample_info->status>0) || $sample_info->sample_id==0 || $sample_info->status==0) echo form_open('samples/save/'.$sample_info->sample_id,array('id'=>'sample_form'));
?>
<fieldset id="sample_info">
<legend><?php echo $this->lang->line("samples_info"); ?></legend>

<div class="field_row clearfix">
<?php echo form_label('ID:', 'timestamp',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'value'=>$sample_info->sample_number!=''?$sample_info->sample_number:'[SAMPLING REQUEST]',
		'disabled'=>'disabled')
	);?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label('Timestamp:', 'timestamp',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'value'=>$sample_info->sample_date!=''?$sample_info->sample_date:date("Y-m-d H:i:s"),
		'disabled'=>'disabled')
	);?>
	<?php echo form_hidden('timestamp',$sample_info->sample_date!=''?$sample_info->sample_date:date("Y-m-d H:i:s")); ?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label('Customer Name:', 'customer',array('class'=>'wide required')); ?>
	<div class='form_field'>
	<?php if($sample_info->status==0) { ?>
	<?php echo form_input(array('name'=>'customer','id'=>'customer')).'<br />';
	?>
	<?php } ?>
	<?php echo form_hidden('customer_id" id="customer_id',$sample_info->person_id!=''?$sample_info->person_id:''); ?>
	<?php echo form_input(array(
		'name'=>'customer_name',
		'id'=>'customer_name',
		'value'=>(isset($sample_info->first_name)?($sample_info->first_name.' '.$sample_info->last_name):''),
		'disabled'=>'disabled'
	));?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label('Comments:', 'comments',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_textarea(array(
		'name'=>'comments',
		'id'=>'comments',
		'value'=>$sample_info->comments,
		'rows'=>'5',
		'cols'=>'18')
	);?>
	</div>
</div>

<?php if($sample_info->status==0) { ?>
<div class="field_row clearfix">
<?php echo form_label('Add Item:', 'item',array('class'=>'wide')); ?>
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
		if($sample_info->status==0)
		{
			echo "<th>".$this->lang->line('common_delete')."</th>";
		}
		
		echo "<th>".$this->lang->line('samples_item_number')."</th>";
		echo "<th>".$this->lang->line('samples_item_name')."</th>";
		echo "<th>".$this->lang->line('samples_quantity')."</th>";
		
		if($sample_info->status>0)
		{
			echo "<th>".$this->lang->line('common_return')."</th>";
		}
		
		if($sample_info->status==2)
		{
			echo "<th>Used</th>";
		} ?>
	</tr>
	
	<?php foreach ($this->Sample_items->get_info($sample_info->sample_id) as $sample_item)
	{
		echo "<tr>";
		$item_info = $this->Item->get_info($sample_item['item_id']);
		
		if($sample_info->status==0)
		{ 
			echo "<td><a href=\"#\" onclick='return deleteItemKitRow(this);'>X</a></td>";
		}
		
		echo "<td>$item_info->item_number</td>";
		echo "<td>".($item_info->selling_name==''?$item_info->name:$item_info->selling_name);
		if($sample_info->status==0)
		{
			echo "<br /><sup>[".(int)$item_info->quantity." in stock]</sup>";
		}
		echo "</td>";
		
		if($sample_info->status==0)
		{
			echo "<td>";
			if($sample_info->status==0)
			{
				echo "<input class='quantity_transferred' id='sample_item_".$sample_item['item_id']."' type='text' size='3' name=sample_item[".$sample_item['item_id']."] value='".(int)$sample_item['quantity_transferred']."' />";
			}
			else
			{
				echo (int)$sample_item['quantity_transferred'];
			}
			echo "</td>";
		}
		else
		{
			echo "<td>".(int)$sample_item['quantity_transferred']."</td>";
		}
		
		if($sample_info->status==1)
		{
			echo "<td><input class='quantity_transferred' id='sample_item_return_".$sample_item['item_id']."' type='text' size='3' name=sample_item_return[".$sample_item['item_id']."] value='".(int)$sample_item['quantity_returned']."'/></td>";
		}
		else if($sample_info->status==2)
		{
			echo "<td>".(int)$sample_item['quantity_returned']."</td>";
			echo "<td>".($sample_item['quantity_transferred']-$sample_item['quantity_returned'])."</td>";
		}
		
		echo "</tr>";
	} ?>
</table></center>
<?php
if($sample_info->status==0 && $canedit)
{
	echo form_checkbox(array('name'=>'approve_request','id'=>'approve_request','value'=>'checked','class'=>'float_left'));
	echo "Approve Sampling Request";
}
else if($sample_info->status==1 && $canedit)
{
	echo form_checkbox(array('name'=>'finalize','id'=>'finalize','value'=>'checked','class'=>'float_left'));
	echo "Finalize Returns";
}

if($canedit || $sample_info->sample_id==0 || $sample_info->status==0) echo form_submit(array('name'=>'submit','id'=>'submit','value'=>$this->lang->line('common_save'),'class'=>'submit_button float_right'));
?>
</fieldset>
<?php
if($canedit || $sample_info->sample_id==0) echo form_close();
if($sample_info->status>0)
{
	echo $this->lang->line("common_print").': ';
	echo anchor("$controller_name/dr/".$sample_info->sample_id,'D.R. for Sampling',array('id'=>'print','target'=>'_blank'));
}
?>

<?php if($sample_info->status>0) { ?>

<ul id="error_message_box2"></ul>
<?php
if($canedit) echo form_open('samples/add_usage/'.$sample_info->sample_id,array('id'=>'payment_form'));
?>
<center>
<fieldset id="sample_info">
<legend>Usage Info</legend>
<?php if($canedit) { ?>
<div class="field_row clearfix">
<?php echo form_label('Item:', 'item_id',array('class'=>'wide required')); ?>
	<div class='form_field'>
		<?php echo form_dropdown('item_id',$items,0,array('id'=>'item_id','name'=>'item_id'));?>
	</div>
</div>

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
<?php echo form_label($this->lang->line('samples_comments').':', 'comments',array('class'=>'wide')); ?>
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
echo form_submit(array('name'=>'submit_usage','id'=>'submit_usage','value'=>'Add Usage','class'=>'submit_button float_right'));
?><br /><br /><br />
<?php } ?>
<center><table id="receivable_payments" border="1" cellpadding="0" cellspacing="0">
	<tr>
		<th><?php echo $this->lang->line('samples_date');?></th>
		<th>Item</th>
		<th><?php echo $this->lang->line('common_amount');?></th>
		<th><?php echo $this->lang->line('samples_comments');?></th>
	</tr>
	
	<?php foreach ($this->Sample_usages->get_info($sample_info->sample_id) as $sample_payment) { ?>
		<tr>
			<td><?php echo $sample_payment['usage_date']; ?></td>
			<td><?php echo $sample_payment['name']; ?></td>
			<td style="text-align:right"><?php echo $sample_payment['usage_amount']; ?></td>
			<td><?php echo $sample_payment['comments']; ?></td>
		</tr>
	<?php } ?>
</table>
</center>
<br />
</fieldset>
</center>
<?php
if($canedit) echo form_close();
} ?>

<script type='text/javascript'>

$("#item").autocomplete('<?php echo site_url("samples/item_search"); ?>',
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
	
	if ($("#sample_item_"+data[0]).length==1)
	{
		if($("#item_price_"+data[0]).val()==data[3])
			$("#sample_item_"+data[0]).val(parseFloat($("#sample_item_"+data[0]).val()) + 1);
		else
			alert("Item with different prices can't co-exist in a single transaction.\n\nItem not added.");
	}
	else
	{
		$("#receivable_items").append("<tr><td><a href='#' onclick='return deleteItemKitRow(this);'>X</a></td><td>"+data[1].split('<br />')[0]+"</td><td>"+data[1].split('<br />')[1]+"<br /><sup>["+data[2]+" in stock]</sup></td><td><input class='quantity_transferred' id='sample_item_"+data[0]+"' type='text' size='3' name=sample_item["+data[0]+"] value='1'/></td></tr>");
	}
});

$("#customer").autocomplete('<?php echo site_url("samples/customer_search"); ?>',
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
	$('#sample_form').validate({
		submitHandler:function(form)
		{
			$('#submit').val('Please Wait...');
			$('#submit').attr('disabled',true);
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_sample_form_submit(response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules:
		{
			customer_id:"required"
		},
		messages:
		{
			customer_id:"<?php echo $this->lang->line('samples_customer_required'); ?>"
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
				post_sample_form_submit(response);
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
			customer_id:"<?php echo $this->lang->line('samples_customer_required'); ?>"
		}
	});
});

function deleteItemKitRow(link)
{
	$(link).parent().parent().remove();
	return false;
}
</script>