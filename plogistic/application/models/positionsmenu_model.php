<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH."libraries/core/crmmodel.php";

class Positionsmenu_model extends Crmmodel 
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_ds()
	{
		$this->db->select('SQL_CALC_FOUND_ROWS _positions_menus.rid as rid, _positions_menus._positions_rid as _positions_rid, 
							_positions_menus.item_name as item_name,
							_positions.name as position_name,
							_modules.module_name as module_name,
							_modules.module_controller as module_controller,
							DATE_FORMAT(_positions_menus.modifyDT, \'%d.%m.%Y\') as modifyDT, 
							_positions_menus.descr as descr, _positions_menus.archive as archive', FALSE);
		$this->db->from('_positions_menus');
		$this->db->join('_positions', '_positions_menus._positions_rid = _positions.rid');
		$this->db->join('_modules', '_positions_menus._modules_rid = _modules.rid', 'LEFT');
		
		if(($searchRule = $this->ki->get_session('searchrule'))) $this->db->like($searchRule);
		if(($sort = $this->ki->get_session('sort'))) $this->db->orderby($sort['c'], $sort['r']);
		
		$this->db->limit($this->ki->config->item('crm_grid_limit'), element('p', $this->ki->a_uri_assoc, null));
		$query = $this->db_get('_cities');
		return $query->num_rows() ? $query->result() : array();
	}
	
	public function get_edit($rid)
	{
		$this->db->select('_positions_menus.rid as rid, _positions_menus._positions_rid as _positions_rid, 
							_positions_menus.item_name as item_name,
							_positions_menus._modules_rid as _modules_rid,
							_positions_menus.parent as parent,
							_positions_menus.item_order as item_order,
							_positions_menus.modifyDT as modifyDT, 
							_positions_menus.owner_users_rid,
							_positions_menus.descr as descr, _positions_menus.archive as archive');
		$this->db->from('_positions_menus');
		$this->db->where(array('_positions_menus.rid' => $rid));
		$query = $this->db_get('_positions_menus');
		return $query->num_rows() ? $query->row() : FALSE;
	}

	public function create_record()
	{
		$ins_arr = array('_positions_rid'=>$this->ki->input->post('_positions_rid'),
							'_modules_rid'=>$this->ki->input->post('_modules_rid')?$this->ki->input->post('_modules_rid'):null,
							'item_name'=>$this->ki->input->post('item_name'),
							'parent'=>$this->ki->input->post('parent'),
							'item_order'=>$this->ki->input->post('item_order'),
							'descr'=>$this->ki->input->post('descr'),
							'archive'=>$this->ki->input->post('archive'),
							'owner_users_rid'=>get_curr_urid(),
							'modifier_users_rid'=>get_curr_urid());
		$this->db->set('createDT', 'now()', FALSE);
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->insert('_positions_menus', $ins_arr);
		$insRid = $this->db->insert_id();
		
		if($this->db->trans_status() === FALSE)
		{
    		$this->db->trans_rollback();
    		return FALSE;
		}
		else
		{
    		$this->db->trans_commit();
    		return $insRid;
		}		
	}
	
	public function update_record()
	{
		$update_arr = array('_positions_rid'=>$this->ki->input->post('_positions_rid'),
							'_modules_rid'=>$this->ki->input->post('_modules_rid') ? $this->ki->input->post('_modules_rid') : null,
							'item_name'=>$this->ki->input->post('item_name'),
							'parent'=>$this->ki->input->post('parent'),
							'item_order'=>$this->ki->input->post('item_order'),
							'descr'=>$this->ki->input->post('descr'),
							'archive'=>$this->ki->input->post('archive'),
							'modifier_users_rid'=>get_curr_urid());
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->update('_positions_menus', $update_arr, array('rid' => $this->ki->input->post('rid')));
		
		if($this->db->trans_status() === FALSE)
		{
    		$this->db->trans_rollback();
    		return FALSE;
		}
		else
		{
    		$this->db->trans_commit();
    		return TRUE;
		}		
	}

	public function remove_items()
	{
		$this->db->trans_begin();
		
		foreach($this->ki->input->post('row') as $rid)
		{
			$this->db->delete('_positions_menus', array('rid' => $rid));	
		}
		
		if($this->db->trans_status() === FALSE)
		{
    		$this->db->trans_rollback();
    		return FALSE;
		}
		else
		{
    		$this->db->trans_commit();
    		return TRUE;
		}		
	}
	
	public function get_list($prid)
	{
		$this->db->select('_positions_menus.*');
		$this->db->from('_positions_menus');
		$this->db->where(array('_positions_menus._positions_rid' => $prid));
		$this->db->order_by('_positions_menus.item_order, _positions_menus.item_name');
		$query = $this->db->get();
		return $query->num_rows() ? $query->result_array() : array(); 
	}	
	
	
	public function move_record()
	{
		$update_doc = array('owner_users_rid' => get_urid_byemprid($this->ki->input->post('_employeers_rid')));
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->update('_positions_menus', $update_doc, array('_positions_menus.rid'=>$this->ki->input->post('rid')));
		
		if($this->db->trans_status() === FALSE)
		{
    		$this->db->trans_rollback();
    		return FALSE;
		}
		else
		{
    		$this->db->trans_commit();
    		return $this->ki->input->post('rid');
		}		
	}
}
?>