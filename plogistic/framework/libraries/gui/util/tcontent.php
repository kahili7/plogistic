<?
class TCONTENT
{

    static public function load($filePath, $sectionName = NULL)
    {
        $tmp = new TCONTENT();

        if (is_file($filePath))
        {
            $tmp->loadFromFile($filePath, $sectionName);
        }
        else if (is_file(PROTECTED_CONTENT_DIR . $filePath))
        {
            $tmp->loadFromFile(PROTECTED_CONTENT_DIR . $filePath, $sectionName);
        }
        else if (is_file(PROTECTED_TEMPLATE_DIR . $filePath))
        {
            $tmp->loadFromFile(PROTECTED_TEMPLATE_DIR . $filePath, $sectionName);
        }
        else
        {
            fireApplicationError("TCONTENT::load() - Can't find file '{$filePath}'");
        }

        return $tmp;
    }

    static public function text($aText)
    {
        $tmp = new TCONTENT();
        $tmp->setText($aText);
        return $tmp;
    }

    private $fText = "";
    private $fTokens = Array();
    private $fReplacements = Array();
    private $fExpressionTokens = Array();
    private $fExpressionReplacements = Array();
    private $fReplaceLocaleStrings = FALSE;
    protected $fModified = TRUE;

    public function TCONTENT()
    {
    }

    public function setText($text)
    {
        $this->fText = $text;
        $this->fModified = TRUE;
    }

    public function appendText($text, $flag = TRUE)
    {
        if ($flag)
        {
            $this->fText .= $text;
            $this->fModified = TRUE;
        }
    }

    public function loadFromFile($filePath, $sectionName = NULL)
    {
        $this->fText = TFILESYSTEM::getFileContent($filePath);

        if ($sectionName)
        {
            preg_match_all("/.*\[" . $sectionName . "\](.*)\[\/" . $sectionName . "\].*/ims", $this->fText, $matches);
            
            if (count($matches) >= 2)
            {
                $this->fText = trim($matches[1][0]);
            }
            else
            {
                $this->fText = "";
            }
        }

        $this->fModified = TRUE;
    }

    public function replace($oldText, $newText)
    {
        $this->fTokens[] = $oldText;

        if ($newText === NULL)
        {
            $newText = "";
        }

        $this->fReplacements[] = $newText;
        $this->fModified = TRUE;
    }

    public function replaceExpr($expr, $text)
    {
        $this->fExpressionTokens[] = $expr;

        if (!$text)
        {
            $text = "";
        }

        $this->fExpressionReplacements[] = $text;
        $this->fModified = TRUE;
    }

    public function replaceLocaleStrings()
    {
        $this->fReplaceLocaleStrings = TRUE;
        $this->fModified = TRUE;
    }

    public function i18n()
    {
        $this->replaceLocaleStrings();
    }

    public function getText()
    {
        if ($this->fModified === FALSE)
        {
            return $this->fText;
        }

        if (count($this->fTokens) > 0)
        {
            $this->fText = str_replace($this->fTokens, $this->fReplacements, $this->fText);
            $this->fTokens = Array();
            $this->fReplacements = Array();
        }

        if (count($this->fExpressionTokens) > 0)
        {
            $this->fText = str_replace($this->fExpressionTokens, $this->fExpressionReplacements, $this->fText);
            $this->fExpressionTokens = Array();
            $this->fExpressionReplacements = Array();
        }

        if ($this->fReplaceLocaleStrings === TRUE)
        {
            $tokens = Array();
            $replacements = Array();
            preg_match_all("/\{#(.*)\}/Ui", $this->fText, $matches);

            for ($i = 0; $i < count($matches[1]); $i++)
            {
                $match = $matches[1][$i];

                if (TLOCALE::exist($match))
                {
                    $tokens[] = "{#" . $match . "}";
                    $replacements[] = TLocale::get($match);
                }
            }

            $this->fText = str_replace($tokens, $replacements, $this->fText);
            $this->fReplaceLocaleStrings = FALSE;
        }

        $this->fModified = FALSE;
        return $this->fText;
    }

    public function toString()
    {
        return $this->getText();
    }

}

?>