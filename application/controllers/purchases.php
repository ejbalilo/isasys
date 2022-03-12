<?php
require_once ("secure_area.php");
require_once ("interfaces/idata_controller.php");
class Purchases extends Secure_area implements iData_controller
{
	function __construct()
	{
		parent::__construct('purchases');
	}

	function index()
	{
		$config['base_url'] = site_url('?c=purchases&m=index');
		$config['total_rows'] = $this->Purchase->count_all();
		$config['per_page'] = '500';
		$this->pagination->initialize($config);
		
		if($this->input->post('show')=="")
			$data['show']="all_pending";
		else
			$data['show']=$this->input->post('show');
		$data['shows']=array('all'=>'All','all_pending'=>'All Pending','pending'=>'Pending for Approval','approved'=>'Approved','complete'=>'Transaction Complete');
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['manage_table']=get_purchases_manage_table($this->Purchase->get_all($config['per_page'],$this->input->get('per_page'),$data['show']),$this);
		$this->load->view('purchases/manage',$data);
	}
	
	function cpo($purchase_id,$type='')
	{
		$data['type']=$type;
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['form_width']=$this->get_form_width();
		$data['purchase_id']=$purchase_id;
		$data['po_number']=$this->Purchase->get_info($purchase_id)->po_number;
		$data['purchase_date']=date("M d, Y",strtotime($this->Purchase->get_info($purchase_id)->purchase_date));
		$data['terms']=$this->Purchase->get_info($purchase_id)->terms;
		$data['company_name']=$this->Purchase->get_info($purchase_id)->company_name;
		$data['contact_person']=$this->Purchase->get_info($purchase_id)->first_name.' '.$this->Purchase->get_info($purchase_id)->last_name;
		$data['supplier_address1']=$this->Purchase->get_info($purchase_id)->address_1.' '.$this->Purchase->get_info($purchase_id)->address_2.' '.$this->Purchase->get_info($purchase_id)->city;
		$data['supplier_address2']=$this->Purchase->get_info($purchase_id)->state.' '.$this->Purchase->get_info($purchase_id)->country.' '.$this->Purchase->get_info($purchase_id)->zip;
		$data['phone_number']=$this->Purchase->get_info($purchase_id)->phone_number;
		$data['comments']=$this->Purchase->get_info($purchase_id)->comments;
		$data['discount']=$this->Customer->get_info($this->Purchase->get_info($purchase_id)->person_id)->customer_discount;
		$data['purchase_status']=$this->Purchase->get_info($purchase_id)->status;
		$this->load->view('purchases/cpo',$data);
	}

