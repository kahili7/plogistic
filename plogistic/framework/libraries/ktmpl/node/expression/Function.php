<?
class Ktmpl_Node_Expression_Function extends Ktmpl_Node_Expression
{
    public function __construct(Ktmpl_Node_Expression_Name $name, Ktmpl_NodeInterface $arguments, $lineno)
    {
        parent::__construct(array('name' => $name, 'arguments' => $arguments), array(), $lineno);
    }

    public function compile(Ktmpl_Compiler $compiler)
    {
        $function = $compiler->getEnvironment()->getFunction($this->getNode('name')->getAttribute('name'));
        
        if (false === $function)
            {
            throw new Ktmpl_Error_Syntax(sprintf('The function "%s" does not exist', $this->getNode('name')->getAttribute('name')), $this->getLine());
        }

        $compiler
            ->raw($function->compile().'(')
            ->raw($function->needsEnvironment() ? '$this->env' : '')
        ;

        if ($function->needsContext())
        {
            $compiler->raw($function->needsEnvironment() ? ', $context' : '$context');
        }

        $first = true;

        foreach ($this->getNode('arguments') as $node)
        {
            if (!$first)
            {
                $compiler->raw(', ');
            } 
            else
            {
                if ($function->needsEnvironment() || $function->needsContext())
                {
                    $compiler->raw(', ');
                }

                $first = false;
            }

            $compiler->subcompile($node);
        }

        $compiler->raw(')');
    }
}
