<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

class KI_ROUTER
{
    private $_called = 0;
    private $_fail_gracefully = false;
    private $_matchbox;

    public $config;
    public $routes = array();
    public $error_routes = array();
    public $class = '';
    public $method = 'index';
    public $directory = '';
    public $uri_protocol = 'auto';
    public $default_controller;
    public $scaffolding_request = FALSE;

    function KI_ROUTER()
    {
        $this->_matchbox = &load_class('Matchbox');
        $this->config =& load_class('Config');
        $this->uri =& load_class('URI');
        $this->_set_routing();
        log_message('debug', "Router Class Initialized");
    }

    private function _set_routing()
    {
        if($this->config->item('enable_query_strings') === TRUE AND isset($_GET[$this->config->item('controller_trigger')]))
        {
            $this->set_class(trim($this->uri->_filter_uri($_GET[$this->config->item('controller_trigger')])));

            if(isset($_GET[$this->config->item('function_trigger')]))
            {
                $this->set_method(trim($this->uri->_filter_uri($_GET[$this->config->item('function_trigger')])));
            }

            return;
        }

        @include(APPPATH.'config/routes'.EXT);
        
        $this->routes = (!isset($route) OR !is_array($route)) ? array() : $route;
        unset($route);
        
        $this->default_controller = (!isset($this->routes['default_controller']) OR $this->routes['default_controller'] == '') ? FALSE : strtolower($this->routes['default_controller']);
        $this->uri->_fetch_uri_string();

        if($this->uri->uri_string == '')
        {
            if($this->default_controller === FALSE)
            {
                show_error("Unable to determine what should be displayed. A default route has not been specified in the routing file.");
            }

            $segments = explode('/', $this->default_controller);
            $this->_fail_gracefully = TRUE;
            $this->_set_request($segments);
            $this->uri->_reindex_segments();

            log_message('debug', "No URI present. Default controller set.");
            return;
        }
        
        unset($this->routes['default_controller']);
        $this->uri->_remove_url_suffix();
        $this->uri->_explode_segments();
        $this->_parse_routes();
        $this->uri->_reindex_segments();
    }

    function _set_request($segments = array())
    {
        $segments = $this->_validate_request($segments);

        if(count($segments) == 0) return;

        $this->set_class($segments[0]);

        if(isset($segments[1]))
        {
            if($this->routes['scaffolding_trigger'] == $segments[1] AND $segments[1] != '_ki_scaffolding')
            {
                $this->scaffolding_request = TRUE;
                unset($this->routes['scaffolding_trigger']);
            }
            else
            {
                $this->set_method($segments[1]);
            }
        }
        else
        {
            $segments[1] = 'index';
        }

        $this->uri->rsegments = $segments;
    }

    private function _validate_request($segments)
    {
        foreach($this->_matchbox->directory_array() as $directory)
        {
            if(count($segments) > 1 && $segments[0] !== $segments[1] && file_exists(APPPATH.$directory.'/'.$segments[0].'/controllers/'.$segments[1].EXT))
            {
                $this->_matchbox->set_directory($directory);
                $this->_matchbox->set_module($segments[0]);
                $segments = array_slice($segments, 1);
                return $segments;
            }

            if(count($segments) > 2 && file_exists(APPPATH.$directory.'/'.$segments[0].'/controllers/'.$segments[1].'/'.$segments[2].EXT))
            {
                $this->_matchbox->set_directory($directory);
                $this->_matchbox->set_module($segments[0]);
                $this->set_directory($segments[1]);
                $segments = array_slice($segments, 2);
                return $segments;
            }

            if(count($segments) > 1 && file_exists(APPPATH.$directory.'/'.$segments[0].'/controllers/'.$segments[1].'/'.$segments[1].EXT))
            {
                $this->_matchbox->set_directory($directory);
                $this->_matchbox->set_module($segments[0]);
                $this->set_directory($segments[1]);
                $segments = array_slice($segments, 1);
                return $segments;
            }

            if(file_exists(APPPATH.$directory.'/'.$segments[0].'/controllers/'.$segments[0].EXT))
            {
                $this->_matchbox->set_directory($directory);
                $this->_matchbox->set_module($segments[0]);
                return $segments;
            }
        }

        if(file_exists(APPPATH.'controllers/'.$segments[0].EXT)) return $segments;

        if(is_dir(APPPATH.'controllers/'.$segments[0]))
        {
            $this->set_directory($segments[0]);
            $segments = array_slice($segments, 1);

            if(count($segments) > 0)
            {
                if(!file_exists(APPPATH.'controllers/'.$this->fetch_directory().$segments[0].EXT))
                {
                    show_404();
                }
            }
            else
            {
                $this->set_class($this->default_controller);
                $this->set_method('index');

                if(!file_exists(APPPATH.'controllers/'.$this->fetch_directory().$this->default_controller.EXT))
                {
                    $this->directory = '';
                    return array();
                }

            }

            return $segments;
        }

        if($this->_fail_gracefully) return array();
      
        show_404();
    }

    function _parse_routes()
    {
        if(count($this->routes) == 1)
        {
            $this->_set_request($this->uri->segments);
            return;
        }

        $uri = implode('/', $this->uri->segments);

        if(isset($this->routes[$uri]))
        {
            $this->_set_request(explode('/', $this->routes[$uri]));
            return;
        }

        foreach($this->routes as $key => $val)
        {
            $key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

            if(preg_match('#^'.$key.'$#', $uri))
            {
                if(strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE)
                {
                    $val = preg_replace('#^'.$key.'$#', $val, $uri);
                }

                $this->_set_request(explode('/', $val));
                return;
            }
        }

        $this->_set_request($this->uri->segments);
    }

    function set_class($class)
    {
        $this->class = $class;
    }

    function fetch_class()
    {
        return $this->class;
    }

    function set_method($method)
    {
        $this->method = $method;
    }

    function fetch_method()
    {
        if($this->method == $this->fetch_class()) return 'index';

        return $this->method;
    }

    function set_directory($dir)
    {
        $this->directory = $dir.'/';
    }

    function fetch_directory()
    {
        if($this->_called < 2)
        {
            $this->_called += 1;

            return '../'.$this->_matchbox->fetch_directory().$this->_matchbox->fetch_module().'controllers/'.$this->directory;
        }

        return $this->directory;
    }
}
?>