<?

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

if (!defined('PHP_EOL'))
{
    define('PHP_EOL', (DIRECTORY_SEPARATOR == '/') ? "\n" : "\r\n");
}

if (!function_exists('file_put_contents'))
{

    function file_put_contents($filename, $data, $flags = NULL)
    {
        if (is_scalar($data))
        {
            settype($data, 'STRING');
        }

        if (!is_string($data) && !is_array($data) && !is_resource($data))
        {
            $backtrace = debug_backtrace();
            _exception_handler(E_USER_WARNING, 'file_put_contents(): the 2nd parameter should be either a string or an array', $backtrace[0]['file'], $backtrace[0]['line']);
            return FALSE;
        }

        if (is_resource($data))
        {
            if (get_resource_type($data) !== 'stream')
            {
                $backtrace = debug_backtrace();
                _exception_handler(E_USER_WARNING, 'file_put_contents(): supplied resource is not a valid stream resource', $backtrace[0]['file'], $backtrace[0]['line']);
                return FALSE;
            }

            $text = '';

            while (!feof($data))
            {
                $text .= fread($data, 4096);
            }

            $data = $text;
            unset($text);
        }

        if (is_array($data))
        {
            $data = implode('', $data);
        }

        if (($flags & 8) > 0) // 8 = FILE_APPEND flag
        {
            $mode = FOPEN_WRITE_CREATE;
        }
        else
        {
            $mode = FOPEN_WRITE_CREATE_DESTRUCTIVE;
        }

        if (($flags & 1) > 0) // 1 = FILE_USE_INCLUDE_PATH flag
        {
            $use_include_path = TRUE;
        }
        else
        {
            $use_include_path = FALSE;
        }

        $fp = @fopen($filename, $mode, $use_include_path);

        if ($fp === FALSE)
        {
            $backtrace = debug_backtrace();
            _exception_handler(E_USER_WARNING, 'file_put_contents(' . htmlentities($filename) . ') failed to open stream', $backtrace[0]['file'], $backtrace[0]['line']);
            return FALSE;
        }

        if (($flags & LOCK_EX) > 0)
        {
            if (!flock($fp, LOCK_EX))
            {
                $backtrace = debug_backtrace();
                _exception_handler(E_USER_WARNING, 'file_put_contents(' . htmlentities($filename) . ') unable to acquire an exclusive lock on file', $backtrace[0]['file'], $backtrace[0]['line']);
                return FALSE;
            }
        }

        if (($written = @fwrite($fp, $data)) === FALSE)
        {
            $backtrace = debug_backtrace();
            _exception_handler(E_USER_WARNING, 'file_put_contents(' . htmlentities($filename) . ') failed to write to ' . htmlentities($filename), $backtrace[0]['file'], $backtrace[0]['line']);
        }

        @fclose($fp);
        return $written;
    }

}

if (!function_exists('fputcsv'))
{

    function fputcsv($handle, $fields, $delimiter = ',', $enclosure = '"')
    {
        if (!is_resource($handle))
        {
            $backtrace = debug_backtrace();
            _exception_handler(E_USER_WARNING, 'fputcsv() expects parameter 1 to be stream resource, ' . gettype($handle) . ' given', $backtrace[0]['file'], $backtrace[0]['line']);
            return FALSE;
        }

        if (get_resource_type($handle) !== 'stream')
        {
            $backtrace = debug_backtrace();
            _exception_handler(E_USER_WARNING, 'fputcsv() expects parameter 1 to be stream resource, ' . get_resource_type($handle) . ' given', $backtrace[0]['file'], $backtrace[0]['line']);
            return FALSE;
        }

        if (!is_array($fields))
        {
            $backtrace = debug_backtrace();
            _exception_handler(E_USER_WARNING, 'fputcsv() expects parameter 2 to be array, ' . gettype($fields) . ' given', $backtrace[0]['file'], $backtrace[0]['line']);
            return FALSE;
        }

        if (strlen($delimiter) > 1)
        {
            $delimiter = substr($delimiter, 0, 1);
            $backtrace = debug_backtrace();
            _exception_handler(E_NOTICE, 'fputcsv() delimiter must be one character long, "' . htmlentities($delimiter) . '" used', $backtrace[0]['file'], $backtrace[0]['line']);
        }

        if (strlen($enclosure) > 1)
        {
            $enclosure = substr($enclosure, 0, 1);
            $backtrace = debug_backtrace();
            _exception_handler(E_NOTICE, 'fputcsv() enclosure must be one character long, "' . htmlentities($enclosure) . '" used', $backtrace[0]['file'], $backtrace[0]['line']);
        }

        $out = '';

        foreach ($fields as $cell)
        {
            $cell = str_replace($enclosure, $enclosure . $enclosure, $cell);

            if (strpos($cell, $delimiter) !== FALSE OR strpos($cell, $enclosure) !== FALSE OR strpos($cell, "\n") !== FALSE)
            {
                $out .= $enclosure . $cell . $enclosure . $delimiter;
            }
            else
            {
                $out .= $cell . $delimiter;
            }
        }

        $length = @fwrite($handle, substr($out, 0, -1) . "\n");

        return $length;
    }

}

