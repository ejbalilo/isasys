<?php
require_once("report.php");
class Detailed_customers extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array('Classification', 'Firstname', 'Lastname', 'Email', 'Phone Number', 'Account Officer', 'Sales Agent', 'Discount%');
	}
	
	public function getData(array $inputs)
	{
		$this->db->select('
			customer_classifications.classification_name,
			customer.last_name as last_name,
			customer.first_name as first_name,
			customer.email as email,
			customer.phone_number as phone_number,
			CONCAT( account_officer.first_name , CHAR(32) , account_officer.last_name ) as account_officer,
			CONCAT( sales_agent.first_name , CHAR(32) , sales_agent.last_name ) as sales_agent,
			customer_discount');
		$this->db->from('customers');
		$this->db->join('people as customer', 'customer.person_id = customers.person_id');
		$this->db->join('people as account_officer', 'account_officer.person_id = customers.account_officer_id', 'left');
		$this->db->join('people as sales_agent', 'sales_agent.person_id = customers.sales_agent_id', 'left');
		$this->db->join('customer_classifications', 'customer_classifications.classification_id = customers.classification_id', 'left');
		$this->db->where('customers.deleted',0);
		$this->db->order_by('customer.first_name,customer.last_name');
		
		return $this->db->get()->result_array();
	}
	
	public function getSummaryData(array $inputs)
	{
		return array();
	}
}
?>