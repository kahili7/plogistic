<?

(defined('BASEPATH')) OR exit('No direct script access allowed');

class KI_CONTROLLER
{

    private static $instance;

    public function KI_CONTROLLER()
    {
	self::$instance = & $this;

	foreach (is_loaded () as $var => $class)
	{
	    $this->$var = & load_class($class);
	}

	$this->load = & load_class('Loader', 'core');
	$this->load->_base_classes = & is_loaded();
	$this->load->_ki_autoloader();
	log_message('debug', "Controller Class Initialized");
	$this->output->enable_profiler(TRUE);
    }

    public static function &get_instance()
    {
	return self::$instance;
    }

}

function &get_instance()
{
    return KI_CONTROLLER::get_instance();
}

?>