<?

class TIOHTTPREQUEST
{

    public $referer = NULL;
    public $userAgent = NULL;
    public $response = "";
    public $errNo = 0;
    public $error = "";

    public function TIOHTTPREQUEST()
    {
        
    }

    public function get($targetPage, $parameters = Array())
    {
        $valuesEncString = "";

        foreach ($parameters AS $name => $value)
        {
            $valuesEncString .= urlencode($name) . "=" . urlencode($value) . "&";
        }

        $valuesEncString = substr($valuesEncString, 0, -1);
        $full_url = $targetPage;

        if ($valuesEncString)
        {
            $full_url .= "?" . $valuesEncString;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $full_url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

        if ($this->referer)
        {
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        }

        if ($this->userAgent)
        {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        }

        $this->response = curl_exec($ch);
        $this->errNo = curl_errno($ch);
        $this->error = curl_error($ch);
        curl_close($ch);
    }

    public function post($targetPage, $parameters = Array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $targetPage);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_POST, 1);

        $valuesEncString = "";

        foreach ($parameters AS $name => $value)
        {
            $valuesEncString .= urlencode($name) . "=" . urlencode($value) . "&";
        }

        $valuesEncString = substr($valuesEncString, 0, -1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $valuesEncString);

        if ($this->referer)
        {
            curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        }

        if ($this->userAgent)
        {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        }

        if (preg_match("/^https.*/i", $targetPage))
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // this line makes it work under https
        }

        $this->response = @curl_exec($ch);
        $this->errNo = curl_errno($ch);
        $this->error = curl_error($ch);
        curl_close($ch);
    }

}

?>