<?

class TXMLPARSER
{

    var $parser;
    var $root;
    var $last = null;

    public function TXMLPARSER()
    {
        $this->parser = xml_parser_create();
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, 'start', 'end');
        xml_set_character_data_handler($this->parser, 'cdata');
    }

    public function parseContent($xml)
    {
        xml_parse($this->parser, $xml);
    }

    public function parseFile($xmlFile)
    {
        $xmlContent = TFILESYSTEM::getFileContent($xmlFile);
        $this->parseContent($xmlContent);
    }

    public function start($parser, $tagname, $attrs)
    {
        $node = new TXMLNODE(trim($tagname), $attrs);

        if (is_null($this->last))
        {
            $this->root = $node;
        }
        else
        {
            $node->parent = $this->last;
            $this->last->childNodes[] = $node;
        }

        $this->last = $node;
    }

    public function end($parser, $name)
    {
        $this->last = $this->last->parent;
    }

    public function cdata($parser, $data)
    {
        if (!is_null($this->last))
        {
            $this->last->content .= $data;
        }
    }

}

?>