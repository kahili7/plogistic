<?

class THTTPRESPONSE extends TABSTRACTRESPONSE
{

    public function THTTPRESPONSE()
    {
        parent::TABSTRACTRESPONSE();
    }

    public function noCache()
    {
        Header("Pragma: no-cache");
        Header("Cache-Control: no-cache, must-revalidate, max_age=0");
        Header("Expires: 0");
    }

    public function setCookie($name, $value, $expire = 0, $path = "", $domain = "", $secure = FALSE)
    {
        setcookie($name, $value, time() + $expire, $path, $domain, $secure);
    }

    public function removeCookie($name, $path = "", $domain = "", $secure = FALSE)
    {
        setcookie($name, "", time() - 3600, $path, $domain, $secure);
    }

    public function write($str)
    {
        echo $str;
    }

    public function writeBinary($content, $type = "application/octet-stream", $sendContentLength = TRUE)
    {
        Header("Content-Type: ", $type);

        if ($sendContentLength)
        {
            Header("Content-Length: " . strlen($content));
        }

        echo $content;
    }

    public function sendAsFile($fileName, $content, $mimeType = NULL, $description = NULL)
    {
        Header("Content-Length: " . strlen($content));
        Header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");

        if ($mimeType)
        {
            Header("Content-Type: " . $mimeType);
        }

        if ($description)
        {
            Header("Content-Description: " . $description);
        }

        echo $content;
    }

    public function addCssFile($uri, $id = NULL)
    {
        $this->addParameter("___addcss:;:" . ($this->fUriSeq++) . ":;:" . ($id == NULL ? "" : $id), $uri);
    }

    public function addScriptFile($uri, $id = NULL)
    {
        $this->addParameter("___addscript:;:" . ($this->fUriSeq++) . ":;:" . ($id == NULL ? "" : $id), $uri);
    }

    public function addScriptContent($content)
    {
        $this->fScriptContent .= $content . "\n\n";
    }

}

?>