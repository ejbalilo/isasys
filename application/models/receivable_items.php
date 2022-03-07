<?php
class Receivable_items extends CI_Model
{
	/*
	Gets receivable items for a particular receivable
	*/
	function get_info($receivable_id)
	{
		$this->db->from('receivable_items');
		$this->db->where('receivable_id',$receivable_id);
		//return an array of receivable items for an item
		return $this->db->get()->result_array();
	}
	
	/*
	Inserts or updates an receivable's items
	*/
	function save(&$receivable_items_data, $receivable_id)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		$this->delete($receivable_id);
		foreach($receivable_items_data as $row)
		{
			$row['receivable_id'] = $receivable_id;
			$this->db->insert('receivable_items',$row);
			if($row['item_id']<0)
			{
				foreach($this->Item_kit_items->get_info(-$row['item_id']) as $item_kit_item)
				{
					$item_kit_row = $row;
					unset($item_kit_row['item_unit_price']);
					unset($item_kit_row['discount_percent']);
					unset($item_kit_row['quantity_returned']);
					$item_kit_row['item_kit_id'] = $item_kit_item['item_kit_id'];
					$item_kit_row['item_id'] = $item_kit_item['item_id'];
					$item_kit_row['quantity_transferred'] = $item_kit_item['quantity']*$item_kit_row['quantity_transferred'];
					$item_kit_row['quantity_returned'] = $item_kit_item['quantity']*$row['quantity_returned'];
					$item_kit_row['item_cost_price'] = $item_kit_item['cost_price'];
					$item_kit_row['item_unit_price'] = $item_kit_item['unit_price'];
					$this->db->insert('receivable_item_kit_items',$item_kit_row);
				}
			}
		}
		$this->db->trans_complete();
		return true;
	}
	
	/*
	Deletes receivable items given an receivable
	*/
	function delete($receivable_id)
	{
		$this->db->delete('receivable_items', array('receivable_id' => $receivable_id));
		$this->db->delete('receivable_item_kit_items', array('receivable_id' => $receivable_id));
		return true;
	}
	
	/*
	Gets total item amount for a particular receivable
	*/
	function get_total_receivable_amount($receivable_id,$date=0)
	{
		$this->db->from('receivable_items');
		$this->db->join('items','items.item_id = receivable_items.item_id','left');
		$this->db->where('receivable_id',$receivable_id);
		$amount = 0;
		foreach($this->db->get()->result_array() as $receivable_item)
		{
			$amount += $receivable_item['item_unit_price']*($receivable_item['quantity_transferred']-$receivable_item['quantity_returned']);
		}
		return $amount;
	}
	
	/*
	Gets total transferred item amount for a particular receivable
	*/
	function get_total_transferred_receivable_amount($receivable_id,$date=0)
	{
		$this->db->from('receivable_items');
		$this->db->join('items','items.item_id = receivable_items.item_id');
		$this->db->where('receivable_id',$receivable_id);
		$amount = 0;
		foreach($this->db->get()->result_array() as $receivable_item)
		{
			$amount += $receivable_item['item_unit_price']*$receivable_item['quantity_transferred'];
		}
		return $amount;
	}
	
	/*
	Gets total returned item amount for a particular receivable
	*/
	function get_total_return_receivable_amount($receivable_id,$date=0)
	{
		$this->db->from('receivable_items');
		$this->db->join('items','items.item_id = receivable_items.item_id');
		$this->db->where('receivable_id',$receivable_id);
		$amount = 0;
		foreach($this->db->get()->result_array() as $receivable_item)
		{
			$amount += $receivable_item['item_unit_price']*$receivable_item['quantity_returned'];
		}
		return $amount;
	}
	
	/*
	Gets total discount item amount for a particular receivable
	*/
	function get_total_discount_receivable_amount($receivable_id,$date=0)
	{
		$this->db->from('receivable_items');
		$this->db->where('receivable_id',$receivable_id);
		$amount = 0;
		foreach($this->db->get()->result_array() as $receivable_item)
		{
			$amount += $receivable_item['item_unit_price']*($receivable_item['quantity_transferred']-$receivable_item['quantity_returned'])*($receivable_item['discount_percent']/100);
		}
		return $amount;
	}
	
	/*
	Gets customer total item amount for a particular receivable
	*/
	function get_customer_total_receivable_amount($customer_id,$date=0)
	{
		$amount = 0;
		$this->db->from('receivables');
		$this->db->where("person_id = $customer_id and receivable_date < '". date("Y-m-d H:i:s",$date) ."' and status > 0");
		foreach($this->db->get()->result_array() as $receivable)
		{
			$amount += $this->get_total_receivable_amount($receivable['receivable_id'],$date)-$this->get_total_discount_receivable_amount($receivable['receivable_id'],$date);
		}
		$this->db->from('receivable_beginning_balance');
		$this->db->where("customer_id = $customer_id and period < '". date("Y-m-d H:i:s",$date) ."' and deleted = 0");
		foreach($this->db->get()->result_array() as $receivable)
		{
			$amount += $receivable['balance'];
		}
		return $amount;
	}
	
	/*
	Gets customer total payments for receivables
	*/
	function get_customer_total_payment_amount($customer_id,$date=0)
	{
		$amount = 0;
		$this->db->from('receivable_payments');
		$this->db->join('receivables','receivables.receivable_id = receivable_payments.receivable_id');
		$this->db->where('receivables.person_id',$customer_id);
		$this->db->where("payment_date < '". date("Y-m-d H:i:s",$date) ."'");
		$this->db->where("mode = 0 and status > -1");
		foreach($this->db->get()->result_array() as $receivable_payment)
		{
			$amount += $receivable_payment['payment_amount'];
		}
		$this->db->from('receivable_payments');
		$this->db->join('receivable_beginning_balance','receivable_beginning_balance.receivable_id = receivable_payments.receivable_id');
		$this->db->where('receivable_beginning_balance.customer_id',$customer_id);
		$this->db->where("payment_date < '". date("Y-m-d H:i:s",$date) ."'");
		$this->db->where("mode = 1 and deleted = 0");
		foreach($this->db->get()->result_array() as $receivable_payment)
		{
			$amount += $receivable_payment['payment_amount'];
		}
		return $amount;
	}
}
?>
