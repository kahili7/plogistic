<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

$autoload['libraries'] = array('database', 'validation', 'session', 'form_validation', 'user', 'menu');

$autoload['helper'] = array('form', 'url', 'others', 'html', 'language', 'array', 'date', 'text', 'user',
    'menu', 'tools', 'fields', 'countries', 'cities', 'employeers', 'users', 'filials', 'constants', 'tree');

$autoload['config'] = array('crm');

$autoload['language'] = array('common');

$autoload['model'] = array();