<?php
session_start();
include_once("./../../wp-config.php");
$bid = $_POST['blog_id'];
if (function_exists('switch_to_blog')) switch_to_blog($bid);


//set new session
setcookie('bs_blog_id',$bid;,0,"/");

//get site url
$query = "SELECT * FROM ".$table_prefix."options WHERE option_name='siteurl'";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error(); exit;}
$arr = mysql_fetch_array($result);


//set new cronjob script
setcookie('bs_site_url',$arr['option_value'];,0,"/");
setcookie('bs_cronjob_script',"heartbeat.php?blog_id=$blog_id",0,"/");


echo $bid."<br>";
echo $table_prefix."<br>-table_prefix";
echo $_COOKIE['bs_blog_id'];
echo $_COOKIE['bs_site_url'];

?>