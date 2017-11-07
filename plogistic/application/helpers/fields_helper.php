<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_valtype($val, $type, $mode='grid')
{
	switch($type)
	{
		case "yes_no":
			return !$val ? img('public/img/icons/decline_inline.gif') : img('public/img/icons/accept_inline.gif');
			break;

		case "email":
			return $val ? safe_mailto($val) : '';
			break;

		case "url":
			return $val ? anchor(prep_url($val), lang('GO_LINK'), 'target="_blank"') : '';
			break;
			
		case "digit":
			return $val ? number_format($val, '3', '.', ' ') : '';
			break;
	
		default:
			return $val;
	}
}

function date_conv($date, $time=FALSE)
{
	if(!$date) return '';
	if($time) return date('H:i', strtotime($date));

	return date('d.m.Y', strtotime($date));
}

function filtr($text, $prob=1)
{
	if($prob == 1)
	{
		$pattern = array(
		//выражение для замены спец символов ; # &
		'/(;)/ie', '/(#)/ie', '/(&)/ie',
		//защита mysql от иньекции
		'/(ACTION)/ie', '/(ADD)/ie', '/(ALL)/ie', '/(ALTER)/ie', '/(ANALYZE)/ie', '/(AND)/ie', '/(AS)/ie', '/(ASC)/ie',
		//замена спец символов < > . , ? ` ! @ $ % ^ * ( ) _ - + = / \ ' " :
		'/(<)/ie', '/(>)/ie', '/(\.)/ie', '/(,)/ie', '/(\?)/ie', '/(`)/ie', '/(!)/ie', '/(@)/ie',
		'/(\$)/ie', '/(%)/ie', '/(\^)/ie', '/(\*)/ie', '/(\()/ie', '/(\))/ie', '/(_)/ie', '/(-)/ie',
		'/(\+)/ie', '/(=)/ie', '/(\/)/ie', '/(\|)/ie', '/(\\\)/ie', "/(')/ie", '/(")/ie', '/(:)/'
		);
	}
	else if($prob == 0)
	{
		$pattern = array(
		//выражение для замены спец символов ; # &
		'/(;)/ie', '/(#)/ie', '/(&)/ie',
		//защита mysql от иньекции
		'/(ACTION)/ie', '/(ADD)/ie', '/(ALL)/ie', '/(ALTER)/ie', '/(ANALYZE)/ie', '/(AND)/ie', '/(AS)/ie', '/(ASC)/ie',
		//замена спец символов < > . , ? ` ! @ $ % ^ * ( ) _ - + = / \ ' " :
		'/(<)/ie', '/(>)/ie', '/(\.)/ie', '/(,)/ie', '/(\?)/ie', '/(`)/ie', '/(!)/ie', '/(@)/ie',
		'/(\$)/ie', '/(%)/ie', '/(\^)/ie', '/(\*)/ie', '/(\()/ie', '/(\))/ie', '/(_)/ie', '/(-)/ie',
		'/(\+)/ie', '/(=)/ie', '/(\/)/ie', '/(\|)/ie', '/(\\\)/ie', "/(')/ie", '/(")/ie', '/(:)/',
		//Пробел
		'/ /ie',
		);
	}

	return preg_replace($pattern, "", $text);
};