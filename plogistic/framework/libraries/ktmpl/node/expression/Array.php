<?
class Ktmpl_Node_Expression_Array extends Ktmpl_Node_Expression
{
    public function __construct(array $elements, $lineno)
    {
        parent::__construct($elements, array(), $lineno);
    }

    public function compile(Ktmpl_Compiler $compiler)
    {
        $compiler->raw('array(');
        $first = true;

        foreach ($this->nodes as $name => $node)
        {
            if (!$first)
            {
                $compiler->raw(', ');
            }

            $first = false;

            $compiler
                ->repr($name)
                ->raw(' => ')
                ->subcompile($node)
            ;
        }

        $compiler->raw(')');
    }
}