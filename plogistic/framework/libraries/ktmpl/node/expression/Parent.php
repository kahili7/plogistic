<?
class Ktmpl_Node_Expression_Parent extends Ktmpl_Node_Expression
{
    public function __construct($name, $lineno, $tag = null)
    {
        parent::__construct(array(), array('name' => $name), $lineno, $tag);
    }

    public function compile(Ktmpl_Compiler $compiler)
    {
        $compiler
            ->raw("\$this->renderParentBlock(")
            ->string($this->getAttribute('name'))
            ->raw(", \$context, \$blocks)")
        ;
    }
}
