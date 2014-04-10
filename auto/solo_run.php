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
ini_set('mysql.connect_timeout', 300);
ini_set('default_socket_timeout', 300);
include_once('../wp-config.php');
if (!isset($_SESSION)) { session_start();}
echo '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">';
echo '<head profile="http://gmpg.org/xfn/11">';
?>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php
echo '</head>';
//check for multisite
//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}
include_once('./functions/f_update_comment_count.php');
include_once("./../wp-admin/includes/image.php");



//stylize log 
echo "<font style='font-size:12px; font-weight:bold;  font-family: arial, helvetica, sans-serif; color: #333;'><center>";	

include_once('includes/prepare_variables.php');
include_once('includes/helper_functions.php');
include_once('./includes/c0n_wp.php');
set_time_limit($cronjob_timeout);


//****************************************************************************
//INITIAL PREPARATION ********************************************************
//****************************************************************************

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


//get vars
$cid = $_GET['id'];
$mode = $_GET['mode'];
$this_mode = "solo";
?>

<div align=middle>
<div align=left style='width:500px'>
<?php

if ($mode=='rss')
{	

	echo "//***********************************************************************************<br>
	  //RSS CAMPAIGNS************************************************************<br>
	  //***********************************************************************************
	  <br><BR>
	  ";
	
		include('functions/f_run_rss.php');

}

if ($mode=='sources')
{	
	echo "
			//***********************************************************************************<br>
			//SOURCE CAMPAIGNS*******************************************************************<br>
			//***********************************************************************************<br>
			<BR>";
			
	include('functions/f_run_sources.php');	
	
}

if ($mode=='video')
{
	echo "//***********************************************************************************<br>
		  //VIDEO CAMPAIGNS************************************************************<br>
		  //***********************************************************************************
		  <br><BR>";
				
	include('functions/f_run_video.php');
	
}




if ($mode=='yahoo')
{	
	echo "//***********************************************************************************<br>
		  //YAHOO ANSWERS CAMPAIGNS********************************************************<br>
		  //***********************************************************************************
		  <br><BR>";
		  
	include('functions/f_run_yahoo.php');
	
}

if ($mode=='amazon')
{	
	echo "//***********************************************************************************<br>";
	echo "//AMAZON CAMPAIGNS ******************************************************************<br>";
	echo "//***********************************************************************************<br>";
	echo "<BR>";
		  
	include('functions/f_run_amazon.php');
	
}

if ($mode=='keywords')
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
	
	echo "//***********************************************************************************<br>";
	echo "//KEYWORD CAMPAIGNS ******************************************************************<br>";
	echo "//***********************************************************************************<br>";
	echo "<BR>";
		  
	include('functions/f_run_keywords.php');
	
}

if ($mode=='fileimport')
{	
	echo "//***********************************************************************************<br>";
	echo "//DROP POSTING CAMPAIGNS ************************************************************<br>";
	echo "//***********************************************************************************<br>";
	echo "<BR>";
		  
	include('functions/f_run_drop.php');
	
}

//include bookmark posting code
include('functions/f_run_bookmarks.php');


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
       $nquery = "SELECT * FROM ".$table_prefix."term_relationships tr JOIN ".$table_prefix."posts p ON tr.object_id=p.ID WHERE tr.term_taxonomy_id=$id AND p.post_status='publish' AND p.post_type='post'";
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

//******************************************************************************
//UPDATE COMMENTS **************************************************************
//******************************************************************************
//set comments scheduled for today to published status
$query = "UPDATE ".$table_prefix."comments SET comment_approved='1' WHERE DATE(comment_date)<='$date_today' AND comment_approved='pending'";
$result = mysql_query($query);
if (!$result){ echo $query; exit; }

//update comment counts
updateCount($table_prefix);



?>