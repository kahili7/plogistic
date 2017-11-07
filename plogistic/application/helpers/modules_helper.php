<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_controllers_list()
{
	$KI =& get_instance();
	$KI->load->helper('directory');
	$map = directory_map(APPPATH.'/controllers/', False);
	$res = array('' => $KI->config->item('crm_dropdown_empty'));
	
	foreach($map as $c)
	{
		$file_info = pathinfo(APPPATH.'/controllers/'.$c);
		
		if('.'.$file_info['extension'] == EXT) $res[$file_info['filename']] = $file_info['filename'];
	}
	
	asort($res);
	return $res;
}

function get_module_permissions($module_rid)
{
	$KI =& get_instance();
	$KI->load->model('modules_model');
	return $KI->modules_model->get_module_permissions($module_rid);
}


function get_modules_list()
{
	$KI =& get_instance();
	$KI->load->model('modules_model');
	$list = $KI->modules_model->get_list();
	$res = array('' => $KI->config->item('crm_dropdown_empty'));
	
	foreach($list as $c) $res[$c->rid] = $c->module_name.($c->module_controller ? ('|'.$c->module_controller) : '');
	return $res;
}
?>