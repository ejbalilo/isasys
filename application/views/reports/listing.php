
<?php $this->load->view("partial/header"); ?>
<div id="page_title" style="margin-bottom:8px;"><?php echo $this->lang->line('reports_reports'); ?></div>
<div id="welcome_message"><?php echo $this->lang->line('reports_welcome_message'); ?>
<table border="0" width="100%"><tr>
<td valign="top">
	<ul id="report_list"><?php if($this->Employee->has_permission('reports_sales',$this->Employee->get_logged_in_employee_info()->person_id)) { ?>
		<li><h3><?php echo $this->lang->line('reports_graphical_reports'); ?></h3>
			<ul>
				<li><a href="<?php echo site_url('reports/graphical_summary_sales');?>"><?php echo $this->lang->line('reports_sales'); ?></a></li>
				<li><a href="<?php echo site_url('reports/graphical_summary_categories');?>"><?php echo $this->lang->line('reports_categories'); ?></a></li>
				<li><a href="<?php echo site_url('reports/graphical_summary_customers');?>"><?php echo $this->lang->line('reports_customers'); ?></a></li>
				<li><a href="<?php echo site_url('reports/graphical_summary_suppliers');?>"><?php echo $this->lang->line('reports_suppliers'); ?></a></li>
				<li style='text-decoration:line-through'><a href="<?php echo '';//site_url('reports/graphical_summary_items');?>"><?php echo $this->lang->line('reports_items'); ?></a></li>
				<li style='text-decoration:line-through'><a href="<?php echo '';//site_url('reports/graphical_summary_employees');?>"><?php echo $this->lang->line('reports_employees'); ?></a></li>
				<li style='text-decoration:line-through'><a href="<?php echo '';//site_url('reports/graphical_summary_taxes');?>"><?php echo $this->lang->line('reports_taxes'); ?></a></li>
				<li style='text-decoration:line-through'><a href="<?php echo '';//site_url('reports/graphical_summary_discounts');?>"><?php echo $this->lang->line('reports_discounts'); ?></a></li>
				<li style='text-decoration:line-through'><a href="<?php echo '';//site_url('reports/graphical_summary_payments');?>"><?php echo $this->lang->line('reports_payments'); ?></a></li>
			</ul>
		</li>
		<br />
		<li><h3>People Reports</h3>
			<ul>
				<li><a href="<?php echo site_url('reports/detailed_customers');?>">Customers</a></li>
				<li style='text-decoration:line-through'><a href="">Employees</a></li>
			</ul>
		</li>
	</ul>
</td><td valign="top">
	<ul id="report_list">
		<li><h3><?php echo $this->lang->line('reports_summary_reports'); ?></h3>
			<ul>
				<li><a href="<?php echo site_url('reports/summary_sales');?>"><?php echo $this->lang->line('reports_sales'); ?></a></li>
				<li><a href="<?php echo site_url('reports/summary_categories');?>"><?php echo $this->lang->line('reports_categories'); ?></a></li>
				<li><a href="<?php echo site_url('reports/summary_customers');?>"><?php echo $this->lang->line('reports_customers'); ?></a></li>
				<li><a href="<?php echo site_url('reports/summary_suppliers');?>"><?php echo $this->lang->line('reports_suppliers'); ?></a></li>
				<li><a href="<?php echo site_url('reports/summary_items_sold');?>"><?php echo $this->lang->line('reports_items'); ?> Sold</a></li>
				<li><a href="<?php echo site_url('reports/summary_items_purchased');?>"><?php echo $this->lang->line('reports_items'); ?> Purchased</a></li>
				<li style='text-decoration:line-through'><a href="<?php echo '';//site_url('reports/summary_employees');?>"><?php echo $this->lang->line('reports_employees'); ?></a></li>
				<li style='text-decoration:line-through'><a href="<?php echo '';//site_url('reports/summary_taxes');?>"><?php echo $this->lang->line('reports_taxes'); ?></a></li>
				<li style='text-decoration:line-through'><a href="<?php echo '';//site_url('reports/summary_discounts');?>"><?php echo $this->lang->line('reports_discounts'); ?></a></li>
				<li style='text-decoration:line-through'><a href="<?php echo '';//site_url('reports/summary_payments');?>"><?php echo $this->lang->line('reports_payments'); ?></a></li>
			</ul>
		</li><?php } ?>
	</ul>
</td><td valign="top">
	<ul id="report_list">
		<?php if($this->Employee->has_permission('reports_sales',$this->Employee->get_logged_in_employee_info()->person_id)) { ?>
		<li><h3><?php echo $this->lang->line('reports_detailed_reports'); ?></h3>
			<ul>
				<li><a href="<?php echo site_url('reports/detailed_sales');?>"><?php echo $this->lang->line('reports_sales'); ?></a></li>
				<li><a href="<?php echo site_url('reports/detailed_receivings');?>"><?php echo $this->lang->line('reports_receivings'); ?></a></li>
				<li><a href="<?php echo site_url('reports/specific_customer');?>"><?php echo $this->lang->line('reports_customer'); ?></a></li>
				<li><a href="<?php echo site_url('reports/specific_employee');?>"><?php echo $this->lang->line('reports_employee'); ?></a></li>
				<li><a href="<?php echo site_url('reports/specific_account_officer');?>">Account Officer</a></li>
				<li><a href="<?php echo site_url('reports/specific_sales_agent');?>">Sales Agent</a></li>
			</ul>
		</li>
		<br /><?php } if($this->Employee->has_permission('reports_inventory',$this->Employee->get_logged_in_employee_info()->person_id)) { ?>
		<li><h3><?php echo $this->lang->line('reports_inventory_reports'); ?></h3>
			<ul>
				<li><a href="<?php echo site_url('reports/inventory_low');?>"><?php echo $this->lang->line('reports_low_inventory'); ?></a><?php $count_notification = $this->Item->count_low_inventory(); if($count_notification>0)
				{
				?>&nbsp;<sup><span style="border-width:0 1px 1px 0;border-color:#000000;border-style:solid;color:#ffffff;background-color:#ff0000;font-size:10px">&nbsp;<b><?php echo $count_notification; ?></b>&nbsp;</span></sup><?php
				} ?></li>
				<li><a href="<?php echo site_url('reports/inventory_summary');?>"><?php echo $this->lang->line('reports_inventory_summary'); ?></a></li>
				<li><a href="<?php echo site_url('reports/deleted_items');?>">Deleted Items</a></li>
			</ul>
		</li><?php } ?>
	</ul>
</td></tr></table>
<?php
if(isset($error))
{
	echo "<div class='error_message'>".$error."</div>";
}
?>

</div><br />

<?php $this->load->view("partial/footer"); ?>