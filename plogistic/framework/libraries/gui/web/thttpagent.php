<?

class THTTPAGENT
{

    private $_ie = FALSE;
    private $_ff = FALSE;
    private $_moz = FALSE;
    private $_opera = FALSE;
    private $_konqueror = FALSE;
    private $_chrome = FALSE;
    private $_safari = FALSE;
    private $_unknown = FALSE;
    private $_ver = 0;
    public $OnLoad = NULL;
    public $OnUnload = NULL;

    public function THTTPAGENT()
    {
        @$this->process();
        $this->OnLoad = new THTTPAGENTEVENT();
        $this->OnUnload = new THTTPAGENTEVENT();
    }

    public function executeScript($script, $priority = 4)
    {
        if (!AJAX_REQUEST)
        {
            $this->OnLoad->executeScript($script, $priority);
        }
        else
        {
            response()->executeScript($script, $priority);
        }
    }

    public function call($funcName, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $priority = 4)
    {
        if ($arg3 !== NULL)
        {
            $this->executeScript($funcName . "(" . json_encode($arg1) . ", " . json_encode($arg2) . ", " . json_encode($arg3) . ")", $priority);
        }
        else if ($arg2 !== NULL)
        {
            $this->executeScript($funcName . "(" . json_encode($arg1) . ", " . json_encode($arg2) . ")", $priority);
        }
        else if ($arg1 !== NULL)
        {
            $this->executeScript($funcName . "(" . json_encode($arg1) . ")", $priority);
        }
        else
        {
            $this->executeScript($funcName . "()", $priority);
        }
    }

    public function callLast($funcName, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL)
    {
        $this->call($funcName, $arg1, $arg2, $arg3, 5);
    }

    public function registerWidget($instanceName, $className, $args = Array())
    {
        $argsPacked = Array();

        foreach ($args as $key => $value)
        {
            if ($value !== NULL)
            {
                $argsPacked[$key] = $value;
            }
        }

        $this->executeScript("page.registerWidget(" . "new " . $className . "(" . json_encode($instanceName) . ", " . json_encode($argsPacked) . "))", 2);
    }

    public function toString()
    {
        return (isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "");
    }

    private function process()
    {
        if (preg_match("/^.*MSIE [456789].*$/i", $this->toString()) && !preg_match("/^.*(Opera|Gecko|Firefox).*$/i", $this->toString()))
        {
            $this->_ie = TRUE;
            preg_match_all("/MSIE (\d+)\.(\d+)/i", $this->toString(), $matches);

            if (count($matches) == 3 && isset($matches[1][0]) && isset($matches[2][0]))
            {
                $this->_ver = $matches[1][0] . "." . $matches[2][0];
            }
        }
        else if (preg_match("/^.*Gecko.*$/i", $this->toString()) && !preg_match("/^.*(Opera|Firefox|Chrome|Netscape|Safari|Konqueror).*$/i", $this->toString()))
        {

            $this->_moz = TRUE;
            preg_match_all("/rv:(\d+)\.(\d+)/i", $this->toString(), $matches);

            if (count($matches) == 3 && isset($matches[1][0]) && isset($matches[2][0]))
            {
                $this->_ver = $matches[1][0] . "." . $matches[2][0];
            }
        }
        else if (preg_match("/^.*Firefox.*$/i", $this->toString()))
        {

            $this->_ff = TRUE;
            preg_match_all("/Firefox\/(\d+)\.(\d+)/i", $this->toString(), $matches);

            if (count($matches) == 3 && isset($matches[1][0]) && isset($matches[2][0]))
            {
                $this->_ver = $matches[1][0] . "." . $matches[2][0];
            }
        }
        else if (preg_match("/^.*Opera.*$/i", $this->toString()))
        {

            $this->_opera = TRUE;
            preg_match_all("/Opera[ \/](\d+)\.(\d+)/i", $this->toString(), $matches);

            if (count($matches) == 3 && isset($matches[1][0]) && isset($matches[2][0]))
            {
                $this->_ver = $matches[1][0] . "." . $matches[2][0];
            }
        }
        else if (preg_match("/^.*Konqueror.*$/i", $this->toString()))
        {

            $this->_konqueror = TRUE;
            preg_match_all("/Konqueror[ \/](\d+)\.(\d+)/i", $this->toString(), $matches);

            if (count($matches) == 3 && isset($matches[1][0]) && isset($matches[2][0]))
            {
                $this->_ver = $matches[1][0] . "." . $matches[2][0];
            }
        }
        else if (preg_match("/^.*Chrome.*$/i", $this->toString()))
        {

            $this->_chrome = TRUE;
            preg_match_all("/Chrome[ \/](\d+)\.(\d+)/i", $this->toString(), $matches);

            if (count($matches) == 3 && isset($matches[1][0]) && isset($matches[2][0]))
            {
                $this->_ver = $matches[1][0] . "." . $matches[2][0];
            }
        }
        else if (preg_match("/^.*Safari.*$/i", $this->toString()))
        {

            $this->_safari = TRUE;
            preg_match_all("/Version[ \/](\d+)\.(\d+)/i", $this->toString(), $matches);

            if (count($matches) == 3 && isset($matches[1][0]) && isset($matches[2][0]))
            {
                $this->_ver = $matches[1][0] . "." . $matches[2][0];
            }
        }
        else
        {
            $this->_unknown = TRUE;
        }
    }

    public function isIE()
    {
        return $this->_ie;
    }

    public function isMozilla()
    {
        return $this->_moz;
    }

    public function isFirefox()
    {
        return $this->_ff;
    }

    public function isOpera()
    {
        return $this->_opera;
    }

    public function isKonqueror()
    {
        return $this->_konqueror;
    }

    public function isSafari()
    {
        return $this->_safari;
    }

    public function isChrome()
    {
        return $this->_chrome;
    }

    public function isUnknown()
    {
        return $_unknown;
    }

    public function version()
    {
        return $this->_ver;
    }

    public function isXHTMLSupported()
    {
        if (isset($_SERVER["HTTP_ACCEPT"]) && preg_match("/^.*application\/xhtml\+xml.*$/i", $_SERVER["HTTP_ACCEPT"]))
        {
            return TRUE;
        }

        if (isset($_SERVER["HTTP_USER_AGENT"]) && preg_match("/^.*W3C_Validator.*$/i", $_SERVER["HTTP_USER_AGENT"]))
        {
            return TRUE;
        }

        if (isset($_SERVER["HTTP_ACCEPT"]) && $_SERVER["HTTP_ACCEPT"] == "*/*")
        {
            return TRUE;
        }

        return FALSE;
    }

}

?>