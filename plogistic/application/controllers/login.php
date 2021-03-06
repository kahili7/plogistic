<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

class LOGIN extends KI_CONTROLLER
{

    public function LOGIN()
    {
	parent::KI_CONTROLLER();
	$this->lang->load('login');
	$this->load->model('user_model');
    }

    public function index()
    {
	$data = array();
	$data['page_title'] = lang('M1_LOGIN_AREA_TITLE');
	$this->form_validation->set_rules('i_login', lang('M1_LOGIN_LABEL'), 'required|callback_check_user|callback_check_edate');
	$this->form_validation->set_rules('i_password', lang('M1_PASSWORD_LABEL'), 'trim');

	if ($this->form_validation->run() === FALSE)
	{
	    $data['content'] = $this->load->view('login/login_form.php', null, TRUE);
	    $this->load->view('layouts/login_layout', $data);
	    return;
	}

	$this->login_processing($this->input->post('i_login'));
	return TRUE;
    }

    private function login_processing($login)
    {
	$today = date('Y-m-d');
	$urid = $this->user_model->get_urid_by_login($login);
	$chdate = $this->user_model->get_chdate_by_urid($urid);
	$this->session->set_userdata('URID', $urid);

	if ($chdate <= $today)
	{
	    redirect('login/chpass', 'refresh');
	}
	else
	    redirect('welcome', 'refresh');
    }

    public function logout()
    {
	$this->session->unset_userdata('URID');
	redirect('login', 'refresh');
    }

    public function check_user($login)
    {
	$row = $this->user_model->check_user($login, $this->input->post('i_password'));

	if ($row)
	    return TRUE;

	$this->form_validation->set_message('check_user', lang('M1_AUTH_ERROR_TITLE'));
	return FALSE;
    }

    public function check_edate($login)
    {
	$row = $this->user_model->check_edate($login);

	if ($row)
	    return TRUE;

	$this->form_validation->set_message('check_edate', lang('M1_END_PASSWORD_TIME'));
	return False;
    }

    public function chpass()
    {
	$data = array();
	$data['page_title'] = lang('LOGIN_CHPASS_TITLE');
	$this->form_validation->set_rules('i_password', lang('M1_PASSWORD_LABEL'), 'required|matches[i_cpassword]|trim|min_length[6]');
	$this->form_validation->set_rules('i_cpassword', lang('M1_PASSWORD_CONFIRM_LABEL'), 'required|');

	if ($this->form_validation->run() === FALSE)
	{
	    $data['content'] = $this->load->view('login/chpass_form.php', null, TRUE);
	}
	else
	{
	    if ($this->chpass_processing($this->input->post('i_password')))
	    {
		$data['content'] = $this->load->view('login/chpass_success.php', null, TRUE);
	    }
	    else
		$data['content'] = $this->load->view('login/chpass_error.php', null, TRUE);
	}

	$this->load->view('layouts/login_layout', $data);
	return TRUE;
    }

    private function chpass_processing($passwd)
    {
	return $this->user_model->cp(get_curr_urid(), $passwd, $this->config->item('crm_chpass_period'));
    }

}

?>