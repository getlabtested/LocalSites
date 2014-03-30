<?php
//************************************************************************//
// BlogSense - WordPress Edition - Professional License                   //
// Website:  http://www.blogsense-wp.com                                  //
// Author: Hudson.Atwell@gmail.com ::: Email me if you have any questions //
//  Check the website for  new versions and addons!                       //
//************************************************************************//
include_once('../wp-config.php');
require ( ABSPATH . WPINC . '/registration.php' );
if (!isset($_SESSION)) { session_start();}
//stylize log 
?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<title>Preview</title>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<link rel="stylesheet" type="text/css" href="style.css">
<script type="text/javascript" src="./includes/jquery.js"></script>
<script type="text/javascript">
	$(document).ready(function() 
	{
		$("#id_previous").live('click', function(){
			var link = $(this).attr('href');
			var id = link.replace('#','');
			//alert(id);
			var pid = id-1;
			//alert(pid);
			var nid = id;
			$('#id_next').attr('href','#'+nid);
			$('#id_previous').attr('href','#'+pid);
		});
		$("#id_next").live('click', function(){
			var link = $(this).attr('href');
			var id = link.replace('#','');
			var pid = id;
			//alert(pid);
			var nid = id;
			nid++;
			$('#id_next').attr('href','#'+nid);
			$('#id_previous').attr('href','#'+pid);
		});
     });
</script>
</head>
<body style="font-family:Khmer UI;background-color:#ffffff;background-image:none;">
<font style='font-size:12px;   font-family: arial, helvetica, sans-serif; color: #333;'><center>
<?php

include_once('includes/prepare_variables.php');
include_once('includes/helper_functions.php');
set_time_limit($cronjob_timeout);


$id = $_GET['id'];
$mode = $_GET['mode'];



$query = "SELECT * FROM ".$table_prefix.$_SESSION['second_prefix']."campaigns WHERE id=$id";
$result = mysql_query($query);
if (!$result){echo $query; exit;}
while ($arr = mysql_fetch_array($result))
{
	$campaign_id = $arr['id'];	
	$campaign_name = $arr['name'];		
	$campaign_type = $arr['module_type'];
	$campaign_source =  $arr['source'];	
	$campaign_query = $arr['query'];
	$campaign_feed = $arr['feed'];
	$campaign_author = $arr['author'];
	$campaign_limit_results = $arr['limit_results'];
	$campaign_include_keywords = $arr['include_keywords'];
	$campaign_exclude_keywords = $arr['exclude_keywords'];
	$campaign_category = $arr['category'];
	$campaign_language = $arr['language'];
	$campaign_spin_text = $arr['spin_text'];
	$campaign_strip_images = $arr['strip_images'];			
	$campaign_strip_links = $arr['strip_links'];
	$campaign_cloak_links = $arr['cloak_links'];
	$campaign_image_floating = $arr['image_floating'];
	$campaign_scrape_profile = $arr['scrape_profile'];
	$campaign_regex_search = $arr['regex_search'];
	$campaign_regex_replace = $arr['regex_replace'];
	$campaign_credit_source = $arr['credit_source'];
	$campaign_credit_source_nofollow = $arr['credit_source_nofollow'];
	$campaign_credit_source_text = $arr['credit_source_text'];
	$campaign_post_frequency = $arr['schedule_post_frequency'];
	$campaign_post_date = $arr['schedule_post_date'];
	$campaign_post_count = $arr['schedule_post_count'];
	$z_affiliate_id = $arr['z_affiliate_id'];
	$campaign_scrape_content = $arr['z_rss_scrape_content'];
	$campaign_scrape_content_begin_code = stripslashes($arr['z_rss_scrape_content_begin_code']);
	$campaign_scrape_content_end_code = stripslashes($arr['z_rss_scrape_content_end_code']);
	$campaign_scrape_comments = $arr['z_rss_scrape_comments'];
	$campaign_scrape_names_begin_code = stripslashes($arr['z_rss_scrape_names_begin_code']);
	$campaign_scrape_names_end_code = stripslashes($arr['z_rss_scrape_names_end_code']);
	$campaign_scrape_comments_begin_code = stripslashes($arr['z_rss_scrape_comments_begin_code']);
	$campaign_scrape_comments_end_code = stripslashes($arr['z_rss_scrape_comments_end_code']);
	$z_video_include_description = $arr['z_video_include_description'];
	$z_yahoo_option_category = $arr['z_yahoo_option_category'];
	$z_yahoo_option_date_range = $arr['z_yahoo_option_category'];
	$z_yahoo_option_region = $arr['z_yahoo_option_category'];
	$z_yahoo_option_results_limit = $arr['z_yahoo_option_category'];
	$z_yahoo_option_sorting = $arr['z_yahoo_option_sorting'];
	$z_yahoo_option_type = $arr['z_yahoo_option_category'];
	$campaign_title_template = $arr['z_title_template'];
	$campaign_post_template = $arr['z_post_template'];
	$campaign_post_overwrite = $arr['z_post_overwrite'];
	$campaign_include_keywords_scope = $arr['z_include_keywords_scope'];
	$campaign_exclude_keywords_scope = $arr['z_include_keywords_scope'];
	
	
	
	if ($mode=='sources')
	{
		$query = "SELECT * FROM ".$table_prefix.$_SESSION['second_prefix']."sourcedata WHERE id=$campaign_scrape_profile";
		$result = mysql_query($query);
		if (!$result){echo $query; exit;}
		while ($arr = mysql_fetch_array($result))
		{
			$campaign_scrape_content_start = $arr['content_start'];
			$campaign_scrape_content_end = $arr['content_end'];
			$campaign_scrape_title_start = $arr['title_start'];
			$campaign_scrape_title_end = $arr['title_end'];
			$campaign_scrape_comments_status = $arr['comments_status'];
			$campaign_scrape_comments_name_start = $arr['comments_name_start'];
			$campaign_scrape_comments_name_end = $arr['comments_name_end'];
			$campaign_scrape_comments_content_start = $arr['comments_content_start'];
			$campaign_scrape_comments_content_end = $arr['comments_content_end'];
			
			if ($campaign_regex_search)
			{
				$source_regex_search = $arr['regex_search'];
				$source_regex_replace = $arr['regex_replace'];
			}
			else
			{
				$campaign_regex_search = $arr['regex_search'];
				$campaign_regex_replace = $arr['regex_replace'];
			}
		}
	}	
	
	//make arrays of regex if available; combine additional regex for sources
	if ($campaign_regex_search)
	{
	   $campaign_regex_search = explode('***r***',$campaign_regex_search);
	   $campaign_regex_replace = explode('***r***',$campaign_regex_replace);
	   
	   if ($source_regex_search)
	   {
			$source_regex_search = explode('***r***',$source_regex_search);
			$source_regex_replace = explode('***r***',$source_regex_replace);
			
			$campaign_regex_search = array_merge( $campaign_regex_search , $source_regex_search);
			$campaign_regex_replace = array_merge($campaign_regex_replace , $source_regex_replace);
			
			$campaign_regex_search = array_unique($campaign_regex_search);
			$campaign_regex_replace = array_unique($campaign_regex_replace);			
	   }
	}
}

