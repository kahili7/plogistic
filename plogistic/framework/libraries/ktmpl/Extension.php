<?
abstract class Ktmpl_Extension implements Ktmpl_ExtensionInterface
{
    public function initRuntime(Ktmpl_Environment $environment)
    {
    }

    public function getTokenParsers()
    {
        return array();
    }

    public function getNodeVisitors()
    {
        return array();
    }

    public function getFilters()
    {
        return array();
    }

    public function getTests()
    {
        return array();
    }

    public function getFunctions()
    {
        return array();
    }

    public function getOperators()
    {
        return array();
    }

    public function getGlobals()
    {
        return array();
    }
}