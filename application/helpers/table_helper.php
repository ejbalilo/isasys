<?php
/*
Gets the html table to manage people.
*/
function get_people_manage_table($people,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';
	
	$headers = array('<input type="checkbox" id="select_all" />', 
	$CI->lang->line('common_last_name'),
	$CI->lang->line('common_first_name'),
	$CI->lang->line('common_email'),
	$CI->lang->line('common_phone_number'),
	'&nbsp');
	
	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_people_manage_table_data_rows($people,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the people.
*/
function get_people_manage_table_data_rows($people,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($people->result() as $person)
	{
		$table_data_rows.=get_person_data_row($person,$controller);
	}
	
	if($people->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='6'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('common_no_persons_to_display')."</div></tr></tr>";
	}
	
	return $table_data_rows;
}

function get_person_data_row($person,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='5%'><input type='checkbox' id='person_$person->person_id' value='".$person->person_id."'/></td>";
	$table_data_row.='<td width="20%">'.character_limiter($person->last_name,13).'</td>';
	$table_data_row.='<td width="20%">'.character_limiter($person->first_name,13).'</td>';
	$table_data_row.='<td width="30%">'.mailto($person->email,character_limiter($person->email,22)).'</td>';
	$table_data_row.='<td width="20%">'.character_limiter($person->phone_number,13).'</td>';		
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$person->person_id/width:".($width*1.25), $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';		
	$table_data_row.='</tr>';
	
	return $table_data_row;
}

/*
Gets the html table to manage suppliers.
*/
function get_supplier_manage_table($suppliers,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';
	
	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('suppliers_company_name'),
	$CI->lang->line('common_last_name'),
	$CI->lang->line('common_first_name'),
	$CI->lang->line('common_email'),
	$CI->lang->line('common_phone_number'),
	'&nbsp');
	
	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_supplier_manage_table_data_rows($suppliers,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the supplier.
*/
function get_supplier_manage_table_data_rows($suppliers,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($suppliers->result() as $supplier)
	{
		$table_data_rows.=get_supplier_data_row($supplier,$controller);
	}
	
	if($suppliers->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='7'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('common_no_persons_to_display')."</div></tr></tr>";
	}
	
	return $table_data_rows;
}

function get_supplier_data_row($supplier,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='5%'><input type='checkbox' id='person_$supplier->person_id' value='".$supplier->person_id."'/></td>";
	$table_data_row.='<td width="17%">'.character_limiter($supplier->company_name,13).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->last_name,13).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->first_name,13).'</td>';
	$table_data_row.='<td width="22%">'.mailto($supplier->email,character_limiter($supplier->email,22)).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->phone_number,13).'</td>';		
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$supplier->person_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';		
	$table_data_row.='</tr>';
	
	return $table_data_row;
}

