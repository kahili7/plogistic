<?
abstract class Ktmpl_TokenParser implements Ktmpl_TokenParserInterface
{
    protected $parser;

    public function setParser(Ktmpl_Parser $parser)
    {
        $this->parser = $parser;
    }
}