<?
class Ktmpl_Autoloader
{
    static public function register()
    {
        ini_set('unserialize_callback_func', 'spl_autoload_call');
        spl_autoload_register(array(new self, 'autoload'));
    }

    static public function autoload($class)
    {
        if (0 !== strpos($class, 'Ktmpl')) return;
        if (file_exists($file = dirname(__FILE__).'/../'.str_replace('_', '/', $class).'.php')) require $file;
    }
}