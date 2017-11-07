<div class="grid">
<h3><?=$title?></h3>
<?=$find?>
<?if($this->session->flashdata('remove_success') === TRUE){?>
<div class="success">
	<?=lang('REMOVE_SYSTEM_SUCCESS')?>
</div>
<?}?>
<?if($this->session->flashdata('remove_failed') === TRUE){?>
<div class="error">
	<?=lang('REMOVE_SYSTEM_ERROR')?>
</div>
<?}?>
<table cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="<?=count($fields)+2?>" style="text-align: right;background-color: #f6f6f6;">
			<div id="paging"><?=$paging?><br></div>
		</td>
	</tr>
	<tr>
		<?foreach($fields as $key => $field) { ?>
		<td class="gridHeader" width="<?=element('colwidth', $field, '')?>" style="white-space: nowrap;">
			<?if($field['sort']) { ?>
			<?=anchor(get_currcontroller().'/sort/'.$key, $field['label'], "title=\"".$field['label']."\"")?>
			&nbsp;&nbsp;&nbsp;
			<?if(element('c', $sort, null) == $key) { ?>
				<? if(element('r', $sort, null)=='ASC') { ?>
					<?=img('public/css/images/icons/arrow_down.gif')?>
				<? } else {?>
					<?=img('public/css/images/icons/arrow_up.gif')?>
				<? } ?>
			<? } else {?>
				<?=img('public/css/images/icons/arrow.gif')?>
			<? } ?>
			<? }else{ ?>
			<?=$field['label']?>
			<? } ?>
		</td>
		<?}?>
		<td class="gridHeader">
			&nbsp;
		</td>
	</tr>
	<?foreach($ds as $record) { ?>
	<tr class="dataRow">
		<?foreach($fields as $key => $field) 
		{
		    if($key == 'pstatus')
		    {
		        $ps_chk = $record->ps_chk - 1;
		        $key = $key.(($ps_chk <= '0') ? '' : $ps_chk).'_name';
		        $ps = $record->$key;
		        $pos = strpos($ps, '-');
		        
		        if($pos > 0) $record->$key = substr($ps, 0, $pos);
		    }
		?>
		<td style="<?=element('style', $field, '')?>">
			<?=get_valtype($record->$key, element('type', $field, ''))?> 
		</td>
		<?}?>
		<td nowrap="nowrap">
			<?
				foreach($tools as $key => $val)
				{
					if(!$val) continue;
					# хак для заархивированных записей 
					if($record->archive && !element('archive_allow', $tools, null) && $key != 'details_allow') continue;
			?>
				<?=get_ctool($key, $record->rid)?>
			<?}?>		
		</td>
	</tr>
	<?}?>
	<tr>
		<td colspan="<?=count($fields)+2?>" style="text-align: right;background-color: #f6f6f6;">
			<div id="paging"><?=$paging?><br></div>
		</td>
	</tr>
</table>
</div>