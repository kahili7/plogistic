<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

include_once APPPATH . "libraries/core/crmcontroller.php";

class Groupsystem extends Crmcontroller
{

    public function __construct()
    {
	parent::__construct();
	$this->lang->load('groupsystem');
	$this->load->model('groupsystem_model');
    }

    public function _remap($m_Name)
    {
	switch ($m_Name)
	{
	    case 'create': $this->create();
		break;
	    case 'edit': $this->edit();
		break;
	    case 'details': $this->details();
		break;
	    case 'remove': $this->remove();
		break;
	    case 'sort': $this->sort();
		break;
	    default: $this->index();
	}
    }

    public function journal()
    {
	$data = array();
	$data['title'] = lang('GROUPSYSTEM_TITLE');
	$data['orid'] = $this->get_orid();
	$data['sort'] = $this->get_session('sort');
	$data['find'] = $this->find();
	$data['fields']['rid'] = array('label' => lang('ID'), 'colwidth' => '5%', 'sort' => TRUE);
	$data['fields']['id_group'] = array('label' => lang('ID_GROUP'), 'colwidth' => '5%', 'sort' => TRUE);
	$data['fields']['id_system'] = array('label' => lang('ID_SYSTEM'), 'colwidth' => '5%', 'sort' => TRUE);
	$data['fields']['name'] = array('label' => lang('NAME'), 'colwidth' => '40%', 'sort' => TRUE);
	$data['fields']['descr'] = array('label' => lang('DESCR'), 'colwidth' => '40%', 'sort' => TRUE);
	$data['tools'] = $this->get_tools();
	$data['ds'] = $this->groupsystem_model->get_ds();
	$data['paging'] = $this->get_paging($this->groupsystem_model->get_calc_rows());
	return $this->load->view('standart/grid', $data, TRUE);
    }

    private function create()
    {
	$data = array();
	$this->form_validation->set_rules('name', lang('NAME'), 'required|trim|callback_check_unique_name');
	$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');

	$data['title'] = lang('GROUPSYSTEM_TITLE_CREATE');
	$data['orid'] = $this->get_orid();
	$data['success'] = null;

	if ($this->form_validation->run() === TRUE)
	{
	    if (($rid = $this->groupsystem_model->create_record()))
	    {
		$this->session->set_flashdata('success', TRUE);
		redirect(get_currcontroller() . "/edit/$rid", 'refresh');
		return;
	    }
	    else
		$data['success'] = FALSE;
	}

	$data['content'] = $this->load->view('groupsystem/create', $data, TRUE);
	return $this->load->view('layouts/main_layout', $data);
    }

    private function edit()
    {
	$rid = (int) $this->uri->segment(3);

	if (!$rid)
	    show_404();

	$data = array();
	$this->form_validation->set_rules('name', lang('NAME'), 'required|trim|callback_check_unique_name');
	$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
	$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');

	$data['title'] = lang('PSTATUS_TITLE_EDIT');
	$data['rid'] = $rid;
	$data['orid'] = $this->get_orid();
	$data['ds'] = $this->pstatus_model->get_edit($rid);
	$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;

	if (!$data['ds'])
	    show_404();
	if ($this->form_validation->run() === TRUE)
	{
	    if ($this->pstatus_model->update_record())
		$data['success'] = TRUE;
	    else
		$data['success'] = FALSE;

	    $data['ds'] = $this->pstatus_model->get_edit($rid);
	}

	$data['content'] = $this->load->view('pstatus/edit', $data, TRUE);
	return $this->load->view('layouts/main_layout', $data);
    }

    private function details()
    {
	$rid = (int) $this->uri->segment(3);

	if (!$rid)
	    show_404();

	$data = array();

	$data['title'] = lang('PSTATUS_TITLE_DETAILS');
	$data['rid'] = $rid;
	$data['orid'] = $this->get_orid();
	$data['ds'] = $this->pstatus_model->get_edit($rid);

	if (!$data['ds'])
	    show_404();

	$data['content'] = $this->load->view('pstatus/details', $data, TRUE);
	return $this->load->view('layouts/main_layout', $data);
    }

    private function find()
    {
	$data['orid'] = $this->get_orid();
	$this->form_validation->set_rules('name', lang('NAME'), 'trim');

	if ($this->form_validation->run() == TRUE)
	{
	    $search_rule = array();

	    if ($this->input->post('name'))
		$search_rule['_groupsystem.name'] = $this->input->post('name');

	    $this->set_searchrule($search_rule);
	}

	$data['search'] = $this->get_session('searchrule');
	return $this->load->view('groupsystem/find', $data, TRUE);
    }

    private function move()
    {
	$rid = (int) $this->uri->segment(3);

	if (!$rid)
	    show_404();

	$data = array();
	$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');
	$data['title'] = lang('PSTATUS_TITLE_MOVE');
	$data['rid'] = $rid;
	$data['orid'] = $this->get_orid();
	$data['ds'] = $this->pstatus_model->get_edit($rid);
	$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;

	if (!$data['ds'])
	    show_404();
	if ($this->form_validation->run() === TRUE)
	{
	    if ($this->pstatus_model->move_record())
		$data['success'] = TRUE;
	    else
		$data['success'] = FALSE;

	    $data['ds'] = $this->pstatus_model->get_edit($rid);
	}

	$data['content'] = $this->load->view('pstatus/move', $data, TRUE);
	return $this->load->view('layouts/main_layout', $data);
    }

    public function check_unique_name($code)
    {
	$rid = $this->input->post('rid');

	if ($this->pstatus_model->check_unique($code, 'name', $rid))
	{
	    $this->form_validation->set_message('check_unique_name', lang('PSTATUS_NAME_NOTUNIQUE'));
	    return FALSE;
	}

	return TRUE;
    }

}

?>