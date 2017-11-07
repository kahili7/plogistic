<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

global $hook;

$hook['post_controller_constructor'][] = array(
    'class' => '',
    'function' => 'hook_profile_on',
    'filename' => 'crm_hooks.php',
    'filepath' => 'hooks',
    'params' => array()
);

$hook['post_controller_constructor'][] = array(
    'class' => '',
    'function' => 'hook_is_logged',
    'filename' => 'crm_hooks.php',
    'filepath' => 'hooks',
    'params' => array()
);

$hook['post_controller_constructor'][] = array(
    'class' => '',
    'function' => 'hook_init_user',
    'filename' => 'crm_hooks.php',
    'filepath' => 'hooks',
    'params' => array()
);

$hook['post_controller_constructor'][] = array(
    'class' => '',
    'function' => 'hook_init_menu',
    'filename' => 'crm_hooks.php',
    'filepath' => 'hooks',
    'params' => array()
);

$hook['post_controller_constructor'][] = array(
    'class' => '',
    'function' => 'hook_check_security',
    'filename' => 'crm_hooks.php',
    'filepath' => 'hooks',
    'params' => array()
);
