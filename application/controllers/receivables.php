<?php
require_once ("secure_area.php");
require_once ("interfaces/idata_controller.php");
class Receivables extends Secure_area implements iData_controller
{
	function __construct()
	{
		parent::__construct('receivables');
	}

	function index()
	{
		$config['base_url'] = site_url('?c=receivables&m=index');
		$config['total_rows'] = $this->Receivable->count_all();
		$config['per_page'] = '500';
		$this->pagination->initialize($config);
		
		if($this->input->post('mode')=="beginningbalance")
			$data['mode']="beginningbalance";
		else
			$data['mode']="itemtransfer";
		if($this->input->post('show')=="")
			$data['show']="all_pending";
		else
			$data['show']=$this->input->post('show');
		$data['modes']=array('itemtransfer'=>$this->lang->line('receivables_item_transfer'),'beginningbalance'=>$this->lang->line('receivables_beginning_balance'));
		$data['shows']=array('all'=>'All','all_pending'=>'All Pending','pending'=>'Pending','nondue'=>'Transaction Not Due','due'=>'Transaction Due','waiting'=>'Waiting for Payments','complete'=>'Transaction Complete');
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		if($data['mode']=="beginningbalance")
			$data['manage_table']=get_receivables_manage_table($this->Receivable->get_all2($config['per_page'],$this->input->get('page')),$this,$data['mode']);
		else
			$data['manage_table']=get_receivables_manage_table($this->Receivable->get_all($config['per_page'],$this->input->get('page'),$data['show']),$this,$data['mode']);
		$this->load->view('receivables/manage',$data);
	}
	
	function cso($receivable_id,$type='')
	{
		$data['type']=$type;
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['form_width']=$this->get_form_width();
		$data['receivable_id']=$receivable_id;
		$data['itr_number']=$this->Receivable->get_info($receivable_id)->itr_number;
		$data['receivable_date']=date("M d, Y",strtotime($this->Receivable->get_info($receivable_id)->receivable_date));
		$data['terms']=$this->Receivable->get_info($receivable_id)->terms;
		$data['customer_name']=$this->Receivable->get_info($receivable_id)->first_name.' '.$this->Receivable->get_info($receivable_id)->last_name;
		$data['customer_address1']=$this->Receivable->get_info($receivable_id)->address_1.' '.$this->Receivable->get_info($receivable_id)->address_2.' '.$this->Receivable->get_info($receivable_id)->city;
		$data['customer_address2']=$this->Receivable->get_info($receivable_id)->state.' '.$this->Receivable->get_info($receivable_id)->country.' '.$this->Receivable->get_info($receivable_id)->zip;
		$data['issued_to']=$this->Receivable->get_info($receivable_id)->issued_to;
		$data['destination']=$this->Receivable->get_info($receivable_id)->destination;
		$data['comments']=$this->Receivable->get_info($receivable_id)->comments;
		$data['discount']=$this->Receivable->get_info($receivable_id)->discount;
		$data['account_officer']=$this->Receivable->get_info($receivable_id)->account_officer_id;
		$data['account_officer']=$this->Customer->get_info($data['account_officer'])->first_name.' '.$this->Customer->get_info($data['account_officer'])->last_name;
		$data['sales_agent_id']=$this->Receivable->get_info($receivable_id)->sales_agent_id;
		$data['sales_agent_commission']=$this->Receivable->get_info($receivable_id)->sales_agent_commission;
		$data['sales_agent']=$this->Customer->get_info($data['sales_agent'])->first_name.' '.$this->Customer->get_info($data['sales_agent'])->last_name;
		$this->load->view('receivables/cso',$data);
	}
	
