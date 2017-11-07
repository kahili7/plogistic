<?
interface Ktmpl_FunctionInterface
{
    function compile();
    function needsEnvironment();
    function needsContext();
    function getSafe(Ktmpl_Node $filterArgs);
}