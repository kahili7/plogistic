<?
interface Ktmpl_CompilerInterface
{
    function compile(Ktmpl_NodeInterface $node);
    function getSource();
}