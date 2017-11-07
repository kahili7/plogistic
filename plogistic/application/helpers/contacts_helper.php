<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_contactname_byrid($rid, $full=FALSE)
{
	$KI =& get_instance();
	$KI->load->model('contacts_model');
	return $KI->contacts_model->get_contactname_byrid($rid);
}

function get_contacts_vp($default_value=null, $val_p='_contacts_rid', $scr_p='contact_name', $show_details=TRUE)
{
	$KI =& get_instance();
	$data = array();
	$data['default_value'] = $default_value;
	$data['val_p'] = $val_p;
	$data['scr_p'] = $scr_p;
	$data['show_details'] = $show_details;
	return $KI->load->view('contacts/value_picker', $data, TRUE); 
}
?>