<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_historyname_byrid($rid, $full=FALSE)
{
	$KI =& get_instance();
	$KI->load->model('histories_model');
	return $KI->histories_model->get_historyname_byrid($rid);
}

function get_histories_vp($default_value=null, $val_p='_histories_rid', $scr_p='history_name', $show_details=TRUE)
{
	$KI =& get_instance();
	$data = array();
	$data['default_value'] = $default_value;
	$data['val_p'] = $val_p;
	$data['scr_p'] = $scr_p;
	$data['show_details'] = $show_details;
	return $KI->load->view('histories/value_picker', $data, TRUE); 
}
?>