if (!function_exists('stripos'))
{

    function stripos($haystack, $needle, $offset = NULL)
    {
        if (is_scalar($haystack))
        {
            settype($haystack, 'STRING');
        }

        if (!is_string($haystack))
        {
            $backtrace = debug_backtrace();
            _exception_handler(E_USER_WARNING, 'stripos() expects parameter 1 to be string, ' . gettype($haystack) . ' given', $backtrace[0]['file'], $backtrace[0]['line']);
            return FALSE;
        }

        if (!is_scalar($needle))
        {
            $backtrace = debug_backtrace();
            _exception_handler(E_USER_WARNING, 'stripos() needle is not a string or an integer in ' . $backtrace[0]['file'], $backtrace[0]['line']);
            return FALSE;
        }

        if (is_float($offset))
        {
            $offset = (int) $offset;
        }

        if (!is_int($offset) && !is_bool($offset) && !is_null($offset))
        {
            $backtrace = debug_backtrace();
            _exception_handler(E_USER_WARNING, 'stripos() expects parameter 3 to be long, ' . gettype($offset) . ' given', $backtrace[0]['file'], $backtrace[0]['line']);
            return NULL;
        }

        return strpos(strtolower($haystack), strtolower($needle), $offset);
    }

}

if (!function_exists('str_ireplace'))
{

    function str_ireplace($search, $replace, $subject)
    {
        if ($search === NULL OR $subject === NULL)
        {
            return $subject;
        }

        if (is_scalar($search) && is_array($replace))
        {
            $backtrace = debug_backtrace();

            if (is_object($replace))
            {
                show_error('Object of class ' . get_class($replace) . ' could not be converted to string in ' . $backtrace[0]['file'] . ' on line ' . $backtrace[0]['line']);
            }
            else
            {
                _exception_handler(E_USER_NOTICE, 'Array to string conversion in ' . $backtrace[0]['file'], $backtrace[0]['line']);
            }
        }

        if (is_array($search))
        {
            if (is_array($replace))
            {
                $search = array_values($search);
                $replace = array_values($replace);

                if (count($search) >= count($replace))
                {
                    $replace = array_pad($replace, count($search), '');
                }
                else
                {
                    $replace = array_slice($replace, 0, count($search));
                }
            }
            else
            {
                $replace = array_fill(0, count($search), $replace);
            }
        }
        else
        {
            $search = array((string) $search);
            $replace = array((string) $replace);
        }

        foreach ($search as $search_key => $search_value)
        {
            $search[$search_key] = '/' . preg_quote($search_value, '/') . '/i';
        }

        foreach ($replace as $k => $v)
        {
            $replace[$k] = str_replace(array(chr(92), '$'), array(chr(92) . chr(92), '\$'), $v);
        }

        $result = preg_replace($search, $replace, (array) $subject);

        if (!is_array($subject))
        {
            return current($result);
        }

        return $result;
    }

}

if (!function_exists('http_build_query'))
{

    function http_build_query($formdata, $numeric_prefix = NULL, $separator = NULL)
    {
        if (!is_array($formdata) && !is_object($formdata))
        {
            $backtrace = debug_backtrace();
            _exception_handler(E_USER_WARNING, 'http_build_query() Parameter 1 expected to be Array or Object. Incorrect value given', $backtrace[0]['file'], $backtrace[0]['line']);
            return FALSE;
        }

        if (is_object($formdata))
        {
            $formdata = get_object_vars($formdata);
        }

        if (empty($formdata))
        {
            return NULL;
        }

        if ($separator === NULL)
        {
            $separator = ini_get('arg_separator.output');

            if (strlen($separator) == 0)
            {
                $separator = '&';
            }
        }

        $tmp = array();

        foreach ($formdata as $key => $val)
        {
            if ($val === NULL)
            {
                continue;
            }

            if (is_integer($key) && $numeric_prefix != NULL)
            {
                $key = $numeric_prefix . $key;
            }

            if (is_resource($val))
            {
                return NULL;
            }

            $tmp[] = _http_build_query_helper($key, $val, $separator);
        }

        return implode($separator, $tmp);
    }

    function _http_build_query_helper($key, $val, $separator = '&')
    {
        if (is_scalar($val))
        {
            return urlencode($key) . '=' . urlencode($val);
        }
        else
        {
            if (is_object($val))
            {
                $val = get_object_vars($val);
            }

            foreach ($val as $k => $v)
            {
                $tmp[] = _http_build_query_helper($key . '[' . $k . ']', $v, $separator);
            }
        }

        return implode($separator, $tmp);
    }

}