<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

class KI_DB_Cache
{
	public $KI;
	public $db;
	
	function KI_DB_Cache(&$db)
	{
		$this->KI =& get_instance();
		$this->db =& $db;
		$this->KI->load->helper('file');	
	}
	
	function check_path($path='')
	{
		if($path == '')
		{
			if($this->db->cachedir == '')
			{
				return $this->db->cache_off();
			}
		
			$path = $this->db->cachedir;
		}
	
		$path = preg_replace("/(.+?)\/*$/", "\\1/",  $path);

		if(!is_dir($path) OR ! is_really_writable($path))
		{
			return $this->db->cache_off();
		}
		
		$this->db->cachedir = $path;
		return TRUE;
	}
	
	function read($sql)
	{
		if(!$this->check_path()) return $this->db->cache_off();

		$segment_one = ($this->KI->uri->segment(1) == FALSE) ? 'default' : $this->KI->uri->segment(1);
		$segment_two = ($this->KI->uri->segment(2) == FALSE) ? 'index' : $this->KI->uri->segment(2);
		$filepath = $this->db->cachedir.$segment_one.'+'.$segment_two.'/'.md5($sql);		
		
		if(FALSE === ($cachedata = read_file($filepath)))
		{	
			return FALSE;
		}
		
		return unserialize($cachedata);			
	}	

	function write($sql, $object)
	{
		if(!$this->check_path()) return $this->db->cache_off();

		$segment_one = ($this->KI->uri->segment(1) == FALSE) ? 'default' : $this->KI->uri->segment(1);
		$segment_two = ($this->KI->uri->segment(2) == FALSE) ? 'index' : $this->KI->uri->segment(2);
		$dir_path = $this->db->cachedir.$segment_one.'+'.$segment_two.'/';
		$filename = md5($sql);
	
		if(!@is_dir($dir_path))
		{
			if(!@mkdir($dir_path, DIR_WRITE_MODE))
			{
				return FALSE;
			}
			
			@chmod($dir_path, DIR_WRITE_MODE);			
		}
		
		if(write_file($dir_path.$filename, serialize($object)) === FALSE)
		{
			return FALSE;
		}
		
		@chmod($dir_path.$filename, DIR_WRITE_MODE);
		return TRUE;
	}

	function delete($segment_one='', $segment_two='')
	{	
		if($segment_one == '')
		{
			$segment_one  = ($this->KI->uri->segment(1) == FALSE) ? 'default' : $this->KI->uri->segment(1);
		}
		
		if($segment_two == '')
		{
			$segment_two = ($this->KI->uri->segment(2) == FALSE) ? 'index' : $this->KI->uri->segment(2);
		}
		
		$dir_path = $this->db->cachedir.$segment_one.'+'.$segment_two.'/';
		delete_files($dir_path, TRUE);
	}
	
	function delete_all()
	{
		delete_files($this->db->cachedir, TRUE);
	}

}
?>