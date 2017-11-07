<?
if(!defined('BASEPATH')) exit('No direct script access allowed');

class KI_DB_MYSQL_RESULT extends KI_DB_RESULT
{
	function num_rows()
	{
		return @mysql_num_rows($this->result_id);
	}
	
	function num_fields()
	{
		return @mysql_num_fields($this->result_id);
	}
	
	function list_fields()
	{
		$field_names = array();
		
		while(($field = mysql_fetch_field($this->result_id)))
		{
			$field_names[] = $field->name;
		}
		
		return $field_names;
	}

	function field_data()
	{
		$retval = array();
		
		while(($field = mysql_fetch_field($this->result_id)))
		{	
			$F = new stdClass();
			$F->name = $field->name;
			$F->type = $field->type;
			$F->default = $field->def;
			$F->max_length = $field->max_length;
			$F->primary_key = $field->primary_key;
			
			$retval[] = $F;
		}
		
		return $retval;
	}
	
	function free_result()
	{
		if (is_resource($this->result_id))
		{
			mysql_free_result($this->result_id);
			$this->result_id = FALSE;
		}
	}
	
	function _data_seek($n = 0)
	{
		return mysql_data_seek($this->result_id, $n);
	}

	function _fetch_assoc()
	{
		return mysql_fetch_assoc($this->result_id);
	}
	
	function _fetch_object()
	{
		return mysql_fetch_object($this->result_id);
	}
}
?>