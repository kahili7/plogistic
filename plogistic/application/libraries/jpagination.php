<?if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

class JPAGINATION
{
	public $base_url			= ''; // The page we are linking to
	public $total_rows  		= ''; // Total number of items (database results)
	public $per_page	 		= 10; // Max number of items you want shown per page
	public $num_links			=  2; // Number of "digit" links to show before/after the currently viewed page
	public $cur_page	 		=  0; // The current page being viewed
	public $first_link   		= '&lsaquo; First';
	public $next_link			= '&gt;';
	public $prev_link			= '&lt;';
	public $last_link			= 'Last &rsaquo;';
	public $uri_segment		= 3;
	public $full_tag_open		= '';
	public $full_tag_close		= '';
	public $first_tag_open		= '';
	public $first_tag_close	= '&nbsp;';
	public $last_tag_open		= '&nbsp;';
	public $last_tag_close		= '';
	public $cur_tag_open		= '&nbsp;<b>';
	public $cur_tag_close		= '</b>';
	public $next_tag_open		= '&nbsp;';
	public $next_tag_close		= '&nbsp;';
	public $prev_tag_open		= '&nbsp;';
	public $prev_tag_close		= '';
	public $num_tag_open		= '&nbsp;';
	public $num_tag_close		= '';
	
	public $div = '';
	public $postpublic = '';
	public $js_rebind = '';
	
	function JPAGINATION($params = array())
	{
		if(count($params) > 0) $this->initialize($params);
		
		log_message('debug', "Pagination Class Initialized");
	}

	function initialize($params = array())
	{
		if(count($params) > 0)
		{
			foreach($params as $key => $val)
			{
				if(isset($this->$key))
				{
					$this->$key = $val;
				}
			}		
		}
	}
	
	function create_links()
	{
		if($this->total_rows == 0 OR $this->per_page == 0) return '';

		$num_pages = ceil($this->total_rows / $this->per_page);

		if($num_pages == 1) return '';

		$KI =& get_instance();	
		
		if($KI->uri->segment($this->uri_segment) != 0)
		{
			$this->cur_page = $KI->uri->segment($this->uri_segment);
			$this->cur_page = (int)$this->cur_page;
		}

		$this->num_links = (int)$this->num_links;
		
		if($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}
				
		if(!is_numeric($this->cur_page)) $this->cur_page = 0;
		
		if($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}
		
		$uri_page_number = $this->cur_page;
		$this->cur_page = floor(($this->cur_page / $this->per_page) + 1);

		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

		$this->base_url = rtrim($this->base_url, '/').'/';
		$output = '';

		if($this->cur_page > $this->num_links)
		{
			$output .= $this->first_tag_open.$this->getAJAXlink('', $this->first_link).$this->first_tag_close; 
		}

		if($this->cur_page != 1)
		{
			$i = $uri_page_number - $this->per_page;
			
			if($i == 0) $i = '';
			
			$output .= $this->prev_tag_open.$this->getAJAXlink($i, $this->prev_link).$this->prev_tag_close;
		}

		for($loop = $start -1; $loop <= $end; $loop++)
		{
			$i = ($loop * $this->per_page) - $this->per_page;
					
			if($i >= 0)
			{
				if($this->cur_page == $loop)
				{
					$output .= $this->cur_tag_open.$loop.$this->cur_tag_close;
				}
				else
				{
					$n = ($i == 0) ? '' : $i;
					$output .= $this->num_tag_open.$this->getAJAXlink($n, $loop).$this->num_tag_close;
				}
			}
		}

		if($this->cur_page < $num_pages)
		{
			$output .= $this->next_tag_open.$this->getAJAXlink($this->cur_page * $this->per_page, $this->next_link).$this->next_tag_close;
		}

		if(($this->cur_page + $this->num_links) < $num_pages)
		{
			$i = (($num_pages * $this->per_page) - $this->per_page);
			$output .= $this->last_tag_open.$this->getAJAXlink($i, $this>last_link).$this->last_tag_close;
		}

		$output = preg_replace("#([^:])//+#", "\\1/", $output);
		$output = $this->full_tag_open.$output.$this->full_tag_close;
		return $output;		
	}

	function getAJAXlink($count, $text)
	{
		return "<a href=\"#\" onclick=\"$.post('".$this->base_url.$count."', {'t' : 't'}, function(data){
		$('".$this->div."').attr('innerHTML',data);}); ".$this->js_rebind.";return false;\">".$text.'</a>';
	}
}
?>