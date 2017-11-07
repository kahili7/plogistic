<?

class TPAGETEMPLATE extends TCONTENT
{

    static public function load($filePath, $sectionName = NULL)
    {
        $tmp = new TPAGETEMPLATE();

        if (is_file($filePath))
        {
            $tmp->loadFromFile($filePath);
        }
        else if (is_file(PROTECTED_TEMPLATE_DIR . $filePath))
        {
            $tmp->loadFromFile(PROTECTED_TEMPLATE_DIR . $filePath);
        }
        else
        {
            fireApplicationError("TPAGETEMPLATE::load() - Can't find file '{$filePath}'");
        }

        return $tmp;
    }

    private $fHtml = NULL;
    private $fBuilt = FALSE;

    public function TPAGETEMPLATE()
    {
        parent::TCONTENT();
    }

    public function setTitle($aTitle)
    {
        TPAGE::getInstance()->head->setProperty("Title", $aTitle);
        $this->fModified = TRUE;
    }

    public function setMetaTag($httpEquiv, $name, $content)
    {
        TPAGE::getInstance()->head->setMetaTag($httpEquiv, $name, $content);
        $this->fModified = TRUE;
    }

    public function setDescription($aValue)
    {
        $this->setMetaTag(NULL, "description", $aValue);
    }

    public function setKeywords($aValue)
    {
        $this->setMetaTag(NULL, "keywords", $aValue);
    }

    public function setRefresh($aValue)
    {
        $this->setMetaTag("refresh", NULL, $aValue);
    }

    public function bind($aKey, $aWebControl)
    {
        $this->replace($aKey, $aWebControl->html());
    }

    public function addJavascriptUri($uri)
    {
        TPAGE::getInstance()->head->addJavascriptUri($uri);
        $this->fModified = TRUE;
    }

    public function OnPageLoadExecute($funcName, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $priority = 4)
    {
        agent()->call($funcName, $arg1, $arg2, $arg3, $priority);
    }

    public function exec($funcName, $arg1 = NULL, $arg2 = NULL, $arg3 = NULL, $priority = 4)
    {
        $this->OnPageLoadExecute($funcName, $arg1, $arg2, $arg3, $priority);
    }

    public function build()
    {
        if ($this->fBuilt === TRUE)
        {
            return;
        }

        $tmp = $this->toString();
        $tmp = preg_replace("/<[ ]*head[ ]*>/i", "<head>" . page()->head->html(), $tmp, 1);
        $tmp = preg_replace("/<[ ]*\/[ ]*body.*>/i", page()->finalize(FALSE) . "\n</body>", $tmp, 1);

        $this->fHtml = $tmp;
        $this->fBuilt = TRUE;
    }

    public function html()
    {
        if ($this->fBuilt === FALSE)
        {
            fireApplicationError("TPageTemplate::html() - First call the 'build' method");
        }

        return $this->fHtml;
    }

}

?>