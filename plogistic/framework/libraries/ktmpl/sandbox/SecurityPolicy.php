<?
class Ktmpl_Sandbox_SecurityPolicy implements Ktmpl_Sandbox_SecurityPolicyInterface
{
    protected $allowedTags;
    protected $allowedFilters;
    protected $allowedMethods;
    protected $allowedProperties;
    protected $allowedFunctions;

    public function __construct(array $allowedTags = array(), array $allowedFilters = array(), array $allowedMethods = array(), array $allowedProperties = array(), array $allowedFunctions = array())
    {
        $this->allowedTags = $allowedTags;
        $this->allowedFilters = $allowedFilters;
        $this->allowedMethods = $allowedMethods;
        $this->allowedProperties = $allowedProperties;
        $this->allowedFunctions = $allowedFunctions;
    }

    public function setAllowedTags(array $tags)
    {
        $this->allowedTags = $tags;
    }

    public function setAllowedFilters(array $filters)
    {
        $this->allowedFilters = $filters;
    }

    public function setAllowedMethods(array $methods)
    {
        $this->allowedMethods = $methods;
    }

    public function setAllowedProperties(array $properties)
    {
        $this->allowedProperties = $properties;
    }

    public function setAllowedFunctions(array $functions)
    {
        $this->allowedFunctions = $functions;
    }

    public function checkSecurity($tags, $filters, $functions)
    {
        foreach ($tags as $tag)
        {
            if (!in_array($tag, $this->allowedTags))
            {
                throw new Ktmpl_Sandbox_SecurityError(sprintf('Tag "%s" is not allowed.', $tag));
            }
        }

        foreach ($filters as $filter)
        {
            if (!in_array($filter, $this->allowedFilters))
            {
                throw new Ktmpl_Sandbox_SecurityError(sprintf('Filter "%s" is not allowed.', $filter));
            }
        }

        foreach ($functions as $function)
        {
            if (!in_array($function, $this->allowedFunctions))
            {
                throw new Ktmpl_Sandbox_SecurityError(sprintf('Function "%s" is not allowed.', $function));
            }
        }
    }

    public function checkMethodAllowed($obj, $method)
    {
        if ($obj instanceof Ktmpl_TemplateInterface || $obj instanceof Ktmpl_Markup)
        {
            return true;
        }

        $allowed = false;

        foreach ($this->allowedMethods as $class => $methods)
        {
            if ($obj instanceof $class)
            {
                $allowed = in_array($method, is_array($methods) ? $methods : array($methods));
                break;
            }
        }

        if (!$allowed)
        {
            throw new Ktmpl_Sandbox_SecurityError(sprintf('Calling "%s" method on a "%s" object is not allowed.', $method, get_class($obj)));
        }
    }

    public function checkPropertyAllowed($obj, $property)
    {
        $allowed = false;

        foreach ($this->allowedProperties as $class => $properties)
        {
            if ($obj instanceof $class)
            {
                $allowed = in_array($property, is_array($properties) ? $properties : array($properties));
                break;
            }
        }

        if (!$allowed)
        {
            throw new Ktmpl_Sandbox_SecurityError(sprintf('Calling "%s" property on a "%s" object is not allowed.', $property, get_class($obj)));
        }
    }
}