<?php $this->load->view("partial/header"); ?>
<div id="page_title" style="margin-bottom:8px;"><?php echo $this->lang->line('reports_report_input'); ?></div>
<?php
if(isset($error))
{
	echo "<div class='error_message'>".$error."</div>";
}
?>
	<?php echo form_label($this->lang->line('reports_date_range'), 'report_date_range_label', array('class'=>'required')); ?>
	<div id='report_date_range_simple'>
		<input type="radio" name="report_type" id="simple_radio" value='simple' checked='checked'/>
		<?php echo form_dropdown('report_date_range_simple',$report_date_range_simple, '', 'id="report_date_range_simple"'); ?>
	</div>
	
	<div id='report_date_range_complex'>
		<input type="radio" name="report_type" id="complex_radio" value='complex' />
		<?php echo form_dropdown('start_month',$months, $selected_month, 'id="start_month"'); ?>
		<?php echo form_dropdown('start_day',$days, $selected_day, 'id="start_day"'); ?>
		<?php echo form_dropdown('start_year',$years, $selected_year, 'id="start_year"'); ?>
		-
		<?php echo form_dropdown('end_month',$months, $selected_month, 'id="end_month"'); ?>
		<?php echo form_dropdown('end_day',$days, $selected_day, 'id="end_day"'); ?>
		<?php echo form_dropdown('end_year',$years, $selected_year, 'id="end_year"'); ?>
	</div>
	
	<?php echo form_label($specific_input_name, 'specific_input_name_label', array('class'=>'required')); ?>
	
	<div id='report_specific_input_data'>
		<?php //echo form_dropdown('specific_input_data',$specific_input_data, '', 'id="specific_input_data"'); ?>
		<?php echo form_input(array('name'=>'customer','id'=>'customer','style'=>'width:300px')).'<br />';?>
		<?php echo form_input(array('name'=>'specific_input_data','id'=>'specific_input_data','style'=>'visibility:hidden;'));?>
	</div>
	
	<?php echo form_label($this->lang->line('reports_sale_type'), 'reports_sale_type_label', array('class'=>'required')); ?>
	<div id='report_sale_type'>
		<?php echo form_dropdown('sale_type',array('all' => $this->lang->line('reports_all'), 'sales' => $this->lang->line('reports_sales'), 'returns' => $this->lang->line('reports_returns')), 'all', 'id="sale_type" disabled="disabled"'); ?>
	</div>
	
	<div>
		Show Profit: <input type="radio" name="show_profit" id="show_profit_yes" value='1' checked='checked' /> Yes
		<input type="radio" name="show_profit" id="show_profit_no" value='0' /> No
	</div>
	
	<div>
		Export to Excel: <input type="radio" name="export_excel" id="export_excel_yes" value='1' /> Yes
		<input type="radio" name="export_excel" id="export_excel_no" value='0' checked='checked' /> No
	</div>

<?php
echo form_button(array(
	'name'=>'generate_report',
	'id'=>'generate_report',
	'content'=>$this->lang->line('common_submit'),
	'class'=>'submit_button')
);
?>

<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript" language="javascript">
$("#customer").autocomplete('<?php echo site_url("reports/customer_search"); ?>',
{
	minChars:0,
	delay:500,
	max:25,
	formatItem: function(row) {
		return row[1];
	}
});

$("#customer").result(function(event, data, formatted)
{
	$("#specific_input_data").val(data[0]);
	$("#customer").val(data[1]);
});

$('#customer').click(function()
{
	//$("#specific_input_data").val('');
	$(this).attr('value','');
});

$(document).ready(function()
{
	$("#generate_report").click(function()
	{
		var sale_type = $("#sale_type").val();
		var export_excel = 0;
		if ($("#export_excel_yes").attr('checked'))
		{
			export_excel = 1;
		}
		var show_profit = 0;
		if ($("#show_profit_yes").attr('checked'))
		{
			show_profit = 1;
		}
		
		if ($("#simple_radio").attr('checked'))
		{
			window.location = window.location+'/'+$("#report_date_range_simple option:selected").val()+ '/' + $('#specific_input_data').val() + '/' + sale_type + '/' + show_profit + '/' + export_excel;
		}
		else
		{
			var start_date = $("#start_year").val()+'-'+$("#start_month").val()+'-'+$('#start_day').val();
			var end_date = $("#end_year").val()+'-'+$("#end_month").val()+'-'+$('#end_day').val();
			
			window.location = window.location+'/'+start_date + '/'+ end_date + '/' + $('#specific_input_data').val() + '/' + sale_type + '/' + show_profit + '/'+ export_excel;
		}
	});
	
	$("#start_month, #start_day, #start_year, #end_month, #end_day, #end_year").click(function()
	{
		$("#complex_radio").attr('checked', 'checked');
	});
	
	$("#report_date_range_simple").click(function()
	{
		$("#simple_radio").attr('checked', 'checked');
	});
	
});
</script>