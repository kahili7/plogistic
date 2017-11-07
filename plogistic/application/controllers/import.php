<?

include_once APPPATH . "libraries/core/crmcontroller.php";

class Import extends Crmcontroller
{

    public function __construct()
    {
	parent::__construct();
	$this->config->load('uploading_script');
	$this->load->model('import_model');
    }

    public function _remap($m_Name)
    {
	switch ($m_Name)
	{
	    case 'upload': $this->upload();
		break;
	    case 'load': $this->load();
		break;
	    case 'db': $this->db();
		break;
	    default: $this->loading();
	}
    }

    public function loading()
    {
	$data['content'] = $this->load->view('import/import_view', TRUE, TRUE);
	return $this->load->view('layouts/upload_layout', $data);
    }

    public function upload()
    {
	$this->config->load('uploading_script');
	$upload_dir = $this->config->item('upload_dir');
	$primary_dir = $this->input->post('directory');

	$config['upload_path'] = './' . $upload_dir . '/' . $primary_dir . '/';
	$config['allowed_types'] = $this->config->item('acceptable_files');
	$config['max_size'] = $this->config->item('max_kb');
	$config['max_width'] = $this->config->item('max_width');
	$config['max_height'] = $this->config->item('max_height');

	$this->load->library('upload', $config);

	if (!$this->upload->do_upload())
	{
	    $json['status'] = 'error';
	    $json['issue'] = $this->upload->display_errors('', '');
	}
	else
	{
	    $json['status'] = 'success';

	    foreach ($this->upload->data() as $k => $v)
	    {
		$json[$k] = $v;
	    }
	}

	echo json_encode($json);
    }

    public function load()
    {
	$dir = $this->uri->segment(3);
	$data['dir'] = $dir;
	$data['files'] = $this->_getFiles("./upload/" . $dir);
	$data['content'] = $this->load->view('import/import_load', $data, TRUE);
	return $this->load->view('layouts/load_layout', $data);
    }

    public function db()
    {
	$dir = $this->input->post('directory');
	$filename = $this->input->post('filename');
	$delete = $this->input->post('delete');
	$path = "./upload/" . $dir . "/" . $filename;

	switch ($dir)
	{
	    case 'warehouse': $this->loadInWAREHOUSE($path, $delete);
		break;

	    case 'groupsystem': $this->loadInGROUPSYSTEM($path, $delete);
		break;

	    case 'client': $this->loadInCLIENT($path, $delete);
		break;

	    case 'art_nr': $this->loadInART($path, $delete);
		break;

	    case 'element': $this->loadInELEMENT($path, $delete);
		break;
	}

	redirect(get_currcontroller(), 'refresh');
    }

    private function loadInWAREHOUSE($path, $delete=FALSE)
    {
	if ($delete)
	    $this->import_model->erase_db('_warehouse');

	$this->import_model->load_insert($path, '_warehouse', array('_id_wh', 'name_wh', 'descr_wh'));
    }

    private function loadInGROUPSYSTEM($path, $delete=FALSE)
    {
	if ($delete)
	    $this->import_model->erase_db('_groupsystem');

	$this->import_model->insert_t_gs($path, '_groupsystem', array('_id_gs', 'name_gs', 'descr_gs'));
    }

    private function loadInCLIENT($path, $delete=FALSE)
    {
	if ($delete)
	    $this->import_model->erase_db('_client');

	$this->import_model->load_insert($path, '_client', array('_id_client', 'name_cl', 'descr_cl'));
    }

    private function loadInART($path, $delete=FALSE)
    {
	if ($delete)
	    $this->import_model->erase_db('_art_nr');

	$this->import_model->load_insert($path, '_art_nr', array('_id_art', 'name_art', 'descr_art'));
    }

