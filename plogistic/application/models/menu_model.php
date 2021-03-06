<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

class Menu_model extends KI_MODEL
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_menu_items($uprid)
	{
		$this->db->select('_positions_menus.rid, 
							_positions_menus.parent, 
							_positions_menus.item_name, 
							_modules.module_controller,
							_positions_menus.descr', FALSE);
		$this->db->from('_positions_menus');
		$this->db->join('_modules', '_positions_menus._modules_rid = _modules.rid and _modules.archive = 0', 'LEFT');
		$this->db->where(array('_positions_menus._positions_rid' => $uprid, '_positions_menus.archive' => 0));
		$this->db->orderby('_positions_menus.item_order,_positions_menus.item_name');
		$query = $this->db->get();
		return $query->num_rows() ? $query->result_array() : array();
	}
	
	public function get_permissions($uprid, $controller)
	{
		$this->db->select('_modules_permissions.add_allow,
							_modules_permissions.edit_allow,
							_modules_permissions.delete_allow,
							_modules_permissions.details_allow,
							_modules_permissions.move_allow,
							_modules_permissions.archive_allow,
							_modules_permissions.viewed_space');
		$this->db->from('_modules_permissions');
		$this->db->join('_modules', '_modules_permissions._modules_rid = _modules.rid');
		$this->db->where(array('_modules.module_controller' => $controller, '_modules_permissions._positions_rid' => $uprid));
		$query = $this->db->get();
		return $query->num_rows() ? $query->row_array() : array();
	}
}
?>