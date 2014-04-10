<?php
	$max_items = $_GET['max_items'];
	$sort = $_GET['sort'];
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
			$description = "";
			$youtube_vids = "";
		
			//pull the title from rss
			$title = get_string_between($string, $title_start, $title_end);
			$string = str_replace_once("$title_start$title$title_end", "***t***", $string);
			//echo $title; exit;
			
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
	foreach ($items as $key=>$val)
	{
		if ($key<$max_items)
		{
			//create the new item
			$item_new = new FeedItem(); 
		
			//add all the copied basics
			$item_new->title = "{$items[$key]['title']}";
			$item_new->link = "{$items[$key]['link']}";
			$item_new->description = "{$items[$key]['description']}";
			$item_new->date = "{$items[$key]['publish_date']}";
			//$item_new->author = $item['author'];
			//$item_new->source = $temp_url;
			//$item_new->description = $temp_url;
			$rss->addItem($item_new);
			//echo $publish_dates[$key];
		}
	}
	
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
	
	// get your news items from other feed and display back
	$rss->saveFeed("RSS2.0", $TMP_ROOT."feed.xml");
?>