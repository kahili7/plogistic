<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH."libraries/core/crmmodel.php";

class Employeers_model extends Crmmodel
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_ds()
	{
		$this->db->select('SQL_CALC_FOUND_ROWS _employeers.*,
						CONCAT(l_name," ",f_name) as full_name, 
						(select _filials.name 
						FROM _emp_to_positions_rows 
						JOIN _emp_to_positions_headers ON _emp_to_positions_rows._emp_to_positions_headers_rid=_emp_to_positions_headers.rid
						JOIN _filials ON _emp_to_positions_rows._filials_rid=_filials.rid
						WHERE _emp_to_positions_rows._employeers_rid = _employeers.rid AND _emp_to_positions_headers.date_obj < now() 
						ORDER BY  _emp_to_positions_headers.date_obj ASC LIMIT 1) as filial_name,  
						(select _filials.rid 
						FROM _emp_to_positions_rows 
						JOIN _emp_to_positions_headers ON _emp_to_positions_rows._emp_to_positions_headers_rid=_emp_to_positions_headers.rid
						JOIN _filials ON _emp_to_positions_rows._filials_rid=_filials.rid
						WHERE _emp_to_positions_rows._employeers_rid = _employeers.rid AND _emp_to_positions_headers.date_obj < now() 
						ORDER BY  _emp_to_positions_headers.date_obj ASC LIMIT 1) as _filials_rid,						
						DATE_FORMAT(_employeers.birthday, \'%d.%m.%Y\') as birthday,
						DATE_FORMAT(_employeers.modifyDT,  \'%d.%m.%Y\') as modifyDT', FALSE);
		$this->db->from('_employeers');
		
		if(($searchRule = element('like', $this->ki->get_session('searchrule'), null))) $this->db->like($searchRule);
		if(($searchRule = element('where', $this->ki->get_session('searchrule'), null))) $this->db->where($searchRule);
		if(($searchRule = element('having', $this->ki->get_session('searchrule'), null))) $this->db->having($searchRule);
		if(($sort = $this->ki->get_session('sort'))) $this->db->orderby($sort['c'], $sort['r']);
		
		$this->db->limit($this->ki->config->item('crm_grid_limit'), element('p', $this->ki->a_uri_assoc, null));
		$query = $this->db_get('_employeers');
		return $query->num_rows() ? $query->result() : array();
	}

	public function get_edit($rid)
	{
		$this->db->select('_employeers.*,
							CONCAT(l_name," ",f_name) as full_name, 
							DATE_FORMAT(_employeers.birthday, \'%d.%m.%Y\') as birthday,
							_employeers.owner_users_rid,
							DATE_FORMAT(_employeers.modifyDT,  \'%d.%m.%Y %H:%i\') as modifyDT', FALSE);
		$this->db->from('_employeers');
		$this->db->where(array('_employeers.rid' => $rid));
		$query = $this->db_get('employeers');
		return $query->num_rows() ? $query->row() : FALSE;
	}

	public function create_record()
	{
		$ins_arr = array('f_name' => $this->ki->input->post('f_name'),
							's_name' => $this->ki->input->post('s_name'),
							'l_name' => $this->ki->input->post('l_name'),
							'f_name_lat' => $this->ki->input->post('f_name_lat'),
							'l_name_lat' => $this->ki->input->post('l_name_lat'),
							'birthday' => date('Y-m-d', strtotime($this->ki->input->post('birthday'))),
							'phone' => $this->ki->input->post('phone'),	
							'email' => $this->ki->input->post('email'),
							'is_legal' => $this->ki->input->post('is_legal'),
							'descr' => $this->ki->input->post('descr'),
							'archive' => $this->ki->input->post('archive'),
							'owner_users_rid' => get_curr_urid(),
							'modifier_users_rid' => get_curr_urid());
		$this->db->set('createDT', 'now()', FALSE);
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->insert('_employeers', $ins_arr);
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
		$update_arr = array('f_name' => $this->ki->input->post('f_name'),
							's_name' => $this->ki->input->post('s_name'),
							'l_name' => $this->ki->input->post('l_name'),
							'f_name_lat' => $this->ki->input->post('f_name_lat'),
							'l_name_lat' => $this->ki->input->post('l_name_lat'),
							'birthday' => date('Y-m-d', strtotime($this->ki->input->post('birthday'))),
							'phone' => $this->ki->input->post('phone'),	
							'email' => $this->ki->input->post('email'),
							'is_legal' => $this->ki->input->post('is_legal'),
							'descr' => $this->ki->input->post('descr'),
							'archive' => $this->ki->input->post('archive'),
							'modifier_users_rid' => get_curr_urid());
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->update('_employeers', $update_arr, array('rid' => $this->ki->input->post('rid')));
		
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
			$this->db->delete('_employeers', array('rid' => $rid));	
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

	public function get_emp_fullname_byrid($rid)
	{
		$this->db->select('CONCAT(l_name," ",f_name) as full_name', FALSE);
		$this->db->from('_employeers');
		$this->db->where(array('rid'=>$rid));
		$this->db->order_by('full_name');
		$query = $this->db->get();
		return $query->num_rows() ? $query->row()->full_name : null; 
	}	
	
	public function move_record()
	{
		$update_doc = array('owner_users_rid' => get_urid_byemprid($this->ki->input->post('_employeers_rid')));
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->update('_employeers', $update_doc, array('_employeers.rid'=>$this->ki->input->post('rid')));
		
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