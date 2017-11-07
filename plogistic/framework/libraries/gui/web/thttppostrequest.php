<?
class THTTPPOSTREQUEST
{

    public function THTTPPOSTREQUEST()
    {
    }

    public function getParameter($key, $defaultValue = NULL)
    {
        $value = NULL;

        if (isset($_POST[$key]))
        {
            $value = $_POST[$key];
        }

        if ($value === NULL && $defaultValue !== NULL)
        {
            $value = $defaultValue;
        }

        return $value;
    }

    public function getParam($name, $defaultValue = NULL)
    {
        return $this->getParameter($name, $defaultValue);
    }

    public function getParameters($regexpr = NULL)
    {
        $list = Array();
        reset($_POST);
        
        while (list($key, $value) = each($_POST))
        {
            if (!$regexpr)
            {
                $list[$key] = $value;
            }
            else if (preg_match($regexpr, $key))
            {
                $list[$key] = $value;
            }
        }
        return $list;
    }

}

?>