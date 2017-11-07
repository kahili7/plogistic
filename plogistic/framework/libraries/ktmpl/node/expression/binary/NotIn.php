<?
class Ktmpl_Node_Expression_Binary_NotIn extends Ktmpl_Node_Expression_Binary
{
    public function compile(Ktmpl_Compiler $compiler)
    {
        $compiler
            ->raw('!ktmpl_in_filter(')
            ->subcompile($this->getNode('left'))
            ->raw(', ')
            ->subcompile($this->getNode('right'))
            ->raw(')')
        ;
    }

    public function operator(Ktmpl_Compiler $compiler)
    {
        return $compiler->raw('not in');
    }
}