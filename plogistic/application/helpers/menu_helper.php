<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_menu()
{
	$KI =& get_instance();
	return $KI->menu->render_menu();
}

function get_currcontroller()
{
	$KI =& get_instance();
	return $KI->menu->get_currcontroller();
}
?>