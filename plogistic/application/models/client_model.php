<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH."libraries/core/crmmodel.php";

class Client_model extends Crmmodel
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_ds()
	{
		$this->db->select('SQL_CALC_FOUND_ROWS _client.rid_cl as rid, _client._id_client as id, _client.name_cl as name,
							_client.descr_cl as descr', FALSE);
		$this->db->from('_client');
		$this->db->group_by('_client._id_client');

		if(($searchRule = element('like', $this->ki->get_session('searchrule'), null))) $this->db->like($searchRule);
		if(($searchRule = element('where', $this->ki->get_session('searchrule'), null))) $this->db->where($searchRule);
		if(($searchRule = element('having', $this->ki->get_session('searchrule'), null))) $this->db->having($searchRule);
		if(($sort = $this->ki->get_session('sort'))) $this->db->orderby($sort['c'], $sort['r']);

		$this->db->limit($this->ki->config->item('crm_grid_limit'), element('p', $this->ki->a_uri_assoc, null));
		$query = $this->db_get('_client');
		return $query->num_rows() ? $query->result() : array();
	}

	public function get_edit($rid)
	{
		$this->db->select('_rivals.rid as rid,
							_rivals.name as name,
							_rivals.name as rival_name,
							DATE_FORMAT(_rivals.modifyDT, \'%d.%m.%Y %H:%i\') as modifyDT,
							_rivals.owner_users_rid,
							_rivals.descr as descr, _rivals.archive', FALSE);
		$this->db->from('_rivals');
		$this->db->where(array('_rivals.rid' => $rid));
		$query = $this->db_get('_rivals');
		return $query->num_rows() ? $query->row() : FALSE;
	}

	public function create_record()
	{
		$ins_arr = array('name' => $this->ki->input->post('name'),
		'descr' => $this->ki->input->post('descr'),
		'archive' => $this->ki->input->post('archive'),
		'owner_users_rid' => get_curr_urid(),
		'modifier_users_rid' => get_curr_urid());
		$this->db->set('createDT', 'now()', FALSE);
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->insert('_rivals', $ins_arr);
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
		$update_arr = array('name' => $this->ki->input->post('name'),
		'descr' => $this->ki->input->post('descr'),
		'archive' => $this->ki->input->post('archive'),
		'modifier_users_rid' => get_curr_urid());
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->update('_rivals', $update_arr, array('rid' => $this->ki->input->post('rid')));

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
			$this->db->delete('_rivals', array('rid' => $rid));
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

	public function check_unique($val, $type='name', $rid=null)
	{
		$this->db->select('count(*) as quan');
		$this->db->from('_rivals');

		if($type == 'name') $this->db->where(array('name' => $val));

		if($rid) $this->db->where(array('rid != ' => $rid));

		$query = $this->db->get();
		return $query->num_rows() ? $query->row()->quan : 0;
	}

	public function move_record()
	{
		$update_doc = array('owner_users_rid' => get_urid_byemprid($this->ki->input->post('_employeers_rid')));
		$this->db->set('modifyDT', 'now()', FALSE);
		$this->db->trans_begin();
		$this->db->update('_rivals', $update_doc, array('_rivals.rid' => $this->ki->input->post('rid')));

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
		$this->db->from('_rivals');
		$this->db->order_by('_rivals.name');
		$query = $this->db->get();
		return $query->num_rows() ? $query->result() : array();
	}

	public function get_rivalname_byrid($rid)
	{
		$this->db->select('name as rival_name', FALSE);
		$this->db->from('_rivals');
		$this->db->where(array('rid' => $rid));
		$this->db->order_by('rival_name');
		$query = $this->db->get();
		return $query->num_rows() ? $query->row()->rival_name : null;
	}
}
?>