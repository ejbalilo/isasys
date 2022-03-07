<?php
require_once("report.php");
class Summary_items_sold extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array($this->lang->line('items_item_number'),$this->lang->line('reports_item'),'Quantity Sold', $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'), $this->lang->line('reports_profit'));
	}
	
	public function getData(array $inputs)
	{
		$this->db->select('item_number,items.name AS name,SUM(quantity_transferred-quantity_returned) AS quantity_sold,SUM((quantity_transferred-quantity_returned)*item_unit_price) AS subtotal,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS total,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit');
		$this->db->from('receivable_items');
		$this->db->join('receivables','receivables.receivable_id = receivable_items.receivable_id','left');
		$this->db->join('items','items.item_id = receivable_items.item_id','left');
		$this->db->where("receivable_items.item_id > 0 AND status > 0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		$this->db->group_by('receivable_items.item_id');
		$data = $this->db->get()->result_array();
		
		$this->db->select('("[KIT]") AS item_number,item_kits.name AS name,SUM(quantity_transferred-quantity_returned) AS quantity_purchased,SUM((quantity_transferred-quantity_returned)*item_unit_price) AS subtotal,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS total,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit');
		$this->db->from('receivable_items');
		$this->db->join('receivables','receivables.receivable_id = receivable_items.receivable_id','left');
		$this->db->join('item_kits','item_kits.item_kit_id = -receivable_items.item_id','left');
		$this->db->where("receivable_items.item_id < 0 AND status > 0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		$this->db->group_by('receivable_items.item_id');
		
		return array_merge($data,$this->db->get()->result_array());
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->db->select('SUM((quantity_transferred-quantity_returned)*item_unit_price) AS subtotal,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS total,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit');
		$this->db->from('receivable_items');
		$this->db->join('receivables','receivables.receivable_id = receivable_items.receivable_id','left');
		$this->db->where("status > 0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		
		return $this->db->get()->row_array();
	}
}
?>