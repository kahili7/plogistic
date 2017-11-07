<div class="grid">
<h3><?=$title?></h3>
<div class="container editform">

<fieldset>
	<legend><?=lang('EMPTOPOSITIONS')?></legend>
	<div class="column span-3">
		<?=form_label('ID', 'rid')?>
	</div>
	<div class="column span-9">
		<?=form_input('rid', set_value('rid', $ds->rid), 'id="rid" class="text" readonly="readonly" style="width:90px;"')?>
	</div>
	<div class="column span-3">
		<?=form_label(lang('DATE_OBJ'), 'date_obj')?> 
	</div>
	<div class="column span-9 last">
		<?=form_input('date_obj', date_conv(set_value('date_obj', $ds->date_obj)), 'id="date_obj" class="text" readonly="readonly" style="width:90px;"')?>
	</div>
</fieldset>

<div class="column span-3">
	<?=form_label(lang('EMPLOYEER'), 'full_name')?> 
</div>
<div class="column span-9">
	<?=form_input('full_name', get_emp_fullname_byrid(set_value('_employeers_rid', $ds->_employeers_rid)), 'id="full_name" class="text" readonly="readonly" ')?>
</div>

<div class="column span-3">
	<?=form_label(lang('FILIAL'), 'filial_name')?> 
</div>
<div class="column span-9 last">
	<?=form_input('filial_name', get_filialname_byrid(set_value('_filials_rid', $ds->_filials_rid)), 'id="filial_name" class="text" readonly="readonly" style="width:150px;"')?>
</div>

<div class="column span-3">
	<?=form_label(lang('POSITION'), '_positions_rid')?> 
</div>
<div class="column span-9">
	<?=form_dropdown('_positions_rid', get_positions_list(), set_value('_positions_rid', $ds->_positions_rid), 'id="_positions_rid" class="text" readonly="readonly" ')?>
</div>

<div class="column span-3">
	<?=form_label(lang('BDATE'), 'bdate')?> 
</div>
<div class="column span-9 last">
	<?=form_input('bdate', date_conv(set_value('bdate', $ds->bdate)), 'id="bdate" class="text" readonly="readonly" style="width:90px;"')?>
</div>
<div class="column span-4">
	<?=form_label(lang('DESCR'), 'descr')?>
</div>
<div class="column span-8">
	<?=form_textarea('descr', set_value('descr', $ds->descr), 'id="descr" class="text" readonly="readonly" style="width:200px; height: 50px;"')?>
</div>
<div class="column span-4">
	<?=form_label(lang('ARCHIVE'), 'archive')?>
</div>
<div class="column span-8 last">
	<?=form_dropdown('archive', array('0' => lang('NO'), '1' => lang('YES')), set_value('archive', $ds->archive), 'id="archive" class="text" readonly="readonly"')?>
</div>
</div>
<div class="column span-24 last">
	<input type="reset" value="<?=lang('CANCEL')?>" class="button" onclick="window.location='<?=site_url(get_currcontroller())?>';" id="reset" name="reset">
</div>
</div>