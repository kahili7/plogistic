<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

abstract class Crmmodel extends KI_MODEL
{
	protected $ki;

	public function __construct()
	{
		parent::__construct();
		$this->ki =& get_instance();
	}

	public function get_calc_rows()
	{
		return $this->db->select('FOUND_ROWS() as rquan')->get()->row()->rquan;
	}

	public function db_get($p_fromTable)
	{
		if(($area = $this->ki->menu->get_allowed_area()))
		{
			switch($area)
			{
				case 'FILIAL':
					$this->db->join('_users', "{$p_fromTable}.owner_users_rid = _users.rid and _users.archive = 0");
					$this->db->join('_employeers', "_users._employeers_rid = _employeers.rid and _employeers.archive = 0");
					$this->db->join('_filials', '_filials.rid = (select _emp_to_positions_rows._filials_rid
									from _emp_to_positions_rows join _filials on _emp_to_positions_rows._filials_rid = _filials.rid 
									where _emp_to_positions_rows._employeers_rid = _employeers.rid and _emp_to_positions_rows.bdate<=\''.date('Y-m-d').'\' order by _emp_to_positions_rows.bdate DESC limit 1)');
					$this->db->where(array('_filials.rid' => $this->kiObject->user->GetFilialRid()));
					break;


				case 'OWN':
					$this->db->join('_users', "{$p_fromTable}.owner_users_rid = _users.rid and _users.archive = 0");
					$this->db->where(array('_users.rid' => $this->kiObject->user->GetUserRid()));
					break;

				default: break;
			}
		}
		
		return $this->db->get();
	}
}
?>