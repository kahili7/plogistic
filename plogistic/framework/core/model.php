<?

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class KI_MODEL
{

    function KI_MODEL()
    {
	log_message('debug', "Model Class Initialized");
    }

    function __get($key)
    {
	$KI = & get_instance();
	return $KI->$key;
    }

}

?>