<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

class KI_LOADER
{
    private $_matchbox;

    function module_library($module, $library = '', $params = null)
    {
        return $this->library($library, $params, $module);
    }

    function module_model($module, $model, $name = '', $db_conn = false)
    {
        return $this->model($model, $name, $db_conn, $module);
    }

    function module_view($module, $view, $vars = array(), $return = false)
    {
        return $this->view($view, $vars, $return, $module);
    }

    function module_file($module, $path, $return = false)
    {
        return $this->file($path, $return, $module);
    }

    function module_helper($module, $helpers = array())
    {
        return $this->helper($helpers, $module);
    }
    
    function module_plugin($module, $plugins = array())
    {
        return $this->plugin($plugins, $module);
    }

    function module_script($module, $scripts = array())
    {
        return $this->script($scripts, $module);
    }

    function module_language($module, $file = array(), $lang = '')
    {
        return $this->language($file, $lang, $module);
    }

    function module_config($module, $file = '', $use_sections = false, $fail_gracefully = false)
    {
        return $this->config($file, $use_sections, $fail_gracefully, $module);
    }

	private $_ki_ob_level;
	private $_ki_view_path = '';
	private $_ki_is_php5 = FALSE;
	private $_ki_is_instance = FALSE;
	private $_ki_cached_vars = array();
	private $_ki_classes	 = array();
	private $_ki_loaded_files = array();
	private $_ki_models = array();
	private $_ki_helpers = array();
	private $_ki_plugins = array();
	private $_ki_varmap = array('unit_test' => 'unit', 'user_agent' => 'agent');

    function KI_LOADER()
    {
        $this->_matchbox = &load_class('Matchbox');

		$this->_ki_is_php5 = (floor(phpversion()) >= 5) ? TRUE : FALSE;
		$this->_ki_view_path = APPPATH.'views/';
		$this->_ki_ob_level = ob_get_level();
				
		log_message('debug', "Loader Class Initialized");
    }

    function library($library = '', $params = NULL)
    {
        if($library == '') return FALSE;

        $module = $this->_matchbox->argument(2);

        if(is_array($library))
        {
            foreach($library as $class)
            {
                $this->_ki_load_class($class, $params, $module);
            }
        }
        else $this->_ki_load_class($library, $params, $module);

        $this->_ki_assign_to_models();
    }

    function model($model, $name = '', $db_conn = FALSE)
    {
        if(is_array($model))
        {
            foreach($model as $babe)
            {
                $this->model($babe);
            }
            
            return;
        }

        if($model == '') return;

        if(strpos($model, '/') === FALSE)
        {
            $path = '';
        }
        else
        {
            $x = explode('/', $model);
            $model = end($x);
            unset($x[count($x) - 1]);
            $path = implode('/', $x).'/';
        }

        if($name == '') $name = $model;
        if(in_array($name, $this->_ki_models, TRUE)) return;

        $KI =& get_instance();
        
        if(isset($KI->$name))
        {
            show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
        }

        $model = strtolower($model);
        $module = $this->_matchbox->argument(3);

        if(!$filepath = $this->_matchbox->find('models/'.$path.$model.EXT, $module)) show_error('Unable to locate the model you have specified: ' . $model);

        if ($db_conn !== FALSE AND ! class_exists('KI_DB'))
        {
            if($db_conn === TRUE) $db_conn = '';

            $KI->load->database($db_conn, FALSE, TRUE);
        }

        if(!class_exists('Model')) load_class('Model', FALSE);

        require_once($filepath);

        $model = ucfirst($model);
        $KI->$name = new $model();
        $KI->$name->_assign_libraries();
        $this->_ki_models[] = $name;
    }

    function database($params = '', $return = FALSE, $active_record = FALSE)
    {
        $KI =& get_instance();
        
        if(class_exists('KI_DB') AND $return == FALSE AND $active_record == FALSE AND isset($KI->db) AND is_object($KI->db)) return FALSE;

        require_once(BASEPATH.'database/DB'.EXT);

        if($return === TRUE)
        {
            return DB($params, $active_record);
        }

        $KI->db = '';
        $KI->db =& DB($params, $active_record);
        $this->_ki_assign_to_models();
    }

