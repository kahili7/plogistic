<?

abstract class TABSTRACTBUTTON extends TWEBCONTROL
{

    public function TABSTRACTBUTTON()
    {
        parent::TWEBCONTROL();
    }

    public function setSubmitMode($mode)
    {
        $this->setProperty("SubmitMode", $mode);
    }

    public function getSubmitMode()
    {
        return $this->getProperty("SubmitMode");
    }

    public function setConfirmMessage($value)
    {
        $this->setProperty("ConfirmMessage", $value);
    }

    public function getConfirmMessage()
    {
        return $this->getProperty("ConfirmMessage");
    }

}

?>