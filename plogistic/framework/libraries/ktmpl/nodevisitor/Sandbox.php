<?
class Ktmpl_NodeVisitor_Sandbox implements Ktmpl_NodeVisitorInterface
{
    protected $inAModule = false;
    protected $tags;
    protected $filters;
    protected $functions;

    public function enterNode(Ktmpl_NodeInterface $node, Ktmpl_Environment $env)
    {
        if ($node instanceof Ktmpl_Node_Module)
        {
            $this->inAModule = true;
            $this->tags = array();
            $this->filters = array();
            $this->functions = array();

            return $node;
        } 
        elseif ($this->inAModule)
        {
            if ($node->getNodeTag())
            {
                $this->tags[] = $node->getNodeTag();
            }

            if ($node instanceof Ktmpl_Node_Expression_Filter)
            {
                $this->filters[] = $node->getNode('filter')->getAttribute('value');
            }

            if ($node instanceof Ktmpl_Node_Expression_Function)
            {
                $this->functions[] = $node->getNode('name')->getAttribute('name');
            }

            if ($node instanceof Ktmpl_Node_Print)
            {
                return new Ktmpl_Node_SandboxedPrint($node->getNode('expr'), $node->getLine(), $node->getNodeTag());
            }
        }

        return $node;
    }

    public function leaveNode(Ktmpl_NodeInterface $node, Ktmpl_Environment $env)
    {
        if ($node instanceof Ktmpl_Node_Module)
        {
            $this->inAModule = false;
            return new Ktmpl_Node_SandboxedModule($node, array_unique($this->filters), array_unique($this->tags), array_unique($this->functions));
        }

        return $node;
    }

    public function getPriority()
    {
        return 0;
    }
}