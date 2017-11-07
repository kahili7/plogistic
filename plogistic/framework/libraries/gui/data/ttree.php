<?

class TTREE extends TTREENODE
{

    private $fNodePrefix = "node";

    public function TTREE()
    {
        parent::TTREENODE();
    }

    public function setNodePrefix($aPrefix)
    {
        $this->fNodePrefix = $aPrefix;
    }

    public function loadXmlContent($content)
    {
        $xmlParser = new TXMLPARSER();
        $xmlParser->parseContent($content);

        $this->id = "root";
        $this->attr = $xmlParser->root->attr;

        $this->counter = 0;
        $this->subNodes = Array();


        $nodeArr = $xmlParser->root->getChildNodes();

        foreach ($nodeArr as $nodeObj)
        {
            $this->visitXmlNode($nodeObj, $this->id);
        }
    }

    public function loadFromXmlFile($fileName)
    {
        $xmlParser = new TXMLPARSER();
        $xmlParser->parseFile($fileName);

        $this->id = "root";
        $this->attr = $xmlParser->root->attr;

        $this->counter = 0;
        $this->subNodes = Array();

        $nodeArr = $xmlParser->root->getChildNodes();

        foreach ($nodeArr as $nodeObj)
        {
            $this->visitXmlNode($nodeObj, $this->id);
        }
    }

    private function visitXmlNode($xmlNode, $parent)
    {
        $id = $this->fNodePrefix . ++$this->counter;
        $p = $this->findNode($parent);

        if ($p)
        {
            $node = new TTREENODE();
            $node->id = $id;
            $node->attr = $xmlNode->attr;
            $node->content = trim($xmlNode->content);
            $p->addNode($node);
        }

        if ($xmlNode->hasChildNodes())
        {
            foreach ($xmlNode->childNodes as $childNode)
            {
                $this->visitXmlNode($childNode, $id);
            }
        }
    }

}

class TTREENODE
{

    var $id = NULL;
    var $attr = Array();
    var $content = "";
    var $subNodes = Array();
    var $parent = NULL;
    var $level = 0;
    var $counter;

    public function TTREENODE()
    {
        
    }

    public function addNode($node)
    {
        $node->parent = $this->id;
        $node->level = $this->level + 1;
        $this->subNodes[$node->id] = $node;
    }

    public function findNode($id)
    {
        if ($this->id == $id)
        {
            return $this;
        }

        if (isset($this->subNodes[$id]) && !is_null($this->subNodes[$id]))
        {
            return $this->subNodes[$id];
        }

        foreach ($this->subNodes as $subNodeId => $subNode)
        {
            $searchedNode = $subNode->findNode($id);

            if ($searchedNode != NULL)
            {
                return $searchedNode;
            }
        }

        return NULL;
    }

    public function findSubNodesByTagName($tagName)
    {
        $arr = Array();

        foreach ($this->subNodes as $subNodeId => $subNode)
        {
            if ($subNode->attr["tagname"] == $tagName)
            {
                $arr[] = $subNode;
            }
        }

        return $arr;
    }

    public function findSubNodeByTagName($tagName)
    {
        foreach ($this->subNodes as $subNodeId => $subNode)
        {
            if ($subNode->attr["tagname"] == $tagName)
            {
                return $subNode;
            }
        }

        return NULL;
    }

    public function removeNode($id)
    {
        if (isset($this->subNodes[$id]))
        {
            unset($this->subNodes[$id]);
            return TRUE;
        }
        else
        {
            foreach ($this->subNodes as $subNodeId => $subNode)
            {
                $flag = $subNode->removeNode($id);

                if ($flag === TRUE)
                {
                    return TRUE;
                }
            }
        }

        return FALSE;
    }

    public function visit($funcName)
    {
        $funcName($this);

        foreach ($this->subNodes as $subNodeId => $subNode)
        {
            $subNode->visit($funcName);
        }
    }

    public function sortBy($attrKey, $dataType = "string", $direction = SORT_ASCENDING)
    {
        TREGISTRY::setProperty("__SortBy", $attrKey);
        TREGISTRY::setProperty("__SortByDatatype", $dataType);
        TREGISTRY::setProperty("__SortByDirection", $direction);
        uasort($this->subNodes, "TTreeNode_onSortBy");

        while (list($subNodeId, ) = each($this->subNodes))
        {
            $n = $this->findNode($subNodeId);

            if ($n != NULL)
            {
                $n->sortBy($attrKey, $dataType, $direction);
            }
        }
    }

}

function TTreeNode_onSortBy($a, $b)
{

    $sortBy = TREGISTRY::getProperty("__SortBy");
    $sortByDatatype = TREGISTRY::getProperty("__SortByDatatype");
    $sortByDirection = TREGISTRY::getProperty("__SortByDirection");

    if (isset($a->attr[$sortBy]) && !isset($b->attr[$sortBy]))
    {
        return 1;
    }

    if (!isset($a->attr[$sortBy]) && isset($b->attr[$sortBy]))
    {
        return -1;
    }

    if (!isset($a->attr[$sortBy]) && !isset($b->attr[$sortBy]))
    {
        return 0;
    }

    if ($sortByDatatype == "string" && $sortByDirection == SORT_ASCENDING)
    {
        return strcmp($a->attr[$sortBy], $b->attr[$sortBy]);
    }
    else if ($sortByDatatype == "string" && $sortByDirection == SORT_DESCENDING)
    {
        return strcmp($b->attr[$sortBy], $a->attr[$sortBy]);
    }

    if ($sortByDatatype == "int" && $sortByDirection == SORT_ASCENDING)
    {
        $v1 = (int) $a->attr[$sortBy];
        $v2 = (int) $b->attr[$sortBy];

        if ($v1 == $v2)
            return 0;

        if ($v1 < $v2)
            return -1;

        if ($v1 > $v2)
            return 1;
    }
    else if ($sortByDatatype == "int" && $sortByDirection == SORT_DESCENDING)
    {
        $v1 = (int) $a->attr[$sortBy];
        $v2 = (int) $b->attr[$sortBy];

        if ($v1 == $v2)
            return 0;

        if ($v1 < $v2)
            return 1;

        if ($v1 > $v2)
            return -1;
    }
}

?>