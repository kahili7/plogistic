<?

if (!DEFINED('BASEPATH'))
    exit('No direct script access allowed');

class USER
{

    private $a_urid;  /* user rid */
    private $a_uprid;  /* user position rid */
    private $a_uerid;  /* user employeer rid */
    private $a_ufn;  /* user full name */
    private $a_upn;  /* user position name */
    private $a_ufr;  /* user filial rid */
    private $a_uem;  /* user employeers mail */
    private $ki; /* ki */

    public function USER()
    {
	$this->ki = & get_instance();
	$this->ki->load->model('user_model');
    }

    public function is_logged()
    {
	$controller = $this->ki->uri->segment(1, 'login');

	if (!$this->ki->session->userdata('URID') && $controller !== 'login')
	{
	    redirect('login', 'refresh');
	    return FALSE;
	}

	return TRUE;
    }

    public function init_user()
    {
	$this->a_urid = $this->ki->session->userdata('URID');
	$this->a_uerid = $this->ki->user_model->get_uerid($this->a_urid);
	$this->a_uprid = $this->ki->user_model->get_uprid($this->a_urid);
	$this->a_ufrid = $this->ki->user_model->get_ufrid($this->a_urid);
	$this->a_ufn = $this->ki->user_model->get_fn($this->a_urid);
	$this->a_upn = $this->ki->user_model->get_pn($this->a_urid);
	$this->a_uem = $this->ki->user_model->get_uem($this->a_urid);
	return TRUE;
    }

    public function get_ufn()
    {
	return $this->a_ufn;
    }

    public function get_upn()
    {
	return $this->a_upn;
    }

    public function get_ufrid()
    {
	return $this->a_ufrid;
    }

    public function get_urid()
    {
	return $this->a_urid;
    }

    public function get_uerid()
    {
	return $this->a_uerid;
    }

    public function get_uprid()
    {
	return $this->a_uprid;
    }

    public function get_uem()
    {
	return $this->a_uem;
    }

}

?>