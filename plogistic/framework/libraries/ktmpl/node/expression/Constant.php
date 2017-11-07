<?
class Ktmpl_Node_Expression_Constant extends Ktmpl_Node_Expression
{
    public function __construct($value, $lineno)
    {
        parent::__construct(array(), array('value' => $value), $lineno);
    }

    public function compile(Ktmpl_Compiler $compiler)
    {
        $compiler->repr($this->getAttribute('value'));
    }
}