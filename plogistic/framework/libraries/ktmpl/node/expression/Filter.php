<?
class Ktmpl_Node_Expression_Filter extends Ktmpl_Node_Expression
{
    public function __construct(Ktmpl_NodeInterface $node, Ktmpl_Node_Expression_Constant $filterName, Ktmpl_NodeInterface $arguments, $lineno, $tag = null)
    {
        parent::__construct(array('node' => $node, 'filter' => $filterName, 'arguments' => $arguments), array(), $lineno, $tag);
    }

    public function compile(Ktmpl_Compiler $compiler)
    {
        $name = $this->getNode('filter')->getAttribute('value');

        if (false === $filter = $compiler->getEnvironment()->getFilter($name))
        {
            throw new Ktmpl_Error_Syntax(sprintf('The filter "%s" does not exist', $name), $this->getLine());
        }

        if ('default' === $name && ($this->getNode('node') instanceof Ktmpl_Node_Expression_Name || $this->getNode('node') instanceof Ktmpl_Node_Expression_GetAttr))
        {
            $compiler->raw('((');

            if ($this->getNode('node') instanceof Ktmpl_Node_Expression_Name)
            {
                $testMap = $compiler->getEnvironment()->getTests();
                $compiler
                    ->raw($testMap['defined']->compile().'(')
                    ->repr($this->getNode('node')->getAttribute('name'))
                    ->raw(', $context)')
                ;
            } 
            elseif ($this->getNode('node') instanceof Ktmpl_Node_Expression_GetAttr)
            {
                $this->getNode('node')->setAttribute('is_defined_test', true);
                $compiler->subcompile($this->getNode('node'));
            }

            $compiler->raw(') ? (');
            $this->compileFilter($compiler, $filter);
            $compiler->raw(') : (');
            $compiler->subcompile($this->getNode('arguments')->getNode(0));
            $compiler->raw('))');
        } 
        else
        {
            $this->compileFilter($compiler, $filter);
        }
    }

    protected function compileFilter(Ktmpl_Compiler $compiler, Ktmpl_FilterInterface $filter)
    {
        $compiler
            ->raw($filter->compile().'(')
            ->raw($filter->needsEnvironment() ? '$this->env, ' : '')
            ->raw($filter->needsContext() ? '$context, ' : '')
        ;

        $this->getNode('node')->compile($compiler);

        foreach ($this->getNode('arguments') as $node) {
            $compiler
                ->raw(', ')
                ->subcompile($node)
            ;
        }

        $compiler->raw(')');
    }
}