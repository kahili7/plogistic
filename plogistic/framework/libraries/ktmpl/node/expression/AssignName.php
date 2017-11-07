<?
class Ktmpl_Node_Expression_AssignName extends Ktmpl_Node_Expression_Name
{
    public function compile(Ktmpl_Compiler $compiler)
    {
        $compiler->raw(sprintf('$context[\'%s\']', $this->getAttribute('name')));
    }
}