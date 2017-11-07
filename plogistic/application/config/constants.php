<?
if(!DEFINEd('BASEPATH')) exit('No direct script access allowed');

DEFINE('FILE_READ_MODE', 0644);
DEFINE('FILE_WRITE_MODE', 0666);
DEFINE('DIR_READ_MODE', 0755);
DEFINE('DIR_WRITE_MODE', 0777);

DEFINE('FOPEN_READ', 							'rb');
DEFINE('FOPEN_READ_WRITE',						'r+b');
DEFINE('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
DEFINE('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
DEFINE('FOPEN_WRITE_CREATE', 					'ab');
DEFINE('FOPEN_READ_WRITE_CREATE', 				'a+b');
DEFINE('FOPEN_WRITE_CREATE_STRICT', 			'xb');
DEFINE('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');