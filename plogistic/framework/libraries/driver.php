<?if(!defined('BASEPATH')) exit('No direct script access allowed');

class KI_DRIVER_LIBRARY 
{
	protected $valid_drivers	= array();
	protected static $lib_name;

	function __get($child)
	{
		if (!isset($this->lib_name))
		{
			$this->lib_name = get_class($this);
		}

		$child_class = $this->lib_name.'_'.$child;

		if (in_array(strtolower($child_class), array_map('strtolower', $this->valid_drivers)))
		{
			if (!class_exists($child_class))
			{
				foreach (array(APPPATH, BASEPATH) as $path)
				{
					foreach (array(ucfirst($this->lib_name), strtolower($this->lib_name)) as $lib)
					{
						foreach (array(ucfirst($child_class), strtolower($child_class)) as $class)
						{
							$filepath = $path.'libraries/'.$this->lib_name.'/drivers/'.$child_class.EXT;

							if (file_exists($filepath))
							{
								include_once $filepath;
								break;
							}
						}
					}
				}

				if (!class_exists($child_class))
				{
					log_message('error', "Unable to load the requested driver: ".$child_class);
					show_error("Unable to load the requested driver: ".$child_class);
				}
			}

			$obj = new $child_class;
			$obj->decorate($this);
			$this->$child = $obj;
			return $this->$child;
		}

		log_message('error', "Invalid driver requested: ".$child_class);
		show_error("Invalid driver requested: ".$child_class);
	}
}

class KI_DRIVER 
{
	protected $parent;

	private $methods = array();
	private $properties = array();

	private static $reflections = array();

	public function decorate($parent)
	{
		$this->parent = $parent;
		$class_name = get_class($parent);

		if (!isset(self::$reflections[$class_name]))
		{
			$r = new ReflectionObject($parent);

			foreach ($r->getMethods() as $method)
			{
				if ($method->isPublic())
				{
					$this->methods[] = $method->getName();
				}
			}

			foreach($r->getProperties() as $prop)
			{
				if ($prop->isPublic())
				{
					$this->properties[] = $prop->getName();
				}
			}

			self::$reflections[$class_name] = array($this->methods, $this->properties);
		}
		else
		{
			list($this->methods, $this->properties) = self::$reflections[$class_name];
		}
	}

	public function __call($method, $args = array())
	{
		if (in_array($method, $this->methods))
		{
			return call_user_func_array(array($this->parent, $method), $args);
		}

		$trace = debug_backtrace();
		_exception_handler(E_ERROR, "No such method '{$method}'", $trace[1]['file'], $trace[1]['line']);
		exit;
	}

	public function __get($var)
	{
		if (in_array($var, $this->properties))
		{
			return $this->parent->$var;
		}
	}

	public function __set($var, $val)
	{
		if (in_array($var, $this->properties))
		{
			$this->parent->$var = $val;
		}
	}
}