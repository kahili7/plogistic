<?
class Ktmpl_Node_Expression_Name extends Ktmpl_Node_Expression
{
    public function __construct($name, $lineno)
    {
        parent::__construct(array(), array('name' => $name), $lineno);
    }

    public function compile(Ktmpl_Compiler $compiler)
    {
        if ('_self' === $this->getAttribute('name'))
        {
            $compiler->raw('$this');
        } 
        elseif ('_context' === $this->getAttribute('name'))
        {
            $compiler->raw('$context');
        } 
        elseif ('_charset' === $this->getAttribute('name'))
        {
            $compiler->raw('$this->env->getCharset()');
        } 
        elseif ($compiler->getEnvironment()->isStrictVariables())
        {
            $compiler->raw(sprintf('$this->getContext($context, \'%s\', \'%s\')', $this->getAttribute('name'), $this->lineno));
        } 
        else
        {
            $compiler->raw(sprintf('(isset($context[\'%s\']) ? $context[\'%s\'] : null)', $this->getAttribute('name'), $this->getAttribute('name')));
        }
    }
}