	function sor($receivable_id,$type='')
	{
		$data['type']=$type;
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['form_width']=$this->get_form_width();
		$data['receivable_id']=$receivable_id;
		$data['receivable_date']=date("M d, Y",strtotime($this->Receivable->get_info($receivable_id)->receivable_date));
		$data['terms']=$this->Receivable->get_info($receivable_id)->terms;
		$data['customer_name']=$this->Receivable->get_info($receivable_id)->first_name.' '.$this->Receivable->get_info($receivable_id)->last_name;
		$data['customer_address1']=$this->Receivable->get_info($receivable_id)->address_1.' '.$this->Receivable->get_info($receivable_id)->address_2.' '.$this->Receivable->get_info($receivable_id)->city;
		$data['customer_address2']=$this->Receivable->get_info($receivable_id)->state.' '.$this->Receivable->get_info($receivable_id)->country.' '.$this->Receivable->get_info($receivable_id)->zip;
		$data['issued_to']=$this->Receivable->get_info($receivable_id)->issued_to;
		$data['destination']=$this->Receivable->get_info($receivable_id)->destination;
		$data['comments']=$this->Receivable->get_info($receivable_id)->comments;
		$data['discount']=$this->Customer->get_info($this->Receivable->get_info($receivable_id)->person_id)->customer_discount;
		$data['account_officer']=$this->Receivable->get_info($receivable_id)->account_officer_id;
		$data['account_officer']=$this->Customer->get_info($data['account_officer'])->first_name.' '.$this->Customer->get_info($data['account_officer'])->last_name;
		$data['sales_agent_id']=$this->Receivable->get_info($receivable_id)->sales_agent_id;
		$data['sales_agent_commission']=$this->Receivable->get_info($receivable_id)->sales_agent_commission;
		$data['sales_agent']=$this->Customer->get_info($data['sales_agent'])->first_name.' '.$this->Customer->get_info($data['sales_agent'])->last_name;
		$this->load->view('receivables/sor',$data);
	}
	
	function dr($receivable_id,$type='')
	{
		$receivable_info = $this->Receivable->get_info($receivable_id);
		$data['type']=$type;
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['form_width']=$this->get_form_width();
		$data['receivable_id']=$receivable_id;
		$data['itr_number']=$receivable_info->itr_number;
		$data['receivable_date']=date("M d, Y",strtotime($receivable_info->receivable_date));
		$data['terms']=$receivable_info->terms;
		$data['customer_name']=$receivable_info->first_name.' '.$receivable_info->last_name;
		$data['customer_address1']=$receivable_info->address_1.' '.$receivable_info->address_2.' '.$receivable_info->city;
		$data['customer_address2']=$receivable_info->state.' '.$receivable_info->country.' '.$receivable_info->zip;
		$data['issued_to']=$receivable_info->issued_to;
		$data['destination']=$receivable_info->destination;
		$data['comments']=$receivable_info->comments;
		$data['discount']=$receivable_info->discount;
		$data['account_officer_id']=$receivable_info->account_officer_id;
		$data['account_officer']=$this->Customer->get_info($receivable_info->account_officer_id)->first_name.' '.$this->Customer->get_info($receivable_info->account_officer_id)->last_name;
		$data['sales_agent_id']=$receivable_info->sales_agent_id;
		$data['sales_agent_commission']=$receivable_info->sales_agent_commission;
		$data['sales_agent']=$this->Customer->get_info($receivable_info->sales_agent_id)->first_name.' '.$this->Customer->get_info($receivable_info->sales_agent_id)->last_name;
		$this->load->view('receivables/dr',$data);
	}
	
	function dr2($receivable_id,$type='')
	{
		$receivable_info = $this->Receivable->get_info($receivable_id);
		$data['type']=$type;
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['form_width']=$this->get_form_width();
		$data['receivable_id']=$receivable_id;
		$data['itr_number']=$receivable_info->itr_number;
		$data['receivable_date']=date("M d, Y",strtotime($receivable_info->receivable_date));
		$data['terms']=$receivable_info->terms;
		$data['customer_name']=$receivable_info->first_name.' '.$receivable_info->last_name;
		$data['customer_address1']=$receivable_info->address_1.' '.$receivable_info->address_2.' '.$receivable_info->city;
		$data['customer_address2']=$receivable_info->state.' '.$receivable_info->country.' '.$receivable_info->zip;
		$data['issued_to']=$receivable_info->issued_to;
		$data['destination']=$receivable_info->destination;
		$data['comments']=$receivable_info->comments;
		$data['discount']=$receivable_info->discount;
		$data['account_officer_id']=$receivable_info->account_officer_id;
		$data['account_officer']=$this->Customer->get_info($receivable_info->account_officer_id)->first_name.' '.$this->Customer->get_info($receivable_info->account_officer_id)->last_name;
		$data['sales_agent_id']=$receivable_info->sales_agent_id;
		$data['sales_agent_commission']=$receivable_info->sales_agent_commission;
		$data['sales_agent']=$this->Customer->get_info($receivable_info->sales_agent_id)->first_name.' '.$this->Customer->get_info($receivable_info->sales_agent_id)->last_name;
		$this->load->view('receivables/dr2',$data);
	}

