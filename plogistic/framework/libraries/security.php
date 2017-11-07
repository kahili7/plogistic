<?

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class KI_SECURITY
{

    public $xss_hash = '';
    public $csrf_hash = '';
    public $csrf_expire = 7200;  // Two hours (in seconds)
    public $csrf_token_name = 'ci_csrf_token';
    public $csrf_cookie_name = 'ci_csrf_token';
    public $never_allowed_str = array(
	'document.cookie' => '[removed]',
	'document.write' => '[removed]',
	'.parentNode' => '[removed]',
	'.innerHTML' => '[removed]',
	'window.location' => '[removed]',
	'-moz-binding' => '[removed]',
	'<!--' => '&lt;!--',
	'-->' => '--&gt;',
	'<![CDATA[' => '&lt;![CDATA['
    );
    public $never_allowed_regex = array(
	"javascript\s*:" => '[removed]',
	"expression\s*(\(|&\#40;)" => '[removed]', // CSS and IE
	"vbscript\s*:" => '[removed]', // IE, surprise!
	"Redirect\s+302" => '[removed]'
    );

    public function __construct()
    {
	$this->csrf_token_name = (config_item('csrf_token_name')) ? config_item('csrf_token_name') : 'csrf_token_name';
	$this->csrf_cookie_name = (config_item('csrf_cookie_name')) ? config_item('csrf_cookie_name') : 'csrf_cookie_name';
	$this->csrf_expire = (config_item('csrf_expire')) ? config_item('csrf_expire') : 7200;
	$this->csrf_cookie_name = (config_item('cookie_prefix')) ? config_item('cookie_prefix') . $this->csrf_token_name : $this->csrf_token_name;
	$this->_csrf_set_hash();
	log_message('debug', "Security Class Initialized");
    }

    public function csrf_verify()
    {
	if (count($_POST) == 0)
	{
	    return $this->csrf_set_cookie();
	}

	if (!isset($_POST[$this->csrf_token_name]) OR !isset($_COOKIE[$this->csrf_cookie_name]))
	{
	    $this->csrf_show_error();
	}

	if ($_POST[$this->csrf_token_name] != $_COOKIE[$this->csrf_cookie_name])
	{
	    $this->csrf_show_error();
	}

	unset($_POST[$this->csrf_token_name]);
	unset($_COOKIE[$this->csrf_cookie_name]);
	$this->_csrf_set_hash();
	$this->csrf_set_cookie();
	log_message('debug', "CSRF token verified ");
    }

    public function csrf_set_cookie()
    {
	$expire = time() + $this->csrf_expire;
	setcookie($this->csrf_cookie_name, $this->csrf_hash, $expire, config_item('cookie_path'), config_item('cookie_domain'), 0);
	log_message('debug', "CRSF cookie Set");
    }

    private function _csrf_set_hash()
    {
	if ($this->csrf_hash == '')
	{
	    if (isset($_COOKIE[$this->csrf_cookie_name]) AND $_COOKIE[$this->csrf_cookie_name] != '')
	    {
		$this->csrf_hash = $_COOKIE[$this->csrf_cookie_name];
	    }
	    else
	    {
		$this->csrf_hash = md5(uniqid(rand(), TRUE));
	    }
	}

	return $this->csrf_hash;
    }

    public function csrf_show_error()
    {
	show_error('The action you have requested is not allowed.');
    }

    public function xss_clean($str, $is_image = FALSE)
    {
	if (is_array($str))
	{
	    while (list($key) = each($str))
	    {
		$str[$key] = $this->xss_clean($str[$key]);
	    }

	    return $str;
	}

	$str = remove_invisible_characters($str);

	/*
	 * Protect GET variables in URLs
	 */

	// 901119URL5918AMP18930PROTECT8198

	$str = preg_replace('|\&([a-z\_0-9\-]+)\=([a-z\_0-9\-]+)|i', $this->xss_hash() . "\\1=\\2", $str);
	$str = preg_replace('#(&\#?[0-9a-z]{2,})([\x00-\x20])*;?#i', "\\1;\\2", $str);
	$str = preg_replace('#(&\#x?)([0-9A-F]+);?#i', "\\1\\2;", $str);
	$str = str_replace($this->xss_hash(), '&', $str);
	$str = rawurldecode($str);
	$str = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", array($this, '_convert_attribute'), $str);
	$str = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", array($this, '_decode_entity'), $str);
	$str = remove_invisible_characters($str);

	if (strpos($str, "\t") !== FALSE)
	{
	    $str = str_replace("\t", ' ', $str);
	}

	$converted_string = $str;

	foreach ($this->never_allowed_str as $key => $val)
	{
	    $str = str_replace($key, $val, $str);
	}

	foreach ($this->never_allowed_regex as $key => $val)
	{
	    $str = preg_replace("#" . $key . "#i", $val, $str);
	}

	if ($is_image === TRUE)
	{
	    $str = preg_replace('/<\?(php)/i', "&lt;?\\1", $str);
	}
	else
	{
	    $str = str_replace(array('<?', '?' . '>'), array('&lt;?', '?&gt;'), $str);
	}

	$words = array('javascript', 'expression', 'vbscript', 'script', 'applet', 'alert', 'document', 'write', 'cookie', 'window');

	foreach ($words as $word)
	{
	    $temp = '';

	    for ($i = 0, $wordlen = strlen($word); $i < $wordlen; $i++)
	    {
		$temp .= substr($word, $i, 1) . "\s*";
	    }

	    $str = preg_replace_callback('#(' . substr($temp, 0, -3) . ')(\W)#is', array($this, '_compact_exploded_words'), $str);
	}

	do
	{
	    $original = $str;

	    if (preg_match("/<a/i", $str))
	    {
		$str = preg_replace_callback("#<a\s+([^>]*?)(>|$)#si", array($this, '_js_link_removal'), $str);
	    }

	    if (preg_match("/<img/i", $str))
	    {
		$str = preg_replace_callback("#<img\s+([^>]*?)(\s?/?>|$)#si", array($this, '_js_img_removal'), $str);
	    }

	    if (preg_match("/script/i", $str) OR preg_match("/xss/i", $str))
	    {
		$str = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[removed]', $str);
	    }
	}
	while ($original != $str);

	unset($original);
	$event_handlers = array('[^a-z_\-]on\w*', 'xmlns');

	if ($is_image === TRUE)
	{
	    unset($event_handlers[array_search('xmlns', $event_handlers)]);
	}

	$str = preg_replace("#<([^><]+?)(" . implode('|', $event_handlers) . ")(\s*=\s*[^><]*)([><]*)#i", "<\\1\\4", $str);

	$naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';
	$str = preg_replace_callback('#<(/*\s*)(' . $naughty . ')([^><]*)([><]*)#is', array($this, '_sanitize_naughty_html'), $str);
	$str = preg_replace('#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si', "\\1\\2&#40;\\3&#41;", $str);

	foreach ($this->never_allowed_str as $key => $val)
	{
	    $str = str_replace($key, $val, $str);
	}

	foreach ($this->never_allowed_regex as $key => $val)
	{
	    $str = preg_replace("#" . $key . "#i", $val, $str);
	}

	if ($is_image === TRUE)
	{
	    if ($str == $converted_string)
	    {
		return TRUE;
	    }
	    else
	    {
		return FALSE;
	    }
	}

	log_message('debug', "XSS Filtering completed");
	return $str;
    }

    public function xss_hash()
    {
	if ($this->xss_hash == '')
	{
	    if (phpversion() >= 4.2)
		mt_srand();
	    else
		mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);

	    $this->xss_hash = md5(time() + mt_rand(0, 1999999999));
	}

	return $this->xss_hash;
    }

    private function _compact_exploded_words($matches)
    {
	return preg_replace('/\s+/s', '', $matches[1]) . $matches[2];
    }

    private function _sanitize_naughty_html($matches)
    {
	$str = '&lt;' . $matches[1] . $matches[2] . $matches[3];
	$str .= str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);
	return $str;
    }

    private function _js_link_removal($match)
    {
	$attributes = $this->_filter_attributes(str_replace(array('<', '>'), '', $match[1]));
	return str_replace($match[1], preg_replace("#href=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
    }

    private function _js_img_removal($match)
    {
	$attributes = $this->_filter_attributes(str_replace(array('<', '>'), '', $match[1]));
	return str_replace($match[1], preg_replace("#src=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
    }

    private function _convert_attribute($match)
    {
	return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
    }

    private function _filter_attributes($str)
    {
	$out = '';

	if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches))
	{
	    foreach ($matches[0] as $match)
	    {
		$out .= preg_replace("#/\*.*?\*/#s", '', $match);
	    }
	}

	return $out;
    }

    private function _decode_entity($match)
    {
	return $this->entity_decode($match[0], strtoupper(config_item('charset')));
    }

    public function entity_decode($str, $charset='UTF-8')
    {
	if (stristr($str, '&') === FALSE)
	    return $str;

	if (function_exists('html_entity_decode') && (strtolower($charset) != 'utf-8' OR is_php('5.0.0')))
	{
	    $str = html_entity_decode($str, ENT_COMPAT, $charset);
	    $str = preg_replace('~&#x(0*[0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $str);
	    return preg_replace('~&#([0-9]{2,4})~e', 'chr(\\1)', $str);
	}

	$str = preg_replace('~&#x(0*[0-9a-f]{2,5});{0,1}~ei', 'chr(hexdec("\\1"))', $str);
	$str = preg_replace('~&#([0-9]{2,4});{0,1}~e', 'chr(\\1)', $str);

	if (stristr($str, '&') === FALSE)
	{
	    $str = strtr($str, array_flip(get_html_translation_table(HTML_ENTITIES)));
	}

	return $str;
    }

    public function sanitize_filename($str, $relative_path = FALSE)
    {
	$bad = array(
	    "../",
	    "<!--",
	    "-->",
	    "<",
	    ">",
	    "'",
	    '"',
	    '&',
	    '$',
	    '#',
	    '{',
	    '}',
	    '[',
	    ']',
	    '=',
	    ';',
	    '?',
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

	if (!$relative_path)
	{
	    $bad[] = './';
	    $bad[] = '/';
	}

	return stripslashes(str_replace($bad, '', $str));
    }

}