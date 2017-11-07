<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

include_once APPPATH . "libraries/core/crmcontroller.php";

class Client extends Crmcontroller
{

    public function __construct()
    {
	parent::__construct();
	$this->lang->load('client');
	$this->load->model('client_model');
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
	$data['title'] = lang('CLIENT_TITLE');
	$data['orid'] = $this->get_orid();
	$data['sort'] = $this->get_session('sort');
	$data['find'] = $this->find();
	$data['fields']['rid'] = array('label' => lang('ID'), 'colwidth' => '5%', 'sort' => TRUE);
	$data['fields']['id'] = array('label' => lang('ID_CLIENT'), 'colwidth' => '5%', 'sort' => TRUE);
	$data['fields']['name'] = array('label' => lang('NAME'), 'colwidth' => '40%', 'sort' => TRUE);
	$data['fields']['descr'] = array('label' => lang('DESCR'), 'colwidth' => '40%', 'sort' => TRUE);
	$data['tools'] = $this->get_tools();
	$data['ds'] = $this->client_model->get_ds();
	$data['paging'] = $this->get_paging($this->client_model->get_calc_rows());
	return $this->load->view('standart/grid', $data, TRUE);
    }

    private function create()
    {
	$data = array();
	$this->form_validation->set_rules('name', lang('NAME'), 'required|trim|callback_check_unique_name');
	$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');

	$data['title'] = lang('CLIENT_TITLE_CREATE');
	$data['orid'] = $this->get_orid();
	$data['success'] = null;

	if ($this->form_validation->run() === TRUE)
	{
	    if (($rid = $this->rivals_model->create_record()))
	    {
		$this->session->set_flashdata('success', TRUE);
		redirect(get_currcontroller() . "/edit/$rid", 'refresh');
		return;
	    }
	    else
		$data['success'] = FALSE;
	}

	$data['content'] = $this->load->view('client/create', $data, TRUE);
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

	$data['title'] = lang('RIVALS_TITLE_EDIT');
	$data['rid'] = $rid;
	$data['orid'] = $this->get_orid();
	$data['ds'] = $this->rivals_model->get_edit($rid);
	$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;

	if (!$data['ds'])
	    show_404();
	if ($this->form_validation->run() === TRUE)
	{
	    if ($this->rivals_model->update_record())
		$data['success'] = TRUE;
	    else
		$data['success'] = FALSE;

	    $data['ds'] = $this->rivals_model->get_edit($rid);
	}

	$data['content'] = $this->load->view('rivals/edit', $data, TRUE);
	return $this->load->view('layouts/main_layout', $data);
    }

    private function details()
    {
	$rid = (int) $this->uri->segment(3);

	if (!$rid)
	    show_404();

	$data = array();

	$data['title'] = lang('RIVALS_TITLE_DETAILS');
	$data['rid'] = $rid;
	$data['orid'] = $this->get_orid();
	$data['ds'] = $this->rivals_model->get_edit($rid);

	if (!$data['ds'])
	    show_404();

	$data['content'] = $this->load->view('rivals/details', $data, TRUE);
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
		$search_rule['_client.name'] = $this->input->post('name');

	    $this->set_searchrule($search_rule);
	}

