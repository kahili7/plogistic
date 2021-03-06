<div class="grid">
<h3><?=$title?></h3>
<?=form_open(get_currcontroller()."/edit/{$rid}", array('id' => 'edit_'.$orid, 'autocomplete' => 'off'))?>
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
<fieldset>
	<legend><?=lang('EMPTOPOSITIONS')?></legend>
	<div class="column span-3">
		<?=form_label('ID', 'rid')?>
	</div>
	<div class="column span-9">
		<?=form_input('rid', set_value('rid', $ds->rid), 'id="rid" class="text" readonly="readonly" style="width:90px;"')?>
	</div>
	<div class="column span-3">
		<?=form_label(lang('DATE_OBJ'), 'date_obj')?> <font color="red">*</font>
	</div>
	<div class="column span-9 last">
		<?=form_input('date_obj', date_conv(set_value('date_obj', $ds->date_obj)), 'id="date_obj" class="text" readonly="readonly" style="width:90px;"')?>
		<script type="text/javascript">
			$('#date_obj').datepick({showOn: 'button', yearRange: '-60:+60',
			buttonImageOnly: true, buttonImage: '<?=base_url()?>public/js/jquery.datapick.package-3.6.1/calendar.gif'});
		</script>
	</div>	
		
</fieldset>

<div class="column span-3">
	<?=form_label(lang('EMPLOYEER'), 'full_name')?> <font color="red">*</font>
</div>
<div class="column span-9">
	<?=get_employeers_vp(set_value('_employeers_rid', $ds->_employeers_rid))?>
</div>

<div class="column span-3">
	<?=form_label(lang('FILIAL'), 'filial_name')?> <font color="red">*</font>
</div>
<div class="column span-9 last">
	<?=get_filials_vp(set_value('_filials_rid', $ds->_filials_rid))?>
</div>

<div class="column span-3">
	<?=form_label(lang('POSITION'), '_positions_rid')?> <font color="red">*</font>
</div>
<div class="column span-9">
	<?=form_dropdown('_positions_rid', get_positions_list(), set_value('_positions_rid', $ds->_positions_rid), 'id="_positions_rid" class="text"')?>
</div>

<div class="column span-3">
	<?=form_label(lang('BDATE'), 'bdate')?> <font color="red">*</font>
</div>
<div class="column span-9 last">
	<?=form_input('bdate', date_conv(set_value('bdate', $ds->bdate)), 'id="bdate" class="text" readonly="readonly" style="width:90px;"')?>
	<script type="text/javascript">
		$('#bdate').datepick({showOn: 'button', yearRange: '-60:+60',
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
	<?=form_dropdown('archive', array('0' => lang('NO'), '1' => lang('YES')), set_value('archive', $ds->archive), 'id="archive" class="text"')?>
</div>
</div>
<div class="column span-24 last">
	<input type="submit" value="<?=lang('SAVE')?>" class="button" id="submit" name="submit"> <input type="reset" value="<?=lang('CANCEL')?>" class="button" onclick="window.location='<?=site_url(get_currcontroller())?>';" id="reset" name="reset">
</div>
<?=form_close();?>
</div>