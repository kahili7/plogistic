<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

class KI_LOADER
{

    var $_ki_ob_level;
    var $_ki_view_path = '';
    var $_ki_library_paths = array();
    var $_ki_model_paths = array();
    var $_ki_helper_paths = array();
    var $_base_classes = array(); // Set by the controller class
    var $_ki_cached_vars = array();
    var $_ki_classes = array();
    var $_ki_loaded_files = array();
    var $_ki_models = array();
    var $_ki_helpers = array();
    var $_ki_varmap = array('unit_test' => 'unit', 'user_agent' => 'agent');

    function __construct()
    {
	$this->_ki_view_path = APPPATH . 'views/';
	$this->_ki_ob_level = ob_get_level();
	$this->_ki_library_paths = array(APPPATH, BASEPATH);
	$this->_ki_helper_paths = array(APPPATH, BASEPATH);
	$this->_ki_model_paths = array(APPPATH);

	log_message('debug', "Loader Class Initialized");
    }

    function library($library = '', $params = NULL, $object_name = NULL)
    {
	if (is_array($library))
	{
	    foreach ($library as $read)
	    {
		$this->library($read);
	    }

	    return;
	}

	if ($library == '' OR isset($this->_base_classes[$library]))
	{
	    return FALSE;
	}

	if (!is_null($params) && !is_array($params))
	{
	    $params = NULL;
	}

	if (is_array($library))
	{
	    foreach ($library as $class)
	    {
		$this->_ki_load_class($class, $params, $object_name);
	    }
	}
	else
	{
	    $this->_ki_load_class($library, $params, $object_name);
	}
    }

    function model($model, $name = '', $db_conn = FALSE)
    {
	if (is_array($model))
	{
	    foreach ($model as $babe)
	    {
		$this->model($babe);
	    }

	    return;
	}

	if ($model == '')
	    return;

	$path = '';

	if (($last_slash = strrpos($model, '/')) !== FALSE)
	{
	    $path = substr($model, 0, $last_slash + 1);
	    $model = substr($model, $last_slash + 1);
	}

	if ($name == '')
	    $name = $model;

	if (in_array($name, $this->_ki_models, TRUE))
	    return;

	$KI = & get_instance();

	if (isset($KI->$name))
	{
	    show_error('The model name you are loading is the name of a resource that is already being used: ' . $name);
	}

	$model = strtolower($model);

	foreach ($this->_ki_model_paths as $mod_path)
	{
	    if (!file_exists($mod_path . 'models/' . $path . $model . EXT))
		continue;

	    if ($db_conn !== FALSE AND !class_exists('KI_DB'))
	    {
		if ($db_conn === TRUE)
		    $db_conn = '';

		$KI->load->database($db_conn, FALSE, TRUE);
	    }

	    if (!class_exists('KI_Model'))
	    {
		load_class('Model', 'core');
	    }

	    require_once($mod_path . 'models/' . $path . $model . EXT);

	    $model = ucfirst($model);
	    $KI->$name = new $model();
	    $this->_ki_models[] = $name;
	    return;
	}

	show_error('Unable to locate the model you have specified: ' . $model);
    }

    function database($params = '', $return = FALSE, $active_record = NULL)
    {
	$KI = & get_instance();

	if (class_exists('KI_DB') AND $return == FALSE AND $active_record == NULL AND isset($KI->db) AND is_object($KI->db))
	{
	    return FALSE;
	}

	require_once(BASEPATH . 'database/db' . EXT);

	if ($return === TRUE)
	{
	    return DB($params, $active_record);
	}

	$KI->db = '';
	$KI->db = & DB($params, $active_record);
    }

    function dbutil()
    {
	if (!class_exists('KI_DB'))
	    $this->database();

	$KI = & get_instance();
	$KI->load->dbforge();

	require_once(BASEPATH . 'database/db_utility' . EXT);
	require_once(BASEPATH . 'database/drivers/' . $KI->db->dbdriver . '/' . $KI->db->dbdriver . '_utility' . EXT);

	$class = strtoupper('KI_DB_' . $KI->db->dbdriver . '_utility');
	$KI->dbutil = & instantiate_class(new $class());
    }

    function dbforge()
    {
	if (!class_exists('KI_DB'))
	    $this->database();

	$KI = & get_instance();

	require_once(BASEPATH . 'database/db_forge' . EXT);
	require_once(BASEPATH . 'database/drivers/' . $KI->db->dbdriver . '/' . $KI->db->dbdriver . '_forge' . EXT);

	$class = strtoupper('KI_DB_' . $KI->db->dbdriver . '_forge');
	$KI->dbforge = new $class();
    }

