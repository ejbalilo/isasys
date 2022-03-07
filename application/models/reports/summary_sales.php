<?php
require_once("report.php");
class Summary_sales extends Report
{
	function __construct()
	{
		parent::__construct();
	}

	public function getDataColumns()
	{
		return array($this->lang->line('reports_date'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'), $this->lang->line('reports_profit'));
	}
	
	public function getData(array $inputs)
	{
		/*$this->db->select('sale_date, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sum(profit) as profit');
		$this->db->from('sales_items_temp');
		if ($inputs['sale_type'] == 'sales')
		{
			$this->db->where('quantity_purchased > 0');
		}
		elseif ($inputs['sale_type'] == 'returns')
		{
			$this->db->where('quantity_purchased < 0');
		}
		
		$this->db->group_by('sale_date');
		$this->db->having('sale_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
		$this->db->order_by('sale_date');*/
		if($inputs['group_by'])
			$this->db->select("DATE_FORMAT(receivable_date, '%Y %M' ) AS sale_date,SUM((quantity_transferred-quantity_returned)*item_unit_price) AS subtotal,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS total,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit",false);
		else
			$this->db->select('DATE(receivable_date) AS sale_date,SUM((quantity_transferred-quantity_returned)*item_unit_price) AS subtotal,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS total,SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit');
		$this->db->from('receivables');
		$this->db->join('receivable_items','receivable_items.receivable_id = receivables.receivable_id','left');
		$this->db->join('customers','customers.person_id = receivables.person_id','left');
		$this->db->where("status > 0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		if($inputs['classification']!=-1)
			$this->db->where('classification_id',$inputs['classification']);
		if($inputs['group_by'])
			$this->db->group_by('YEAR(receivable_date),MONTH(receivable_date)');
		else
			$this->db->group_by('DATE(receivable_date)');
		return $this->db->get()->result_array();
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->db->select('
			SUM((quantity_transferred-quantity_returned)*item_unit_price) AS subtotal,
			SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100) AS total,
			SUM((quantity_transferred-quantity_returned)*item_unit_price*(100-discount_percent)/100)-SUM((quantity_transferred-quantity_returned)*item_cost_price) AS profit');
		$this->db->from('receivables');
		$this->db->join('receivable_items','receivable_items.receivable_id = receivables.receivable_id','left');
		$this->db->join('customers','customers.person_id = receivables.person_id','left');
		$this->db->where("status > 0 AND DATE(receivable_date) BETWEEN '".$inputs['start_date']."' AND '".$inputs['end_date']."'");
		if($inputs['classification']!=-1)
			$this->db->where('classification_id',$inputs['classification']);
		return $this->db->get()->row_array();	
	}

}
?>