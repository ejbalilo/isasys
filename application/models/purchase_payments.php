<?php
class Purchase_payments extends CI_Model
{
	/*
	Gets purchase payments for a particular purchase
	*/
	function get_info($purchase_id,$mode)
	{
		$this->db->from('purchase_payments');
		$this->db->where('purchase_id',$purchase_id);
		$this->db->where('mode',$mode);
		//return an array of purchase payments for an item
		return $this->db->get()->result_array();
	}
	
	/*
	Inserts or updates an purchase's payments
	*/
	function save(&$purchase_payments_data, $purchase_id)
	{
		if($purchase_payments_data['payment_amount']==0)
		{
			return false;
		}
		else
		{
			$purchase_payments_data = array_merge($purchase_payments_data,array(
				'purchase_id'=>$purchase_id,
				'person_id'=>$this->Employee->get_logged_in_employee_info()->person_id,
				'payment_date'=>date("Y-m-d H:i:s")));
			$this->db->insert('purchase_payments',$purchase_payments_data);
			$this->db->trans_complete();
			return true;
		}
	}
	
	/*
	Deletes purchase payments given an purchase
	*/
	function delete($purchase_id)
	{
		return $this->db->delete('purchase_payments', array('purchase_id' => $purchase_id)); 
	}
	
	/*
	Gets total payments for a particular purchase
	*/
	function get_total_payments($purchase_id,$mode)
	{
		$this->db->select('sum(payment_amount)');
		$this->db->from('purchase_payments');
		$this->db->where('purchase_id',$purchase_id);
		$this->db->where('mode',$mode);
		$this->db->group_by('purchase_id');
		//return an array of purchase payments for an item
		$total = 0;
		foreach($this->db->get()->result_array() as $total)
			$total = $total['sum(payment_amount)'];
		return $total;
	}
}
?>
