<?php
include_once('./../../wp-config.php');

$limit = $_GET['limit'];
$summary = $_GET['summary'];
$cid = $_GET['id'];

function getItem($title,$link,$description)
{
	$item='<item>';
	$item.="<title><![CDATA[$title]]></title>";
	$item.="<link><![CDATA[$link]]></link>";
	$item.="<description><![CDATA[$description]]></description>";
	$item.='<pubDate>'.date('r').'</pubDate>';
	$item.='</item>';
	return $item;
}

	$xml = '<?xml version="1.0" encoding="utf-8"?>';  
	$xml.='<rss version="2.0">';  
	$xml.='<channel> ';
	$xml.="<title>BlogSense Campaigns</title>";
	$xml.="<description>BlogSense Campaigns: Last $limit published items</description>"; 
	$xml.='<language>en-us</language>';
	//echo $titles[$a];exit;	

	$query = "SELECT * from ".$table_prefix."posts WHERE bs_campaign_id='$cid' AND post_status='publish' ORDER BY ID LIMIT $limit";
	$result = mysql_query($query);
	if (!$result){ echo $query; echo mysql_error();}
	
	//echo $item; exit;
	while ($array = mysql_fetch_array($result))
	{
		$pid = $array['ID'];
		$title = $array['post_title'];
		$description = strip_tags($array['post_content'], '<br><p><div><img><a>');
		$link = get_permalink($pid);
		
		if ($summary==1)
		{
			
			$description  = str_replace('<p>','',$description);
			$description  = str_replace('</p>','<br>',$description);
			$description = substr($description[$key], 0 ,600);
			$description = "$description[$key]...";	
		}

		$item = getItem($title,$link,$description);
		$xml.=$item;												
					
	}
  
  $xml.='</channel></rss>';
  
  
  echo $xml;
  
?>