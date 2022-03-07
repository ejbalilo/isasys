<?php
class Purchase extends CI_Model
{
	function exists($purchase_id)
	{
		$this->db->from('purchases');
		$this->db->where('purchase_id',$purchase_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}
	
	function get_all($limit=10000, $offset=0, $show="all")
	{
		$this->db->select('*,status AS balance');
		$this->db->from('purchases');
		$this->db->join('people','people.person_id = purchases.person_id','left');
		$this->db->join('suppliers','suppliers.person_id = purchases.person_id','left');
		if($show=="all")
		{
			$this->db->where("status > -1");
			$this->db->order_by("status", "asc");
		}
		else if($show=="all_pending")
		{
			$this->db->where("status IN (0,1)");
		}
		else if($show=="pending")
		{
			$this->db->where("status = 0");
		}
		else if($show=="approved")
		{
			$this->db->where("status = 1");
		}
		else if($show=="complete")
		{
			$this->db->where("status = 2");
			//$this->db->where("balance <= IFNULL(payment,0)");
		}
		$this->db->order_by('status');
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function count_all()
	{
		$this->db->from('purchases');
		return $this->db->count_all_results();
	}
	
	function get_info($purchase_id)
	{
		$this->db->select('*,purchases.comments AS purchase_comments');
		$this->db->from('purchases');
		$this->db->join('people','people.person_id = purchases.person_id','left');
		$this->db->join('suppliers','suppliers.person_id = purchases.person_id','left');
		$this->db->where('purchase_id',$purchase_id);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $purchase_id is NOT an item kit
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->db->list_fields('purchases');

			foreach ($fields as $field)
			{
				$item_obj->$field='';
			}

			return $item_obj;
		}
	}
	
	function get_multiple_info($purchase_ids)
	{
		$this->db->from('purchases');
		$this->db->where_in('purchase_id',$purchase_ids);
		$this->db->order_by("purchase_date", "asc");
		return $this->db->get();
	}

	function save(&$purchase_data,$purchase_id=false)
	{
		if (!$purchase_id or !$this->exists($purchase_id))
		{
			if($this->db->insert('purchases',$purchase_data))
			{
				$purchase_data['purchase_id']=$this->db->insert_id();
				return true;
			}
			return false;
		}

		$this->db->where('purchase_id', $purchase_id);
		return $this->db->update('purchases',$purchase_data);
	}

	function delete($purchase_id)
	{
		//return $this->db->delete('purchases', array('purchase_id' => $id)); 	
		$this->db->where('purchase_id', $purchase_id);
		return $this->db->update('purchases',array('status'=>-1));
	}

	function delete_list($purchase_ids)
	{
		//$this->db->where_in('purchase_id',$purchase_ids);
		//return $this->db->delete('purchases');		
		$this->db->where_in('purchase_id', $purchase_ids);
		return $this->db->update('purchases',array('status'=>-1));
 	}

	function get_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->db->from('purchases');
		$this->db->join('people','people.person_id = purchases.person_id');
		$this->db->where("purchase_date LIKE '%$search%' OR CONCAT_WS(' ',first_name,last_name) LIKE '%$search%' OR CONCAT_WS(' ',last_name,first_name) LIKE '%$search%' OR po_number=".(int)$search);
		$this->db->order_by("purchase_date", "asc");
		$by_date = $this->db->get();
		foreach($by_date->result() as $row)
		{
			$suggestions[]='PO#'.$row->po_number.'<br />'.$row->purchase_date.'<br />'.$row->first_name.' '.$row->last_name.'|'.$row->purchase_id;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
	}
	
	function get_purchase_search_suggestions($search, $limit=25)
	{
		$suggestions = array();

		$this->db->from('purchases');
		$this->db->join('people','people.person_id = purchases.person_id');
		$this->db->where("purchase_date LIKE '%$search%' OR CONCAT_WS(' ',first_name,last_name) LIKE '%$search%' OR CONCAT_WS(' ',last_name,first_name) LIKE '%$search%'");
		$this->db->order_by("purchase_date", "asc");
		$by_date = $this->db->get();
		foreach($by_date->result() as $row)
		{
			$suggestions[]='PURCHASE '.$row->purchase_id.'|'.$row->purchase_date;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
		
	}

	function search($search)
	{
		$this->db->from('purchases');
		$this->db->where("purchase_id",$search);
		if($this->db->count_all_results()==0)
		{
			$this->db->from('purchases');
			$this->db->join('people','people.person_id = purchases.person_id');
			$this->db->where("purchase_date LIKE '%$search%' OR CONCAT_WS(' ',first_name,last_name) LIKE '%$search%' OR CONCAT_WS(' ',last_name,first_name) LIKE '%$search%'");
			$this->db->order_by("purchase_date", "asc");
			$result = $this->db->get();
		}
		else
		{
			$this->db->from('purchases');
			$this->db->join('people','people.person_id = purchases.person_id');
			$this->db->where("purchase_id",$search);
			$this->db->order_by("purchase_date", "asc");
			$result = $this->db->get();
		}
		return $result;
	}
	
	function count_pending()
	{
		$this->db->from('purchases');
		$this->db->where('status',0);
		return $this->db->count_all_results();
	}
}
?>