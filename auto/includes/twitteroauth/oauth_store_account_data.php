<?php
include_once('./../../../wp-config.php');
if (!isset($_SESSION)) { session_start();}
require("./../prepare_variables.php");
require("twitteroauth.php");

if (function_exists('switch_to_blog')) switch_to_blog($_SESSION['blog_id']);

$oauth_token = $_GET['oauth_token'];
$oauth_secret = $_SESSION['requestTokenSecret'];
$oauth_verifier = $_GET['oauth_verifier'];

if (is_array($twitter_user))
{
	$multi=1;
}

// TwitterOAuth instance, with two new parameters we got in twitter_login.php
$twitteroauth = new TwitterOAuth($twitter_oauth_consumer_key,$twitter_oauth_consumer_secret, $oauth_token, $oauth_secret);
// Let's request the access token
$creds = $twitteroauth->getAccessToken($_GET['oauth_verifier']);

$oauth_token = $creds['oauth_token'];
$oauth_secret = $creds['oauth_token_secret'];
$new_twitter_user = $creds['screen_name'];



//print_r($twitter_user);
if ($multi==1)
{
	$twitter_user[] = $new_twitter_user;
	$twitter_oauth_secret[] = $oauth_secret;
	$twitter_oauth_token[] = $oauth_token;
	
	$twitter_user = array_filter($twitter_user);
	$twitter_oauth_secret = array_filter($twitter_oauth_secret);
	$twitter_oauth_token = array_filter($twitter_oauth_token);
	
	$twitter_user = implode(";", $twitter_user);
	$twitter_oauth_secret = implode(";", $twitter_oauth_secret);
	$twitter_oauth_token = implode(";", $twitter_oauth_token);
	
	
}
else
{
	$twitter_user = $new_twitter_user;
	$twitter_oauth_secret = $oauth_secret;
	$twitter_oauth_token = $oauth_token;
}

//echo "<br>";
//echo $twitter_user;exit;
$query = "UPDATE ".$table_prefix."blogsense SET option_value = '$twitter_user' WHERE option_name='blogsense_twitter_user'";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error();exit;}

$query = "UPDATE ".$table_prefix."blogsense SET option_value = '$twitter_oauth_secret' WHERE option_name='blogsense_twitter_oauth_secret'";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error();exit;}

$query = "UPDATE ".$table_prefix."blogsense SET option_value = '$twitter_oauth_token' WHERE option_name='blogsense_twitter_oauth_token'";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error();exit;}

echo "<br><br><br><font color=green>Twitter User Verified</font>";


?>