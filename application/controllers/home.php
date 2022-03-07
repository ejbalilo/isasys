<?php
require_once ("secure_area.php");

class Home extends Secure_area 
{
	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
		$this->load->view("home");
	}
	
	function logout()
	{
		$this->Employee->logout();
	}
	
	/*
	Loads the profile edit form
	*/
	function edit_profile($employee_id=-1)
	{
		$this->load->view("employees/profile");
	}
	
	function save_profile()
	{
		$old_password = md5($this->input->post('old_password'));
		$new_password = md5($this->input->post('new_password'));
		if($this->Employee->get_logged_in_employee_info()->password==$old_password)
		{
			if($this->Employee->save2($new_password,$this->Employee->get_logged_in_employee_info()->person_id))
			{
				echo json_encode(array('success'=>true,'message'=>'Password changed successfully'));
			}
			else
			{
				echo json_encode(array('success'=>false,'message'=>'Password change unsuccessful'));
			}
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>'Invalid password'));
		}
	}
}
?>