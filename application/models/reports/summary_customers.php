<?php
require_once("report.php");
class Summary_customers extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array($this->lang->line('reports_customer'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'), $this->lang->line('reports_profit'));
	}
	
	public function getData(array $inputs)
	{
		$this->db->select("CONCAT(first_name,CHAR(32),last_name ) AS customer,SUM((quantity_transferred-quantity_returned)*item_unit_price) AS subtotal,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS total,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit");
		$this->db->from('receivables');
		$this->db->join('receivable_items','receivable_items.receivable_id = receivables.receivable_id','left');
		$this->db->join('people','people.person_id = receivables.person_id','left');
		$this->db->where("status > 0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		$this->db->group_by('receivables.person_id');
		return $this->db->get()->result_array();
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->db->select('SUM((quantity_transferred-quantity_returned)*item_unit_price) AS subtotal,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS total,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit');
		$this->db->from('receivables');
		$this->db->join('receivable_items','receivable_items.receivable_id = receivables.receivable_id','left');
		$this->db->join('people','people.person_id = receivables.person_id','left');
		$this->db->where("status > 0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		return $this->db->get()->row_array();
	}
}
?>