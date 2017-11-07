<?

class TLOCALE
{

    static private $fPropertyList = Array();

    static public function put($key, $value)
    {
        self::$fPropertyList[$key] = $value;
    }

    static public function get($key, $defaultValue = NULL)
    {
        return (isset(self::$fPropertyList[$key]) ? self::$fPropertyList[$key] : ($defaultValue !== NULL ? $defaultValue : $key));
    }

    static public function format($key, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL)
    {
        $val = TLOCALE::get($key);

        if (!$val)
        {
            return $val;
        }

        if ($arg1 !== NULL && $arg2 !== NULL && $arg3 !== NULL)
        {
            return sprintf($val, $arg1, $arg2, $arg3);
        }
        else if ($arg1 !== NULL && $arg2 !== NULL)
        {
            return sprintf($val, $arg1, $arg2);
        }
        else if ($arg1 !== NULL)
        {
            return sprintf($val, $arg1);
        }

        return $val;
    }

    static public function exist($key)
    {
        return isset(self::$fPropertyList[$key]);
    }

}

?>