/*
Gets the html table to manage items.
*/
function get_items_manage_table($items,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';
	
	$headers = array('<input type="checkbox" id="select_all" />', 
	$CI->lang->line('items_item_number'),
	$CI->lang->line('items_name'),
	$CI->lang->line('items_category'),
	$CI->lang->line('items_quantity'),
	'&nbsp;',
	$CI->lang->line('items_inventory')
	);
	
	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_items_manage_table_data_rows($items,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the items.
*/
function get_items_manage_table_data_rows($items,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($items->result() as $item)
	{
		$table_data_rows.=get_item_data_row($item,$controller);
	}
	
	if($items->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('items_no_items_to_display')."</div></tr></tr>";
	}
	
	return $table_data_rows;
}

function get_item_data_row($item,$controller)
{
	$CI =& get_instance();
	$item_tax_info=$CI->Item_taxes->get_info($item->item_id);
	$tax_percents = '';
	foreach($item_tax_info as $tax_info)
	{
		$tax_percents.=$tax_info['percent']. '%, ';
	}
	$tax_percents=substr($tax_percents, 0, -2);
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'><input type='checkbox' id='item_$item->item_id' value='".$item->item_id."'/></td>";
	$table_data_row.='<td width="12%">'.$item->item_number.'</td>';
	$table_data_row.='<td width="40%">'.$item->name.'</td>';
	$table_data_row.='<td width="25%">'.$item->category.'</td>';
	$table_data_row.='<td width="10%" align="center">'.$item->quantity.'</td>';
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$item->item_id/width:".($width*1.25), ($CI->Employee->has_permission('items_edit',$CI->Employee->get_logged_in_employee_info()->person_id))?$CI->lang->line('common_edit'):$CI->lang->line('common_view'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	
	//Ramel Inventory Tracking
	$table_data_row.='<td width="10%">';
	if($CI->Employee->has_permission('items_edit',$CI->Employee->get_logged_in_employee_info()->person_id))
		$table_data_row.=anchor($controller_name."/inventory/$item->item_id/width:$width", $CI->lang->line('common_inv'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_count'))).'&nbsp;&nbsp;&nbsp;&nbsp;';
	$table_data_row.=anchor($controller_name."/count_details/$item->item_id/width:$width", $CI->lang->line('common_det'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_details_count'))).'</td>';//inventory details	
	
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage giftcards.
*/
function get_giftcards_manage_table( $giftcards, $controller )
{
	$CI =& get_instance();
	
	$table='<table class="tablesorter" id="sortable_table">';
	
	$headers = array('<input type="checkbox" id="select_all" />', 
	$CI->lang->line('giftcards_giftcard_number'),
	$CI->lang->line('giftcards_card_value'),
	'&nbsp', 
	);
	
	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_giftcards_manage_table_data_rows( $giftcards, $controller );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the giftcard.
*/
function get_giftcards_manage_table_data_rows( $giftcards, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($giftcards->result() as $giftcard)
	{
		$table_data_rows.=get_giftcard_data_row( $giftcard, $controller );
	}
	
	if($giftcards->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('giftcards_no_giftcards_to_display')."</div></tr></tr>";
	}
	
	return $table_data_rows;
}

function get_giftcard_data_row($giftcard,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'><input type='checkbox' id='giftcard_$giftcard->giftcard_id' value='".$giftcard->giftcard_id."'/></td>";
	$table_data_row.='<td width="15%">'.$giftcard->giftcard_number.'</td>';
	$table_data_row.='<td width="20%">'.to_currency($giftcard->value).'</td>';
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$giftcard->giftcard_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';		
	
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage item kits.
*/
function get_item_kits_manage_table( $item_kits, $controller )
{
	$CI =& get_instance();
	
	$table='<table class="tablesorter" id="sortable_table">';
	
	$headers = array('<input type="checkbox" id="select_all" />', 
	$CI->lang->line('item_kits_name'),
	$CI->lang->line('item_kits_description'),
	'&nbsp', 
	);
	
	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_item_kits_manage_table_data_rows( $item_kits, $controller );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the item kits.
*/
function get_item_kits_manage_table_data_rows( $item_kits, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($item_kits->result() as $item_kit)
	{
		$table_data_rows.=get_item_kit_data_row( $item_kit, $controller );
	}
	
	if($item_kits->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('item_kits_no_item_kits_to_display')."</div></tr></tr>";
	}
	
	return $table_data_rows;
}

function get_item_kit_data_row($item_kit,$controller)
{
	$CI =& get_instance();
	$controller_name=strtolower(get_class($CI));
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'><input type='checkbox' id='item_kit_$item_kit->item_kit_id' value='".$item_kit->item_kit_id."'/></td>";
	$table_data_row.='<td width="15%">'.$item_kit->name.'</td>';
	$table_data_row.='<td width="20%">'.character_limiter($item_kit->description, 25).'</td>';
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$item_kit->item_kit_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';		
	
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage receivables.
*/
function get_receivables_manage_table( $receivables, $controller, $mode )
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';
	
	if($mode=="beginningbalance")
	{
		$headers = array('<input type="checkbox" id="select_all" />',
			"Period",
			"First Name",
			"Last Name",
			$CI->lang->line('common_amount'),
			"Balance",
			'&nbsp'
		);
	}
	else
	{
		$headers = array('<input type="checkbox" id="select_all" />',
			'ID',
			$CI->lang->line('receivables_date'),
			"First Name",
			"Last Name",
			$CI->lang->line('receivables_status'),
			'&nbsp;'
		);
	}
	
	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_receivables_manage_table_data_rows( $receivables, $controller, $mode );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the receivables.
*/
function get_receivables_manage_table_data_rows( $receivables, $controller, $mode )
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($receivables->result() as $receivable)
	{
		if($mode=="beginningbalance")
			$table_data_rows.=get_receivable_data_row2( $receivable, $controller );
		else
			$table_data_rows.=get_receivable_data_row( $receivable, $controller );
	}
	
	if($receivables->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('receivables_no_receivables_to_display')."</div></tr></tr>";
	}
	
	return $table_data_rows;
}

function get_receivable_data_row($receivable,$controller)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));
	$width = $controller->get_form_width();
	$itr_number = $receivable->itr_number;
	if($receivable->status==0)
	{
		$status = "Pending for Approval";
	}
	else if($receivable->status==1)
	{
		$status = (date("U",strtotime(date("Y-m-d",strtotime($receivable->receivable_date))))+($receivable->terms*86400))-date("U");
		if($status<0)
		{
			$status = (int)($status/86400);
			if($status==0)
			{
				$status = 'Due today';
			}
			else if($status==-1)
			{
				$status = 'Due yesterday';
			}
			else
			{
				$status *= -1;
				$status .= ' days due';
			}
		}
		else if($status>=0)
		{
			$status = (int)($status/86400);
			if($status==0)
			{
				$status = 'Due today';
			}
			else if($status==1)
			{
				$status = 'Due tomorrow';
			}
			else
			{
				$status .= ' days before due date';
			}
		}
	}
	else if($receivable->status==2)
	{
		$balance = ($CI->Receivable_items->get_total_receivable_amount($receivable->receivable_id,date("U")+86400)-$CI->Receivable_items->get_total_discount_receivable_amount($receivable->receivable_id,date("U")+86400)-$CI->Receivable_payments->get_total_payments($receivable->receivable_id,0)-$receivable->tax_amount)*-1;
		if(number_format($balance,4)<0)
			$status = "Waiting for Payments<br />(Balance: ".to_currency($balance*-1).")";
		else
			$status = "Transaction Complete";
	}

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'><input type='checkbox' id='receivable_$receivable->receivable_id' value='$receivable->receivable_id'/></td>";
	$table_data_row.="<td width='5%' align='right'>".$itr_number."</td>";
	$table_data_row.="<td width='21%'>".$receivable->receivable_date;
	if($receivable->status==1)
	{
		$table_data_row.=((date("U")-strtotime($receivable->receivable_date))>=$receivable->terms*86400?' <img src="images/attention.png" /> ':'');
	}
	$table_data_row.='</td>';
	$table_data_row.='<td>'.character_limiter($receivable->first_name, 25).'</td>';
	$table_data_row.='<td>'.character_limiter($receivable->last_name, 25).'</td>';
	$table_data_row.='<td width="30%">'.$status.'</td>';
	if($receivable->status==0)
	{
		$multiplier = 1.5;
	}
	else if($receivable->status==1 || $receivable->status==2)
	{
		$multiplier = 1.75;
	}
	else if($receivable->status==3)
	{
		$multiplier = 2.25;
	}
	else
	{
		$multiplier = 1;
	}
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$receivable->receivable_id/width:".($width*$multiplier),$CI->lang->line('common_view'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update')));
	if($receivable->status>0)
	{
		$table_data_row.='&nbsp;'.anchor($controller_name."/payment/$receivable->receivable_id/width:".($width*1.5),'payment',array('class'=>'thickbox','title'=>'Collectible Payments'));
	}
	
	$table_data_row.='</td>';
	
	$table_data_row.='</tr>';
	
	return $table_data_row;
}

function get_receivable_data_row2($receivable,$controller)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));
	$width = $controller->get_form_width();
	$status = to_currency($receivable->balance - $CI->Receivable_payments->get_total_payments($receivable->receivable_id,1));

	$table_data_row='<tr>';
	$table_data_row.='<td width="3%"><input type="checkbox" id="receivable_$receivable->receivable_id" value="'.$receivable->receivable_id.'"/></td>';
	$table_data_row.='<td width="12%" align="right">'.$receivable->period.'</td>';
	$table_data_row.='<td>'.character_limiter($receivable->first_name, 25).'</td>';
	$table_data_row.='<td>'.character_limiter($receivable->last_name, 25).'</td>';
	$table_data_row.='<td width="15%" align="right">'.to_currency($receivable->balance).'</td>';
	$table_data_row.='<td width="15%" align="right">'.$status.'</td>';
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view2/$receivable->receivable_id/width:$width",$CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage purchases.
*/
function get_purchases_manage_table( $purchases, $controller )
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';
	
	$headers = array('<input type="checkbox" id="select_all" />',
		'ID',
		$CI->lang->line('purchases_date'),
		"Company Name",
		"First Name",
		"Last Name",
		$CI->lang->line('purchases_status'),
		'&nbsp;'
	);
	
	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_purchases_manage_table_data_rows( $purchases, $controller );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the purchases.
*/
function get_purchases_manage_table_data_rows( $purchases, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($purchases->result() as $purchase)
	{
		$table_data_rows.=get_purchase_data_row( $purchase, $controller );
	}
	
	if($purchases->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('purchases_no_purchases_to_display')."</div></tr></tr>";
	}
	
	return $table_data_rows;
}

function get_purchase_data_row($purchase,$controller)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));
	$width = $controller->get_form_width();
	$po_number = $purchase->po_number;
	if($purchase->status==0)
	{
		$status = "Pending for Approval";
	}
	else if($purchase->status==1)
	{
		$status = "Waiting for Delivery";
	}
	else if($purchase->status==2)
	{
		//$balance = ($CI->Purchase_items->get_total_purchase_amount($purchase->purchase_id,date("U")+86400)-$CI->Purchase_items->get_total_discount_purchase_amount($purchase->purchase_id,date("U")+86400)-$CI->Purchase_payments->get_total_payments($purchase->purchase_id,0))*-1;
		$balance = 10;
		if($balance<0)
			$status = "Waiting for Payments<br />(Balance: ".to_currency($balance*-1).")";
		else
			$status = "Transaction Complete";
	}

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'><input type='checkbox' id='purchase_$purchase->purchase_id' value='$purchase->purchase_id'/></td>";
	$table_data_row.="<td width='5%' align='right'>".$po_number."</td>";
	$table_data_row.="<td width='21%'>".$purchase->purchase_date;
	$table_data_row.='</td>';
	$table_data_row.='<td>'.character_limiter($purchase->company_name, 25).'</td>';
	$table_data_row.='<td>'.character_limiter($purchase->first_name, 25).'</td>';
	$table_data_row.='<td>'.character_limiter($purchase->last_name, 25).'</td>';
	$table_data_row.='<td width="30%">'.$status.'</td>';
	if($purchase->status==0)
	{
		$multiplier = 1.5;
	}
	else if($purchase->status==1 || $purchase->status==2)
	{
		$multiplier = 1.75;
	}
	else if($purchase->status==3)
	{
		$multiplier = 2.25;
	}
	else
	{
		$multiplier = 1;
	}
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$purchase->purchase_id/width:".($width*$multiplier),$CI->lang->line('common_view'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	
	$table_data_row.='</tr>';
	
	return $table_data_row;
}

/*
Gets the html table to manage samples.
*/
function get_samples_manage_table( $samples, $controller )
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';
	
	$headers = array('<input type="checkbox" id="select_all" />',
		'ID',
		$CI->lang->line('samples_date'),
		"First Name",
		"Last Name",
		$CI->lang->line('samples_status'),
		'&nbsp;'
	);
	
	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_samples_manage_table_data_rows( $samples, $controller );
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the samples.
*/
function get_samples_manage_table_data_rows( $samples, $controller )
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($samples->result() as $sample)
	{
		$table_data_rows.=get_sample_data_row( $sample, $controller );
	}
	
	if($samples->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('samples_no_samples_to_display')."</div></tr></tr>";
	}
	
	return $table_data_rows;
}

function get_sample_data_row($sample,$controller)
{
	$CI =& get_instance();
	$controller_name = strtolower(get_class($CI));
	$width = $controller->get_form_width();
	$sample_number = $sample->sample_number;
	
	if($sample->status==0)
	{
		$status = "Pending for Approval";
	}
	else if($sample->status==1)
	{
		$status = "Approved";
	}
	else if($sample->status==2)
	{
		$status = "Transaction Complete";
	}
	else
	{
		$status = "";
	}

	$table_data_row='<tr>';
	$table_data_row.="<td width='3%'><input type='checkbox' id='sample_$sample->sample_id' value='$sample->sample_id'/></td>";
	$table_data_row.="<td width='5%' align='right'>".$sample_number.".</td>";
	$table_data_row.="<td width='21%'>".$sample->sample_date;
	$table_data_row.='</td>';
	$table_data_row.='<td>'.character_limiter($sample->first_name, 25).'</td>';
	$table_data_row.='<td>'.character_limiter($sample->last_name, 25).'</td>';
	$table_data_row.='<td width="30%">'.$status.'</td>';
	$multiplier = 1.5;
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$sample->sample_id/width:".($width*$multiplier),$CI->lang->line('common_view'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	
	$table_data_row.='</tr>';
	
	return $table_data_row;
}
?>