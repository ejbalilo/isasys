<?php
require_once("report.php");
class Detailed_receivings extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array('summary' => array('ID', $this->lang->line('reports_date'), $this->lang->line('reports_items_received'), $this->lang->line('reports_received_by'), $this->lang->line('reports_supplied_by'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'), $this->lang->line('reports_comments')),
					'details' => array($this->lang->line('items_item_number'), $this->lang->line('reports_name'), $this->lang->line('reports_category'), $this->lang->line('reports_quantity_purchased'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'), $this->lang->line('reports_discount'))
		);		
	}
	
	public function getData(array $inputs)
	{
		$query = $this->db->query("
			SELECT
				*,
				po_number AS purchase_id,
				ospos_purchases.purchase_id AS receiving_id,
				purchase_date AS receiving_date,
				COUNT(item_id) AS items_purchased,
				(SELECT CONCAT(first_name,' ',last_name) FROM ospos_people WHERE person_id = employee_id) AS employee_name,
				(SELECT CONCAT(company_name,' (',first_name,' ',last_name,')') FROM ospos_people LEFT JOIN ospos_suppliers ON ospos_suppliers.person_id = ospos_people.person_id WHERE ospos_people.person_id = ospos_purchases.person_id) AS supplier_name,
				SUM(quantity_received*item_cost_price) AS subtotal,
				IF(discount_type,SUM(quantity_received*item_cost_price-discount_percent),SUM(quantity_received*item_cost_price*(100-discount_percent)/100)) AS total
			FROM ospos_purchases
			LEFT JOIN ospos_purchase_items
				ON ospos_purchase_items.purchase_id = ospos_purchases.purchase_id
			WHERE status>1 AND DATE(purchase_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'
			GROUP BY ospos_purchases.purchase_id 
			ORDER BY ospos_purchases.purchase_id");
		$data['summary'] = $query->result_array();
		
		$data['details'] = array();
		foreach($data['summary'] as $key=>$value)
		{
			$this->db->select('*,
				purchase_items.item_id AS item_id,
				item_number,
				name,
				category,
				(quantity_received) AS quantity_purchased,
				description,
				(quantity_received*item_cost_price) AS subtotal,
				IF(discount_type,(quantity_received*item_cost_price-discount_percent),(quantity_received*item_cost_price*(100-discount_percent)/100)) AS total,
				discount_percent,
				discount_type');
			$this->db->from('purchase_items');
			$this->db->join('items','items.item_id = purchase_items.item_id','left');
			$this->db->where('purchase_id',$value['receiving_id']);
			
			$data['details'][$key] = $this->db->get()->result_array();
		}
		return $data;
	}
	
	public function getSummaryData(array $inputs)
	{
		$query = $this->db->query("
			SELECT
				SUM(quantity_received*item_cost_price) AS subtotal,
				SUM(IF(discount_type,quantity_received*item_cost_price-discount_percent,quantity_received*item_cost_price*(100-discount_percent)/100)) AS total
			FROM ospos_purchase_items
			LEFT JOIN ospos_purchases
				ON  ospos_purchases.purchase_id = ospos_purchase_items.purchase_id
			WHERE status>1 AND DATE(purchase_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		return $query->row_array();
	}
}
?>