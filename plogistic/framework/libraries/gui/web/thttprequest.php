<?

class THTTPREQUEST
{

    public $post = NULL;
    public $get = NULL;

    public function THttpRequest()
    {
        $this->post = new THTTPPOSTREQUEST();
        $this->get = new THTTPGETREQUEST();
    }

    public function getParameter($name, $defaultValue = NULL)
    {
        $value = NULL;

        if (isset($_GET[$name]))
        {
            $value = $_GET[$name];
        }
        else if (isset($_POST[$name]))
        {
            $value = $_POST[$name];
        }

        if ($value !== NULL)
        {
            return $value;
        }

        if ($defaultValue !== NULL)
        {
            return $defaultValue;
        }

        return NULL;
    }

    public function getParam($name, $defaultValue = NULL)
    {
        return $this->getParameter($name, $defaultValue);
    }

    public function param($name, $defaultValue = NULL)
    {
        return $this->getParameter($name, $defaultValue);
    }

    public function getParameters($regexpr = NULL)
    {
        $paramsGet = $this->get->getParameters($regexpr);
        $paramsPost = $this->post->getParameters($regexpr);
        return array_merge($paramsGet, $paramsPost);
    }

    public function isSqlInjectionSafe()
    {
        $validator = new TDATAVALIDATOR();
        $params = $this->getParameters();

        foreach ($params as $key => $value)
        {
            if ($validator->isSqlInjectionSafe($value) === FALSE)
            {
                return FALSE;
            }
        }

        return TRUE;
    }

    public function exist($name, $value = NULL)
    {
        $tmp = $this->getParam($name);

        if ($tmp == NULL)
        {
            return FALSE;
        }

        if ($value == NULL)
        {
            return TRUE;
        }

        if ($value == $tmp)
        {
            return TRUE;
        }

        return FALSE;
    }

    public function getCookie($name)
    {
        if (isset($_COOKIE[$name]))
        {
            return $_COOKIE[$name];
        }
        else
        {
            return NULL;
        }
    }

    public function getRemoteIp()
    {
        return $_SERVER["REMOTE_ADDR"];
    }

    public function getHost()
    {
        if (isset($_SERVER["HTTP_HOST"]))
        {
            return $_SERVER["HTTP_HOST"];
        }
        else
        {
            return "UnknownHost";
        }
    }

    public function getDomain()
    {
        $host = strtolower($this->getHost());
        $temp = explode(".", $host);

        if (count($temp) >= 2)
        {
            return $temp[count($temp) - 2] . "." . $temp[count($temp) - 1];
        }

        return NULL;
    }

    public function getRemotePort()
    {
        return $_SERVER["REMOTE_PORT"];
    }

    public function getPhpSelf()
    {
        return $_SERVER["PHP_SELF"];
    }

    public function getQueryString()
    {
        return $_SERVER["QUERY_STRING"];
    }

    public function getUri()
    {
        return $_SERVER["REQUEST_URI"];
    }

    public function getUrl()
    {
        return $this->getProtocol() . "://" . $this->getHost() . $this->getUri();
    }

    public function getMethod()
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    public function getProtocol()
    {
        return ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https" : "http");
    }

    public function isHTTPS()
    {
        return (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
    }

    public function getReferer()
    {
        return isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : NULL;
    }

    public function getDocumentRoot()
    {
        return isset($_SERVER["DOCUMENT_ROOT"]) ? $_SERVER["DOCUMENT_ROOT"] : NULL;
    }

    public function getScriptFilename()
    {
        return isset($_SERVER["SCRIPT_FILENAME"]) ? $_SERVER["SCRIPT_FILENAME"] : NULL;
    }

}

?>