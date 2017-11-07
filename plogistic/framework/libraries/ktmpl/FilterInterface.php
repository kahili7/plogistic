<?
interface Ktmpl_FilterInterface
{
    function compile();
    function needsEnvironment();
    function needsContext();
    function getSafe(Ktmpl_Node $filterArgs);
    function getPreEscape();
}