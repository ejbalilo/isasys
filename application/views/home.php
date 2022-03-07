<?php $this->load->view("partial/header"); ?>
<br />
<h3><?php echo $this->lang->line('common_welcome_message'); ?></h3>
<div id="home_module_list">
	<table><tr><?php
	$x = 0;
	foreach($allowed_modules->result() as $module)
	{
		if($module->sort%10==0)
		{
			if($x==4)
			{
				$x = 0;
				echo "</tr><tr>";
			}
	?>
	<td align="center" valign="top"><div class="module_item">
		<a href="<?php echo site_url("$module->module_id");?>">
		<img src="<?php echo base_url().'images/menubar/'.$module->module_id.'.png';?>" border="0" alt="Menubar Image" /></a><br />
		<a href="<?php echo site_url("$module->module_id");?>"><b><?php echo $this->lang->line("module_".$module->module_id) ?></b></a>
		<br /><i><sup><?php echo $this->lang->line('module_'.$module->module_id.'_desc');?></sup></i>
	</div></td>
	<?php
		$x++;
		}
	}
	while($x%4!=0)
	{
		echo "</td><td>";
		$x++;
	}
	?></tr></table>
</div>
<?php $this->load->view("partial/footer"); ?>