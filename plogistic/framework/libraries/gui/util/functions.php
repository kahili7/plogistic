<?
function isTrue($value)
{
    return (($value === TRUE || $value === 1 || strtolower($value) === "true") ? TRUE : FALSE);
}

function startsWith($haystack, $needle, $case = TRUE)
{
    if ($case)
    {
        return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }
    else
    {
        return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }
}

function endsWith($haystack, $needle, $case = TRUE)
{
    if ($case)
    {
        return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
    }
    else
    {
        return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
    }
}

function str2uid($str)
{
    $str1 = strtolower($str);
    $str1 = md5($str1);
    $result = 0;

    for ($i = 0; $i < strlen($str1); $i++)
    {
        $ascii = ord($str1[$i]);
        $result += $ascii;
    }

    return (int) ($result + strlen($str) + (ord($str1[0]) * ord($str1[strlen($str1) - 1])));
}

function randomString($length)
{
    $str = "";
    $alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    
    for ($i = 0; $i < $length; $i++)
    {
        $str .= $alphabet[mt_rand(0, strlen($alphabet) - 1)];
    }

    return $str;
}

function safenl($text)
{
    $text = str_replace(chr(13), "", $text);
    $text = str_replace(chr(10), " ", $text);
    return $text;
}

function filesizeToString($size)
{
    if (!$size)
    {
        return null;
    }

    if ($size < 1024)
    {
        return $size . "b";
    }

    if ($size < 1048576)
    {
        return floor($size / 1024) . "kb " . filesizeToString($size - floor($size / 1024) * 1024);
    }

    if ($size < 1073741824)
    {
        return floor($size / (1048576)) . "mb " . filesizeToString($size - floor($size / (1048576)) * 1048576);
    }

    if ($size < 1099511627776)
    {
        return floor($size / (1073741824)) . "gb " . filesizeToString($size - floor($size / (1073741824)) * 1073741824);
    }

    return $size . "";
}

function isRelativePath($filePath)
{
    if (!$filePath)
    {
        return FALSE;
    }

    return !preg_match("/^[A-Z]\:.*$/i", $filePath) && !preg_match("/^\/.*$/i", $filePath);
}

function safequot($text)
{
    $text = str_replace("\"", "&quot;", $text);
    $text = str_replace("'", "&apos;", $text);
    return $text;
}

function first($arr)
{
    if (is_array($arr) && count($arr) > 0)
    {

        if (isset($arr[0]))
        {
            return $arr[0];
        }
        else
        {
            $values = array_values($arr);
            return $values[0];
        }
    }

    return NULL;
}

function addSafeBackSlash($dir)
{
    return rtrim($dir, "\\/") . "/";
}

function dropBackSlash($dir)
{
    if (is_dir($dir))
    {
        return rtrim($dir, "\\/");
    }

    return $dir;
}

function addSafeDirSlash($dir)
{
    return rtrim($dir, "\\/") . DIRECTORY_SEPARATOR;
}

function appendUrlParam($url, $name, $value)
{
    if ($name && $value)
    {
        $suffix = $name . '=' . $value;

        if (preg_match('/\?/i', $url))
        {
            $url = $url . '&' . $suffix;
        }
        else
        {
            $url = $url . '?' . $suffix;
        }
    }

    return $url;
}

function toTimestamp($timestampStr, $pattern = "j-M-Y H:i:s")
{
    if (is_long($timestampStr))
    {
        return date($pattern, $timestampStr);
    }

    return date($pattern, strtotime($timestampStr));
}

function toMoney($amount)
{
    return number_format($amount, 2, '.', ',');
}

function httpBuildQuery($arr, $convention = '%s')
{
    if (false && function_exists('http_build_query'))
    {
        $query = http_build_query($arr);
        return $query;
    }
    else
    {
        $query = "";

        foreach ($arr as $key => $value)
        {
            $key = "" . $key;
            $value = "" . $value;

            if (is_array($value))
            {
                $new_convention = sprintf($convention, $key) . "[%s]";
                $query .= httpBuildQuery($value, $new_convention);
            }
            else
            {
                $key = urlencode($key);
                $value = non_ascii_urlencode($value);
                $query .= sprintf($convention, $key) . "=$value&";
            }
        }

        if ($query)
            $query = substr($query, 0, strlen($query) - 1);

        return $query;
    }
}

function httpParseQuery($str, $sep = "&")
{
    parse_str($str, $output);
    return $output;
}

function non_ascii_urlencode($str)
{
    $output = "";

    for ($i = 0; $i < strlen($str); $i++)
    {
        if (ord($str[$i]) < 128)
        {
            $output .= urlencode($str[$i]);
        }
        else
        {
            $output .= $str[$i];
        }
    }

    return $output;
}

function tagLink($uri, $args = Array())
{
    $uri = endsWith($uri, ".php") ? appendUrlParam($uri, "rnd", SYS_RANDOM_STRING) : $uri;
    return "<link " . (isset($args['id']) ? "id='" . $args['id'] . "' " : "") . "rel='stylesheet' type='text/css' href='{$uri}'/>";
}

function tagScript($uri, $args = Array())
{
    $uri = endsWith($uri, ".php") ? appendUrlParam($uri, "rnd", SYS_RANDOM_STRING) : $uri;
    return "<script type='text/javascript' " . (isset($args['id']) ? "id='" . $args['id'] . "' " : "") . "src='{$uri}'></script>";
}

function tagExecOnLoad($code)
{
    $page = TPage::getInstance();

    if ($page->agent->isIE() || ($page->agent->isOpera() && ($page->agent->version() < 8.5)))
    {
        return "window.attachEvent('onload', function() {" . $code . "});";
    }
    else
    {
        return "window.addEventListener('load', function() {" . $code . "}, false);";
    }
}

function maxStringChars($str, $max, $append = "...")
{
    if (strlen($str) <= ($max - strlen($append)))
    {
        return $str;
    }

    return substr($str, 0, $max - strlen($append)) . $append;
}

function stdToArray($stdObject)
{

    if ($stdObject === NULL)
    {
        return $stdObject;
    }

    if (is_array($stdObject))
    {
        $tmp = Array();

        foreach ($stdObject as $key => $value)
        {
            $tmp[$key] = stdToArray($value);
        }

        return $tmp;
    }

    if (is_class($stdObject, "stdClass"))
    {
        $tmp = Array();

        foreach ($stdObject as $key => $value)
        {
            $tmp[$key] = stdToArray($stdObject->$key);
        }

        return $tmp;
    }

    return $stdObject;
}

function explodeStyleString($styleString)
{
    $buffer = Array();
    $styleTokens = explode(";", $styleString);

    for ($i = 0; $i < count($styleTokens); $i++)
    {
        $styleToken = trim($styleTokens[$i]);

        if ($styleToken)
        {
            $styleTokenKV = explode(":", $styleToken);

            if (count($styleTokenKV) == 2)
            {
                $k = str_replace(" ", "", strtolower(trim($styleTokenKV[0])));
                $k = str_replace("-", " ", $k);
                $k = ucwords($k);
                $k = str_replace(" ", "", $k);
                $k = strtolower($k[0]) . substr($k, 1);
                $v = trim($styleTokenKV[1]);
                $buffer[$k] = $v;
            }
        }
    }

    return $buffer;
}

function destroyWidget($aWidgetName)
{
    TPage::getInstance()->destroyWidget($aWidgetName);
}

?>