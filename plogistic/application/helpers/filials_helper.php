<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_filialname_byrid($rid)
{
	$KI =& get_instance();
	$KI->load->model('filials_model');
	return $KI->filials_model->get_name_byrid($rid);
}

function get_filial_info($rid)
{
	$KI =& get_instance();
	$KI->load->model('filials_model');
	return $KI->filials_model->get_edit($rid);
}

function get_filials_vp($default_value=null, $val_p='_filials_rid', $scr_p='filial_name', $show_details=TRUE)
{
	$KI =& get_instance();
	$data = array();
	$data['default_value'] = $default_value;
	$data['val_p'] = $val_p;
	$data['scr_p'] = $scr_p;
	$data['show_details'] = $show_details;
	return $KI->load->view('filials/value_picker', $data, TRUE); 
}
?>