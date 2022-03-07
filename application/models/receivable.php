<?php
class Receivable extends CI_Model
{
	/*
	Determines if a given item_id is an item kit
	*/
	function exists($receivable_id)
	{
		$this->db->from('receivables');
		$this->db->where('receivable_id',$receivable_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}
	
	function exists2($receivable_id)
	{
		$this->db->from('receivable_beginning_balance');
		$this->db->where('receivable_id',$receivable_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	/*
	Returns all the item kits
	*/
	function get_all($limit=10000, $offset=0, $show="all")
	{
		//$this->db->from('receivables');
		$this->db->select('*,receivables.receivable_id AS receivable_id');
		$this->db->from('ospos_receivables');
		$this->db->join('(SELECT receivable_id,SUM((quantity_transferred-quantity_returned)*item_unit_price*(1-(discount_percent/100))) AS balance FROM ospos_receivable_items LEFT JOIN ospos_items ON ospos_items.item_id=ospos_receivable_items.item_id GROUP BY receivable_id) AS items','items.receivable_id=ospos_receivables.receivable_id','left');
		$this->db->join('(SELECT receivable_id,SUM(payment_amount) AS payment FROM ospos_receivable_payments WHERE mode=0 GROUP BY ospos_receivable_payments.receivable_id) AS payments','payments.receivable_id=items.receivable_id','left');
		$this->db->join('people','people.person_id = receivables.person_id','left');
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
		else if($show=="nondue")
		{
			$this->db->where("status = 1");
			$this->db->where("DATEDIFF(CURDATE(),DATE(receivable_date))-terms<0");
		}
		else if($show=="due")
		{
			$this->db->where("status = 1");
			$this->db->where("DATEDIFF(CURDATE(),DATE(receivable_date))-terms>=0");
		}
		else if($show=="waiting")
		{
			$this->db->where("status = 2");
			$this->db->where("balance > IFNULL(payment,0)");
		}
		else if($show=="complete")
		{
			$this->db->where("status = 2");
			$this->db->where("balance <= IFNULL(payment,0)");
		}
		$this->db->order_by("DATEDIFF(NOW(),IF(status=1,(receivable_date + INTERVAL terms DAY),NOW()))", "desc");
		$this->db->order_by("receivable_date","asc");
		$this->db->group_by("receivables.receivable_id");
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function get_all2($limit=10000, $offset=0)
	{
		$this->db->from('receivable_beginning_balance');
		$this->db->join('people','people.person_id = receivable_beginning_balance.customer_id','left');
		$this->db->where("deleted = 0");
		$this->db->limit($limit);
		$this->db->offset($offset);
		return $this->db->get();
	}
	
	function count_all()
	{
		$this->db->from('receivables');
		return $this->db->count_all_results();
	}

	/*
	Gets information about a particular item kit
	*/
	function get_info($receivable_id)
	{
		$this->db->select('receivables.*,people.person_id,people.last_name,people.first_name,people.address_1,people.address_2,people.city,people.state,people.country,people.zip,customers.customer_discount');
		$this->db->from('receivables');
		$this->db->join('people','people.person_id = receivables.person_id');
		$this->db->join('customers','customers.person_id = receivables.person_id','left');
		$this->db->where('receivable_id',$receivable_id);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $receivable_id is NOT an item kit
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->db->list_fields('receivables');

			foreach ($fields as $field)
			{
				$item_obj->$field='';
			}

			return $item_obj;
		}
	}
	
	function get_info2($receivable_id)
	{
		$this->db->select('receivable_beginning_balance.*,people.person_id,people.last_name,people.first_name');
		$this->db->from('receivable_beginning_balance');
		$this->db->join('people','people.person_id = receivable_beginning_balance.customer_id');
		$this->db->where('receivable_id',$receivable_id);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $receivable_id is NOT an item kit
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->db->list_fields('receivable_beginning_balance');

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
	function get_multiple_info($receivable_ids)
	{
		$this->db->from('receivables');
		$this->db->where_in('receivable_id',$receivable_ids);
		$this->db->order_by("receivable_date", "asc");
		return $this->db->get();
	}

	/*
	Inserts or updates an item kit
	*/
	function save(&$receivable_data,$receivable_id=false)
	{
		if (!$receivable_id or !$this->exists($receivable_id))
		{
			if($this->db->insert('receivables',$receivable_data))
			{
				$receivable_data['receivable_id']=$this->db->insert_id();
				return true;
			}
			return false;
		}
		else
		{
			$receivable_data['receivable_id']=$receivable_id;
		}
		
		$this->db->where('receivable_id', $receivable_id);
		return $this->db->update('receivables',$receivable_data);
	}
	
	function save2(&$receivable_data,$receivable_id=false)
	{
		if (!$receivable_id or !$this->exists2($receivable_id))
		{
			if($this->db->insert('receivable_beginning_balance',$receivable_data))
			{
				$receivable_data['receivable_id']=$this->db->insert_id();
				return true;
			}
			return false;
		}

		$this->db->where('receivable_id', $receivable_id);
		return $this->db->update('receivable_beginning_balance',$receivable_data);
	}

	/*
	Deletes one item kit
	*/
	function delete($receivable_id)
	{
		//return $this->db->delete('receivables', array('receivable_id' => $id)); 	
		$this->db->where('receivable_id',$receivable_id);
		$this->db->where('status',0);
		return $this->db->update('receivables',array('status'=>-1));
	}

	/*
	Deletes a list of item kits
	*/
	function delete_list($receivable_ids)
	{
		//$this->db->where_in('receivable_id',$receivable_ids);
		//return $this->db->delete('receivables');		
		$this->db->where_in('receivable_id', $receivable_ids);
		$this->db->where('status',0);
		return $this->db->update('receivables',array('status'=>-1));
 	}

 	/*
	Get search suggestions to find kits
	*/
	function get_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->db->from('receivables');
		$this->db->join('people','people.person_id = receivables.person_id');
		$this->db->where("receivable_date LIKE '%$search%' OR CONCAT_WS(' ',first_name,last_name) LIKE '%$search%' OR CONCAT_WS(' ',last_name,first_name) LIKE '%$search%' OR itr_number=".(int)$search);
		$this->db->order_by("receivable_date", "asc");
		$by_date = $this->db->get();
		foreach($by_date->result() as $row)
		{
			$suggestions[]='DR#'.$row->itr_number.'<br />'.$row->receivable_date.'<br />'.$row->first_name.' '.$row->last_name.'|'.$row->receivable_id;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
	}
	
	function get_receivable_search_suggestions($search, $limit=25)
	{
		$suggestions = array();

		$this->db->from('receivables');
		$this->db->join('people','people.person_id = receivables.person_id');
		$this->db->where("receivable_date LIKE '%$search%' OR CONCAT_WS(' ',first_name,last_name) LIKE '%$search%' OR CONCAT_WS(' ',last_name,first_name) LIKE '%$search%'");
		$this->db->order_by("receivable_date", "asc");
		$by_date = $this->db->get();
		foreach($by_date->result() as $row)
		{
			$suggestions[]='RECEIVABLE '.$row->receivable_id.'|'.$row->receivable_date;
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
	function search($search, $show = 'all')
	{
		$this->db->from('receivables');
		$this->db->where("receivable_id",$search);
		if($this->db->count_all_results()==0)
		{
			$this->db->from('receivables');
			$this->db->join('people','people.person_id = receivables.person_id');
			$this->db->where("receivable_date LIKE '%$search%' OR CONCAT_WS(' ',first_name,last_name) LIKE '%$search%' OR CONCAT_WS(' ',last_name,first_name) LIKE '%$search%'");
			$this->db->order_by("receivable_date", "asc");
			$result = $this->db->get();
		}
		else
		{
			$this->db->from('receivables');
			if($show=="all")
			{
				$this->db->where("status >= 0");
				$this->db->order_by("status", "asc");
			}
			else if($show=="all_pending")
			{
				$this->db->where("status IN (0,1)");
			}
			$this->db->join('people','people.person_id = receivables.person_id');
			$this->db->where("receivable_id",$search);
			$this->db->order_by("receivable_date", "asc");
			$result = $this->db->get();
		}
		return $result;
	}
	
	function count_pending()
	{
		$this->db->from('receivables');
		$this->db->where('status',0);
		return $this->db->count_all_results();
	}
}
?>