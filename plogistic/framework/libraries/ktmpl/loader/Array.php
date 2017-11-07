<?
class Ktmpl_Loader_Array implements Ktmpl_LoaderInterface
{
    protected $templates;

    public function __construct(array $templates)
    {
        $this->templates = array();

        foreach ($templates as $name => $template)
        {
            $this->templates[$name] = $template;
        }
    }

    public function getSource($name)
    {
        if (!isset($this->templates[$name]))
        {
            throw new Ktmpl_Error_Loader(sprintf('Template "%s" is not defined.', $name));
        }

        return $this->templates[$name];
    }

    public function getCacheKey($name)
    {
        if (!isset($this->templates[$name]))
        {
            throw new Ktmpl_Error_Loader(sprintf('Template "%s" is not defined.', $name));
        }

        return $this->templates[$name];
    }

    public function isFresh($name, $time)
    {
        return true;
    }
}