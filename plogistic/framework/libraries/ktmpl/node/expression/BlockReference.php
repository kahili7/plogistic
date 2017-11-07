<?
class Ktmpl_Node_Expression_BlockReference extends Ktmpl_Node_Expression
{
    public function __construct(Ktmpl_NodeInterface $name, $asString = false, $lineno, $tag = null)
    {
        parent::__construct(array('name' => $name), array('as_string' => $asString), $lineno, $tag);
    }

    public function compile(Ktmpl_Compiler $compiler)
    {
        if ($this->getAttribute('as_string'))
        {
            $compiler->raw('(string) ');
        }

        $compiler
            ->raw("\$this->renderBlock(")
            ->subcompile($this->getNode('name'))
            ->raw(", \$context, \$blocks)")
        ;
    }
}