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
		<tr><td width="50%">
			<table>
			<tr>
				<td colspan="5" valign="top">
					<h1><?php echo $this->config->item('company'); ?></h1>
					<h5><?php echo nl2br($this->config->item('address')); ?><br />
					<?php echo $this->lang->line('common_phone_number').': '.$this->config->item('phone'); ?>; 
					<?php echo $this->lang->line('common_email').': '.$this->config->item('email'); ?></h5>
				</td>
			</tr>
			</table>
		</td><td align="right" valign="top">
			<table>
			<tr>
				<td align="right" colspan="2"><h3>APPROVED PURCHASE ORDER</h3></td>
			</tr>
			<tr>
				<td align="right" valign="bottom"><b>P.O.#:</b>&nbsp;</td><td><h2><?php echo str_pad($po_number,8,'0',STR_PAD_LEFT); ?></h2></td>
			</tr>
			<tr>
				<td align="right"><b>Date:</b>&nbsp;</td><td width="10%"><?php echo $purchase_date; ?></td>
			</tr>
			</table>
		</td></tr>
		<tr><td colspan="2"><hr></td></tr>
		<tr>
			<td valign="top"><table border="0">
				<tr><td align="right" valign="top" width="1%"><b>Supplier:</b>&nbsp;</td><td width="40%"><?php echo $company_name; ?></td></tr>
				<tr><td align="right" valign="top"><b>Address:</b>&nbsp;</td><td><?php echo $supplier_address1; ?></td></tr>
				<tr><td>&nbsp;</td><td><?php echo $supplier_address2; ?></td></tr></table>
			</td>
			<td valign="top"><table border="0">
				<tr><td align="right" valign="top" width="1%"><b>Contact&nbsp;Person:</b>&nbsp;</td><td><?php echo $contact_person; ?></td></tr>
				<tr><td align="right" valign="top"><b>Phone&nbsp;Number:</b>&nbsp;</td><td><?php echo $phone_number; ?></td></tr>
			</table></td>
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
			<th style="border:1px #000 solid;width:5%;text-align:center;"><?php echo $this->lang->line('common_discount'); ?></th>
			<th style="border:1px #000 solid;width:5%;text-align:center;">Net&nbsp;Amount</th>
		</tr>
		<?php $count=0;$total=0;$discounttotal=0;$soldtotal=0;$nettotal=0; ?>
		<?php foreach ($this->Purchase_items->get_info($purchase_id) as $purchase_item) { if($purchase_item['discount_percent']>0) { $item_quantity = $purchase_status==2?$purchase_item['quantity_received']:$purchase_item['quantity_request']; if($item_quantity>0) { $count++; ?>
		<tr>
			<?php
			$item_info = $this->Item->get_info($purchase_item['item_id']);
			?>
			<td style="border:1px #000 solid;" align="center"><?php echo $item_quantity; ?></td>
			<td style="border:1px #000 solid;" align="center"><?php echo $item_info->item_number; ?></td>
			<td style="border:1px #000 solid;"><?php echo $item_info->name; ?></td>
			<td style="border:1px #000 solid;" align="right"><?php echo to_currency($purchase_item['item_cost_price']) ?></td>
			<td style="border:1px #000 solid;" align="right"><?php $subtotal = $item_quantity*$purchase_item['item_cost_price'];if($purchase_item['discount_type']==0){$discounttotal+=$subtotal*($purchase_item['discount_percent']/100);}else{$discounttotal+=$purchase_item['discount_percent'];}$total+=$subtotal;echo to_currency($subtotal); ?></td>
			<td style="border:1px #000 solid;" align="right"><?php if($purchase_item['discount_type']==0) { echo to_currency($subtotal*($purchase_item['discount_percent']/100)).'&nbsp;('.$purchase_item['discount_percent'].'%)'; } else { echo to_currency($purchase_item['discount_percent']).'&nbsp;('.number_format($purchase_item['discount_percent']/$subtotal*100,2).'%)'; } ?></td>
			<td style="border:1px #000 solid;" align="right"><?php if($purchase_item['discount_type']==0) { echo to_currency($subtotal-($subtotal*($purchase_item['discount_percent']/100))); } else { echo to_currency($subtotal-$purchase_item['discount_percent']); } ?></td>
		</tr>
		<?php }}} ?>
		<?php if($count>0) { ?><tr>
			<td style="border:1px #000 solid;" colspan="4" align="right"><b>Sub Total</b></td>
			<td style="border:1px #000 solid;" align="right"><b><?php echo to_currency($total); ?></b></td>
			<td style="border:1px #000 solid;" align="right"><b><?php echo to_currency($discounttotal); ?></b></td>
			<td style="border:1px #000 solid;" align="right"><b><?php echo to_currency($total-$discounttotal); ?></b></td>
		</tr><?php $count++; } ?>
		<?php $previouscount=$count;$previoustotal=$total; ?>
		<?php foreach ($this->Purchase_items->get_info($purchase_id) as $purchase_item) { if($purchase_item['discount_percent']==0) { $item_quantity = $purchase_status==2?$purchase_item['quantity_received']:$purchase_item['quantity_request']; if($item_quantity>0) { $count++; ?>
		<tr>
			<?php
			$item_info = $this->Item->get_info($purchase_item['item_id']);
			?>
			<td style="border:1px #000 solid;" align="center"><?php echo $item_quantity; ?></td>
			<td style="border:1px #000 solid;" align="center"><?php echo $item_info->item_number; ?></td>
			<td style="border:1px #000 solid;"><?php echo $item_info->name; ?></td>
			<td style="border:1px #000 solid;" align="right"><?php echo to_currency($purchase_item['item_cost_price']) ?></td>
			<td style="border:1px #000 solid;" align="right"><?php $subtotal = $item_quantity*$purchase_item['item_cost_price'];$total+=$subtotal;echo to_currency($subtotal) ?></td>
			<td style="border:1px #000 solid;" align="right"><?php echo $purchase_item['discount_percent']>0?$purchase_item['discount_percent'].'%':'' ?></td>
			<td style="border:1px #000 solid;" align="right"><?php echo to_currency($subtotal) ?></td>
		</tr>
		<?php }}} ?>
		<?php if($previouscount!=$count) { ?><tr>
			<td style="border:1px #000 solid;" colspan="4" align="right"><b>Sub Total</b></td>
			<td style="border:1px #000 solid;" align="right"><b><?php echo to_currency($total-$previoustotal); ?></b></td>
			<td style="border:1px #000 solid;" align="right"></td>
			<td style="border:1px #000 solid;" align="right"><b><?php echo to_currency($total-$previoustotal); ?></b></td>
		</tr><?php $count++; } ?>
		
		<tr>
			<td colspan="4" style='text-align:right;border-top:1px solid #000000;'><b><?php echo $this->lang->line('sales_total'); ?></b></td>
			<td style='text-align:right;border-top:1px solid #000000;'><span style="border-bottom:3px double #000000;"><b><?php echo to_currency($total); ?></b></span></td>
			<td style='text-align:right;border-top:1px solid #000000;'><span style="border-bottom:3px double #000000;"><b><?php echo to_currency($discounttotal); ?></b></span></td>
			<td style='text-align:right;border-top:1px solid #000000;'><span style="border-bottom:3px double #000000;"><b><?php echo to_currency($total-$discounttotal); ?></b></span></td>
		</tr>
		<?php for(;$count<13;$count++) echo "<tr><td colspan=\"5\">&nbsp;</td></tr>"; ?>
		<?php if($count>13) for(;$count<50;$count++) echo "<tr><td colspan=\"5\">&nbsp;</td></tr>"; ?>
	</table>
	</center>
	
	<table width="100%" border="0" cellspacing="0">
		<tr>
			<td colspan="2"><b>Prepared By:</b></td>
			<td><b>Reviewed By:</b></td>
			<td colspan="3"><b>Approved By:</b></td>
		</tr>
		<tr>
			<td colspan="2"><br /><table><tr><td>____________________<td></tr><tr><td align="center">SOPHIA PATRIARCA</td></tr></table></td>
			<td><br /><table><tr><td>____________________<td></tr><tr><td align="center">SOPHIA PATRIARCA</td></tr></table></td>
			<td colspan="3"><br /><table><tr><td>____________________<td></tr><tr><td align="center">JINA N. MIJARES</td></tr></table></td>
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