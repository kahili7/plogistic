<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_companyname_byrid($rid, $full=FALSE)
{
	$KI =& get_instance();
	$KI->load->model('companies_model');
	return $KI->companies_model->get_companyname_byrid($rid);
}

function get_companies_vp($default_value=null, $val_p='_companies_headers_rid', $scr_p='company_name', $show_details=TRUE)
{
	$KI =& get_instance();
	$data = array();
	$data['default_value'] = $default_value;
	$data['val_p'] = $val_p;
	$data['scr_p'] = $scr_p;
	$data['show_details'] = $show_details;
	return $KI->load->view('companies/value_picker', $data, TRUE); 
}
?>