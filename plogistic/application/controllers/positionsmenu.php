<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH."libraries/core/crmcontroller.php";

class Positionsmenu extends Crmcontroller 
{
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('positionsmenu');
		$this->load->model('positionsmenu_model');
		$this->load->helper('positions');
		$this->load->helper('positionsmenus');
		$this->load->helper('modules');
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
			case 'mlist': $this->output->set_output($this->mlist());break;
			default: $this->index();
		}
	}

	public function journal()
	{
		$data = array();
		$data['title'] = lang('POSITIONSMENU_TITLE');
		$data['orid'] = $this->get_orid();
		$data['sort'] = $this->get_session('sort');
		$data['find'] = $this->find();
		$data['fields']['rid'] = array('label' => 'ID', 'colwidth' => '5%', 'sort' => TRUE); 
		$data['fields']['position_name'] = array('label' => lang('POSITION'), 'colwidth' => '20%', 'sort' => TRUE);
		$data['fields']['item_name'] = array('label' => lang('ITEM_NAME'), 'colwidth' => '20%', 'sort' => TRUE);
		$data['fields']['module_controller'] = array('label' => lang('CONTROLLER'), 'colwidth' => '20%', 'sort' => TRUE);
		$data['fields']['archive'] = array('label' => lang('ARCHIVE'), 'colwidth' => '10%', 'sort' => TRUE, 'type' => 'yes_no'); 
		$data['fields']['modifyDT'] = array('label' => lang('MODIFYDT'), 'colwidth' => '20%', 'sort' => TRUE); 
		$data['tools'] = $this->get_tools(); 
		$data['ds'] = $this->positionsmenu_model->get_ds();
		$data['paging'] = $this->get_paging($this->positionsmenu_model->get_calc_rows());
		return $this->load->view('standart/grid', $data, TRUE);		
	}

	private function create()
	{
		$data = array();
		$this->set_validation();
		$data['title'] = lang('POSITIONSMENU_TITLE_CREATE');
		$data['orid'] = $this->get_orid();
		$data['success'] = null;
		
		if($this->form_validation->run() === TRUE)
		{
			if(($rid = $this->positionsmenu_model->create_record()))
			{
				$this->session->set_flashdata('success', TRUE);
				redirect(get_currcontroller()."/edit/$rid", 'refresh');
				return;
			}
			else $data['success'] = FALSE;
		}
		
		$data['content'] = $this->load->view('positionsmenu/create', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}

	private function edit()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();
		$this->set_validation();
		$data['title'] = lang('POSITIONSMENU_TITLE_EDIT');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->positionsmenu_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;
		
		if(!$data['ds']) show_404(); 
		if($this->form_validation->run() === TRUE)
		{
			if($this->positionsmenu_model->update_record()) $data['success'] = TRUE;
			else $data['success'] = FALSE;
			
			$data['ds'] = $this->positionsmenu_model->get_edit($rid);
		}
		
		$data['content'] = $this->load->view('positionsmenu/edit', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}

	private function details()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();
		$data['title'] = lang('POSITIONSMENU_TITLE_DETAILS');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->positionsmenu_model->get_edit($rid);
		
		if(!$data['ds']) show_404(); 
		
		$data['content'] = $this->load->view('positionsmenu/details', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}

	private function find()
	{
		$data['orid'] = $this->get_orid();
		$this->form_validation->set_rules('item_name', lang('ITEM_NAME'), 'trim');
		$this->form_validation->set_rules('position_name', lang('POSITION'), 'trim');
		
		if($this->form_validation->run() == TRUE)
		{
			$search_rule = array();
			
			if($this->input->post('item_name')) $search_rule['_positions_menus.item_name'] = $this->input->post('item_name');
			if($this->input->post('position_name')) $search_rule['_positions.name'] = $this->input->post('position_name');
			
			$this->set_searchrule($search_rule);
		}
		
		$data['search'] = $this->get_session('searchrule');
		return $this->load->view('positionsmenu/find', $data, TRUE);
	}
	
	private function mlist()
	{
		$prid = $this->input->post('prid');
		
		if(!$prid) return null;
		
		return build_tree_dropdown($prid);
	}
	
	private function move()
	{
		$rid = (int)$this->uri->segment(3);
		
		if(!$rid) show_404();
		
		$data = array();
		$this->form_validation->set_rules('_employeers_rid', lang('NEW_OWNER'), 'required');
		$data['title'] = lang('POSITIONSMENU_TITLE_MOVE');
		$data['rid'] = $rid;
		$data['orid'] = $this->get_orid();
		$data['ds'] = $this->positionsmenu_model->get_edit($rid);
		$data['success'] = $this->session->flashdata('success')?$this->session->flashdata('success'):null;
		
		if(!$data['ds']) show_404(); 
		if($this->form_validation->run() === TRUE)
		{
			if($this->positionsmenu_model->move_record()) $data['success'] = TRUE;
			else $data['success'] = FALSE;
			
			$data['ds'] = $this->positionsmenu_model->get_edit($rid);
		}
		
		$data['content'] = $this->load->view('positionsmenu/move', $data, TRUE);
		return $this->load->view('layouts/main_layout', $data);
	}
	
	private function set_validation()
	{
		$this->form_validation->set_rules('_positions_rid', lang('POSITION'), 'required|trim');
		$this->form_validation->set_rules('item_name', lang('ITEM_NAME'), 'trim|required');
		$this->form_validation->set_rules('_modules_rid', lang('MODULE'), 'trim');
		$this->form_validation->set_rules('descr', lang('DESCR'), 'trim|max_length[512]');
		$this->form_validation->set_rules('archive', lang('ARCHIVE'), 'trim');
		return;		
	}
}
?>