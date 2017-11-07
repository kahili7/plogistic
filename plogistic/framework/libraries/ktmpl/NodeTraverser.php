<?
class Ktmpl_NodeTraverser
{
    protected $env;
    protected $visitors;

    public function __construct(Ktmpl_Environment $env, array $visitors = array())
    {
        $this->env = $env;
        $this->visitors = array();

        foreach ($visitors as $visitor)
        {
            $this->addVisitor($visitor);
        }
    }

    public function addVisitor(Ktmpl_NodeVisitorInterface $visitor)
    {
        if (!isset($this->visitors[$visitor->getPriority()]))
        {
            $this->visitors[$visitor->getPriority()] = array();
        }

        $this->visitors[$visitor->getPriority()][] = $visitor;
    }

    public function traverse(Ktmpl_NodeInterface $node)
    {
        ksort($this->visitors);

        foreach ($this->visitors as $visitors)
        {
            foreach ($visitors as $visitor)
            {
                $node = $this->traverseForVisitor($visitor, $node);
            }
        }

        return $node;
    }

    protected function traverseForVisitor(Ktmpl_NodeVisitorInterface $visitor, Ktmpl_NodeInterface $node = null)
    {
        if (null === $node)
        {
            return null;
        }

        $node = $visitor->enterNode($node, $this->env);

        foreach ($node as $k => $n)
        {
            if (false !== $n = $this->traverseForVisitor($visitor, $n))
            {
                $node->setNode($k, $n);
            } 
            else
            {
                $node->removeNode($k);
            }
        }

        return $visitor->leaveNode($node, $this->env);
    }
}