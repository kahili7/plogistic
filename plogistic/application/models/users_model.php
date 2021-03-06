<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH."libraries/core/crmmodel.php";

class Users_model extends Crmmodel
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_ds()
	{
		$this->db->select('SQL_CALC_FOUND_ROWS _users.rid as rid, 
							_users.user_login as user_login, 
							_users.user_passwd as user_passwd, 
							DATE_FORMAT(_users.edate_passwd, \'%d.%m.%Y \') as edate_passwd, 
							DATE_FORMAT(_users.chdate_passwd, \'%d.%m.%Y \') as chdate_passwd, 
							DATE_FORMAT(_users.modifyDT, \'%d.%m.%Y\') as modifyDT, 
							_users.descr as descr, _users.archive, 
							CONCAT(_employeers.l_name,\' \', _employeers.f_name, \' \', _employeers.s_name) as emp_name', False);
		$this->db->from('_users');
		$this->db->join('_employeers', '_users._employeers_rid = _employeers.rid');
		
		if(($searchRule = element('like', $this->ki->get_session('searchrule'), null))) $this->db->like($searchRule);
		if(($searchRule = element('where', $this->ki->get_session('searchrule'), null))) $this->db->where($searchRule);
		if(($searchRule = element('having', $this->ki->get_session('searchrule'), null))) $this->db->having($searchRule);
		if(($sort = $this->ki->get_session('sort'))) $this->db->orderby($sort['c'], $sort['r']);
		
		$this->db->limit($this->ki->config->item('crm_grid_limit'), element('p', $this->ki->a_uri_assoc, null));
		$query = $this->db_get('_users');
		return $query->num_rows() ? $query->result() : array();
	}
	
	public function get_edit($rid)
	{
		$this->db->select('_users.rid as rid, 
							_users.user_login as user_login, 
							_users.user_passwd as user_passwd, 
							DATE_FORMAT(_users.edate_passwd, \'%d.%m.%Y \') as edate_passwd, 
							DATE_FORMAT(_users.chdate_passwd, \'%d.%m.%Y \') as chdate_passwd, 
							_users.modifyDT as modifyDT,
							_users.owner_users_rid, 
							_users.descr as descr, _users.archive, _users._employeers_rid', FALSE);
		$this->db->from('_users');
		$this->db->where(array('rid' => $rid));
		$query = $this->db_get('_users');
		return $query->num_rows() ? $query->row() : FALSE;
	}
	
	public function create_record()
	{
		$ins_arr = array('_employeers_rid'=>$this->ki->input->post('_employeers_rid'),
							'user_login'=>$this->ki->input->post('user_login'),
							'user_passwd'=>$this->ki->input->post('user_passwd'),
							'edate_passwd'=>date('Y-m-d', strtotime($this->ki->input->post('edate_passwd'))),
							'chdate_passwd'=>date('Y-m-d', strtotime($this->ki->input->post('chdate_passwd'))),
							'descr'=>$this->ki->input->post('descr'),
							'archive'=>$this->ki->input->post('archive'),
							'owner_users_rid'=>get_curr_urid(),
							'modifier_users_rid'=>get_curr_urid());
		$this->db->set('createDT', 'now()', FALSE);
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->insert('_users', $ins_arr);
		$insRid = $this->db->insert_id();
		
		if ($this->db->trans_status() === FALSE)
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
		$update_arr = array('_employeers_rid'=>$this->ki->input->post('_employeers_rid'),
							'user_login'=>$this->ki->input->post('user_login'),
							'user_passwd'=>$this->ki->input->post('user_passwd'),
							'edate_passwd'=>date('Y-m-d', strtotime($this->ki->input->post('edate_passwd'))),
							'chdate_passwd'=>date('Y-m-d', strtotime($this->ki->input->post('chdate_passwd'))),
							'descr'=>$this->ki->input->post('descr'),
							'archive'=>$this->ki->input->post('archive'),
							'modifier_users_rid'=>get_curr_urid());
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->update('_users', $update_arr, array('rid'=>$this->ki->input->post('rid')));
		
		if($this->db->trans_status() === FALSE)
		{
    		$this->db->trans_rollback();
    		return False;
		}
		else
		{
    		$this->db->trans_commit();
    		return True;
		}		
	}
	
	public function remove_items()
	{
		$this->db->trans_begin();
		
		foreach($this->ki->input->post('row') as $rid)
		{
			$this->db->delete('_users', array('rid' => $rid));	
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
	
	public function check_unique($val, $rid=null)
	{
		$this->db->select('count(*) as quan');
		$this->db->from('_users');
		$this->db->where(array('user_login' => $val));
		
		if($rid) $this->db->where(array('rid != ' => $rid));
		$query = $this->db->get();
		return $query->num_rows() ? $query->row()->quan : 0;
	}

	public function get_emprid_byurid($urid)
	{
		$query = $this->db->select('_users._employeers_rid')->from('_users')->where(array('rid' => $urid))->get();
		return $query->num_rows() ? $query->row()->_employeers_rid : null; 	
	}
	
	public function get_urid_byemprid($urid)
	{
		$query = $this->db->select('_users.rid')->from('_users')->where(array('_employeers_rid' => $urid))->get();
		return $query->num_rows() ? $query->row()->rid : null; 	
	}
	
	public function move_record()
	{
		$update_doc = array('owner_users_rid' => get_urid_byemprid($this->ki->input->post('_employeers_rid')));
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->update('_users', $update_doc, array('_users.rid' => $this->ki->input->post('rid')));
		
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