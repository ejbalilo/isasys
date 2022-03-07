<?php
class Sample_usages extends CI_Model
{
	function get_info($sample_id)
	{
		$this->db->from('sample_usages');
		$this->db->join('items','items.item_id = sample_usages.item_id','left');
		$this->db->where('sample_id',$sample_id);
		return $this->db->get()->result_array();
	}
	
	function save(&$sample_usages_data,$sample_id)
	{
		if($sample_usages_data['usage_amount']==0)
		{
			return false;
		}
		else
		{
			$sample_usages_data = array_merge($sample_usages_data,array(
				'sample_id'=>$sample_id,
				'item_id'=>$sample_usages_data['item_id'],
				'usage_amount'=>$sample_usages_data['usage_amount'],
				'usage_date'=>date("Y-m-d H:i:s"),
				'comments'=>$sample_usages_data['comments'],
				'person_id'=>$this->Employee->get_logged_in_employee_info()->person_id));
			$this->db->insert('sample_usages',$sample_usages_data);
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
