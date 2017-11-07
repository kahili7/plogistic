<?
class Ktmpl_Node_Expression_Binary_Range extends Ktmpl_Node_Expression_Binary
{
    public function compile(Ktmpl_Compiler $compiler)
    {
        $compiler
            ->raw('range(')
            ->subcompile($this->getNode('left'))
            ->raw(', ')
            ->subcompile($this->getNode('right'))
            ->raw(')')
        ;
    }

    public function operator(Ktmpl_Compiler $compiler)
    {
        return $compiler->raw('..');
    }
}