<?
class Ktmpl_Node_Expression_Binary_Concat extends Ktmpl_Node_Expression_Binary
{
    public function operator(Ktmpl_Compiler $compiler)
    {
        return $compiler->raw('.');
    }
}