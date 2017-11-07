<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_cities_list()
{
	$KI =& get_instance();
	$KI->load->model('cities_model');
	$list = $KI->cities_model->get_list();
	$res = array('' => $KI->config->item('crm_dropdown_empty'));
	
	foreach($list as $c) $res[$c->rid] = $c->city_name;
	return $res;
}

function get_cityname_byrid($rid)
{
	$KI =& get_instance();
	$KI->load->model('cities_model');
	return $KI->cities_model->get_name_byrid($rid);
}

function get_cities_vp($default_value=null, $val_p='_cities_rid', $scr_p='city_name', $show_details=TRUE)
{
	$KI =& get_instance();
	$data = array();
	$data['default_value'] = $default_value;
	$data['val_p'] = $val_p;
	$data['scr_p'] = $scr_p;
	$data['show_details'] = $show_details;
	return $KI->load->view('cities/value_picker', $data, TRUE); 
}
?>