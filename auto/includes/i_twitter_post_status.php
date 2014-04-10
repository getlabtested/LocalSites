<?php
include_once('./../../wp-config.php');
if (!isset($_SESSION)) { session_start();}
require("prepare_variables.php");
require("twitteroauth/twitteroauth.php");

$tweet = $_POST['tweet'];
$oauth_secret = $_POST['oauth_secret'];
$oauth_token = $_POST['oauth_token'];

$connection = new TwitterOAuth($twitter_oauth_consumer_key, $twitter_oauth_consumer_secret, $oauth_token, $oauth_secret);
//print_r($connection);

$content = $connection->get('account/verify_credentials');
//print_r($content);
$connection->post("statuses/update", array("status" => $tweet));

echo "Tweeted to : $oauth_secret : $oauth_secret : $tweet";

?>