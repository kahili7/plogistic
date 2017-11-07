<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

include_once APPPATH . "libraries/core/crmmodel.php";

class Contacts_model extends Crmmodel
{

    public function __construct()
    {
	parent::__construct();
    }

    public function get_ds()
    {
	$this->db->select('SQL_CALC_FOUND_ROWS _contacts.rid as rid, _contacts.name as contact_name,
							_contacts.contface as contface,
							_contacts.job as job,
							_contacts.phone as phone,
							CONCAT(_employeers.l_name, \' \', SUBSTRING(_employeers.f_name FROM 1 FOR 1), \'.\') as emp_name,
							_contacts.descr as descr, _contacts.archive', FALSE);
	$this->db->from('_contacts');
	$this->db->join('_objs_rows', '_objs_rows._contacts_rid = _contacts.rid', 'LEFT');
	$this->db->join('_objs_headers', '_objs_rows._objs_headers_rid = _objs_headers.rid', 'LEFT');
	$this->db->join('_users', '_contacts.owner_users_rid = _users.rid');
	$this->db->join('_employeers', '_employeers.rid = _users._employeers_rid');
	$this->db->group_by('_contacts.rid');

	$searchRule = element('like', $this->ki->get_session('searchrule'), null);
	if ($searchRule)
	    $this->db->like($searchRule);

	$searchRule = element('where', $this->ki->get_session('searchrule'), null);
	if ($searchRule)
	    $this->db->where($searchRule);

	$searchRule = element('having', $this->ki->get_session('searchrule'), null);
	if ($searchRule)
	    $this->db->having($searchRule);

	$sort = $this->ki->get_session('sort');
	if ($sort)
	    $this->db->orderby($sort['c'], $sort['r']);

	$this->db->limit($this->ki->config->item('crm_grid_limit'), element('p', $this->ki->a_uri_assoc, null));
	$query = $this->db_get('_contacts');
	return $query->num_rows() ? $query->result() : array();
    }

    public function vget_ds()
    {
	$this->db->select('SQL_CALC_FOUND_ROWS _contacts.rid as rid, _contacts.name as contact_name,
							_contacts.contface as contface,
							_contacts.job as job,
							_contacts.phone as phone,
							CONCAT(_employeers.l_name, \' \', SUBSTRING(_employeers.f_name FROM 1 FOR 1), \'.\') as emp_name,
							_contacts.descr as descr, _contacts.archive', FALSE);
	$this->db->from('_contacts');
	$this->db->join('_objs_rows', '_objs_rows._contacts_rid = _contacts.rid', 'LEFT');
	$this->db->join('_objs_headers', '_objs_rows._objs_headers_rid = _objs_headers.rid', 'LEFT');
	$this->db->join('_users', '_contacts.owner_users_rid = _users.rid');
	$this->db->join('_employeers', '_employeers.rid = _users._employeers_rid');
	$this->db->group_by('_contacts.rid');
	$this->db->where(array('_users.rid' => get_curr_urid()));

	$searchRule = element('like', $this->ki->get_session('searchrule'), null);
	if ($searchRule)
	    $this->db->like($searchRule);

	$searchRule = element('where', $this->ki->get_session('searchrule'), null);
	if ($searchRule)
	    $this->db->where($searchRule);

	$searchRule = element('having', $this->ki->get_session('searchrule'), null);
	if ($searchRule)
	    $this->db->having($searchRule);

	$sort = $this->ki->get_session('sort');
	if ($sort)
	    $this->db->orderby($sort['c'], $sort['r']);

	$this->db->limit($this->ki->config->item('crm_grid_limit'), element('p', $this->ki->a_uri_assoc, null));
	$query = $this->db_get('_contacts');
	return $query->num_rows() ? $query->result() : array();
    }

    public function get_edit($rid)
    {
	$this->db->select('_contacts.rid as rid, _contacts.name as name,
							_contacts.name as contact_name,
							_contacts.contface as contface,
							_contacts.job as job,
							_contacts.phone as phone,
							_contacts.email as email,  
							DATE_FORMAT(_contacts.birthday, \'%d.%m.%Y\') as birthday, 
							DATE_FORMAT(_contacts.modifyDT, \'%d.%m.%Y %H:%i\') as modifyDT, 
							_contacts.owner_users_rid,
							_contacts.descr as descr, _contacts.archive', FALSE);
	$this->db->from('_contacts');
	$this->db->join('_users', '_contacts.owner_users_rid = _users.rid');
	$this->db->where(array('_contacts.rid' => $rid));
	$this->db->where(array('_users.rid' => get_curr_urid()));
	$query = $this->db_get('_contacts');
	return $query->num_rows() ? $query->row() : FALSE;
    }

    public function create_record()
    {
	$ins_arr = array('name' => $this->ki->input->post('name'),
	    'contface' => $this->ki->input->post('contface'),
	    'job' => $this->ki->input->post('job'),
	    'phone' => $this->ki->input->post('phone'),
	    'email' => $this->ki->input->post('email'),
	    'birthday' => date('Y-m-d', strtotime($this->ki->input->post('birthday'))),
	    'descr' => $this->ki->input->post('descr'),
	    'archive' => $this->ki->input->post('archive'),
	    'owner_users_rid' => get_curr_urid(),
	    'modifier_users_rid' => get_curr_urid());
	$this->db->set('createDT', 'now()', FALSE);
	$this->db->set('modifyDT', 'now()', FALSE);
	$this->db->trans_begin();
	$this->db->insert('_contacts', $ins_arr);
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
	$update_arr = array('name' => $this->ki->input->post('name'),
	    'contface' => $this->ki->input->post('contface'),
	    'job' => $this->ki->input->post('job'),
	    'phone' => $this->ki->input->post('phone'),
	    'email' => $this->ki->input->post('email'),
	    'birthday' => date('Y-m-d', strtotime($this->ki->input->post('birthday'))),
	    'descr' => $this->ki->input->post('descr'),
	    'archive' => $this->ki->input->post('archive'),
	    'owner_users_rid' => get_curr_urid(),
	    'modifier_users_rid' => get_curr_urid());
	$this->db->set('modifyDT', 'now()', FALSE);
	$this->db->trans_begin();
	$this->db->update('_contacts', $update_arr, array('rid' => $this->ki->input->post('rid')));

	if ($this->db->trans_status() === FALSE)
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

	foreach ($this->ki->input->post('row') as $rid)
	{
	    $this->db->delete('_contacts', array('rid' => $rid));
	}

	if ($this->db->trans_status() === FALSE)
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

    public function get_list()
    {
	$this->db->select('*');
	$this->db->from('_contacts');
	$this->db->order_by('_contacts.contface');
	$query = $this->db->get();
	return $query->num_rows() ? $query->result() : array();
    }

    public function check_unique($val, $type='name', $rid=null)
    {
	$this->db->select('count(*) as quan');
	$this->db->from('_contacts');

	if ($type == 'name')
	    $this->db->where(array('name' => $val));
	else
	    $this->db->where(array('contface' => $val));

	if ($rid)
	    $this->db->where(array('rid != ' => $rid));

	$query = $this->db->get();
	return $query->num_rows() ? $query->row()->quan : 0;
    }

    public function move_record()
    {
	$update = array('owner_users_rid' => get_urid_byemprid($this->ki->input->post('_employeers_rid')));
	$this->db->set('modifyDT', 'now()', FALSE);
	$this->db->trans_begin();
	$this->db->update('_contacts', $update, array('_contacts.rid' => $this->ki->input->post('rid')));

	if ($this->db->trans_status() === FALSE)
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

    public function get_contactname_byrid($rid)
    {
	$this->db->select('name as contact_name', FALSE);
	$this->db->from('_contacts');
	$this->db->where(array('rid' => $rid));
	$this->db->order_by('contact_name');
	$query = $this->db->get();
	return $query->num_rows() ? $query->row()->contact_name : null;
    }

}

?>