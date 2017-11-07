<?
if(!DEFINED('BASEPATH')) exit('No direct script access allowed');

class KI_PROFILER
{
	public $KI;
 	
 	function KI_PROFILER()
 	{
 		$this->KI =& get_instance();
 		$this->KI->load->language('profiler');
 	}
 	
 	function _compile_benchmarks()
 	{
  		$profile = array();
  		
 		foreach($this->KI->benchmark->marker as $key => $val)
 		{
 			if(preg_match("/(.+?)_end/i", $key, $match))
 			{ 			
 				if(isset($this->KI->benchmark->marker[$match[1].'_end']) AND isset($this->KI->benchmark->marker[$match[1].'_start']))
 				{
 					$profile[$match[1]] = $this->KI->benchmark->elapsed_time($match[1].'_start', $key);
 				}
 			}
 		}
	
		$output  = "\n\n";
		$output .= '<fieldset style="border:1px solid #990000;padding:6px 10px 10px 10px;margin:0 0 20px 0;background-color:#eee">';
		$output .= "\n";
		$output .= '<legend style="color:#990000;">&nbsp;&nbsp;'.$this->KI->lang->line('profiler_benchmarks').'&nbsp;&nbsp;</legend>';
		$output .= "\n";			
		$output .= "\n\n<table cellpadding='4' cellspaKIng='1' border='0' width='100%'>\n";
		
		foreach($profile as $key => $val)
		{
			$key = ucwords(str_replace(array('_', '-'), ' ', $key));
			$output .= "<tr><td width='50%' style='color:#000;font-weight:bold;background-color:#ddd;'>".$key."&nbsp;&nbsp;</td><td width='50%' style='color:#990000;font-weight:normal;background-color:#ddd;'>".$val."</td></tr>\n";
		}
		
		$output .= "</table>\n";
		$output .= "</fieldset>";
 		return $output;
 	}
 	
	function _compile_queries()
	{
		$dbs = array();
		
		foreach(get_object_vars($this->KI) as $KI_object)
		{
			if(is_subclass_of(get_class($KI_object), 'KI_DB'))
			{
				$dbs[] = $KI_object;
			}
		}
					
		if(count($dbs) == 0)
		{
			$output  = "\n\n";
			$output .= '<fieldset style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
			$output .= "\n";
			$output .= '<legend style="color:#0000FF;">&nbsp;&nbsp;'.$this->KI->lang->line('profiler_queries').'&nbsp;&nbsp;</legend>';
			$output .= "\n";		
			$output .= "\n\n<table cellpadding='4' cellspaKIng='1' border='0' width='100%'>\n";
			$output .="<tr><td width='100%' style='color:#0000FF;font-weight:normal;background-color:#eee;'>".$this->KI->lang->line('profiler_no_db')."</td></tr>\n";
			$output .= "</table>\n";
			$output .= "</fieldset>";
			
			return $output;
		}
		
		$this->KI->load->helper('text');
		$highlight = array('SELECT', 'DISTINCT', 'FROM', 'WHERE', 'AND', 'LEFT&nbsp;JOIN', 'ORDER&nbsp;BY', 'GROUP&nbsp;BY', 'LIMIT', 'INSERT', 'INTO', 'VALUES', 'UPDATE', 'OR', 'HAVING', 'OFFSET', 'NOT&nbsp;IN', 'IN', 'LIKE', 'NOT&nbsp;LIKE', 'COUNT', 'MAX', 'MIN', 'ON', 'AS', 'AVG', 'SUM', '(', ')');
		$output  = "\n\n";
			
		foreach($dbs as $db)
		{
			$output .= '<fieldset style="border:1px solid #0000FF;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
			$output .= "\n";
			$output .= '<legend style="color:#0000FF;">&nbsp;&nbsp;'.$this->KI->lang->line('profiler_database').':&nbsp; '.$db->database.'&nbsp;&nbsp;&nbsp;'.$this->KI->lang->line('profiler_queries').': '.count($this->KI->db->queries).'&nbsp;&nbsp;&nbsp;</legend>';
			$output .= "\n";		
			$output .= "\n\n<table cellpadding='4' cellspaKIng='1' border='0' width='100%'>\n";
		
			if(count($db->queries) == 0)
			{
				$output .= "<tr><td width='100%' style='color:#0000FF;font-weight:normal;background-color:#eee;'>".$this->KI->lang->line('profiler_no_queries')."</td></tr>\n";
			}
			else
			{				
				foreach($db->queries as $key => $val)
				{					
					$time = number_format($db->query_times[$key], 4);
					$val = highlight_code($val, ENT_QUOTES);
	
					foreach($highlight as $bold)
					{
						$val = str_replace($bold, '<strong>'.$bold.'</strong>', $val);	
					}
					
					$output .= "<tr><td width='1%' valign='top' style='color:#990000;font-weight:normal;background-color:#ddd;'>".$time."&nbsp;&nbsp;</td><td style='color:#000;font-weight:normal;background-color:#ddd;'>".$val."</td></tr>\n";
				}
			}
			
			$output .= "</table>\n";
			$output .= "</fieldset>";
		}
		
		return $output;
	}

