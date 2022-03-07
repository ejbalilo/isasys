<?php
require_once("report.php");
class Summary_suppliers extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array($this->lang->line('suppliers_supplier'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'));
	}
	
	public function getData(array $inputs)
	{
		$this->db->select('CONCAT(company_name,CHAR(32),CHAR(40),first_name,CHAR(32),last_name,CHAR(41)) AS supplier,SUM(quantity_received*item_cost_price) AS subtotal,IF(discount_type,SUM(quantity_received*item_cost_price-discount_percent),SUM(quantity_received*item_cost_price*(100-discount_percent)/100)) AS total');
		$this->db->from('purchases');
		$this->db->join('purchase_items','purchase_items.purchase_id = purchases.purchase_id','left');
		$this->db->join('people','people.person_id = purchases.person_id','left');
		$this->db->join('suppliers','suppliers.person_id = purchases.person_id','left');
		$this->db->where("status > 1 AND DATE(purchase_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		$this->db->group_by('purchases.person_id');
		return $this->db->get()->result_array();
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->db->select('SUM(quantity_received*item_cost_price) AS subtotal,SUM(IF(discount_type,(quantity_received*item_cost_price-discount_percent),(quantity_received*item_cost_price*(100-discount_percent)/100))) AS total');
		$this->db->from('purchases');
		$this->db->join('purchase_items','purchase_items.purchase_id = purchases.purchase_id','left');
		$this->db->join('people','people.person_id = purchases.person_id','left');
		$this->db->where("status > 1 AND DATE(purchase_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		return $this->db->get()->row_array();
	}
}
?>