<?

class TTCPSOCKET
{

    private $fSocket = NULL;
    private $fErrors = Array();
    private $fData = "";

    public function TTCPSOCKET()
    {
        
    }

    public function getErrors()
    {
        return $this->fErrors;
    }

    public function getData()
    {
        return $this->fData;
    }

    public function hasErrors()
    {
        return (count($this->fErrors) > 0);
    }

    public function connect($host, $port, $readTimeout = NULL)
    {
        $this->fSocket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($this->fSocket < 0)
        {
            $this->fErrors[] = socket_strerror(socket_last_error($this->fSocket));
            return FALSE;
        }

        if ($readTimeout != NULL)
        {
            @socket_set_option($this->fSocket, SOL_SOCKET, SO_RCVTIMEO, Array("sec" => $readTimeout, "usec" => 0));
        }

        $result = @socket_connect($this->fSocket, $host, $port);

        if (!$result)
        {
            $this->fErrors[] = socket_strerror(socket_last_error($this->fSocket));
            return FALSE;
        }

        return TRUE;
    }

    public function write($data)
    {
        if ($this->hasErrors())
        {
            $this->fErrors[] = "Could not write to socket";
            return;
        }

        @socket_write($this->fSocket, $data, strlen($data));
    }

    public function read()
    {
        if ($this->hasErrors())
        {
            $this->fErrors[] = "Could not read from socket";
            return;
        }

        $buf = "";
        $portion = "";

        while ($portion = @socket_read($this->fSocket, 2048))
        {
            $buf .= $portion;
        }

        $this->fData = $buf;
        return $this->fData;
    }

    public function disconnect()
    {
        @socket_close($this->fSocket);
    }

}

?>