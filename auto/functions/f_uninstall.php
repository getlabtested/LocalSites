<?php
include_once('../../wp-config.php');
session_start();
include_once('./../includes/build_version.php');
include("./../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../login.php");

//check multisite
//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}


$query = "DELETE FROM ".$table_prefix."options WHERE option_name LIKE 'blogsense_%'";
$result = mysql_query($query);
if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator:error 1."; echo mysql_error(); echo $query;}

$query = "DROP table ".$table_prefix."autoblog";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}

$query = "DROP table ".$table_prefix."post_templates";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}

$query = "DROP table ".$table_prefix."seoprofiles";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}

$query = "DROP table ".$table_prefix."sourcedata";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}

$query = "DROP table ".$table_prefix."blocked_urls";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}

$query = "DROP table ".$table_prefix."campaigns";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}

$query = "DROP table ".$table_prefix."posts_to_bookmark";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}

$query = "DROP table ".$table_prefix."post_templates";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}

$query = "DROP table ".$table_prefix."contentblocks";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}

$query = "DROP table ".$table_prefix."blogsense";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}

$query = "DROP table ".$table_prefix."custom_tokens";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}


$query = "DROP table ".$table_prefix."blogsense_remote_published_urls";
$result = mysql_query($query);
//if (!$result){ echo "Blogsense could not be uninstalled, this is a bug, contact adminstrator: error 2."; echo mysql_error();}


header("Location: ../index.php");
?>