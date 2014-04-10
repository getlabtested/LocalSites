<?php
//$site = "ideamarketers.com/";
//$query = 'papaya fruit';
//$query = urlencode($query);

$site =  $_GET['s'];
//echo $site; exit;
$site = str_replace("open*","",$site);
$site = urlencode($site);
$query =  urlencode(stripslashes($_GET['q']));
$query = str_replace('%2B','+',$query);

if (strstr($site, 'news.yahoo.com'))
{	
	$n=40;
	$yahoo_url = "http://news.search.yahoo.com/search/news?ei=UTF-8&n=".$n."&p=";
	$yahoo_url .="+$query&fr2=tab-web&fr=404_news#r=&rid=refiner15";
	//echo $yahoo_url;exit;
}
else if (strstr($site,'buzzle.com'))
{
	$n=40;
	$yahoo_url = "http://search.yahoo.com/search?n=".$n."&p=";
	$yahoo_url .="site%3A$site+$query&fr2=sb-top&fr=siteexplorer&sao=1";
}
else
{
	$n=100;
	$yahoo_url = "http://search.yahoo.com/search?n=".$n."&p=";
	$yahoo_url .="site%3A$site+$query&fr2=sb-top&fr=siteexplorer&sao=1";
}
echo "<yahoo>$yahoo_url</yahoo>";
//echo 1;
//echo $yahoo_url; exit;

//helper functions///////////////////
function get_string_between($string, $start, $end)
{
     $string = " ".$string;
     $ini = strpos($string,$start);
     if ($ini == 0) return "";
     $ini += strlen($start);   
     $len = strpos($string,$end,$ini) - $ini;
     return substr($string,$ini,$len);
}
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
function str_replace_once($remove , $replace , $string)
{
	// Looks for the first occurence of $needle in $haystack
	// and replaces it with $replace.
	$pos = strpos($string, $remove);
	if ($pos === false) 
	{
	// Nothing found
	return $haystack;
	}
	return substr_replace($string, $replace, $pos, strlen($remove));
}  

////////////////////////////////////


//get search html into string
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$yahoo_url");
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
$string = curl_exec($ch);
curl_close($ch);
//$string = file_get_contents($yahoo_url);

//echo $string; exit;

if (strstr($string, 'Sorry, Unable to process request at this time -- error 999.'))
{
	echo "<br><br>yahoo throttling error. please wait awhile before trying again";
}

//pull result links into array
$links = array();
$link_count = substr_count($string, 'yschttl spt" href="');
//echo $link_count; exit;

$start = '"yschttl spt" href="';
$end = '"';

$n_start = '<a href="';
$n_end = '"';
for ($i=0; $i<$link_count;$i++)
{
   $links[$i] = get_string_between($string, $start, $end);
   //echo $links[$i]; exit;
   
   $remove = "$start$links[$i]$end";
   $string = str_replace_once($remove, "", $string);
   
   echo "<link>$links[$i]</link>";
}

?>

