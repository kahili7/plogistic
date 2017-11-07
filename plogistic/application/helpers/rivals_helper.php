<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_rivals_list()
{
	$KI =& get_instance();
	$KI->load->model('rivals_model');
	$list = $KI->rivals_model->get_list();
	$res = array('' => $KI->config->item('crm_dropdown_empty'));
	
	foreach($list as $c) $res[$c->rid] = $c->name;
	return $res;
}

function get_rivalname_byrid($rid)
{
	$KI =& get_instance();
	$KI->load->model('rivals_model');
	return $KI->rivals_model->get_rivalname_byrid($rid);
}

function get_rivals_vp($default_value=null, $val_p='_rivals_rid', $scr_p='rival_name', $show_details=TRUE)
{
	$KI =& get_instance();
	$data = array();
	$data['default_value'] = $default_value;
	$data['val_p'] = $val_p;
	$data['scr_p'] = $scr_p;
	$data['show_details'] = $show_details;
	return $KI->load->view('rivals/value_picker', $data, TRUE); 
}
?>