<?
class TPHPERRORHANDLER
{

    private $fLogToFile = TRUE;
    private $fLogToScreen = FALSE;
    public $errorTypes = NULL;

    public function getLogToFile()
    {
        return $this->fLogToFile;
    }

    public function setLogToFile($aValue)
    {
        $this->fLogToFile = $aValue;
    }

    public function getLogToScreen()
    {
        return $this->fLogToScreen;
    }

    public function setLogToScreen($aValue)
    {
        $this->fLogToScreen = $aValue;
    }

    public function TPhpErrorHandler()
    {
        $this->errorTypes = Array(
            E_ERROR => "Error",
            E_WARNING => "Warning",
            E_PARSE => "Parsing Error",
            E_NOTICE => "Notice",
            E_CORE_ERROR => "Core Error",
            E_CORE_WARNING => "Core Warning",
            E_COMPILE_ERROR => "Compile Error",
            E_COMPILE_WARNING => "Compile Warning",
            E_USER_ERROR => "User Error",
            E_USER_WARNING => "User Warning",
            E_USER_NOTICE => "User Notice",
            E_STRICT => "Runtime Notice"
        );
    }

    public function handle($errno, $errmsg, $filename, $linenum, $vars)
    {

        if ($this->fLogToFile && isset($this->errorTypes[$errno]))
        {
            $dt = date("Y-m-d H:i:s (T)");

            $err = "<errorEntry>\n";
            $err .= "\t<dateTime>" . $dt . "</dateTime>\n";
            $err .= "\t<errorNum>" . $errno . "</errorNum>\n";
            $err .= "\t<errorType>" . $this->errorTypes[$errno] . "</errorType>\n";
            $err .= "\t<errorMsg>" . $errmsg . "</errorMsg>\n";
            $err .= "\t<scriptName>" . $filename . "</scriptName>\n";
            $err .= "\t<scriptLineNum>" . $linenum . "</scriptLineNum>\n";
            $err .= "\t<phpPage>" . $_SERVER["PHP_SELF"] . "</phpPage>\n";
            $err .= "\t<ip_address>" . $_SERVER["REMOTE_ADDR"] . "</ip_address>\n";
            $err .= "</errorEntry>\n\n\n";

            errorLog($err);
        }

        if ($this->fLogToScreen && isset($this->errorTypes[$errno]))
        {
            $err = "<strong>" . $this->errorTypes[$errno] . ": </strong>" . $errmsg;
        }
        else
        {
            $err = "System error has occured.";
        }


        if (TPAGE::getInstance() && !in_array($err, TPAGE::getInstance()->getErrors()))
        {
            TPAGE::getInstance()->addError($err);
        }
    }

    public function start()
    {
        error_reporting(0);
        set_error_handler("handler_AppPhpError");
    }

}

function handler_AppPhpError($errno, $errmsg, $filename, $linenum, $vars)
{
    if (TAPPLICATION::getInstance())
    {
        TAPPLICATION::getInstance()->errorHandler->handle($errno, $errmsg, $filename, $linenum, $vars);
    }
}

?>