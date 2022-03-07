<?php
class Receivable_payments extends CI_Model
{
	/*
	Gets receivable payments for a particular receivable
	*/
	function get_info($receivable_id,$mode)
	{
		$this->db->from('receivable_payments');
		$this->db->where('receivable_id',$receivable_id);
		$this->db->where('mode',$mode);
		//return an array of receivable payments for an item
		return $this->db->get()->result_array();
	}
	
	/*
	Inserts or updates an receivable's payments
	*/
	function save(&$receivable_payments_data, $receivable_id)
	{
		if($receivable_payments_data['payment_amount']==0)
		{
			return false;
		}
		else
		{
			$receivable_payments_data = array_merge($receivable_payments_data,array(
				'receivable_id'=>$receivable_id,
				'person_id'=>$this->Employee->get_logged_in_employee_info()->person_id,
				'payment_date'=>date("Y-m-d H:i:s")));
			$this->db->insert('receivable_payments',$receivable_payments_data);
			$this->db->trans_complete();
			return true;
		}
	}
	
	/*
	Deletes receivable payments given an receivable
	*/
	function delete($receivable_id)
	{
		return $this->db->delete('receivable_payments', array('receivable_id' => $receivable_id)); 
	}
	
	/*
	Gets total payments for a particular receivable
	*/
	function get_total_payments($receivable_id,$mode)
	{
		$this->db->select('sum(payment_amount)');
		$this->db->from('receivable_payments');
		$this->db->where('receivable_id',$receivable_id);
		$this->db->where('mode',$mode);
		$this->db->group_by('receivable_id');
		//return an array of receivable payments for an item
		$total = 0;
		foreach($this->db->get()->result_array() as $total)
			$total = $total['sum(payment_amount)'];
		return $total;
	}
}
?>
