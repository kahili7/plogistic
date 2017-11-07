<?

class TDATAMODEL
{

    private $fPropertyList = Array();

    public function TDATAMODEL()
    {
        
    }

    public function getDataModelPropertyList()
    {
        return $this->fPropertyList;
    }

    public function getPropertyList()
    {
        $list = Array();

        foreach ($this->fPropertyList as $key => $value)
        {
            if (isset($value["val"]))
            {
                $list[$key] = $value["val"];
            }
        }

        return $list;
    }

    public function setProperty($key, $value, $browserAvailability = FALSE, $stateful = FALSE)
    {
        $this->fPropertyList[$key] = Array(
            "val" => $value,
            "ba" => $browserAvailability,
            "sf" => $stateful
        );
    }

    public function getProperty($key, $defaultValue = NULL)
    {
        $tmp = isset($this->fPropertyList[$key]) ? $this->fPropertyList[$key]["val"] : NULL;

        if ($tmp === NULL && $defaultValue !== NULL)
        {
            $tmp = $defaultValue;
        }

        return $tmp;
    }

    public function hasProperty($key, $value = NULL, $flexibleMatch = FALSE)
    {
        $currValue = $this->getProperty($key);

        if ($currValue === NULL)
        {
            return FALSE;
        }

        if ($flexibleMatch == FALSE)
        {
            return $currValue === $value;
        }
        else
        {
            return strtolower((string) $currValue) == strtolower((string) $value);
        }
    }

    public function removeProperty($key)
    {
        if (isset($this->fPropertyList[$key]))
        {
            unset($this->fPropertyList[$key]);
        }
    }

}

?>