	function _compile_get()
	{	
		$output  = "\n\n";
		$output .= '<fieldset style="border:1px solid #cd6e00;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
		$output .= "\n";
		$output .= '<legend style="color:#cd6e00;">&nbsp;&nbsp;'.$this->KI->lang->line('profiler_get_data').'&nbsp;&nbsp;</legend>';
		$output .= "\n";
				
		if(count($_GET) == 0)
		{
			$output .= "<div style='color:#cd6e00;font-weight:normal;padding:4px 0 4px 0'>".$this->KI->lang->line('profiler_no_get')."</div>";
		}
		else
		{
			$output .= "\n\n<table cellpadding='4' cellspaKIng='1' border='0' width='100%'>\n";
		
			foreach($_GET as $key => $val)
			{
				if(!is_numeric($key))
				{
					$key = "'".$key."'";
				}
			
				$output .= "<tr><td width='50%' style='color:#000;background-color:#ddd;'>&#36;_GET[".$key."]&nbsp;&nbsp; </td><td width='50%' style='color:#cd6e00;font-weight:normal;background-color:#ddd;'>";
				
				if(is_array($val))
				{
					$output .= "<pre>".htmlspecialchars(stripslashes(print_r($val, true)))."</pre>";
				}
				else
				{
					$output .= htmlspecialchars(stripslashes($val));
				}
				
				$output .= "</td></tr>\n";
			}
			
			$output .= "</table>\n";
		}
		
		$output .= "</fieldset>";
		return $output;	
	}
	
	function _compile_post()
	{	
		$output  = "\n\n";
		$output .= '<fieldset style="border:1px solid #009900;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
		$output .= "\n";
		$output .= '<legend style="color:#009900;">&nbsp;&nbsp;'.$this->KI->lang->line('profiler_post_data').'&nbsp;&nbsp;</legend>';
		$output .= "\n";
				
		if(count($_POST) == 0)
		{
			$output .= "<div style='color:#009900;font-weight:normal;padding:4px 0 4px 0'>".$this->KI->lang->line('profiler_no_post')."</div>";
		}
		else
		{
			$output .= "\n\n<table cellpadding='4' cellspaKIng='1' border='0' width='100%'>\n";
		
			foreach($_POST as $key => $val)
			{
				if(!is_numeric($key))
				{
					$key = "'".$key."'";
				}
			
				$output .= "<tr><td width='50%' style='color:#000;background-color:#ddd;'>&#36;_POST[".$key."]&nbsp;&nbsp; </td><td width='50%' style='color:#009900;font-weight:normal;background-color:#ddd;'>";
				
				if(is_array($val))
				{
					$output .= "<pre>".htmlspecialchars(stripslashes(print_r($val, true)))."</pre>";
				}
				else
				{
					$output .= htmlspecialchars(stripslashes($val));
				}
				
				$output .= "</td></tr>\n";
			}
			
			$output .= "</table>\n";
		}
		
		$output .= "</fieldset>";
		return $output;	
	}
	
	function _compile_uri_string()
	{	
		$output  = "\n\n";
		$output .= '<fieldset style="border:1px solid #000;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
		$output .= "\n";
		$output .= '<legend style="color:#000;">&nbsp;&nbsp;'.$this->KI->lang->line('profiler_uri_string').'&nbsp;&nbsp;</legend>';
		$output .= "\n";
		
		if($this->KI->uri->uri_string == '')
		{
			$output .= "<div style='color:#000;font-weight:normal;padding:4px 0 4px 0'>".$this->KI->lang->line('profiler_no_uri')."</div>";
		}
		else
		{
			$output .= "<div style='color:#000;font-weight:normal;padding:4px 0 4px 0'>".$this->KI->uri->uri_string."</div>";				
		}
		
		$output .= "</fieldset>";
		return $output;	
	}

	function _compile_controller_info()
	{	
		$output  = "\n\n";
		$output .= '<fieldset style="border:1px solid #995300;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
		$output .= "\n";
		$output .= '<legend style="color:#995300;">&nbsp;&nbsp;'.$this->KI->lang->line('profiler_controller_info').'&nbsp;&nbsp;</legend>';
		$output .= "\n";
		$output .= "<div style='color:#995300;font-weight:normal;padding:4px 0 4px 0'>".$this->KI->router->fetch_class()."/".$this->KI->router->fetch_method()."</div>";
		$output .= "</fieldset>";
		return $output;	
	}

	function _compile_memory_usage()
	{
		$output  = "\n\n";
		$output .= '<fieldset style="border:1px solid #5a0099;padding:6px 10px 10px 10px;margin:20px 0 20px 0;background-color:#eee">';
		$output .= "\n";
		$output .= '<legend style="color:#5a0099;">&nbsp;&nbsp;'.$this->KI->lang->line('profiler_memory_usage').'&nbsp;&nbsp;</legend>';
		$output .= "\n";
		
		if(function_exists('memory_get_usage') && ($usage = memory_get_usage()) != '')
		{
			$output .= "<div style='color:#5a0099;font-weight:normal;padding:4px 0 4px 0'>".number_format($usage).' bytes</div>';
		}
		else
		{
			$output .= "<div style='color:#5a0099;font-weight:normal;padding:4px 0 4px 0'>".$this->KI->lang->line('profiler_no_memory_usage')."</div>";				
		}
		
		$output .= "</fieldset>";
		return $output;
	}

	function run()
	{
		$output = "<div id='codeigniter_profiler' style='clear:both;background-color:#fff;padding:10px;'>";
		$output .= $this->_compile_uri_string();
		$output .= $this->_compile_controller_info();
		$output .= $this->_compile_memory_usage();
		$output .= $this->_compile_benchmarks();
		$output .= $this->_compile_get();
		$output .= $this->_compile_post();
		$output .= $this->_compile_queries();
		$output .= '</div>';
		return $output;
	}

}

// END KI_Profiler class

/* End of file Profiler.php */
/* Location: ./system/libraries/Profiler.php */