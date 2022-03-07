<?php $this->load->view("partial/header"); ?>
<?php
if (isset($error_message))
{
	echo '<h1 style="text-align: center;">'.$error_message.'</h1>';
	exit;
}
?>
<div id="receipt_wrapper">
	<table width="100%" border="0">
		<tr>
			<td width="50%" valign="top">
				<table><tr><td>
					<h1><?php echo $this->config->item('company'); ?></h1>
					<h5><?php echo nl2br($this->config->item('address')); ?></b><br />
					<?php echo $this->lang->line('common_phone_number').': '.$this->config->item('phone'); ?>; 
					<?php echo $this->lang->line('common_email').': '.$this->config->item('email'); ?></h5>
				</td></tr></table>
			</td>
			<td align="right" valign="top">
				<table>
					<tr><td align="right" colspan="2"><h3>DELIVERY RECEIPT FOR SAMPLING</h3></td></tr>
					<tr><td align="right" valign="bottom"><b>D.R.S.#:</b>&nbsp;</td><td width="1%"><h2><?php echo str_pad($sample_number,8,'0',STR_PAD_LEFT); ?></h2></td></tr>
					<tr><td align="right"><b>Date:</b>&nbsp;</td><td><?php echo $sample_date; ?></td></tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td colspan="2">
				<table border="0">
				<tr><td align="right" width="1%"><b>Sampling&nbsp;For:</b>&nbsp;</td><td><?php echo $customer_name; ?></td></tr>
				<tr><td align="right"><b>Address:</b>&nbsp;</td><td><?php echo $customer_address1; ?></td></tr>
				<tr><td>&nbsp;</td><td><?php echo $customer_address2; ?></td></tr>
				</table>
			</td>
		</tr>
	</table>
	
	<center>
	<table cellspacing="0" id="receipt_items" style="font-size:12px;">
		<tr style="color:#ffffff;background:#000000;">
			<th style="border:1px #000 solid;width:5%;text-align:center;"><?php echo $this->lang->line('sales_quantity'); ?></th>
			<th style="border:1px #000 solid;width:15%;text-align:center"><?php echo $this->lang->line('items_item_number'); ?></th>
			<th style="border:1px #000 solid;text-align:center;"><?php echo $this->lang->line('items_item'); ?></th>
		</tr>
		<?php $count=0; foreach ($this->Sample_items->get_info($sample_id) as $sample_item) { $count++; ?>
		<tr>
			<?php
			$item_info = $this->Item->get_info($sample_item['item_id']);
			?>
			<td style="border:1px #000 solid;" align="center"><?php $quantity = (int)$sample_item['quantity_transferred']-(int)$sample_item['quantity_returned']; echo $quantity; ?></td>
			<td style="border:1px #000 solid;" align="center"><?php echo $item_info->item_number; ?></td>
			<td style="border:1px #000 solid;"><?php echo $item_info->selling_name==''?$item_info->name:$item_info->selling_name; if($sample_item['item_id']<0) echo '<small> ('.$item_info->description.')</small>'; ?></td>
		</tr>
		<?php } ?>
		<?php $count++; ?>
		<tr><td colspan="5">&nbsp;</td></tr>
		<?php for(;$count<14;$count++) echo "<tr><td colspan=\"5\">&nbsp;</td></tr>"; ?>
		<?php if($count>14) for(;$count<49;$count++) echo "<tr><td colspan=\"5\">&nbsp;</td></tr>"; ?>
	</table>
	</center>
	
	<table width="100%" border="0" cellspacing="0">
		<tr>
			<td valign="top" width="50%">
				<table border="1" width="100%" height="100%" id="form_table">
					<tr><td width="10%"><b>Prepared&nbsp;By:</b></td><td colspan="3">&nbsp;</td></tr>
					<tr><td><b>Checked&nbsp;By:</b></td><td colspan="3">&nbsp;</td></tr>
					<tr><td><b>Delivered&nbsp;By:</b></td><td>&nbsp;</td><td width="10%"><b>Plate&nbsp;No:</b></td><td>&nbsp;</td></tr>
				</table>
			</td>
			<td>&nbsp;</td>
			<td valign="top">
				<table id="form_table" width="100%"><tr><td>
					<table border="1" width="100%" style="font-size:12px" id="form_table_clear">
						<tr><td align="center" colspan="2"><b>Received the above goods in good order and condition:</b></td></tr>
						<tr><td align="center" valign="bottom">___________________________________<br />Print Name and Signature</td>
						<td align="center" valign="bottom">____________________<br />Date</td></tr>
					</table>
				</td></tr></table>
			</td>
		</tr>
	</table>
	
	<table width="100%" style="font-size:8px"><tr><td colspan="2" align="right">Print Date/Time: <?php echo date("Y-m-d H:i:s") ?></td></tr></table>

</div>
<?php $this->load->view("partial/footer"); ?>

<?php if ($this->Appconfig->get('print_after_sale'))
{
?>
<script type="text/javascript">
$(window).load(function()
{
	window.print();
});
</script>
<?php
}
?>