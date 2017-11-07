<?

class TCHECKBOX extends TWEBCONTROL
{

    public function TCHECKBOX()
    {
        parent::TWEBCONTROL();
    }

    public function isChecked()
    {
        if ($this->getValue() !== NULL && $this->getValue() === TRUE)
        {
            return TRUE;
        }

        return FALSE;
    }

    public function setChecked($flag)
    {
        $this->setValue($flag);
    }

    protected function toHtml()
    {
        $html = new TCONTROL();

        $html->appendText("<input type='checkbox' id='{$this->fName}' name='{$this->fName}'");

        if ($this->getValue() !== NULL && $this->isChecked())
        {
            $html->appendText(" checked='checked'");
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

    protected function OnPostBackData()
    {
        $val = request()->getParam($this->fName);
        $this->setChecked($val == "on" ? TRUE : FALSE);
        $this->removeProperty("ValueChanged");
    }

    protected function OnFinalize()
    {
        agent()->registerWidget($this->getName(), "TCHECKBOX", Array(
            "mode" => $this->getProperty("SubmitMode", "none")
        ));
    }

    protected function OnPossibleStateChange()
    {
        if (AJAX_REQUEST && $this->isValueChanged())
        {
            agent()->call("page.{$this->fName}.setChecked", $this->isChecked());
        }
    }

}

?>