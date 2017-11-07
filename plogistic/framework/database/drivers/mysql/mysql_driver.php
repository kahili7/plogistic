<?
if(!defined('BASEPATH')) exit('No direct script access allowed');

class KI_DB_MYSQL_DRIVER extends KI_DB
{
	public $dbdriver = 'mysql';
	public $_escape_char = '`';
	public $delete_hack = TRUE;
	public $_count_string = 'SELECT COUNT(*) AS ';
	public $_random_keyword = ' RAND()';

	function db_connect()
	{
		if($this->port != '')
		{
			$this->hostname .= ':'.$this->port;
		}

		return @mysql_connect($this->hostname, $this->username, $this->password, TRUE);
	}

	function db_pconnect()
	{
		if($this->port != '')
		{
			$this->hostname .= ':'.$this->port;
		}

		return @mysql_pconnect($this->hostname, $this->username, $this->password);
	}

	function db_select()
	{
		return @mysql_select_db($this->database, $this->conn_id);
	}

	function db_set_charset($charset, $collation)
	{
		return @mysql_query("SET NAMES '".$this->escape_str($charset)."' COLLATE '".$this->escape_str($collation)."'", $this->conn_id);
	}

	function _version()
	{
		return "SELECT version() AS ver";
	}

	function _execute($sql)
	{
		$sql = $this->_prep_query($sql);
		return @mysql_query($sql, $this->conn_id);
	}

	function _prep_query($sql)
	{
		if($this->delete_hack === TRUE)
		{
			if(preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $sql))
			{
				$sql = preg_replace("/^\s*DELETE\s+FROM\s+(\S+)\s*$/", "DELETE FROM \\1 WHERE 1=1", $sql);
			}
		}

