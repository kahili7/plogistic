<?
abstract class Ktmpl_Template implements Ktmpl_TemplateInterface
{
    static protected $cache = array();

    protected $env;
    protected $blocks;

    public function __construct(Ktmpl_Environment $env)
    {
        $this->env = $env;
        $this->blocks = array();
    }

    public function getTemplateName()
    {
        return null;
    }

    public function getEnvironment()
    {
        return $this->env;
    }

    public function getParent(array $context)
    {
        return false;
    }

    public function displayParentBlock($name, array $context, array $blocks = array())
    {
        if (false !== $parent = $this->getParent($context))
                {
            $parent->displayBlock($name, $context, $blocks);
        } 
        else
        {
            throw new Ktmpl_Error_Runtime('This template has no parent', -1, $this->getTemplateName());
        }
    }

    public function displayBlock($name, array $context, array $blocks = array())
    {
        if (isset($blocks[$name]))
        {
            $b = $blocks;
            unset($b[$name]);
            call_user_func($blocks[$name], $context, $b);
        } 
        elseif (isset($this->blocks[$name]))
        {
            call_user_func($this->blocks[$name], $context, $blocks);
        } 
        elseif (false !== $parent = $this->getParent($context))
        {
            $parent->displayBlock($name, $context, array_merge($this->blocks, $blocks));
        }
    }

    public function renderParentBlock($name, array $context, array $blocks = array())
    {
        ob_start();
        $this->displayParentBlock($name, $context, $blocks);
        return new Ktmpl_Markup(ob_get_clean());
    }

    public function renderBlock($name, array $context, array $blocks = array())
    {
        ob_start();
        $this->displayBlock($name, $context, $blocks);
        return new Ktmpl_Markup(ob_get_clean());
    }

    public function hasBlock($name)
    {
        return isset($this->blocks[$name]);
    }

    public function getBlockNames()
    {
        return array_keys($this->blocks);
    }

    public function render(array $context)
    {
        ob_start();

        try {
            $this->display($context);
        } 
        catch (Exception $e)
        {
            $count = 100;

            while (ob_get_level() && --$count)
            {
                ob_end_clean();
            }

            throw $e;
        }

        return ob_get_clean();
    }

    protected function getContext($context, $item, $line = -1)
    {
        if (!array_key_exists($item, $context))
        {
            throw new Ktmpl_Error_Runtime(sprintf('Variable "%s" does not exist', $item), $line, $this->getTemplateName());
        }

        return $context[$item];
    }

    protected function getAttribute($object, $item, array $arguments = array(), $type = Ktmpl_TemplateInterface::ANY_CALL, $noStrictCheck = false, $line = -1)
    {
        // array
        if (Ktmpl_TemplateInterface::METHOD_CALL !== $type)
        {
            if ((is_array($object) || is_object($object) && $object instanceof ArrayAccess) && isset($object[$item]))
            {
                return $object[$item];
            }

            if (Ktmpl_TemplateInterface::ARRAY_CALL === $type)
            {
                if (!$this->env->isStrictVariables() || $noStrictCheck)
                {
                    return null;
                }

                if (is_object($object))
                {
                    throw new Ktmpl_Error_Runtime(sprintf('Key "%s" in object (with ArrayAccess) of type "%s" does not exist', $item, get_class($object)), $line, $this->getTemplateName());
                // array
                } 
                else
                {
                    throw new Ktmpl_Error_Runtime(sprintf('Key "%s" for array with keys "%s" does not exist', $item, implode(', ', array_keys($object))), $line, $this->getTemplateName());
                }
            }
        }

        if (!is_object($object))
        {
            if (!$this->env->isStrictVariables() || $noStrictCheck)
            {
                return null;
            }

            throw new Ktmpl_Error_Runtime(sprintf('Item "%s" for "%s" does not exist', $item, $object), $line, $this->getTemplateName());
        }

        $class = get_class($object);

        if (!isset(self::$cache[$class]))
        {
            $r = new ReflectionClass($class);

            self::$cache[$class] = array('methods' => array(), 'properties' => array());

            foreach ($r->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
            {
                self::$cache[$class]['methods'][strtolower($method->getName())] = true;
            }

            foreach ($r->getProperties(ReflectionProperty::IS_PUBLIC) as $property)
            {
                self::$cache[$class]['properties'][$property->getName()] = true;
            }
        }

        if (Ktmpl_TemplateInterface::METHOD_CALL !== $type)
        {
            if (isset(self::$cache[$class]['properties'][$item]) || isset($object->$item))
            {
                if ($this->env->hasExtension('sandbox'))
                {
                    $this->env->getExtension('sandbox')->checkPropertyAllowed($object, $item);
                }

                return $object->$item;
            }
        }

        // object method
        $lcItem = strtolower($item);

        if (isset(self::$cache[$class]['methods'][$lcItem]))
        {
            $method = $item;
        } 
        elseif (isset(self::$cache[$class]['methods']['get'.$lcItem]))
        {
            $method = 'get'.$item;
        } 
        elseif (isset(self::$cache[$class]['methods']['is'.$lcItem]))
        {
            $method = 'is'.$item;
        } 
        elseif (isset(self::$cache[$class]['methods']['__call']))
        {
            $method = $item;
        } 
        else
        {
            if (!$this->env->isStrictVariables() || $noStrictCheck)
            {
                return null;
            }

            throw new Ktmpl_Error_Runtime(sprintf('Method "%s" for object "%s" does not exist', $item, get_class($object)), $line, $this->getTemplateName());
        }

        if ($this->env->hasExtension('sandbox'))
        {
            $this->env->getExtension('sandbox')->checkMethodAllowed($object, $method);
        }

        $ret = call_user_func_array(array($object, $method), $arguments);

        if ($object instanceof Ktmpl_TemplateInterface)
        {
            return new Ktmpl_Markup($ret);
        }

        return $ret;
    }
}