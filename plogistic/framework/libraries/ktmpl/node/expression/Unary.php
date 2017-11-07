<?
abstract class Ktmpl_Node_Expression_Unary extends Ktmpl_Node_Expression
{
    public function __construct(Ktmpl_NodeInterface $node, $lineno)
    {
        parent::__construct(array('node' => $node), array(), $lineno);
    }

    public function compile(Ktmpl_Compiler $compiler)
    {
        $compiler->raw('(');
        $this->operator($compiler);
        $compiler
            ->subcompile($this->getNode('node'))
            ->raw(')')
        ;
    }

    abstract public function operator(Ktmpl_Compiler $compiler);
}