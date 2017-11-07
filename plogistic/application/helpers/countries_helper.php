<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_countries_list(){
	$KI =& get_instance();
	$KI->load->model('countries_model');
	$list = $KI->countries_model->get_list();
	$res = array('' => $KI->config->item('crm_dropdown_empty'));
	
	foreach($list as $c) $res[$c->rid] = $c->country_name;
	
	return $res;
}

function get_countryname_byrid($rid)
{
	$KI =& get_instance();
	$KI->load->model('countries_model');
	return $KI->countries_model->get_countryname_byrid($rid);
}
?>