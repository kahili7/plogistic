<?

class TREGISTRY
{

    static private $fPropertyList = Array();

    static public function setProperty($key, $value)
    {
        self::$fPropertyList[$key] = $value;
    }

    static public function getProperty($key)
    {
        return (isset(self::$fPropertyList[$key]) ? self::$fPropertyList[$key] : NULL);
    }

    static public function removeProperty($key)
    {
        self::$fPropertyList[$key] = NULL;
    }

}

?>