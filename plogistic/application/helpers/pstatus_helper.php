<?if (!DEFINED('BASEPATH')) exit('No direct script access allowed');

function get_pstatus_list($flag=TRUE)
{
    $KI = & get_instance();
    $KI->load->model('pstatus_model');
    $list = $KI->pstatus_model->get_list();
    $res = array('' => $KI->config->item('crm_dropdown_empty'));

    if($flag)
    {
	foreach ($list as $c)
	{
	    $pos = strpos($c->name, '-');

	    if ($pos > 0)
		$res[$c->rid] = substr($c->name, 0, $pos);
	}
    }
    else
    {
	foreach ($list as $c)
	{
	    $res[$c->rid] = $c->name;
	}
    }

    return $res;
}

function get_pstatusname_byrid($rid)
{
    $KI = & get_instance();
    $KI->load->model('pstatus_model');
    return $KI->pstatus_model->get_pstatusname_byrid($rid);
}

?>