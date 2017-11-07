<?

class THTTPUSER
{

    public function THTTPUSER()
    {
    }

    public function setLoggedAs($token)
    {
        $_SESSION["______wu_status"] = "hvjbfg4gs9dfao1qweidkqwe";
        $_SESSION["______wu_token"] = $token;
        $_SESSION["______wu_remote_ip"] = $_SERVER["REMOTE_ADDR"];
    }

    public function isLogged()
    {
        return (isset($_SESSION["______wu_status"]) &&
        $_SESSION["______wu_status"] == "hvjbfg4gs9dfao1qweidkqwe" &&
        $_SESSION["______wu_remote_ip"] == $_SERVER["REMOTE_ADDR"]);
    }

    public function isLoggedAs($token)
    {
        return ($this->isLogged() && $_SESSION["______wu_token"] == $token);
    }

    public function logOut()
    {
        if (isset($_SESSION["______wu_status"]))
        {
            unset($_SESSION["______wu_status"]);
        }

        if (isset($_SESSION["______wu_token"]))
        {
            unset($_SESSION["______wu_token"]);
        }

        if (isset($_SESSION["______wu_id"]))
        {
            unset($_SESSION["______wu_id"]);
        }

        if (isset($_SESSION["______wu_username"]))
        {
            unset($_SESSION["______wu_username"]);
        }

        if (isset($_SESSION["______wu_password"]))
        {
            unset($_SESSION["______wu_password"]);
        }

        if (isset($_SESSION["______wu_sessid"]))
        {
            unset($_SESSION["______wu_sessid"]);
        }

        if (isset($_SESSION["______wu_token"]))
        {
            unset($_SESSION["______wu_token"]);
        }

        if (isset($_SESSION["______wu_remote_ip"]))
        {
            unset($_SESSION["______wu_remote_ip"]);
        }

        if (isset($_SESSION["______wu_roles"]))
        {
            unset($_SESSION["______wu_roles"]);
        }
    }

    public function getAuthToken()
    {
        $k = "______wu_token";
        return isset($_SESSION[$k]) ? $_SESSION[$k] : NULL;
    }

    public function setId($id)
    {
        $_SESSION["______wu_id"] = $id;
    }

    public function getId()
    {
        $k = "______wu_id";
        return isset($_SESSION[$k]) ? $_SESSION[$k] : NULL;
    }

    public function setUsername($username)
    {
        $_SESSION["______wu_username"] = $username;
    }

    public function getUsername()
    {
        $k = "______wu_username";
        return isset($_SESSION[$k]) ? $_SESSION[$k] : NULL;
    }

    public function setPassword($password)
    {
        $_SESSION["______wu_password"] = $password;
    }

    public function getPassword()
    {
        $k = "______wu_password";
        return isset($_SESSION[$k]) ? $_SESSION[$k] : NULL;
    }

    public function setSessionId($id)
    {
        $_SESSION["______wu_sessid"] = $id;
    }

    public function getSessionId()
    {
        $k = "______wu_sessid";
        return isset($_SESSION[$k]) ? $_SESSION[$k] : NULL;
    }

    public function setRoles($roles)
    {
        if (count($roles) > 0)
        {
            $_SESSION["______wu_roles"] = implode(",", $roles);
        }
    }

    public function addRole($role)
    {
        $temp = $this->getRoles();
        $temp[] = $role;
        $this->setRoles($temp);
    }

    public function getRoles()
    {
        if (isset($_SESSION["______wu_roles"]))
        {
            return explode(",", $_SESSION["______wu_roles"]);
        }
        else
        {
            return Array();
        }
    }

    public function hasRole($role)
    {
        if (in_array($role, $this->getRoles(), TRUE))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function setProperty($key, $value)
    {
        TPAGE::getInstance()->session->setProperty("______" . $key, $value);
    }

    public function getProperty($key)
    {
        return TPAGE::getInstance()->session->getProperty("______" . $key);
    }

    public function removeProperty($key)
    {
        TPAGE::getInstance()->session->removeProperty("______" . $key);
    }

    public function loginAttempt($file, $username, $password)
    {
        $dataSource = new TDATASOURCE();
        $dataSource->loadFromXmlFile(PROTECTED_SECURITY_DIR . $file);
        $dataSource->keep("username", $username);

        if ($dataSource->isNotEmpty())
        {
            $record = $dataSource->record(0);

            if (isset($record["password"]))
            {
                if ($record["password"] == $password || strtolower($record["password"]) == strtolower(md5($password)))
                {
                    $this->setLoggedAs("");
                    $this->setUsername($record["username"]);

                    $record["username"] = NULL;
                    $record["password"] = NULL;

                    if (isset($record["id"]))
                    {
                        $this->setId($record["id"]);
                        $record["id"] = NULL;
                    }

                    if (isset($record["logged-as"]))
                    {
                        $this->setLoggedAs($record["logged-as"]);
                        $record["logged-as"] = NULL;
                    }

                    if (isset($record["roles"]))
                    {
                        $this->setRoles(explode(",", $record["roles"]));
                        $record["roles"] = NULL;
                    }

                    foreach ($record as $key => $value)
                    {
                        if ($value !== NULL)
                        {
                            $this->setProperty($key, $value);
                        }
                    }
                }
            }
        }
    }

}

?>