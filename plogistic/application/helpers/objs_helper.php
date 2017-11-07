<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_objname_byrid($rid, $full=FALSE)
{
	$KI =& get_instance();
	$KI->load->model('objs_model');
	return $KI->objs_model->get_objname_byrid($rid);
}

function get_objs_vp($default_value=null, $val_p='_objs_headers_rid', $scr_p='obj_name', $show_details=TRUE)
{
	$KI =& get_instance();
	$data = array();
	$data['default_value'] = $default_value;
	$data['val_p'] = $val_p;
	$data['scr_p'] = $scr_p;
	$data['show_details'] = $show_details;
	return $KI->load->view('objs/value_picker', $data, TRUE); 
}
?>