<?
if(!defined('BASEPATH')) exit('No direct script access allowed');

class KI_USER_AGENT
{
	var $agent		= NULL;

	var $is_browser	= FALSE;
	var $is_robot	= FALSE;
	var $is_mobile	= FALSE;

	var $languages	= array();
	var $charsets	= array();

	var $platforms	= array();
	var $browsers	= array();
	var $mobiles	= array();
	var $robots		= array();

	var $platform	= '';
	var $browser	= '';
	var $version	= '';
	var $mobile		= '';
	var $robot		= '';
	
	public function __construct()
	{
            if (isset($_SERVER['HTTP_USER_AGENT']))
            {
		$this->agent = trim($_SERVER['HTTP_USER_AGENT']);
            }

            if (!is_null($this->agent))
            {
                if ($this->_load_agent_file())
                {
                    $this->_compile_data();
		}
            }

            log_message('debug', "User Agent Class Initialized");
	}
	
	private function _load_agent_file()
	{
            if(!@include(APPPATH.'config/user_agents'.EXT))
            {
		return FALSE;
            }

            $return = FALSE;

            if(isset($platforms))
            {
		$this->platforms = $platforms;
		unset($platforms);
		$return = TRUE;
            }

            if(isset($browsers))
            {
                $this->browsers = $browsers;
		unset($browsers);
		$return = TRUE;
            }

            if(isset($mobiles))
            {
		$this->mobiles = $mobiles;
		unset($mobiles);
		$return = TRUE;
            }

            if(isset($robots))
            {
		$this->robots = $robots;
		unset($robots);
		$return = TRUE;
            }

            return $return;
	}
	
	private function _compile_data()
	{
            $this->_set_platform();

            foreach(array('_set_browser', '_set_robot', '_set_mobile') as $function)
            {
		if($this->$function() === TRUE)
		{
                    break;
		}
            }
	}

	private function _set_platform()
	{
            if(is_array($this->platforms) AND count($this->platforms) > 0)
            {
		foreach($this->platforms as $key => $val)
		{
                    if(preg_match("|".preg_quote($key)."|i", $this->agent))
                    {
			$this->platform = $val;
			return TRUE;
                    }
		}
            }

            $this->platform = 'Unknown Platform';
	}
	
	private function _set_browser()
	{
            if(is_array($this->browsers) AND count($this->browsers) > 0)
            {
		foreach($this->browsers as $key => $val)
		{
                    if(preg_match("|".preg_quote($key).".*?([0-9\.]+)|i", $this->agent, $match))
                    {
                        $this->is_browser = TRUE;
			$this->version = $match[1];
			$this->browser = $val;
			$this->_set_mobile();
			return TRUE;
                    }
		}
            }

            return FALSE;
	}
	
	private function _set_robot()
	{
            if(is_array($this->robots) AND count($this->robots) > 0)
            {
		foreach($this->robots as $key => $val)
                {
                    if(preg_match("|".preg_quote($key)."|i", $this->agent))
                    {
			$this->is_robot = TRUE;
			$this->robot = $val;
			return TRUE;
                    }
		}
            }
		
            return FALSE;
	}
	
	private function _set_mobile()
	{
            if(is_array($this->mobiles) AND count($this->mobiles) > 0)
            {
                foreach($this->mobiles as $key => $val)
		{
                    if(FALSE !== (strpos(strtolower($this->agent), $key)))
                    {
			$this->is_mobile = TRUE;
                        $this->mobile = $val;
                        return TRUE;
			}
                    }
            }

            return FALSE;
	}
	
	private function _set_languages()
	{
            if((count($this->languages) == 0) AND isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) AND $_SERVER['HTTP_ACCEPT_LANGUAGE'] != '')
            {
		$languages = preg_replace('/(;q=[0-9\.]+)/i', '', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'])));
		$this->languages = explode(',', $languages);
            }

            if (count($this->languages) == 0)
            {
		$this->languages = array('Undefined');
            }
	}
	
	private function _set_charsets()
	{
            if((count($this->charsets) == 0) AND isset($_SERVER['HTTP_ACCEPT_CHARSET']) AND $_SERVER['HTTP_ACCEPT_CHARSET'] != '')
            {
		$charsets = preg_replace('/(;q=.+)/i', '', strtolower(trim($_SERVER['HTTP_ACCEPT_CHARSET'])));
		$this->charsets = explode(',', $charsets);
            }

            if(count($this->charsets) == 0)
            {
		$this->charsets = array('Undefined');
            }
	}
	
	public function is_browser($key = NULL)
	{
            if (!$this->is_browser)
            {
		return FALSE;
            }

            if ($key === NULL)
            {
		return TRUE;
            }

            return array_key_exists($key, $this->browsers) AND $this->browser === $this->browsers[$key];
	}
	
	public function is_robot($key = NULL)
	{
            if (!$this->is_robot)
            {
		return FALSE;
            }

            if ($key === NULL)
            {
		return TRUE;
            }

            return array_key_exists($key, $this->robots) AND $this->robot === $this->robots[$key];
	}
	
	public function is_mobile($key = NULL)
	{
            if (!$this->is_mobile)
            {
		return FALSE;
            }

            if ($key === NULL)
            {
                return TRUE;
            }

            return array_key_exists($key, $this->mobiles) AND $this->mobile === $this->mobiles[$key];
	}
			
	function is_referral()
	{
            return (!isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? FALSE : TRUE;
	}
		
	function agent_string()
	{
            return $this->agent;
	}
		
	function platform()
	{
            return $this->platform;
	}
	
	function browser()
	{
            return $this->browser;
	}
		
	function version()
	{
            return $this->version;
	}
			
	function robot()
	{
            return $this->robot;
	}
		
	function mobile()
	{
            return $this->mobile;
	}
		
	function referrer()
	{
            return (!isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? '' : trim($_SERVER['HTTP_REFERER']);
	}
		
	function languages()
	{
            if(count($this->languages) == 0)
            {
		$this->_set_languages();
            }

            return $this->languages;
	}

		
	function charsets()
	{
            if(count($this->charsets) == 0)
            {
                $this->_set_charsets();
            }

            return $this->charsets;
	}
		
	function accept_lang($lang='en')
	{
            return (in_array(strtolower($lang), $this->languages(), TRUE)) ? TRUE : FALSE;
	}
		
	function accept_charset($charset='utf-8')
	{
            return (in_array(strtolower($charset), $this->charsets(), TRUE)) ? TRUE : FALSE;
	}
}
?>