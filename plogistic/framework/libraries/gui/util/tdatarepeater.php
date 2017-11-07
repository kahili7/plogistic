<?

class TDATAREPEATER
{

    public $header;
    public $repeat;
    public $repeatA;
    public $repeatE;
    public $separator;
    public $footer;
    private $fDataSource = NULL;
    private $fContent = "";

    public function TDATAREPEATER()
    {
        $this->header = new TCONTENT();
        $this->repeat = new TCONTENT();
        $this->repeatA = new TCONTENT();
        $this->repeatE = new TCONTENT();
        $this->separator = new TCONTENT();
        $this->footer = new TCONTENT();
    }

    /**
     * Load template from file
     *
     * @param string $filePath File path
     */
    public function loadTemplateFromFile($filePath)
    {
        $str = "";

        if (is_file($filePath))
        {
            $str = TFILESYSTEM::getFileContent($filePath);
        }
        else if (is_file(PROTECTED_TEMPLATE_DIR . $filePath))
        {
            $str = TFILESYSTEM::getFileContent(PROTECTED_TEMPLATE_DIR . $filePath);
        }
        else
        {
            fireApplicationError("TDataRepeater::loadTemplateFromFile() - Can't find file '{$filePath}'");
        }

        preg_match_all("/.*\[header\](.*)\[repeat\](.*)\[repeat\-alternate\](.*)\[repeat\-empty\](.*)\[repeat\-separator\](.*)\[footer\](.*)/ims", $str, $matches);

        if (count($matches) >= 5)
        {
            $this->header->setText(trim($matches[1][0]));
            $this->repeat->setText(trim($matches[2][0]));
            $this->repeatA->setText(trim($matches[3][0]));
            $this->repeatE->setText(trim($matches[4][0]));
            $this->separator->setText(trim($matches[5][0]));
            $this->footer->setText(trim($matches[6][0]));
        }
    }

    public function setDataSource($dataSource)
    {
        $this->fDataSource = $dataSource;
    }

    public function build($fixedItems = 0)
    {
        $str = "";
        $str .= $this->header->getText() . "\n";

        if ($this->fDataSource && $this->fDataSource->size() > 0)
        {
            $dsSize = $this->fDataSource->size();

            for ($i = 0; $i < $dsSize; $i++)
            {
                $repeatString = $this->repeat->getText();

                if ($i % 2 == 1 && $this->repeatA->getText())
                {
                    $repeatString = $this->repeatA->getText();
                }

                $patterns = Array();
                $replacements = Array();
                $record = $this->fDataSource->record($i);

                foreach ($record as $k => $v)
                {
                    $patterns[] = "/\{#" . $k . "\}/";
                    $replacements[] = $v;
                }

                $repeatString = preg_replace($patterns, $replacements, $repeatString);
                $str .= $repeatString . "\n";

                if (($i + 1) < $dsSize)
                {
                    $str .= $this->separator->getText() . "\n";
                }
            }
        }

        if ($fixedItems > 0 && $this->fDataSource->size() < $fixedItems)
        {
            $str .= $this->separator->getText() . "\n";
            $emptySize = $fixedItems - $this->fDataSource->size();

            for ($i = 0; $i < $emptySize; $i++)
            {
                $str .= $this->repeatE->getText() . "\n";

                if (($i + 1) < $emptySize)
                {
                    $str .= $this->separator->getText() . "\n";
                }
            }
        }

        $str .= $this->footer->getText();
        $this->fContent = $str;
    }

    public function getContent()
    {
        return $this->fContent;
    }

}

?>