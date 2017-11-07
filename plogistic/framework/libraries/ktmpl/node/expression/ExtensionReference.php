<?
class Ktmpl_Node_Expression_ExtensionReference extends Ktmpl_Node_Expression
{
    public function __construct($name, $lineno, $tag = null)
    {
        parent::__construct(array(), array('name' => $name), $lineno, $tag);
    }

    public function compile(Ktmpl_Compiler $compiler)
    {
        $compiler->raw(sprintf("\$this->env->getExtension('%s')", $this->getAttribute('name')));
    }
}