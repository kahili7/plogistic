<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('lang'))
{
	function lang($line, $id='')
	{
		$KI =& get_instance();
		$line = $KI->lang->line($line);

		if($id != '')
		{
			$line = '<label for="'.$id.'">'.$line."</label>";
		}

		return $line;
	}
}