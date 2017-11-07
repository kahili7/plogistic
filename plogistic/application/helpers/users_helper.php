<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_emprid_byurid($urid)
{
	$KI =& get_instance();
	$KI->load->model('users_model');
	return $KI->users_model->get_emprid_byurid($urid);
}

function get_urid_byemprid($urid)
{
	$KI =& get_instance();
	$KI->load->model('users_model');
	return $KI->users_model->get_urid_byemprid($urid);
}
?>