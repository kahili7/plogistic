<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_parents_tree($prid)
{
	$KI =& get_instance();
	$KI->load->model('positionsmenu_model');
	$list = transform2forest($KI->positionsmenu_model->get_list($prid), 'rid', 'parent');
	$tarr_1 = array(''=>$KI->config->item('crm_dropdown_empty'));
	$tarr_2 = parents_build($list, '');
	$tarr_2 = is_array($tarr_2)?$tarr_2:array();
	$tarr = $tarr_1+$tarr_2;
	return $tarr; 
}

function parents_build($items, $w)
{
	global $t_res;
	
	foreach($items as $item)
	{
		if($item['childNodes'])
		{
			$t_res[$item['rid']] = $w.$item['item_name'];
			parents_build($item['childNodes'], $w.'----');
		}
		else 
		{
			$t_res[$item['rid']] = $w.$item['item_name'];
		}
	}
	
	return $t_res;
}

function build_tree_dropdown($position_rid, $parent='')
{
	return form_dropdown('parent', get_parents_tree($position_rid), set_value('parent', $parent), 'id="parent" class="text" ');
}
?>