<?
class Ktmpl_Filter_Method extends Ktmpl_Filter
{
    protected $extension, $method;

    public function __construct(Ktmpl_ExtensionInterface $extension, $method, array $options = array())
    {
        parent::__construct($options);

        $this->extension = $extension;
        $this->method = $method;
    }

    public function compile()
    {
        return sprintf('$this->env->getExtension(\'%s\')->%s', $this->extension->getName(), $this->method);
    }
}
