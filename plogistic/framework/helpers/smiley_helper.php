<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('js_insert_smiley'))
{
	function js_insert_smiley($form_name='', $form_field='')
	{
		return <<<EOF
<script type="text/javascript">
	function insert_smiley(smiley)
	{
		document.{$form_name}.{$form_field}.value += " " + smiley;
	}
</script>
EOF;
}
}

if(!function_exists('get_clickable_smileys'))
{
	function get_clickable_smileys($image_url='', $smileys=NULL)
	{
		if(!is_array($smileys))
		{
			if(FALSE === ($smileys = _get_smiley_array()))
			{
				return $smileys;
			}
		}

		$image_url = preg_replace("/(.+?)\/*$/", "\\1/",  $image_url);
		$used = array();

		foreach($smileys as $key => $val)
		{
			if(isset($used[$smileys[$key][0]])) continue;

			$link[] = "<a href=\"javascript:void(0);\" onClick=\"insert_smiley('".$key."')\"><img src=\"".$image_url.$smileys[$key][0]."\" width=\"".$smileys[$key][1]."\" height=\"".$smileys[$key][2]."\" alt=\"".$smileys[$key][3]."\" style=\"border:0;\" /></a>";
			$used[$smileys[$key][0]] = TRUE;
		}

		return $link;
	}
}

if(!function_exists('parse_smileys'))
{
	function parse_smileys($str='', $image_url='', $smileys=NULL)
	{
		if($image_url == '') return $str;

		if(!is_array($smileys))
		{
			if(FALSE === ($smileys = _get_smiley_array())) return $str;
		}

		$image_url = preg_replace("/(.+?)\/*$/", "\\1/",  $image_url);

		foreach($smileys as $key => $val)
		{
			$str = str_replace($key, "<img src=\"".$image_url.$smileys[$key][0]."\" width=\"".$smileys[$key][1]."\" height=\"".$smileys[$key][2]."\" alt=\"".$smileys[$key][3]."\" style=\"border:0;\" />", $str);
		}

		return $str;
	}
}

if(!function_exists('_get_smiley_array'))
{
	function _get_smiley_array()
	{
		if(!file_exists(APPPATH.'config/smileys'.EXT)) return FALSE;

		include(APPPATH.'config/smileys'.EXT);

		if(!isset($smileys) OR !is_array($smileys)) return FALSE;

		return $smileys;
	}
}