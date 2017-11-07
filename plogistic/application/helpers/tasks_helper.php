<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_tasks()
{
	$KI =& get_instance();
	return $KI->taskslib->get_tasks();
}

function get_task_bkg($date)
{
	if(date('Y-m-d') > date('Y-m-d', strtotime($date))) return '#FBE3E4';
	else if(date('Y-m-d') == date('Y-m-d', strtotime($date))) return '#E6EFC2';
	else return '#FFFFFF';
}
?>