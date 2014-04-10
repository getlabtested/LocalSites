<?php
session_start();
include_once('./../../../wp-config.php');
include_once('./../../../wp-includes/link-template.php');
//check for multisite
if (strstr($table_prefix,'_1'))
{
	$table_prefix = str_replace('_1', '', $table_prefix);
	$fpx = 1;
}

function google_search_api($args, $endpoint = 'web'){

	$referer = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
	$url = "http://ajax.googleapis.com/ajax/services/search/".$endpoint;

	if ( !array_key_exists('v', $args) )	$args['v'] = '1.0';

	$url .= '?'.http_build_query($args, '', '&');
	//echo $url;	

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $referer);
	$string = curl_exec($ch);
	curl_close($ch);

	if (strstr($string, 'GwebSearch'))
	{
	  return 1;
	}
	else
	{
	  return 2;
	}
}

$indexes = $_GET['indexes'];
if (function_exists('switch_to_blog')) switch_to_blog($_SESSION['blog_id']);

//echo count($indexes); exit;
foreach ($indexes as $k=>$v)
{
	//echo $v;
	$query = "SELECT * FROM ".$table_prefix."posts_to_bookmark WHERE id=$v";
	$result = mysql_query($query);
	if (!$result){ echo $query;echo mysql_error(); }
	//echo mysql_num_rows($result);
	
	$arr = mysql_fetch_array($result);

	$post_id = $arr['post_id'];	
	$permalink = get_permalink($post_id);
	
	//echo $post_id;
	//echo $permalink;
	if (!$permalink){ $permalink = 'http://www.blogsense.com/john.html'; }
	$keyphrase = "info:$permalink;";
	$status = google_search_api(array(
					"q" => "$keyphrase",
				  ));
	
	if ($permalink=='http://www.blogsense.com/john.html')
	{
		$permalink='not found';
	}
	
	if ($permalink)
	{
		$query = "UPDATE ".$table_prefix."posts_to_bookmark SET permalink='$permalink' WHERE id=$v";
		$result = mysql_query($query);
	}
	
	if ($status==1)
	{
		$query = "UPDATE ".$table_prefix."posts_to_bookmark SET index_status='1' WHERE id=$v";
		$result = mysql_query($query);
	}
	else
	{
		$query = "UPDATE ".$table_prefix."posts_to_bookmark SET index_status='0' WHERE id=$v";
		$result = mysql_query($query);
	}
}

header('Location: ./../../functions/f_bookmarking_report.php');
echo "";
exit;
?>