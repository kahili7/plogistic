<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH."libraries/core/crmcontroller.php";

class Contacts extends Crmcontroller 
{
	private $jtp = array('val' => 'rid', 'scr' => 'contact_name', 'val_p' => '_contacts_rid', 'scr_p'=>'contact_name');
	
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('contacts');
		$this->load->model('contacts_model');
		
		if(element('val_p', $this->a_uri_assoc, null)) $this->jtp['val_p'] = element('val_p', $this->a_uri_assoc, null);
		if(element('scr_p', $this->a_uri_assoc, null)) $this->jtp['scr_p'] = element('scr_p', $this->a_uri_assoc, null);
	}
	
	public function _remap($m_Name)
	{
		switch($m_Name) 
		{
			case 'create': $this->create();break;
			case 'edit': $this->edit();break;
			case 'details': $this->details();break;
			case 'remove': $this->remove();break;
			case 'move': $this->move();break;
			case 'sort': $this->sort();break;
			
			case 'vcreate': $this->vcreate();break;
			case 'vedit': $this->vedit();break;
			case 'vdetails': $this->vdetails();break;
			case 'vremove': $this->vremove();break;
			case 'vmove': $this->vmove();break;
			case 'vsort': $this->vsort();break;
			case 'vjournal': 
			case 'vfind': $this->vjournal(); break;

			default: $this->index();
		}
	}
	
	public function journal()
	{
		$data = array();
		$data['title'] = lang('CONTACTS_TITLE');
		$data['orid'] = $this->get_orid();
		$data['sort'] = $this->get_session('sort');
		$data['find'] = $this->find();
		$data['fields']['rid'] = array('label' => 'ID', 'colwidth' => '5%', 'sort' => TRUE); 
		$data['fields']['contact_name'] = array('label' => lang('NAME'), 'colwidth' => '20%', 'sort' => TRUE);
		$data['fields']['contface'] = array('label' => lang('CONTFACE'), 'colwidth' => '25%', 'sort' => TRUE);
		$data['fields']['job'] = array('label' => lang('JOB'), 'colwidth' => '20%', 'sort' => TRUE);
		$data['fields']['phone'] =  array('label' => lang('PHONE'), 'colwidth' => '20%', 'sort' => TRUE); 
		$data['fields']['emp_name'] = array('label' => lang('OWNER'), 'colwidth' => '10%', 'sort' => TRUE);
		$data['tools'] = $this->get_tools(); 
		$data['ds'] = $this->contacts_model->get_ds();
		$data['paging'] = $this->get_paging($this->contacts_model->get_calc_rows());
		return $this->load->view('standart/grid', $data, TRUE);		
	}
	
	private function create()
	{
		$data = array();

		$this->form_validation->set_rules('name', lang('NAME'), 'required|trim|callback_check_unique_name');
		$this->form_validation->set_rules('contface', lang('CONTFACE'), 'required|trim');
		$this->form_validation->set_rules('job', lang('JOB'), 'trim');
		$this->form_validation->set_rules('phone', lang('PHONE'), 'trim');
		$this->form_validation->set_rules('email', lang('EMAIL'), 'trim');
		$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
		$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');

		$data['title'] = lang('CONTACTS_TITLE_CREATE');
		$data['orid'] = $this->get_orid();
		$data['success'] = null;
		
		if($this->form_validation->run() === TRUE)
		{
			if(($rid = $this->contacts_model->create_record()))
			{
				$this->session->set_flashdata('success', TRUE);
				redirect(get_currcontroller()."/edit/$rid", 'refresh');
				return;
			}
			else $data['success'] = FALSE;
		}
		
		$data['content'] = $this->load->view('contacts/create', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}
	
	private function edit()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();

		$this->form_validation->set_rules('name', lang('NAME'), 'required|trim');
		$this->form_validation->set_rules('contface', lang('CONTFACE'), 'required|trim');
		$this->form_validation->set_rules('job', lang('JOB'), 'trim');
		$this->form_validation->set_rules('phone', lang('PHONE'), 'trim');
		$this->form_validation->set_rules('email', lang('EMAIL'), 'trim');
		$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
		$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');
		
		$data['title'] = lang('CONTACTS_TITLE_EDIT');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->contacts_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;
		
		if(!$data['ds']) show_404(); 
		if($this->form_validation->run() === TRUE)
		{
			if($this->contacts_model->update_record()) $data['success'] = TRUE;
			else $data['success'] = FALSE;
			
			$data['ds'] = $this->contacts_model->get_edit($rid);
		}
		
		$data['content'] = $this->load->view('contacts/edit', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}
	
	private function details()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();

		$data['title'] = lang('CONTACTS_TITLE_DETAILS');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->contacts_model->get_edit($rid);
		
		if(!$data['ds']) show_404(); 
		
		$data['content'] = $this->load->view('contacts/details', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}

	private function find()
	{
		$data['orid'] = $this->get_orid();
		$this->form_validation->set_rules('name', lang('NAME'), 'trim');
		$this->form_validation->set_rules('contface', lang('CONTFACE'), 'trim');
		$this->form_validation->set_rules('phone', lang('PHONE'), 'trim');
		$this->form_validation->set_rules('email', lang('EMAIL'), 'trim');
		
		if($this->form_validation->run() == TRUE)
		{
			$search_rule = array();
			
			if($this->input->post('name')) $search_rule['like']['_contacts.name'] = $this->input->post('name');
			if($this->input->post('contface')) $search_rule['like']['_contacts.contface'] = $this->input->post('contface');
			if($this->input->post('phone')) $search_rule['like']['_contacts.phone'] = $this->input->post('phone');
			if($this->input->post('email')) $search_rule['like']['_contacts.email'] = $this->input->post('email');			
			
			$this->set_searchrule($search_rule);
		}
		
		$search = $this->get_session('searchrule');
		$data['search'] = array_merge(element('like', $search, array()), element('where', $search, array()), element('having', $search, array()));
		
		return $this->load->view('contacts/find', $data, TRUE);
	}

	public function check_unique_name($code)
	{
		$rid = $this->input->post('rid');
		
		if($this->contacts_model->check_unique($code, 'name', $rid))
		{
			$this->form_validation->set_message('check_unique_name', lang('CONTACTS_NAME_NOTUNIQUE'));
			return FALSE;
		}
		
		return TRUE;
	}
	
	private function move()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();
		$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');
		$data['title'] = lang('CONTACTS_TITLE_MOVE');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->contacts_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;
		
		if(!$data['ds']) show_404(); 
		if($this->form_validation->run() === TRUE)
		{
			if($this->contacts_model->move_record()) $data['success'] = TRUE;
			else $data['success'] = FALSE;
			
			$data['ds'] = $this->contacts_model->get_edit($rid);
		}
		
		$data['content'] = $this->load->view('contacts/move', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}
	
	private function vcreate()
	{
		$data = array();
		
		$this->form_validation->set_rules('name', lang('NAME'), 'required|trim|callback_check_unique_code');
		$this->form_validation->set_rules('contface', lang('CONTFACE'), 'required|trim|callback_check_unique_name');
		$this->form_validation->set_rules('job', lang('JOB'), 'trim');
		$this->form_validation->set_rules('phone', lang('PHONE'), 'trim');
		$this->form_validation->set_rules('email', lang('EMAIL'), 'trim');
		$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
		$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');
		
		$data['title'] = lang('CONTACTS_TITLE_CREATE');
		$data['orid'] = $this->get_orid();
		$data['success'] = null;
		
		if($this->form_validation->run() === TRUE)
		{
			if(($rid = $this->contacts_model->create_record()))
			{
				$this->session->set_flashdata('success', TRUE);
				redirect(get_currcontroller()."/vedit/$rid", 'refresh');
				return;
			}
			else $data['success'] = FALSE;
		}
		
		$data['content'] = $this->load->view('contacts/vcreate', $data, TRUE);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}
	
	public function vjournal()
	{
		$data = array();
		$data['title'] = lang('CONTACTS_TITLE');
		$data['orid'] = $this->get_orid();
		$data['sort'] = $this->get_session('sort');
		$data['find'] = $this->vfind();
		$data['fields']['rid'] = array('label' => 'ID', 'colwidth' => '5%', 'sort' => TRUE); 
		$data['fields']['contact_name'] = array('label' => lang('NAME'), 'colwidth' => '20%', 'sort' => TRUE);
		$data['fields']['contface'] = array('label' => lang('CONTFACE'), 'colwidth' => '25%', 'sort' => TRUE);
		$data['fields']['job'] = array('label' => lang('JOB'), 'colwidth' => '20%', 'sort' => TRUE);
		$data['fields']['phone'] =  array('label' => lang('PHONE'), 'colwidth' => '20%', 'sort' => TRUE); 
		$data['fields']['emp_name'] = array('label' => lang('OWNER'), 'colwidth' => '10%', 'sort' => TRUE);
		$data['jtp'] = $this->jtp;
		$data['tools'] = $this->get_tools(); 
		$data['ds'] = $this->contacts_model->vget_ds();
		$data['paging'] = $this->get_paging($this->contacts_model->get_calc_rows(), TRUE);
		$content = $this->load->view('standart/vgrid', $data, TRUE);
		$this->load->view('layouts/valuepicker_layout', array('content' => $content));		
		return;	
	}
	
	private function vedit()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();
		
		$this->form_validation->set_rules('name', lang('NAME'), 'required|trim');
		$this->form_validation->set_rules('contface', lang('CONTFACE'), 'required|trim');
		$this->form_validation->set_rules('job', lang('JOB'), 'trim');
		$this->form_validation->set_rules('phone', lang('PHONE'), 'trim');
		$this->form_validation->set_rules('email', lang('EMAIL'), 'trim');
		$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
		$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');
		
		$data['title'] = lang('CONTACTS_TITLE_EDIT');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['jtp'] = $this->jtp;
		$data['ds'] = $this->contacts_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;
		
		if(!$data['ds']) show_404(); 
		if($this->form_validation->run() === TRUE)
		{
			if($this->contacts_model->update_record()) $data['success'] = TRUE;
			else $data['success'] = FALSE;
			
			$data['ds'] = $this->contacts_model->get_edit($rid);
		}
		
		$data['content'] = $this->load->view('contacts/vedit', $data, TRUE);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}

	private function vdetails()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();

		$data['title'] = lang('CONTACTS_TITLE_DETAILS');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['jtp'] = $this->jtp;
		$data['ds'] = $this->contacts_model->get_edit($rid);
		
		if(!$data['ds']) show_404(); 
		
		$data['content'] = $this->load->view('contacts/vdetails', $data, TRUE);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}

	private function vfind()
	{
		$data['orid'] = $this->get_orid();
		$this->form_validation->set_rules('name', lang('NAME'), 'trim');
		$this->form_validation->set_rules('contface', lang('CONTFACE'), 'trim');
		$this->form_validation->set_rules('phone', lang('PHONE'), 'trim');
		$this->form_validation->set_rules('email', lang('EMAIL'), 'trim');
		
		if($this->form_validation->run() == TRUE)
		{
			$search_rule = array();
			
			if($this->input->post('name')) $search_rule['like']['_contacts.name'] = $this->input->post('name');
			if($this->input->post('contface')) $search_rule['like']['_contacts.contface'] = $this->input->post('contface');
			if($this->input->post('phone')) $search_rule['like']['_contacts.phone'] = $this->input->post('phone');
			if($this->input->post('email')) $search_rule['like']['_contacts.email'] = $this->input->post('email');			
			
			$this->set_searchrule($search_rule);
		}
		
		$search = $this->get_session('searchrule');
		$data['search'] = array_merge(element('like', $search, array()), element('where', $search, array()), element('having', $search, array()));
		return $this->load->view('contacts/vfind', $data, TRUE);
	}

	private function vmove()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();
		$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');
		$data['title'] = lang('CONTACTS_TITLE_MOVE');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->contacts_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;
		
		if(!$data['ds']) show_404(); 
		if($this->form_validation->run() === TRUE)
		{
			if($this->contacts_model->move_record()) $data['success'] = TRUE;
			else $data['success'] = FALSE;
			
			$data['ds'] = $this->contacts_model->get_edit($rid);
		}
		
		$data['content'] = $this->load->view('contacts/vmove', $data, TRUE);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}
}
?>