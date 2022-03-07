<?php 
//OJB: Check if for excel export process
if($export_excel == 1){
	ob_start();
	$this->load->view("partial/header_excel");
}else{
	$this->load->view("partial/header");
} 
?>
<div id="page_title" style="margin-bottom:8px;"><?php echo $title ?></div>
<div id="page_subtitle" style="margin-bottom:8px;"><?php echo $subtitle ?></div>
<?php if(isset($summary_data['form_open'])) echo $summary_data['form_open']; ?>
<div id="table_holder">
	<table class="tablesorter report" id="sortable_table">
		<thead>
			<tr>
				<?php foreach ($headers as $header) { ?>
				<th><?php echo $header; ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($data as $row) { ?>
			<tr>
				<?php foreach ($row as $cell) { ?>
				<td><?php echo $cell; ?></td>
				<?php } ?>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<div id="report_summary">
<center><table border="0">
<?php foreach($summary_data as $name=>$value) { ?>
	<tr><td><b><div class="summary_row"><?php echo $this->lang->line('reports_'.$name). ': &nbsp; </div></b></td><td>'.to_currency($value); ?></td></tr>
<?php }?>
</table></center>
</div><br />
<?php if(isset($summary_data['form_submit'])&&$this->Employee->has_permission('purchases_request',$this->Employee->get_logged_in_employee_info()->person_id)) echo '<center>'.$summary_data['form_submit'].'</center>'; ?>
<?php if(isset($summary_data['form_open'])) echo form_close(); ?>
<div id="feedback_bar"></div>
<?php 
if($export_excel == 1){
	$this->load->view("partial/footer_excel");
	$content = ob_end_flush();
	
	$filename = trim($filename);
	$filename = str_replace(array(' ', '/', '\\'), '', $title);
	$filename .= "_Export.xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);
	echo $content;
	die();
	
}else{
	$this->load->view("partial/footer"); 
?>

<script type="text/javascript" language="javascript">
function init_table_sorting()
{
	//Only init if there is more than one row
	if($('.tablesorter tbody tr').length >1)
	{
		$("#sortable_table").tablesorter(); 
	}
}
$(document).ready(function()
{
	init_table_sorting();
	$("#generate_po").click(function()
    {
		var items = new Array();
		$("#sortable_table tbody :checkbox:checked").each(function()
		{
			items.push($(this).val());
		});
		
		if(items.length>0)
		{
			if (confirm('Are you sure you want to generate Purchase Order request for ['+items.length+'] item'+(items.length>1?'s':'')+'?'))
			{
				$.post('index.php/reports/low_inventory_request_po', { 'items[]': items },function(response)
				{
					if(response.success)
					{	
						set_feedback(response.message,'success_message',false);	
					}
					else
					{
						set_feedback(response.message,'error_message',true);	
					}
				},"json");
			}
		}
		else
		{
			alert("Invalid number of items selected to generate a Purchase Order.");
		}
    });
});
</script>
<?php 
} // end if not is excel export 
?>