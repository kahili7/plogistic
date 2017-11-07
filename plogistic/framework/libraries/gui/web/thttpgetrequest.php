<?
class THTTPGETREQUEST
{

    public function THTTPGETREQUEST()
    {
    }

    public function getParameter($key, $defaultValue = NULL)
    {
        $value = NULL;

        if (isset($_GET[$key]))
        {
            $value = $_GET[$key];
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
        reset($_GET);

        while (list($key, $value) = each($_GET))
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