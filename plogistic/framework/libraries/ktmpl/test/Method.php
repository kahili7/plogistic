<?
class Ktmpl_Test_Method implements Ktmpl_TestInterface
{
    protected $extension, $method;

    public function __construct(Ktmpl_ExtensionInterface $extension, $method)
    {
        $this->extension = $extension;
        $this->method = $method;
    }

    public function compile()
    {
        return sprintf('$this->env->getExtension(\'%s\')->%s', $this->extension->getName(), $this->method);
    }
}