<?
function menu_build($items)
{
	foreach($items as $item)
	{
		if($item['childNodes'])
		{
			print '<li class="accessible">';
			print anchor($item['module_controller'], $item['item_name'].' »', "title=\"{$item['descr']}\"");
			print '<ul>';
			menu_build($item['childNodes']);
			print '</ul>';
			print '</li>';
		} 
		else 
		{
			print '<li>'.anchor($item['module_controller'], $item['item_name'], "title=\"{$item['descr']}\"").'</li>';
		}
	}
}
?>
<div class="container">
	<div class="span-24 last">
		<ul class="jd_menu">
				<?menu_build($items)?>
		</ul>
	</div>
</div>