	function search()
	{
		$search=$this->input->post('search');
		$data_rows=get_purchases_manage_table_data_rows($this->Purchase->search($search),$this,'');
		echo $data_rows;
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		$suggestions = $this->Purchase->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function get_row()
	{
		$purchase_id = $this->input->post('row_id');
		$data_row=get_purchase_data_row($this->Purchase->get_info($purchase_id),$this);
		echo $data_row;
	}

	function view($purchase_id=-1)
	{
		$data['controller_name']=strtolower(get_class());
		$data['purchase_info']=$this->Purchase->get_info($purchase_id);
		$this->load->view("purchases/form",$data);
	}
	
	function save($purchase_id=-1)
	{
		$purchase_data = array();
		if($purchase_id==-1 || $purchase_id!=-1?$this->Purchase->get_info($purchase_id)->status==0:false)
		{
			$purchase_data = array(
			'purchase_date'=>$this->input->post('timestamp'),
			'person_id'=>$this->input->post('supplier_id'),
			'employee_id'=>$this->Employee->get_logged_in_employee_info()->person_id
			);
		}
		$purchase_data = array_merge($purchase_data,array('comments'=>$this->input->post('comments')));

		if($this->Purchase->save($purchase_data,$purchase_id))
		{
			//New item kit
			if($purchase_id==-1)
			{
				$status = 0;
				if($this->Employee->has_permission('purchases_approve_request',$this->Employee->get_logged_in_employee_info()->person_id) && $this->input->post('approve_request'))
				{
					$status = 1;
				}
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('purchases_successful_adding').' '.$purchase_data['comments'],'purchase_id'=>$purchase_data['purchase_id']));
				$purchase_id = $purchase_data['purchase_id'];
			}
			else //previous item
			{
				$status = $this->Purchase->get_info($purchase_id)->status;
				$purchase_date = $this->Purchase->get_info($purchase_id)->purchase_date;
				
				if($this->Employee->has_permission('purchases_approve_request',$this->Employee->get_logged_in_employee_info()->person_id) && $this->input->post('approve_request') && $status==0 && !$this->input->post('undo'))
				{
					$status = 1;
					$purchase_date = date('Y-m-d H:i:s');
				}
				else if($this->Employee->has_permission('purchases_receive_discount',$this->Employee->get_logged_in_employee_info()->person_id) && $this->input->post('finalize') && $status==1)
				{
					$status = 2;
				}
				else if($this->Employee->has_permission('purchases_undo',$this->Employee->get_logged_in_employee_info()->person_id) && $this->input->post('undo'))
				{
					if ($status==2)
						$status = 1;
					else if ($status==1)
						$status = 0;
				}
				
				$this->db->where('purchase_id', $purchase_id);
				$this->db->update('purchases',array('status'=>$status,'purchase_date'=>$purchase_date));
				
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('purchases_successful_updating').' '.$this->Purchase->get_info($purchase_id)->purchase_date,'purchase_id'=>$purchase_id));
			}
			
			if(($status==0 && !$this->input->post('undo')) || ($status==1 && $this->input->post('approve_request')))
			{
				$purchase_items = array();
				if($this->input->post('purchase_item'))
				{
					foreach($this->input->post('purchase_item') as $item_id => $quantity)
					{
						$purchase_items[] = array(
							'item_id' => $item_id,
							'quantity_request' => $quantity,
							'item_cost_price' => $this->Item->get_info($item_id)->cost_price,
							'item_unit_price' => 0
							);
					}
				}
				$this->Purchase_items->save($purchase_items, $purchase_id);
			}
			
			if($status==1)
			{
				if($this->input->post('approve_request'))
				{
					$po_number = $this->Purchase->get_info($purchase_id)->po_number;
					if(!$po_number>0)
					{
						$po_number = $this->config->item('last_po_number')+1;
						$po_number_data = array('po_number'=>$po_number);
						$this->Purchase->save($po_number_data,$purchase_id);
						$this->Appconfig->save('last_po_number',$po_number);
						/*foreach($this->Purchase_items->get_info($purchase_id) as $purchase_item)
						{
							$this->update_inventory($purchase_id,$purchase_item['item_id'],$purchase_item['quantity_request'],$status);
						}*/
					}
				}
				
				if($this->input->post('purchase_item_receive') && $this->input->post('purchase_item_discount') && $this->input->post('purchase_item_discount_type'))
				{
					$purchase_items = array();
					$received = array();
					$discount_type = $this->input->post('purchase_item_discount_type');
					foreach($this->input->post('purchase_item_receive') as $item_id => $quantity)
					{
						$received[$item_id] = $quantity;
					}
					
					$purchase_items = array();
					foreach($this->input->post('purchase_item_discount') as $item_id => $discount)
					{
						$purchase_items[] = array(
							'item_id' => $item_id,
							'quantity_request' => $this->get_item_info($purchase_id,$item_id)->quantity_request,
							'item_cost_price' => $this->get_item_info($purchase_id,$item_id)->item_cost_price,
							'item_unit_price' => $this->get_item_info($purchase_id,$item_id)->item_unit_price,
							'quantity_received' => $received[$item_id]==null?0:$received[$item_id],
							'discount_percent' => $discount,
							'discount_type' => $discount_type[$item_id]
							);
					}
					$result = $this->Purchase_items->save($purchase_items, $purchase_id);
				}

				if ($this->input->post('undo'))
				{
					$purchase_items = array();
					foreach($this->Purchase_items->get_info($purchase_id) as $item)
					{
						if($item['quantity_received']!=0)
						{
							$this->update_inventory($purchase_id,$item['item_id'],-$item['quantity_received'],$status);
						}
					}
				}
			}
			else if($status==2 && $this->input->post('finalize'))
			{
				if($this->input->post('purchase_item_receive') && $this->input->post('purchase_item_discount'))
				{
					$purchase_items = array();
					$received = array();
					$discount_type = $this->input->post('purchase_item_discount_type');
					foreach($this->input->post('purchase_item_receive') as $item_id => $quantity)
					{
						$received[$item_id] = $quantity;
						if($this->input->post('finalize') && $quantity!=0)
						{
							$this->update_inventory($purchase_id,$item_id,$quantity,$status);
						}
					}
					
					$purchase_items = array();
					foreach($this->input->post('purchase_item_discount') as $item_id => $discount)
					{
						$purchase_items[] = array(
							'item_id' => $item_id,
							'quantity_request' => $this->get_item_info($purchase_id,$item_id)->quantity_request,
							'item_cost_price' => $this->get_item_info($purchase_id,$item_id)->item_cost_price,
							'item_unit_price' => $this->get_item_info($purchase_id,$item_id)->item_unit_price,
							'quantity_received' => $received[$item_id]==null?0:$received[$item_id],
							'discount_percent' => $discount,
							'discount_type' => $discount_type[$item_id]
							);
					}
					$result = $this->Purchase_items->save($purchase_items, $purchase_id);
				}
			}
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('purchases_error_adding_updating').' '.
			$purchase_data['purchase_date'],'purchase_id'=>-1));
		}
	}
	
	function delete()
	{
		$purchases_to_delete=$this->input->post('ids');

		if($this->Purchase->delete_list($purchases_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('purchases_successful_deleted').' '.
			count($purchases_to_delete).' '.$this->lang->line('purchases_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('purchases_cannot_be_deleted')));
		}
	}
	
	function get_item_info($purchase_id,$item_id)
	{
		$this->db->from('purchase_items');
		$this->db->where('purchase_id',$purchase_id);
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
	
	function update_inventory($purchase_id,$item_id,$quantity,$status)
	{
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$cur_item_info = $this->Item->get_info($item_id);
		$po_number = $this->Purchase->get_info($purchase_id)->po_number;
		
		$trans_comment = "Purchase Error";
		if($status==2)
		{
			$trans_comment = "PURCHASE $po_number";
		}
		else if($status==1)
		{
			$trans_comment = "PURCHASE UNDO $po_number";
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
		
		//Update stock quantity
		$item_data = array('quantity'=>$cur_item_info->quantity + $quantity);
		if($this->Item->exists($item_id))
		{
			if($this->Item->save($item_data,$item_id))
			{			
				//echo json_encode(array('success'=>true,'message'=>$this->lang->line('items_successful_updating').' '.$cur_item_info->name,'item_id'=>$item_id));
				true;
			}
			else//failure
			{	
				//echo json_encode(array('success'=>false,'message'=>$this->lang->line('items_error_adding_updating').' '.$cur_item_info->name,'item_id'=>-1));
				false;
			}
		}
		else
		{
			false;
		}
	}
	
	function add_payment($purchase_id=-1,$mode)
	{
		$payment_data = array(
		'payment_amount'=>$this->input->post('amount'),
		'comments'=>$this->input->post('comments'),
		'mode'=>$mode
		);

		if($this->Purchase_payments->save($payment_data,$purchase_id))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('purchases_successful_updating').' '.$this->Purchase->get_info($purchase_id)->purchase_date.'-'.$payment_data['comments'],'purchase_id'=>$purchase_id));
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('purchases_error_adding_updating').' '.$this->Purchase->get_info($purchase_id)->purchase_date,'purchase_id'=>-1));
		}
	}
	
	function supplier_search()
	{
		$suggestions = $this->Supplier->get_suppliers_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
	
	function item_search()
	{
		$suggestions = $this->Item->get_item_search_suggestions($this->input->post('q'),$this->input->post('limit'));//waaaaa
		echo implode("\n",$suggestions);
	}
}
?>