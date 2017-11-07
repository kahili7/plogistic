<?
abstract class TWEBCONTROL
{

    static public function createInstance($name, $clazz, $args = Array(), $finalizeInstance = TRUE)
    {
        $tmp = new $clazz();
        $tmp->setName($name);
        $tmp->saveArgs($args);
        $tmp->init();
        $tmp->postBackData();

        if ($finalizeInstance === TRUE)
        {
            $tmp->finalize();
        }

        return $tmp;
    }

    protected $fName;
    protected $fValue = NULL;

    public $style = NULL;

    protected $fKeepState = NULL;
    private $fRegistrable = FALSE;
    protected $fContext = NULL;

    public $parent = NULL;
    public $codeProps = Array();
    public $designProps = Array();
    public $fFiredPostBackEvent = FALSE;

    private $fErrorMessage = NULL;

    public function TWEBCONTROL()
    {
        $this->style = new TSTYLE();
        $this->fKeepState = FALSE;
    }

    public function setName($aName)
    {
        $this->fName = $aName;
    }

    public function getName()
    {
        return $this->fName;
    }

    public function setKeepState($aValue)
    {
        $this->fKeepState = $aValue;
    }

    public function isKeepState()
    {
        return $this->fKeepState;
    }

    public function setContext($aValue)
    {
        $this->fContext = $aValue;
    }

    public function getContext()
    {
        return $this->fContext;
    }

    public function setRegistrable($aValue)
    {
        $this->fRegistrable = $aValue;
    }

    protected function isRegistrable()
    {
        return $this->fRegistrable;
    }

    public function hasFiredPostBackEvent()
    {
        return $this->fFiredPostBackEvent;
    }

    public function setErrorMessage($aMessage)
    {
        $this->fErrorMessage = $aMessage;
    }

    public function getErrorMessage()
    {
        return $this->fErrorMessage;
    }

    public function saveArgs($args)
    {
        if (!$args)
            $args = Array();

        $args = array_change_key_case($args, CASE_LOWER);

        while (list($key, $value) = each($args))
        {
            $this->designProps[$key] = $value;
        }
    }

    public function setProperties($props)
    {
        while (list($key, $value) = each($props))
        {
            $this->setProperty($key, $value);
        }
    }

    public function getProperty($key, $defaultValue = NULL)
    {
        $key = strtolower($key);

        if (isset($this->codeProps[$key]) && $this->codeProps[$key] !== NULL)
        {
            return $this->codeProps[$key];
        }
        else if (isset($this->designProps[$key]) && $this->designProps[$key] !== NULL)
        {
            return $this->designProps[$key];
        }
        else if ($defaultValue !== NULL)
        {
            return $defaultValue;
        }

        return NULL;
    }

    public function setProperty($key, $value)
    {
        $this->setCodeProperty($key, $value);
    }

    public function hasProperty($key, $value = NULL, $flexibleMatch = FALSE)
    {
        $key = strtolower($key);

        if (isset($this->codeProps[$key]) || isset($this->designProps[$key]))
        {
            if ($value === NULL)
            {
                return TRUE;
            }

            $propertyValue = $this->getProperty($key);

            if ($propertyValue === NULL)
            {
                return FALSE;
            }

            if ($flexibleMatch == FALSE)
            {
                return $propertyValue === $value;
            }
            else
            {
                return ($propertyValue == $value) || (strtolower((string) $propertyValue) == strtolower((string) $value));
            }
        }

        return FALSE;
    }

    public function removeProperty($key)
    {
        $key = strtolower($key);

        if (isset($this->codeProps[$key]))
        {
            unset($this->codeProps[$key]);
        }

        if (isset($this->designProps[$key]))
        {
            unset($this->designProps[$key]);
        }
    }

    public function setCodeProperty($key, $value)
    {
        $key = strtolower($key);
        $this->codeProps[$key] = $value;
    }

    public function getCodeProperty($key, $defaultValue = NULL)
    {
        $key = strtolower($key);

        if (isset($this->codeProps[$key]) && $this->codeProps[$key] != NULL)
        {
            return $this->codeProps[$key];
        }
        else if ($defaultValue != NULL)
        {
            return $defaultValue;
        }

        return NULL;
    }

    public function setDesignProperty($key, $value)
    {
        $key = strtolower($key);
        $this->designProps[$key] = $value;
    }

    public function getDesignProperty($key, $defaultValue = NULL)
    {
        $key = strtolower($key);

        if (isset($this->designProps[$key]) && $this->designProps[$key] != NULL)
        {
            return $this->designProps[$key];
        }
        else if ($defaultValue != NULL)
        {
            return $defaultValue;
        }

        return NULL;
    }

    public function setValue($aValue, $aValueChanged = TRUE)
    {
        $this->fValue = $aValue;

        if ($aValueChanged === TRUE)
        {
            $this->setProperty("ValueChanged", TRUE);
        }
    }

    public function getValue()
    {
        if ($this->fValue !== NULL)
        {
            return $this->fValue;
        }

        if ($this->hasProperty("value"))
        {
            return $this->getProperty("value");
        }

        return NULL;
    }

    public function isValueChanged()
    {
        return $this->hasProperty("ValueChanged", TRUE);
    }

    public function setEnabled($flag)
    {
        $this->setProperty("enabled", $flag);
    }

    public function isEnabled()
    {
        return (!$this->hasProperty("enabled", FALSE));
    }

    public function setVisible($flag)
    {
        $this->setProperty("visible", $flag);
    }

    public function isVisible()
    {
        return (!$this->hasProperty("visible", FALSE));
    }

    public function setDisplay($flag)
    {
        $this->setProperty("display", $flag);
    }

    public function setClass($value)
    {
        $this->setProperty("class", $value);
    }

