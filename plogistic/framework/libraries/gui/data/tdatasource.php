<?

class TDATASOURCE
{

    static public function load($fileName, $trimValue = TRUE)
    {
        $tmp = new TDATASOURCE();

        if (is_file($fileName))
        {
            $tmp->loadFromXmlFile($fileName, $trimValue);
        }
        else if (is_file(PROTECTED_XML_DIR . $fileName))
        {
            $tmp->loadFromXmlFile(PROTECTED_XML_DIR . $fileName, $trimValue);
        }
        else
        {
            fireApplicationError("Can't find file '{$fileName}'");
        }

        return $tmp;
    }

    static public function xml($content, $trimValue = TRUE)
    {
        $tmp = new TDATASOURCE();
        $tmp->loadXmlContent($content, $trimValue);
        return $tmp;
    }

    static public function records($aRecords)
    {
        $tmp = new TDATASOURCE();
        $tmp->setRecords($aRecords);
        return $tmp;
    }

    private $fRecords = Array();

    public function TDATASOURCE()
    {
        
    }

    public function getRecords()
    {
        return $this->fRecords;
    }

    public function setRecords($aRecords)
    {
        $this->fRecords = $aRecords;
    }

    public function addRecord($record)
    {
        $this->fRecords[] = $record;
    }

    public function insert0($record)
    {
        array_unshift($this->fRecords, $record);
    }

    public function setRecord($index, $record)
    {
        $this->fRecords[$index] = $record;
    }

    public function getRecord($index)
    {
        return $this->fRecords[$index];
    }

    public function record($index)
    {
        return $this->fRecords[$index];
    }

    public function count()
    {
        return count($this->fRecords);
    }

    public function size()
    {
        return count($this->fRecords);
    }

    public function isEmpty()
    {
        return ($this->count() == 0);
    }

    public function isNotEmpty()
    {
        return!$this->isEmpty();
    }

    public function shuffle()
    {
        srand((float) microtime() * 1000000);
        shuffle($this->fRecords);
    }

    public function sortBy($keyColumn, $dataType = "string", $direction = SORT_ASCENDING)
    {
        TREGISTRY::setProperty("__SortBy", $keyColumn);
        TREGISTRY::setProperty("__SortByDatatype", $dataType);
        TREGISTRY::setProperty("__SortByDirection", $direction);
        usort($this->fRecords, "TDataSource_functionSortBy");
    }

    public function find($keyColumn, $value, $regexpr = FALSE)
    {
        for ($i = 0; $i < $this->count(); $i++)
        {
            $record = $this->getRecord($i);

            if (isset($record[$keyColumn]))
            {
                if (!$regexpr && $record[$keyColumn] == $value)
                {
                    return $record;
                }
                else if ($regexpr && preg_match($value, $record[$keyColumn]))
                {
                    return $record;
                }
            }
        }

        return NULL;
    }

    public function keep($keyColumn, $value, $regexpr = FALSE)
    {
        $tmpRecords = Array();

        for ($i = 0; $i < $this->count(); $i++)
        {
            $record = $this->getRecord($i);

            if (isset($record[$keyColumn]))
            {
                if (!$regexpr && $record[$keyColumn] == $value)
                {
                    $tmpRecords[] = $record;
                }
                else if ($regexpr && preg_match($value, $record[$keyColumn]))
                {
                    $tmpRecords[] = $record;
                }
            }
        }

        $this->fRecords = $tmpRecords;
    }

    public function remove($keyColumn, $value, $regexpr = FALSE)
    {
        $tmpRecords = Array();

        for ($i = 0; $i < $this->count(); $i++)
        {
            $record = $this->getRecord($i);

            if (isset($record[$keyColumn]))
            {
                if (!$regexpr && $record[$keyColumn] != $value)
                {
                    $tmpRecords[] = $record;
                }
                else if ($regexpr && !preg_match($value, $record[$keyColumn]))
                {
                    $tmpRecords[] = $record;
                }
            }
        }

        $this->fRecords = $tmpRecords;
    }

    public function getKeyValueArray($keyColumn, $valueColumn)
    {
        $result = Array();

        for ($i = 0; $i < $this->count(); $i++)
        {
            $record = $this->getRecord($i);

            if (isset($record[$keyColumn]) && isset($record[$valueColumn]))
            {
                $result[$record[$keyColumn]] = $record[$valueColumn];
            }
        }

        return $result;
    }

    public function getArray($columnName)
    {
        $result = Array();

        for ($i = 0; $i < $this->count(); $i++)
        {
            $record = $this->getRecord($i);

            if (isset($record[$columnName]))
            {
                $result[] = $record[$columnName];
            }
        }

        return $result;
    }

    public function loadXmlContent($content, $trimValue = TRUE)
    {
        $xmlParser = new TXMLPARSER();
        $xmlParser->parseContent($content);
        $arrayRecords = $xmlParser->root->getChildNodes();

        foreach ($arrayRecords as $recordNode)
        {
            $record = Array();

            foreach ($recordNode->childNodes as $columnNode)
            {
                $props = $columnNode->attr;

                if (isset($props["name"]))
                {
                    $record[$props["name"]] = $trimValue ? trim($columnNode->content) : $columnNode->content;
                }
            }

            $this->addRecord($record);
        }
    }

    public function loadFromXmlFile($filePath, $trimValue = TRUE)
    {
        $this->loadXmlContent(TFILESYSTEM::getFileContent($filePath), $trimValue);
    }

}

function TDataSource_functionSortBy($a, $b)
{

    $sortBy = TREGISTRY::getProperty("__SortBy");
    $sortByDatatype = TREGISTRY::getProperty("__SortByDatatype");
    $sortByDirection = TREGISTRY::getProperty("__SortByDirection");

    if ($sortByDatatype == "string" && $sortByDirection == SORT_ASCENDING)
    {
        return strcmp($a[$sortBy], $b[$sortBy]);
    }
    else if ($sortByDatatype == "string" && $sortByDirection == SORT_DESCENDING)
    {
        return strcmp($b[$sortBy], $a[$sortBy]);
    }

    if ($sortByDatatype == "int" && $sortByDirection == SORT_ASCENDING)
    {
        $v1 = (int) $a[$sortBy];
        $v2 = (int) $b[$sortBy];

        if ($v1 == $v2)
            return 0;

        if ($v1 < $v2)
            return -1;

        if ($v1 > $v2)
            return 1;
    }
    else if ($sortByDatatype == "int" && $sortByDirection == SORT_DESCENDING)
    {
        $v1 = (int) $a[$sortBy];
        $v2 = (int) $b[$sortBy];

        if ($v1 == $v2)
            return 0;

        if ($v1 < $v2)
            return 1;

        if ($v1 > $v2)
            return -1;
    }
}

?>