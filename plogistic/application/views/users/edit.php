<div class="grid">
<h3><?=$title?></h3>
<?= form_open(get_currcontroller()."/edit/{$rid}", array('id'=>'edit_'.$orid, 'autocomplete'=>'off'))?>
<div class="container editform">
<?=form_hidden('rid', $rid)?>
<?if(validation_errors()){?>
<div class="error">
	<?=validation_errors('<div>', '</div>');?>
</div>	
<?}?>
<?if($success===False){?>
<div class="error">
	<?=lang('SAVE_SYSTEM_ERROR')?>
</div>
<?}?>
<?if($success===True){?>
<div class="success">
	<?=lang('SAVE_SYSTEM_SUCCESS')?>
</div>
<?}?>

<div class="column span-3">	<?=form_label(lang('EMPLOYEER'), 'emp_name')?> <font color="red">*</font></div>
<div class="column span-21 last">
	<?=get_employeers_vp(set_value('_employeers_rid', $ds->_employeers_rid))?>
</div>


<div class="column span-3" style="white-space: nowrap;">
	<?=form_label(lang('USER_LOGIN'), 'user_login')?> <font color="red">*</font>
</div>
<div class="column span-9">
	<?=form_input('user_login', set_value('user_login', $ds->user_login), 'id="user_login" class="text"')?>
</div>
<div class="column span-3" style="white-space: nowrap;">
	<?=form_label(lang('USER_PASSWORD'), 'user_passwd')?> <font color="red">*</font>
</div>
<div class="column span-9 last">
	<?=form_password('user_passwd', set_value('user_passwd', $ds->user_passwd), 'id="user_passwd" class="text"')?>
</div>

<div class="column span-3" style="white-space: nowrap;">
	<?=form_label(lang('END_PASSWD_DATE'), 'edate_passwd')?> <font color="red">*</font>
</div>
<div class="column span-9">
	<?=form_input('edate_passwd', set_value('edate_passwd', $ds->edate_passwd), 'id="edate_passwd" class="text" readonly="readonly" style="width:90px; color:#000000;"')?>
	<script type="text/javascript">
		$('#edate_passwd').datepick({showOn: 'button', 
	    buttonImageOnly: true, buttonImage: '<?=base_url()?>public/js/jquery.datapick.package-3.6.1/calendar.gif'});
	</script>
</div>
<div class="column span-3" style="white-space: nowrap;">
	<?=form_label(lang('CHANGE_PASSWD_DATE'), 'chdate_passwd')?> <font color="red">*</font>
</div>
<div class="column span-9 last">
	<?=form_input('chdate_passwd', set_value('chdate_passwd', $ds->chdate_passwd), 'id="chdate_passwd" class="text" readonly="readonly" style="width:90px; color:#000000;"')?>
	<script type="text/javascript">
		$('#chdate_passwd').datepick({showOn: 'button', 
	    buttonImageOnly: true, buttonImage: '<?=base_url()?>public/js/jquery.datapick.package-3.6.1/calendar.gif'});
	</script>
</div>


<div class="column span-3">
	<?=form_label(lang('DESCR'), 'descr')?>
</div>
<div class="column span-9">
	<?=form_textarea('descr', set_value('descr', $ds->descr), 'id="descr" class="text" style="width:300px; height: 50px;"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('ARCHIVE'), 'archive')?>
</div>
<div class="column span-9 last">
	<?=form_dropdown('archive', array('0'=>lang('NO'), '1'=>lang('YES')), set_value('archive', $ds->archive), 'id="archive" class="text"')?>
</div>

</div>
<div class="column span-24 last">
	<input type="submit" value="<?=lang('SAVE')?>" class="button" id="submit" name="submit"> <input type="reset" value="<?=lang('CANCEL')?>" class="button" onclick="window.location='<?=site_url(get_currcontroller()) ?>';" id="reset" name="reset">
</div>

<?= form_close(); ?>

</div>
