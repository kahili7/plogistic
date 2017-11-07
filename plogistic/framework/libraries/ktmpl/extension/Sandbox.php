<?
class Ktmpl_Extension_Sandbox extends Ktmpl_Extension
{
    protected $sandboxedGlobally;
    protected $sandboxed;
    protected $policy;

    public function __construct(Ktmpl_Sandbox_SecurityPolicyInterface $policy, $sandboxed = false)
    {
        $this->policy            = $policy;
        $this->sandboxedGlobally = $sandboxed;
    }

    public function getTokenParsers()
    {
        return array(new Ktmpl_TokenParser_Sandbox());
    }

    public function getNodeVisitors()
    {
        return array(new Ktmpl_NodeVisitor_Sandbox());
    }

    public function enableSandbox()
    {
        $this->sandboxed = true;
    }

    public function disableSandbox()
    {
        $this->sandboxed = false;
    }

    public function isSandboxed()
    {
        return $this->sandboxedGlobally || $this->sandboxed;
    }

    public function isSandboxedGlobally()
    {
        return $this->sandboxedGlobally;
    }

    public function setSecurityPolicy(Ktmpl_Sandbox_SecurityPolicyInterface $policy)
    {
        $this->policy = $policy;
    }

    public function getSecurityPolicy()
    {
        return $this->policy;
    }

    public function checkSecurity($tags, $filters, $functions)
    {
        if ($this->isSandboxed())
        {
            $this->policy->checkSecurity($tags, $filters, $functions);
        }
    }

    public function checkMethodAllowed($obj, $method)
    {
        if ($this->isSandboxed())
        {
            $this->policy->checkMethodAllowed($obj, $method);
        }
    }

    public function checkPropertyAllowed($obj, $method)
    {
        if ($this->isSandboxed())
        {
            $this->policy->checkPropertyAllowed($obj, $method);
        }
    }

    public function getName()
    {
        return 'sandbox';
    }
}