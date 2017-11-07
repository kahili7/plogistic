<?

class TAPPLICATIONCONTENT
{

    private $fHost;
    private $fHostAliases = Array();
    private $fAllowedIpAddresses = NULL;
    private $fLoginPages = Array();
    private $fAuthPages = Array();
    private $fNotAuthPages = Array();
    private $fLookAndFeel = NULL;

    public function TAPPLICATIONCONTENT($aHost)
    {
        $this->fHost = $aHost;
    }

    public function getHost()
    {
        return $this->fHost;
    }

    public function addHostAlias($hostAlias)
    {
        $this->fHostAliases[$hostAlias] = $hostAlias;
    }

    public function isHostAlias($hostAlias)
    {
        return isset($this->fHostAliases[$hostAlias]);
    }

    public function setAllowedIpAddresses($aAllowedIpAddresses)
    {
        $this->fAllowedIpAddresses = $aAllowedIpAddresses;
    }

    public function getAllowedIpAddresses()
    {
        return $this->fAllowedIpAddresses;
    }

    public function setLoginPage($page, $token = "AuthToken")
    {
        $this->fLoginPages[$token] = $page;
        $this->addNotAuthPage($page);
    }

    public function getLoginPage($token = "AuthToken")
    {
        return $this->fLoginPages[$token];
    }

    public function addAuthPage($page, $token = "AuthToken")
    {
        $this->fAuthPages[$page] = $token;
    }

    public function addNotAuthPage($page)
    {
        $this->fNotAuthPages[$page] = "notauth";
    }

    public function isAuthPage($page, $token = "AuthToken")
    {
        reset($this->fNotAuthPages);

        while (list($p, $t) = each($this->fNotAuthPages))
        {
            if (preg_match("/^" . str_replace("/", "\/", $p) . "$/i", $page))
            {
                return FALSE;
            }
        }

        reset($this->fAuthPages);

        while (list($p, $t) = each($this->fAuthPages))
        {
            if (preg_match("/^" . str_replace("/", "\/", $p) . "$/i", $page))
            {
                return $t;
            }
        }

        return FALSE;
    }

    public function setLookAndFeel($aLookAndFeel)
    {
        $this->fLookAndFeel = $aLookAndFeel;
    }

    public function getLookAndFeel()
    {
        return $this->fLookAndFeel;
    }

}

?>