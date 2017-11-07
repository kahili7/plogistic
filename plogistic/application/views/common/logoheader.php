<div class="container">
	<div class="column span-24 logoheader">
		<div class="container">
			<div class="column span-10">		
				<?=img(array('src'=>'public/img/logos/logo.gif', 'border'=>'0'))?>
			</div>
			<div class="column span-10" style="text-align: right;">
				<b><?=lang('CURRENT_USER')?></b> <em><?=get_curr_uname()?></em>
				<br/>
				<b><?=lang('CURRENT_POSITION')?></b> <em><?=get_curr_pname()?></em>
			</div>
			<div class="column span-4 last" style="text-align: right;">
				<?=anchor('login/logout', lang('EXIT'), 'onclick="return confirm(\''.lang('CONFIRM_EXIT').'\');"')?>
			</div>
		</div>
	</div>
</div>
