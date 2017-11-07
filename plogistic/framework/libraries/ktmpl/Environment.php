<?
class Ktmpl_Environment
{
    const VERSION = '1.0.0';

    protected $charset;
    protected $loader;
    protected $debug;
    protected $autoReload;
    protected $cache;
    protected $lexer;
    protected $parser;
    protected $compiler;
    protected $baseTemplateClass;
    protected $extensions;
    protected $parsers;
    protected $visitors;
    protected $filters;
    protected $tests;
    protected $functions;
    protected $globals;
    protected $runtimeInitialized;
    protected $loadedTemplates;
    protected $strictVariables;
    protected $unaryOperators;
    protected $binaryOperators;
    protected $templateClassPrefix = '__TwigTemplate_';
    protected $functionCallbacks;
    protected $filterCallbacks;

    public function __construct(Ktmpl_LoaderInterface $loader = null, $options = array())
    {
        if (null !== $loader) 
		{
            $this->setLoader($loader);
        }

        $options = array_merge(array(
            'debug'               => false,
            'charset'             => 'UTF-8',
            'base_template_class' => 'Ktmpl_Template',
            'strict_variables'    => false,
            'autoescape'          => true,
            'cache'               => false,
            'auto_reload'         => null,
            'optimizations'       => -1,
        ), $options);

        $this->debug              = (bool) $options['debug'];
        $this->charset            = $options['charset'];
        $this->baseTemplateClass  = $options['base_template_class'];
        $this->autoReload         = null === $options['auto_reload'] ? $this->debug : (bool) $options['auto_reload'];
        $this->extensions         = array(
            'core'      => new Ktmpl_Extension_Core(),
            'escaper'   => new Ktmpl_Extension_Escaper((bool) $options['autoescape']),
            'optimizer' => new Ktmpl_Extension_Optimizer($options['optimizations']),
        );
        $this->strictVariables    = (bool) $options['strict_variables'];
        $this->runtimeInitialized = false;
        $this->setCache($options['cache']);
        $this->functionCallbacks = array();
        $this->filterCallbacks = array();
    }

    public function getBaseTemplateClass()
    {
        return $this->baseTemplateClass;
    }

    public function setBaseTemplateClass($class)
    {
        $this->baseTemplateClass = $class;
    }

    public function enableDebug()
    {
        $this->debug = true;
    }

    public function disableDebug()
    {
        $this->debug = false;
    }

    public function isDebug()
    {
        return $this->debug;
    }

    public function enableAutoReload()
    {
        $this->autoReload = true;
    }

    public function disableAutoReload()
    {
        $this->autoReload = false;
    }

    public function isAutoReload()
    {
        return $this->autoReload;
    }

    public function enableStrictVariables()
    {
        $this->strictVariables = true;
    }

    public function disableStrictVariables()
    {
        $this->strictVariables = false;
    }

    public function isStrictVariables()
    {
        return $this->strictVariables;
    }

    public function getCache()
    {
        return $this->cache;
    }

    public function setCache($cache)
    {
        $this->cache = $cache ? $cache : false;
    }

    public function getCacheFilename($name)
    {
        if (false === $this->cache)
        {
            return false;
        }

        $class = substr($this->getTemplateClass($name), strlen($this->templateClassPrefix));
        return $this->getCache().'/'.substr($class, 0, 2).'/'.substr($class, 2, 2).'/'.substr($class, 4).'.php';
    }

    public function getTemplateClass($name)
    {
        return $this->templateClassPrefix.md5($this->loader->getCacheKey($name));
    }

    public function getTemplateClassPrefix()
    {
        return $this->templateClassPrefix;
    }

    public function loadTemplate($name)
    {
        $cls = $this->getTemplateClass($name);

        if (isset($this->loadedTemplates[$cls]))
        {
            return $this->loadedTemplates[$cls];
        }

        if (!class_exists($cls, false))
        {
            if (false === $cache = $this->getCacheFilename($name))
            {
                eval('?>'.$this->compileSource($this->loader->getSource($name), $name));
            } 
            else
            {
                if (!file_exists($cache) || ($this->isAutoReload() && !$this->loader->isFresh($name, filemtime($cache))))
                {
                    $this->writeCacheFile($cache, $this->compileSource($this->loader->getSource($name), $name));
                }

                require_once $cache;
            }
        }

        if (!$this->runtimeInitialized)
        {
            $this->initRuntime();
        }

        return $this->loadedTemplates[$cls] = new $cls($this);
    }

