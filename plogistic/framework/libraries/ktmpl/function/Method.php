<?
class Ktmpl_Function_Method extends Ktmpl_Function
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