		return $sql;
	}

	function trans_begin($test_mode=FALSE)
	{
		if(!$this->trans_enabled) return TRUE;
		if($this->_trans_depth > 0) return TRUE;

		$this->_trans_failure = ($test_mode === TRUE) ? TRUE : FALSE;
		$this->simple_query('SET AUTOCOMMIT=0');
		$this->simple_query('START TRANSACTION');
		return TRUE;
	}

	function trans_commit()
	{
		if(!$this->trans_enabled) return TRUE;
		if($this->_trans_depth > 0) return TRUE;
		
		$this->simple_query('COMMIT');
		$this->simple_query('SET AUTOCOMMIT=1');
		return TRUE;
	}

	function trans_rollback()
	{
		if(!$this->trans_enabled) return TRUE;
		if($this->_trans_depth > 0) return TRUE;

		$this->simple_query('ROLLBACK');
		$this->simple_query('SET AUTOCOMMIT=1');
		return TRUE;
	}

	function escape_str($str)
	{
		if(is_array($str))
		{
			foreach($str as $key => $val)
			{
				$str[$key] = $this->escape_str($val);
			}

			return $str;
		}

		if(function_exists('mysql_real_escape_string') AND is_resource($this->conn_id))
		{
			return mysql_real_escape_string($str, $this->conn_id);
		}
		else if(function_exists('mysql_escape_string'))
		{
			return mysql_escape_string($str);
		}
		else
		{
			return addslashes($str);
		}
	}

	function affected_rows()
	{
		return @mysql_affected_rows($this->conn_id);
	}

	function insert_id()
	{
		return @mysql_insert_id($this->conn_id);
	}

	function count_all($table='')
	{
		if($table == '') return 0;

		$query = $this->query($this->_count_string.$this->_protect_identifiers('numrows')." FROM ".$this->_protect_identifiers($table, TRUE, NULL, FALSE));

		if($query->num_rows() == 0) return 0;

		$row = $query->row();
		return (int) $row->numrows;
	}

	function _list_tables($prefix_limit=FALSE)
	{
		$sql = "SHOW TABLES FROM ".$this->_escape_char.$this->database.$this->_escape_char;

		if($prefix_limit !== FALSE AND $this->dbprefix != '')
		{
			$sql .= " LIKE '".$this->dbprefix."%'";
		}

		return $sql;
	}

	function _list_columns($table='')
	{
		return "SHOW COLUMNS FROM ".$table;
	}

	function _field_data($table)
	{
		return "SELECT * FROM ".$table." LIMIT 1";
	}

	function _error_message()
	{
		return mysql_error($this->conn_id);
	}

	function _error_number()
	{
		return mysql_errno($this->conn_id);
	}

	function _escape_identifiers($item)
	{
		if($this->_escape_char == '') return $item;

		foreach($this->_reserved_identifiers as $id)
		{
			if(strpos($item, '.'.$id) !== FALSE)
			{
				$str = $this->_escape_char. str_replace('.', $this->_escape_char.'.', $item);
				
				return preg_replace('/['.$this->_escape_char.']+/', $this->_escape_char, $str);
			}
		}

		if(strpos($item, '.') !== FALSE)
		{
			$str = $this->_escape_char.str_replace('.', $this->_escape_char.'.'.$this->_escape_char, $item).$this->_escape_char;
		}
		else
		{
			$str = $this->_escape_char.$item.$this->_escape_char;
		}

		return preg_replace('/['.$this->_escape_char.']+/', $this->_escape_char, $str);
	}

	function _from_tables($tables)
	{
		if(!is_array($tables))
		{
			$tables = array($tables);
		}

		return '('.implode(', ', $tables).')';
	}

	function _insert($table, $keys, $values)
	{
		return "INSERT INTO ".$table." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
	}
	
	function _insert_ignore($table, $keys, $values)
	{
		return "INSERT IGNORE INTO ".$table." (".implode(', ', $keys).") VALUES (".implode(', ', $values).")";
	}
	
	function _update($table, $values, $where, $orderby=array(), $limit=FALSE)
	{
		foreach($values as $key => $val)
		{
			$valstr[] = $key." = ".$val;
		}

		$limit = (!$limit) ? '' : ' LIMIT '.$limit;
		$orderby = (count($orderby) >= 1) ? ' ORDER BY '.implode(", ", $orderby) : '';
		$sql = "UPDATE ".$table." SET ".implode(', ', $valstr);
		$sql .= ($where != '' AND count($where) >= 1) ? " WHERE ".implode(" ", $where) : '';
		$sql .= $orderby.$limit;

		return $sql;
	}

	function _truncate($table)
	{
		return "TRUNCATE ".$table;
	}

	function _delete($table, $where=array(), $like=array(), $limit=FALSE)
	{
		$conditions = '';

		if(count($where) > 0 OR count($like) > 0)
		{
			$conditions = "\nWHERE ";
			$conditions .= implode("\n", $this->ar_where);

			if(count($where) > 0 && count($like) > 0)
			{
				$conditions .= " AND ";
			}
			
			$conditions .= implode("\n", $like);
		}

		$limit = (!$limit) ? '' : ' LIMIT '.$limit;

		return "DELETE FROM ".$table.$conditions.$limit;
	}

	function _delete_ignore($table, $where=array(), $like=array(), $limit=FALSE)
	{
		$conditions = '';

		if(count($where) > 0 OR count($like) > 0)
		{
			$conditions = "\nWHERE ";
			$conditions .= implode("\n", $this->ar_where);

			if(count($where) > 0 && count($like) > 0)
			{
				$conditions .= " AND ";
			}
			
			$conditions .= implode("\n", $like);
		}

		$limit = (!$limit) ? '' : ' LIMIT '.$limit;

		return "DELETE IGNORE FROM ".$table.$conditions.$limit;
	}
	
	function _limit($sql, $limit, $offset)
	{
		if($offset == 0)
		{
			$offset = '';
		}
		else
		{
			$offset .= ", ";
		}

		return $sql."LIMIT ".$offset.$limit;
	}

	function _close($conn_id)
	{
		@mysql_close($conn_id);
	}
}
?>