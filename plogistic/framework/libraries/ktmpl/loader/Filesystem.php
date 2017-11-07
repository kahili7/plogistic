<?
class Ktmpl_Loader_Filesystem implements Ktmpl_LoaderInterface
{
    protected $paths;
    protected $cache;

    public function __construct($paths)
    {
        $this->setPaths($paths);
    }

    public function getPaths()
    {
        return $this->paths;
    }

    public function setPaths($paths)
    {
        $this->cache = array();

        if (!is_array($paths))
        {
            $paths = array($paths);
        }

        $this->paths = array();

        foreach ($paths as $path)
        {
            if (!is_dir($path))
            {
                throw new Ktmpl_Error_Loader(sprintf('The "%s" directory does not exist.', $path));
            }

            $this->paths[] = $path;
        }
    }

    public function getSource($name)
    {
        return file_get_contents($this->findTemplate($name));
    }

    public function getCacheKey($name)
    {
        return $this->findTemplate($name);
    }

    public function isFresh($name, $time)
    {
        return filemtime($this->findTemplate($name)) < $time;
    }

    protected function findTemplate($name)
    {
        $name = preg_replace('#/{2,}#', '/', strtr($name, '\\', '/'));

        if (isset($this->cache[$name]))
        {
            return $this->cache[$name];
        }

        $this->validateName($name);

        foreach ($this->paths as $path)
        {
            if (is_file($path.'/'.$name))
            {
                return $this->cache[$name] = $path.'/'.$name;
            }
        }

        throw new Ktmpl_Error_Loader(sprintf('Unable to find template "%s" (looked into: %s).', $name, implode(', ', $this->paths)));
    }

    protected function validateName($name)
    {
        $parts = explode('/', $name);
        $level = 0;

        foreach ($parts as $part)
        {
            if ('..' === $part)
            {
                --$level;
            } 
            elseif ('.' !== $part)
            {
                ++$level;
            }

            if ($level < 0)
            {
                throw new Ktmpl_Error_Loader(sprintf('Looks like you try to load a template outside configured directories (%s).', $name));
            }
        }
    }
}