    public function clearTemplateCache()
    {
        $this->loadedTemplates = array();
    }

    public function clearCacheFiles()
    {
        if (false === $this->cache)
        {
            return;
        }

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->cache), RecursiveIteratorIterator::LEAVES_ONLY) as $file)
        {
            if ($file->isFile())
            {
                @unlink($file->getPathname());
            }
        }
    }

    public function getLexer()
    {
        if (null === $this->lexer)
        {
            $this->lexer = new Ktmpl_Lexer($this);
        }

        return $this->lexer;
    }

    public function setLexer(Ktmpl_LexerInterface $lexer)
    {
        $this->lexer = $lexer;
    }

    public function tokenize($source, $name = null)
    {
        return $this->getLexer()->tokenize($source, $name);
    }

    public function getParser()
    {
        if (null === $this->parser)
        {
            $this->parser = new Twig_Parser($this);
        }

        return $this->parser;
    }

    public function setParser(Ktmpl_ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    public function parse(Ktmpl_TokenStream $tokens)
    {
        return $this->getParser()->parse($tokens);
    }

    public function getCompiler()
    {
        if (null === $this->compiler)
        {
            $this->compiler = new Ktmpl_Compiler($this);
        }

        return $this->compiler;
    }

    public function setCompiler(Twig_CompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }

    public function compile(Ktmpl_NodeInterface $node)
    {
        return $this->getCompiler()->compile($node)->getSource();
    }

    public function compileSource($source, $name = null)
    {
        return $this->compile($this->parse($this->tokenize($source, $name)));
    }

    public function setLoader(Twig_LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function initRuntime()
    {
        $this->runtimeInitialized = true;

        foreach ($this->getExtensions() as $extension)
        {
            $extension->initRuntime($this);
        }
    }

    public function hasExtension($name)
    {
        return isset($this->extensions[$name]);
    }

    public function getExtension($name)
    {
        if (!isset($this->extensions[$name]))
        {
            throw new Ktmpl_Error_Runtime(sprintf('The "%s" extension is not enabled.', $name));
        }

        return $this->extensions[$name];
    }

    public function addExtension(Ktmpl_ExtensionInterface $extension)
    {
        $this->extensions[$extension->getName()] = $extension;
    }

    public function removeExtension($name)
    {
        unset($this->extensions[$name]);
    }

    public function setExtensions(array $extensions)
    {
        foreach ($extensions as $extension)
        {
            $this->addExtension($extension);
        }
    }

    public function getExtensions()
    {
        return $this->extensions;
    }

    public function addTokenParser(Ktmpl_TokenParserInterface $parser)
    {
        if (null === $this->parsers)
        {
            $this->getTokenParsers();
        }

        $this->parsers->addTokenParser($parser);
    }

    public function getTokenParsers()
    {
        if (null === $this->parsers)
        {
            $this->parsers = new Ktmpl_TokenParserBroker;

            foreach ($this->getExtensions() as $extension)
            {
                $parsers = $extension->getTokenParsers();

                foreach($parsers as $parser)
                {
                    if ($parser instanceof Ktmpl_TokenParserInterface)
                    {
                        $this->parsers->addTokenParser($parser);
                    } 
                    else if ($parser instanceof Ktmpl_TokenParserBrokerInterface)
                    {
                        $this->parsers->addTokenParserBroker($parser);
                    } 
                    else
                    {
                        throw new Ktmpl_Error_Runtime('getTokenParsers() must return an array of Twig_TokenParserInterface or Twig_TokenParserBrokerInterface instances');
                    }
                }
            }
        }

        return $this->parsers;
    }

    public function addNodeVisitor(Ktmpl_NodeVisitorInterface $visitor)
    {
        if (null === $this->visitors)
        {
            $this->getNodeVisitors();
        }

        $this->visitors[] = $visitor;
    }

    public function getNodeVisitors()
    {
        if (null === $this->visitors)
        {
            $this->visitors = array();

            foreach ($this->getExtensions() as $extension)
            {
                $this->visitors = array_merge($this->visitors, $extension->getNodeVisitors());
            }
        }

        return $this->visitors;
    }

    public function addFilter($name, Ktmpl_FilterInterface $filter)
    {
        if (null === $this->filters)
        {
            $this->loadFilters();
        }

        $this->filters[$name] = $filter;
    }

    public function getFilter($name)
    {
        if (null === $this->filters)
        {
            $this->loadFilters();
        }

        if (isset($this->filters[$name]))
        {
            return $this->filters[$name];
        }

        foreach ($this->filterCallbacks as $callback)
        {
            if (false !== $filter = call_user_func($callback, $name))
            {
                return $filter;
            }
        }

        return false;
    }

    public function registerUndefinedFilterCallback($callable)
    {
        $this->filterCallbacks[] = $callable;
    }

    protected function loadFilters()
    {
        $this->filters = array();

        foreach ($this->getExtensions() as $extension)
        {
            $this->filters = array_merge($this->filters, $extension->getFilters());
        }
    }

    public function addTest($name, Ktmpl_TestInterface $test)
    {
        if (null === $this->tests)
        {
            $this->getTests();
        }

        $this->tests[$name] = $test;
    }

    public function getTests()
    {
        if (null === $this->tests)
        {
            $this->tests = array();

            foreach ($this->getExtensions() as $extension)
            {
                $this->tests = array_merge($this->tests, $extension->getTests());
            }
        }

        return $this->tests;
    }

    public function addFunction($name, Ktmpl_FunctionInterface $function)
    {
        if (null === $this->functions)
        {
            $this->loadFunctions();
        }

        $this->functions[$name] = $function;
    }

    public function getFunction($name)
    {
        if (null === $this->functions)
        {
            $this->loadFunctions();
        }

        if (isset($this->functions[$name]))
        {
            return $this->functions[$name];
        }

        foreach ($this->functionCallbacks as $callback)
        {
            if (false !== $function = call_user_func($callback, $name))
            {
                return $function;
            }
        }

        return false;
    }

    public function registerUndefinedFunctionCallback($callable)
    {
        $this->functionCallbacks[] = $callable;
    }

    protected function loadFunctions()
    {
        $this->functions = array();

        foreach ($this->getExtensions() as $extension)
        {
            $this->functions = array_merge($this->functions, $extension->getFunctions());
        }
    }

    public function addGlobal($name, $value)
    {
        if (null === $this->globals)
        {
            $this->getGlobals();
        }

        $this->globals[$name] = $value;
    }

    public function getGlobals()
    {
        if (null === $this->globals)
        {
            $this->globals = array();

            foreach ($this->getExtensions() as $extension)
            {
                $this->globals = array_merge($this->globals, $extension->getGlobals());
            }
        }

        return $this->globals;
    }

    public function getUnaryOperators()
    {
        if (null === $this->unaryOperators)
        {
            $this->initOperators();
        }

        return $this->unaryOperators;
    }

    public function getBinaryOperators()
    {
        if (null === $this->binaryOperators)
        {
            $this->initOperators();
        }

        return $this->binaryOperators;
    }

    protected function initOperators()
    {
        $this->unaryOperators = array();
        $this->binaryOperators = array();

        foreach ($this->getExtensions() as $extension)
        {
            $operators = $extension->getOperators();

            if (!$operators)
            {
                continue;
            }

            if (2 !== count($operators))
            {
                throw new InvalidArgumentException(sprintf('"%s::getOperators()" does not return a valid operators array.', get_class($extension)));
            }

            $this->unaryOperators = array_merge($this->unaryOperators, $operators[0]);
            $this->binaryOperators = array_merge($this->binaryOperators, $operators[1]);
        }
    }

    protected function writeCacheFile($file, $content)
    {
        if (!is_dir(dirname($file)))
        {
            mkdir(dirname($file), 0777, true);
        }

        $tmpFile = tempnam(dirname($file), basename($file));

        if (false !== @file_put_contents($tmpFile, $content))
        {
            if (@rename($tmpFile, $file) || (@copy($tmpFile, $file) && unlink($tmpFile)))
            {
                chmod($file, 0644);
                return;
            }
        }

        throw new Ktmpl_Error_Runtime(sprintf('Failed to write cache file "%s".', $file));
    }
}