    public function getClass()
    {
        return $this->getProperty("class");
    }

    public function setStyle($value)
    {
        $this->setProperty("style", $value);
    }

    public function getStyle()
    {
        return $this->getProperty("style");
    }

    public function getWidgetName()
    {
        return TREGISTRY::getProperty("__widget__" . get_class($this));
    }

    public function getWidgetDirectory()
    {
        $widgetName = $this->getWidgetName();

        if ($widgetName)
        {
            return PROTECTED_WIDGETS_DIR . $widgetName . DIRECTORY_SEPARATOR;
        }
        else
        {
            return "";
        }
    }

    public function file($resourceRelativePath)
    {
        $widgetName = $this->getWidgetName();

        if ($widgetName)
        {
            return PROTECTED_WIDGETS_DIR . $widgetName . DIRECTORY_SEPARATOR . $resourceRelativePath;
        }
        else
        {
            return "";
        }
    }

    public function getWidgetPath()
    {
        $widgetName = $this->getWidgetName();

        if ($widgetName)
        {
            return "/system/widgets/" . $widgetName . "/";
        }
        else
        {
            return "";
        }
    }

    public function uri($resourceRelativePath)
    {
        $widgetName = $this->getWidgetName();

        if ($widgetName)
        {
            return "/system/widgets/" . $widgetName . "/" . $resourceRelativePath;
        }
        else
        {
            return "";
        }
    }

    public function loadLocaleStrings($locale)
    {
        $widgetName = $this->getWidgetName();

        if ($widgetName)
        {
            $file = PROTECTED_I18N_DIR . $locale . DIRECTORY_SEPARATOR . $widgetName . ".i18n";

            if (file_exists($file))
            {
                $assocArray = parse_ini_file($file);

                while (list($k, $v) = each($assocArray))
                {
                    TLOCATE::put($k, $v);
                }

                return;
            }

            $file = PROTECTED_WIDGETS_DIR . $widgetName . DIRECTORY_SEPARATOR . "i18n" . DIRECTORY_SEPARATOR . $locale;
            
            if (file_exists($file))
            {
                $assocArray = parse_ini_file($file);

                while (list($k, $v) = each($assocArray))
                {
                    TLOCATE::put($k, $v);
                }

                return;
            }

            if ($locale != "en")
            {
                $this->loadLocaleStrings("en");
            }
        }
    }

    public function render($args = Array())
    {
        $this->saveArgs($args);
        echo $this->toHtml();
    }

    public function html($args = Array())
    {
        $this->saveArgs($args);
        return $this->toHtml();
    }

    protected function toHtml()
    {
        return "";
    }

    public function init()
    {
        $this->OnInit();
    }

    protected function OnInit()
    {
    }

    public function postBackData()
    {
        $this->OnPostBackData();
    }

    protected function OnPostBackData()
    {
    }

    public function OnPostBackEvent()
    {
        $this->fFiredPostBackEvent = TRUE;
    }

    public function loadState()
    {
        $this->OnLoadState();
    }

    protected function OnLoadState()
    {
        if ($this->fKeepState)
        {
            $temp = $this->fContext->controlState->getProperty($this->fName);

            if ($temp)
            {
                $this->codeProps = $temp;
            }
        }
    }

    public function saveState()
    {
        $this->OnSaveState();
    }

    protected function OnSaveState()
    {
        if ($this->fKeepState)
        {
            $this->fContext->controlState->setProperty($this->fName, $this->codeProps);
        }
    }

    public function finalize()
    {
        $this->OnFinalize();
    }

    protected function OnFinalize()
    {
        //
    }

    public function applyPossibleStateChange()
    {
        if ($this->hasProperty("Class") && (AJAX_REQUEST || $this->hasProperty("ClassNotSet", TRUE)))
        {
            agent()->call("page.{$this->fName}.applyClass", $this->getClass());
        }

        $styleProps = Array();

        if ($this->hasProperty("Style") && (AJAX_REQUEST || $this->hasProperty("StyleNotSet", TRUE)))
        {
            $styleProps = explodeStyleString($this->getStyle());
        }

        $tmp = $this->style->toArray();

        if (count($tmp) > 0)
        {
            $styleProps = array_merge($styleProps, $tmp);
        }

        if (count($styleProps) > 0)
        {
            agent()->call("page.{$this->fName}.applyStyleProperties", $styleProps);
        }

        if ($this->hasProperty("enabled"))
        {
            agent()->call("page.{$this->fName}.setEnabled", ($this->isEnabled() ? TRUE : FALSE));
        }

        if ($this->hasProperty("visible"))
        {
            agent()->call("page.{$this->fName}.setVisible", ($this->isVisible() ? TRUE : FALSE));
        }

        if ($this->hasProperty("display"))
        {
            agent()->call("page.{$this->fName}.setDisplay", $this->getProperty("display"));
        }

        if ($this->fErrorMessage !== NULL)
        {
            agent()->call("page.{$this->fName}.setErrorMessage", $this->fErrorMessage);
        }

        $this->OnPossibleStateChange();
    }

    protected function OnPossibleStateChange()
    {
        //
    }

}

function errorFor($target, $args = Array())
{
    $args = array_change_key_case($args, CASE_LOWER);
    $style = "color:#FF0000";

    if (isset($args["style"]))
    {
        $style = $args["style"];
    }

    $html = "";
    $html .= "<span id='{$target}_error'";

    if (isset($args["class"]))
    {
        $html .= " class='" . $args["class"] . "'";
    }

    $style = "color:#FF0000;";

    if (isset($args["style"]))
    {
        $style = $args["style"];
    }

    $html .= " style='" . $style . "'";
    $html .= ">";
    $html .= "</span>";
    return $html;
}

?>