    function dbutil()
    {
        if(!class_exists('KI_DB')) $this->database();

        $KI =& get_instance();
        $KI->load->dbforge();

        require_once(BASEPATH.'database/DB_utility'.EXT);
        require_once(BASEPATH.'database/drivers/'.$KI->db->dbdriver.'/'.$KI->db->dbdriver.'_utility'.EXT);
        
        $class = 'KI_DB_'.$KI->db->dbdriver.'_utility';
        $KI->dbutil =& new $class();
        $KI->load->_ki_assign_to_models();
    }

    function dbforge()
    {
        if(!class_exists('KI_DB')) $this->database();

        $KI =& get_instance();

        require_once(BASEPATH.'database/DB_forge'.EXT);
        require_once(BASEPATH.'database/drivers/'.$KI->db->dbdriver.'/'.$KI->db->dbdriver.'_forge'.EXT);
        
        $class = 'KI_DB_'.$KI->db->dbdriver.'_forge';
        $KI->dbforge = new $class();
    }

    function view($view, $vars = array(), $return = FALSE)
    {
        $module = $this->_matchbox->argument(3);
        return $this->_ki_load(array('_ki_view' => $view, '_ki_vars' => $this->_ki_object_to_array($vars), '_ki_return' => $return), $module);
    }

    function file($path, $return = FALSE)
    {
        $module = $this->_matchbox->argument(2);
        return $this->_ki_load(array('_ki_path' => $path, '_ki_return' => $return), $module);
    }

    function vars($vars = array(), $val = '')
    {
    	if($val != '' AND is_string($vars))
		{
			$vars = array($vars => $val);
		}
		
        $vars = $this->_ki_object_to_array($vars);

        if(is_array($vars) AND count($vars) > 0)
        {
            foreach($vars as $key => $val)
            {
                $this->_ki_cached_vars[$key] = $val;
            }
        }
    }

    function helper($helpers = array())
    {
        if(!is_array($helpers)) $helpers = array($helpers);

        foreach($helpers as $helper)
        {
            $helper = strtolower(str_replace(EXT, '', str_replace('_helper', '', $helper)).'_helper');

            if(isset($this->_ki_helpers[$helper])) continue;

            $module = $this->_matchbox->argument(1);

            if(($ext_helper = $this->_matchbox->find('helpers/'.config_item('subclass_prefix').$helper.EXT, $module)))
            {
                $base_helper = BASEPATH.'helpers/'.$helper.EXT;

                if(!file_exists($base_helper))
                {
                    show_error('Unable to load the requested file: helpers/'.$helper.EXT);
                }

                include_once($ext_helper);
                include_once($base_helper);
            }
            else if(($filepath = $this->_matchbox->find('helpers/'.$helper.EXT, $module, 2)))
            {
                include($filepath);
            }
            else show_error('Unable to load the requested file: helpers/'.$helper.EXT);

            $this->_ki_helpers[$helper] = TRUE;

        }

        log_message('debug', 'Helpers loaded: '.implode(', ', $helpers));
    }

    function helpers($helpers = array())
    {
        $this->helper($helpers);
    }

    function plugin($plugins = array())
    {
        if(!is_array($plugins)) $plugins = array($plugins);

        foreach($plugins as $plugin)
        {
            $plugin = strtolower(str_replace(EXT, '', str_replace('_pi', '', $plugin)).'_pi');

            if(isset($this->_ki_plugins[$plugin])) continue;

            $module = $this->_matchbox->argument(1);

            if(!$filepath = $this->_matchbox->find('plugins/'.$plugin.EXT, $module, 2))
            {
                show_error('Unable to load the requested file: plugins/'.$plugin.EXT);
            }

            include($filepath);

            $this->_ki_plugins[$plugin] = TRUE;
        }

        log_message('debug', 'Plugins loaded: '.implode(', ', $plugins));
    }

    function plugins($plugins = array())
    {
        $this->plugin($plugins);
    }

    function script($scripts = array())
    {
        if(!is_array($scripts)) $scripts = array($scripts);

        foreach($scripts as $script)
        {
            $script = strtolower(str_replace(EXT, '', $script));

            if(isset($this->_ki_scripts[$script])) continue;
            
            $module = $this->_matchbox->argument(1);

            if(!$filepath = $this->_matchbox->find('scripts/'.$script.EXT, $module, 2))
            {
                show_error('Unable to load the requested script: scripts/'.$script.EXT);
            }

            include($filepath);
        }

        log_message('debug', 'Scripts loaded: '.implode(', ', $scripts));
    }

