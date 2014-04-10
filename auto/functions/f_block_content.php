<html>
<head>
<title>Message</title>
<?php
include_once('./../../wp-config.php');
$campaign_id = $_GET['id'];



//decode variable
$link =  $_GET['link'];
$link = urldecode($link);
$link = str_replace("****","http://",$link);
$link = str_replace(' ','%20',$link);
//$link = str_replace('&amp;','&',$link);


	
	$query = "INSERT INTO ".$table_prefix."blocked_urls (`id`,`campaign_id`,`url`) VALUES ('','$campaign_id','$link')"; 
	$result = mysql_query($query);
	//if (!$result){echo $query; exit;}
	
	echo "<br><br><br><center><font color=green><i>Article Blocked.</i></font></center>";
?>