    function view($view, $vars=array(), $return=FALSE)
    {
	return $this->_ki_load(array('_ki_view' => $view, '_ki_vars' => $this->_ki_object_to_array($vars), '_ki_return' => $return));
    }

    function file($path, $return = FALSE)
    {
	return $this->_ki_load(array('_ki_path' => $path, '_ki_return' => $return));
    }

    function vars($vars = array(), $val = '')
    {
	if ($val != '' AND is_string($vars))
	{
	    $vars = array($vars => $val);
	}

	$vars = $this->_ki_object_to_array($vars);


	if (is_array($vars) AND count($vars) > 0)
	{
	    foreach ($vars as $key => $val)
	    {
		$this->_ki_cached_vars[$key] = $val;
	    }
	}
    }

    function helper($helpers=array())
    {
	foreach ($this->_ki_prep_filename($helpers, '_helper') as $helper)
	{
	    if (isset($this->_ki_helpers[$helper]))
		continue;

	    $ext_helper = APPPATH . 'helpers/' . config_item('subclass_prefix') . $helper . EXT;

	    if (file_exists($ext_helper))
	    {
		$base_helper = BASEPATH . 'helpers/' . $helper . EXT;

		if (!file_exists($base_helper))
		{
		    show_error('Unable to load the requested file: helpers/' . $helper . EXT);
		}

		include_once($ext_helper);
		include_once($base_helper);

		$this->_ki_helpers[$helper] = TRUE;
		log_message('debug', 'Helper loaded: ' . $helper);
		continue;
	    }

	    foreach ($this->_ki_helper_paths as $path)
	    {
		if (file_exists($path . 'helpers/' . $helper . EXT))
		{
		    include_once($path . 'helpers/' . $helper . EXT);

		    $this->_ki_helpers[$helper] = TRUE;
		    log_message('debug', 'Helper loaded: ' . $helper);
		    break;
		}
	    }

	    if (!isset($this->_ki_helpers[$helper]))
	    {
		show_error('Unable to load the requested file: helpers/' . $helper . EXT);
	    }
	}
    }

    function helpers($helpers=array())
    {
	$this->helper($helpers);
    }

    function language($file=array(), $lang='')
    {
	$KI = & get_instance();

	if (!is_array($file))
	{
	    $file = array($file);
	}

	foreach ($file as $langfile)
	{
	    $KI->lang->load($langfile, $lang);
	}
    }

    function config($file='', $use_sections=FALSE, $fail_gracefully=FALSE)
    {
	$KI = & get_instance();
	$KI->config->load($file, $use_sections, $fail_gracefully);
    }

    function driver($library = '', $params = NULL, $object_name = NULL)
    {
	if (!class_exists('KI_DRIVER_LIBRARY'))
	{
	    require BASEPATH . 'libraries/driver' . EXT;
	}

	if (!strpos($library, '/'))
	{
	    $library = ucfirst($library) . '/' . $library;
	}

	return $this->library($library, $params, $object_name);
    }

    function add_package_path($path)
    {
	$path = rtrim($path, '/') . '/';

	array_unshift($this->_ki_library_paths, $path);
	array_unshift($this->_ki_model_paths, $path);
	array_unshift($this->_ki_helper_paths, $path);

	$config = & $this->_ki_get_component('config');
	array_unshift($config->_config_paths, $path);
    }

    function get_package_paths($include_base = FALSE)
    {
	return $include_base === TRUE ? $this->_ki_library_paths : $this->_ki_model_paths;
    }

    function remove_package_path($path = '', $remove_config_path = TRUE)
    {
	$config = & $this->_ki_get_component('config');

	if ($path == '')
	{
	    $void = array_shift($this->_ki_library_paths);
	    $void = array_shift($this->_ki_model_paths);
	    $void = array_shift($this->_ki_helper_paths);
	    $void = array_shift($config->_config_paths);
	}
	else
	{
	    $path = rtrim($path, '/') . '/';

	    foreach (array('_ki_library_paths', '_ki_model_paths', '_ki_helper_paths') as $var)
	    {
		if (($key = array_search($path, $this->{$var})) !== FALSE)
		{
		    unset($this->{$var}[$key]);
		}
	    }

	    if (($key = array_search($path, $config->_config_paths)) !== FALSE)
	    {
		unset($config->_config_paths[$key]);
	    }
	}

	$this->_ki_library_paths = array_unique(array_merge($this->_ki_library_paths, array(APPPATH, BASEPATH)));
	$this->_ki_helper_paths = array_unique(array_merge($this->_ki_helper_paths, array(APPPATH, BASEPATH)));
	$this->_ki_model_paths = array_unique(array_merge($this->_ki_model_paths, array(APPPATH)));
	$config->_config_paths = array_unique(array_merge($config->_config_paths, array(APPPATH)));
    }

