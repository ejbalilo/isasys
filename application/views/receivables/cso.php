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
			<td colspan="5" valign="top" align="center">
				<h1><?php echo $this->config->item('company'); ?></h1>
				<h5><?php echo nl2br($this->config->item('address')); ?><br />
				<?php echo $this->lang->line('common_phone_number').': '.$this->config->item('phone'); ?><br />
				<?php echo $this->lang->line('common_email').': '.$this->config->item('email'); ?></h5>
			</td>
		</tr>
		<tr>
			<td colspan="5">&nbsp;</td>
		</tr>
		<tr>
			<td align="center" colspan="5"><h2>APPROVED SALES ORDER</h2></td>
		</tr>
		<tr>
			<td valign="top" align="right" colspan="5">
				<table>
					<tr><td align="right" valign="bottom"><b>No:</b>&nbsp;</td><td valign="bottom"><h2><?php echo str_pad($itr_number,8,'0',STR_PAD_LEFT); ?></h2></td></tr>
					<tr><td align="right"><b>Date:</b>&nbsp;</td><td><?php echo $receivable_date; ?></td></tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="5"><table width="100%"><tr>
				<td width="50%"><table>
					<tr><td align="right" width="10%"><b>Sold To:</b>&nbsp;</td><td width="40%"><?php echo $customer_name; ?></td></tr>
					<tr><td align="right"><b>Address:</b>&nbsp;</td><td><?php echo $customer_address1; ?></td></tr>
					<tr><td>&nbsp;</td><td><?php echo $customer_address2; ?></td></tr>
				</table></td>
				<td><table>
					<tr><td align="right" width="10%"><b>Account&nbsp;Officer:</b>&nbsp;</td><td width="40%"><?php echo $account_officer ?></td></tr>
					<tr><td align="right" width="10%"><b>Sales&nbsp;Agent:</b>&nbsp;</td><td width="40%"><?php echo $sales_agent ?></td></tr>
					<tr><td align="right" width="10%"><b>Terms&nbsp;of&nbsp;Payment:</b>&nbsp;</td><td width="40%">&nbsp;</td></tr>
				</table></td>
			</tr></table></td>
		</tr>
	</table>
	
	<center>
	<table cellspacing="0" id="receipt_items" style="font-size:14px;">
		<tr style="color:#ffffff;background:#000000;">
			<th style="border:1px #000 solid;width:5%;text-align:center;"><?php echo $this->lang->line('sales_quantity'); ?></th>
			<th style="border:1px #000 solid;width:15%;text-align:center"><?php echo $this->lang->line('items_item_number'); ?></th>
			<th style="border:1px #000 solid;text-align:center;"><?php echo $this->lang->line('items_item'); ?></th>
			<th style="border:1px #000 solid;width:5%;text-align:center;"><?php echo $this->lang->line('common_price'); ?></th>
			<th style="border:1px #000 solid;width:5%;text-align:center;"><?php echo $this->lang->line('common_amount'); ?></th>
		</tr>
		<?php $count=0;$total=0;$returntotal=0;$soldtotal=0;$nettotal=0; ?>
		<?php foreach ($this->Receivable_items->get_info($receivable_id) as $receivable_item) { $count++; ?>
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
		<?php } ?>
		
		<tr>
			<td colspan="4" style='text-align:right;border-top:1px solid #000000;'><b><?php echo $this->lang->line('sales_total'); ?></b></td>
			<td style='text-align:right;border-top:1px solid #000000;'><span style="border-bottom:3px double #000000;"><?php echo to_currency($total); ?></span></td>
		</tr>
		<?php for(;$count<24;$count++) echo "<tr><td colspan=\"5\">&nbsp;</td></tr>"; ?>
	</table>
	</center>
	
	<table width="100%" border="0" cellspacing="0">
		<tr>
			<td valign="top" width="50%">
				<table width="100%" height="100%" id="form_table">
					<tr><td><table id="form_table_clear">
					<tr>
						<td width="50%">Approved By:</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td valign="bottom" align="center">_______________________<br /><b>Jina N. Mijares</b></td>
					</tr>
					</table></td></tr>
				</table>
			</td>
			<td>&nbsp;</td>
			<td valign="top">
				<table border="1" width="100%" id="form_table">
					<tr>
						<td><b>Date:</b></td>
					</tr>
					<tr>
						<td valign="top"><b>Confirmed Order By:</b><br />&nbsp;<br />&nbsp;</td>
					</tr>
				</table>
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