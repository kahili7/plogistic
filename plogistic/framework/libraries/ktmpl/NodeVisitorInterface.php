<?
interface Ktmpl_NodeVisitorInterface
{
    function enterNode(Ktmpl_NodeInterface $node, Ktmpl_Environment $env);
    function leaveNode(Ktmpl_NodeInterface $node, Ktmpl_Environment $env);
    function getPriority();
}