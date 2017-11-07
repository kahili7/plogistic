<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

class CACHE_DUMMY extends KI_DRIVER 
{
	public function get($id)
	{
		return FALSE;
	}

	public function save($id, $data, $ttl = 60)
	{
		return TRUE;
	}
	
	public function delete($id)
	{
		return TRUE;
	}

	public function clean()
	{
		return TRUE;
	}

	 public function cache_info($type = NULL)
	 {
		 return FALSE;
	 }

	public function get_metadata($id)
	{
		return FALSE;
	}

	public function is_supported()
	{
		return TRUE;
	}
}