<?

class TXMLNODE
{

    public $tagName;
    public $attr = Array();
    public $content;
    public $childNodes;
    public $parent = null;

    public function TXMLNODE($tagname, $attributes)
    {
        $this->tagName = strtolower($tagname);

        foreach ($attributes as $key => $value)
        {
            $this->attr[strtolower($key)] = $value;
        }

        $this->attr["tagname"] = $this->tagName;
        $this->childNodes = Array();
    }

    public function getAttribute($key)
    {
        $key = strtolower($key);
        return (isset($this->attr[$key]) ? $this->attr[$key] : NULL);
    }

    public function hasChildNodes()
    {
        return count($this->childNodes) > 0;
    }

    public function getSubNodeContent($tagName)
    {
        $tagName = strtolower($tagName);

        foreach ($this->childNodes as $child)
        {
            if ($child->tagName == strtolower($tagName))
            {
                return $child->content;
            }
        }

        return NULL;
    }

    public function getChildNodesByTagName($nodeName)
    {
        $nodeName = strtolower($nodeName);
        $out = Array();

        foreach ($this->childNodes as $child)
        {
            if ($child->tagName == $nodeName)
            {
                $out[] = $child;
            }
        }

        return $out;
    }

    public function getChildNodes()
    {
        $out = Array();

        foreach ($this->childNodes as $child)
        {
            $out[] = $child;
        }

        return $out;
    }

    public function findChildByAttr($attrName, $attrValue)
    {
        if ($this->getAttribute($attrName) == $attrValue)
        {
            return $this;
        }

        foreach ($this->childNodes as $child)
        {
            $found = $child->findChildByAttr($attrName, $attrValue);

            if ($found)
            {
                return $found;
            }
        }

        return NULL;
    }

}

?>