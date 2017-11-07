<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH."libraries/core/crmcontroller.php";

class Employeers extends Crmcontroller 
{
	private $jtp = array('val'=>'rid', 'scr'=>'full_name', 'val_p'=>'_employeers_rid', 'scr_p'=>'full_name');
	
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('employeers');
		$this->load->model('employeers_model');
		
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
		$data['title'] = lang('EMPLOYEERS_TITLE');
		$data['orid'] = $this->get_orid();
		$data['sort'] = $this->get_session('sort');
		$data['find'] = $this->find();
		$data['fields']['rid'] = array('label' => 'ID', 'colwidth' => '5%', 'sort' => TRUE); 
		$data['fields']['l_name'] = array('label' => lang('L_NAME'), 'colwidth' => '20%', 'sort' => TRUE);
		$data['fields']['f_name'] = array('label' => lang('F_NAME'), 'colwidth' => '20%', 'sort' => TRUE);
		$data['fields']['filial_name'] = array('label' => lang('FILIAL'), 'colwidth' => '10%', 'sort' => TRUE); 
		$data['fields']['birthday'] = array('label' => lang('BIRTHDAY'), 'colwidth' => '15%', 'sort' => TRUE); 
		$data['fields']['archive'] = array('label' => lang('ARCHIVE'), 'colwidth' => '5%', 'sort' => TRUE, 'type' => 'yes_no'); 
		$data['fields']['modifyDT'] = array('label' => lang('MODIFYDT'), 'colwidth' => '15%', 'sort' => TRUE); 
		$data['tools'] = $this->get_tools(); 
		$data['ds'] = $this->employeers_model->get_ds();
		$data['paging'] = $this->get_paging($this->employeers_model->get_calc_rows());
		return $this->load->view('standart/grid', $data, TRUE);		
	}

	private function create()
	{
		$data = array();
		$this->set_validation();
		$data['title'] = lang('EMPLOYEERS_TITLE_CREATE');
		$data['orid'] = $this->get_orid();
		$data['success'] = null;
		
		if($this->form_validation->run() === TRUE)
		{
			if(($rid = $this->employeers_model->create_record()))
			{
				$this->session->set_flashdata('success', TRUE);
				redirect(get_currcontroller()."/edit/$rid", 'refresh');
				return;
			}
			else 
			{
				$data['success'] = FALSE;
			} 
		}
		
		$data['content'] = $this->load->view('employeers/create', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}

	private function edit()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();
		$this->set_validation();
		$data['title'] = lang('EMPLOYEERS_TITLE_EDIT');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->employeers_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;
		
		if(!$data['ds']) show_404(); 
		if($this->form_validation->run() === TRUE)
		{
			if($this->employeers_model->update_record()) $data['success'] = TRUE;
			else $data['success'] = FALSE;
			
			$data['ds'] = $this->employeers_model->get_edit($rid);
		}
		
		$data['content'] = $this->load->view('employeers/edit', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}

	private function details()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();
		$data['title'] = lang('EMPLOYEERS_TITLE_DETAILS');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->employeers_model->get_edit($rid);
		
		if(!$data['ds']) show_404(); 
		
		$data['content'] = $this->load->view('employeers/details', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}

	private function find()
	{
		$data['orid'] = $this->get_orid();
		$this->form_validation->set_rules('l_name', lang('LNAME'), 'trim');
		$this->form_validation->set_rules('_filials_rid', lang('FILIAL'), 'trim');
		
		if($this->form_validation->run() == TRUE)
		{
			$search_rule = array();
			
			if($this->input->post('l_name')) $search_rule['like']['_employeers.l_name'] = $this->input->post('l_name');
			if($this->input->post('_filials_rid')) $search_rule['having']['_filials_rid'] = $this->input->post('_filials_rid');
			
			$this->set_searchrule($search_rule);
		}
		
		$search = $this->get_session('searchrule');
		$data['search'] = array_merge(element('like', $search, array()), element('where', $search, array()), element('having', $search, array()));
		return $this->load->view('employeers/find', $data, TRUE);
	}

	public function check_unique_name($code)
	{
		$rid = $this->input->post('rid'); 
		
		if($this->employeers_model->check_unique($code, 'name', $rid))
		{
			$this->form_validation->set_message('check_unique_name', lang('employeers_NAME_NOTUNIQUE'));
			return FALSE;
		}
		
		return TRUE;
	}

	public function check_unique_name_lat($code)
	{
		$rid = $this->input->post('rid');
		
		if($this->employeers_model->check_unique($code, 'name_lat', $rid))
		{
			$this->form_validation->set_message('check_unique_name_lat', lang('employeers_NAME_LAT_NOTUNIQUE'));
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
		$data['title'] = lang('EMPLOYEERS_TITLE_MOVE');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->employeers_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;
		
		if(!$data['ds']) show_404(); 
		if($this->form_validation->run() === TRUE)
		{
			if($this->employeers_model->move_record()) $data['success'] = TRUE;
			else $data['success'] = FALSE;
			
			$data['ds'] = $this->employeers_model->get_edit($rid);
		}
		
		$data['content'] = $this->load->view('employeers/move', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}
	
	private function vcreate()
	{
		$data = array();
		$this->set_validation();
		$data['title'] = lang('EMPLOYEERS_TITLE_CREATE');
		$data['orid'] = $this->get_orid();
		$data['success'] = null;
		
		if($this->form_validation->run() === TRUE)
		{
			if(($rid = $this->employeers_model->create_record()))
			{
				$this->session->set_flashdata('success', TRUE);
				redirect(get_currcontroller()."/vedit/$rid", 'refresh');
				return;
			}
			else $data['success'] = FALSE;
		}
		
		$data['content'] = $this->load->view('employeers/vcreate', $data, TRUE);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}

	public function vjournal()
	{
		$data = array();
		$data['title'] = lang('EMPLOYEERS_TITLE');
		$data['orid'] = $this->get_orid();
		$data['sort'] = $this->get_session('sort');
		$data['find'] = $this->vfind();
		$data['fields']['rid'] = array('label' => 'ID', 'colwidth' => '5%', 'sort' => TRUE); 
		$data['fields']['l_name'] = array('label' => lang('L_NAME'), 'colwidth' => '20%', 'sort' => TRUE);
		$data['fields']['f_name'] = array('label' => lang('F_NAME'), 'colwidth' => '20%', 'sort' => TRUE);
		$data['fields']['filial_name'] = array('label' => lang('FILIAL'), 'colwidth' => '10%', 'sort' => TRUE); 
		$data['fields']['birthday'] = array('label' => lang('BIRTHDAY'), 'colwidth' => '15%', 'sort' => TRUE);  
		$data['fields']['archive'] = array('label' => lang('ARCHIVE'), 'colwidth' => '10%', 'sort' => TRUE, 'type' => 'yes_no');
		$data['fields']['modifyDT'] = array('label' => lang('MODIFYDT'), 'colwidth' => '15%', 'sort' => TRUE); 
		$data['jtp'] = $this->jtp; 
		$data['tools'] = $this->get_tools(); 
		$data['ds'] = $this->employeers_model->get_ds();
		$data['paging'] = $this->get_paging($this->employeers_model->get_calc_rows(), TRUE);
		$content = $this->load->view('standart/vgrid', $data, TRUE);
		$this->load->view('layouts/valuepicker_layout', array('content' => $content));		
		return;
	}
	
	private function vedit()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();
		$this->set_validation();
		$data['title'] = lang('EMPLOYEERS_TITLE_EDIT');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['jtp'] = $this->jtp;
		$data['ds'] = $this->employeers_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success') ? $this->session->flashdata('success') : null;
		
		if(!$data['ds']) show_404(); 
		if($this->form_validation->run() === TRUE)
		{
			if($this->employeers_model->update_record()) $data['success'] = TRUE;
			else $data['success'] = FALSE;
			
			$data['ds'] = $this->employeers_model->get_edit($rid);
		}
		
		$data['content'] = $this->load->view('employeers/vedit', $data, TRUE);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}

	private function vdetails()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();
		$data['title'] = lang('EMPLOYEERS_TITLE_DETAILS');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['jtp'] = $this->jtp;
		$data['ds'] = $this->employeers_model->get_edit($rid);
		
		if(!$data['ds']) show_404(); 
		
		$data['content'] = $this->load->view('employeers/vdetails', $data, TRUE);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}

	private function vfind()
	{
		$data['orid'] = $this->get_orid();
		$this->form_validation->set_rules('l_name', lang('LNAME'), 'trim');
		$this->form_validation->set_rules('_filials_rid', lang('FILIAL'), 'trim');
		
		if($this->form_validation->run() == TRUE)
		{
			$search_rule = array();
			
			if($this->input->post('l_name')) $search_rule['like']['_employeers.l_name'] = $this->input->post('l_name');
			if($this->input->post('_filials_rid')) $search_rule['having']['_filials_rid'] = $this->input->post('_filials_rid');
			
			$this->set_searchrule($search_rule);
		}
		
		$search = $this->get_session('searchrule');
		$data['search'] = array_merge(element('like', $search, array()), element('where', $search, array()), element('having', $search, array()));
		return $this->load->view('employeers/vfind', $data, TRUE);
	}
	
	private function vmove()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();
		$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');
		$data['title'] = lang('EMPLOYEERS_TITLE_MOVE');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->employeers_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;
		
		if(!$data['ds']) show_404(); 
		if($this->form_validation->run() === TRUE)
		{
			if($this->employeers_model->move_record()) $data['success'] = TRUE;
			else $data['success'] = FALSE;
			
			$data['ds'] = $this->employeers_model->get_edit($rid);
		}
		
		$data['content'] = $this->load->view('employeers/vmove', $data, TRUE);
		return $this->load->view('layouts/valuepicker_layout', $data);
	}
	
	private function set_validation()
	{
		$this->form_validation->set_rules('f_name', lang('F_NAME'), 'required|trim|min_length[2]');
		$this->form_validation->set_rules('s_name', lang('S_NAME'), 'trim|min_length[2]');
		$this->form_validation->set_rules('l_name', lang('L_NAME'), 'required|trim|min_length[2]');
		$this->form_validation->set_rules('f_name_lat', lang('F_NAME_LAT'), 'trim|min_length[2]');
		$this->form_validation->set_rules('l_name_lat', lang('L_NAME_LAT'), 'trim|min_length[2]');
		$this->form_validation->set_rules('birthday', lang('BIRTHDAY'), 'trim');
		$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
		$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');
		return;
	}
}
?>