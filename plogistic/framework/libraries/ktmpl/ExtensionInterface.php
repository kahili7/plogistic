<?
interface Ktmpl_ExtensionInterface
{
    function initRuntime(Ktmpl_Environment $environment);
    function getTokenParsers();
    function getNodeVisitors();
    function getFilters();
    function getTests();
    function getFunctions();
    function getOperators();
    function getGlobals();
    function getName();
}