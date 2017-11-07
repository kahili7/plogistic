<?
class Ktmpl_NodeVisitor_Optimizer implements Ktmpl_NodeVisitorInterface
{
    const OPTIMIZE_ALL         = -1;
    const OPTIMIZE_NONE        = 0;
    const OPTIMIZE_FOR         = 2;
    const OPTIMIZE_RAW_FILTER  = 4;

    protected $loops = array();
    protected $optimizers;

    public function __construct($optimizers = -1)
    {
        if (!is_int($optimizers) || $optimizers > 2)
        {
            throw new InvalidArgumentException(sprintf('Optimizer mode "%s" is not valid.', $optimizers));
        }

        $this->optimizers = $optimizers;
    }

    public function enterNode(Ktmpl_NodeInterface $node, Ktmpl_Environment $env)
    {
        if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers))
        {
            $this->enterOptimizeFor($node, $env);
        }

        return $node;
    }

    public function leaveNode(Ktmpl_NodeInterface $node, Ktmpl_Environment $env)
    {
        if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers))
        {
            $this->leaveOptimizeFor($node, $env);
        }

        if (self::OPTIMIZE_RAW_FILTER === (self::OPTIMIZE_RAW_FILTER & $this->optimizers))
        {
            $node = $this->optimizeRawFilter($node, $env);
        }

        return $node;
    }

    protected function optimizeRawFilter($node, $env)
    {
        if ($node instanceof Ktmpl_Node_Expression_Filter && 'raw' == $node->getNode('filter')->getAttribute('value'))
        {
            return $node->getNode('node');
        }

        return $node;
    }

    protected function enterOptimizeFor($node, $env)
    {
        if ($node instanceof Ktmpl_Node_For)
        {
            $node->setAttribute('with_loop', false);
            array_unshift($this->loops, $node);
        } 
        elseif (!$this->loops)
        {
            return;
        }
        elseif ($node instanceof Ktmpl_Node_Expression_Name && 'loop' === $node->getAttribute('name'))
        {
            $this->addLoopToCurrent();
        }
        elseif ($node instanceof Ktmpl_Node_BlockReference || $node instanceof Ktmpl_Node_Expression_BlockReference)
        {
            $this->addLoopToCurrent();
        }
        elseif ($node instanceof Ktmpl_Node_Include && !$node->getAttribute('only'))
        {
            $this->addLoopToAll();
        }
        elseif ($node instanceof Ktmpl_Node_Expression_GetAttr
            && (!$node->getNode('attribute') instanceof Ktmpl_Node_Expression_Constant
                || 'parent' === $node->getNode('attribute')->getAttribute('value')
               )
            && (true === $this->loops[0]->getAttribute('with_loop')
                || ($node->getNode('node') instanceof Ktmpl_Node_Expression_Name
                    && 'loop' === $node->getNode('node')->getAttribute('name')
                   )
               )
        ) {
            $this->addLoopToAll();
        }
    }

    protected function leaveOptimizeFor($node, $env)
    {
        if ($node instanceof Ktmpl_Node_For)
        {
            array_shift($this->loops);
        }
    }

    protected function addLoopToCurrent()
    {
        $this->loops[0]->setAttribute('with_loop', true);
    }

    protected function addLoopToAll()
    {
        foreach ($this->loops as $loop)
        {
            $loop->setAttribute('with_loop', true);
        }
    }

    public function getPriority()
    {
        return 255;
    }
}