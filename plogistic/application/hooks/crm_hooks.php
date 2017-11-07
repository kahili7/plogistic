<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

function hook_profile_on()
{
    $KI = & get_instance();
    return $KI->config->item('crm_profile_on') ? $KI->output->enable_profiler(TRUE) : $KI->output->enable_profiler(FALSE);
}

function hook_is_logged()
{
    $KI = & get_instance();
    return $KI->user->is_logged();
}

function hook_init_user()
{
    $KI = & get_instance();
    return $KI->user->init_user();
}

function hook_init_menu()
{
    $KI = & get_instance();
    return $KI->menu->init_menu();
}

function hook_check_security()
{
    $KI = & get_instance();

    if ($KI->menu->get_currcontroller() == '' || $KI->menu->get_currcontroller() == 'login' || $KI->menu->get_currcontroller() == 'logout' || $KI->menu->get_currcontroller() == 'welcome')
	return;

    $permissions = $KI->menu->get_rights();

    if (!element('viewed_space', $permissions, null))
	return show_error(lang('MENU_SECURITY_ERROR'));
    if (!element('add_allow', $permissions, null) && element('create', $KI->a_uri_assoc, null))
	return show_error(lang('MENU_SECURITY_ERROR'));
    if (!element('edit_allow', $permissions, null) && element('edit', $KI->a_uri_assoc, null))
	return show_error(lang('MENU_SECURITY_ERROR'));
    if (!element('details_allow', $permissions, null) && element('details', $KI->a_uri_assoc, null))
	return show_error(lang('MENU_SECURITY_ERROR'));
    if (!element('delete_allow', $permissions, null) && element('delete', $KI->a_uri_assoc, null))
	return show_error(lang('MENU_SECURITY_ERROR'));
    return;
}
