<?

class TBUTTON extends TABSTRACTBUTTON
{

    public function TBUTTON()
    {
        parent::TABSTRACTBUTTON();
    }

    public function setText($value)
    {
        $this->setProperty("Text", $value);
    }

    public function getText()
    {
        return $this->getProperty("Text");
    }

    protected function toHtml()
    {
        $html = new TCONTENT();

        $html->appendText("<input type='button' id='{$this->fName}' name='{$this->fName}'");

        if ($this->hasProperty("Text"))
        {
            $html->appendText(" value='" . htmlspecialchars($this->getProperty("Text"), ENT_QUOTES) . "'");
        }

        if ($this->hasProperty("Class"))
        {
            $html->appendText(" class='" . $this->getProperty("Class") . "'");
        }

        if ($this->hasProperty("Style"))
        {
            $html->appendText(" style='" . $this->getProperty("Style") . "'");
        }

        $html->appendText(" />");
        return $html->toString();
    }

    protected function OnFinalize()
    {
        agent()->registerWidget($this->getName(), "TButton", Array(
            "mode" => $this->getProperty("SubmitMode", "post"),
            "cm" => $this->getConfirmMessage()
        ));
    }

    protected function OnPossibleStateChange()
    {
        if (AJAX_REQUEST && $this->hasProperty("Text"))
        {
            agent()->call("page.{$this->fName}.setValue", $this->getProperty("Text"));
        }
    }

}

?>