<div class="container findform">
<?=form_open(get_currcontroller()."/find/go", array('id' => 'find_'.$orid, 'autocomplete' => 'off'))?>
<?=form_hidden('obj_rid', $orid)?>
<div class="column span-4">
	<h6><?=lang('SEARCH_TITLE')?></h6>
</div>
<div class="column span-4">
	&nbsp;
</div>
<div class="column span-10">
	<?=form_label(lang('NAME'), 'city_name')?>
	<br>
	<?=form_input('city_name', element('_cities.city_name', $search, ''), 'id="city_name" class="text"')?>
</div>
<?=form_close();?>
</div>
<div class="container" style="padding: 0px 0px 5px 0px; margin: 0px; text-align: right;">
	<div class="column span-24 last">
		<input type="button" value="<?=lang('GOFIND')?>" onclick="" class="button" id="find_submit" name="find_submit"> <input type="button" value="<?=lang('GOCLEAR')?>" onclick="" class="button"  id="find_reset" name="find_reset">
	</div>
</div>
<script type="text/javascript">
$(document).ready(
		function(){
			$('#find_submit').click(function(){$('#find_<?=$orid?>').submit();});
			$('#find_reset').click(function(){
					$('#_countries_rid').val('');
					$('#city_name').val('');
					$('#find_<?=$orid?>').submit();
				});
		}
)	
</script>