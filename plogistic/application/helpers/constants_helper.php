<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_constant($code)
{
	$KI =& get_instance();
	return element($code, $KI->a_constants, null);
}
?>