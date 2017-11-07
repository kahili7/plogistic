<?
class Ktmpl_Error extends Exception
{
    protected $lineno;
    protected $filename;
    protected $rawMessage;
    protected $previous;

    public function __construct($message, $lineno = -1, $filename = null, Exception $previous = null)
    {
        $this->lineno = $lineno;
        $this->filename = $filename;
        $this->rawMessage = $message;

        $this->updateRepr();

        if (version_compare(PHP_VERSION, '5.3.0', '<')) 
		{
            $this->previous = $previous;
            parent::__construct($this->message);
        } 
		else 
		{
            parent::__construct($this->message, 0, $previous);
        }
    }

    public function getTemplateFile()
    {
        return $this->filename;
    }

    public function setTemplateFile($filename)
    {
        $this->filename = $filename;
        $this->updateRepr();
    }

    public function getTemplateLine()
    {
        return $this->lineno;
    }

    public function setTemplateLine($lineno)
    {
        $this->lineno = $lineno;
        $this->updateRepr();
    }

    public function __call($method, $arguments)
    {
        if ('getprevious' == strtolower($method)) {
            return $this->previous;
        }

        throw new BadMethodCallException(sprintf('Method "Ktmpl_Error::%s()" does not exist.', $method));
    }

    protected function updateRepr()
    {
        $this->message = $this->rawMessage;

        if (null !== $this->filename) 
		{
            $this->message .= sprintf(' in %s', json_encode($this->filename));
        }

        if ($this->lineno >= 0) 
		{
            $this->message .= sprintf(' at line %d', $this->lineno);
        }
    }
}