<?
interface Ktmpl_TokenParserInterface
{
    function setParser(Ktmpl_Parser $parser);
    function parse(Ktmpl_Token $token);
    function getTag();
}