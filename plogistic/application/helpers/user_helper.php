<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_curr_uname()
{
	$KI =& get_instance();
	return $KI->user->get_ufn();
}

function get_curr_pname()
{
	$KI =& get_instance();
	return $KI->user->get_upn();
}

function get_curr_urid()
{
	$KI =& get_instance();
	return $KI->user->get_urid();
}

function get_curr_uprid()
{
	$KI =& get_instance();
	return $KI->user->get_uprid();
}

function get_curr_ufrid()
{
	$KI =& get_instance();
	return $KI->user->get_ufrid();
}
?>