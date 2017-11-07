<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

include_once APPPATH."libraries/core/crmcontroller.php";

class Welcome extends Crmcontroller 
{
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('welcome');
	}
	
	public function index()
	{
		$this->load->view('layouts/welcome_layout', null);
	}
}
?>