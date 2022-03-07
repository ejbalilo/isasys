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
					<tr><td align="right" colspan="2"><h3>DELIVERY RECEIPT</h3></td></tr>
					<tr><td align="right" valign="bottom"><b>D.R.#:</b>&nbsp;</td><td width="1%"><h2><?php echo str_pad($itr_number,8,'0',STR_PAD_LEFT); ?></h2></td></tr>
					<tr><td align="right"><b>Date:</b>&nbsp;</td><td><?php echo $receivable_date; ?></td></tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td colspan="2">
				<table border="0">
				<tr><td align="right" width="1%" valign="bottom"><b>Sold&nbsp;To:</b>&nbsp;</td><td><h2><?php echo $customer_name; ?></h2></td></tr>
				<tr><td align="right"><b>Address:</b>&nbsp;</td><td><?php echo $customer_address1; ?></td></tr>
				<tr><td>&nbsp;</td><td><?php echo $customer_address2; ?></td></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%" style="font-size:12px;border:1px solid"><tr>
					<td width="20%"><b>Destination:</b> <?php echo $destination; ?></td>
					<td><b>Account&nbsp;Officer:</b> <?php echo $account_officer; ?></td>
					<td><b>Sales&nbsp;Agent:</b> <?php echo $sales_agent; ?></td>
					<td><b>Terms&nbsp;of&nbsp;Payment:</b></td>
				</tr></table>
			</td>
		</tr>
	</table>
	
	<center>
	<table cellspacing="0" id="receipt_items" style="font-size:12px;">
		<tr style="color:#ffffff;background:#000000;">
			<th style="border:1px #000 solid;width:5%;text-align:center;"><?php echo $this->lang->line('sales_quantity'); ?></th>
			<th style="border:1px #000 solid;width:15%;text-align:center"><?php echo $this->lang->line('items_item_number'); ?></th>
			<th style="border:1px #000 solid;text-align:center;"><?php echo $this->lang->line('items_item'); ?></th>
			<th style="border:1px #000 solid;width:5%;text-align:center;"><?php echo $this->lang->line('common_price'); ?></th>
			<th style="border:1px #000 solid;width:5%;text-align:center;"><?php echo $this->lang->line('common_amount'); ?></th>
		</tr>
		<?php $count=0;$total=0;$returntotal=0;$soldtotal=0;$nettotal=0; ?>
		<?php foreach ($this->Receivable_items->get_info($receivable_id) as $receivable_item) { if($receivable_item['discount_percent']>0 && $receivable_item['quantity_transferred']-$receivable_item['quantity_returned']>0) {$count++; ?>
		<tr>
			<?php
			$item_info = $this->Item->get_info($receivable_item['item_id']);
			?>
			<td style="border:1px #000 solid;" align="center"><?php $quantity = (int)$receivable_item['quantity_transferred']-(int)$receivable_item['quantity_returned']; echo $quantity; ?></td>
			<td style="border:1px #000 solid;" align="center"><?php echo $item_info->item_number; ?></td>
			<td style="border:1px #000 solid;"><?php echo $item_info->selling_name==''?$item_info->name:$item_info->selling_name; if($receivable_item['item_id']<0) echo '<small> ('.$item_info->description.')</small>'; ?></td>
			<td style="border:1px #000 solid;" align="right"><?php echo to_currency($receivable_item['item_unit_price']) ?></td>
			<td style="border:1px #000 solid;" align="right"><?php $subtotal = $quantity*$receivable_item['item_unit_price'];$total+=$subtotal;echo to_currency($subtotal) ?></td>
		</tr>
		<?php }} if($count>0){ ?>
		<tr><td colspan="4" align="right"><b>Sub Total Gross Amount</b></td><td align="right"><?php echo to_currency($total); ?></td></tr>
		<tr>
			<td colspan="4" align="right"><b>Less <?php echo $discount; ?>% Discount</b></td>
			<td align="right"><?php echo to_currency($total*$discount/100); $total *= (100-$discount)/100; ?></td>
		</tr>
		<?php $count++; } $total_no_discount=0; foreach ($this->Receivable_items->get_info($receivable_id) as $receivable_item) { if($receivable_item['discount_percent']==0 && $receivable_item['quantity_transferred']-$receivable_item['quantity_returned']>0) {$count++; ?>
		<tr>
			<?php
			$item_info = $this->Item->get_info($receivable_item['item_id']);
			?>
			<td style="border:1px #000 solid;" align="center"><?php $quantity = (int)$receivable_item['quantity_transferred']-(int)$receivable_item['quantity_returned']; echo $quantity; ?></td>
			<td style="border:1px #000 solid;" align="center"><?php echo $item_info->item_number; ?></td>
			<td style="border:1px #000 solid;"><?php echo $item_info->selling_name==''?$item_info->name:$item_info->selling_name; if($receivable_item['item_id']<0) echo '<small> ('.$item_info->description.')</small>'; ?></td>
			<td style="border:1px #000 solid;" align="right"><?php echo to_currency($receivable_item['item_unit_price']) ?></td>
			<td style="border:1px #000 solid;" align="right"><?php $subtotal = $quantity*$receivable_item['item_unit_price'];$total_no_discount+=$subtotal;echo to_currency($subtotal) ?></td>
		</tr>
		<?php }} if($total_no_discount>0){ ?>
		<tr><td colspan="4" align="right"><b>Sub Total Gross Amount</b></td><td align="right"><?php echo to_currency($total_no_discount); ?></td></tr><?php $count++; } ?>
		<tr><td colspan="5">&nbsp;</td></tr>
		<tr>
			<td colspan="4" align="right"><b><?php echo $this->lang->line('sales_total'); ?></b></td>
			<td align="right"><span style="border-bottom:3px double #000000;"><?php echo to_currency($total+$total_no_discount); ?></span></td>
		</tr>
		<?php for(;$count<9;$count++) echo "<tr><td colspan=\"5\">&nbsp;</td></tr>"; ?>
		<?php if($count>9) for(;$count<44;$count++) echo "<tr><td colspan=\"5\">&nbsp;</td></tr>"; ?>
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