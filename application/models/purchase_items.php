<?php
class Purchase_items extends CI_Model
{
	/*
	Gets purchase items for a particular purchase
	*/
	function get_info($purchase_id)
	{
		$this->db->from('purchase_items');
		$this->db->where('purchase_id',$purchase_id);
		//return an array of purchase items for an item
		return $this->db->get()->result_array();
	}
	
	/*
	Inserts or updates an purchase's items
	*/
	function save(&$purchase_items_data, $purchase_id)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		$this->delete($purchase_id);
		
		foreach ($purchase_items_data as $row)
		{
			$row['purchase_id'] = $purchase_id;
			$this->db->insert('purchase_items',$row);
		}
		
		$this->db->trans_complete();
		return true;
	}
	
	/*
	Deletes purchase items given an purchase
	*/
	function delete($purchase_id)
	{
		return $this->db->delete('purchase_items', array('purchase_id' => $purchase_id)); 
	}
	
	/*
	Gets total item amount for a particular purchase
	*/
	function get_total_purchase_amount($purchase_id,$date=0)
	{
		$this->db->from('purchase_items');
		$this->db->join('items','items.item_id = purchase_items.item_id');
		$this->db->where('purchase_id',$purchase_id);
		$amount = 0;
		foreach($this->db->get()->result_array() as $purchase_item)
		{
			$amount += $purchase_item['item_unit_price']*($purchase_item['quantity_transferred']-$purchase_item['quantity_returned']);
		}
		return $amount;
	}
	
	/*
	Gets total transferred item amount for a particular purchase
	*/
	function get_total_transferred_purchase_amount($purchase_id,$date=0)
	{
		$this->db->from('purchase_items');
		$this->db->join('items','items.item_id = purchase_items.item_id');
		$this->db->where('purchase_id',$purchase_id);
		$amount = 0;
		foreach($this->db->get()->result_array() as $purchase_item)
		{
			$amount += $purchase_item['item_unit_price']*$purchase_item['quantity_transferred'];
		}
		return $amount;
	}
	
	/*
	Gets total returned item amount for a particular purchase
	*/
	function get_total_return_purchase_amount($purchase_id,$date=0)
	{
		$this->db->from('purchase_items');
		$this->db->join('items','items.item_id = purchase_items.item_id');
		$this->db->where('purchase_id',$purchase_id);
		$amount = 0;
		foreach($this->db->get()->result_array() as $purchase_item)
		{
			$amount += $purchase_item['item_unit_price']*$purchase_item['quantity_returned'];
		}
		return $amount;
	}
	
	/*
	Gets total discount item amount for a particular purchase
	*/
	function get_total_discount_purchase_amount($purchase_id,$date=0)
	{
		$this->db->from('purchase_items');
		$this->db->join('items','items.item_id = purchase_items.item_id');
		$this->db->where('purchase_id',$purchase_id);
		$amount = 0;
		foreach($this->db->get()->result_array() as $purchase_item)
		{
			$amount += $purchase_item['item_unit_price']*($purchase_item['quantity_transferred']-$purchase_item['quantity_returned'])*($purchase_item['discount_percent']/100);
		}
		return $amount;
	}
	
	/*
	Gets customer total item amount for a particular purchase
	*/
	function get_customer_total_purchase_amount($customer_id,$date=0)
	{
		$amount = 0;
		$this->db->from('purchases');
		$this->db->where("person_id = $customer_id and purchase_date < '". date("Y-m-d H:i:s",$date) ."' and status > 0");
		foreach($this->db->get()->result_array() as $purchase)
		{
			$amount += $this->get_total_purchase_amount($purchase['purchase_id'],$date)-$this->get_total_discount_purchase_amount($purchase['purchase_id'],$date);
		}
		$this->db->from('purchase_beginning_balance');
		$this->db->where("customer_id = $customer_id and period < '". date("Y-m-d H:i:s",$date) ."' and deleted = 0");
		foreach($this->db->get()->result_array() as $purchase)
		{
			$amount += $purchase['balance'];
		}
		return $amount;
	}
	
	/*
	Gets customer total payments for purchases
	*/
	function get_customer_total_payment_amount($customer_id,$date=0)
	{
		$amount = 0;
		$this->db->from('purchase_payments');
		$this->db->join('purchases','purchases.purchase_id = purchase_payments.purchase_id');
		$this->db->where('purchases.person_id',$customer_id);
		$this->db->where("payment_date < '". date("Y-m-d H:i:s",$date) ."'");
		$this->db->where("mode = 0 and status > -1");
		foreach($this->db->get()->result_array() as $purchase_payment)
		{
			$amount += $purchase_payment['payment_amount'];
		}
		$this->db->from('purchase_payments');
		$this->db->join('purchase_beginning_balance','purchase_beginning_balance.purchase_id = purchase_payments.purchase_id');
		$this->db->where('purchase_beginning_balance.customer_id',$customer_id);
		$this->db->where("payment_date < '". date("Y-m-d H:i:s",$date) ."'");
		$this->db->where("mode = 1 and deleted = 0");
		foreach($this->db->get()->result_array() as $purchase_payment)
		{
			$amount += $purchase_payment['payment_amount'];
		}
		return $amount;
	}
}
?>
