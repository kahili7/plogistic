<?
class Ktmpl_Node_Expression_GetAttr extends Ktmpl_Node_Expression
{
    public function __construct(Ktmpl_Node_Expression $node, Ktmpl_Node_Expression $attribute, Ktmpl_NodeInterface $arguments, $type, $lineno)
    {
        parent::__construct(array('node' => $node, 'attribute' => $attribute, 'arguments' => $arguments), array('type' => $type), $lineno);
    }

    public function compile(Ktmpl_Compiler $compiler)
    {
        $compiler
            ->raw('$this->getAttribute(')
            ->subcompile($this->getNode('node'))
            ->raw(', ')
            ->subcompile($this->getNode('attribute'))
            ->raw(', array(')
        ;

        foreach ($this->getNode('arguments') as $node)
        {
            $compiler
                ->subcompile($node)
                ->raw(', ')
            ;
        }

        $compiler
            ->raw('), ')
            ->repr($this->getAttribute('type'))
            ->raw($this->hasAttribute('is_defined_test') ? ', true' : ', false')
            ->raw(sprintf(', %d', $this->lineno))
            ->raw(')');
    }
}