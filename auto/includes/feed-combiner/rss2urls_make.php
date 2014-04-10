<?php
	$max_items = $_GET['max_items'];
	$sort = $_GET['sort'];
	$format = $_GET['format'];
	$feed_limit= 12;

	$TMP_ROOT = "temp/"; //a atempory folder for storing the cached feeds, need to have write access (mod755)
	$DOMAIN_NAME = "http://www.blogsense-wp.com";
	$SITE_TITLE = "Randomized Feeds";
	$SITE_DESRIPTION = "Randomized Feeds";
	$SITE_AUTHOR = "BlogSense";

	//print_r($FEEDS_ARRAY);exit;
	
	$MAX_ITEMS = 99;
	$SHOW_FULL_FEED = TRUE;
	
	//stop editing from here onwards
	
	define('MAGPIE_DIR', '');
	define('MAGPIE_CACHE_DIR', $TMP_ROOT);
	
	//@require_once(MAGPIE_DIR.'rss_fetch.inc');
	@include(MAGPIE_DIR.'feedcreator.class.php');
	include_once('./functions.php');
	
	
	
	
	$FEEDS_ARRAY = array_clean($_GET['rss_feeds']);	
	//create the basic rss feed
	$rss = new UniversalFeedCreator();
	$rss->useCached();
	$rss->title = $SITE_TITLE;
	$rss->description = $SITE_DESRIPTION;
	$rss->link = $DOMAIN_NAME;
	$rss->syndicationURL = curPageURL();
	
	//get all items is all feeds
	$total_temp = 0; //temp total number of posts in all rss feeds
	foreach ($FEEDS_ARRAY as $COUNT=>$single_url) 
	{
		
		//echo $single_url; exit;
		$feed_links = array();
		$string = quick_curl($single_url);
		//$string = htmlspecialchars_decode($string);
		
		//echo $string; exit;
		//discover rss format
		$parameters = discover_rss_parameters($string);
		$string = $parameters['string'];
		$title_start = $parameters['title_start'];
		$title_end = $parameters['title_end'];
		$description_start = $parameters['description_start'];
		$description_end = $parameters['description_end'];
		$link_start = $parameters['link_start'];
		$link_end = $parameters['link_end'];
		$publish_date_start = $parameters['publish_date_start'];
		$publish_date_end = $parameters['publish_date_end'];
		
		//echo $string; exit;
		
	
		$link_count = @substr_count($string, $link_start);
		if ($feed_limit!=0&&($feed_limit<$link_count))
		{
			$link_count = $feed_limit;
		}
	
		for ($i=0;$i<$link_count;$i++)
		{
			
			$feed_links[$i] = get_string_between($string, $link_start, $link_end);
			//echo $links[$i]; exit;			  
			$string = str_replace("".$link_start."".$feed_links[$i]."".$link_end."", "", $string);
			$feed_links[$i] = clean_cdata($feed_links[$i]);
		}
	
		if (count($feed_links)==0)
		{
		 echo "Feeds produced no results. ";exit;
		}
		//print_r($feed_links);exit;
		foreach ($feed_links as $key=>$value)
		{
			//ECHO $COUNT;
			$title = "";
		
			
		
			//pull the title from rss
			$title = get_string_between($string, $title_start, $title_end);
			$string = str_replace_once("$title_start$title$title_end", "***t***", $string);
			//echo $title; exit;
			
			$title = str_replace('â?"','-',$title);
			$title = str_replace('â?"','-',$title);
			$title = str_replace('8211','',$title);
			$title = str_replace(' amp ',' ',$title);
			$title = str_replace('laquo','',$title);
			$title = $input = preg_replace('/[^a-z0-9 \'\']+/i', "", $title);
			
			//pull the description from rss						   
			$description = get_string_between($string, $description_start, $description_end);			   
			$string = str_replace_once("{$description_start}{$description}{$description_end}", " ***d***" , $string); 
			
			//pull publish date from rss					   
			$publish_date = get_string_between($string, $publish_date_start, $publish_date_end);			   
			$string = str_replace_once("{$publish_date_start}{$publish_date}{$publish_date_end}", " ***d***" , $string); 
			
			//echo $publish_date;
			//echo "<br>"; exit;
			$publish_date =  date_normalize($publish_date);
			//$publish_date =  parse_w3cdtf($publish_date);
			
			//echo $publish_date; exit;
			//echo $description_start; exit;
			if ($COUNT==2)
			{
				//echo $string; exit;
				//echo $description; EXIT;
			}
		
			//echo $string;exit;
			$title = clean_cdata($title);
			$title = htmlspecialchars_decode($title);						
			$description = htmlspecialchars_decode($description);						
			$title = strip_tags($title);
			$title = replace_trash_characters($title);
			$titles[] = trim($title);
			$descriptions[] = clean_cdata($description);
			$publish_dates[] = $publish_date;
			$links[] = $value;
		}
	}
	
	if ($sort=='bydate_desc')
	{
		foreach ($titles as $k=>$v)
		{
			$items[] = array('title'=>$titles[$k],'description'=>$descriptions[$k],'link'=>$links[$k],'publish_date'=>$publish_dates[$k]);
		}
		$items = array_sort($items,'publish_date',SORT_DESC);
		//print_r($items);
	}
	else if ($sort=='bydate_asc')
	{
		foreach ($titles as $k=>$v)
		{
			$items[] = array('title'=>$titles[$k],'description'=>$descriptions[$k],'link'=>$links[$k],'publish_date'=>$publish_dates[$k]);
		}
		$items = array_sort($items,'publish_date',SORT_ASC);
	}
	else
	{
		foreach ($titles as $k=>$v)
		{
			$items[] = array('title'=>$titles[$k],'description'=>$descriptions[$k],'link'=>$links[$k],'publish_date'=>$publish_dates[$k]);
		}
		$items = shuffle_assoc($items,'publish_date',SORT_ASC);
	}
	//print_r($publish_dates);exit;
	//print_r($links);exit;
	echo "<pre>";
	foreach ($items as $key=>$val)
	{
		if ($key<$max_items)
		{
			
			if ($format=='2')
			{
				echo $items[$key]['link'].";".$items[$key]['title'];
				echo "<br>";
			}
			else if ($format=='3')
			{
				echo $items[$key]['link']."{".$items[$key]['title']."}";
				echo "<br>";
			}
			else if ($format=='4')
			{
				$tags = prepare_tags($items[$key]['title']);
				echo $items[$key]['link'].";$tags";
				echo "<br>";
			}
			else if ($format=='5')
			{
				$tags = prepare_tags($items[$key]['title']);
				$tags = str_replace(',','|',$tags);
				echo $items[$key]['link']."{".$tags."}";
				echo "<br>";
			}
			else
			{
				echo $items[$key]['link'];
				echo "<br>";
			}
			
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
		if ($data === False && $proxy) 
		{
			echo "<b>FAILED PROXY: $proxy:$proxy_port</b>";
		}
		else if($data === False)
		{
			sleep(1);
			//$data = curl_exec($ch);
		}
		
		if ($data === False)
		{
			echo "<b>CURL CONNECTION FAILED: connection with $url was terminated <br>";
		}
		//echo $data; exit;
		curl_close($ch);
		return $data;
	}
	
?>