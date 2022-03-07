<?php
require_once("report.php");
class Summary_items_purchased extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array($this->lang->line('items_item_number'),$this->lang->line('reports_item'),$this->lang->line('reports_quantity_purchased'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'));
	}
	
	public function getData(array $inputs)
	{
		$this->db->select('item_number,items.name AS name,SUM(quantity_received) AS quantity_purchased,SUM(quantity_received*item_cost_price) AS subtotal,IF(discount_type,SUM(quantity_received*item_cost_price-discount_percent),SUM(quantity_received*item_cost_price*(100-discount_percent)/100)) AS total');
		$this->db->from('purchase_items');
		$this->db->join('purchases','purchases.purchase_id = purchase_items.purchase_id','left');
		$this->db->join('items','items.item_id = purchase_items.item_id','left');
		$this->db->where("status > 1 AND DATE(purchase_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		$this->db->group_by('purchase_items.item_id');
		return $this->db->get()->result_array();
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->db->select('SUM(quantity_received*item_cost_price) AS subtotal,SUM(IF(discount_type,(quantity_received*item_cost_price-discount_percent),(quantity_received*item_cost_price*(100-discount_percent)/100))) AS total');
		$this->db->from('purchase_items');
		$this->db->join('purchases','purchases.purchase_id = purchase_items.purchase_id','left');
		$this->db->where("status > 1 AND DATE(purchase_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		return $this->db->get()->row_array();
	}
}
?>