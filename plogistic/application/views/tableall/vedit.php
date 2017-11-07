<div class="grid">
<h3><?=$title?></h3>
<?= form_open(get_currcontroller()."/vedit/{$rid}", array('id' => 'edit_'.$orid, 'autocomplete' => 'off'))?>
<div class="container editform">
<?=form_hidden('rid', $rid)?>
<?if(validation_errors()){?>
<div class="error">
	<?=validation_errors('<div>', '</div>');?>
</div>	
<?}?>
<?if($success === FALSE){?>
<div class="error">
	<?=lang('SAVE_SYSTEM_ERROR')?>
</div>
<?}?>
<?if($success === TRUE){?>
<div class="success">
	<?=lang('SAVE_SYSTEM_SUCCESS')?>
</div>
<?}?>
<div class="column span-3">
	<?=form_label(lang('NAME'), 'name')?> <font color="red">*</font>
</div>
<div class="column span-9">
	<?=form_input('name', set_value('name', $ds->name), 'id="name" class="text"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('CONTFACE'), 'contface')?> <font color="red">*</font>
</div>
<div class="column span-9 last">
	<?=form_input('contface', set_value('contface', $ds->contface), 'id="contface" class="text"')?>
</div>

<div class="column span-3">
	<?=form_label(lang('JOB'), 'job')?>
</div>
<div class="column span-9">
	<?=form_input('job', set_value('job', $ds->job), 'id="job" class="text"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('PHONE'), 'phone')?> 
</div>
<div class="column span-9">
	<?=form_input('phone', set_value('phone', $ds->phone), 'id="phone" class="text"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('EMAIL'), 'email')?>
</div>
<div class="column span-9 last">
	<?=form_input('email', set_value('email', $ds->email), 'id="email" class="text"')?>
</div>

<div class="column span-3">
	<?=form_label(lang('BIRTHDAY'), 'birthday')?>
</div>
<div class="column span-9 last">
	<?=form_input('birthday', set_value('birthday', $ds->birthday), 'id="birthday" class="text" style="width:90px;"')?>
	<script type="text/javascript">
		$('#birthday').datepick({showOn: 'button', yearRange: '-60:+0',
	    buttonImageOnly: true, buttonImage: '<?=base_url()?>public/js/jquery.datapick.package-3.6.1/calendar.gif'});
	</script>
</div>
<div class="column span-3">
	<?=form_label(lang('DESCR'), 'descr')?>
</div>
<div class="column span-9">
	<?=form_textarea('descr', set_value('descr', $ds->descr), 'id="descr" class="text" style="width:300px; height: 100px;"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('ARCHIVE'), 'archive')?>
</div>
<div class="column span-9 last">
	<?=form_dropdown('archive', array('0' => lang('NO'), '1' => lang('YES')), set_value('archive', $ds->archive), 'id="archive" class="text"')?>
</div>
</div>
<div class="column span-24 last">
	<input type="submit" value="<?=lang('SAVE')?>" class="button" id="submit" name="submit"> <input type="reset" value="<?=lang('CANCEL')?>" class="button" onclick="window.location='<?=site_url(get_currcontroller().'/vjournal/go/')?>';" id="reset" name="reset">
	<button onclick="joinToParent('<?=$ds->$jtp['val']?>', '<?=htmlspecialchars($ds->$jtp['scr'])?>')" class="button"><?=lang('SELECT')?></button>
</div>
<?=form_close();?>
</div>
<script type="text/javascript">
function joinToParent(val, scr){
	$("input[name='<?=$jtp['val_p']?>']", window.opener.document).val(val);
	$('#<?=$jtp['scr_p']?>', window.opener.document).val(scr);
	this.close();
	return;
}	
</script>