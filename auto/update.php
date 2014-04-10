<?php
ini_set("memory_limit","1024M");
include_once('includes/build_version.php');
include_once('../wp-config.php');
include_once('includes/prepare_variables.php');
session_start();

$secret_key = $blogsense_api_secret_key;

if ($_GET['secret_key']!=$secret_key)
{
	include("functions/f_login.php");
	if(checkSession() == false)
	blogsense_redirect("login.php");
}

include_once('../wp-admin/includes/class-pclzip.php');

if (function_exists('switch_to_blog')) 
{
	
	$nuprefix = explode('_',$table_prefix);
	$nuprefix= $nuprefix[0]."_";
}
else
{
	$nuprefix = $table_prefix;
}
?>
<html>
<body>
Initiating Download. <br>
Do not close your browser until complete....<br><br>


<?php

//check for multisite
echo "Checking for multiple blogs (MultiSite). <br>";
$query = "SELECT * FROM ".$nuprefix."blogs";
$result = mysql_query($query);
if ($result!=NULL)
{
	while ($arr = mysql_fetch_array($result))
	{		
		$multisite_blog_id[] = $arr['blog_id'];			
	}
	$multisite='on';
}
else
{
	$multisite='off';
}

if ($multisite=='on')
{
	echo "Multiple blogs discover. <br>";
}

$query = "SELECT option_value FROM ".$table_prefix."options WHERE option_name='blogsense_activation' || option_name='blogsense_activation_key' || option_name='blogsense_activation_email'";
$result = mysql_query($query);
$count = mysql_num_rows($result);

if ($count>0)
{
	for ($i=0;$i<$count;$i++)
	{
		$array = mysql_fetch_array($result);
		if ($i==0){ $blogsense_activation = $array['option_value'];}
		if ($i==2){ $blogsense_activation_key = $array['option_value']; }
		if ($i==1){ $blogsense_activation_email = $array['option_value']; }
	}
}
else
{
	$query = "SELECT option_value FROM ".$table_prefix."blogsense WHERE option_name='blogsense_activation' || option_name='blogsense_activation_key' || option_name='blogsense_activation_email'";
	$result = mysql_query($query);
	$count = mysql_num_rows($result);
	
	for ($i=0;$i<$count;$i++)
	{
		$array = mysql_fetch_array($result);
		if ($i==0){ $blogsense_activation = $array['option_value'];}
		if ($i==2){ $blogsense_activation_key = $array['option_value']; }
		if ($i==1){ $blogsense_activation_email = $array['option_value']; }
	}
}

//prepare variables accoring to nature

	if ($_GET['special']==1)
	{
		$download_file = "http://www.hatnohat.com/api/blogsense/download.php?autoupdate=1";
	}
	else
	{
		$download_file = "http://www.hatnohat.com/api/blogsense/download.php?license=$blogsense_activation_key&license_email=$blogsense_activation_email";
	}
	$version_check = "http://www.hatnohat.com/api/blogsense/update.php?serve_public=1";


//get latest version number

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $version_check);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$build_version = curl_exec($ch);
curl_close($ch);


//build remove path
$remove_path = "BlogSense-WP $build_version";





function zip_extract($download_file, $store_path, $remove_path)
{
	//echo 1; exit;
	//echo $remove_path; exit;
	$archive = new PclZip($download_file);
	$list = $archive->delete(PCLZIP_OPT_BY_EREG, '/robots/');
	$list = $archive->extract(PCLZIP_OPT_REMOVE_PATH, $remove_path, PCLZIP_OPT_PATH, $store_path, PCLZIP_OPT_REPLACE_NEWER );
	if ($list == 0) 
	{
		//echo "death here"; exit;
		die("Error : ".$archive->errorInfo(true));

	}
	else
	{
		//print_r($list); exit;
		return 1;
		echo" hello";

	}

}



$temp_file = tempnam('/tmp','BLOGSENSEWP');
	
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$download_file);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
$file = curl_exec($ch);
curl_close($ch);
$fp = fopen($temp_file, 'w');
fwrite($fp, $file);
fclose($fp);

if (strstr($file,'License information invalid.'))
{
	echo $file;exit;
}
echo "extracting updated files...<br>";

$return = zip_extract($temp_file, "../", $remove_path);

unlink($temp_file);

if ($multisite=='on')
{
	$count = count($multisite_blog_id);
	echo "Preparing to update tables for $count blogs. <br>";
	foreach ($multisite_blog_id as $key=>$val)
	{
		$_COOKIE['bs_blog_id'] = $val;		
		switch_to_blog($_COOKIE['bs_blog_id']);
		$query = "SELECT * FROM `".$table_prefix."blogsense` WHERE option_name='blogsense_build_version'";
		$result = mysql_query($query);
		if ($result)
		{
			$count = mysql_num_rows($result);
			if ($count==1)
			{	
				include('update_sql.php');
			}	
		}
	}
}
else
{
	echo "running update_sql.php...<br>";
	$second_prefix = "";
	include('update_sql.php');
}





//sleep(3);

//header("

?>
</body>
</html>