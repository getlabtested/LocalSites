<?php
//************************************************************************//
// BlogSense - WordPress Edition - Professional License                   //
// Website:  http://www.blogsense-wp                                      //
// Author: Hudson.Atwell@gmail.com ::: Email me if you have any questions //
//  Check the website for  new versions and addons!                       //
//************************************************************************//
//example of cron command:
//  you will run this from the appropriate section in your host's cpanel
//  if you have never ran a cron or the idea confuses you, ask your hosting provider for
//  the information on how to set one up with them. They will help you out.
//  ramdisk/bin/php5 -q /home/useracct/public_html/auto/cron_config.php

//check for cronjob configuration
$heartbeat = (isset($_POST['heartbeat']) ? $_POST['heartbeat'] : '');
include_once('../wp-config.php');
session_start();

echo '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">';
echo '<head profile="http://gmpg.org/xfn/11">';
echo "<font style='font-size:12px; font-weight:bold;  font-family: arial, helvetica, sans-serif; color: #333;'><center>";	
echo "</head>";
include_once ('../wp-blog-header.php');
include_once ('../wp-includes/registration.php');
include_once("./../wp-admin/includes/image.php");



//check for multisite
$bid = $_GET['blog_id'];
if ($heartbeat)
{
	$_COOKIE['bs_blog_id']=$bid;
}
//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($bid);
}
include_once('./functions/f_update_comment_count.php');



//stylize log 
echo "<body >";
include_once('includes/prepare_variables.php');
set_time_limit($cronjob_timeout);
ini_set('mysql.connect_timeout', $cronjob_timeout);
ini_set('default_socket_timeout', $cronjob_timeout);
include_once('includes/helper_functions.php');
include_once('./includes/c0n_wp.php');


//****************************************************************************
//INITIAL PREPARATION ********************************************************
//****************************************************************************

//clean up any broken bookmarking dates
$query = "SELECT * FROM ".$table_prefix."posts_to_bookmark WHERE date='0000-00-00 00:00:00' AND status='0' ORDER BY id ASC";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error();}
$rand = 1;
$this_date_time = $wordpress_date_time;
$date_today = date('Y-m-d', strtotime($this_date_time));

while ($arr=mysql_fetch_array($result))
{
	$rand = rand($bookmarking_minutes_min,$bookmarking_minutes_max);

	$this_date_time = date('Y-m-d H:i:s', strtotime("$this_date_time + $rand minutes"));
	$this_id = $arr['id'];
	$query2 = "UPDATE ".$table_prefix."posts_to_bookmark SET date='$this_date_time' WHERE id='$this_id'";
	$result2 = mysql_query($query2);
	if (!$result){echo $query2; echo mysql_error(); exit;}
}

//discover what is scheduled for today
$date_today = date('Y-m-d');
$query = "SELECT * FROM ".$table_prefix."posts  WHERE `post_date`<'$wordpress_date_time' AND post_status='future'";
$result = mysql_query($query);
if (!$result){ echo $query; exit; }
$posts_to_publish = array();
while ($arr = mysql_fetch_array($result))
{
        $post_id = $arr['ID'];
	    $posts_to_publish[] = $post_id;
}

//set comments scheduled for today to published status
$query = "UPDATE ".$table_prefix."comments SET comment_approved='1' WHERE DATE(comment_date)<='$date_today' AND comment_approved='pending' OR  DATE(comment_date)<='$date_today' AND comment_approved='2'";
$result = mysql_query($query);
if (!$result){ echo $query; exit; }

//rebuild bookmarking dates if needed.
$bookmarking_ids = array();
$max_times = max(count($twitter_user),count($pingfm_user));
$date_time = $wordpress_date_time;

$yesterday = date('Y-m-d ', strtotime("$date_today - 1 day"));
$query = "SELECT * FROM ".$table_prefix."posts_to_bookmark WHERE DATE(date)<='$yesterday' AND status='0' ORDER BY NULL";
$result = mysql_query($query);
if (!$result){ echo $query; echo mysql_error(); exit; }

while ($arr = mysql_fetch_array($result))
{
	$bookmarking_ids[] = $arr['id'];
}


foreach ($bookmarking_ids as $key=>$val)
{
	$rand = rand($bookmarking_minutes_min,$bookmarking_minutes_max);
	$date_time = date('Y-m-d H:i:s', strtotime("$date_time +$rand minutes"));
	
	for ($i=0;$i<$max_times;$i++)
	{
		$this_date_time = date('Y-m-d H:i:s', strtotime("$this_date_time +$rand minutes"));
		$mysql_date_times[] = $this_date_time;
	}
	
	$query = "UPDATE ".$table_prefix."posts_to_bookmark SET date='$mysql_date_times' WHERE id='$val'";
	$result = mysql_query($query);
	if (!$result){ echo $query; exit; }
}


?>

<div align=middle>
<div align=left style='width:500px'>
<?php


