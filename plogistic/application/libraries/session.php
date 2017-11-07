<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

class KI_SESSION
{

    public $KI;
    public $now;
    public $encryption = TRUE;
    public $use_database = FALSE;
    public $session_table = FALSE;
    public $sess_length = 7200;
    public $sess_cookie = 'KI_SESSION';
    public $userdata = array();
    public $gc_probability = 5;
    public $flashdata_key = 'flash';
    public $time_to_update = 300;
    public $session_id = '';

    function KI_SESSION()
    {
	$this->KI = & get_instance();
	log_message('debug', "Session Class Initialized");
	$this->sess_run();
    }

    function sess_run()
    {
	if (is_numeric($this->KI->config->item('sess_time_to_update')))
	{
	    $this->time_to_update = $this->KI->config->item('sess_time_to_update');
	}

	if (strtolower($this->KI->config->item('time_reference')) == 'gmt')
	{
	    $now = time();
	    $this->now = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));

	    if (strlen($this->now) < 10)
	    {
		$this->now = time();
		log_message('error', 'The session class could not set a proper GMT timestamp so the local time() value was used.');
	    }
	}
	else
	{
	    $this->now = time();
	}

	$expiration = $this->KI->config->item('sess_expiration');

	if (is_numeric($expiration))
	{
	    if ($expiration > 0)
	    {
		$this->sess_length = $this->KI->config->item('sess_expiration');
	    }
	    else
	    {
		$this->sess_length = (60 * 60 * 24 * 365 * 2);
	    }
	}

	$this->encryption = $this->KI->config->item('sess_encrypt_cookie');

	if ($this->encryption == TRUE)
	{
	    $this->KI->load->library('encrypt');
	}

	if ($this->KI->config->item('sess_use_database') === TRUE AND $this->KI->config->item('sess_table_name') != '')
	{
	    $this->use_database = TRUE;
	    $this->session_table = $this->KI->config->item('sess_table_name');

	    if (!isset($this->KI->db))
		$this->KI->load->database();
	}

	if ($this->KI->config->item('sess_cookie_name') != FALSE)
	{
	    $this->sess_cookie = $this->KI->config->item('cookie_prefix') . $this->KI->config->item('sess_cookie_name');
	}

	if (!$this->sess_read())
	{
	    $this->sess_create();
	}
	else
	{
	    if (($this->userdata['last_activity'] + $this->time_to_update) < $this->now)
	    {
		$this->sess_update();
	    }
	}

	if ($this->use_database == TRUE)
	{
	    $this->sess_gc();
	}

	$this->_flashdata_sweep();
	$this->_flashdata_mark();
    }

    function sess_read()
    {
	$session = $this->KI->input->cookie($this->sess_cookie);

	if ($session === FALSE)
	{
	    log_message('debug', 'A session cookie was not found.');
	    return FALSE;
	}

	if ($this->encryption == TRUE)
	{
	    $session = $this->KI->encrypt->decode($session);
	}

	$session = @unserialize($this->strip_slashes($session));

	if (!is_array($session) OR !isset($session['session_id']))
	{
	    log_message('error', 'The session cookie data did not contain a valid session_id. This could be a possible hacking attempt.');
	    return FALSE;
	}

	$this->session_id = $session['session_id'];

	if ($this->use_database == TRUE)
	{
	    $this->KI->db->where('session_id', $this->session_id);

	    if ($this->KI->config->item('sess_match_ip') == TRUE)
	    {
		$this->KI->db->where('ip_address', $this->KI->input->ip_address());
	    }

	    if ($this->KI->config->item('sess_match_useragent') == TRUE)
	    {
		$this->KI->db->where('user_agent', trim(substr($this->KI->input->user_agent(), 0, 50)));
	    }

	    $query = $this->KI->db->get($this->session_table);

	    if ($query->num_rows() == 0)
	    {
		$this->sess_destroy();
		return FALSE;
	    }

	    $row = $query->row();
	    log_message('debug', '!DBSESSREAD:' . $row->session_data);

	    $session = @unserialize($row->session_data);

	    if (!is_array($session))
		$session = array();

	    $session['session_id'] = $this->session_id;
	    $session['ip_address'] = $row->ip_address;
	    $session['user_agent'] = $row->user_agent;
	    $session['last_activity'] = $row->last_activity;
	}

	if (($session['last_activity'] + $this->sess_length) < $this->now)
	{
	    $this->sess_destroy();
	    return FALSE;
	}

	if ($this->KI->config->item('sess_match_ip') == TRUE AND $session['ip_address'] != $this->KI->input->ip_address())
	{
	    $this->sess_destroy();
	    return FALSE;
	}

	if ($this->KI->config->item('sess_match_useragent') == TRUE AND trim($session['user_agent']) != trim(substr($this->KI->input->user_agent(), 0, 50)))
	{
	    $this->sess_destroy();
	    return FALSE;
	}

	$this->userdata = $session;
	unset($session);
	return TRUE;
    }

    function sess_write_database()
    {
	$db_data = array(
	    'ip_address' => $this->userdata['ip_address'],
	    'user_agent' => $this->userdata['user_agent'],
	    'last_activity' => $this->userdata['last_activity']
	);

	$db_userdata = $this->userdata;
	unset($db_userdata['session_id']);
	unset($db_userdata['ip_address']);
	unset($db_userdata['user_agent']);
	unset($db_userdata['last_activity']);
	$db_data['session_data'] = serialize($db_userdata);

	log_message('debug', 'DBSESS:' . $this->KI->db->update_string($this->session_table, $db_data, array('session_id' => $this->session_id)));

	$this->KI->db->query($this->KI->db->update_string($this->session_table, $db_data, array('session_id' => $this->session_id)));
    }

    function sess_write_cookie()
    {
	if ($this->use_database == TRUE)
	{
	    $cookie_data = serialize(array('session_id' => $this->session_id));
	}
	else
	{
	    $cookie_data = serialize($this->userdata);
	}

	if ($this->encryption == TRUE)
	{
	    $cookie_data = $this->KI->encrypt->encode($cookie_data);
	}

	setcookie($this->sess_cookie, $cookie_data, $this->sess_length + time(),
		$this->KI->config->item('cookie_path'), $this->KI->config->item('cookie_domain'), 0);
    }

    function sess_create()
    {
	$sessid = '';

	while (strlen($sessid) < 32)
	{
	    $sessid .= mt_rand(0, mt_getrandmax());
	}

	$this->session_id = md5(uniqid($sessid, TRUE));
	$this->userdata = array(
	    'session_id' => $this->session_id,
	    'ip_address' => $this->KI->input->ip_address(),
	    'user_agent' => substr($this->KI->input->user_agent(), 0, 50),
	    'last_activity' => $this->now
	);

	if ($this->use_database == TRUE)
	{
	    $this->KI->db->query($this->KI->db->insert_string($this->session_table, $this->userdata));
	}

	$this->sess_write_cookie();
    }

    function sess_update()
    {
	$new_sessid = '';

	while (strlen($new_sessid) < 32)
	{
	    $new_sessid .= mt_rand(0, mt_getrandmax());
	}

	$new_sessid = md5(uniqid($new_sessid, TRUE));
	$this->userdata['session_id'] = $new_sessid;
	$this->userdata['last_activity'] = $this->now;

	if ($this->use_database == TRUE)
	{
	    $this->sess_write_database();
	}

	$this->sess_write_cookie();
    }

    function sess_destroy()
    {
	if ($this->use_database == TRUE)
	{
	    $this->KI->db->where('session_id', $this->session_id);
	    $this->KI->db->delete($this->session_table);
	}

	setcookie($this->sess_cookie,
		addslashes(serialize(array())),
		($this->now - 31500000),
		$this->KI->config->item('cookie_path'),
		$this->KI->config->item('cookie_domain'),
		0);
    }

    function sess_gc()
    {
	srand(time());

	if ((rand() % 100) < $this->gc_probability)
	{
	    $expire = $this->now - $this->sess_length;
	    $this->KI->db->where("last_activity < {$expire}");
	    $this->KI->db->delete($this->session_table);
	    log_message('debug', 'Session garbage collection performed.');
	}
    }

    function userdata($item)
    {
	return (!isset($this->userdata[$item])) ? FALSE : $this->userdata[$item];
    }

    function all_userdata()
    {
	return (!isset($this->userdata)) ? FALSE : $this->userdata;
    }

    function set_userdata($newdata=array(), $newval='')
    {
	if (is_string($newdata))
	{
	    $newdata = array($newdata => $newval);
	}

	if (count($newdata) > 0)
	{
	    foreach ($newdata as $key => $val)
	    {
		$this->userdata[$key] = $val;
	    }
	}

	if ($this->use_database == TRUE)
	{
	    $this->sess_write_database();
	}

	$this->sess_write_cookie();
    }

    function unset_userdata($newdata=array())
    {
	if (is_string($newdata))
	{
	    $newdata = array($newdata => '');
	}

	if (count($newdata) > 0)
	{
	    foreach ($newdata as $key => $val)
	    {
		unset($this->userdata[$key]);
	    }
	}

	if ($this->use_database == TRUE)
	{
	    $this->sess_write_database();
	}

	$this->sess_write_cookie();
    }

    function strip_slashes($vals)
    {
	if (is_array($vals))
	{
	    foreach ($vals as $key => $val)
	    {
		$vals[$key] = $this->strip_slashes($val);
	    }
	}
	else
	{
	    $vals = stripslashes($vals);
	}

	return $vals;
    }

    function set_flashdata($newdata=array(), $newval='')
    {
	if (is_string($newdata))
	{
	    $newdata = array($newdata => $newval);
	}

	if (count($newdata) > 0)
	{
	    foreach ($newdata as $key => $val)
	    {
		$flashdata_key = $this->flashdata_key . ':new:' . $key;
		$this->set_userdata($flashdata_key, $val);
	    }
	}
    }

    function keep_flashdata($key)
    {
	$old_flashdata_key = $this->flashdata_key . ':old:' . $key;
	$value = $this->userdata($old_flashdata_key);

	$new_flashdata_key = $this->flashdata_key . ':new:' . $key;
	$this->set_userdata($new_flashdata_key, $value);
    }

    function flashdata($key)
    {
	$flashdata_key = $this->flashdata_key . ':old:' . $key;
	return $this->userdata($flashdata_key);
    }

    function _flashdata_mark()
    {
	$userdata = $this->all_userdata();

	foreach ($userdata as $name => $value)
	{
	    $parts = explode(':new:', $name);

	    if (is_array($parts) && count($parts) === 2)
	    {
		$new_name = $this->flashdata_key . ':old:' . $parts[1];
		$this->set_userdata($new_name, $value);
		$this->unset_userdata($name);
	    }
	}
    }

    function _flashdata_sweep()
    {
	$userdata = $this->all_userdata();

	foreach ($userdata as $key => $value)
	{
	    if (strpos($key, ':old:'))
	    {
		$this->unset_userdata($key);
	    }
	}
    }

}

?>