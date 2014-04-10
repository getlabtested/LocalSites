<?php
	$max_items = "9999999999";
	$sort = $_POST['sort'];
	$format = $_POST['format'];
	$feed_limit= 12;
	$url_list = $_POST['url_list'];
	$url_list = explode("\n",$url_list);
	
	//print_r($url_list);exit;
	foreach ($url_list as $key=>$val) 
	{
		$val = trim($val);
		$string = stealth_curl($val,0);
		//echo $string;exit;
		//pull the title from rss
		$title = get_string_between($string, '<title>', '</title>');
		if ($title){ 		
			$title = $input = preg_replace('/[^a-z0-9 \'\']+/i', "", $title);
			$title = str_replace(array('8211','9531','128147','9411','9543','3863','128147','3881','9421','9152'),'',$title);
			$titles[] = trim($title);
			$links[] = $val;
		}

	}
	//print_r($titles);
	//echo "<br><br>";
	//print_r($links);exit;
	
	if ($sort=='random')
	{
		foreach ($titles as $k=>$v)
		{
			$items[] = array('title'=>$titles[$k],'description'=>$descriptions[$k],'link'=>$links[$k],'publish_date'=>$publish_dates[$k]);
		}
		$items = shuffle_assoc($items,'publish_date',SORT_ASC);
	}
	else
	{
		foreach ($titles as $k=>$v)
		{
			$items[] = array('title'=>$titles[$k],'link'=>$links[$k]);
		}
		
	}
	//print_r($publish_dates);exit;
	//print_r($items);exit;
	echo "<pre>";
	foreach ($items as $key=>$val)
	{
		
			
		if ($format=='5')
		{		
			$tags = prepare_tags($items[$key]['title']);
			$tags = str_replace(',','|',$tags);;
			echo $items[$key]['link'].";$tags";
			echo "<br>";
			unset($tags);
			
		}
		else if ($format=='4')
		{		
			$tags = prepare_tags($items[$key]['title']);
			echo $items[$key]['link'].";$tags";
			echo "<br>";
			unset($tags);
			
		}
		else if ($format=='3')
		{		
				echo $items[$key]['title']."<br>".$items[$key]['link']."<br><br>";
			
		}
		else if ($format=='2')
		{		
			echo $items[$key]['link']."{".$items[$key]['title']."}";
			echo "<br>";
			
		}
		else 
		{
			echo $items[$key]['link'].";".$items[$key]['title'];
			echo "<br>";
		}
		
		
	}
	echo "</pre>";
	
	// a quick function the grab a pages title
	function url_grab_title($rss_url) {
  		$contents = file_get_contents($rss_url, TRUE, NULL, 0, 3072);
  		$contents = preg_replace("/(\n|\r)/", '', $contents);
		preg_match('/<title>(.*?)<\/title>/i', $contents, $matches);
		return $matches[1];
	}
	
	//get page url (for syndication), source http://www.webcheatsheet.com/PHP/get_current_page_url.php
	function curPageURL() {
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	function specialsort($a,$b)
	{
		return strlen($b)-strlen($a);
	}

	function prepare_tags($string)
	{
		$url = "http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction";
		$paramaters = array('appid'=>'ZAlzNRjV34H56QbVJk7fRvu_yAP8bYHxG9Q77nNjaDsj9aelNCiTlo2bGiO_m2do1ic-', 'context'=>$string );
		$nature = 'yahoo_tags';
		$result = stealth_curl($url, 0 , $paramaters , $nature);
		//echo $result; exit;
		while (strstr($result,'<Result>'))
		{
			$tags[] = get_string_between($result, '<Result>','</Result>');
			$result = str_replace_once('<Result','',$result);
		}
		
		if ($tags)
		{
			$array = array_unique($tags);
			usort($array ,'specialsort');
			$array = array_filter($array);
			$array = array_slice($array,0,3);
			$tags = implode(',',$array);
		}
			
		if (!$tags)
		{
			$tags = $string;
		}
		return $tags;
	}
	
	function stealth_curl($url, $use_proxies, $paramaters=NULL, $nature=NULL, $val_1=NULL,$val_2=NULL)
	{	
		global $proxy_array;
		global $proxy_type;
		
		$agents[] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; Media Center PC 5.0)";
		$agents[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
		$agents[] = "Opera/9.63 (Windows NT 6.0; U; ru) Presto/2.1.1";
		$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5?";
		$agents[] = "Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.18) Gecko/20081203 Firefox/2.0.0.18";
		$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16";
		$agents[] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1";
		 
		 
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		
		if ($nature=='yahoo_tags')
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $paramaters);
		}

		curl_setopt($ch, CURLOPT_TIMEOUT ,10);
		curl_setopt($ch, CURLOPT_HEADER, false);
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, $agents[rand(0,(count($agents)-1))]);
		$data = curl_exec($ch);
		
		//echo $data; exit;
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
		
		$regex = "/{$start}(.*?){$end}/si";
		//echo $regex; 

		
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
	
?>