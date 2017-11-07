<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function &DB($params='', $active_record_override=FALSE)
{
	if(is_string($params) AND strpos($params, '://') === FALSE)
	{
		include(APPPATH.'config/database'.EXT);

		if(!isset($db) OR count($db) == 0)
		{
			show_error('No database connection settings were found in the database config file.');
		}

		if($params != '')
		{
			$active_group = $params;
		}

		if(!isset($active_group) OR ! isset($db[$active_group]))
		{
			show_error('You have specified an invalid database connection group.');
		}

		$params = $db[$active_group];
	}
	else if(is_string($params))
	{
		if(($dns = @parse_url($params)) === FALSE)
		{
			show_error('Invalid DB Connection String');
		}

		$params = array(
		'dbdriver'	=> $dns['scheme'],
		'hostname'	=> (isset($dns['host'])) ? rawurldecode($dns['host']) : '',
		'username'	=> (isset($dns['user'])) ? rawurldecode($dns['user']) : '',
		'password'	=> (isset($dns['pass'])) ? rawurldecode($dns['pass']) : '',
		'database'	=> (isset($dns['path'])) ? rawurldecode(substr($dns['path'], 1)) : ''
		);

		if(isset($dns['query']))
		{
			parse_str($dns['query'], $extra);

			foreach($extra as $key => $val)
			{
				if(strtoupper($val) == "TRUE")
				{
					$val = TRUE;
				}
				else if(strtoupper($val) == "FALSE")
				{
					$val = FALSE;
				}

				$params[$key] = $val;
			}
		}
	}

	if(!isset($params['dbdriver']) OR $params['dbdriver'] == '')
	{
		show_error('You have not selected a database type to connect to.');
	}

	if ($active_record_override !== NULL)
	{
		$active_record = $active_record_override;
	}

	require_once(BASEPATH.'database/db_driver'.EXT);

	if(!isset($active_record) OR $active_record == TRUE)
	{
		require_once(BASEPATH.'database/db_active_rec'.EXT);

		if(!class_exists('KI_DB'))
		{
			eval('class KI_DB extends KI_DB_ACTIVE_RECORD { }');
		}
	}
	else
	{
		if(!class_exists('KI_DB'))
		{
			eval('class KI_DB extends KI_DB_DRIVER { }');
		}
	}

	require_once(BASEPATH.'database/drivers/'.$params['dbdriver'].'/'.$params['dbdriver'].'_driver'.EXT);

	$driver = 'KI_DB_'.$params['dbdriver'].'_driver';
	$driver = strtoupper($driver);
	$DB =& instantiate_class(new $driver($params));

	if($DB->autoinit == TRUE)
	{
		$DB->initialize();
	}
	
	if (isset($params['stricton']) && $params['stricton'] == TRUE)
	{
		$DB->query('SET SESSION sql_mode="STRICT_ALL_TABLES"');
	}
	
	return $DB;
}
?>