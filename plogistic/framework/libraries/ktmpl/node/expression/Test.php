<?
class Ktmpl_Node_Expression_Test extends Ktmpl_Node_Expression
{
    public function __construct(Ktmpl_NodeInterface $node, $name, Ktmpl_NodeInterface $arguments = null, $lineno)
    {
        parent::__construct(array('node' => $node, 'arguments' => $arguments), array('name' => $name), $lineno);
    }

    public function compile(Ktmpl_Compiler $compiler)
    {
        $testMap = $compiler->getEnvironment()->getTests();

        if (!isset($testMap[$this->getAttribute('name')]))
        {
            throw new Ktmpl_Error_Syntax(sprintf('The test "%s" does not exist', $this->getAttribute('name')), $this->getLine());
        }

        if ('defined' === $this->getAttribute('name'))
        {
            if ($this->getNode('node') instanceof Ktmpl_Node_Expression_Name)
            {
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
            else
            {
                throw new Ktmpl_Error_Syntax('The "defined" test only works with simple variables', $this->getLine());
            }

            return;
        }

        $compiler
            ->raw($testMap[$this->getAttribute('name')]->compile().'(')
            ->subcompile($this->getNode('node'))
        ;

        if (null !== $this->getNode('arguments'))
        {
            $compiler->raw(', ');
            $max = count($this->getNode('arguments')) - 1;

            foreach ($this->getNode('arguments') as $i => $node)
            {
                $compiler->subcompile($node);

                if ($i != $max)
                {
                    $compiler->raw(', ');
                }
            }
        }

        $compiler->raw(')');
    }
}