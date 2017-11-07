<?
class Ktmpl_Compiler implements Ktmpl_CompilerInterface
{
    protected $lastLine;
    protected $source;
    protected $indentation;
    protected $env;

    public function __construct(Ktmpl_Environment $env)
    {
        $this->env = $env;
    }

    public function getEnvironment()
    {
        return $this->env;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function compile(Ktmpl_NodeInterface $node, $indentation = 0)
    {
        $this->lastLine = null;
        $this->source = '';
        $this->indentation = $indentation;
        $node->compile($this);
        return $this;
    }

    public function subcompile(Ktmpl_NodeInterface $node, $raw = true)
    {
        if (false === $raw)
        {
            $this->addIndentation();
        }

        $node->compile($this);
        return $this;
    }

    public function raw($string)
    {
        $this->source .= $string;
        return $this;
    }

    public function write()
    {
        $strings = func_get_args();

        foreach ($strings as $string)
            {
            $this->addIndentation();
            $this->source .= $string;
        }

        return $this;
    }

    public function addIndentation()
    {
        $this->source .= str_repeat(' ', $this->indentation * 4);
        return $this;
    }

    public function string($value)
    {
        $this->source .= sprintf('"%s"', addcslashes($value, "\t\"\$\\"));
        return $this;
    }

    public function repr($value)
    {
        if (is_int($value) || is_float($value))
        {
            $this->raw($value);
        } 
        else if (null === $value)
        {
            $this->raw('null');
        } 
        else if (is_bool($value))
        {
            $this->raw($value ? 'true' : 'false');
        } 
        else if (is_array($value))
        {
            $this->raw('array(');
            $i = 0;

            foreach ($value as $key => $value)
            {
                if ($i++)
                {
                    $this->raw(', ');
                }

                $this->repr($key);
                $this->raw(' => ');
                $this->repr($value);
            }

            $this->raw(')');
        } 
        else
        {
            $this->string($value);
        }

        return $this;
    }

    public function addDebugInfo(Ktmpl_NodeInterface $node)
    {
        if ($node->getLine() != $this->lastLine)
        {
            $this->lastLine = $node->getLine();
            $this->write("// line {$node->getLine()}\n");
        }

        return $this;
    }

    public function indent($step = 1)
    {
        $this->indentation += $step;
        return $this;
    }

    public function outdent($step = 1)
    {
        $this->indentation -= $step;

        if ($this->indentation < 0)
                {
            throw new Ktmpl_Error('Unable to call outdent() as the indentation would become negative');
        }

        return $this;
    }
}