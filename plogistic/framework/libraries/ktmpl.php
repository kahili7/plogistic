<?

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class KTMPL
{

    private $obj = false;

    function __construct()
    {
        $this->obj = &get_instance();

        require_once BASEPATH . 'libraries/ktmpl/Autoloader.php';

        Ktmpl_Autoloader::register();
    }

    function renderString($template, $data=array())
    {
        $loader = new Ktmpl_Loader_String();
        $ktmpl = new Ktmpl_Environment($loader);
        $template = $ktmpl->loadTemplate($template);
        $out = $template->render($data);
        return $out;
    }

    function _render($template, $data=array(), $return=false)
    {
        $loader = new Ktmpl_Loader_Filesystem('application/views/');
        $ktmpl = new Ktmpl_Environment($loader, array(
                    'auto_reload' => $this->obj->config->item('auto_reload'),
                    'debug' => $this->obj->config->item('debug'),
                    'strict_variables' => $this->obj->config->item('debug'),
                    'cache' => $this->obj->config->item('cache')
                ));
        $escaper = new Ktmpl_Extension_Escaper(true);
        $ktmpl->addExtension($escaper);

        require_once(BASEPATH . 'libraries/ktmpl/config/extends.php');

        $ktmpl->addExtension(new New_Ktmpl_Extension());
        $template = $ktmpl->loadTemplate($template);
        $out = $template->render($data + array('obj' => $this->obj));
        return $out;
    }

    function view($template, $data=array(), $return=false)
    {
        $out = $this->_render($template, $data, $return);

        if (!$return)
        {
            $this->obj->output->set_output($out);
        }

        return $out;
    }

}