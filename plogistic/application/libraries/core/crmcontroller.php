<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

abstract class Crmcontroller extends KI_CONTROLLER
{
	public $a_uri_assoc = null; 
	public $a_constants = array();
	
	public function __construct() 
	{
		parent::__construct();
		$this->lang->load('tools'); 
		$this->lang->load('common');
		$this->load->model('constants_model');
		$this->a_uri_assoc = $this->uri->uri_to_assoc(2);
		$constants = $this->constants_model->get_list();
		
		foreach($constants as $constant)
		{
			$this->a_constants[$constant->code] = $constant->descr;  	
		}
	}
	
	public function index()
	{
		$data = array();
		$data['content'] = $this->journal();
		$this->load->view('layouts/main_layout', $data);
	}
	
	public function set_session($name, $value)
	{
		$sessionData = $this->session->userdata($this->get_orid());
		$sessionData[$name] = $value; 
		$this->session->set_userdata($this->get_orid(), $sessionData);
		return;
	}
	
	public function get_session($name)
	{
		return element($name, $this->session->userdata($this->get_orid()), null);
	}
	
	public function refresh_session()
	{
		return $this->session->set_userdata($this->get_orid(), null);		
	}
	
	public function get_orid()
	{
		return 'obj_'.$this->menu->get_currcontroller();
	}
	
	public function get_paging($total_rows, $value_picker=FALSE)
	{
		$t = $this->a_uri_assoc;
		unset($t['p']);
		$this->load->library('pagination');
		$config['base_url'] = $value_picker ? site_url().'/'.get_currcontroller().'/vjournal/go/p/' : site_url().'/'.get_currcontroller().'/p/';
		$config['total_rows'] = $total_rows;
		$config['uri_segment'] = count($t)*2+3;
		$config['num_links'] = 10;
		$config['per_page'] = $this->config->item('crm_grid_limit');
		$config['first_link'] = '&lt;&lt;';
		$config['last_link'] = '&gt;&gt;';
		$this->pagination->initialize($config);
		$data = array();
		$data['pagination'] = $this->pagination->create_links();
		$data['p_descr'] = sprintf(lang('PAGING'), $total_rows);
		return $this->load->view('standart/paging', $data, TRUE);
	}
	
	public function get_tools()
	{
		return $this->menu->get_rights();
	}

	public function get_objtype()
	{
		return strtoupper(get_class($this)); 	
	}

	protected function sort()
	{
		$field = element('sort', $this->a_uri_assoc, null);
		
		if($field)
		{
			$sort = array('c' => $field, 'r' => 'ASC');
			
			if(($oldSort = $this->get_session('sort')))
			{
				if($oldSort['c'] == $field) 
				{
					$sort['r'] = ($oldSort['r'] == 'ASC') ? 'DESC' : 'ASC';
				}
			}
			
			$this->set_session('sort', $sort);
		}
		
		$this->index();
		return;
	}
	
	protected function vsort()
	{
		$field = element('vsort', $this->a_uri_assoc, null);
		
		if($field)
		{
			$sort = array('c' => $field, 'r' => 'ASC');
			
			if(($oldSort = $this->get_session('sort')))
			{
				if($oldSort['c']==$field) 
				{
					$sort['r'] = ($oldSort['r'] == 'ASC') ? 'DESC' : 'ASC';
				}
			}
			
			$this->set_session('sort', $sort);
		}
		
		$this->vjournal();
		return;
	}
	
	public function set_searchrule($searchrule)
	{
		$this->set_session('searchrule', $searchrule);
		return;
	}
	
	protected function remove()
	{
		$model_name = strtolower(get_currcontroller().'_model');
		
		if($this->$model_name->remove_items())
		{
			$this->session->set_flashdata('remove_success', TRUE);
		} 
		else 
		{
			$this->session->set_flashdata('remove_failed', TRUE);
		}
		
		redirect(get_currcontroller(), 'refresh');
	}
	
	protected function vremove()
	{
		$model_name = strtolower(get_currcontroller().'_model');
		
		if($this->$model_name->remove_items())
		{
			$this->session->set_flashdata('vremove_success', TRUE);
		} 
		else 
		{
			$this->session->set_flashdata('vremove_failed', TRUE);
		}
		
		redirect(get_currcontroller()."/vjournal/go", 'refresh');
	}
}
?>