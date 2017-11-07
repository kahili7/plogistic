<?

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class KI_VALIDATION
{

    public $KI;
    public $error_string = '';
    public $_error_array = array();
    public $_rules = array();
    public $_fields = array();
    public $_error_messages = array();
    public $_current_field = '';
    public $_safe_form_data = FALSE;
    public $_error_prefix = '<p>';
    public $_error_suffix = '</p>';

    function KI_VALIDATION()
    {
	$this->KI = & get_instance();

	if (function_exists('mb_internal_encoding'))
	{
	    mb_internal_encoding($this->KI->config->item('charset'));
	}

	log_message('debug', "Validation Class Initialized");
    }

    function set_fields($data='', $field='')
    {
	if ($data == '')
	{
	    if (count($this->_fields) == 0)
	    {
		return FALSE;
	    }
	}
	else
	{
	    if (!is_array($data))
	    {
		$data = array($data => $field);
	    }

	    if (count($data) > 0)
	    {
		$this->_fields = $data;
	    }
	}

	foreach ($this->_fields as $key => $val)
	{
	    $this->$key = (!isset($_POST[$key])) ? '' : $this->prep_for_form($_POST[$key]);

	    $error = $key . '_error';

	    if (!isset($this->$error))
	    {
		$this->$error = '';
	    }
	}

	return TRUE;
    }

    function set_rules($data, $rules='')
    {
	if (!is_array($data))
	{
	    if ($rules == '')
		return;

	    $data = array($data => $rules);
	}

	foreach ($data as $key => $val)
	{
	    $this->_rules[$key] = $val;
	}
    }

    function set_message($lang, $val='')
    {
	if (!is_array($lang))
	{
	    $lang = array($lang => $val);
	}

	$this->_error_messages = array_merge($this->_error_messages, $lang);
    }

    function set_error_delimiters($prefix='<p>', $suffix='</p>')
    {
	$this->_error_prefix = $prefix;
	$this->_error_suffix = $suffix;
    }

    function run()
    {
	if (count($_POST) == 0 OR count($this->_rules) == 0)
	{
	    return FALSE;
	}

	$this->KI->lang->load('validation');

	foreach ($this->_rules as $field => $rules)
	{
	    $ex = explode('|', $rules);

	    if (!in_array('required', $ex, TRUE))
	    {
		if (!isset($_POST[$field]) OR $_POST[$field] == '')
		{
		    continue;
		}
	    }

	    if (!isset($_POST[$field]))
	    {
		if (in_array('isset', $ex, TRUE) OR in_array('required', $ex))
		{
		    if (!isset($this->_error_messages['isset']))
		    {
			if (FALSE === ($line = $this->KI->lang->line('isset')))
			{
			    $line = 'The field was not set';
			}
		    }
		    else
		    {
			$line = $this->_error_messages['isset'];
		    }

		    $mfield = (!isset($this->_fields[$field])) ? $field : $this->_fields[$field];
		    $message = sprintf($line, $mfield);

		    $error = $field . '_error';
		    $this->$error = $this->_error_prefix . $message . $this->_error_suffix;
		    $this->_error_array[] = $message;
		}

		continue;
	    }

	    $this->_current_field = $field;

	    foreach ($ex As $rule)
	    {
		$callback = FALSE;

		if (substr($rule, 0, 9) == 'callback_')
		{
		    $rule = substr($rule, 9);
		    $callback = TRUE;
		}

		$param = FALSE;

		if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match))
		{
		    $rule = $match[1];
		    $param = $match[2];
		}

		if ($callback === TRUE)
		{
		    if (!method_exists($this->KI, $rule))
		    {
			continue;
		    }

		    $result = $this->KI->$rule($_POST[$field], $param);

		    if (!in_array('required', $ex, TRUE) AND $result !== FALSE)
		    {
			continue 2;
		    }
		}
		else
		{
		    if (!method_exists($this, $rule))
		    {
			if (function_exists($rule))
			{
			    $_POST[$field] = $rule($_POST[$field]);
			    $this->$field = $_POST[$field];
			}

			continue;
		    }

		    $result = $this->$rule($_POST[$field], $param);
		}

		if ($result === FALSE)
		{
		    if (!isset($this->_error_messages[$rule]))
		    {
			if (FALSE === ($line = $this->KI->lang->line($rule)))
			{
			    $line = 'Unable to access an error message corresponding to your field name.';
			}
		    }
		    else
		    {
			$line = $this->_error_messages[$rule];
		    }

		    $mfield = (!isset($this->_fields[$field])) ? $field : $this->_fields[$field];
		    $mparam = (!isset($this->_fields[$param])) ? $param : $this->_fields[$param];
		    $message = sprintf($line, $mfield, $mparam);

		    $error = $field . '_error';
		    $this->$error = $this->_error_prefix . $message . $this->_error_suffix;

		    $this->_error_array[] = $message;
		    continue 2;
		}
	    }
	}

	$total_errors = count($this->_error_array);

	if ($total_errors > 0)
	{
	    $this->_safe_form_data = TRUE;
	}

	$this->set_fields();

	if ($total_errors == 0)
	{
	    return TRUE;
	}

	foreach ($this->_error_array as $val)
	{
	    $this->error_string .= $this->_error_prefix . $val . $this->_error_suffix . "\n";
	}

	return FALSE;
    }

    function required($str)
    {
	if (!is_array($str))
	{
	    return (trim($str) == '') ? FALSE : TRUE;
	}
	else
	{
	    return (!empty($str));
	}
    }

    function matches($str, $field)
    {
	if (!isset($_POST[$field]))
	{
	    return FALSE;
	}

	return ($str !== $_POST[$field]) ? FALSE : TRUE;
    }

    function min_length($str, $val)
    {
	if (preg_match("/[^0-9]/", $val))
	{
	    return FALSE;
	}

	if (function_exists('mb_strlen'))
	{
	    return (mb_strlen($str) < $val) ? FALSE : TRUE;
	}

	return (strlen($str) < $val) ? FALSE : TRUE;
    }

    function max_length($str, $val)
    {
	if (preg_match("/[^0-9]/", $val))
	{
	    return FALSE;
	}

	if (function_exists('mb_strlen'))
	{
	    return (mb_strlen($str) > $val) ? FALSE : TRUE;
	}

	return (strlen($str) > $val) ? FALSE : TRUE;
    }

    function exact_length($str, $val)
    {
	if (preg_match("/[^0-9]/", $val))
	{
	    return FALSE;
	}

	if (function_exists('mb_strlen'))
	{
	    return (mb_strlen($str) != $val) ? FALSE : TRUE;
	}

	return (strlen($str) != $val) ? FALSE : TRUE;
    }

    function valid_email($str)
    {
	return (!preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
    }

    function valid_emails($str)
    {
	if (strpos($str, ',') === FALSE)
	{
	    return $this->valid_email(trim($str));
	}

	foreach (explode(',', $str) as $email)
	{
	    if (trim($email) != '' && $this->valid_email(trim($email)) === FALSE)
	    {
		return FALSE;
	    }
	}

	return TRUE;
    }

    function valid_ip($ip)
    {
	return $this->KI->input->valid_ip($ip);
    }

    function alpha($str)
    {
	return (!preg_match("/^([a-z])+$/i", $str)) ? FALSE : TRUE;
    }

    function alpha_numeric($str)
    {
	return (!preg_match("/^([a-z0-9])+$/i", $str)) ? FALSE : TRUE;
    }

    function alpha_dash($str)
    {
	return (!preg_match("/^([-a-z0-9_-])+$/i", $str)) ? FALSE : TRUE;
    }

    function numeric($str)
    {
	return (bool) preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);
    }

    function is_numeric($str)
    {
	return (!is_numeric($str)) ? FALSE : TRUE;
    }

    function integer($str)
    {
	return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
    }

    function is_natural($str)
    {
	return (bool) preg_match('/^[0-9]+$/', $str);
    }

    function is_natural_no_zero($str)
    {
	if (!preg_match('/^[0-9]+$/', $str))
	{
	    return FALSE;
	}

	if ($str == 0)
	{
	    return FALSE;
	}

	return TRUE;
    }

    function valid_base64($str)
    {
	return (bool) !preg_match('/[^a-zA-Z0-9\/\+=]/', $str);
    }

    function set_select($field='', $value='')
    {
	if ($field == '' OR $value == '' OR !isset($_POST[$field]))
	{
	    return '';
	}

	if ($_POST[$field] == $value)
	{
	    return ' selected="selected"';
	}
    }

    function set_radio($field='', $value='')
    {
	if ($field == '' OR $value == '' OR !isset($_POST[$field]))
	{
	    return '';
	}

	if ($_POST[$field] == $value)
	{
	    return ' checked="checked"';
	}
    }

    function set_checkbox($field='', $value='')
    {
	if ($field == '' OR $value == '' OR !isset($_POST[$field]))
	{
	    return '';
	}

	if ($_POST[$field] == $value)
	{
	    return ' checked="checked"';
	}
    }

    function prep_for_form($data = '')
    {
	if (is_array($data))
	{
	    foreach ($data as $key => $val)
	    {
		$data[$key] = $this->prep_for_form($val);
	    }

	    return $data;
	}

	if ($this->_safe_form_data == FALSE OR $data == '')
	{
	    return $data;
	}

	return str_replace(array("'", '"', '<', '>'), array("&#39;", "&quot;", '&lt;', '&gt;'), stripslashes($data));
    }

    function prep_url($str = '')
    {
	if ($str == 'http://' OR $str == '')
	{
	    $_POST[$this->_current_field] = '';
	    return;
	}

	if (substr($str, 0, 7) != 'http://' && substr($str, 0, 8) != 'https://')
	{
	    $str = 'http://' . $str;
	}

	$_POST[$this->_current_field] = $str;
    }

    function strip_image_tags($str)
    {
	$_POST[$this->_current_field] = $this->KI->input->strip_image_tags($str);
    }

    function xss_clean($str)
    {
	$_POST[$this->_current_field] = $this->KI->input->xss_clean($str);
    }

    function encode_php_tags($str)
    {
	$_POST[$this->_current_field] = str_replace(array('<?php', '<?PHP', '<?', '?>'), array('&lt;?php', '&lt;?PHP', '&lt;?', '?&gt;'), $str);
    }

}

?>