<?

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$config['base_url'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$config['base_url'] .= "://" . $_SERVER['HTTP_HOST'];
$config['base_url'] .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
$config['index_page'] = "index.php";

/*
  |--------------------------------------------------------------------------
  | URI PROTOCOL
  |--------------------------------------------------------------------------
  |
  | This item determines which server global should be used to retrieve the
  | URI string.  The default setting of "AUTO" works for most servers.
  | If your links do not seem to work, try one of the other delicious flavors:
  |
  | 'AUTO'			Default - auto detects
  | 'PATH_INFO'		Uses the PATH_INFO
  | 'QUERY_STRING'	Uses the QUERY_STRING
  | 'REQUEST_URI'		Uses the REQUEST_URI
  | 'ORIG_PATH_INFO'	Uses the ORIG_PATH_INFO
  |
 */
$config['uri_protocol'] = "AUTO";

$config['url_suffix'] = "";

$config['language'] = "russian";

$config['charset'] = "UTF-8";

$config['enable_hooks'] = TRUE;

$config['subclass_prefix'] = 'CRM_';

$config['permitted_uri_chars'] = 'a-z 0-9~%.:_\-';

$config['output_enable_profiler'] = TRUE;

$config['enable_query_strings'] = FALSE;
$config['controller_trigger'] = 'c';
$config['function_trigger'] = 'm';
$config['directory_trigger'] = 'd'; // experimental not currently in use

$config['log_threshold'] = 0;
$config['log_path'] = '';
$config['log_date_format'] = 'Y-m-d H:i:s';

$config['cache_path'] = '';

$config['encryption_key'] = "";

$config['sess_cookie_name'] = 'crmsession';
$config['sess_expiration'] = 36000;
$config['sess_encrypt_cookie'] = FALSE;
$config['sess_use_database'] = TRUE;
$config['sess_table_name'] = '_sessions';
$config['sess_match_ip'] = FALSE;
$config['sess_match_useragent'] = TRUE;
$config['sess_time_to_update'] = 300;

$config['cookie_prefix'] = "";
$config['cookie_domain'] = "";
$config['cookie_path'] = "/";

$config['global_xss_filtering'] = TRUE;

$config['compress_output'] = FALSE;

$config['time_reference'] = 'local';

$config['rewrite_short_tags'] = FALSE;

$config['proxy_ips'] = '';