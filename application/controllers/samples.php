<?php
require_once ("secure_area.php");
require_once ("interfaces/idata_controller.php");
class Samples extends Secure_area implements iData_controller
{
	function __construct()
	{
		parent::__construct('samples');
	}

	function index()
	{
		$config['base_url'] = site_url('?c=samples&m=index');
		$config['total_rows'] = $this->Sample->count_all();
		$config['per_page'] = '500';
		$this->pagination->initialize($config);
		
		if($this->input->post('show')=="")
			$data['show']="all_pending";
		else
			$data['show']=$this->input->post('show');
		$data['shows']=array('all'=>'All','all_pending'=>'All Pending','pending'=>'Pending Request','approved'=>'Approved Request','complete'=>'Transaction Complete');
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['manage_table']=get_samples_manage_table($this->Sample->get_all($config['per_page'],$this->input->get('per_page'),$data['show']),$this);
		$this->load->view('samples/manage',$data);
	}
	
	function dr($sample_id,$type='')
	{
		$data['type']=$type;
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['sample_id']=$sample_id;
		$data['sample_number']=$this->Sample->get_info($sample_id)->sample_number;
		$data['sample_date']=date("M d, Y",strtotime($this->Sample->get_info($sample_id)->sample_date));
		$data['customer_name']=$this->Sample->get_info($sample_id)->first_name.' '.$this->Sample->get_info($sample_id)->last_name;
		$data['customer_address1']=$this->Sample->get_info($sample_id)->address_1.' '.$this->Sample->get_info($sample_id)->address_2.' '.$this->Sample->get_info($sample_id)->city;
		$data['customer_address2']=$this->Sample->get_info($sample_id)->state.' '.$this->Sample->get_info($sample_id)->country.' '.$this->Sample->get_info($sample_id)->zip;
		$data['comments']=$this->Receivable->get_info($sample_id)->comments;
		$this->load->view('samples/dr',$data);
	}

