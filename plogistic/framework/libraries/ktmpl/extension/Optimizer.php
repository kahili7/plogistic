<?
class Ktmpl_Extension_Optimizer extends Ktmpl_Extension
{
    protected $optimizers;

    public function __construct($optimizers = -1)
    {
        $this->optimizers = $optimizers;
    }

    public function getNodeVisitors()
    {
        return array(new Ktmpl_NodeVisitor_Optimizer($this->optimizers));
    }

    public function getName()
    {
        return 'optimizer';
    }
}