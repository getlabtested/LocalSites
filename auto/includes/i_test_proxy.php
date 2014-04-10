<?php
include_once('../../wp-config.php');
include_once('prepare_variables.php');
include_once('helper_functions.php');

if ($_GET['go'])
{
	echo 1; 
	exit;
}
$blogsense_url = explode('includes',$blogsense_url);
$blogsense_url = $blogsense_url[0];
$url = $blogsense_url."includes/i_test_proxy_confirm.php";
//echo $url; 
//echo "<br>";
//print_r($proxy_array);
//echo "<br>";
//echo $proxy_type;
//echo "<br>";

$result =  stealth_curl($url, 1, null);

if ($result)
{
	echo "random proxy tested: ($result) success.";
}
else
{
	echo "Connection failed.<br>";
}

?>