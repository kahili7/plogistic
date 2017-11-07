<?if(!defined('BASEPATH')) exit('No direct script access allowed');

class New_Ktmpl_Extension extends Ktmpl_Extension
{
    public function getFilters()
    {
        $this->obj = &get_instance();

        return array(
            'json_decode' => new Ktmpl_Filter_Function('json_decode'),
            'json_encode' => new Ktmpl_Filter_Function('json_encode'),
            'php_date' => new Ktmpl_Filter_Function('date'),
            'header' => new Ktmpl_Filter_Function('header'),
	);
    }

    public function getName()
    {
	return 'extends';
    }
}