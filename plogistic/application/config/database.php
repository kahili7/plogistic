<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

$active_group = "default";
$active_record = TRUE;

global $db;

$db['default']['hostname'] = "localhost";
$db['default']['username'] = "root";
$db['default']['password'] = "ingodwet900";
$db['default']['database'] = "perilog";
$db['default']['dbdriver'] = "mysql";
$db['default']['dbprefix'] = "";
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = "system/cache/db";
$db['default']['char_set'] = "utf8";
$db['default']['dbcollat'] = "utf8_general_ci";

$db['default']['autoinit'] = FALSE;