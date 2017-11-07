<?

class THIDDENFIELD extends TWEBCONTROL
{

    public function THIDDENFIELD()
    {
        parent::TWEBCONTROL();
    }

    protected function toHtml()
    {
        $html = new TCONTENT();
        $html->appendText("<input type='hidden' id='{$this->fName}' name='{$this->fName}'");

        if ($this->getValue())
        {
            $html->appendText(" value='" . htmlspecialchars($this->getValue(), ENT_QUOTES) . "'");
        }
        else if ($this->hasProperty("InitialValue"))
        {
            $html->appendText(" value='" . htmlspecialchars($this->getProperty("InitialValue"), ENT_QUOTES) . "'");
        }

        $html->appendText(" />");
        return $html->toString();
    }

    protected function OnPostBackData()
    {
        $this->setValue(request()->getParam($this->getName()), FALSE);
    }

    protected function OnFinalize()
    {
        agent()->registerWidget($this->getName(), "THiddenField");
    }

    protected function OnPossibleStateChange()
    {
        if (AJAX_REQUEST && $this->isValueChanged())
        {
            agent()->call("page.{$this->fName}.setValue", $this->getValue());
        }
    }

}

?>