//**********************************************************************************
//RSS MODULE************************************************************************
//**********************************************************************************
if ($rss_module=='1')
{	
	echo "//***********************************************************************************<br>
		  //RSS CAMPAIGNS************************************************************<br>
		  //***********************************************************************************
		  <br><BR>
		  ";
	$this_mode = 'cron_config';
	include_once('functions/f_run_rss.php');
}//if rss_module is on

//******************************************************************************************************************************************************
//SOURCES MODULE*****************************************************************************************************************************************
//******************************************************************************************************************************************************
if ($sources_module=='1')
{	
	echo "//***********************************************************************************<br>
		  //SOURCE CAMPAIGNS************************************************************<br>
		  //***********************************************************************************
		  <br><BR>
		  ";
	$this_mode = 'cron_config';
	include_once('functions/f_run_sources.php');
	
}//if sources_module is on


//******************************************************************************************************************************************************
//YAHOO ANSWERS MODULE*****************************************************************************************************************************************
//******************************************************************************************************************************************************
if ($yahoo_module=='1')
{	
	echo "//***********************************************************************************<br>
		  //YAHOO ANSWERS CAMPAIGNS************************************************************<br>
		  //***********************************************************************************
		  <br><BR>
		  ";
		  
	$this_mode = 'cron_config';
	include_once('functions/f_run_yahoo.php');
	
}//if yahoo answers _module is on



//******************************************************************************
//AMAZON MODULE*******************************************************
//******************************************************************************

if ($amazon_module==1)
{
	echo "<br><br>**********************************************************************************************<br>";
	echo "***************************************AMAZON MODULE*****************************************<br>";
	echo "*********************************************************************************************<br>";
	
	$this_mode = 'cron_config';
	include_once('functions/f_run_amazon.php');
}//if amazon module on



//******************************************************************************
// VIDEO MODULE*******************************************************
//******************************************************************************

if ($video_module==1)
{
	echo "//***********************************************************************************<br>
		  //VIDEO CAMPAIGNS************************************************************<br>
		  //***********************************************************************************
		  <br><BR>
		  ";
	$this_mode = 'cron_config';
	include_once('functions/f_run_video.php');
}//if video module on

if ($drop_module==1)
{	
	echo "//***********************************************************************************<br>";
	echo "//DROP POSTING CAMPAIGNS ************************************************************<br>";
	echo "//***********************************************************************************<br>";
	echo "<BR>";
	
	$this_mode = 'cron_config';
	include('functions/f_run_drop.php');
	
}

if ($keywords_module==1)
{	
	//reset array variables
	$query = "SELECT keyword FROM ".$table_prefix."wptt_wptraffictools_google ORDER BY RAND() LIMIT 200";
	$result = mysql_query($query);
	if ($result)
	{
		//echo 1;
		while ($arr = mysql_fetch_array($result))
		{
			if (!strstr($arr['keyword'],':')&&strlen($arr['keyword'])>3)
			{
				$wptt_keywords[] = addslashes($arr['keyword']);
			}
		}
		
	}
	
	$rank_tracker=  get_option('rank_tracker_tool');
	if ($rank_tracker)
	{
		$rank_tracker = $rank_tracker['rankings'];
		//print_r($rank_tracker);
		foreach ($rank_tracker as $k=>$v)
		{
			if (!strstr($k,':')&&strlen($k)>3)
			{
				$wptt_keywords[] = $k;
			}
		}
	}
	
	$this_mode = 'cron_config';
	echo "//***********************************************************************************<br>";
	echo "//KEYWORD CAMPAIGNS ******************************************************************<br>";
	echo "//***********************************************************************************<br>";
	echo "<BR>";
		  
	include('functions/f_run_keywords.php');
	
}

if ($heartbeat!='1')
{
	include_once('functions/f_run_bookmarks.php');
}

//******************************************************************************
//UPDATE ANY NEW CATEGORY*******************************************************
//******************************************************************************
$query = "SELECT * FROM ".$table_prefix."term_taxonomy WHERE taxonomy='category'";
$result = mysql_query($query);
if (!$result){ echo $query; echo mysql_error(); exit;}
$count = mysql_num_rows($result);
if ($count>0)
{
   while ($array = mysql_fetch_array($result))
   {
       $id = $array['term_taxonomy_id'];
       $nquery = "SELECT * FROM ".$table_prefix."term_relationships tr JOIN ".$table_prefix."posts p ON tr.object_id=p.ID WHERE tr.term_taxonomy_id=$id AND p.post_status='publish' AND p.post_type='post' ORDER BY NULL";
       $nresult = mysql_query($nquery);
	   if (!$nresult){ echo $nquery; echo mysql_error(); exit;}
       
       $ncount = mysql_num_rows($nresult);
       if ($ncount>0)
       {
			$nquery = "UPDATE ".$table_prefix."term_taxonomy SET count=$ncount WHERE term_taxonomy_id=$id ";
			$nresult = mysql_query($nquery);
			if (!$nresult){ echo $nquery; echo mysql_error(); exit;}
       }
   } 
}

//update comment counts
updateCount($table_prefix);
//update category count
update_cat_count($table_prefix);
?>
</div>
</div>