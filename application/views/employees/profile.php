<?php
echo form_open('home/save_profile/',array('id'=>'edit_profile_form'));
?>
<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
<ul id="error_message_box"></ul>

<fieldset id="employee_edit_profile">
<legend><?php echo $this->lang->line("employees_login_info"); ?></legend>

<div class="field_row clearfix">	
<?php echo form_label("Old ".$this->lang->line('employees_password').':', 'old_password',array('class'=>'required')); ?>
	<div class='form_field'>
	<?php echo form_password(array(
		'name'=>'old_password',
		'id'=>'old_password'
	));?>
	</div>
</div>

<div class="field_row clearfix">	
<?php echo form_label("New ".$this->lang->line('employees_password').':', 'new_password',array('class'=>'required')); ?>
	<div class='form_field'>
	<?php echo form_password(array(
		'name'=>'new_password',
		'id'=>'new_password'
	));?>
	</div>
</div>

<div class="field_row clearfix">	
<?php echo form_label($this->lang->line('employees_repeat_password').':', 'new_repeat_password',array('class'=>'required')); ?>
	<div class='form_field'>
	<?php echo form_password(array(
		'name'=>'new_repeat_password',
		'id'=>'new_repeat_password'
	));?>
	</div>
</div>
<?php
echo form_submit(array(
	'name'=>'submit',
	'id'=>'submit',
	'value'=>$this->lang->line('common_submit'),
	'class'=>'submit_button float_right')
);
?>
</fieldset>

<?php
echo form_close();
?>
<script type='text/javascript'>

//validation and submit handling
$(document).ready(function()
{
	$('#edit_profile_form').validate({
		submitHandler:function(form)
		{
			$('#submit').val('Please Wait...');
			$('#submit').attr('disabled',true);
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				if(response.success)
				{
					set_feedback(response.message,'success_message',false);		
				}
				else
				{
					set_feedback(response.message,'error_message',true);		
				}
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules: 
		{
			old_password:
			{
				required:true
			},
			new_password:
			{
				required:true,
				minlength: 8
			},
			new_repeat_password:
			{
 				equalTo: "#new_password"
			}
   		},
		messages: 
		{
			old_password:
			{
				required:"<?php echo $this->lang->line('employees_password_required'); ?>",
				minlength: "<?php echo $this->lang->line('employees_password_minlength'); ?>"
			},
			new_password:
			{
				required:"<?php echo $this->lang->line('employees_password_required'); ?>",
				minlength: "<?php echo $this->lang->line('employees_password_minlength'); ?>"
			},
			new_repeat_password:
			{
				equalTo: "<?php echo $this->lang->line('employees_password_must_match'); ?>"
     		}
		}
	});
});
</script>