<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

class KI_UPLOAD
{

    public $max_size = 0;
    public $max_width = 0;
    public $max_height = 0;
    public $max_filename = 0;
    public $allowed_types = "";
    public $file_temp = "";
    public $file_name = "";
    public $orig_name = "";
    public $file_type = "";
    public $file_size = "";
    public $file_ext = "";
    public $upload_path = "";
    public $overwrite = FALSE;
    public $encrypt_name = FALSE;
    public $is_image = FALSE;
    public $image_width = '';
    public $image_height = '';
    public $image_type = '';
    public $image_size_str = '';
    public $error_msg = array();
    public $mimes = array();
    public $remove_spaces = TRUE;
    public $xss_clean = FALSE;
    public $temp_prefix = "temp_file_";

    function KI_UPLOAD($props=array())
    {
	if (count($props) > 0)
	{
	    $this->initialize($props);
	}

	log_message('debug', "KI_UPLOAD Class Initialized");
    }

    function initialize($config=array())
    {
	$defaults = array(
	    'max_size' => 0,
	    'max_width' => 0,
	    'max_height' => 0,
	    'max_filename' => 0,
	    'allowed_types' => "",
	    'file_temp' => "",
	    'file_name' => "",
	    'orig_name' => "",
	    'file_type' => "",
	    'file_size' => "",
	    'file_ext' => "",
	    'upload_path' => "",
	    'overwrite' => FALSE,
	    'encrypt_name' => FALSE,
	    'is_image' => FALSE,
	    'image_width' => '',
	    'image_height' => '',
	    'image_type' => '',
	    'image_size_str' => '',
	    'error_msg' => array(),
	    'mimes' => array(),
	    'remove_spaces' => TRUE,
	    'xss_clean' => FALSE,
	    'temp_prefix' => "temp_file_"
	);

	foreach ($defaults as $key => $val)
	{
	    if (isset($config[$key]))
	    {
		$method = 'set_' . $key;

		if (method_exists($this, $method))
		{
		    $this->$method($config[$key]);
		}
		else
		{
		    $this->$key = $config[$key];
		}
	    }
	    else
	    {
		$this->$key = $val;
	    }
	}
    }

    function do_upload($field='userfile')
    {
	if (!isset($_FILES[$field]))
	{
	    $this->set_error('upload_no_file_selected');
	    return FALSE;
	}

	if (!$this->validate_upload_path())
	{
	    return FALSE;
	}

	if (!is_uploaded_file($_FILES[$field]['tmp_name']))
	{
	    $error = (!isset($_FILES[$field]['error'])) ? 4 : $_FILES[$field]['error'];

	    switch ($error)
	    {
		case 1: // UPLOAD_ERR_INI_SIZE
		    $this->set_error('upload_file_exceeds_limit');
		    break;
		case 2: // UPLOAD_ERR_FORM_SIZE
		    $this->set_error('upload_file_exceeds_form_limit');
		    break;
		case 3: // UPLOAD_ERR_PARTIAL
		    $this->set_error('upload_file_partial');
		    break;
		case 4: // UPLOAD_ERR_NO_FILE
		    $this->set_error('upload_no_file_selected');
		    break;
		case 6: // UPLOAD_ERR_NO_TMP_DIR
		    $this->set_error('upload_no_temp_directory');
		    break;
		case 7: // UPLOAD_ERR_CANT_WRITE
		    $this->set_error('upload_unable_to_write_file');
		    break;
		case 8: // UPLOAD_ERR_EXTENSION
		    $this->set_error('upload_stopped_by_extension');
		    break;
		default : $this->set_error('upload_no_file_selected');
		    break;
	    }

	    return FALSE;
	}

	$this->file_temp = $_FILES[$field]['tmp_name'];
	$this->file_name = $this->_prep_filename($_FILES[$field]['name']);
	$this->file_size = $_FILES[$field]['size'];
	$this->file_type = preg_replace("/^(.+?);.*$/", "\\1", $_FILES[$field]['type']);
	$this->file_type = strtolower($this->file_type);
	$this->file_ext = $this->get_extension($_FILES[$field]['name']);

	if ($this->file_size > 0)
	{
	    $this->file_size = round($this->file_size / 1024, 2);
	}

	if (!$this->is_allowed_filetype())
	{
	    $this->set_error('upload_invalid_filetype');
	    return FALSE;
	}

	if (!$this->is_allowed_filesize())
	{
	    $this->set_error('upload_invalid_filesize');
	    return FALSE;
	}

	if (!$this->is_allowed_dimensions())
	{
	    $this->set_error('upload_invalid_dimensions');
	    return FALSE;
	}

	$this->file_name = $this->clean_file_name($this->file_name);

	if ($this->max_filename > 0)
	{
	    $this->file_name = $this->limit_filename_length($this->file_name, $this->max_filename);
	}

	if ($this->remove_spaces == TRUE)
	{
	    $this->file_name = preg_replace("/\s+/", "_", $this->file_name);
	}

	$this->orig_name = $this->file_name;

	if ($this->overwrite == FALSE)
	{
	    $this->file_name = $this->set_filename($this->upload_path, $this->file_name);

	    if ($this->file_name === FALSE)
	    {
		return FALSE;
	    }
	}

	if (!@copy($this->file_temp, $this->upload_path . $this->file_name))
	{
	    if (!@move_uploaded_file($this->file_temp, $this->upload_path . $this->file_name))
	    {
		$this->set_error('upload_destination_error');
		return FALSE;
	    }
	}

	if ($this->xss_clean == TRUE)
	{
	    $this->do_xss_clean();
	}


	$this->set_image_properties($this->upload_path . $this->file_name);
	return TRUE;
    }

