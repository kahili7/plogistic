<?

class THTTPAGENTEVENT
{

    private $fExecutableCode = Array();
    private $fLastExecutableCode = Array();
    private $fSystemExecutableCode = Array();
    private $fScriptCodeForExecution = Array(
        "p1" => Array(),
        "p2" => Array(),
        "p3" => Array(),
        "p4" => Array(),
        "p5" => Array()
    );

    public function THTTPAGENTEVENT()
    {
        
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

    public function getExecutableCode()
    {
        $tmp = Array();

        for ($i = 1; $i <= 5; $i++)
        {
            $tmp = array_merge($tmp, $this->fScriptCodeForExecution["p" . $i]);
        }

        return $tmp;
    }

    public function systemExecute($code)
    {
        $this->fSystemExecutableCode[] = $code;
    }

    public function getSystemExecutableCode()
    {
        return $this->fSystemExecutableCode;
    }

}

?>