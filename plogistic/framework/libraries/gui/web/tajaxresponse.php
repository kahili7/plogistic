<?

class TAJAXRESPONSE extends TABSTRACTRESPONSE
{

    static private $myself;

    static public function getInstance()
    {
        return self::$myself;
    }

    static public function createInstance()
    {
        if (is_object(self::$myself) == TRUE)
        {
            exit("Only one istance of TAjaxResponse can be created");
            return;
        }

        self::$myself = new TAJAXRESPONSE();
        return self::$myself;
    }

    private $fCharset = NULL;
    private $fParams = Array();
    private $fScriptContent = "";
    private $fScriptCodeForExecution = Array(
        "p1" => Array(),
        "p2" => Array(),
        "p3" => Array(),
        "p4" => Array(),
        "p5" => Array()
    );
    private $fPopulateList = Array();
    private $fUriSeq = 1;
    private $fFlushed = FALSE;

    private function TAJAXRESPONSE()
    {
        parent::TABSTRACTRESPONSE();
    }

    public function setCharset($aCharset)
    {
        $this->fCharset = $aCharset;
    }

    public function addParameter($key, $value)
    {
        $this->fParams[$key] = $value;
    }

    public function addParam($key, $value)
    {
        $this->fParams[$key] = $value;
    }

    public function addParameters($arr)
    {
        foreach ($arr as $key => $value)
        {
            $this->addParameter($key, $value);
        }
    }

    public function addParams($arr)
    {
        $this->addParameters($arr);
    }

    public function setCookie($name, $value, $expires = 0)
    {
        $this->addParameter("setcookie:;:" . $name . ":;:" . $expires, $value);
    }

    public function removeCookie($name)
    {
        $this->setCookie($name, "removeme", -1);
    }

    public function populate($id, $value)
    {
        if (is_class($value, "TWEBCONTROL"))
        {
            $html = $value->html();
            $value->applyPossibleStateChange();
            $this->fPopulateList[$id] = $html;
        }
        else
        {
            $this->fPopulateList[$id] = $value;
        }
    }

    public function addScriptFile($uri, $id = NULL)
    {
        $this->addParameter("___addscript:;:" . ($this->fUriSeq++) . ":;:" . ($id == NULL ? "" : $id), $uri);
    }

    public function addScriptContent($content)
    {
        $this->fScriptContent .= $content . "\n\n";
    }

    public function addCssFile($uri, $id = NULL)
    {
        $this->addParameter("___addcss:;:" . ($this->fUriSeq++) . ":;:" . ($id == NULL ? "" : $id), $uri);
    }

    public function executeScript($script, $priority = 4)
    {
        if ($priority > 5)
        {
            throw new Exception("Priority can't be bigger than 5");
        }

        $codes = $this->fScriptCodeForExecution["p" . $priority];
        $codes[] = $script;
        $this->fScriptCodeForExecution["p" . $priority] = $codes;
    }

    private function mergeScripts()
    {
        $tmp = "";

        for ($i = 1; $i <= 5; $i++)
        {
            $scriptList = $this->fScriptCodeForExecution["p" . $i];

            for ($j = 0; $j < count($scriptList); $j++)
            {
                $tmp .= $scriptList[$j] . ";\n\n";
            }
        }

        return $tmp;
    }

    public function flush()
    {
        if ($this->fFlushed === TRUE)
        {
            return;
        }

        $this->fFlushed = TRUE;
        $page = TPAGE::getInstance();

        if ($page->getDirectLookAndFeel())
        {
            $html .= "\tpage.setLookAndFeel(" . json_encode($page->getDirectLookAndFeel()) . ");";
        }

        if ($page->getClass())
        {
            $html .= "\tdocument.body.className = , " . json_encode($page->getClass()) . ";";
        }

        $styleProps = Array();

        if ($page->getStyle())
        {
            $styleProps = explodeStyleString($page->getStyle());
        }

        $tmp = $page->style->toArray();

        if (count($tmp) > 0)
        {
            $styleProps = array_merge($styleProps, $tmp);
        }

        if (count($styleProps) > 0)
        {
            $html .= "\tapplyElementStyleProperties(document.body, " . json_encode($styleProps) . ");";
        }

        $destroyList = $page->getDestroyWidgetList();

        if ($destroyList && count($destroyList) > 0)
        {
            $this->executeScript("page.destroyWidgets(" . json_encode($destroyList) . ")");
        }

        if ($page->hasMessages())
        {
            $this->executeScript("page.showMessages(" . json_encode($page->getMessages()) . ")");
        }

        if ($page->hasErrors())
        {
            $this->executeScript("page.showErrors(" . json_encode($page->getErrors()) . ")");
        }

        if ($this->fScriptContent != "")
        {
            $this->addParameter("___addscript_c", $this->fScriptContent);
        }

        $tmpScript = $this->mergeScripts();

        if ($tmpScript != "")
        {
            $this->addParameter("___safe_exec", $tmpScript);
        }

        $propertyList = $page->model->getDataModelPropertyList();

        while (list($key, $obj) = each($propertyList))
        {
            if (isset($obj["ba"]) && $obj["ba"] === TRUE && isset($obj["val"]))
            {
                $this->addParameter("___model_property:;:" . (isset($obj["sf"]) && $obj["sf"] === TRUE ? "true" : "false") . ":" . $key, $obj["val"]);
            }
        }

        while (list($id, $value) = each($this->fPopulateList))
        {
            $this->addParameter("___set_element_value:;:" . $id, $value);
        }

        $charset = $this->fCharset;

        if (!$charset)
        {
            $charset = TLOCATE::get("SYS_PAGE_CHARSET");
        }

        if ($charset)
        {
            Header('Content-type: text/plain;charset=' . $charset);
        }

        return json_encode($this->fParams);
    }

}

?>