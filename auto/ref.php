<?php
include_once('../wp-config.php');

$nuprefix = explode("_" , $table_prefix);
$nuprefix = $nuprefix[0]."_";


function url_exists($url) {
    // Version 4.x supported
    $handle   = curl_init($url);
    if (false === $handle)
    {
        return false;
    }
    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
    curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // request as if Firefox   
    curl_setopt($handle, CURLOPT_NOBODY, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
    $connectable = curl_exec($handle);
    curl_close($handle);  
    return $connectable;
}

$query = "SELECT * FROM ".$nuprefix."options WHERE option_name='blogsense_cloaking_redirect'";
$result = mysql_query($query);
while ($arr = mysql_fetch_array($result))
{
	$mode = $arr['option_value'];
	
}

$ref = $_GET['ref'];
$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
$current_url = str_replace('auto/ref.php','',$current_url);

//check if user is using a 'most common browser'
$known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape','konqueror', 'gecko','chrome','songbird','seamonkey','flock');
$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
foreach ($known as $k=>$v)
{
	if(strstr($useragent, $v)||$mode='none')
	{
		
		$query = "SELECT * FROM ".$nuprefix."cloaking WHERE ref='$ref'";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error();}

		while ($arr = mysql_fetch_array($result))
		{
		  $url = $arr['url'];
		}
		
		//echo $url; exit;
		if (!strstr($url,'http://'))
		{		
		$url = $current_url;
		}
		header("HTTP/1.1 303 See Other");
		header("Location: $url");
		exit;
	}
	
}

if ($mode='random')
{
	$query = "SELECT guid FROM ".$table_prefix."posts WHERE post_status='publish' AND post_type='post'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); exit; }
	
	$count = mysql_num_rows($result);
	
	if ($count >0)
	{
		while ($arr = mysql_fetch_array($result))
		{
			$list[] = $arr['guid'];			
		}
		$rand = array_rand($list);
		$url = $list[$rand];
	}
	else
	{
		$url = $current_url;
	}
}
else
{
	$url = $current_url;
}

header("HTTP/1.1 303 See Other");
header("Location: $url");
exit;
?>