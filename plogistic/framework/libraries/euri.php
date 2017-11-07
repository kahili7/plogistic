<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

class KI_EURI extends KI_URI
{
	private $_get_params = array();

	function _fetch_uri_string() 
	{
		parse_str($_SERVER['QUERY_STRING'], $this->_get_params);

		$_GET = array();
		$_SERVER['QUERY_STRING'] = '';

		parent::_fetch_uri_string();
	}

	function getParam($key) 
	{
		if(isset($this->_get_params[$key])) 
		{
			return $this->_get_params[$key];
		} 
		else return false;
	}

	function getParams() 
	{
		return $this->_get_params;
	}
}
?>