    function data()
    {
	return array(
	    'file_name' => $this->file_name,
	    'file_type' => $this->file_type,
	    'file_path' => $this->upload_path,
	    'full_path' => $this->upload_path . $this->file_name,
	    'raw_name' => str_replace($this->file_ext, '', $this->file_name),
	    'orig_name' => $this->orig_name,
	    'file_ext' => $this->file_ext,
	    'file_size' => $this->file_size,
	    'is_image' => $this->is_image(),
	    'image_width' => $this->image_width,
	    'image_height' => $this->image_height,
	    'image_type' => $this->image_type,
	    'image_size_str' => $this->image_size_str,
	);
    }

    function set_upload_path($path)
    {
	$this->upload_path = rtrim($path, '/') . '/';
    }

    function set_filename($path, $filename)
    {
	if ($this->encrypt_name == TRUE)
	{
	    mt_srand();
	    $filename = md5(uniqid(mt_rand())) . $this->file_ext;
	}

	if (!file_exists($path . $filename))
	{
	    return $filename;
	}

	unlink($path . $filename);
	$new_filename = $filename;

	if ($new_filename == '')
	{
	    $this->set_error('upload_bad_filename');
	    return FALSE;
	}
	else
	{
	    return $new_filename;
	}
    }

    function set_max_filesize($n)
    {
	$this->max_size = ((int) $n < 0) ? 0 : (int) $n;
    }

    function set_max_filename($n)
    {
	$this->max_filename = ((int) $n < 0) ? 0 : (int) $n;
    }

    function set_max_width($n)
    {
	$this->max_width = ((int) $n < 0) ? 0 : (int) $n;
    }

    function set_max_height($n)
    {
	$this->max_height = ((int) $n < 0) ? 0 : (int) $n;
    }

    function set_allowed_types($types)
    {
	$this->allowed_types = explode('|', $types);
    }

    function set_image_properties($path='')
    {
	if (!$this->is_image())
	    return;

	if (function_exists('getimagesize'))
	{
	    if (FALSE !== ($D = @getimagesize($path)))
	    {
		$types = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
		$this->image_width = $D['0'];
		$this->image_height = $D['1'];
		$this->image_type = (!isset($types[$D['2']])) ? 'unknown' : $types[$D['2']];
		$this->image_size_str = $D['3'];
	    }
	}
    }

    function set_xss_clean($flag=FALSE)
    {
	$this->xss_clean = ($flag == TRUE) ? TRUE : FALSE;
    }

    function is_image()
    {
	$png_mimes = array('image/x-png');
	$jpeg_mimes = array('image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg');

	if (in_array($this->file_type, $png_mimes))
	{
	    $this->file_type = 'image/png';
	}

	if (in_array($this->file_type, $jpeg_mimes))
	{
	    $this->file_type = 'image/jpeg';
	}

	$img_mimes = array(
	    'image/gif',
	    'image/jpeg',
	    'image/png',
	);

	return (in_array($this->file_type, $img_mimes, TRUE)) ? TRUE : FALSE;
    }

    function is_allowed_filetype()
    {
	if (count($this->allowed_types) == 0 OR !is_array($this->allowed_types))
	{
	    $this->set_error('upload_no_file_types');
	    return FALSE;
	}

	return in_array(str_replace('.', '', $this->file_ext), $this->allowed_types);
    }

    function is_allowed_filesize()
    {
	if ($this->max_size != 0 AND $this->file_size > $this->max_size)
	{
	    return FALSE;
	}
	else
	{
	    return TRUE;
	}
    }

