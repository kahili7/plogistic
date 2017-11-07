<?
abstract class Ktmpl_Filter implements Ktmpl_FilterInterface
{
    protected $options;

    public function __construct(array $options = array())
    {
        $this->options = array_merge(array(
            'needs_environment' => false,
            'needs_context'     => false,
            'pre_escape'        => null,
        ), $options);
    }

    public function needsEnvironment()
    {
        return $this->options['needs_environment'];
    }

    public function needsContext()
    {
        return $this->options['needs_context'];
    }

    public function getSafe(Ktmpl_Node $filterArgs)
    {
        if (isset($this->options['is_safe']))
        {
            return $this->options['is_safe'];
        }

        if (isset($this->options['is_safe_callback']))
        {
            return call_user_func($this->options['is_safe_callback'], $filterArgs);
        }

        return array();
    }

    public function getPreEscape()
    {
        return $this->options['pre_escape'];
    }
}