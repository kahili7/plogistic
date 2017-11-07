<?

class TIMAGE extends TWEBCONTROL
{

    public function TIMAGE()
    {
        parent::TWEBCONTROL();
    }

    public function setImageSource($source)
    {
        $this->setProperty("Src", $source);
    }

    public function getImageSource()
    {
        return $this->getProperty("Src");
    }

    public function setBlank()
    {
        $this->setImageSource("/system/assets/images/blank.gif");
    }

    public function setBinaryData($aBinaryData)
    {
        if (!$aBinaryData || strlen($aBinaryData) < 5)
        {
            $this->setBlank();
            return;
        }

        $token = randomString(20);
        TFILESYSTEM::saveFileContent(PROTECTED_PERSISTENT_DIR . "tmp/" . $token, $aBinaryData);
        $this->setImageSource("/system/assets/images/get.php?" . $token);
    }

    function toHtml()
    {
        $html = new TCONTENT();
        $html->appendText("<img id='{$this->fName}' name='{$this->fName}'");

        if ($this->hasProperty("Src"))
        {
            $html->appendText(" src='" . $this->getProperty("Src") . "'");
        }

        if ($this->hasProperty("Class"))
        {
            $html->appendText(" class='" . $this->getProperty("Class") . "'");
        }

        if ($this->hasProperty("Style"))
        {
            $html->appendText(" style='" . $this->getProperty("Style") . "'");
        }

        if ($this->hasProperty("Alt"))
        {
            $html->appendText(" alt='" . htmlspecialchars($this->getProperty("Alt"), ENT_QUOTES) . "'");
        }

        if ($this->hasProperty("Title"))
        {
            $html->appendText(" title='" . htmlspecialchars($this->getProperty("Title"), ENT_QUOTES) . "'");
        }

        $html->appendText(" />");
        return $html->toString();
    }

    protected function OnFinalize()
    {
        agent()->registerWidget($this->getName(), "TImage");
    }

    protected function OnPossibleStateChange()
    {
        if (AJAX_REQUEST && $this->hasProperty("Src"))
        {
            agent()->call("page.{$this->fName}.setImageSource", $this->getImageSource());
        }
    }

}

?>