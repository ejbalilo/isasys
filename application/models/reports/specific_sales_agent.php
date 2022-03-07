<?php
require_once("report.php");
class Specific_sales_agent extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns($show_profit=0)
	{
		$summary_headers = array("DR#", $this->lang->line('reports_date'), $this->lang->line('reports_items_purchased'), $this->lang->line('reports_sold_to'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'));
		if($show_profit)
			$summary_headers = array_merge($summary_headers,array($this->lang->line('reports_profit')));
		$summary_headers = array_merge($summary_headers,array("Balance", 'Commission', $this->lang->line('reports_comments')));
	
		$detail_headers = array($this->lang->line('items_item_number'), $this->lang->line('reports_name'), $this->lang->line('reports_category'), $this->lang->line('reports_quantity_purchased'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'));
		if($show_profit)
			$detail_headers = array_merge($detail_headers,array($this->lang->line('reports_profit')));
		$detail_headers = array_merge($detail_headers,array($this->lang->line('reports_discount')));
		
		return array('summary' => $summary_headers,'details' => $detail_headers);		
	}
	
	public function getData(array $inputs)
	{
		$query = $this->db->query("
			SELECT
				*,
				itr_number AS receivable_id,
				ospos_receivables.receivable_id AS sale_id,
				receivable_date AS sale_date,
				COUNT(item_id) AS items_purchased,
				(SELECT CONCAT(first_name,' ',last_name) FROM ospos_people WHERE person_id = employee_id) AS employee_name,
				(SELECT CONCAT(first_name,' ',last_name) FROM ospos_people WHERE ospos_people.person_id = ospos_receivables.person_id) AS customer_name,
				SUM(((quantity_transferred-quantity_returned)*item_unit_price)) AS subtotal,
				SUM(((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)) AS total,
				SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit,
				ospos_receivables.comments AS comments,
				0 AS payment_type
			FROM ospos_receivables
			LEFT JOIN ospos_receivable_items
				ON ospos_receivable_items.receivable_id = ospos_receivables.receivable_id
			WHERE status>0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."' AND sales_agent_id = ".$inputs['sales_agent_id']."
			GROUP BY ospos_receivables.receivable_id 
			ORDER BY ospos_receivables.receivable_id");
		$data['summary'] = $query->result_array();
		
		$data['details'] = array();
		foreach($data['summary'] as $key=>$value)
		{
			$details = array();
			
			$this->db->select('
				ospos_receivable_items.item_id AS item_id,
				item_number,
				name,
				category,
				(quantity_transferred-quantity_returned) AS quantity_purchased,
				("") AS serial_number,
				description,
				((quantity_transferred-quantity_returned)*item_unit_price) AS subtotal,
				((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS total,
				(0) AS tax,
				((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-((quantity_transferred-quantity_returned)*item_cost_price) AS profit,
				discount_percent');
			$this->db->from('receivable_items');
			$this->db->join('items','items.item_id = receivable_items.item_id','left');
			$this->db->where('receivable_id',$value['sale_id']);
			$this->db->where('ospos_receivable_items.item_id >',0);
			$details = $this->db->get()->result_array();
			
			$this->db->select('
				ospos_receivable_items.item_id AS item_id,
				("[KIT]") AS item_number,
				name,
				("Bundled Items") AS category,
				(quantity_transferred-quantity_returned) AS quantity_purchased,
				("") AS serial_number,
				description,
				((quantity_transferred-quantity_returned)*item_unit_price) AS subtotal,
				((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS total,
				(0) AS tax,
				((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-((quantity_transferred-quantity_returned)*item_cost_price) AS profit,
				discount_percent');
			$this->db->from('receivable_items');
			$this->db->join('item_kits','item_kits.item_kit_id = -receivable_items.item_id','left');
			$this->db->where('receivable_id',$value['sale_id']);
			$this->db->where('ospos_receivable_items.item_id <',0);
			
			$data['details'][$key] = array_merge($details,$this->db->get()->result_array());
		}
		
		$data['payments'] = array();
		foreach($data['summary'] as $key=>$value)
		{
			$this->db->select('SUM(payment_amount) AS total_payments');
			$this->db->from('receivable_payments');
			$this->db->where('receivable_id',$value['sale_id']);
			$data['payments'][$key] = $this->db->get()->row_array();
		}
		
		return $data;
	}
	
	public function getSummaryData(array $inputs)
	{
		$query = $this->db->query("
			SELECT
				SUM(((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)) AS total,
				SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit,
				SUM((quantity_transferred-quantity_returned)*item_unit_price) AS gross_total,
				SUM(((quantity_transferred-quantity_returned)*item_unit_price*discount_percent/100)) AS discount_total,
				SUM(((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)*sales_agent_commission/100) AS commission_total
			FROM ospos_receivables
			LEFT JOIN ospos_receivable_items
				ON ospos_receivable_items.receivable_id = ospos_receivables.receivable_id
			WHERE status>0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."' AND sales_agent_id = ".$inputs['sales_agent_id']);
		$total_sales = $query->row_array();

		$data = array('subtotal'=>$total_sales['gross_total'],'discount'=>$total_sales['discount_total'],'total'=>$total_sales['total']);
		if($inputs['show_profit'])
			$data = array_merge($data,array('profit'=>$total_sales['profit']));
		$query = $this->db->query("
			SELECT
				SUM(payment_amount) AS total
			FROM ospos_receivables
			LEFT JOIN ospos_receivable_payments
				ON ospos_receivable_payments.receivable_id = ospos_receivables.receivable_id
			WHERE mode=0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."' AND ospos_receivables.sales_agent_id = ".$inputs['sales_agent_id']);
		$total_payments = $query->row_array();
		
		return array_merge($data,array('balance'=>$data['total']-$total_payments['total'],'commission'=>$total_sales['commission_total']));
	}
}
?>