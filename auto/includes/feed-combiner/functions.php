<?php
	function array_clean($input)
	{
	  foreach($input as $key => $value) 
	  {
		  if(trim($value) =="") 
		  {
			unset($input[$key]);
		  }
	  }
	  return $input;
	}
	
	function clean_cdata($input)
	{
	  if (strstr($input, "<![CDATA["))
	  {
		$start = "<![CDATA[";
		$end  = "]]>";
		$cleaned = get_string_between($input, $start, $end);
		$input = str_replace ("$start$cleaned$end",$cleaned,$input);
	  }
	  return $input;
	}
		
	function str_replace_once($remove , $replace , $string)
	{
		//$remove = str_replace('/','\/',$remove);
		$return = preg_replace('/'.preg_quote($remove,'/').'/', $replace, $string, 1); 
		if (!$return)
		{
			echo "str_replace_once fail";
			echo "<br><br> Here is the string:<br><br>$string";  EXIT;
		}
		return $return;
	}  
	
	function replace_trash_characters($input)
	{
	   //echo $input; exit;
	   $input = str_replace('»', '\'',$input);
	   $input = str_replace('í­Â¢í?Â?í?Â', '\'',$input);
	   $input = str_replace('í¢Â?Â?', '\'',$input);
	   $input = str_replace('—', '-',$input);
	   $input = str_replace('&mdash;', '-',$input);   
	   $input = str_replace("’", "'",$input);
	   $input = str_replace('"', '"',$input);
	   $input = str_replace('"', '" ',$input);
	   $input = str_replace('&amp;', '&',$input);	
	   $input = str_replace('&amp;rsquo;', '',$input);
	   $input = str_replace('&amp;#x2019;', '',$input);
	   $input = str_replace('&#x2019;', '',$input);
	   $input = str_replace('&amp;amp;', '&amp;',$input);
	   $input = str_replace('&amp;#x2018;', '',$input);
	   $input = str_replace('&amp;#x201C;', '"',$input);
	   $input = str_replace('&amp;#x201D;', '"',$input);
	   $input = str_replace('&amp;#xE9;', '', $input);
	   $input = str_replace('&amp;quot;', '"',$input);
	   $input = str_replace("&#039;", "",$input);
	   $input = str_replace("?", "",$input);
	   $input = str_replace("Â’", "'",$input);
	   $input = str_replace('Â“', '"',$input);
	   $input = str_replace('Â”', '"',$input);
	   $input = str_replace('&#xFFFD;', '',$input);
	   $input = str_replace('&Acirc;', '',$input);
	   $input = str_replace('&Atilde;', '',$input);
	  
	   return $input;
	}
	
	function quick_curl($link)
	{
		$agents[] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; Media Center PC 5.0)";
		$agents[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
		$agents[] = "Opera/9.63 (Windows NT 6.0; U; ru) Presto/2.1.1";
		$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5?";
		$agents[] = "Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.18) Gecko/20081203 Firefox/2.0.0.18";
		$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16";
		$agents[] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1";
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $link);
		curl_setopt($ch, CURLOPT_HEADER, false);
		//echo ini_get('open_basedir');exit;
		//print_r(ini_get('safe_mode'));exit;
		if (!ini_get('open_basedir') && !ini_get('safe_mode'))
		{
				curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		}
	   // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, $agents[rand(0,(count($agents)-1))]);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	function get_string_between($string, $start, $end) 
	{
		if (strstr($start,'%wildcard%'))
		{
			$start = str_replace("%wildcard%", ".*?", preg_quote($start, "/"));
		}
		else
		{
			$start = preg_quote($start, "/");
		}
		if (strstr($end,'%wildcard%'))
		{
			$end = str_replace("%wildcard%", ".*?", preg_quote($end, "/"));
		}
		else
		{
			//echo $end;exit;
			$end = preg_quote($end, "/");
			//echo $end; exit;
		}
		$regex = "/$start(.*?)$end/si";
			//echo "<hr>";
			//echo $string; 
			//echo "<hr>";
			//echo $regex; 
			//echo "<hr>";
			//print_r($matches);
			//exit;
		
		if (preg_match($regex, $string, $matches))
			return $matches[1];
		else
			//echo "<hr>";
			//echo $string; 
			//echo "<hr>";
			//echo $regex; 
			//echo "<hr>";
			//print_r($matches);
			//exit;
			return false;
	}
	
	function discover_rss_parameters($string)
	{

		if (strstr($string, "<item"))
		{
		   $entry_start ="<item";
		}
		if (strstr($string, "<entry>"))
		{
		   $entry_start ="<entry>";
		}  		
		
		//scoop-out feed header information
		if ($entry_start)
		{
			$string = str_replace_once($entry_start, "***b***", $string);
			$parts = explode("***b***", $string);
			$string = $parts[1];
		}
		
		//determine what the title opener looks like		   
		if (strstr($string, "<title>"))
		{
			$title_start =  "<title>";
			$title_end =  "</title>";
		}
		if (strstr($string, '<title type="html"'))
		{
			$title_start =  '<title type="html">';
			$title_end =  "</title>";
		}		

		if (strstr($string, "<title type='text'>"))
		{
		 $title_start =  "<title type='text'>";
		 $title_end =  "</title>";
		}
		
		if (strstr($string, '<title type="text">'))
		{
		 $title_start =  '<title type="text">';
		 $title_end =  "</title>";
		}		

		if (strstr($string, "<description type='text'>"))
		{
			$description_start = "<description type='text'>";
			$description_end =  "</description>";
		}
		if (strstr($string, "<description>"))
		{
			$description_start =  "<description>";
			$description_end =  "</description>";
		}
		if (strstr($string, "<summary>"))
		{
			$description_start =  "<summary>";
			$description_end =  "</summary>";
		}
		if (strstr($string, "<content type='html'>"))
		{
			$description_start = "<content type='html'>";
			$description_end =  "</content>";
		}
		if (strstr($string, '<content type="html">'))
		{
			$description_start = '<content type="html">';
			$description_end =  "</content>";
		}
		if (strstr($string, '<content type="html">'))
		{
			$description_start = '<content type="html">';
			$description_end =  "</content>";
		}
		
		if (strstr($string, "<pubDate>"))
		{
			$publish_date_start = "<pubDate>";
			$publish_date_end = "</pubDate>";
		}    
		
		if (strstr($string, "<published>"))
		{
			$publish_date_start = "<published>";
			$publish_date_end = "</published>";
		}  
		
		//determine what the link opener looks like
		if (strstr($string, "<link>"))
		{
			$link_start = "<link>";
			$link_end = "</link>";
		}    
		if (strstr($string, "<link rel='alternate' type='text/html' href='"))
		{
			$link_start = "<link rel='alternate' type='text/html' href='";
			$link_end = "'";
		}
		if (strstr($string, '<feedburner:origLink>'))
		{
			$link_start = "<feedburner:origLink>";
			$link_end = "</feedburner:origLink>";
			$close =1;
		}
		
		if(strstr($string, '<link rel="alternate" type="text/html" href=')&&$close!=1)
		{
			$link_start =  '<link rel="alternate" type="text/html" href="';
			$link_end = '"';
		}			
		
		if(strstr($string, '<link rel="alternate" href=')&&$close!=1)
		{
			$link_start =  '<link rel="alternate" href="';
			$link_end = '"';
		}	
		
		if(strstr($string, '<generator uri="http://www.google.com/reader">Google Reader</generator>'))
		{
			$string =  str_replace('</summary>','</content>',$string);
			$title_start =  '<title type="html">';
			$title_end =  "</title>";
			$description_start = 'type="html">';
			$description_end =  "</content>";
			$link_start =  '<link rel="alternate" href="';
			$link_end = '"';
			$google_reader = 1;
			
			$string = preg_replace('/gr:annotation(.*?)\/gr:annotation/si','',$string);
			
			while (strstr($string, '<source'))
			{
				$remove = get_string_between($string, '<source','</source>');
				$string = str_replace('<source'.$remove.'</source>' , '' ,$string);
			}
		}
		else
		{
			$google_reader = 0;
		}
		
		return array( 'string'=>$string, 'title_start' => $title_start , 'title_end'=>$title_end , 'description_start' => $description_start, 'description_end' => $description_end, 'link_start' => $link_start , 'link_end' => $link_end , 'publish_date_start' => $publish_date_start,  'publish_date_end' => $publish_date_end,  'google_reader' => $google_reader);
	}
	
	
	function date_normalize( $date_str ) 
	{
		if (!$date_str)
		{
			$date_str = date('Y-m-d H:i:s');
		}
		//$dt=new DateTime("$date_str");
		//$dt = $dt->format('U');
		$dt = date ('Y-m-d H:i:s', strtotime ($date_str));
		//echo $dt;
		return $dt;

	}
	
	function shuffle_assoc($list) 
	{
	  if (!is_array($list)) return $list;

	  $keys = array_keys($list);
	  shuffle($keys);
	  $random = array();
	  foreach ($keys as $key)
		$random[$key] = $list[$key];

	  return $random;
	} 
	
	function array_sort($array, $on, $order=SORT_ASC)
	{
		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
				break;
				case SORT_DESC:
					arsort($sortable_array);
				break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}
	
	