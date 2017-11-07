<?

class TCONTROLSTATE
{

    private $fPropertyList = Array();

    public function TCONTROLSTATE()
    {
        
    }

    public function setProperty($key, $value)
    {
        $this->fPropertyList[$key] = $value;
    }

    public function getProperty($key)
    {
        return (isset($this->fPropertyList[$key]) ? $this->fPropertyList[$key] : NULL);
    }

    public function serialize()
    {
        if (count($this->fPropertyList) > 0)
        {
            return base64_encode(serialize($this->fPropertyList));
        }
        else
        {
            return "";
        }
    }

    public function unserialize($value)
    {
        if ($value)
        {
            $this->fPropertyList = unserialize(base64_decode($value));
        }
    }

}

?>