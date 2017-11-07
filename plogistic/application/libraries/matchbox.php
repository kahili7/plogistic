<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

DEFINE('MATCHBOX_VERSION', '0.9.3');

class KI_MATCHBOX
{
    private $_callers = array('Loader', 'Matchbox', 'MY_Config', 'MY_Language', 'Parser');
    private $_directories = array('modules');
    private $_directory = '';
    private $_module = '';

    function KI_MATCHBOX()
    {
        @include(APPPATH.'config/matchbox'.EXT);

        if(isset($config))
        {
            $this->_callers = array_merge($this->_callers, (!is_array($config['callers']) ? array() : $config['callers']));
            $this->_directories = (!is_array($config['directories'])) ? $this->_directories : $config['directories'];
        }
        else log_message('error', 'Matchbox Config File Not Found');

        log_message('debug', 'Matchbox Class Initialized');
    }

    function find($resource, $module = '', $search = 1)
    {
        log_message('debug', '---Matchbox---');
        log_message('debug', 'Finding: '.$resource);

        $directories = array();

        if($module !== '')
        {
            foreach($this->directory_array() as $directory)
            {
                $directories[] = APPPATH.$directory.'/'.$module.'/';
            }
        }
        else
        {
            $caller = $this->detect_caller();

            foreach($this->directory_array() as $directory)
            {
                $directories[] = APPPATH.$directory.'/'.$caller.'/';
            }

            if($search == 3)
            {
                $directories[] = '';
            }
            else
            {
                $directories[] = APPPATH;

                if($search == 2) $directories[] = BASEPATH;
            }
        }

        foreach($directories as $directory)
        {
            $filepath = $directory.$resource;

            log_message('debug', 'Looking in: '.$filepath);

            if(file_exists($filepath))
            {
                log_message('debug', 'Found');
                log_message('debug', '--------------');
                return $filepath;
            }
        }

        log_message('debug', 'Not found');
        log_message('debug', '--------------');
        return false;
    }

    function detect_caller()
    {
        $callers = array();
        $directories = array();
        $traces = debug_backtrace();

        foreach($this->caller_array() as $caller)
        {
            $callers[] = $this->_swap_separators($caller, true);
        }

        $search = '/(?:'.implode('|', $callers).')'.EXT.'$/i';

        foreach($traces as $trace)
        {
            $filepath = $this->_swap_separators($trace['file']);

            if(!preg_match($search, $filepath)) break;
        }

        foreach($this->directory_array() as $directory)
        {
            $directories[] = $this->_swap_separators(realpath(APPPATH.$directory), true);
        }

        $search = '/^(?:'.implode('|', $directories).')\/(.+?)\//i';

        if(preg_match($search, $filepath, $matches))
        {
            log_message('debug', 'Calling module: '.$matches[1]);
            return $matches[1];
        }

        log_message('debug', 'No valid caller');
        return '';
    }

    function argument($argument)
    {
        $traces = debug_backtrace();

        if(isset($traces[1]['args'][$argument]))
        {
            return $traces[1]['args'][$argument];
        }

        return '';
    }
    
    function caller_array()
    {
        return $this->_callers;
    }

    function directory_array()
    {
        return $this->_directories;
    }

    function set_directory($directory)
    {
        $this->_directory = $directory.'/';
    }

    function fetch_directory()
    {
        return $this->_directory;
    }

    function set_module($module)
    {
        $this->_module = $module.'/';
    }

    function fetch_module()
    {
        return $this->_module;
    }

    private function _swap_separators($path, $search = false)
    {
        $path = strtr($path, '\\', '/');

        if($search) $path = str_replace(array('/', '|'), array('\/', '\|'), $path);

        return $path;
    }
}
?>