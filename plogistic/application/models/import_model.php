<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

include_once APPPATH . "libraries/core/crmmodel.php";

class Import_model extends Crmmodel
{

    public function __construct()
    {
	parent::__construct();
    }

    public function erase_db($db)
    {
	$this->db->truncate($db);
    }

    public function load_insert($path, $table, $arr_field)
    {
	set_time_limit(0);
	$file_content = file($path);
	$desc = '';
	
	if (is_array($file_content) and (count($file_content) > 0))
	{
	    foreach ($file_content as $line_num => $line)
	    {
		list($id, $name) = explode("\t", $line);
		$nm = addslashes($name);
		$data[$line_num] = array($id, $nm, $desc);
	    }
	}
	else
	    return FALSE;

	foreach ($data as $key => $value)
	{
	    $sql_ins[] = join("','", $data[$key]);
	    array_pop($data[$key]);
	    array_shift($data[$key]);
	}

	$multi_v = "('" . join("'),('", $sql_ins) . "')";

	if (empty($arr_field))
	    return FALSE;

	$multi_i = "(`" . join("`, `", $arr_field) . "`)";
	$sql = "INSERT INTO `".$table."` ".$multi_i." VALUES " . $multi_v . ";";
	$this->db->query($sql);

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

    public function insert_t_gs($path, $table, $arr_field)
    {
	set_time_limit(0);
	$file_content = file($path);
	$desc = '';

	if (is_array($file_content) and (count($file_content) > 0))
	{
	    foreach ($file_content as $line_num => $line)
	    {
		list($id_g, $id_s, $name) = explode("\t", $line);
		$nm = addslashes($name);
		$data[$line_num] = array(($id_g . " " . $id_s), $nm, $desc);
	    }
	}
	else
	    return FALSE;

	foreach ($data as $key => $value)
	{
	    $sql_ins[] = join("','", $data[$key]);
	    array_pop($data[$key]);
	    array_shift($data[$key]);
	}

	$multi_v = "('" . join("'),('", $sql_ins) . "')";

	if (empty($arr_field))
	    return FALSE;

	$multi_i = "(`" . join("`, `", $arr_field) . "`)";
	$sql = "INSERT INTO `".$table."` ".$multi_i." VALUES " . $multi_v . ";";
	$this->db->query($sql);

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

    public function insert_t_all($arr, $arr_field)
    {
	set_time_limit(0);

	if (is_array($arr) and (count($arr) > 0))
	{
	    foreach ($arr as $line_num => $line)
	    {
		list($_id_wh, $_id_gs, $art_nr, $name, $svobodno, $vsevo_na_sklade) = explode(";;", $line);
		$nm = addslashes($name);
		$data[$line_num] = array($_id_wh, $_id_gs, $art_nr, $nm, $svobodno, $vsevo_na_sklade);
	    }
	}
	else
	    return FALSE;

	foreach ($data as $key => $value)
	{
	    $sql_ins[] = join("','", $data[$key]);
	    array_pop($data[$key]);
	    array_shift($data[$key]);
	}

	$multi_v = "('" . join("'),('", $sql_ins) . "')";

	if (empty($arr_field))
	    return FALSE;

	$multi_i = "(`" . join("`, `", $arr_field) . "`)";
	$sql = "INSERT INTO `_table_all` ".$multi_i." VALUES " . $multi_v . ";";
	$this->db->query($sql);

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

    public function update_t_all($arr, $arr_field)
    {
	set_time_limit(0);

	if (is_array($arr) and (count($arr) > 0))
	{
	    foreach ($arr as $line_num => $line)
	    {
		list($art_nr, $svobodno, $id_wh) = explode(";;", $line);
		$data[$line_num] = "WHEN `".$arr_field[0]."`='".$art_nr."' THEN '".$svobodno."'";
	    }
	}
	else
	    return FALSE;

	$multi_v = "SET `" . $arr_field[1] . "` = CASE " . join(" ", $data) . " END";

	if (empty($arr_field))
	    return FALSE;

	$sql = "UPDATE `_table_all` ".$multi_v." WHERE `".$arr_field[2]."`='".$id_wh."';";
	$this->db->query($sql);

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

    public function insert_t_pr($arr, $arr_field)
    {
	set_time_limit(0);

	if (is_array($arr) and (count($arr) > 0))
	{
	    foreach ($arr as $line_num => $line)
	    {
		list($_id_wh, $art_nr, $_id_client, $prixod_date, $prixod_nr, $prixod_pos, $prixod_count, $prixod_flag) = explode(";;", $line);
		$data[$line_num] = array($_id_wh, $art_nr, $_id_client, $prixod_date, $prixod_nr, $prixod_pos, $prixod_count, $prixod_flag);
	    }
	}
	else
	    return FALSE;

	foreach ($data as $key => $value)
	{
	    $sql_ins[] = join("','", $data[$key]);
	    array_pop($data[$key]);
	    array_shift($data[$key]);
	}

	$multi_v = "('" . join("'),('", $sql_ins) . "')";

	if (empty($arr_field))
	    return FALSE;

	$multi_i = "(`" . join("`, `", $arr_field) . "`)";
	$sql = "INSERT INTO `_table_prixod` ".$multi_i." VALUES " . $multi_v . ";";
	$this->db->query($sql);

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

    public function insert_t_zk($arr, $arr_field)
    {
	set_time_limit(0);

	if (is_array($arr) and (count($arr) > 0))
	{
	    foreach ($arr as $line_num => $line)
	    {
		list($_id_wh, $art_nr, $_id_client, $zakaz_date, $zakaz_nr, $zakaz_pos, $zakaz_count, $zakaz_flag) = explode(";;", $line);
		$data[$line_num] = array($_id_wh, $art_nr, $_id_client, $zakaz_date, $zakaz_nr, $zakaz_pos, $zakaz_count, $zakaz_flag);
	    }
	}
	else
	    return FALSE;

	foreach ($data as $key => $value)
	{
	    $sql_ins[] = join("','", $data[$key]);
	    array_pop($data[$key]);
	    array_shift($data[$key]);
	}

	$multi_v = "('" . join("'),('", $sql_ins) . "')";

	if (empty($arr_field))
	    return FALSE;

	$multi_i = "(`" . join("`, `", $arr_field) . "`)";
	$sql = "INSERT INTO `_table_zakaz` ".$multi_i." VALUES " . $multi_v . ";";
	$this->db->query($sql);

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

    public function get_warehouse_list()
    {
	$this->db->select('_warehouse._id_wh as id_warehouse, _warehouse.name_wh as name');
	$this->db->from('_warehouse');
	$this->db->order_by('_warehouse.rid_wh');
	$query = $this->db->get();
	return $query->num_rows() ? $query->result() : array();
    }

    public function get_groupsystem_list()
    {
	$this->db->select('_groupsystem._id_gs as id_groupsystem, _groupsystem.name_gs as name');
	$this->db->from('_groupsystem');
	$this->db->order_by('_groupsystem.rid_gs');
	$query = $this->db->get();
	return $query->num_rows() ? $query->result() : array();
    }

    public function get_client_list()
    {
	$this->db->select('_client._id_client as id_client, _client.name_cl as name');
	$this->db->from('_client');
	$this->db->order_by('_client.rid_cl');
	$query = $this->db->get();
	return $query->num_rows() ? $query->result() : array();
    }

    public function get_art_list()
    {
	$this->db->select('_art_nr._id_art as id_art, _art_nr.name_art as name');
	$this->db->from('_art_nr');
	$this->db->order_by('_art_nr.rid_art');
	$query = $this->db->get();
	return $query->num_rows() ? $query->result() : array();
    }

}

?>