<?php
require_once ("secure_area.php");
class Config extends Secure_area 
{
	function __construct()
	{
		parent::__construct('config');
	}
	
	function index()
	{
		$this->load->view("config");
	}
		
	function save()
	{
		$batch_save_data=array(
		'company'=>$this->input->post('company'),
		'address'=>$this->input->post('address'),
		'phone'=>$this->input->post('phone'),
		'email'=>$this->input->post('email'),
		'fax'=>$this->input->post('fax'),
		'website'=>$this->input->post('website'),
		'default_tax_1_rate'=>$this->input->post('default_tax_1_rate'),		
		'default_tax_1_name'=>$this->input->post('default_tax_1_name'),		
		'default_tax_2_rate'=>$this->input->post('default_tax_2_rate'),	
		'default_tax_2_name'=>$this->input->post('default_tax_2_name'),		
		'currency_symbol'=>$this->input->post('currency_symbol'),
		'return_policy'=>$this->input->post('return_policy'),
		'language'=>$this->input->post('language'),
		'timezone'=>$this->input->post('timezone'),
		'print_after_sale'=>$this->input->post('print_after_sale'),
		'receivable_timestamp_override'=>$this->input->post('receivable_timestamp_override'),
		'purchase_timestamp_override'=>$this->input->post('purchase_timestamp_override')
		);
		
		if($_SERVER['HTTP_HOST'] !='ospos.pappastech.com' && $this->Appconfig->batch_save($batch_save_data))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('config_saved_successfully').'['.$this->input->post('shutdown').']'));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('config_saved_unsuccessfully')));
		}
	}
	
	function shutdown()
	{
		shell_exec("shutdown -s");
		echo json_encode(array('success'=>true,'message'=>'Server will shutdown in less than a minute.'));
	}
	
	function restart()
	{
		shell_exec("shutdown -r");
		echo json_encode(array('success'=>true,'message'=>'Server will restart in less than a minute.'));
	}
}
?>