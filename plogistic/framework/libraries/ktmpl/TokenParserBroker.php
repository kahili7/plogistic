<?
class Ktmpl_TokenParserBroker implements Ktmpl_TokenParserBrokerInterface
{
    protected $parser;
    protected $parsers = array();
    protected $brokers = array();

    public function __construct($parsers = array(), $brokers = array())
    {
        foreach($parsers as $parser)
        {
            if (!$parser instanceof Ktmpl_TokenParserInterface)
            {
                throw new Ktmpl_Error('$parsers must a an array of Ktmpl_TokenParserInterface');
            }

            $this->parsers[$parser->getTag()] = $parser;
        }

        foreach($brokers as $broker)
        {
            if (!$broker instanceof Ktmpl_TokenParserBrokerInterface)
            {
                throw new Ktmpl_Error('$brokers must a an array of Ktmpl_TokenParserBrokerInterface');
            }
            $this->brokers[] = $broker;
        }
    }

    public function addTokenParser(Ktmpl_TokenParserInterface $parser)
    {
        $this->parsers[$parser->getTag()] = $parser;
    }

    public function addTokenParserBroker(Ktmpl_TokenParserBroker $broker)
    {
        $this->brokers[] = $broker;
    }

    public function getTokenParser($tag)
    {
        if (isset($this->parsers[$tag]))
        {
            return $this->parsers[$tag];
        }

        $broker = end($this->brokers);

        while (false !== $broker)
        {
            $parser = $broker->getTokenParser($tag);

            if (null !== $parser)
            {
                return $parser;
            }

            $broker = prev($this->brokers);
        }

        return null;
    }

    public function getParser()
    {
        return $this->parser;
    }

    public function setParser(Ktmpl_ParserInterface $parser)
    {
        $this->parser = $parser;

        foreach ($this->parsers as $tokenParser)
        {
            $tokenParser->setParser($parser);
        }

        foreach ($this->brokers as $broker)
        {
            $broker->setParser($parser);
        }
    }
}