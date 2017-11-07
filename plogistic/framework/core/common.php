<?

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function is_php($version = '5.0.0')
{
    static $_is_php;

    $version = (string) $version;

    if (!isset($_is_php[$version]))
    {
        $_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
    }

    return $_is_php[$version];
}

function is_really_writable($file)
{
    if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
    {
        return is_writable($file);
    }

    if (is_dir($file))
    {
        $file = rtrim($file, '/') . '/' . md5(rand(1, 100));

        if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
        {
            return FALSE;
        }

        fclose($fp);
        @chmod($file, DIR_WRITE_MODE);
        @unlink($file);
        return TRUE;
    }
    else if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
    {
        return FALSE;
    }

    fclose($fp);
    return TRUE;
}

function &load_class($class, $directory = 'libraries', $prefix = 'KI_')
{
    static $_classes = array();

    if (isset($_classes[$class]))
        return $_classes[$class];

    $name = FALSE;
    $class = strtolower($class);

    foreach (array(BASEPATH, APPPATH) as $path)
    {
        if (file_exists($path . $directory . '/' . $class . EXT))
        {
            $name = strtoupper($prefix . $class);

            if (class_exists($name) === FALSE)
            {
                require($path . $directory . '/' . $class . EXT);
            }

            break;
        }
    }

    if (file_exists(APPPATH . $directory . '/' . config_item('subclass_prefix') . $class . EXT))
    {
        $name = strtoupper(config_item('subclass_prefix') . $class);

        if (class_exists($name) === FALSE)
        {
            require(APPPATH . $directory . '/' . config_item('subclass_prefix') . $class . EXT);
        }
    }

    if ($name === FALSE)
    {
        exit('Unable to locate the specified class: ' . $class . EXT);
    }

    is_loaded($class);
    $_classes[$class] =& instantiate_class(new $name());
    return $_classes[$class];
}

function &instantiate_class(&$class_object)
{
    return $class_object;
}

function is_loaded($class = '')
{
    static $_is_loaded = array();

    if ($class != '')
    {
        $_is_loaded[strtolower($class)] = $class;
    }

    return $_is_loaded;
}

function &get_config($replace = array())
{
    static $_config;

    if (isset($_config))
    {
        return $_config[0];
    }

    if (!file_exists(APPPATH . 'config/config' . EXT))
    {
        exit('The configuration file does not exist.');
    }
    else
    {
        require(APPPATH . 'config/config' . EXT);
    }

    if (!isset($config) OR !is_array($config))
    {
        exit('Your config file does not appear to be formatted correctly.');
    }

    if (count($replace) > 0)
    {
        foreach ($replace as $key => $val)
        {
            if (isset($config[$key]))
            {
                $config[$key] = $val;
            }
        }
    }

    return $_config[0] = & $config;
}

function config_item($item)
{
    static $config_item = array();

    if (!isset($config_item[$item]))
    {
        $config = & get_config();

        if (!isset($config[$item]))
            return FALSE;

        $config_item[$item] = $config[$item];
    }

    return $config_item[$item];
}

function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
{
    $_error = & load_class('Exceptions', 'core');
    print $_error->show_error($heading, $message, 'error_general', $status_code);
    exit;
}

function show_404($page = '', $log_error = TRUE)
{
    $_error = & load_class('Exceptions', 'core');
    $_error->show_404($page, $log_error);
    exit;
}

function log_message($level = 'error', $message, $php_error = FALSE)
{
    static $_log;

    if (config_item('log_threshold') == 0)
    {
        return;
    }

    $_log = & load_class('Log');
    $_log->write_log($level, $message, $php_error);
}

function set_status_header($code = 200, $text = '')
{
    $stati = array(
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    if ($code == '' OR !is_numeric($code))
    {
        show_error('Status codes must be numeric', 500);
    }

    if (isset($stati[$code]) AND $text == '')
    {
        $text = $stati[$code];
    }

    if ($text == '')
    {
        show_error('No status text available.  Please check your status code number or supply your own message text.', 500);
    }

    $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

    if (substr(php_sapi_name(), 0, 3) == 'cgi')
    {
        header("Status: {$code} {$text}", TRUE);
    }
    elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0')
    {
        header($server_protocol . " {$code} {$text}", TRUE, $code);
    }
    else
    {
        header("HTTP/1.1 {$code} {$text}", TRUE, $code);
    }
}

function _exception_handler($severity, $message, $filepath, $line)
{
    if ($severity == E_STRICT)
        return;

    $_error = & load_class('Exceptions', 'core');

    if (($severity & error_reporting()) == $severity)
    {
        $_error->show_php_error($severity, $message, $filepath, $line);
    }

    if (config_item('log_threshold') == 0)
    {
        return;
    }

    $_error->log_exception($severity, $message, $filepath, $line);
}

function remove_invisible_characters($str)
{
    static $non_displayables;

    if (!isset($non_displayables))
    {
        $non_displayables = array(
            '/%0[0-8bcef]/', // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/', // url encoded 16-31
            '/[\x00-\x08]/', // 00-08
            '/\x0b/', '/\x0c/', // 11, 12
            '/[\x0e-\x1f]/'    // 14-31
        );
    }

    do
    {
        $cleaned = $str;
        $str = preg_replace($non_displayables, '', $str);
    }
    while ($cleaned != $str);

    return $str;
}

?>