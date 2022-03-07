<?php
class Sample_items extends CI_Model
{
	function get_info($sample_id)
	{
		$this->db->from('sample_items');
		$this->db->join('items','items.item_id = sample_items.item_id','left');
		$this->db->where('sample_id',$sample_id);
		return $this->db->get()->result_array();
	}
	
	function get_list($sample_id)
	{
		$this->db->select('*,sample_items.item_id AS item_id');
		$this->db->from('sample_items');
		$this->db->join('items','items.item_id = sample_items.item_id','left');
		$this->db->where('sample_id',$sample_id);
		$data = $this->db->get()->result_array();
		$list = array();
		foreach($data as $row)
		{
			$list[$row['item_id']] = $row['name'];
		}
		return $list;
	}
	
	/*
	Inserts or updates an receivable's items
	*/
	function save(&$sample_items_data, $sample_id)
	{
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		$this->delete($sample_id);
		foreach($sample_items_data as $row)
		{
			$row['sample_id'] = $sample_id;
			$this->db->insert('sample_items',$row);
		}
		$this->db->trans_complete();
		return true;
	}
	
	/*
	Deletes receivable items given an receivable
	*/
	function delete($sample_id)
	{
		$this->db->delete('sample_items', array('sample_id' => $sample_id));
		return true;
	}
	
	/*
	Gets total item amount for a particular receivable
	*/
	function get_total_receivable_amount($sample_id,$date=0)
	{
		$this->db->from('receivable_items');
		$this->db->join('items','items.item_id = receivable_items.item_id','left');
		$this->db->where('sample_id',$sample_id);
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
	function get_total_transferred_receivable_amount($sample_id,$date=0)
	{
		$this->db->from('receivable_items');
		$this->db->join('items','items.item_id = receivable_items.item_id');
		$this->db->where('sample_id',$sample_id);
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
	function get_total_return_receivable_amount($sample_id,$date=0)
	{
		$this->db->from('receivable_items');
		$this->db->join('items','items.item_id = receivable_items.item_id');
		$this->db->where('sample_id',$sample_id);
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
	function get_total_discount_receivable_amount($sample_id,$date=0)
	{
		$this->db->from('receivable_items');
		$this->db->where('sample_id',$sample_id);
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
			$amount += $this->get_total_receivable_amount($receivable['sample_id'],$date)-$this->get_total_discount_receivable_amount($receivable['sample_id'],$date);
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
		$this->db->join('receivables','receivables.sample_id = receivable_payments.sample_id');
		$this->db->where('receivables.person_id',$customer_id);
		$this->db->where("payment_date < '". date("Y-m-d H:i:s",$date) ."'");
		$this->db->where("mode = 0 and status > -1");
		foreach($this->db->get()->result_array() as $receivable_payment)
		{
			$amount += $receivable_payment['payment_amount'];
		}
		$this->db->from('receivable_payments');
		$this->db->join('receivable_beginning_balance','receivable_beginning_balance.sample_id = receivable_payments.sample_id');
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
