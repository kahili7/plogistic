<div class="grid">
<h3><?=$title?></h3>
<?= form_open(get_currcontroller()."/create/go", array('id' => 'create_'.$orid, 'autocomplete' => 'off'))?>
<div class="container editform">
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
	<?=form_label(lang('CODE'), 'code')?> <font color="red">*</font>
</div>
<div class="column span-9">
	<?=form_input('code', set_value('code', ''), 'id="code" class="text"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('NAME'), 'name')?> <font color="red">*</font>
</div>
<div class="column span-9 last">
	<?=form_input('name', set_value('name', ''), 'id="name" class="text"')?>
</div>

<div class="column span-3">
	<?=form_label(lang('DESCR'), 'descr')?>
</div>
<div class="column span-9">
	<?=form_textarea('descr', set_value('descr', ''), 'id="descr" class="text" style="width:200px; height: 50px;"')?>
</div>
<div class="column span-3">
	<?=form_label(lang('ARCHIVE'), 'archive')?>
</div>
<div class="column span-9 last">
	<?=form_dropdown('archive', array('0'=>lang('NO'), '1'=>lang('YES')), set_value('archive', '0'), 'id="archive" class="text"')?>
</div>

</div>
<div class="column span-24 last">
	<input type="submit" value="<?=lang('SAVE')?>" class="button" id="submit" name="submit"> <input type="reset" value="<?=lang('CANCEL')?>" class="button" onclick="window.location='<?=site_url(get_currcontroller()) ?>';" id="reset" name="reset">
</div>
<?=form_close();?>
</div>