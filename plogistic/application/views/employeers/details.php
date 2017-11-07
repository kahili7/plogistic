<div class="grid">
<h3><?=$title?></h3>
<div class="container editform">
<div class="column span-4">
	<?=form_label(lang('F_NAME'), 'f_name')?> <font color="red">*</font>
</div>
<div class="column span-8">
	<?=form_input('f_name', set_value('f_name', $ds->f_name), 'id="f_name" class="text" readonly="readonly" style="width:150px;"')?>
</div>
<div class="column span-4">
	<?=form_label(lang('F_NAME_LAT'), 'f_name_lat')?>
</div>
<div class="column span-8 lat">
	<?=form_input('f_name_lat', set_value('f_name_lat', $ds->f_name_lat), 'id="f_name_lat" readonly="readonly" class="text" style="width:150px;"')?>
</div>

<div class="column span-4">
	<?=form_label(lang('S_NAME'), 's_name')?>
</div>
<div class="column span-8">
	<?=form_input('s_name', set_value('s_name', $ds->s_name), 'id="s_name" class="text" readonly="readonly" style="width:150px;"')?>
</div>
<div class="column span-4">
	<?=form_label(lang('L_NAME_LAT'), 'l_name_lat')?>
</div>
<div class="column span-8 last">
	<?=form_input('l_name_lat', set_value('l_name_lat', $ds->l_name_lat), 'id="l_name_lat" readonly="readonly" class="text" style="width:150px;"')?>
</div>

<div class="column span-4">
	<?=form_label(lang('L_NAME'), 'l_name')?> <font color="red">*</font>
</div>
<div class="column span-20 last">
	<?=form_input('l_name', set_value('l_name', $ds->l_name), 'id="l_name" class="text" readonly="readonly" style="width:150px;"')?>
</div>
<div class="column span-4">
	<?=form_label(lang('BIRTHDAY'), 'birthday')?>
</div>
<div class="column span-20 last">
	<?=form_input('birthday', set_value('birthday', $ds->birthday), 'id="birthday" class="text" readonly="readonly" style="width:90px;"')?>
</div>
<div class="column span-4">
	<?=form_label(lang('PHONE'), 'phone')?>
</div>
<div class="column span-8">
	<?=form_input('phone', set_value('phone', $ds->phone), 'id="phone" readonly="readonly" class="text" style="width: 150px;"')?>
</div>
<div class="column span-4">
	<?=form_label(lang('EMAIL'), 'email')?>
</div>
<div class="column span-8">
	<?=form_input('email', set_value('email', $ds->email), 'id="email" readonly="readonly" class="text" ')?>
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
	<?=form_dropdown('archive', array('0'=>lang('NO'), '1'=>lang('YES')), set_value('archive', $ds->archive), 'id="archive" class="text" readonly="readonly"')?>
</div>

</div>
<div class="column span-24 last">
	<input type="reset" value="<?=lang('CANCEL')?>" class="button" onclick="window.location='<?=site_url(get_currcontroller()) ?>';" id="reset" name="reset">
</div>

</div>