<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('heading'))
{
	function heading($data='', $h='1')
	{
		return "<h".$h.">".$data."</h".$h.">";
	}
}

if(!function_exists('ul'))
{
	function ul($list, $attributes='')
	{
		return _list('ul', $list, $attributes);
	}
}

if(!function_exists('ol'))
{
	function ol($list, $attributes='')
	{
		return _list('ol', $list, $attributes);
	}
}

if(!function_exists('_list'))
{
	function _list($type='ul', $list, $attributes='', $depth=0)
	{
		if(!is_array($list)) return $list;

		$out = str_repeat(" ", $depth);

		if(is_array($attributes))
		{
			$atts = '';
			
			foreach($attributes as $key => $val)
			{
				$atts .= ' '.$key.'="'.$val.'"';
			}
			
			$attributes = $atts;
		}

		$out .= "<".$type.$attributes.">\n";

		static $_last_list_item = '';
		
		foreach($list as $key => $val)
		{
			$_last_list_item = $key;

			$out .= str_repeat(" ", $depth + 2);
			$out .= "<li>";

			if(!is_array($val))
			{
				$out .= $val;
			}
			else
			{
				$out .= $_last_list_item."\n";
				$out .= _list($type, $val, '', $depth + 4);
				$out .= str_repeat(" ", $depth + 2);
			}

			$out .= "</li>\n";
		}

		$out .= str_repeat(" ", $depth);
		$out .= "</".$type.">\n";
		return $out;
	}
}

if(!function_exists('br'))
{
	function br($num=1)
	{
		return str_repeat("<br />", $num);
	}
}

if(!function_exists('img'))
{
	function img($src='', $index_page=FALSE)
	{
		if(!is_array($src)) $src = array('src' => $src);

		$img = '<img';

		foreach($src as $k => $v)
		{
			if($k == 'src' AND strpos($v, '://') === FALSE)
			{
				$KI =& get_instance();

				if ($index_page === TRUE)
				{
					$img .= ' src="'.$KI->config->site_url($v).'" ';
				}
				else
				{
					$img .= ' src="'.$KI->config->slash_item('base_url').$v.'" ';
				}
			}
			else
			{
				$img .= " $k=\"$v\" ";
			}
		}

		$img .= '/>';
		return $img;
	}
}

if(!function_exists('doctype'))
{
	function doctype($type='xhtml-strict')
	{
		global $_doctypes;

		if(!is_array($_doctypes))
		{
			if(!require_once(APPPATH.'config/doctypes.php'))
			{
				return FALSE;
			}
		}

		if(isset($_doctypes[$type]))
		{
			return $_doctypes[$type];
		}
		else
		{
			return FALSE;
		}
	}
}

if(!function_exists('link_tag'))
{
	function link_tag($href='', $rel='stylesheet', $type='text/css', $title='', $media='', $index_page=FALSE)
	{
		$KI =& get_instance();
		$link = '<link ';

		if(is_array($href))
		{
			foreach($href as $k => $v)
			{
				if($k == 'href' AND strpos($v, '://') === FALSE)
				{
					if($index_page === TRUE)
					{
						$link .= ' href="'.$KI->config->site_url($v).'" ';
					}
					else
					{
						$link .= ' href="'.$KI->config->slash_item('base_url').$v.'" ';
					}
				}
				else
				{
					$link .= "$k=\"$v\" ";
				}
			}

			$link .= "/>";
		}
		else
		{
			if(strpos($href, '://') !== FALSE)
			{
				$link .= ' href="'.$href.'" ';
			}
			else if($index_page === TRUE)
			{
				$link .= ' href="'.$KI->config->site_url($href).'" ';
			}
			else
			{
				$link .= ' href="'.$KI->config->slash_item('base_url').$href.'" ';
			}

			$link .= 'rel="'.$rel.'" type="'.$type.'" ';

			if($media != '')
			{
				$link .= 'media="'.$media.'" ';
			}

			if($title != '')
			{
				$link .= 'title="'.$title.'" ';
			}

			$link .= '/>';
		}


		return $link;
	}
}

if(!function_exists('meta'))
{
	function meta($name='', $content='', $type='name', $newline="\n")
	{
		if(!is_array($name))
		{
			$name = array(array('name' => $name, 'content' => $content, 'type' => $type, 'newline' => $newline));
		}
		else
		{
			if(isset($name['name']))
			{
				$name = array($name);
			}
		}

		$str = '';
		
		foreach($name as $meta)
		{
			$type = (!isset($meta['type']) OR $meta['type'] == 'name') ? 'name' : 'http-equiv';
			$name = (!isset($meta['name'])) ? '' : $meta['name'];
			$content = (!isset($meta['content'])) ? '' : $meta['content'];
			$newline = (!isset($meta['newline'])) ? "\n" : $meta['newline'];
			$str .= '<meta '.$type.'="'.$name.'" content="'.$content.'" />'.$newline;
		}

		return $str;
	}
}

if(!function_exists('nbs'))
{
	function nbs($num=1)
	{
		return str_repeat("&nbsp;", $num);
	}
}