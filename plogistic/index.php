<?

error_reporting(E_ALL);

set_include_path(dirname(__FILE__));

@ini_set('cgi.fix_pathinfo', 0);

if (ini_get('date.timezone') == '')
{
    date_default_timezone_set('GMT');
}

$system_path = "framework";
$application_folder = "application";
$addon_folder = 'addons';

if (realpath($system_path) !== FALSE)
{
    $system_path = realpath($system_path) . '/';
}

$system_path = rtrim($system_path, '/') . '/';

if (!is_dir($system_path))
{
    exit("Your system folder path does not appear to be set correctly. Please open the following file and correct this: " . pathinfo(__FILE__, PATHINFO_BASENAME));
}

define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('EXT', '.php');
define('BASEPATH', str_replace("\\", "/", $system_path));
define('ADDONPATH', $addon_folder . '/');
define('FCPATH', str_replace(SELF, '', __FILE__));
define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));
define('APPPATH', $application_folder.'/');

require_once BASEPATH . 'core/kibo' . EXT;