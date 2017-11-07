<?
interface Ktmpl_TemplateInterface
{
    const ANY_CALL    = 'any';
    const ARRAY_CALL  = 'array';
    const METHOD_CALL = 'method';

    function render(array $context);
    function display(array $context);
    function getEnvironment();
}