<div class="container findform">
<?=form_open(get_currcontroller()."/find/go", array('id' => 'find_'.$orid, 'autocomplete' => 'off'))?>
<?=form_hidden('obj_rid', $orid)?>
<div class="column span-4">
	<h6><?=lang('SEARCH_TITLE')?></h6>
</div>
<div class="column span-6">
	<?=form_label('ID', 'rid')?>
	<br>
	<?=form_input('rid', element('_objects.rid', $search, ''), 'id="rid" class="text" style="width:90px;"')?>
</div>
<div class="column span-7">
	<?=form_label(lang('DOC_FROM'), 'doc_from')?>
	<br>
	<?=form_input('doc_from', date_conv(element('_emp_to_positions_headers.date_doc >=', $search, null)), 'id="doc_from" class="text" readonly="readonly" style="width:90px;"')?>
	<script type="text/javascript">
		$('#doc_from').datepick({showOn: 'button', yearRange: '-60:+0',
		buttonImageOnly: true, buttonImage: '<?=base_url()?>public/js/jquery.datapick.package-3.6.1/calendar.gif'});
	</script>
</div>
<div class="column span-7 last">
	<?=form_label(lang('DOC_TO'), 'doc_to')?>
	<br>
	<?=form_input('doc_to', date_conv(element('_emp_to_positions_headers.date_doc <=', $search, null)), 'id="doc_to" class="text" readonly="readonly" style="width:90px;"')?>
		<script type="text/javascript">
			$('#doc_to').datepick({showOn: 'button', yearRange: '-60:+0',
			buttonImageOnly: true, buttonImage: '<?=base_url()?>public/js/jquery.datapick.package-3.6.1/calendar.gif'});
		</script>
</div>
<div class="column span-4">
	&nbsp;
</div>
<div class="column span-20 last">
	<?=form_label(lang('FILIAL'), 'filial_name')?>
	<br>
	<?=get_filials_vp(element('_emp_to_positions_rows._filials_rid', $search, null), '_filials_rid', 'filial_name', False)?>
</div>
<div class="column span-4">
	&nbsp;
</div>
<div class="column span-20 last">
	<?=form_label(lang('EMPLOYEER'), 'filial_name')?>
	<br>
	<?=get_employeers_vp(element('_emp_to_positions_rows._employeers_rid', $search, null), '_employeers_rid', 'full_name', False)?>
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
					$("input[name='_filials_rid']").val('');
					$('#filial_name').val('');
					$("input[name='_employeers_rid']").val('');
					$('#full_name').val('');
					$('#rid').val('');
					$('#doc_from').val('');
					$('#doc_to').val('');
					$('#find_<?=$orid?>').submit();
				});
		}
)	
</script>