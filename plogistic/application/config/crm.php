<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

$config['crm_version'] = 'v1.0.1';
$config['crm_profile_on'] = FALSE;
$config['crm_chpass_period'] = 90; /* период смены пароля в днях */
$config['crm_grid_limit'] = 25;
$config['crm_dropdown_empty'] = '--None--'; /* пустышка в выпадающем списке */
$config['crm_upload_max_size'] = 2048; /* максимальный размер файла в прикрепленных файлах */
$config['crm_allowed_types'] = 'gif|jpg|png';
$config['crm_upload_path'] = './public/attaches/';
$config['crm_chat_limit'] = 100;
?>