    function _ki_load($_ki_data)
    {
	foreach (array('_ki_view', '_ki_vars', '_ki_path', '_ki_return') as $_ki_val)
	{
	    $$_ki_val = (!isset($_ki_data[$_ki_val])) ? FALSE : $_ki_data[$_ki_val];
	}

	if ($_ki_path == '')
	{
	    $_ki_ext = pathinfo($_ki_view, PATHINFO_EXTENSION);
	    $_ki_file = ($_ki_ext == '') ? $_ki_view . EXT : $_ki_view;
	    $_ki_path = $this->_ki_view_path . $_ki_file;
	}
	else
	{
	    $_ki_x = explode('/', $_ki_path);
	    $_ki_file = end($_ki_x);
	}

	if (!file_exists($_ki_path))
	{
	    show_error('Unable to load the requested file: ' . $_ki_file);
	}

	if ($this->_ki_is_instance())
	{
	    $_ki_KI = & get_instance();

	    foreach (get_object_vars($_ki_KI) as $_ki_key => $_ki_var)
	    {
		if (!isset($this->$_ki_key))
		{
		    $this->$_ki_key = & $_ki_KI->$_ki_key;
		}
	    }
	}

	if (is_array($_ki_vars))
	{
	    $this->_ki_cached_vars = array_merge($this->_ki_cached_vars, $_ki_vars);
	}

	extract($this->_ki_cached_vars);

	ob_start();

	if ((bool) @ini_get('short_open_tag') === FALSE AND config_item('rewrite_short_tags') == TRUE)
	{
	    print eval('?>' . preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?print ', file_get_contents($_ki_path))));
	}
	else
	{
	    include($_ki_path);
	}

	log_message('debug', 'File loaded: ' . $_ki_path);

	if ($_ki_return === TRUE)
	{
	    $buffer = ob_get_contents();
	    @ob_end_clean();
	    return $buffer;
	}

	if (ob_get_level() > $this->_ki_ob_level + 1)
	{
	    ob_end_flush();
	}
	else
	{
	    $_ki_KI->output->append_output(ob_get_contents());
	    @ob_end_clean();
	}

	return "";
    }

    function _ki_load_class($class, $params=NULL, $object_name=NULL)
    {
	$class = strtolower(str_replace(EXT, '', trim($class, '/')));
	$subdir = '';

	if (($last_slash = strrpos($class, '/')) !== FALSE)
	{
	    $subdir = substr($class, 0, $last_slash + 1);
	    $class = substr($class, $last_slash + 1);
	}

	foreach (array($class, $class) as $class)
	{
	    $subclass = APPPATH . 'libraries/' . $subdir . config_item('subclass_prefix') . $class . EXT;

	    if (file_exists($subclass))
	    {
		$baseclass = BASEPATH . 'libraries/' . $class . EXT;

		if (!file_exists($baseclass))
		{
		    log_message('error', "Unable to load the requested class: " . $class);
		    show_error("Unable to load the requested class: " . $class);
		}

		if (in_array($subclass, $this->_ki_loaded_files))
		{
		    if (!is_null($object_name))
		    {
			$KI = & get_instance();

			if (!isset($KI->$object_name))
			{
			    return $this->_ki_init_class($class, config_item('subclass_prefix'), $params, $object_name);
			}
		    }

		    $is_duplicate = TRUE;
		    log_message('debug', $class . " class already loaded. Second attempt ignored.");
		    return;
		}

		include_once($baseclass);
		include_once($subclass);

		$this->_ki_loaded_files[] = $subclass;
		return $this->_ki_init_class($class, config_item('subclass_prefix'), $params, $object_name);
	    }

	    $is_duplicate = FALSE;

	    foreach ($this->_ki_library_paths as $path)
	    {
		$filepath = $path . 'libraries/' . $subdir . $class . EXT;

		if (!file_exists($filepath))
		    continue;

		if (in_array($filepath, $this->_ki_loaded_files))
		{
		    if (!is_null($object_name))
		    {
			$KI = & get_instance();

			if (!isset($KI->$object_name))
			{
			    return $this->_ki_init_class($class, '', $params, $object_name);
			}
		    }

		    $is_duplicate = TRUE;
		    log_message('debug', $class . " class already loaded. Second attempt ignored.");
		    return;
		}

		include_once($filepath);

		$this->_ki_loaded_files[] = $filepath;
		return $this->_ki_init_class($class, '', $params, $object_name);
	    }
	}

	if ($subdir == '')
	{
	    $path = strtolower($class) . '/' . $class;
	    return $this->_ki_load_class($path, $params);
	}

	if ($is_duplicate == FALSE)
	{
	    log_message('error', "Unable to load the requested class: " . $class);
	    show_error("Unable to load the requested class: " . $class);
	}
    }

    function _ki_init_class($class, $prefix='', $config=FALSE, $object_name=NULL)
    {
	if ($config === NULL)
	{
	    $config_component = $this->_ki_get_component('config');

	    if (is_array($config_component->_config_paths))
	    {
		foreach ($config_component->_config_paths as $path)
		{
		    if (file_exists($path . 'config/' . strtolower($class) . EXT))
		    {
			include_once($path . 'config/' . strtolower($class) . EXT);
			break;
		    }
		    elseif (file_exists($path . 'config/' . strtolower($class) . EXT))
		    {
			include_once($path . 'config/' . strtolower($class) . EXT);
			break;
		    }
		}
	    }
	}

	if ($prefix == '')
	{
	    if (class_exists('KI_' . $class))
	    {
		$name = 'KI_' . $class;
	    }
	    elseif (class_exists(config_item('subclass_prefix') . $class))
	    {
		$name = config_item('subclass_prefix') . $class;
	    }
	    else
	    {
		$name = $class;
	    }
	}
	else
	{
	    $name = $prefix . $class;
	}

	$name = strtoupper($name);

	if (!class_exists($name))
	{
	    log_message('error', "Non-existent class: " . $name);
	    show_error("Non-existent class: " . $class);
	}

	$class = strtolower($class);

	if (is_null($object_name))
	{
	    $classvar = (!isset($this->_ki_varmap[$class])) ? $class : $this->_ki_varmap[$class];
	}
	else
	{
	    $classvar = $object_name;
	}

	$this->_ki_classes[$class] = $classvar;
	$KI = & get_instance();

	if ($config !== NULL)
	{
	    $KI->$classvar = new $name($config);
	}
	else
	{
	    $KI->$classvar = new $name;
	}
    }

    function _ki_autoloader()
    {
	include_once(APPPATH . 'config/autoload' . EXT);

	if (!isset($autoload))
	    return FALSE;

	if (isset($autoload['packages']))
	{
	    foreach ($autoload['packages'] as $package_path)
	    {
		$this->add_package_path($package_path);
	    }
	}

	if (count($autoload['config']) > 0)
	{
	    $KI = & get_instance();

	    foreach ($autoload['config'] as $key => $val)
	    {
		$KI->config->load($val);
	    }
	}

	foreach (array('helper', 'language') as $type)
	{
	    if (isset($autoload[$type]) AND count($autoload[$type]) > 0)
	    {
		$this->$type($autoload[$type]);
	    }
	}

	if (!isset($autoload['libraries']) AND isset($autoload['core']))
	{
	    $autoload['libraries'] = $autoload['core'];
	}

	if (isset($autoload['libraries']) AND count($autoload['libraries']) > 0)
	{
	    if (in_array('database', $autoload['libraries']))
	    {
		$this->database();
		$autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
	    }

	    foreach ($autoload['libraries'] as $item)
	    {
		$this->library($item);
	    }
	}

	if (isset($autoload['model']))
	{
	    $this->model($autoload['model']);
	}
    }

    function _ki_object_to_array($object)
    {
	return (is_object($object)) ? get_object_vars($object) : $object;
    }

    function _ki_is_instance()
    {
	if (is_php('5.0.0') == TRUE)
	{
	    return TRUE;
	}

	global $KI;
	return (is_object($KI)) ? TRUE : FALSE;
    }

    function &_ki_get_component($component)
    {
	$KI = & get_instance();
	return $KI->$component;
    }

    function _ki_prep_filename($filename, $extension)
    {
	if (!is_array($filename))
	{
	    return array(strtolower(str_replace(EXT, '', str_replace($extension, '', $filename)) . $extension));
	}
	else
	{
	    foreach ($filename as $key => $val)
	    {
		$filename[$key] = strtolower(str_replace(EXT, '', str_replace($extension, '', $val)) . $extension);
	    }

	    return $filename;
	}
    }

}

?>