	function search()
	{
		$search=$this->input->post('search');
		$data_rows=get_samples_manage_table_data_rows($this->Sample->search($search),$this,'');
		echo $data_rows;
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		$suggestions = $this->Sample->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function get_row()
	{
		$sample_id = $this->input->post('row_id');
		$data_row=get_sample_data_row($this->Sample->get_info($sample_id),$this);
		echo $data_row;
	}

	function view($sample_id=-1)
	{
		$data['controller_name']=strtolower(get_class());
		$data['sample_info']=$this->Sample->get_info($sample_id);
		$data['items']=$this->Sample_items->get_list($sample_id);
		$this->load->view("samples/form",$data);
	}
	
	function save($sample_id=-1)
	{
		if($sample_id==-1 || $this->Employee->has_permission('samples_edit',$this->Employee->get_logged_in_employee_info()->person_id) || $this->Sample->get_info($sample_id)->status==0)
		{
			$sample_data = array();
			if($sample_id==-1 || $sample_id!=-1?$this->Sample->get_info($sample_id)->status==0:false)
			{
				$sample_data = array(
				'sample_date'=>$this->input->post('timestamp'),
				'person_id'=>$this->input->post('customer_id'),
				'employee_id'=>$this->Employee->get_logged_in_employee_info()->person_id
				);
				if($this->input->post('approve_request'))
				{
					$sample_data = array_merge($sample_data,array('status'=>1));
				}
			}
			$sample_data = array_merge($sample_data,array('comments'=>$this->input->post('comments')));

			if($this->Sample->save($sample_data,$sample_id))
			{
				$sample_id = $sample_data['sample_id'];
				if($sample_id==-1)
				{
					$status = 0;
					if($this->input->post('approve_request'))
					{
						$status = 1;
					}
					echo json_encode(array('success'=>true,'message'=>$this->lang->line('samples_successful_adding').' '.$sample_data['sample_date'],'sample_id'=>$sample_data['sample_id']));
				}
				else
				{
					$status = $this->Sample->get_info($sample_id)->status;
					$sample_date = $this->Sample->get_info($sample_id)->sample_date;
					
					if($this->input->post('approve_request') && $status==0)
					{
						$status = 1;
						$sample_date = date('Y-m-d H:i:s');
					}
					else if($this->input->post('finalize') && $status==1)
					{
						$status = 2;
					}
					
					
					$this->db->where('sample_id', $sample_id);
					$this->db->update('samples',array('status'=>$status,'sample_date'=>$sample_date));
					
					echo json_encode(array('success'=>true,'message'=>$this->lang->line('samples_successful_updating').' '.$this->Sample->get_info($sample_id)->sample_date,'sample_id'=>$sample_id));
				}
				
				if($status==0 || ($status==1 && $this->input->post('approve_request')))
				{
					$sample_items = array();
					if($this->input->post('sample_item'))
					{
						$k = 0;
						foreach($this->input->post('sample_item') as $item_id => $quantity)
						{
							$sample_items[] = array(
								'item_id' => $item_id,
								'quantity_transferred' => $quantity,
								'item_cost_price' => $this->Item->get_info($item_id)->cost_price
								);
							$k++;
						}
					}
					$this->Sample_items->save($sample_items,$sample_id);
				}
				
				if($status==1 || ($status==2 && $this->input->post('finalize')))
				{
					
					if($this->input->post('approve_request'))
					{
						$sample_number = $this->config->item('last_sample_number')+1;
						$sample_number_data = array('sample_number'=>$sample_number);
						$this->Sample->save($sample_number_data,$sample_id);
						$this->Appconfig->save('last_sample_number',$sample_number);
						foreach($this->Sample_items->get_info($sample_id) as $sample_item)
						{
							$this->update_inventory($sample_id,$sample_item['item_id'],$sample_item['quantity_transferred'],$status);
						}
					}
					if($this->input->post('sample_item_return'))
					{
						$sample_items = array();
						foreach($this->input->post('sample_item_return') as $item_id=>$quantity)
						{
							$sample_items[] = array(
								'item_id' => $item_id,
								'quantity_transferred' => $this->get_item_info($sample_id,$item_id)->quantity_transferred,
								'item_cost_price' => $this->get_item_info($sample_id,$item_id)->item_cost_price,
								'quantity_returned' => (int)$quantity
								);
						}
						$this->Sample_items->save($sample_items, $sample_id);
						if($this->input->post('finalize'))
							foreach($this->Sample_items->get_info($sample_id) as $sample_item)
								$this->update_inventory($sample_id,$sample_item['item_id'],$sample_item['quantity_returned'],$status);
					}
				}
			}
			else//failure
			{
				echo json_encode(array('success'=>false,'message'=>$this->lang->line('samples_error_adding_updating').' '.
				$sample_data['sample_date'],'sample_id'=>-1));
			}
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('samples_error_adding_updating').' '.
			$sample_data['sample_date'],'sample_id'=>-1));
		}
	}
	
	function delete()
	{
		$samples_to_delete=$this->input->post('ids');
		if($this->Employee->has_permission('samples_edit',$this->Employee->get_logged_in_employee_info()->person_id))
		{
			if($this->Sample->delete_list($samples_to_delete))
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('samples_successful_deleted').' '.
				count($samples_to_delete).' '.$this->lang->line('samples_one_or_multiple')));
			}
			else
			{
				echo json_encode(array('success'=>false,'message'=>$this->lang->line('samples_cannot_be_deleted')));
			}
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('samples_cannot_be_deleted')));
		}
		
	}
	
	function get_item_info($sample_id,$item_id)
	{
		$this->db->from('sample_items');
		$this->db->where('sample_id',$sample_id);
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
	
	function update_inventory($sample_id,$item_id,$quantity,$status)
	{
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$sample_number = $this->Sample->get_info($sample_id)->sample_number;
		$trans_comment = "Sample Error";
		
		if($status==1)
		{
			$trans_comment = "SAMPLE $sample_number";
			$quantity *= -1;
		}
		else if($status==2)
		{
			$trans_comment = "SAMPLE $sample_number";
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
	
	function add_usage($sample_id=-1)
	{
		$sample_usages_data = array(
			'item_id'=>$this->input->post('item_id'),
			'usage_amount'=>$this->input->post('amount'),
			'comments'=>$this->input->post('comments')
			);

		if($this->Sample_usages->save($sample_usages_data,$sample_id))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('samples_successful_updating').' '.$this->Receivable->get_info($sample_id)->sample_date,'sample_id'=>$sample_id));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('samples_error_adding_updating').' '.$this->Receivable->get_info($sample_id)->sample_date,'sample_id'=>-1));
		}
	}
	
	function customer_search()
	{
		$suggestions = $this->Customer->get_customer_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
	
	function item_search()
	{
		$suggestions = $this->Item->get_item_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
}
?>