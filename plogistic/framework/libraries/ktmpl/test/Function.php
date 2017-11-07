<?
class Ktmpl_Test_Function implements Ktmpl_TestInterface
{
    protected $function;

    public function __construct($function)
    {
        $this->function = $function;
    }

    public function compile()
    {
        return $this->function;
    }
}