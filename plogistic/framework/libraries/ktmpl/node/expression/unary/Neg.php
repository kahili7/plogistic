<?
class Ktmpl_Node_Expression_Unary_Neg extends Ktmpl_Node_Expression_Unary
{
    public function operator(Ktmpl_Compiler $compiler)
    {
        $compiler->raw('-');
    }
}