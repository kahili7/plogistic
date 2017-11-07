<?
interface Ktmpl_NodeInterface
{
    function compile(Ktmpl_Compiler $compiler);
    function getLine();
    function getNodeTag();
}