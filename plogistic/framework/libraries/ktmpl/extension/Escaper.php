<?
class Ktmpl_Extension_Escaper extends Ktmpl_Extension
{
    protected $autoescape;

    public function __construct($autoescape = true)
    {
        $this->autoescape = $autoescape;
    }

    public function getTokenParsers()
    {
        return array(new Ktmpl_TokenParser_AutoEscape());
    }

    public function getNodeVisitors()
    {
        return array(new Ktmpl_NodeVisitor_Escaper());
    }

    public function getFilters()
    {
        return array(
            'raw' => new Ktmpl_Filter_Function('ktmpl_raw_filter', array('is_safe' => array('all'))),
        );
    }

    public function isGlobal()
    {
        return $this->autoescape;
    }

    public function getName()
    {
        return 'escaper';
    }
}

function ktmpl_raw_filter($string)
{
    return $string;
}