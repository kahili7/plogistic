<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('set_cookie'))
{
	function set_cookie($name = '', $value = '', $expire = '', $domain = '', $path = '/', $prefix = '')
	{
		$KI =& get_instance();
		$KI->input->set_cookie($name, $value, $expire, $domain, $path, $prefix);
	}
}

if (!function_exists('get_cookie'))
{
	function get_cookie($index = '', $xss_clean = FALSE)
	{
		$KI =& get_instance();
		$prefix = '';

		if (!isset($_COOKIE[$index]) && config_item('cookie_prefix') != '')
		{
			$prefix = config_item('cookie_prefix');
		}

		return $KI->input->cookie($prefix.$index, $xss_clean);
	}
}

if (!function_exists('delete_cookie'))
{
	function delete_cookie($name = '', $domain = '', $path = '/', $prefix = '')
	{
		set_cookie($name, '', '', $domain, $path, $prefix);
	}
}