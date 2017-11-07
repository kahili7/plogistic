<div class="grid">
<h3><?=$title?></h3>
<div class="container editform">
<div class="column span-3">
	<?=form_label(lang('NAME'), 'name')?><font color="red">*</font>
</div>
<div class="column span-9">
	<?=form_input('name', set_value('name', $ds->name), 'id="name" class="text" readonly="readonly"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('CONTFACE'), 'contface')?><font color="red">*</font> 
</div>
<div class="column span-9 last">
	<?=form_input('contface', set_value('contface', $ds->contface), 'id="contface" class="text" readonly="readonly"')?>
</div>

<div class="column span-3">
	<?=form_label(lang('JOB'), 'job')?>
</div>
<div class="column span-9">
	<?=form_input('job', set_value('job', $ds->job), 'id="job" class="text" readonly="readonly"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('PHONE'), 'phone')?> 
</div>
<div class="column span-9">
	<?=form_input('phone', set_value('phone', $ds->phone), 'id="phone" class="text" readonly="readonly"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('EMAIL'), 'email')?>
</div>
<div class="column span-9 last">
	<?=form_input('email', set_value('email', $ds->email), 'id="email" class="text" readonly="readonly"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('BIRTHDAY'), 'birthday')?>
</div>
<div class="column span-9 last">
	<?=form_input('birthday', set_value('birthday', $ds->birthday), 'id="birthday" class="text" readonly="readonly" style="width:90px;"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('DESCR'), 'descr')?>
</div>
<div class="column span-9">
	<?=form_textarea('descr', set_value('descr', $ds->descr), 'id="descr" class="text" readonly="readonly" style="width:300px; height: 100px;"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('ARCHIVE'), 'archive')?>
</div>
<div class="column span-9 last">
	<?=form_dropdown('archive', array('0' => lang('NO'), '1' => lang('YES')), set_value('archive', $ds->archive), 'id="archive" class="text" readonly="readonly"')?>
</div>

</div>
<div class="column span-24 last">
	<input type="reset" value="<?=lang('CANCEL')?>" class="button" onclick="window.location='<?=site_url(get_currcontroller().'/vjournal/go/')?>';" id="reset" name="reset">
	<button onclick="joinToParent('<?=$ds->$jtp['val']?>', '<?=htmlspecialchars($ds->$jtp['scr'])?>')" class="button"><?=lang('SELECT')?></button>
</div>
</div>
<script type="text/javascript">
function joinToParent(val, scr){
	$("input[name='<?=$jtp['val_p']?>']", window.opener.document).val(val);
	$('#<?=$jtp['scr_p']?>', window.opener.document).val(scr);
	this.close();
	return;
}	
</script>