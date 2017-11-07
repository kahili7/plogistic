<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_positions_list()
{
	$KI =& get_instance();
	$KI->load->model('positions_model');
	$list = $KI->positions_model->get_list();
	$res = array('' => $KI->config->item('crm_dropdown_empty'));
	
	foreach($list as $c) $res[$c->rid] = $c->name;
	
	return $res;
}

function get_areas()
{
	$KI =& get_instance();
	$res = array('' => $KI->config->item('crm_dropdown_empty'), 'ALL' => lang('A_A'), 'FILIAL' => lang('A_F'), 'OWN' => lang('A_O'));
	return $res;
}
?>