?>
<div align=middle>
<div align=left style='width:500px'>
<?php


//******************************************************//
//***********************RSS Preview****************//
//******************************************************//
//******************************************************//

if ($mode=='rss')
{	
	include('functions/f_preview_rss.php');	
	
}//if mode==rss



//******************************************************//
//***********************Sources Preview****************//
//******************************************************//
//******************************************************//
if ($mode=='sources')
{	
	include('functions/f_preview_sources.php');	
}//if mode==sources


//******************************************************//
//***********************Video Preview******************//
//******************************************************//
//******************************************************//

if ($mode=='video')
{	
	include('functions/f_preview_video.php');	
}//if mode==video


//******************************************************//
//***********************Yahoo Preview****************//
//******************************************************//
//******************************************************//
if ($mode=='yahoo')
{	
	include('functions/f_preview_yahoo.php');
}//if mode==yahoo

//******************************************************************************
// AMAZON MODULE*******************************************************
//******************************************************************************

if ($mode=='amazon')
{
	include('functions/f_preview_amazon.php');
}

//******************************************************************************
// KEYWORDS MODULE*******************************************************
//******************************************************************************

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
	
	include('functions/f_preview_keywords.php');
}

//******************************************************************************
// DROP MODULE*******************************************************
//******************************************************************************

if ($mode=='fileimport')
{
	include('functions/f_preview_drop.php');
}


?>
<!--end-->
</div>
</div>
<div style="position:fixed;top:5px;right:5px;"><a href="#0" id=id_previous>< Previous</a>&nbsp;&nbsp;<a href="#1" id=id_next>Next ></a></div>

<br><br><br><br><br><br>

<?php
if ($error)
{
	echo "<hr>
		<h2>
		<img src='nav/tip.png' style='cursor:pointer;' border=0 title='These logs show the sourced html from each individual post. You can look at these to determine how you might have made a mistake in setting up your paramater, or if ther might be another error preventing you from scraping material'>
		Error Report</h2>
		<div align='left'>
		";
	foreach ($error as $key=>$val)
	{
	  echo $val;
	}
}
?>
</div>
</body>
</html>		