<?
interface Ktmpl_TokenParserBrokerInterface
{
    function getTokenParser($tag);
    function setParser(Ktmpl_ParserInterface $parser);
    function getParser();
}