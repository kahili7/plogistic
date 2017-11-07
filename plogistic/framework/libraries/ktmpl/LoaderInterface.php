<?
interface Ktmpl_LoaderInterface
{
    function getSource($name);
    function getCacheKey($name);
    function isFresh($name, $time);
}