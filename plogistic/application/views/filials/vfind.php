<div class="container findform">
<?= form_open(get_currcontroller()."/vfind/go", array('id' => 'vfind_'.$orid, 'autocomplete' => 'off'))?>
<?=form_hidden('obj_rid', $orid)?>
<div class="column span-4">
	<h6><?=lang('SEARCH_TITLE')?></h6>
</div>
<div class="column span-8">
	<?=form_label(lang('CITY'), 'city_name')?>
	<br>
	<?=get_cities_vp(element('_filials._cities_rid', $search, null), '_cities_rid', 'city_name', False);?>
</div>
<div class="column span-4">
	<?=form_label(lang('CODE'), 'code')?>
	<br>
	<?=form_input('code', element('_filials.code', $search, ''), 'id="code" class="text" style="width: 60px;"')?>
</div>
<div class="column span-8">
	<?=form_label(lang('NAME'), 'name')?>
	<br>
	<?=form_input('name', element('_filials.name', $search, ''), 'id="name" class="text"')?>
</div>
<?= form_close(); ?>
</div>
<div class="container" style="padding: 0px 0px 5px 0px; margin: 0px; text-align: right;">
	<div class="column span-24 last">
		<input type="button" value="<?=lang('GOFIND')?>" onclick="" class="button" id="find_submit" name="find_submit"> <input type="button" value="<?=lang('GOCLEAR')?>" onclick="" class="button"  id="find_reset" name="find_reset">
	</div>
</div>
<script type="text/javascript">
$(document).ready(
		function(){
			$('#find_submit').click(function(){$('#vfind_<?=$orid?>').submit();});
			$('#find_reset').click(function(){
					$("input[name='_cities_rid']").val('');
					$('#name').val('');
					$('#code').val('');
					$('#vfind_<?=$orid?>').submit();
				});
		}
)	
</script>