    function is_allowed_dimensions()
    {
	if (!$this->is_image())
	{
	    return TRUE;
	}

	if (function_exists('getimagesize'))
	{
	    $D = @getimagesize($this->file_temp);

	    if ($this->max_width > 0 AND $D['0'] > $this->max_width)
	    {
		return FALSE;
	    }

	    if ($this->max_height > 0 AND $D['1'] > $this->max_height)
	    {
		return FALSE;
	    }

	    return TRUE;
	}

	return TRUE;
    }

    function validate_upload_path()
    {
	if ($this->upload_path == '')
	{
	    $this->set_error('upload_no_filepath');
	    return FALSE;
	}

	if (function_exists('realpath') AND @realpath($this->upload_path) !== FALSE)
	{
	    $this->upload_path = str_replace("\\", "/", realpath($this->upload_path));
	}

	if (!@is_dir($this->upload_path))
	{
	    $this->set_error('upload_no_filepath');
	    return FALSE;
	}

	if (!is_really_writable($this->upload_path))
	{
	    $this->set_error('upload_not_writable');
	    return FALSE;
	}

	$this->upload_path = preg_replace("/(.+?)\/*$/", "\\1/", $this->upload_path);
	return TRUE;
    }

    function get_extension($filename)
    {
	$x = explode('.', $filename);
	return '.' . end($x);
    }

    function clean_file_name($filename)
    {
	$bad = array(
	    "<!--",
	    "-->",
	    "'",
	    "<",
	    ">",
	    '"',
	    '&',
	    '$',
	    '=',
	    ';',
	    '?',
	    '/',
	    "%20",
	    "%22",
	    "%3c", // <
	    "%253c", // <
	    "%3e", // >
	    "%0e", // >
	    "%28", // (
	    "%29", // )
	    "%2528", // (
	    "%26", // &
	    "%24", // $
	    "%3f", // ?
	    "%3b", // ;
	    "%3d"  // =
	);

	$filename = str_replace($bad, '', $filename);
	return stripslashes($filename);
    }

    function limit_filename_length($filename, $length)
    {
	if (strlen($filename) < $length)
	    return $filename;

	$ext = '';

	if (strpos($filename, '.') !== FALSE)
	{
	    $parts = explode('.', $filename);
	    $ext = '.' . array_pop($parts);
	    $filename = implode('.', $parts);
	}

	return substr($filename, 0, ($length - strlen($ext))) . $ext;
    }

    function do_xss_clean()
    {
	$file = $this->upload_path . $this->file_name;

	if (filesize($file) == 0)
	    return FALSE;
	if (($data = @file_get_contents($file)) === FALSE)
	    return FALSE;
	if (!$fp = @fopen($file, FOPEN_READ_WRITE))
	    return FALSE;

	$KI = & get_instance();
	$data = $KI->input->xss_clean($data);

	flock($fp, LOCK_EX);
	fwrite($fp, $data);
	flock($fp, LOCK_UN);
	fclose($fp);
    }

    function set_error($msg)
    {
	$KI = & get_instance();
	$KI->lang->load('upload');

	if (is_array($msg))
	{
	    foreach ($msg as $val)
	    {
		$msg = ($KI->lang->line($val) == FALSE) ? $val : $KI->lang->line($val);
		$this->error_msg[] = $msg;
		log_message('error', $msg);
	    }
	}
	else
	{
	    $msg = ($KI->lang->line($msg) == FALSE) ? $msg : $KI->lang->line($msg);
	    $this->error_msg[] = $msg;
	    log_message('error', $msg);
	}
    }

    function display_errors($open='<p>', $close='</p>')
    {
	$str = '';

	foreach ($this->error_msg as $val)
	{
	    $str .= $open . $val . $close;
	}

	return $str;
    }

    function mimes_types($mime)
    {
	global $mimes;

	if (count($this->mimes) == 0)
	{
	    if (@require_once(APPPATH . 'config/mimes' . EXT))
	    {
		$this->mimes = $mimes;
		unset($mimes);
	    }
	}

	return (!isset($this->mimes[$mime])) ? FALSE : $this->mimes[$mime];
    }

    function _prep_filename($filename)
    {
	if (strpos($filename, '.') === FALSE)
	    return $filename;

	$parts = explode('.', $filename);
	$ext = array_pop($parts);
	$filename = array_shift($parts);

	foreach ($parts as $part)
	{
	    if ($this->mimes_types(strtolower($part)) === FALSE)
	    {
		$filename .= '.' . $part . '_';
	    }
	    else
	    {
		$filename .= '.' . $part;
	    }
	}

	$filename .= '.' . $ext;
	return $filename;
    }

}