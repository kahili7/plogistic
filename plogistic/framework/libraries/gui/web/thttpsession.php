<?

class THTTPSESSION
{

    public function THTTPSESSION()
    {
        
    }

    public function setProperty($key, $value)
    {
        if ($value === NULL)
        {
            $this->removeProperty($key);
            return;
        }

        $_SESSION[$key] = serialize($value);
    }

    public function getProperty($key, $defaultValue = NULL)
    {
        $tmp = (isset($_SESSION[$key]) ? unserialize($_SESSION[$key]) : NULL);

        if ($tmp === NULL && $defaultValue !== NULL)
        {
            $tmp = $defaultValue;
        }

        return $tmp;
    }

    public function hasProperty($key, $value = NULL)
    {
        $tmp = $this->getProperty($key);

        if ($tmp == NULL)
        {
            return FALSE;
        }

        if ($value == NULL)
        {
            return TRUE;
        }

        if ($value === $tmp)
        {
            return TRUE;
        }

        return FALSE;
    }

    public function exists($key, $value = NULL)
    {
        return $this->hasProperty($key, $value);
    }

    public function removeProperty($key)
    {
        if (isset($_SESSION[$key]))
        {
            unset($_SESSION[$key]);
        }
    }

    public function destroy()
    {
        session_unset();
        session_destroy();
    }

}

?>