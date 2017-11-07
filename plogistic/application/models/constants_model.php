<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH."libraries/core/crmmodel.php";

class Constants_model extends Crmmodel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_ds()
	{
		$this->db->select('SQL_CALC_FOUND_ROWS _constants.rid as rid, _constants.code as code,
							_constants.name as name,
							DATE_FORMAT(_constants.modifyDT, \'%d.%m.%Y\') as modifyDT,
							_constants.descr as descr, _constants.archive', FALSE);
		$this->db->from('_constants');
		
		if(($searchRule = $this->ki->get_session('searchrule'))) $this->db->like($searchRule);
		if(($sort = $this->ki->get_session('sort'))) $this->db->orderby($sort['c'], $sort['r']);
		
		$this->db->limit($this->ki->config->item('crm_grid_limit'), element('p', $this->ki->a_uri_assoc, null));
		$query = $this->db_get('_constants');
		return $query->num_rows() ? $query->result() : array();
	}
	
	public function get_edit($rid)
	{
		$this->db->select('_constants.rid as rid, _constants.code as code,
							_constants.name as name,
							DATE_FORMAT(_constants.modifyDT, \'%d.%m.%Y %H:%i\') as modifyDT,
							_constants.owner_users_rid,
							_constants.descr as descr, _constants.archive', FALSE);
		$this->db->from('_constants');
		$this->db->where(array('_constants.rid' => $rid));
		$query = $this->db_get('_constants');
		return $query->num_rows() ? $query->row() : FALSE;
	}
	
	public function create_record()
	{
		$ins_arr = array('code' => $this->ki->input->post('code'),
							'name' => $this->ki->input->post('name'),
							'descr' => $this->ki->input->post('descr'),
							'archive' => $this->ki->input->post('archive'),
							'owner_users_rid' => get_curr_urid(),
							'modifier_users_rid' => get_curr_urid());
		$this->db->set('createDT', 'now()', FALSE);
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->insert('_constants', $ins_arr);
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
		$update_arr = array('code' => $this->ki->input->post('code'),
							'name' => $this->ki->input->post('name'),
							'descr' => $this->ki->input->post('descr'),
							'archive' => $this->ki->input->post('archive'),
							'modifier_users_rid'=>get_curr_urid());
		$this->db->set('modifyDT', 'now()', False);
		$this->db->trans_begin();
		$this->db->update('_constants', $update_arr, array('rid' => $this->ki->input->post('rid')));
		
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
			$this->db->delete('_constants', array('rid' => $rid));	
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

	public function check_unique($val, $type='code', $rid=null)
	{
		$this->db->select('count(*) as quan');
		$this->db->from('_constants');
		
		if($type == 'code') $this->db->where(array('code' => $val));
		else $this->db->where(array('name' => $val));
		
		if($rid) $this->db->where(array('rid != ' => $rid));
		
		$query = $this->db->get();
		return $query->num_rows() ? $query->row()->quan : 0;
	}
	
	public function move_record()
	{
		$update_doc = array('owner_users_rid' => get_urid_byemprid($this->ki->input->post('_employeers_rid')));
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->update('_constants', $update_doc, array('_constants.rid'=>$this->ki->input->post('rid')));
		
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
	
	public function get_list()
	{
		$this->db->select('*');
		$this->db->from('_constants');
		$this->db->order_by('_constants.name');
		$query = $this->db->get();
		return $query->num_rows() ? $query->result() : array(); 
	}
}
?>