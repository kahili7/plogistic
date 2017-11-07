<div class="container">
	<div class="column span-10 prepend-7 append-7 last " style="margin-top: 80px;">	
	<?=form_open('login', array('name'=>'f_login','id'=>'f_login', 'autocomplete'=>'off'))?>
	<fieldset class="login">
	<legend><?=lang('M1_LOGIN_AREA_TITLE'); ?></legend>
	<?if(validation_errors()){?>
	<?=validation_errors('<div class="error">', '</div>');?>
	<?}?>
	<p>	
		<label for="i_login"><?=lang('M1_LOGIN_LABEL'); ?></label>
		<br/>
		<?=form_input(array('name'=>'i_login', 'class'=>'text', 'id'=>'i_login')); ?>
	</p>
	<p>
		<label for="i_password"><?=lang('M1_PASSWORD_LABEL'); ?></label>
		<br/>
		<?=form_password(array('name'=>'i_password', 'class'=>'text', 'id'=>'i_password')); ?><br>
	</p>
	<?=form_submit('submit', lang('M1_BUTTON_VALUE'))?><br>
	</fieldset>
	<?=form_close()?>
	</div>
</div>

