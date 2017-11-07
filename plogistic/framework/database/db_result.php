<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

class KI_DB_RESULT
{
	public $conn_id = NULL;
	public $result_id = NULL;
	public $result_array = array();
	public $result_object = array();
	public $current_row = 0;
	public $num_rows = 0;
	public $row_data = NULL;

	function result($type='object')
	{	
		return ($type == 'object') ? $this->result_object() : $this->result_array();
	}

	function result_object()
	{
		if(count($this->result_object) > 0)
		{
			return $this->result_object;
		}
		
		if($this->result_id === FALSE OR $this->num_rows() == 0)
		{
			return array();
		}

		$this->_data_seek(0);
		
		while(($row = $this->_fetch_object()))
		{
			$this->result_object[] = $row;
		}
		
		return $this->result_object;
	}
	
	function result_array()
	{
		if(count($this->result_array) > 0)
		{
			return $this->result_array;
		}

		if($this->result_id === FALSE OR $this->num_rows() == 0)
		{
			return array();
		}

		$this->_data_seek(0);
		
		while(($row = $this->_fetch_assoc()))
		{
			$this->result_array[] = $row;
		}
		
		return $this->result_array;
	}

	function row($n=0, $type='object')
	{
		if(!is_numeric($n))
		{

			if(!is_array($this->row_data))
			{
				$this->row_data = $this->row_array(0);
			}
		
			if(array_key_exists($n, $this->row_data))
			{
				return $this->row_data[$n];
			}
			
			$n = 0;
		}
		
		return ($type == 'object') ? $this->row_object($n) : $this->row_array($n);
	}

	function set_row($key, $value=NULL)
	{
		if(!is_array($this->row_data))
		{
			$this->row_data = $this->row_array(0);
		}
	
		if(is_array($key))
		{
			foreach($key as $k => $v)
			{
				$this->row_data[$k] = $v;
			}
			
			return;
		}
	
		if($key != '' AND ! is_null($value))
		{
			$this->row_data[$key] = $value;
		}
	}

	function row_object($n=0)
	{
		$result = $this->result_object();
		
		if(count($result) == 0) return $result;

		if($n != $this->current_row AND isset($result[$n]))
		{
			$this->current_row = $n;
		}

		return $result[$this->current_row];
	}

	function row_array($n=0)
	{
		$result = $this->result_array();

		if(count($result) == 0) return $result;
			
		if($n != $this->current_row AND isset($result[$n]))
		{
			$this->current_row = $n;
		}
		
		return $result[$this->current_row];
	}

	function first_row($type='object')
	{
		$result = $this->result($type);

		if(count($result) == 0) return $result;
	
		return $result[0];
	}
	
	function last_row($type='object')
	{
		$result = $this->result($type);

		if(count($result) == 0) return $result;
	
		return $result[count($result) -1];
	}	

	function next_row($type='object')
	{
		$result = $this->result($type);

		if(count($result) == 0) return $result;

		if(isset($result[$this->current_row + 1]))
		{
			++$this->current_row;
		}
				
		return $result[$this->current_row];
	}
	
	function previous_row($type='object')
	{
		$result = $this->result($type);

		if(count($result) == 0)
		{
			return $result;
		}

		if (isset($result[$this->current_row - 1]))
		{
			--$this->current_row;
		}
		
		return $result[$this->current_row];
	}

	function num_rows() { return $this->num_rows; }
	function num_fields() { return 0; }
	function list_fields() { return array(); }
	function field_data() { return array(); }	
	function free_result() { return TRUE; }
	function _data_seek() { return TRUE; }
	function _fetch_assoc() { return array(); }	
	function _fetch_object() { return array(); }
}
?>