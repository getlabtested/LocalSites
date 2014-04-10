<?php
include_once('../../wp-config.php');
session_start();

//check for multisite
//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}


$type = $_GET['type'];
$id = $_GET['id'];

if ($type=="campaign")
{
  $query = "UPDATE ".$table_prefix."campaigns SET schedule_post_date='0000-00-00 00:00:00' , schedule_post_count='0' WHERE id='$id'";
  $result = mysql_query($query);
  if (!$result){ echo $query; exit; }
  
  echo "<br><br><font style='color:green'><center>Campaign Post Date Reset</center></font>";
  exit;
}
else
{
  $query = "UPDATE ".$table_prefix."autoblog SET last_scheduled='0000-00-00 00:00:00' WHERE id='$id'";
  $result = mysql_query($query);
  if (!$result){ echo $query; echo mysql_error(); exit; }
  
  header("Location: ../index.php?p=3&reset=y");
}