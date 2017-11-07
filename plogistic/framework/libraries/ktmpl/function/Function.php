<?
class Ktmpl_Function_Function extends Ktmpl_Function
{
    protected $function;

    public function __construct($function, array $options = array())
    {
        parent::__construct($options);

        $this->function = $function;
    }

    public function compile()
    {
        return $this->function;
    }
}