	function search()
	{
		$search=$this->input->post('search');
		$show=$this->input->post('show');
		$data_rows=get_receivables_manage_table_data_rows($this->Receivable->search($search),$this,'',$show);
		echo $data_rows;
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		$suggestions = $this->Receivable->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function get_row()
	{
		$receivable_id = $this->input->post('row_id');
		$data_row=get_receivable_data_row($this->Receivable->get_info($receivable_id),$this);
		echo $data_row;
	}
	
	function get_row2()
	{
		$receivable_id = $this->input->post('row_id');
		$data_row=get_receivable_data_row2($this->Receivable->get_info2($receivable_id),$this);
		echo $data_row;
	}

	function view($receivable_id=-1)
	{
		$data['controller_name']=strtolower(get_class());
		$data['receivable_info']=$this->Receivable->get_info($receivable_id);
		$this->load->view("receivables/form",$data);
	}
	
	function payment($receivable_id=-1)
	{
		$data['controller_name']=strtolower(get_class());
		$data['receivable_info']=$this->Receivable->get_info($receivable_id);
		$this->load->view("receivables/payment",$data);
	}
	
	function view2($receivable_id=-1)
	{
		$data['controller_name']=strtolower(get_class());
		$data['receivable_info']=$this->Receivable->get_info2($receivable_id);
		$this->load->view("receivables/form2",$data);
	}
	
	function save($receivable_id=-1)
	{
		$receivable_data = array();
		if($receivable_id==-1 || $receivable_id!=-1?$this->Receivable->get_info($receivable_id)->status==0:false)
		{
			$receivable_data = array(
				'receivable_date'=>$this->input->post('timestamp'),
				'person_id'=>$this->input->post('customer_id'),
				'terms'=>$this->input->post('terms'),
				'discount'=>$this->Customer->get_info($this->input->post('customer_id'))->customer_discount,
				'employee_id'=>$this->Employee->get_logged_in_employee_info()->person_id,
				'account_officer_id'=>$this->Customer->get_info($this->input->post('customer_id'))->account_officer_id,
				'sales_agent_id'=>$this->Customer->get_info($this->input->post('customer_id'))->sales_agent_id,
				'sales_agent_commission'=>$this->Customer->get_info($this->input->post('customer_id'))->sales_agent_commission
			);
			if($this->input->post('approve_request'))
			{
				$receivable_data = array_merge($receivable_data,array('status'=>1));
			}
		}
		$receivable_data = array_merge($receivable_data,array('issued_to'=>$this->input->post('issued_to'),'destination'=>$this->input->post('destination'),'comments'=>$this->input->post('comments')));

		if($this->Receivable->save($receivable_data,$receivable_id))
		{
			$receivable_id = $receivable_data['receivable_id'];
			//New receivable
			if($receivable_id==-1)
			{
				$status = 0;
				if($this->Employee->has_permission('receivables_approve_request',$this->Employee->get_logged_in_employee_info()->person_id) && $this->input->post('approve_request'))
				{
					$status = 1;
				}
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('receivables_successful_adding').' '.$receivable_data['receivable_date'],'receivable_id'=>$receivable_data['receivable_id']));
			}
			else //previous receivable
			{
				$status = $this->Receivable->get_info($receivable_id)->status;
				$receivable_date = $this->Receivable->get_info($receivable_id)->receivable_date;
				
				if($this->Employee->has_permission('receivables_approve_request',$this->Employee->get_logged_in_employee_info()->person_id) && $this->input->post('approve_request') && $status==0)
				{
					$status = 1;
					$receivable_date = date('Y-m-d H:i:s');
				}
				else if($this->Employee->has_permission('receivables_return',$this->Employee->get_logged_in_employee_info()->person_id) && $this->input->post('finalize') && $status==1)
				{
					$status = 2;
				}
				else if($this->input->post('deny_request'))
				{
					$status = -1;
				}
				else if($this->Employee->has_permission('receivables_undo',$this->Employee->get_logged_in_employee_info()->person_id) && $this->input->post('undo_approve'))
				{
					$status = 0;
				}
				else if($this->Employee->has_permission('receivables_undo',$this->Employee->get_logged_in_employee_info()->person_id) && $this->input->post('undo'))
				{
					$status = 1;
				}
				
				$this->db->where('receivable_id', $receivable_id);
				$this->db->update('receivables',array('status'=>$status,'receivable_date'=>$receivable_date));
				
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('receivables_successful_updating').' '.$this->Receivable->get_info($receivable_id)->receivable_date,'receivable_id'=>$receivable_id));
			}
			
			if(($status==0 && !$this->input->post('undo_approve')) || ($status==1 && $this->input->post('approve_request')))
			{
				$receivable_items = array();
				if($this->input->post('receivable_item'))
				{
					foreach($this->input->post('receivable_item') as $item_id => $quantity)
					{
						$receivable_items_quantity[] = array('quantity_transferred'=>$quantity);
					}
					$k = 0;
					foreach($this->input->post('item_price') as $item_id => $unit_price)
					{
						$receivable_items[] = array(
							'item_id' => $item_id,
							'quantity_transferred' => $receivable_items_quantity[$k]['quantity_transferred'],
							'item_cost_price' => $item_id>0?$this->Item->get_info($item_id)->cost_price:$this->Item_kit->get_cost_price(-$item_id),
							'item_unit_price' => $unit_price,
							'discount_percent' => $this->Customer->get_info($this->Receivable->get_info($receivable_id)->person_id)->customer_discount
							);
						$k++;
					}
				}
				$this->Receivable_items->save($receivable_items, $receivable_id);
			}
			
			if($status==1 || ($status==2 && $this->input->post('finalize')))
			{
				if($this->input->post('approve_request'))
				{
					$itr_number = $this->config->item('last_itr_number')+1;
					$itr_number_data = array('itr_number'=>$itr_number);
					$this->Receivable->save($itr_number_data,$receivable_id);
					$this->Appconfig->save('last_itr_number',$itr_number);
					foreach($this->Receivable_items->get_info($receivable_id) as $receivable_item)
					{
						$this->update_inventory($receivable_id,$receivable_item['item_id'],$receivable_item['quantity_transferred'],$status);
					}
				}
				if($this->input->post('receivable_item_return'))
				{
					$receivable_items = array();
					$default_discount = $this->Receivable->get_info($receivable_id)->discount;
					$discounts = $this->input->post('receivable_item_discount');
					foreach($this->input->post('receivable_item_return') as $item_id=>$quantity)
					{
						$receivable_items[] = array(
							'item_id' => $item_id,
							'quantity_transferred' => $this->get_item_info($receivable_id,$item_id)->quantity_transferred,
							'item_cost_price' => $this->get_item_info($receivable_id,$item_id)->item_cost_price,
							'item_unit_price' => $this->get_item_info($receivable_id,$item_id)->item_unit_price,
							'quantity_returned' => (int)$quantity,
							'discount_percent' => $discounts[$item_id]!=''?$default_discount:0
							);
					}
					$this->Receivable_items->save($receivable_items, $receivable_id);
					if($this->input->post('finalize'))
						foreach($this->Receivable_items->get_info($receivable_id) as $receivable_item)
							$this->update_inventory($receivable_id,$receivable_item['item_id'],$receivable_item['quantity_returned'],$status);
				}
			}
			
			if($status==0 && $this->input->post('undo_approve'))
			{
				foreach($this->Receivable_items->get_info($receivable_id) as $receivable_item)
				{
					$this->update_inventory($receivable_id,$receivable_item['item_id'],$receivable_item['quantity_transferred'],-2);
				}
			}
			
			if($status==1 && $this->input->post('undo'))
			{
				foreach($this->Receivable_items->get_info($receivable_id) as $receivable_item)
				{
					$this->update_inventory($receivable_id,$receivable_item['item_id'],$receivable_item['quantity_returned'],-$status);
				}
			}
			else if($status==1 && $this->input->post('undo'))
			{
				foreach($this->Receivable_items->get_info($receivable_id) as $receivable_item)
				{
					$this->update_inventory($receivable_id,$receivable_item['item_id'],$receivable_item['quantity_returned'],-$status);
				}
			}
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('receivables_error_adding_updating').' '.
			$receivable_data['receivable_date'],'receivable_id'=>-1));
		}
	}
	
	function save2($receivable_id=-1)
	{
		$receivable_data = array('period'=>$this->input->post('period'));
		if($receivable_id==-1)
		{
			$receivable_data = array_merge($receivable_data,array(
			'customer_id'=>$this->input->post('customer_id'),
			'balance'=>$this->input->post('balance'),
			'employee_id'=>$this->Employee->get_logged_in_employee_info()->person_id
			));
		}
		else
		{
			$receivable_data = array_merge($receivable_data,array('receivable_id'=>$receivable_id));
		}
		$receivable_data = array_merge($receivable_data,array('comments'=>$this->input->post('comments')));

		if($this->Receivable->save2($receivable_data,$receivable_id))
		{
			//New receivable
			if($receivable_id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('receivables_successful_adding').' '.to_currency($receivable_data['balance']).' for '.$receivable_data['customer_id'],'receivable_id'=>$receivable_data['receivable_id']));
			}
			else //previous receivable
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('receivables_successful_updating').' ','receivable_id'=>$receivable_id));
			}
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('receivables_error_adding_updating').' '.to_currency($receivable_data['balance']).' for '.$receivable_data['customer_id'],'receivable_id'=>-1));
		}
	}
	
	function delete()
	{
		$receivables_to_delete=$this->input->post('ids');

		if($this->Receivable->delete_list($receivables_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('receivables_successful_deleted').' '.
			count($receivables_to_delete).' '.$this->lang->line('receivables_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('receivables_cannot_be_deleted')));
		}
	}
	
	function get_item_info($receivable_id,$item_id)
	{
		$this->db->from('receivable_items');
		$this->db->where('receivable_id',$receivable_id);
		$this->db->where('item_id',$item_id);
		return $this->db->get()->row();
	}
	
	/*
	get the width for the add/edit form
	*/
	function get_form_width()
	{
		return 360;
	}
	
	function update_inventory($receivable_id,$item_id,$quantity,$status)
	{
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$itr_number = $this->Receivable->get_info($receivable_id)->itr_number;
		$trans_comment = "Receivable Error";
		
		if($item_id>0)
		{
			if($status==1)
			{
				$trans_comment = "RECEIVABLE $itr_number";
				$quantity *= -1;
			}
			else if($status==2)
			{
				$trans_comment = "RECEIVABLE $itr_number";
			}
			else if($status==-1)
			{
				$trans_comment = "RECEIVABLE $itr_number UNDO";
				$quantity *= -1;
			}
			else if($status==-2)
			{
				$trans_comment = "RECEIVABLE $itr_number UNDO";
			}
			
			$inv_data = array
			(
				'trans_date'=>date('Y-m-d H:i:s'),
				'trans_items'=>$item_id,
				'trans_user'=>$employee_id,
				'trans_comment'=>$trans_comment,
				'trans_inventory'=>$quantity
			);
			$this->Inventory->insert($inv_data);
			
			$item_data = $this->Item->get_info($item_id);
			$item_data = array('quantity'=>$item_data->quantity + $quantity);
			if($this->Item->exists($item_id))
				$this->Item->save($item_data,$item_id);
		}
		else
		{
			if($status==1)
			{
				$trans_comment = "RECEIVABLE $itr_number (KIT)";
				$quantity *= -1;
			}
			else if($status==2)
			{
				$trans_comment = "RECEIVABLE $itr_number (KIT)";
			}
			else if($status==-1)
			{
				$trans_comment = "RECEIVABLE $itr_number (KIT) UNDO";
				$quantity *= -1;
			}
			
			foreach($this->Item_kit_items->get_info(-$item_id) as $item_kit_row)
			{
				$inv_data = array
				(
					'trans_date'=>date('Y-m-d H:i:s'),
					'trans_items'=>$item_kit_row['item_id'],
					'trans_user'=>$employee_id,
					'trans_comment'=>$trans_comment,
					'trans_inventory'=>$item_kit_row['quantity']*$quantity
				);
				$this->Inventory->insert($inv_data);
				
				$item_data = $this->Item->get_info($item_kit_row['item_id']);
				$item_data = array('quantity'=>($item_data->quantity + $inv_data['trans_inventory']));
				if($this->Item->exists($item_kit_row['item_id']))
					$this->Item->save($item_data,$item_kit_row['item_id']);
			}
		}
	}
	
	function add_payment($receivable_id=-1,$mode)
	{
		$payment_data = array(
		'payment_amount'=>str_replace(',','',$this->input->post('amount')),
		'or_number'=>$this->input->post('or_number'),
		'comments'=>$this->input->post('comments'),
		'mode'=>$mode
		);

		if($this->Receivable_payments->save($payment_data,$receivable_id))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('receivables_successful_updating').' '.$this->Receivable->get_info($receivable_id)->receivable_date.'-'.$payment_data['comments'],'receivable_id'=>$receivable_id));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('receivables_error_adding_updating').' '.$this->Receivable->get_info($receivable_id)->receivable_date,'receivable_id'=>-1));
		}
	}
	
	function customer_search()
	{
		$suggestions = $this->Customer->get_customer_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
	
	function item_search()
	{
		$suggestions = $this->Item->get_item_search_suggestions_srp($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
}
?>