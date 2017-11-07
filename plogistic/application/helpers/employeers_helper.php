<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_emp_fullname_byrid($rid)
{
	$KI =& get_instance();
	$KI->load->model('employeers_model');
	return $KI->employeers_model->get_emp_fullname_byrid($rid);
}

function get_employeers_vp($default_value=null, $val_p='_employeers_rid', $scr_p='full_name', $show_details=TRUE)
{
	$KI =& get_instance();
	$data = array();
	$data['default_value'] = $default_value;
	$data['val_p'] = $val_p;
	$data['scr_p'] = $scr_p;
	$data['show_details'] = $show_details;
	return $KI->load->view('employeers/value_picker', $data, TRUE); 
}
?>