    private function loadInELEMENT($path, $delete=FALSE)
    {
	if ($delete)
	{
	    $this->import_model->erase_db('_table_all');
	    $this->import_model->erase_db('_table_zakaz');
	    $this->import_model->erase_db('_table_prixod');
	}

	$lines = file($path);
	$cnt = count($lines);

	$list_wh = $this->_getWarehouseList();
	$cnt_wh = count($list_wh);
	$list_gr = $this->_getGroupsystemList();
	$list_art = $this->_getArtList();
	$i = 0;

	$st_wh = 0;
	$st_gr = 0;
	$st_ar = 0;

	$t_all = array();
	$t_pr = array();
	$t_zk = array();
	$ut_all = array();

	$str_wh = "";

	foreach ($list_wh as $key => $val)
	{
	    if ($i == ($cnt_wh - 1))
	    {
		$str_wh .= $key;
		break;
	    }

	    $str_wh .= $key . "|";
	    $i++;
	}
	
	$str_wh = explode("|", $str_wh);
	$scnt = count($str_wh);
	$res_wh = "";
	
	for ($i = 0; $i < $cnt; $i++)
	{
		for ($j = 0; $j < $scnt; $j++)
		{
			if (preg_match("/\s".$str_wh[$j]."/i", $lines[$i], $mt))
				$res_wh = $mt[0];
		}
		
	    if ($res_wh)
	    {
		$st_wh++;
		break;
	    }
		
	}

	if ($st_wh)
	{
	    foreach ($lines as $key => $line)
	    {
		$line = rtrim(preg_replace("/[\r\n]+/m", "\r\n", $line));
		
		if(empty($line))
		{
		    continue;
		}

		$st = $this->_getState($line, $res);

		switch ($st)
		{
		    case "1":
			$tmp_gr = $res;
			$st_gr = 1;
			$st_ar = 0;
			break;

		    case "2":
			if ($st_gr)
			{
			    $tmp_art = $res;

			    if (empty($list_art[$tmp_art]))
				$str_gr = "";
			    else
				$str_gr = $list_art[$tmp_art];


			    $count_in = $this->_searchInString(trim($line), "-?\d+[\.|\,]?\d*$");
			    $t_all[] = join(';;', array($res_wh, $tmp_gr, $tmp_art, rtrim($str_gr), '0', $count_in));
			    $st_ar = 1;
			}
			break;

		    case "3":
			if ($st_ar)
			{
			    $k = $key + 1;
			    $st1 = $this->_getState($lines[$k], $res);
			    $plus = $this->_searchInString($line, "[\+]");

			    if ($plus)
			    {
				list($date, $zero, $id_client, $in_nr, $in_pos, $in_flag, $tmp1, $tmp2, $postuplenie, $svobodno) = explode(" ", $line);
				$in_flag = "+";
			    }
			    else
			    {
				list($date, $zero, $id_client, $in_nr, $in_pos, $tmp1, $tmp2, $postuplenie, $svobodno) = explode(" ", $line);
				$in_flag = "-";
			    }

			    if ($in_flag === "+")
			    {
				$t_pr[] = join(';;', array($res_wh, $tmp_art, $id_client, $date, $in_nr, $in_pos, $postuplenie, $in_flag));
			    }
			    else
			    {
				$t_zk[] = join(';;', array($res_wh, $tmp_art, $id_client, $date, $in_nr, $in_pos, $postuplenie, $in_flag));
			    }

			    if ($st1 != 3 AND $st1 != 0)
			    {
				$ut_all[] = join(';;', array($tmp_art, $svobodno, $res_wh));
				break;
			    }

			    if($k >= count($lines))
			    {
				$ut_all[] = join(';;', array($tmp_art, $svobodno, $res_wh));
				break;
			    }
			}
			break;
		}
	    }

	    $this->import_model->insert_t_all($t_all, array('_id_wh', '_id_gs', 'art_nr', 'name', 'svobodno', 'vsevo_na_sklade'));
	    $this->import_model->insert_t_pr($t_pr, array('_id_wh', 'art_nr', '_id_client', 'prixod_date', 'prixod_nr', 'prixod_pos', 'prixod_count', 'prixod_flag'));
	    $this->import_model->insert_t_zk($t_zk, array('_id_wh', 'art_nr', '_id_client', 'zakaz_date', 'zakaz_nr', 'zakaz_pos', 'zakaz_count', 'zakaz_flag'));
	    $this->import_model->update_t_all($ut_all, array('art_nr', 'svobodno', '_id_wh'));
	}
    }

    private function _getState($str, &$res)
    {
	$res = NULL;

	if (($res = $this->_searchInString($str, "^(\d{2})\s(\d{2})")))
	    return 1;
	else if (($res = $this->_searchInString($str, "^(\d{6})")))
	    return 2;
	else if (($res = $this->_searchInString($str, "^(\d{2}).(\d{2}).(\d{4})")))
	    return 3;
	
	return 0;
    }

    private function _getWarehouseList()
    {
	$lists = $this->import_model->get_warehouse_list();

	if (!empty($lists))
	{
	    foreach ($lists as $key => $list)
	    {
		$res[$list->id_warehouse] = $list->name;
	    }

	    return $res;
	}

	return FALSE;
    }

    private function _getGroupsystemList()
    {
	$lists = $this->import_model->get_groupsystem_list();

	if (!empty($lists))
	{
	    foreach ($lists as $key => $list)
	    {
		$res[$list->id_groupsystem] = $list->name;
	    }

	    return $res;
	}

	return FALSE;
    }

    private function _getClientList()
    {
	$lists = $this->import_model->get_client_list();

	if (!empty($lists))
	{
	    foreach ($lists as $key => $list)
	    {
		$res[$list->id_client] = $list->name;
	    }

	    return $res;
	}

	return FALSE;
    }

    private function _getArtList()
    {
	$lists = $this->import_model->get_art_list();

	if (!empty($lists))
	{
	    foreach ($lists as $key => $list)
	    {
		$res[$list->id_art] = $list->name;
	    }

	    return $res;
	}

	return FALSE;
    }

    private function _searchInArray($key, $arr)
    {
	if (array_key_exists($key, $arr))
	    return $arr[$key];
	else
	{
	    foreach ($arr as $val)
	    {
		if (gettype($val) == 'array')
		{
		    $res = $this->_searchInArray($key, $val);

		    if ($res != NULL)
			return $res;
		}
	    }

	    return NULL;
	}
    }

    private function _searchInString($str, $regular)
    {
	if (preg_match("/".$regular."/i", $str, $mt))
	    return $mt[0];
	else
	    return FALSE;
    }

    private function _getFiles($dir)
    {
	$d = dir($dir);

	while (false !== ($entry = $d->read()))
	{
	    if (!preg_match('/^[.]/i', $entry))
	    {
		$dirs[$entry] = $entry;
	    }
	}

	$d->close();
	return $dirs;
    }

}

?>