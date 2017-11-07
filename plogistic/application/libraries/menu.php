<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

class MENU
{
	private $KI;
	private $a_menu_items = array();
	private $a_currcontroller = null;
	private $a_permissions = null;

	public function __construct() 
	{
		$this->KI =& get_instance();	
		$this->KI->load->model('menu_model');
	}
	
	public function init_menu()
	{
		$assoc_arr = $this->KI->uri->uri_to_assoc(2);
		$this->a_currcontroller = $this->KI->uri->segment(1);
		$this->a_menu_items = $this->KI->menu_model->get_menu_items(get_curr_uprid());
		$this->a_permissions = $this->KI->menu_model->get_permissions(get_curr_uprid(), $this->a_currcontroller);
		return;
	}
	
	public function get_currcontroller()
	{
		return $this->a_currcontroller;
	}
	
	public function render_menu()
	{
		$data['items'] = transform2forest($this->a_menu_items, 'rid', 'parent');
		return $this->KI->load->view('common/menu', $data, TRUE);
	}

	
	public function get_rights()
	{
		return $this->a_permissions;
	}

	public function get_allowed_area()
	{
		return element('viewed_space', $this->a_permissions, null);
	}
}
?>