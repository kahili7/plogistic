<?

(defined('BASEPATH')) OR exit('No direct script access allowed');

DEFINE('KI_VERSION', '1.0.0');

require(BASEPATH . 'core/common' . EXT);
require(APPPATH . 'config/constants' . EXT);

set_error_handler('_exception_handler');

if (!is_php('5.3'))
{
    @set_magic_quotes_runtime(0);
}

if (isset($assign_to_config['subclass_prefix']) AND $assign_to_config['subclass_prefix'] != '')
{
    get_config(array('subclass_prefix' => $assign_to_config['subclass_prefix']));
}

if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0)
{
    @set_time_limit(300);
}

$BM = & load_class('Benchmark', 'core');
$BM->mark('total_execution_time_start');
$BM->mark('loading_time_base_classes_start');

$EXT = & load_class('Hooks', 'core');
$EXT->_call_hook('pre_system');

$CFG = & load_class('Config', 'core');

if (isset($assign_to_config))
{
    $CFG->_assign_to_config($assign_to_config);
}

$UNI = & load_class('Utf8', 'core');
$URI = & load_class('URI', 'core');

$RTR = & load_class('Router', 'core');
$RTR->_set_routing();

if (isset($routing))
{
    $RTR->_set_overrides($routing);
}

$OUT = & load_class('Output', 'core');

if ($EXT->_call_hook('cache_override') === FALSE)
{
    if ($OUT->_display_cache($CFG, $URI) == TRUE)
    {
	exit;
    }
}

$IN = & load_class('Input', 'core');
$LANG = & load_class('Lang', 'core');

require(BASEPATH . 'core/controller' . EXT);

if (file_exists(APPPATH . 'core/' . $CFG->config['subclass_prefix'] . 'controller' . EXT))
{
    require APPPATH . 'core/' . $CFG->config['subclass_prefix'] . 'controller' . EXT;
}

if (!file_exists(APPPATH . 'controllers/' . $RTR->fetch_directory() . $RTR->fetch_class() . EXT))
{
    show_error('Unable to load your default controller. Please make sure the controller specified in your Routes.php file is valid.');
}

include(APPPATH . 'controllers/' . $RTR->fetch_directory() . $RTR->fetch_class() . EXT);

$BM->mark('loading_time:_base_classes_end');

$class = $RTR->fetch_class();
$method = $RTR->fetch_method();

if (!class_exists($class) OR strncmp($method, '_', 1) == 0 OR in_array(strtolower($method), array_map('strtolower', get_class_methods('KI_CONTROLLER'))))
{
    show_404("{$class}/{$method}");
}

// controller
$EXT->_call_hook('pre_controller');

$BM->mark('controller_execution_time_( ' . $class . ' / ' . $method . ' )_start');

$class = strtoupper($class);
$KI = new $class();

$EXT->_call_hook('post_controller_constructor');

if (method_exists($KI, '_remap'))
{
    $KI->_remap($method, array_slice($URI->rsegments, 2));
}
else
{
    if (!in_array(strtolower($method), array_map('strtolower', get_class_methods($KI))))
    {
	show_404("{$class}/{$method}");
    }

    call_user_func_array(array(&$KI, $method), array_slice($URI->rsegments, 2));
}

$BM->mark('controller_execution_time_( ' . $class . ' / ' . $method . ' )_end');

$EXT->_call_hook('post_controller');
// end controller

if ($EXT->_call_hook('display_override') === FALSE)
{
    $OUT->_display();
}

$EXT->_call_hook('post_system');

if (class_exists('KI_DB') AND isset($KI->db))
{
    $KI->db->close();
}
?>