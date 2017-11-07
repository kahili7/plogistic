<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

class KI_DB_FORGE
{
	public $fields = array();
	public $keys = array();
	public $primary_keys = array();
	public $db_char_set = '';

	function KI_DB_FORGE()
	{
		$KI =& get_instance();
		$this->db =& $KI->db;
		log_message('debug', "Database Forge Class Initialized");
	}

	function create_database($db_name)
	{
		$sql = $this->_create_database($db_name);

		if(is_bool($sql)) return $sql;

		return $this->db->query($sql);
	}

	function drop_database($db_name)
	{
		$sql = $this->_drop_database($db_name);

		if(is_bool($sql)) return $sql;

		return $this->db->query($sql);
	}

	function add_key($key='', $primary=FALSE)
	{
		if(is_array($key))
		{
			foreach($key as $one)
			{
				$this->add_key($one, $primary);
			}

			return;
		}

		if($key == '')
		{
			show_error('Key information is required for that operation.');
		}

		if($primary === TRUE)
		{
			$this->primary_keys[] = $key;
		}
		else
		{
			$this->keys[] = $key;
		}
	}

	function add_field($field='')
	{
		if($field == '')
		{
			show_error('Field information is required.');
		}

		if(is_string($field))
		{
			if($field == 'id')
			{
				$this->add_field(array(
				'id' => array(
				'type' => 'INT',
				'constraint' => 9,
				'auto_increment' => TRUE
				)
				));
				$this->add_key('id', TRUE);
			}
			else
			{
				if(strpos($field, ' ') === FALSE)
				{
					show_error('Field information is required for that operation.');
				}

				$this->fields[] = $field;
			}
		}

		if(is_array($field))
		{
			$this->fields = array_merge($this->fields, $field);
		}
	}

	function create_table($table='', $if_not_exists=FALSE)
	{
		if($table == '')
		{
			show_error('A table name is required for that operation.');
		}

		if(count($this->fields) == 0)
		{
			show_error('Field information is required.');
		}

		$sql = $this->_create_table($this->db->dbprefix.$table, $this->fields, $this->primary_keys, $this->keys, $if_not_exists);
		$this->_reset();
		return $this->db->query($sql);
	}

	function drop_table($table_name)
	{
		$sql = $this->_drop_table($this->db->dbprefix.$table_name);

		if(is_bool($sql)) return $sql;

		return $this->db->query($sql);
	}

	function rename_table($table_name, $new_table_name)
	{
		if($table_name == '' OR $new_table_name == '')
		{
			show_error('A table name is required for that operation.');
		}

		$sql = $this->_rename_table($table_name, $new_table_name);
		return $this->db->query($sql);
	}

	function add_column($table='', $field=array(), $after_field='')
	{
		if($table == '')
		{
			show_error('A table name is required for that operation.');
		}

		$this->add_field(array_slice($field, 0, 1));

		if(count($this->fields) == 0)
		{
			show_error('Field information is required.');
		}

		$sql = $this->_alter_table('ADD', $this->db->dbprefix.$table, $this->fields, $after_field);
		$this->_reset();
		return $this->db->query($sql);
	}

	function drop_column($table='', $column_name='')
	{

		if($table == '')
		{
			show_error('A table name is required for that operation.');
		}

		if($column_name == '')
		{
			show_error('A column name is required for that operation.');
		}

		$sql = $this->_alter_table('DROP', $this->db->dbprefix.$table, $column_name);
		return $this->db->query($sql);
	}

	function modify_column($table='', $field=array())
	{
		if($table == '')
		{
			show_error('A table name is required for that operation.');
		}

		$this->add_field(array_slice($field, 0, 1));

		if(count($this->fields) == 0)
		{
			show_error('Field information is required.');
		}

		$sql = $this->_alter_table('CHANGE', $this->db->dbprefix.$table, $this->fields);
		$this->_reset();
		return $this->db->query($sql);
	}

	function _reset()
	{
		$this->fields = array();
		$this->keys = array();
		$this->primary_keys = array();
	}
}
?>