    function language($file=array(), $lang='')
    {
        $KI =& get_instance();

        if(!is_array($file)) $file = array($file);

        foreach($file as $langfile)
        {
            $module = $this->_matchbox->argument(2);
            $KI->lang->load($langfile, $lang, false, $module);
        }
    }

    function scaffold_language($file = '', $lang = '', $return = FALSE)
    {
        $KI =& get_instance();
        return $KI->lang->load($file, $lang, $return);
    }

    function config($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
    {
        $KI =& get_instance();
        $module = $this->_matchbox->argument(3);
        $KI->config->load($file, $use_sections, $fail_gracefully, $module);
    }

    function scaffolding($table = '')
    {
        if($table === FALSE)
        {
            show_error('You must include the name of the table you would like to access when you initialize scaffolding');
        }

        $KI =& get_instance();
        $KI->_ki_scaffolding = TRUE;
        $KI->_ki_scaff_table = $table;
    }

    private function _ki_load($_ki_data)
    {
        foreach(array('_ki_view', '_ki_vars', '_ki_path', '_ki_return') as $_ki_val)
        {
            $$_ki_val = (!isset($_ki_data[$_ki_val])) ? FALSE : $_ki_data[$_ki_val];
        }

        if($_ki_path == '')
        {
            $_ki_ext  = pathinfo($_ki_view, PATHINFO_EXTENSION);
            $_ki_file = ($_ki_ext == '') ? $_ki_view.EXT : $_ki_view;
            $_ki_path = str_replace(APPPATH, '', $this->_ki_view_path).$_ki_file;
            $search   = 1;
        }
        else
        {
            $_ki_x    = explode('/', $_ki_path);
            $_ki_file = end($_ki_x);
            $search   = 3;
        }

        $module = $this->_matchbox->argument(1);

        if(!$_ki_path = $this->_matchbox->find($_ki_path, $module, $search))
        {
            show_error('Unable to load the requested file: '.$_ki_file);
        }

        if($this->_ki_is_instance())
        {
            $_ki_KI =& get_instance();
            
            foreach(get_object_vars($_ki_KI) as $_ki_key => $_ki_var)
            {
                if(!isset($this->$_ki_key))
                {
                    $this->$_ki_key =& $_ki_KI->$_ki_key;
                }
            }
        }

        if(is_array($_ki_vars))
        {
            $this->_ki_cached_vars = array_merge($this->_ki_cached_vars, $_ki_vars);
        }
        
        extract($this->_ki_cached_vars);

        ob_start();

        if((bool) @ini_get('short_open_tag') === FALSE AND config_item('rewrite_short_tags') == TRUE)
        {
            echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ki_path))).'<?php ');
        }
        else
        {
            include($_ki_path);
        }

        log_message('debug', 'File loaded: '.$_ki_path);

        if($_ki_return === TRUE)
        {
            $buffer = ob_get_contents();
            @ob_end_clean();
            return $buffer;
        }

        if(ob_get_level() > $this->_ki_ob_level + 1)
        {
            ob_end_flush();
        }
        else
        {
            global $OUT;
            
            $OUT->append_output(ob_get_contents());
            @ob_end_clean();
        }
    }

    private function _ki_load_class($class, $params = NULL)
    {
        $class = str_replace(EXT, '', $class);

        foreach(array(ucfirst($class), strtolower($class)) as $class)
        {
            $module = $this->_matchbox->argument(2);

            if(($subclass = $this->_matchbox->find('libraries/'.config_item('subclass_prefix').$class.EXT, $module)))
            {
                $baseclass = $this->_matchbox->find('libraries/'.$class.EXT, $module, 2);

                if(!file_exists($baseclass))
                {
                    log_message('error', "Unable to load the requested class: ".$class);
                    show_error("Unable to load the requested class: ".$class);
                }

                if(in_array($subclass, $this->_ki_classes))
                {
                    $is_duplicate = TRUE;
                    log_message('debug', $class." class already loaded. Second attempt ignored.");
                    return;
                }

                include($baseclass);
                include($subclass);
                
                $this->_ki_classes[] = $subclass;
                return $this->_ki_init_class($class, config_item('subclass_prefix'), $params, $module);
            }

            $is_duplicate = FALSE;

            if(($filepath = $this->_matchbox->find('libraries/'.$class.EXT, $module, 2)))
            {
                if(in_array($class, $this->_ki_classes))
                {
                    $is_duplicate = true;
                    log_message('debug', $class.' class already loaded. Second attempt ignored.');
                    return;
                }

                include($filepath);
                
                $this->_ki_classes[] = $class;
                return $this->_ki_init_class($class, '', $params, $module);
            }
        }

        if($is_duplicate == FALSE)
        {
            log_message('error', "Unable to load the requested class: ".$class);
            show_error("Unable to load the requested class: ".$class);
        }
    }

    private function _ki_init_class($class, $prefix = '', $config = FALSE)
    {
        $class = strtolower($class);
        $module = $this->_matchbox->argument(3);

        if($config === null)
        {
            if(($filepath = $this->_matchbox->find('config/'.$class.EXT, $module)))
            {
                include($filepath);
            }
        }

        if($prefix == '') $name = (class_exists('KI_'.$class)) ? 'KI_'.$class : $class;
        else $name = $prefix.$class;

        $classvar = (!isset($this->_ki_varmap[$class])) ? $class : $this->_ki_varmap[$class];
        $KI =& get_instance();
        
        if($config !== NULL) $KI->$classvar = new $name($config);
        else $KI->$classvar = new $name;
    }

    function _ki_autoloader()
    {
        $ki = &get_instance();
        $ki->matchbox = &load_class('Matchbox');

        include(APPPATH.'config/autoload'.EXT);

        if(!isset($autoload)) return FALSE;

        if(count($autoload['config']) > 0)
        {
            foreach($autoload['config'] as $key => $value)
            {
                if(is_string($key))
                {
                    if(is_array($value))
                    {
                        foreach($value as $config)
                        {
                            $ki->config->module_load($key, $config);
                        }
                    }
                    else $ki->config->module_load($key, $value);
                }
                else $ki->config->load($value);
            }
        }

        foreach(array('helper', 'plugin', 'script', 'language') as $type)
        {
            if(isset($autoload[$type]) AND count($autoload[$type]) > 0)
            {
                foreach($autoload[$type] as $key => $value)
                {
                    if(is_string($key))
                    {
                        $this->{'module_'.$type}($key, $value);
                    }
                    else $this->$type($value);
                }
            }
        }

        if(!isset($autoload['libraries'])) $autoload['libraries'] = $autoload['core'];
        if(isset($autoload['libraries']) AND count($autoload['libraries']) > 0)
        {
            if(in_array('database', $autoload['libraries']))
            {
                $this->database();
                $autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
            }

            if(in_array('scaffolding', $autoload['libraries']))
            {
                $this->scaffolding();
                $autoload['libraries'] = array_diff($autoload['libraries'], array('scaffolding'));
            }

            foreach($autoload['libraries'] as $key => $value)
            {
                if(is_string($key)) $this->module_library($key, $value);
                else $this->library($value);
            }
        }

        if(isset($autoload['model']))
        {
            foreach($autoload['model'] as $key => $value)
            {
                if(is_string($key)) $this->module_model($key, $value);
                else $this->model($value);
            }
        }
    }

    private function _ki_assign_to_models()
    {
        if(count($this->_ki_models) == 0) return;
        
        if($this->_ki_is_instance())
        {
            $KI =& get_instance();
            
            foreach($this->_ki_models as $model)
            {
                $KI->$model->_assign_libraries();
            }
        }
        else
        {
            foreach($this->_ki_models as $model)
            {
                $this->$model->_assign_libraries();
            }
        }
    }

    private function _ki_object_to_array($object)
    {
        return (is_object($object)) ? get_object_vars($object) : $object;
    }

    private function _ki_is_instance()
    {
        if($this->_ki_is_php5 == TRUE) return TRUE;

        global $KI;
        
        return (is_object($KI)) ? TRUE : FALSE;
    }
}
?>