<?php
include_once('./../../wp-config.php');
if (!isset($_SESSION)) { session_start();}
include("./../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../login.php");

if (function_exists('switch_to_blog')) switch_to_blog($_SESSION['blog_id']);

require("prepare_variables.php");
require("twitteroauth/twitteroauth.php");
//echo $blogsense_url;exit;

$redirect_url = $blogsense_url."/includes/twitteroauth/oauth_store_account_data.php";
//echo $twitter_oauth_consumer_secret; exit;
// The TwitterOAuth instance
$oauth = new TwitterOAuth($twitter_oauth_consumer_key, $twitter_oauth_consumer_secret);
//print_r($oauth);
// Requesting authentication tokens, the parameter is the URL we will be redirected to
$requestToken = $oauth->getRequestToken($redirect_url);
//print_r($requestToken);

$_SESSION['requestToken'] = $requestToken['oauth_token'];
$_SESSION['requestTokenSecret'] = $requestToken['oauth_token_secret'];


// display Twitter generated registration URL
$registerURL = $oauth->getAuthorizeURL($requestToken);
echo '<br><br><ol><li>Please sign into the twitter account you would like to add.</a></li>';
echo '<li><a href="' . $registerURL . '"  target=_blank>Click here to provide BlogSense access to your Twitter account</a></ul></li></ol></br>';


?>