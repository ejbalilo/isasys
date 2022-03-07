<?php
require_once("report.php");
class Detailed_payments extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array('summary' => array("ID", $this->lang->line('reports_date'), $this->lang->line('reports_items_purchased'), $this->lang->line('reports_sold_to'), $this->lang->line('reports_total'), $this->lang->line('reports_profit'), "Balance", $this->lang->line('reports_comments')),
					'details' => array($this->lang->line('items_item_number'), $this->lang->line('reports_name'), $this->lang->line('reports_category'), $this->lang->line('reports_quantity_purchased'), $this->lang->line('reports_total'), $this->lang->line('reports_profit'),$this->lang->line('reports_discount'))
		);		
	}
	
	public function getData(array $inputs)
	{
		/*$this->db->select('sale_id, sale_date, sum(quantity_purchased) as items_purchased, CONCAT(employee.first_name," ",employee.last_name) as employee_name, CONCAT(customer.first_name," ",customer.last_name) as customer_name, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit, payment_type, comment', false);
		$this->db->from('sales_items_temp');
		$this->db->join('people as employee', 'sales_items_temp.employee_id = employee.person_id');
		$this->db->join('people as customer', 'sales_items_temp.customer_id = customer.person_id', 'left');
		$this->db->where('sale_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
		if ($inputs['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($inputs['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		$this->db->group_by('sale_id');
		$this->db->order_by('sale_date');

		$data = array();
		$data['summary'] = $this->db->get()->result_array();*/
		
		$query = $this->db->query("
			SELECT
				*,
				itr_number AS receivable_id,
				ospos_receivables.receivable_id AS sale_id,
				receivable_date AS sale_date,
				COUNT(item_id) AS items_purchased,
				(SELECT CONCAT(first_name,' ',last_name) FROM ospos_people WHERE person_id = employee_id) AS employee_name,
				(SELECT CONCAT(first_name,' ',last_name) FROM ospos_people WHERE ospos_people.person_id = ospos_receivables.person_id) AS customer_name,
				SUM(((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)) AS subtotal,
				SUM(((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)) AS total,
				SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit,
				0 AS payment_type
			FROM ospos_receivables
			LEFT JOIN ospos_receivable_items
				ON ospos_receivable_items.receivable_id = ospos_receivables.receivable_id
			WHERE status>0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'
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
				((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS subtotal,
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
				((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS subtotal,
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
				SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit
			FROM ospos_receivables
			LEFT JOIN ospos_receivable_items
				ON ospos_receivable_items.receivable_id = ospos_receivables.receivable_id
			WHERE status>0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		$total_sales = $query->row_array();
	
		/*$this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit');
		$this->db->from('sales_items_temp');
		$this->db->where('sale_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
		if ($inputs['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($inputs['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		return $this->db->get()->row_array();*/
		$data = array('total'=>$total_sales['total'],'profit'=>$total_sales['profit']);
		$query = $this->db->query("
			SELECT
				SUM(payment_amount) AS total
			FROM ospos_receivables
			LEFT JOIN ospos_receivable_payments
				ON ospos_receivable_payments.receivable_id = ospos_receivables.receivable_id
			WHERE mode=0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		$total_payments = $query->row_array();
		
		return array_merge($data,array('balance'=>$data['total']-$total_payments['total']));
	}
}
?>