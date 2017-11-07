<?
interface Ktmpl_Sandbox_SecurityPolicyInterface
{
    function checkSecurity($tags, $filters, $functions);
    function checkMethodAllowed($obj, $method);
    function checkPropertyAllowed($obj, $method);
}