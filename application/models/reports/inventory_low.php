<?php
require_once("report.php");
class Inventory_low extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		$dataColumns = array($this->lang->line('reports_item_number'), $this->lang->line('reports_item_name'), $this->lang->line('items_location'), $this->lang->line('reports_count'), $this->lang->line('reports_reorder_level'), "EQL");
		return $dataColumns;
	}
	
	public function getData(array $inputs)
	{
		$this->db->select('item_id, name, item_number, quantity, reorder_level, description, economic_quantity_level');
		$this->db->from('items');
		$this->db->where('quantity <= reorder_level and deleted=0');
		$this->db->order_by('name');
		
		return $this->db->get()->result_array();
	}
	
	public function getSummaryData(array $inputs)
	{
		$data = array('form_open'=>form_open("reports/low_inventory_request_po/width:540",array('id'=>'po_request_form')),'form_submit'=>"<input type='button' class='submit_button' id='generate_po' name='generate_po' value='Generate Purchase Order Request' />");
		return $data;
	}
}
?>