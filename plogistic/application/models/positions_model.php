<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

include_once APPPATH . "libraries/core/crmmodel.php";

class Positions_model extends Crmmodel
{

    public function __construct()
    {
	parent::__construct();
    }

    public function get_ds()
    {
	$this->db->select('SQL_CALC_FOUND_ROWS _positions.rid as rid, _positions.name as name,
							_positions.modifyDT as modifyDT, 
							_positions.descr as descr, _positions.archive', FALSE);
	$this->db->from('_positions');

	if (($searchRule = $this->ki->get_session('searchrule')))
	    $this->db->like($searchRule);
	if (($sort = $this->ki->get_session('sort')))
	    $this->db->orderby($sort['c'], $sort['r']);

	$this->db->limit($this->ki->config->item('crm_grid_limit'), element('p', $this->ki->a_uri_assoc, null));
	$query = $this->db_get('_positions');
	return $query->num_rows() ? $query->result() : array();
    }

    public function get_edit($rid)
    {
	$this->db->select('_positions.rid as rid, _positions.name as name,
							_positions.modifyDT as modifyDT, 
							_positions.owner_users_rid,
							_positions.descr as descr, _positions.archive');
	$this->db->from('_positions');
	$this->db->where(array('_positions.rid' => $rid));
	$query = $this->db_get('_positions');
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
	$this->db->insert('_positions', $ins_arr);
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
	    'descr' => $this->ki->input->post('descr'),
	    'archive' => $this->ki->input->post('archive'),
	    'modifier_users_rid' => get_curr_urid());
	$this->db->set('modifyDT', 'now()', FALSE);
	$this->db->trans_begin();
	$this->db->update('_positions', $update_arr, array('rid' => $this->ki->input->post('rid')));

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
	    $this->db->delete('_positions', array('rid' => $rid));
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

    public function check_unique($val, $type='name', $rid=null)
    {
	$this->db->select('count(*) as quan');
	$this->db->from('_positions');

	if ($type == 'name')
	    $this->db->where(array('name' => $val));
	
	if ($rid)
	    $this->db->where(array('rid != ' => $rid));

	$query = $this->db->get();
	return $query->num_rows() ? $query->row()->quan : 0;
    }

    public function get_list()
    {
	$this->db->select('*');
	$this->db->from('_positions');
	$this->db->order_by('_positions.name');
	$query = $this->db->get();
	return $query->num_rows() ? $query->result() : array();
    }

    public function move_record()
    {
	$update_doc = array('owner_users_rid' => get_urid_byemprid($this->ki->input->post('_employeers_rid')));
	$this->db->set('modifyDT', 'now()', FALSE);
	$this->db->trans_begin();
	$this->db->update('_positions', $update_doc, array('_positions.rid' => $this->ki->input->post('rid')));

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

}

?>