	$data['search'] = $this->get_session('searchrule');
	return $this->load->view('client/find', $data, TRUE);
    }

    private function move()
    {
	$rid = (int) $this->uri->segment(3);

	if (!$rid)
	    show_404();

	$data = array();
	$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');
	$data['title'] = lang('RIVALS_TITLE_MOVE');
	$data['rid'] = $rid;
	$data['orid'] = $this->get_orid();
	$data['ds'] = $this->rivals_model->get_edit($rid);
	$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;

	if (!$data['ds'])
	    show_404();
	if ($this->form_validation->run() === TRUE)
	{
	    if ($this->rivals_model->move_record())
		$data['success'] = TRUE;
	    else
		$data['success'] = FALSE;

	    $data['ds'] = $this->rivals_model->get_edit($rid);
	}

	$data['content'] = $this->load->view('rivals/move', $data, TRUE);
	return $this->load->view('layouts/main_layout', $data);
    }

    public function check_unique_name($code)
    {
	$rid = $this->input->post('rid');

	if ($this->rivals_model->check_unique($code, 'name', $rid))
	{
	    $this->form_validation->set_message('check_unique_name', lang('RIVALS_NAME_NOTUNIQUE'));
	    return FALSE;
	}

	return TRUE;
    }

    public function vjournal()
    {
	$data = array();
	$data['title'] = lang('RIVALS_TITLE');
	$data['orid'] = $this->get_orid();
	$data['sort'] = $this->get_session('sort');
	$data['find'] = $this->vfind();
	$data['fields']['rid'] = array('label' => 'ID', 'colwidth' => '5%', 'sort' => TRUE);
	$data['fields']['rival_name'] = array('label' => lang('NAME'), 'colwidth' => '40%', 'sort' => TRUE);
	$data['fields']['descr'] = array('label' => lang('DESCR'), 'colwidth' => '40%', 'sort' => TRUE);
	$data['fields']['modifyDT'] = array('label' => lang('MODIFYDT'), 'colwidth' => '10%', 'sort' => TRUE);
	$data['fields']['archive'] = array('label' => lang('ARCHIVE'), 'colwidth' => '5%', 'sort' => TRUE, 'type' => 'yes_no');
	$data['jtp'] = $this->jtp;
	$data['tools'] = $this->get_tools();
	$data['ds'] = $this->rivals_model->get_ds();
	$data['paging'] = $this->get_paging($this->rivals_model->get_calc_rows(), TRUE);
	$content = $this->load->view('standart/vgrid', $data, TRUE);
	$this->load->view('layouts/valuepicker_layout', array('content' => $content));
    }

    private function vcreate()
    {
	$data = array();
	$this->form_validation->set_rules('name', lang('NAME'), 'required|trim|callback_check_unique_name');
	$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
	$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');

	$data['title'] = lang('RIVALS_TITLE_CREATE');
	$data['orid'] = $this->get_orid();
	$data['success'] = null;

	if ($this->form_validation->run() === TRUE)
	{
	    if (($rid = $this->rivals_model->create_record()))
	    {
		$this->session->set_flashdata('success', TRUE);
		redirect(get_currcontroller() . "/vedit/$rid", 'refresh');
		return;
	    }
	    else
		$data['success'] = FALSE;
	}

	$data['content'] = $this->load->view('rivals/vcreate', $data, TRUE);
	return $this->load->view('layouts/valuepicker_layout', $data);
    }

    private function vedit()
    {
	$rid = (int) $this->uri->segment(3);

	if (!$rid)
	    show_404();

	$data = array();
	$this->form_validation->set_rules('name', lang('NAME'), 'required|trim|callback_check_unique_name');
	$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
	$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');

	$data['title'] = lang('RIVALS_TITLE_EDIT');
	$data['rid'] = $rid;
	$data['orid'] = $this->get_orid();
	$data['jtp'] = $this->jtp;
	$data['ds'] = $this->rivals_model->get_edit($rid);
	$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;

	if (!$data['ds'])
	    show_404();
	if ($this->form_validation->run() === TRUE)
	{
	    if ($this->rivals_model->update_record())
		$data['success'] = TRUE;
	    else
		$data['success'] = FALSE;

	    $data['ds'] = $this->rivals_model->get_edit($rid);
	}

	$data['content'] = $this->load->view('rivals/vedit', $data, TRUE);
	return $this->load->view('layouts/valuepicker_layout', $data);
    }

    private function vdetails()
    {
	$rid = (int) $this->uri->segment(3);

	if (!$rid)
	    show_404();

	$data = array();

	$data['title'] = lang('RIVALS_TITLE_DETAILS');
	$data['rid'] = $rid;
	$data['orid'] = $this->get_orid();
	$data['jtp'] = $this->jtp;
	$data['ds'] = $this->rivals_model->get_edit($rid);

	if (!$data['ds'])
	    show_404();

	$data['content'] = $this->load->view('rivals/vdetails', $data, TRUE);
	return $this->load->view('layouts/valuepicker_layout', $data);
    }

    private function vfind()
    {
	$data['orid'] = $this->get_orid();
	$this->form_validation->set_rules('name', lang('NAME'), 'trim');

	if ($this->form_validation->run() == TRUE)
	{
	    $search_rule = array();

	    if ($this->input->post('name'))
		$search_rule['_rivals.name'] = $this->input->post('name');

	    $this->set_searchrule($search_rule);
	}

	$search = $this->get_session('searchrule');
	$data['search'] = array_merge(element('like', $search, array()), element('where', $search, array()), element('having', $search, array()));
	return $this->load->view('rivals/vfind', $data, TRUE);
    }

    private function vmove()
    {
	$rid = (int) $this->uri->segment(3);

	if (!$rid)
	    show_404();

	$data = array();
	$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');
	$data['title'] = lang('RIVALS_TITLE_MOVE');
	$data['rid'] = $rid;
	$data['orid'] = $this->get_orid();
	$data['ds'] = $this->rivals_model->get_edit($rid);
	$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;

	if (!$data['ds'])
	    show_404();
	if ($this->form_validation->run() === TRUE)
	{
	    if ($this->rivals_model->move_record())
		$data['success'] = TRUE;
	    else
		$data['success'] = FALSE;

	    $data['ds'] = $this->rivals_model->get_edit($rid);
	}

	$data['content'] = $this->load->view('rivals/vmove', $data, TRUE);
	return $this->load->view('layouts/valuepicker_layout', $data);
    }

}

?>