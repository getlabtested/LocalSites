<?php
include_once('../../wp-config.php');
session_start();
include_once('../includes/build_version.php');
$license_key = $_POST['license'];
$license_email = $_POST['license_email'];


//check for multisite
//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}

$nuprefix = explode("_" , $table_prefix);
$nuprefix = $nuprefix[0]."_";


//****************CHECK REQUIRED ****************//

if (!$license_key||!$license_email)
{
	header("Location: ../index.php?r=all");
	exit;
}

//****************VALIDATE SOFTWARE*************//
$ch = curl_init();
$wordpress_url = get_bloginfo('url');
$query = "http://www.hatnohat.com/api/blogsense/validate.php?key=$license_key&email=$license_email";
curl_setopt($ch, CURLOPT_URL, $query);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array('url' => $wordpress_url));	
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close ($ch);		
	
if ($output!=1)
{
	//In case of a major piracy outbreak (widespread piracy outside of TV) the developer holds right to booby trap all tampered installs to the loss of the unauthorized users. 
	//For this reason please place this disclaimer whereever blogsense is shared illegally and ask them not to share in high-traffic-public communities or they may ruin it for everyone and injure their business model. 
	//BlogSense users running untampered versions of BlogSense experience 0 risk of this possibility. 

	echo $query;
	echo $output;exit;
    header("Location: ../index.php?a=no");
	exit;
}
else
{
	$blogsense_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
	$blogsense_url = explode("functions/f_activate_blogsense.php",$blogsense_url); 
	$blogsense_url = $blogsense_url[0];
	include('f_install_blogsense.php');
}
?>
