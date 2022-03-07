<?php
class Sample extends CI_Model
{
	function exists($id)
	{
		$this->db->from('samples');
		$this->db->where('sample_id',$id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	/*
	Returns all the item kits
	*/
	function get_all($limit=10000, $offset=0, $show="all")
	{
		$this->db->select('*,samples.sample_id AS sample_id');
		$this->db->from('samples');
		$this->db->join('sample_items','sample_items.sample_id = samples.sample_id','left');
		$this->db->join('people','people.person_id = samples.person_id','left');
		if($show=="all")
		{
			$this->db->where("status >= 0");
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
		}
		$this->db->order_by("status","asc");
		$this->db->order_by("sample_date","asc");
		$this->db->group_by("samples.sample_id");
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function count_all()
	{
		$this->db->from('samples');
		return $this->db->count_all_results();
	}

	/*
	Gets information about a particular item kit
	*/
	function get_info($sample_id)
	{
		$this->db->select('samples.*,people.person_id,people.last_name,people.first_name,people.address_1,people.address_2,people.city,people.state,people.country,people.zip,customers.customer_discount');
		$this->db->from('samples');
		$this->db->join('people','people.person_id = samples.person_id');
		$this->db->join('customers','customers.person_id = samples.person_id','left');
		$this->db->where('sample_id',$sample_id);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $sample_id is NOT an item kit
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->db->list_fields('samples');

			foreach ($fields as $field)
			{
				$item_obj->$field='';
			}

			return $item_obj;
		}
	}

	/*
	Gets information about multiple item kits
	*/
	function get_multiple_info($sample_ids)
	{
		$this->db->from('samples');
		$this->db->where_in('sample_id',$sample_ids);
		$this->db->order_by("sample_date", "asc");
		return $this->db->get();
	}

	/*
	Inserts or updates an item kit
	*/
	function save(&$sample_data,$sample_id=false)
	{
		if (!$sample_id or !$this->exists($sample_id))
		{
			if($this->db->insert('samples',$sample_data))
			{
				$sample_data['sample_id']=$this->db->insert_id();
				return true;
			}
			return false;
		}
		else
		{
			$sample_data['sample_id']=$sample_id;
		}
		
		$this->db->where('sample_id', $sample_id);
		return $this->db->update('samples',$sample_data);
	}

	/*
	Deletes one item kit
	*/
	function delete($sample_id)
	{	
		/*$this->db->where('sample_id', $sample_id);
		return $this->db->update('samples',array('status'=>-1));*/
		return false;
	}

	/*
	Deletes a list of item kits
	*/
	function delete_list($sample_ids)
	{
		/*$this->db->where_in('sample_id', $sample_ids);
		return $this->db->update('samples',array('status'=>-1));*/
		return false;
 	}

 	/*
	Get search suggestions to find kits
	*/
	function get_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->db->from('samples');
		$this->db->join('people','people.person_id = samples.person_id');
		$this->db->where("sample_date LIKE '%$search%' OR CONCAT_WS(' ',first_name,last_name) LIKE '%$search%' OR CONCAT_WS(' ',last_name,first_name) LIKE '%$search%' OR sample_number=".(int)$search);
		$this->db->order_by("sample_date", "asc");
		$by_date = $this->db->get();
		foreach($by_date->result() as $row)
		{
			$suggestions[]='DRS#'.$row->sample_number.'<br />'.$row->sample_date.'<br />'.$row->first_name.' '.$row->last_name.'|'.$row->sample_id;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
		return array();
	}
	
	function get_sample_search_suggestions($search, $limit=25)
	{
		$suggestions = array();

		$this->db->from('samples');
		$this->db->join('people','people.person_id = samples.person_id');
		$this->db->where("sample_date LIKE '%$search%' OR CONCAT_WS(' ',first_name,last_name) LIKE '%$search%' OR CONCAT_WS(' ',last_name,first_name) LIKE '%$search%'");
		$this->db->order_by("sample_date", "asc");
		$by_date = $this->db->get();
		foreach($by_date->result() as $row)
		{
			$suggestions[]='DRS '.$row->sample_number.'|'.$row->sample_date;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
		
	}

	/*
	Preform a search on items
	*/
	function search($search)
	{
		$this->db->from('samples');
		$this->db->where("sample_id",$search);
		if($this->db->count_all_results()==0)
		{
			$this->db->from('samples');
			$this->db->join('people','people.person_id = samples.person_id');
			$this->db->where("sample_date LIKE '%$search%' OR CONCAT_WS(' ',first_name,last_name) LIKE '%$search%' OR CONCAT_WS(' ',last_name,first_name) LIKE '%$search%'");
			$this->db->order_by("sample_date", "asc");
			$result = $this->db->get();
		}
		else
		{
			$this->db->from('samples');
			$this->db->join('people','people.person_id = samples.person_id');
			$this->db->where("sample_id",$search);
			$this->db->order_by("sample_date", "asc");
			$result = $this->db->get();
		}
		return $result;
	}
	
	function count_pending()
	{
		$this->db->from('samples');
		$this->db->where('status',0);
		return $this->db->count_all_results();
	}
}
?>