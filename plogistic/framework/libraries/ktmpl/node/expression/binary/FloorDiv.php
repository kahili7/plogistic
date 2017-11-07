<?
class Ktmpl_Node_Expression_Binary_FloorDiv extends Ktmpl_Node_Expression_Binary
{
    public function compile(Ktmpl_Compiler $compiler)
    {
        $compiler->raw('floor(');
        parent::compile($compiler);
        $compiler->raw(')');
    }

    public function operator(Ktmpl_Compiler $compiler)
    {
        return $compiler->raw('/');
    }
}