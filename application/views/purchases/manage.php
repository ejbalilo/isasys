<?php $this->load->view("partial/header"); ?>
<script type="text/javascript">
$(document).ready(function()
{
    init_table_sorting();
    enable_select_all();
    enable_checkboxes();
    enable_row_selection();
    enable_search2('<?php echo site_url("$controller_name/suggest")?>','<?php echo $this->lang->line("common_confirm_search")?>');
    enable_delete('<?php echo $this->lang->line($controller_name."_confirm_delete")?>','<?php echo $this->lang->line($controller_name."_none_selected")?>');
});

function enable_search2(suggest_url,confirm_search_message)
{
	//Keep track of enable_email has been called
	if(!enable_search.enabled)
		enable_search.enabled=true;

	$('#search').click(function()
    {
    	$(this).attr('value','');
    });

    $("#search").autocomplete(suggest_url,{max:100,delay:10, selectFirst: false});
    $("#search").result(function(event, data, formatted)
    {
		$("#search").val(data[1]);
		do_search(true);
    });
    
	$('#search_form').submit(function(event)
	{
		event.preventDefault();

		if(get_selected_values().length >0)
		{
			if(!confirm(confirm_search_message))
				return;
		}
		do_search(true);
	});
}

function init_table_sorting()
{
	//Only init if there is more than one row
	if($('.tablesorter tbody tr').length >1)
	{
		$("#sortable_table").tablesorter(
		{
			//sortList: [[1,1]],
			headers:
			{
				0: { sorter: false},
				1: { sorter: 'digit'},
				6: { sorter: false}
			}
		});
	}
}

function post_purchase_form_submit(response)
{
	if(!response.success)
	{
		set_feedback(response.message,'error_message',true);
	}
	else
	{
		//This is an update, just update one row
		if(jQuery.inArray(response.purchase_id,get_visible_checkbox_ids()) != -1)
		{
			update_row(response.purchase_id,'<?php echo site_url("$controller_name/get_row")?>');
			set_feedback(response.message,'success_message',false);
		}
		else //refresh entire table
		{
			$('#search').val(response.purchase_id);
			do_search(true,function()
			{
				//highlight new row
				hightlight_row(response.purchase_id);
				set_feedback(response.message,'success_message',false);
			});
		}
	}
}

function post_purchase_form_submit2(response)
{
	if(!response.success)
	{
		set_feedback(response.message,'error_message',true);
	}
	else
	{
		//This is an update, just update one row
		if(jQuery.inArray(response.purchase_id,get_visible_checkbox_ids()) != -1)
		{
			update_row(response.purchase_id,'<?php echo site_url("$controller_name/get_row2")?>');
			set_feedback(response.message,'success_message',false);
		}
		else //refresh entire table
		{
			do_search(true,function()
			{
				//highlight new row
				hightlight_row(response.purchase_id);
				set_feedback(response.message,'success_message',false);
			});
		}
	}
}
</script>

<div id="title_bar">
	<div id="title" class="float_left"><?php echo $this->lang->line('common_list_of').' '.$this->lang->line('module_'.$controller_name); ?></div>
	<div id="new_button">
		<?php
		if($this->Employee->has_permission('purchases_request',$this->Employee->get_logged_in_employee_info()->person_id))
			echo anchor("$controller_name/view/-1/width:".($form_width*1.5),"<div class='big_button' style='float: left;'><span>".$this->lang->line($controller_name.'_new')."</span></div>",array('class'=>'thickbox none','title'=>$this->lang->line($controller_name.'_new')));
		?>
	</div>
</div>

<?php echo form_open("purchases",array('id'=>'mode_form')); ?>
&nbsp;Show: <?php echo form_dropdown('show',$shows,$show,'onchange="$(\'#mode_form\').submit();"'); ?>
</form>

<?php echo $this->pagination->create_links();?>
<div id="table_action_header">
	<ul>
		<li class="float_left">
		<span><?php echo anchor("$controller_name/delete2",$this->lang->line("common_delete"),array('id'=>'delete')); ?></span>
		</li>
		<li class="float_right">
		<img src='<?php echo base_url()?>images/spinner_small.gif' alt='spinner' id='spinner' />
		<?php if($mode!="beginningbalance") echo form_open("$controller_name/search",array('id'=>'search_form'))."<input type='text' name ='search' id='search'/></form>"; ?>
		</li>
	</ul>
</div>

<div id="table_holder">
<?php echo $manage_table; ?>
</div>
<div id="feedback_bar"></div>
<?php $this->load->view("partial/footer"); ?>