<?php

include_once('./../../wp-config.php');
include_once("./../../wp-admin/includes/image.php");
session_start();
include("./../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../login.php");

ini_set("magic_quotes_runtime", 0);
include_once("../includes/helper_functions.php");
//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}
include_once('./../includes/prepare_variables.php');
set_time_limit($cronjob_timeout);
$module_type = $_GET['module_type'];

if ($_GET['edit']==1||$_GET['import']==1)
{	
	//echo 1; exit;
	$campaign_id = $_GET['id'];
	$query = "SELECT * FROM ".$table_prefix."campaigns WHERE id=$campaign_id";
	$result = mysql_query($query);
	if (!$result){echo $query; exit;}
	while ($arr = mysql_fetch_array($result))
	{
		$campaign_id = $arr['id'];	
		$campaign_name = $arr['name'];			
		$campaign_type = $arr['module_type'];
		$campaign_source =  $arr['source'];
		$campaign_query = $arr['query'];
		$campaign_query = str_replace('%2B', ' ', $campaign_query);
		$campaign_query = str_replace('+', ' ', $campaign_query);
		$campaign_query = urldecode($campaign_query);
		$campaign_query = stripslashes($campaign_query);
		$campaign_feed = $arr['feed'];
		$campaign_author = $arr['author'];
		$campaign_limit_results = $arr['limit_results'];
		$campaign_include_keywords = $arr['include_keywords'];
		$campaign_exclude_keywords = $arr['exclude_keywords'];
		$campaign_category = $arr['category'];
		$campaign_autocategorize = $arr['autocategorize'];
		$campaign_autocategorize_search = $arr['autocategorize_search'];
		$campaign_autocategorize_method = $arr['autocategorize_method'];
		$campaign_autocategorize_filter_keywords = $arr['autocategorize_filter_keywords'];
		$campaign_autocategorize_filter_categories = $arr['autocategorize_filter_categories'];
		$campaign_autocategorize_filter_list = $arr['autocategorize_filter_list'];
		$campaign_autotag_method = $arr['autotag_method'];
		$campaign_autotag_custom_tags = $arr['autotag_custom_tags'];
		$campaign_autotag_min = $arr['autotag_min'];
		$campaign_autotag_max = $arr['autotag_max'];
		$campaign_language = $arr['language'];		
		$campaign_spin_text = $arr['spin_text'];
		$campaign_strip_images = $arr['strip_images'];			
		$campaign_strip_links = $arr['strip_links'];
		$campaign_image_floating = $arr['image_floating'];
		$campaign_scrape_profile = $arr['scrape_profile'];
		$campaign_regex_search = $arr['regex_search'];
		$campaign_regex_replace = $arr['regex_replace'];
		$campaign_credit_source = $arr['credit_source'];
		$campaign_credit_source_nofollow = $arr['credit_source_nofollow'];
		$campaign_credit_source_text = $arr['credit_source_text'];
		$campaign_backdating = $arr['schedule_backdating'];
		$campaign_post_frequency = $arr['schedule_post_frequency'];
		$campaign_post_frequency_start_date = $arr['schedule_post_date'];
		$campaign_post_count = $arr['schedule_post_count'];
		$campaign_post_status = $arr['z_post_status'];
		$campaign_post_type = $arr['z_post_type'];
		$campaign_custom_field_name = $arr['custom_field_name'];
		$campaign_custom_field_value = $arr['custom_field_value'];
		$campaign_comments_include = $arr['z_comments_include'];
		$campaign_comments_limit = $arr['z_comments_limit'];
		$z_affiliate_id = $arr['z_affiliate_id'];
		$campaign_scrape_content = $arr['z_rss_scrape_content'];
		$campaign_scrape_content_begin_code = $arr['z_rss_scrape_content_begin_code'];
		$campaign_scrape_content_end_code = $arr['z_rss_scrape_content_end_code'];
		$campaign_scrape_comments = $arr['z_rss_scrape_comments'];
		$campaign_scrape_comments_begin_code = $arr['z_rss_scrape_comments_begin_code'];
		$campaign_scrape_comments_end_code = $arr['z_rss_scrape_comments_end_code'];
		$campaign_scrape_names_begin_code = $arr['z_rss_scrape_names_begin_code'];
		$campaign_scrape_names_end_code = $arr['z_rss_scrape_names_end_code'];
		$z_yahoo_option_category = $arr['z_yahoo_option_category'];
		$z_yahoo_option_date_range = $arr['z_yahoo_option_date_range'];
		$z_yahoo_option_region = $arr['z_yahoo_option_region'];
		$z_yahoo_option_results_limit = $arr['z_yahoo_option_results_limit'];
		$z_yahoo_option_sorting = $arr['z_yahoo_option_sorting'];
		$z_yahoo_option_type = $arr['z_yahoo_option_type'];
		$campaign_post_template = $arr['z_post_template'];		
		$campaign_title_template  = $arr['z_title_template'];
		$bookmark_twitter = $arr['z_bookmark_twitter'];
		$bookmark_pixelpipe = $arr['z_bookmark_pixelpipe'];
		$campaign_remote_publishing_api_bs = $arr['z_remote_publishing_api_bs'];
		$campaign_remote_publishing_api_email = $arr['z_remote_publishing_api_email'];
		$campaign_remote_publishing_api_email_footer = $arr['z_remote_publishing_api_email_footer'];
		$campaign_remote_publishing_api_pp_email = $arr['z_remote_publishing_api_pp_email'];
		$campaign_remote_publishing_api_pp_routing = $arr['z_remote_publishing_api_pp_routing'];
		$campaign_remote_publishing_api_xmlrpc = $arr['z_remote_publishing_api_xmlrpc'];
		$campaign_remote_publishing_api_xmlrpc_spin = $arr['z_remote_publishing_api_xmlrpc_spin'];
		$campaign_post_overwrite = $arr['z_post_overwrite'];
		$campaign_include_keywords_scope = $arr['z_include_keywords_scope'];
		$campaign_exclude_keywords_scope = $arr['z_exclude_keywords_scope'];
		
		
		//echo $bookmark_hellotxt;exit;
		if ($module_type=='sources')
		{
			$query = "SELECT * FROM ".$table_prefix."sourcedata WHERE id=$campaign_scrape_profile";
			$result = mysql_query($query);
			if (!$result){echo $query; exit;}
			while ($arr = mysql_fetch_array($result))
			{
				$campaign_scrape_footprint = $arr['footprint'];
				$campaign_scrape_content_start = $arr['content_start'];
				$campaign_scrape_content_start_backup_1 = $arr['content_start_backup_1'];
				$campaign_scrape_content_start_backup_2 = $arr['content_start_backup_2'];
				$campaign_scrape_content_end = $arr['content_end'];
				$campaign_scrape_content_end_backup_1 = $arr['content_end_backup_1'];
				$campaign_scrape_content_end_backup_2 = $arr['content_end_backup_2'];
				$campaign_scrape_title_start = $arr['title_start'];
				$campaign_scrape_title_start_backup_1 = $arr['title_start_backup_1'];
				$campaign_scrape_title_start_backup_2 = $arr['title_start_backup_2'];
				$campaign_scrape_title_end = $arr['title_end'];
				$campaign_scrape_title_end_backup_1 = $arr['title_end_backup_1'];
				$campaign_scrape_title_end_backup_2 = $arr['title_end_backup_2'];
				$campaign_scrape_comments_status = $arr['comments_status'];
				$campaign_scrape_comments_name_start = $arr['comments_name_start'];
				$campaign_scrape_comments_name_end = $arr['comments_name_end'];
				$campaign_scrape_comments_content_start = $arr['comments_content_start'];
				$campaign_scrape_comments_content_end = $arr['comments_content_end'];
				if (count($campaign_regex_search)==0)
				{
					$campaign_regex_search = $arr['regex_search'];
					$campaign_regex_replace = $arr['regex_replace'];
				}
				
			}
		}	
		
		//make arrays of regex if available
		if ($campaign_regex_search)
		{
			$campaign_regex_search = explode('***r***',$campaign_regex_search);
			$campaign_regex_replace = explode('***r***',$campaign_regex_replace);
		}
		
		if ($campaign_custom_field_name)
		{
			$campaign_custom_field_name = explode('***',$campaign_custom_field_name);
			$campaign_custom_field_value = explode('***',$campaign_custom_field_value);
		}
		
		if ($campaign_autocategorize_filter_keywords)
		{
			$campaign_autocategorize_filter_keywords = explode(';',$campaign_autocategorize_filter_keywords);
			$campaign_autocategorize_filter_categories = explode(';',$campaign_autocategorize_filter_categories);
		}
	}
}

//prepare new categories
$slugs = "";
$categories = "";
$cat_ids = "";
$query = "SELECT t.name, t.slug, t.term_id, tt.term_id, tt.term_taxonomy_id FROM ".$table_prefix."term_taxonomy tt,  ".$table_prefix."terms t WHERE tt.term_id = t.term_id AND tt.taxonomy='category'";
$result = mysql_query($query);
if (!$result){echo $query; exit;}
while ($array = mysql_fetch_array($result))
{
	$slugs[] = $array['slug'];
	$categories[] = $array['name'];
	$cat_ids[] = $array['term_id'];
}
$blog_categories = $categories;

foreach($categories as $k=>$v)
{	
	
	if ($campaign_category==$cat_ids[$k])
	{
		$selected = "selected=true";
	}
	else
	{
	   $selected = "";
	}
	if (!$select_html)
	{
		$select_html = "<option value='$cat_ids[$k]' $selected>$slugs[$k]</option>";
	}
	else
	{
		$select_html .= "<option value='$cat_ids[$k]' $selected>$slugs[$k]</</option>";
	}				
}



if (!$_POST['submit_nature'])
{
	if ($module_type=='sources')
	{
		include_once('f_add_sources.php');

	}
	if ($module_type=='rss')
	{
		include_once('f_add_rss.php');
	}
	if ($module_type=='video')
	{
		include_once('f_add_video.php');
	}
	if ($module_type=='fileimport')
	{
		include_once('f_add_drop.php');
	}
	
	if ($module_type=='yahoo')
	{
		include_once('f_add_yahoo.php');
	}
	
	if ($module_type=='amazon')
	{
		include_once('f_add_amazon.php');
	}
	
	if ($module_type=='keywords')
	{
		include_once('f_add_keywords.php');
	}
	
}
else
{
	if (!$keyphrase)
	{
	  $keyphrase = array();
	}
	
	//get variables
	if ($_POST['campaign_id']){ $campaign_id = $_POST['campaign_id'];}
	$campaign_name =  $_POST['campaign_name'];
    $source =  urlencode($_POST['source']);	
	$limit_results = $_POST['limit_results'];
	$keywords = stripslashes($_POST['query']);
	$include_keywords = $_POST['include_keywords'];
	$exclude_keywords = $_POST['exclude_keywords'];
	$author = urlencode($_POST['author']);
	if ($_POST['links']){ $links = explode(";", $_POST['links']);}
	$backdating = $_POST['backdating'];
	$post_frequency = $_POST['post_frequency'];
	$post_frequency_start_date = $_POST['post_frequency_start_date'];
	$strip_links = $_POST['strip_links'];
	$strip_images = $_POST['strip_images'];
	$campaign_language = $_POST['language'];
	$spin_text = $_POST['spin_text'];
	$category = $_POST['category'];
	$autocategorize = $_POST['autocategorize'];
	$autocategorize_search = $_POST['autocategorize_search'];
	$autocategorize_method = $_POST['autocategorize_method'];
	$autocategorize_filter_keywords = $_POST['autocategorize_filter_keywords'];
	$autocategorize_filter_categories = $_POST['autocategorize_filter_categories'];
	$autocategorize_filter_list = $_POST['autocategorize_filter_list'];
	$autotag_method = $_POST['autotag_method'];
	$autotag_custom_tags = $_POST['autotag_custom_tags'];
	$autotag_min = $_POST['autotag_min'];
	$autotag_max = $_POST['autotag_max'];
	$image_floating = $_POST['image_floating'];
	$scrape_footprint = stripslashes($_POST['scrape_footprint']);
	$scrape_content_start = stripslashes($_POST['scrape_content_start']);
	$scrape_content_start_backup_1 = stripslashes($_POST['scrape_content_start_backup_1']);
	$scrape_content_start_backup_2 = stripslashes($_POST['scrape_content_start_backup_2']);
	$scrape_content_end = stripslashes($_POST['scrape_content_end']);
	$scrape_content_end_backup_1 = stripslashes($_POST['scrape_content_end_backup_1']);
	$scrape_content_end_backup_2 = stripslashes($_POST['scrape_content_end_backup_2']);
	$scrape_title_start = stripslashes($_POST['scrape_title_start']);
	$scrape_title_start_backup_1 = stripslashes($_POST['scrape_title_start_backup_1']);
	$scrape_title_start_backup_2 = stripslashes($_POST['scrape_title_start_backup_2']);
	$scrape_title_end = stripslashes($_POST['scrape_title_end']);
	$scrape_title_end_backup_1 = stripslashes($_POST['scrape_title_end_backup_1']);
	$scrape_title_end_backup_2 = stripslashes($_POST['scrape_title_end_backup_2']);
	$scrape_comments_status = stripslashes($_POST['scrape_comments_status']);
	$scrape_comments_name_start = stripslashes($_POST['scrape_comments_name_start']);
	$scrape_comments_name_end = stripslashes($_POST['scrape_comments_name_end']);
	$scrape_comments_content_start = stripslashes($_POST['scrape_comments_content_start']);
	$scrape_comments_content_end = stripslashes($_POST['scrape_comments_content_end']);
	$regex_search = $_POST['regex_search'];
	$regex_replace = $_POST['regex_replace'];	
	$title_template = stripslashes($_POST['title_template']);
	$post_template = stripslashes($_POST['post_template']);
	$credit_source= $_POST['credit_source'];
	$credit_source_nofollow= $_POST['credit_source_nofollow'];
	$credit_source_text= $_POST['credit_source_text'];
	$module_type = $_POST['module_type'];
	$rss_scrape_content = $_POST['rss_scrape_content'];
	$rss_scrape_content_begin_code = $_POST['rss_scrape_content_begin_code'];
	$rss_scrape_content_end_code = $_POST['rss_scrape_content_end_code'];
	$rss_scrape_comments = $_POST['rss_scrape_comments'];
	$rss_scrape_comments_begin_code = $_POST['rss_scrape_comments_begin_code'];
	$rss_scrape_comments_end_code = $_POST['rss_scrape_comments_end_code'];
	$rss_scrape_names_begin_code = $_POST['rss_scrape_names_begin_code'];
	$rss_scrape_names_end_code = $_POST['rss_scrape_names_end_code'];
	$video_include_description = stripslashes($_POST['video_include_description']);
	$z_yahoo_option_category = $_POST['z_yahoo_option_category'];
	$z_yahoo_option_date_range = $_POST['z_yahoo_option_date_range'];
	$z_yahoo_option_region = $_POST['z_yahoo_option_region'];
	$z_yahoo_option_results_limit = $_POST['z_yahoo_option_results_limit'];
	$z_yahoo_option_sorting = $_POST['z_yahoo_option_sorting'];
	$z_yahoo_option_type = $_POST['z_yahoo_option_type'];
	$custom_field_name = $_POST['custom_field_name'];
	$custom_field_value = $_POST['custom_field_value'];
	$post_status = $_POST['post_status'];
	$post_type  = $_POST['post_type'];
	$comments_include = $_POST['comments_include'];
	$comments_limit = $_POST['comments_limit'];
	$remote_publishing_api_bs =$_POST['remote_publishing_api_bs'];
	$remote_publishing_api_email =$_POST['remote_publishing_api_email'];
	$remote_publishing_api_email_footer =$_POST['remote_publishing_api_email_footer'];
	$remote_publishing_api_pp_email =$_POST['remote_publishing_api_pp_email'];
	$remote_publishing_api_pp_routing =$_POST['remote_publishing_api_pp_routing'];
	$remote_publishing_api_xmlrpc =$_POST['remote_publishing_api_xmlrpc'];
	$remote_publishing_api_xmlrpc_spin =$_POST['remote_publishing_api_xmlrpc_spin'];
	$post_overwrite =$_POST['post_overwrite'];
	$include_keywords_scope =$_POST['include_keywords_scope'];
	$exclude_keywords_scope =$_POST['exclude_keywords_scope'];
	$csv_use_collumn_for_category = $_POST['csv_use_collumn_for_category'];
	$csv_collumn_category = $_POST['csv_collumn_category'];

	$batch_keywords =  $_POST['batch_keywords'];
	
	if ($batch_keywords)
	{
		$keyword_option =  $_POST['keyword_option'];
		$batch_keywords = nl2br($batch_keywords);
		$batch_keywords = explode('<br />', $batch_keywords);
		
		//print_r($batch_keywords);exit;
		$keyword_tag = $_POST['keyword_tag'];
		
		foreach ($batch_keywords as $key=>$val)
		{
			$batch_keywords[$key] = trim($val);
			//make original source url
			if ($keyword_tag)
			{
				$batch_keywords[$key] = "$val $keyword_tag";
			}
		}
		$batch_keywords = array_filter($batch_keywords);
		$keywords = implode(';',$batch_keywords);
	}
	//echo $keywords;exit;
	foreach ($twitter_user as $key=>$val)
	{
		$this_status = $_POST["bookmark_twitter_status_$key"];
		if (!$this_status)
		{
			$bookmark_twitter_status[] = 'off';
		}
		else
		{
			$bookmark_twitter_status[] = $this_status;
		}
		$bookmark_twitter_hash[] = $_POST["bookmark_twitter_hash_$key"];
	}
	
	$z_bookmark_twitter = json_encode(array($bookmark_twitter_status,$bookmark_twitter_hash));
	
	foreach ($pixelpipe_email as $key=>$val)
	{
		$this_status = $_POST["bookmark_pixelpipe_status_$key"];
		if (!$this_status)
		{
			$bookmark_pixelpipe_status[] = 'off';
		}
		else
		{
			$bookmark_pixelpipe_status[] = $this_status;
		}
	}
	
	$z_bookmark_pixelpipe= json_encode($bookmark_pixelpipe_status);

	if ($csv_use_collumn_for_category==1)
	{
		//echo 1; exit;
		$category = "csv:$csv_collumn_category";
		//print_r($category);exit;
	}
	
	if ($remote_publishing_api_bs)
	{
		$remote_publishing_api_bs = implode(';',$remote_publishing_api_bs);
	}
	
	if ($remote_publishing_api_xmlrpc)
	{
		foreach ($remote_publishing_api_xmlrpc as $key=>$val)
		{
			$remote_publishing_api_xmlrpc_spin[] = $_POST["remote_publishing_api_email_footer_$key"];
		}
	}
	
	if ($remote_publishing_api_xmlrpc)
	{
		$remote_publishing_api_xmlrpc = implode(':::',$remote_publishing_api_xmlrpc);
		$remote_publishing_api_xmlrpc_spin = implode(':::',$remote_publishing_api_xmlrpc_spin);
	}
	
	if ($remote_publishing_api_email)
	{
		$remote_publishing_api_email = implode(';',$remote_publishing_api_email);
		$remote_publishing_api_email_footer = implode(';',$remote_publishing_api_email_footer);
	}
	
	if ($remote_publishing_api_pp_email)
	{
		$remote_publishing_api_pp_email = implode(';',$remote_publishing_api_pp_email);
		if ($remote_publishing_api_pp_routing)
		{
			$remote_publishing_api_pp_routing = implode(';',$remote_publishing_api_pp_routing);
		}
	}
	
	if ($autocategorize_filter_keywords)
	{
		$autocategorize_filter_keywords = implode(';',$autocategorize_filter_keywords);
		$autocategorize_filter_categories = implode(';',$autocategorize_filter_categories);
	}
	
	if (!$include_keywords) { $include_keywords = "Separate with commas."; }
	if (!$exclude_keywords) { $exclude_keywords = "Separate with commas."; }
	
	if (!$z_bookmark_twitter) { $z_bookmark_twitter = "0"; }
	
	if ($autotag_method==3)
	{
	   $query = "SELECT * FROM ".$table_prefix."terms t JOIN  ".$table_prefix."term_taxonomy tt ON (t.term_id=tt.term_id) AND tt.taxonomy='post_tag'";
	   $result = mysql_query($query);
	   while ($arr = mysql_fetch_array($result))
	   {
		  $tags_db[] = trim($arr['name']);
	   }
	   if ($tags_db)
	   {
		$tags_db = implode(',',$tags_db);
	   }
	   else
	   {
		 $tags_db = "no tags available";
	   }
	   while (strstr($tags_db, ',,'))
	   {
		  $tags_db = str_replace(',,','', $tags_db);
	   }
	   $autotag_custom_tags = $tags_db;			
	}
	
	$autotag_custom_tags = addslashes($autotag_custom_tags);
	
	if ($_POST['submit_nature']=='create')
	{
	     //make original source url
	    $source = urldecode($source);
		
		if ($module_type=='sources')
		{	   
			//build the special feed link
			$include_url = explode('functions', $current_url);	
			$include_url = "".$include_url[0]."includes/";	
			$site_formated = str_replace("http://", "open*", $source);
			$footprint = urlencode($scrape_footprint);
			if (strstr($keywords,','))
			{
				$keywords = str_replace(',','|',$keywords);
				$keywords = '[spyntax]{'.$keywords.'}[/spyntax]';
			}
			$nukeywords = "$keywords+$footprint";
			$nukeywords = str_replace(' ', '+',$nukeywords);
			$source_feed = "".$include_url."search3links.php?s=$site_formated&q=$nukeywords";
			//echo $source_feed;exit;
			//descover scrape profile if there
			if (strlen($source)>2)
			{
				$query = "SELECT * FROM ".$table_prefix."sourcedata WHERE source_url='$source'";
				$result = mysql_query($query);
				if (!$result){ echo $query; echo mysql_error(); exit; }
			 
				$array = mysql_fetch_array($result);
				$scrape_profile = $array['id'];
				//echo $scrape_profile; exit;
			}
		}
		if($module_type=='rss'||$module_type=='amazon')
		{
			$source_feed = $source;
		}
		if($module_type=='video')
		{
			if (strstr($keywords,','))
			{
				$keywords = str_replace(',','|',$keywords);
				$keywords = '[spyntax]{'.$keywords.'}[/spyntax]';
			}
			
			$nukeywords = urlencode($keywords);
			if ($source=='query')
			{
				$source_feed = "http://gdata.youtube.com/feeds/base/videos?q=$nukeywords&key=AI39si6RmbtB6goYpu0MrGKmEeEhg5dIOSdZUClTencT6F_Saf3Wjqp9y55xoJ1PAa_htlx3ArxozpuNiG-jdWzNxMAV-NhvKw";	
			}
			else
			{
				$source_feed = "http://gdata.youtube.com/feeds/api/users/$nukeywords/uploads/?key=AI39si6RmbtB6goYpu0MrGKmEeEhg5dIOSdZUClTencT6F_Saf3Wjqp9y55xoJ1PAa_htlx3ArxozpuNiG-jdWzNxMAV-NhvKw";	
			}
		}
		
		if($module_type=='yahoo')
		{
			if (strstr($keywords,','))
			{
				$keywords = str_replace(',','|',$keywords);
				$keywords = '[spyntax]{'.$keywords.'}[/spyntax]';
			}
			$nukeywords = urlencode($keywords);
			if ($limit_results==0||$limit_results>50) $limit_results=50;
			$y_feed = "http://answers.yahooapis.com/AnswersService/V1/questionSearch?&appid=$yahoo_api_key&query=$nukeywords&type=$z_yahoo_option_type&category_id=$z_yahoo_option_category&region=$z_yahoo_option_region&date_range=$z_yahoo_option_date_range&results=$limit_results&sort=$z_yahoo_option_sorting&search_in=all";
			$source_feed = $y_feed;
		}

		
		//prepare regex searches if needed
		if ($regex_search)
		{
			foreach($regex_search as $k=>$v)
			{
				if (!strstr($regex_search[$k],'/'))
				{
					$regex_search[$k] = "/$regex_search[$k]/";
				}
			}
			
			$regex_search = implode('***r***',$regex_search);
			$regex_replace = implode('***r***',$regex_replace);
		}
		
		//build start date		
		if ($post_frequency_start_date==date('Y-m-d'))
		{
			$post_frequency_start_date = '0000-00-00 00:00:00';
		}
		else
		{
			$time = date('H:i:s');
			$post_frequency_start_date = "$post_frequency_start_date $time";
		}
	   
	   	if ($custom_field_name)
		{
			$custom_field_name = implode('***',$custom_field_name);
			$custom_field_value = implode('***',$custom_field_value);
		}
	    
		//add slashes if needed
		$rss_scrape_content_begin_code = addslashes($rss_scrape_content_begin_code);
		$rss_scrape_content_end_code = addslashes($rss_scrape_content_end_code);
		$rss_scrape_names_begin_code = addslashes($rss_scrape_names_begin_code);
		$rss_scrape_names_end_code = addslashes($rss_scrape_names_end_code);
		$rss_scrape_comments_begin_code = addslashes($rss_scrape_comments_begin_code);
		$rss_scrape_comments_end_code = addslashes($rss_scrape_comments_end_code);
		$post_template = addslashes($post_template);
		$title_template = trim(addslashes($title_template));
	
	    $query = "INSERT INTO ".$table_prefix."campaigns (`id`,`name`,`campaign_status`,`module_type`,`source`,`query`,`feed`,`limit_results`,`author`,`include_keywords`,`exclude_keywords`,`category`,`autocategorize`,`autocategorize_search`,`autocategorize_method`,`autocategorize_filter_keywords`,`autocategorize_filter_categories`,`autocategorize_filter_list`,`autotag_method`,`autotag_custom_tags`,`autotag_min`,`autotag_max`,`language`,`spin_text`,`strip_images`,`strip_links`,`image_floating`,`scrape_profile`,`regex_search`,`regex_replace`,`credit_source`,`credit_source_nofollow`,`credit_source_text`,`schedule_backdating`,`schedule_post_frequency`,`schedule_post_date`,`schedule_post_count`,`custom_field_name`,`custom_field_value`,`z_affiliate_id`,`z_bookmark_twitter`,`z_bookmark_pixelpipe`,`z_rss_scrape_content`,`z_rss_scrape_content_begin_code`,`z_rss_scrape_content_end_code`,`z_rss_scrape_comments`,`z_rss_scrape_names_begin_code`,`z_rss_scrape_names_end_code`,`z_rss_scrape_comments_begin_code`,`z_rss_scrape_comments_end_code`,`z_video_include_description`,`z_yahoo_option_category`,`z_yahoo_option_date_range`,`z_yahoo_option_region`,`z_yahoo_option_results_limit`,`z_yahoo_option_sorting`,`z_yahoo_option_type`,`z_post_template`,`z_title_template`,`z_post_status`,`z_post_type`,`z_comments_include`,`z_comments_limit`,`z_remote_publishing_api_bs`,`z_remote_publishing_api_xmlrpc`,`z_remote_publishing_api_xmlrpc_spin`,`z_remote_publishing_api_pp_email`,`z_remote_publishing_api_pp_routing`,`z_remote_publishing_api_email`,`z_remote_publishing_api_email_footer`,`z_post_overwrite`,`z_include_keywords_scope`,`z_exclude_keywords_scope`)";
	    $query .= "VALUES ('','$campaign_name','1','$module_type','$source','$keywords','$source_feed','$limit_results','$author','$include_keywords','$exclude_keywords','$category','$autocategorize','$autocategorize_search','$autocategorize_method','$autocategorize_filter_keywords','$autocategorize_filter_categories','$autocategorize_filter_list','$autotag_method','$autotag_custom_tags','$autotag_min','$autotag_max','$campaign_language','$spin_text','$strip_images','$strip_links','$image_floating','$scrape_profile','$regex_search','$regex_replace','$credit_source','$credit_source_nofollow','$credit_source_text','$backdating','$post_frequency','$post_frequency_start_date','0','$custom_field_name','$custom_field_value','$z_affiliate_id','$z_bookmark_twitter','$z_bookmark_pixelpipe','$rss_scrape_content','$rss_scrape_content_begin_code','$rss_scrape_content_end_code','$rss_scrape_comments','$rss_scrape_names_begin_code','$rss_scrape_names_end_code','$rss_scrape_comments_begin_code','$rss_scrape_comments_end_code','$video_include_description','$z_yahoo_option_category','$z_yahoo_option_date_range','$z_yahoo_option_region','$z_yahoo_option_results_limit','$z_yahoo_option_sorting','$z_yahoo_option_type','$post_template','$title_template','$post_status','$post_type','$comments_include','$comments_limit', '$remote_publishing_api_bs','$remote_publishing_api_xmlrpc','$remote_publishing_api_xmlrpc_spin','$remote_publishing_api_pp_email','$remote_publishing_api_pp_routing','$remote_publishing_api_email','$remote_publishing_api_email_footer', '$post_overwrite','$include_keywords_scope','$exclude_keywords_scope')";
	    $result = mysql_query($query);
	    if (!$result){ echo $query; echo mysql_error(); exit; }
	
	    echo "<center><br><br><br><br><font color=green>Campaign Created. </center></font>";
	    exit;
	}
	//echo 1;exit;
	if ($_POST['submit_nature']=='save')
	{
		
		//make original source url
	    $source = urldecode($source);
	    //echo $source; exit;
		if ($module_type=='sources')
		{	 
			//build the special feed link
			$include_url = explode('functions', $current_url);	
			$include_url = "".$include_url[0]."includes/";	
			$site_formated = str_replace("http://", "open*", $source);
			$footprint = urlencode($scrape_footprint);	
			if (strstr($keywords,','))
			{
				$keywords = str_replace(',','|',$keywords);
				$keywords = '[spyntax]{'.$keywords.'}[/spyntax]';
			}
			$nukeywords = "$keywords+$footprint";
			$nukeywords = str_replace(' ', '+',$nukeywords);
			$source_feed = "".$include_url."search3links.php?s=$site_formated&q=$nukeywords";
			//echo $source_feed;exit;
		
			
			//descover scrape profile if there
			if (strlen($source)>2)
			{
				$query = "SELECT * FROM ".$table_prefix."sourcedata WHERE source_url='$source'";
				$result = mysql_query($query);
				if (!$result){ echo $query; echo mysql_error(); exit; }
			 
				$array = mysql_fetch_array($result);
				$scrape_profile = $array['id'];
				//echo $scrape_profile; exit;
			}
		}
		
		if($module_type=='rss'||$module_type=='amazon')
		{
			$source_feed = $source;
		}
		
		if($module_type=='video')
		{
			if (strstr($keywords,','))
			{
				$keywords = str_replace(',','|',$keywords);
				$keywords = '[spyntax]{'.$keywords.'}[/spyntax]';
			}
			$nukeywords = urlencode($keywords);
			if ($source=='query')
			{
				$source_feed = "http://gdata.youtube.com/feeds/base/videos?q=$nukeywords&key=AI39si6RmbtB6goYpu0MrGKmEeEhg5dIOSdZUClTencT6F_Saf3Wjqp9y55xoJ1PAa_htlx3ArxozpuNiG-jdWzNxMAV-NhvKw";	
			}
			else
			{
				$source_feed = "http://gdata.youtube.com/feeds/api/users/$nukeywords/uploads/?key=AI39si6RmbtB6goYpu0MrGKmEeEhg5dIOSdZUClTencT6F_Saf3Wjqp9y55xoJ1PAa_htlx3ArxozpuNiG-jdWzNxMAV-NhvKw";	
			}
		}
		
		if($module_type=='yahoo')
		{
			if (strstr($keywords,','))
			{
				$keywords = str_replace(',','|',$keywords);
				$keywords = '[spyntax]{'.$keywords.'}[/spyntax]';
			}
			$nukeywords  = urlencode($keywords);
			if ($limit_results==0||$limit_results>50) $limit_results=50;
			$y_feed = "http://answers.yahooapis.com/AnswersService/V1/questionSearch?&appid=$yahoo_api_key&query=$nukeywords&type=$z_yahoo_option_type&category_id=$z_yahoo_option_category&region=$z_yahoo_option_region&date_range=$z_yahoo_option_date_range&results=$limit_results&sort=$z_yahoo_option_sorting&search_in=all";
			$source_feed = $y_feed;
		}
		
		//prepare regex searches if needed
		if ($regex_search)
		{
			foreach($regex_search as $k=>$v)
			{
				if (!strstr($regex_search[$k],'/'))
				{
					$regex_search[$k] = "/$regex_search[$k]/";
				}
				
				
			}
			
			$regex_search = implode('***r***',$regex_search);
			$regex_replace = implode('***r***',$regex_replace);
		}
		
		//build start date
		if ($post_frequency=='backdate')
		{
			$time = date('H:i:s');
			$post_frequency_start_date = "$post_frequency_start_date $time";
		}
		else
		{
			$time = date('H:i:s');
			$post_frequency_start_date = "$post_frequency_start_date $time";
		}
		
		if ($custom_field_name)
		{
			$custom_field_name = implode('***',$custom_field_name);
			$custom_field_value = implode('***',$custom_field_value);
		}
		
		//add slashes if needed
		$rss_scrape_content_begin_code = addslashes($rss_scrape_content_begin_code);
		$rss_scrape_content_end_code = addslashes($rss_scrape_content_end_code);
		$rss_scrape_comments_begin_code = addslashes($rss_scrape_comments_begin_code);
		$rss_scrape_comments_end_code = addslashes($rss_scrape_comments_end_code);
		$rss_scrape_names_begin_code = addslashes($rss_scrape_names_begin_code);
		$rss_scrape_names_end_code = addslashes($rss_scrape_names_end_code);
		$post_template = addslashes($post_template);
		$title_template = trim(addslashes($title_template));

	    $query = "UPDATE ".$table_prefix."campaigns SET ";
		$query .="name='$campaign_name',";
		$query .="source='$source',";
		$query .="query='$keywords',";
		$query .="feed='$source_feed',";
		$query .="limit_results='$limit_results',";
		$query .="author='$author',";
		$query .="include_keywords='$include_keywords',";
		$query .="exclude_keywords='$exclude_keywords',";
		$query .="category='$category',";
		$query .="autocategorize='$autocategorize',";
		$query .="autocategorize_search ='$autocategorize_search',";
		$query .="autocategorize_method ='$autocategorize_method',";
		$query .="autocategorize_filter_keywords ='$autocategorize_filter_keywords',";
		$query .="autocategorize_filter_categories ='$autocategorize_filter_categories',";
		$query .="autocategorize_filter_list ='$autocategorize_filter_list',";
		$query .="autotag_method ='$autotag_method',";
		$query .="autotag_custom_tags ='$autotag_custom_tags',";
		$query .="autotag_min ='$autotag_min',";
		$query .="autotag_max ='$autotag_max',";
		$query .="language='$campaign_language',";
		$query .="spin_text='$spin_text',";
		$query .="strip_images='$strip_images',";
		$query .="strip_links='$strip_links',";
		$query .="image_floating='$image_floating',";
		$query .="scrape_profile='$scrape_profile',";
		$query .="regex_search='$regex_search',";
		$query .="regex_replace='$regex_replace',";
		$query .="credit_source='$credit_source',";
		$query .="credit_source_nofollow='$credit_source_nofollow',";
		$query .="credit_source_text='$credit_source_text',";
		$query .="schedule_backdating='$backdating',";
		$query .="schedule_post_frequency='$post_frequency',";
		$query .="schedule_post_date='$post_frequency_start_date',";
		$query .="custom_field_name='$custom_field_name',";
		$query .="custom_field_value='$custom_field_value',";
		$query .="z_affiliate_id='$affiliate_id',";
		$query .="z_bookmark_twitter='$z_bookmark_twitter',";
		$query .="z_bookmark_pixelpipe='$z_bookmark_pixelpipe',";
		$query .="z_rss_scrape_content='$rss_scrape_content',";
		$query .="z_rss_scrape_content_begin_code='$rss_scrape_content_begin_code',";
		$query .="z_rss_scrape_content_end_code='$rss_scrape_content_end_code',";
		$query .="z_rss_scrape_comments='$rss_scrape_comments',";
		$query .="z_rss_scrape_names_begin_code='$rss_scrape_names_begin_code',";
		$query .="z_rss_scrape_names_end_code='$rss_scrape_names_end_code',";
		$query .="z_rss_scrape_comments_begin_code='$rss_scrape_comments_begin_code',";
		$query .="z_rss_scrape_comments_end_code='$rss_scrape_comments_end_code',";
		$query .="z_video_include_description='$video_include_description',";
		$query .="z_yahoo_option_category='$z_yahoo_option_category',";
		$query .="z_yahoo_option_date_range='$z_yahoo_option_date_range',";
		$query .="z_yahoo_option_region='$z_yahoo_option_region',";
		$query .="z_yahoo_option_results_limit='$z_yahoo_option_results_limit',";
		$query .="z_yahoo_option_sorting='$z_yahoo_option_sorting',";
		$query .="z_yahoo_option_type='$z_yahoo_option_type',";		
		$query .="z_title_template='$title_template',";
		$query .="z_post_template='$post_template', ";
		$query .="z_comments_include='$comments_include', ";
		$query .="z_comments_limit='$comments_limit', ";
		$query .="z_remote_publishing_api_bs='$remote_publishing_api_bs', ";
		$query .="z_remote_publishing_api_xmlrpc='$remote_publishing_api_xmlrpc', ";
		$query .="z_remote_publishing_api_xmlrpc_spin='$remote_publishing_api_xmlrpc_spin', ";
		$query .="z_remote_publishing_api_pp_email='$remote_publishing_api_pp_email', ";
		$query .="z_remote_publishing_api_pp_routing='$remote_publishing_api_pp_routing', ";
		$query .="z_remote_publishing_api_email='$remote_publishing_api_email', ";
		$query .="z_remote_publishing_api_email_footer='$remote_publishing_api_email_footer', ";
		$query .="z_post_overwrite='$post_overwrite', ";
		$query .="z_include_keywords_scope='$include_keywords_scope', ";
		$query .="z_exclude_keywords_scope='$exclude_keywords_scope', ";
		$query .="z_post_status='$post_status', ";
		$query .="z_post_type='$post_type' ";
		$query .="WHERE id=$campaign_id";
	    $result = mysql_query($query);
	    if (!$result){ echo $query; echo mysql_error(); exit; }
	
	    echo "<center><br><br><br><br><font color=green>Campaign Saved. </center></font>";
	    exit;
	}
	
	if ($_POST['submit_nature']=="import")
	{
		if ($tbs_spinning==1&&!$_SESSION['tbs'])
		{
			authenticate_tbs();
		}
		
		$include_count = 0;
		
		//build start date
		$time = date('H:i:s');
		$post_frequency_start_date = "$post_frequency_start_date $time";

		
	?>
	    <html>
		<head>

		<script type="text/javascript" src="./../includes/jquery.js"></script>
		<script type="text/javascript" src="./../includes/pagination/jquery.pagination.js"></script>
		<script type="text/javascript" src="./../includes/wymeditor/jquery.wymeditor.pack.js"></script>
		<script type="text/javascript" src="./../includes/wymeditor/jquery.wymeditor.embed.js"></script>
		<link href="./../includes/pagination/pagination.css" media="screen" rel="stylesheet" type="text/css"/>
		<style type='text/css'>
		body
		{
			background-color:#ffffff;
		}
		selects
		{
			max-width:300px;
		}
		</style>
		<script type="text/javascript">

		
		
		
		function pageselectCallback(page_index, jq){
		        //get current id
		        store_id = $('.class_result_content').attr('id');
				cur_id = store_id.replace('id_result_content_','');
				//alert(cur_id);
				//alert(page_index);
				//hide current content
				if (cur_id=='x')
				{
					$('.class_result_content').html(' ');
					$('.class_result_content').attr('id', 'id_result_content_0');
					$('#hiddenresult div.result:eq('+page_index+')').css('display','block');    
					
				}
				else
				{
					$('.class_result_content').attr('id', 'id_result_content_'+page_index);
					$('#hiddenresult div.result:eq('+cur_id+')').css('display','none');    
					$('#hiddenresult div.result:eq('+page_index+')').css('display','block');    
					
				}			
				
                return false;
            }

		
		function initPagination() {
                var num_entries = $('#hiddenresult div.result').length;
                // Create pagination element
                $("#Pagination").pagination(num_entries, {
                    num_edge_entries: 2,
                    num_display_entries: 8,
                    callback: pageselectCallback,
                    items_per_page:1
                });
             }
			 
		$(document).ready(function(){ 					
                initPagination();				
		
				$('.category_button_on').live("click" ,function(){
					//get page id
					var string = this.id.replace('id_category_button_','');
					//alert(string);
					var page_id = string.split("_")[0];
					var cat_id = string.split("_")[1];
					//alert(cat_id);
					
					//get page inputs values
					var theinput = $('#id_category_input_'+page_id).val();
					
					//count commas
					var commas = theinput.split(/,/g).length - 1;
					//alert(commas);
					if (commas>0)
					{
						//remove ,cat_id from inputs value
						new_input = theinput.replace(','+cat_id,'');
					
						//replace old input value with new input value
						$('#id_category_input_'+page_id).val(new_input);
						//alert(new_input);
						
						//change class of item to off
						$('#id_category_button_'+page_id+'_'+cat_id).removeClass('category_button_on');
						$('#id_category_button_'+page_id+'_'+cat_id).addClass('category_button_off'); 
					//else abort
					}
				});
				$('.category_button_off').live("click" ,function(){
					//get page id
					var string = this.id.replace('id_category_button_','');
					var page_id = string.split("_")[0];
					var cat_id = string.split("_")[1];
					//alert(cat_id);
					
					//get page inputs values
					var theinput = $('#id_category_input_'+page_id).val();
					
					//add ,cat_id to inputs value
					var new_input = theinput + ","+cat_id;
					//alert(new_input);
					
					//replace old input value with new input value
					$('#id_category_input_'+page_id).val(new_input);
						
					//change class of item to off
					$('#id_category_button_'+page_id+'_'+cat_id).removeClass('category_button_off');
					$('#id_category_button_'+page_id+'_'+cat_id).addClass('category_button_on'); 
										
					
				});
				
        });
		jQuery(function() {
		    jQuery(".wymeditor").wymeditor();					
		});
		

		 
		</script>
		</head>

		<body>
		<form action="<?php echo $action_url; ?>" id="form_import" method=POST>
		<input type=hidden name='submit_nature' value='import_publish'>
		<input type=hidden name='module_type' value='<?php echo $module_type; ?>'>
		<input type=hidden name='source' value='<?php echo $source; ?>'>
		<input type=hidden name='query' value='<?php echo $keywords; ?>'>	
		<input type=hidden name='author' value='<?php echo $author; ?>'>			
		<input type=hidden name='backdating' value='<?php echo $backdating; ?>'>
		<input type=hidden name='post_frequency' value='<?php echo $post_frequency; ?>'>
		<input type=hidden name='post_frequency_start_date' value='<?php echo $post_frequency_start_date; ?>'>
		<input type=hidden name='strip_links' value='<?php echo $strip_links; ?>'>
		<input type=hidden name='language' value='<?php echo $campaign_language; ?>'>
		<input type=hidden name='spin_text' value='<?php echo $spin_text; ?>'>
		<input type=hidden name='category' value='<?php echo $category; ?>'>
		<input type=hidden name='autocategorize' value='<?php echo $autocategorize; ?>'>
		<input type=hidden name='autocategorize_search' value='<?php echo $autocategorize_search; ?>'>
		<input type=hidden name='autocategorize_method' value='<?php echo $autocategorize_method; ?>'>
		<input type=hidden name='autocategorize_filter_keywords' value='<?php echo $autocategorize_filter_keywords; ?>'>
		<input type=hidden name='autocategorize_filter_categories' value='<?php echo $autocategorize_filter_categories; ?>'>
		<input type=hidden name='autocategorize_filter_list' value='<?php echo $autocategorize_filter_list; ?>'>
		<input type=hidden name='autotag_method' value='<?php echo $autotag_method; ?>'>
		<input type=hidden name='autotag_custom_tags' value='<?php echo $autotag_custom_tags; ?>'>
		<input type=hidden name='autotag_min' value='<?php echo $autotag_min; ?>'>
		<input type=hidden name='autotag_max' value='<?php echo $autotag_min; ?>'>
		<input type=hidden name='image_floating' value='<?php echo $image_floating; ?>'>
		<input type=hidden name='scrape_footprint' value='<?php echo $scrape_footprint; ?>'>
		<input type=hidden name='scrape_content_start' value='<?php echo $scrape_content_start; ?>'>
		<input type=hidden name='scrape_content_start_backup_1' value='<?php echo $scrape_content_start_backup_1; ?>'>
		<input type=hidden name='scrape_content_start_backup_2' value='<?php echo $scrape_content_start_backup_2; ?>'>
		<input type=hidden name='scrape_content_end' value='<?php echo $scrape_content_end; ?>'>
		<input type=hidden name='scrape_content_end_backup_1' value='<?php echo $scrape_content_end_backup_1; ?>'>
		<input type=hidden name='scrape_content_end_backup_2' value='<?php echo $scrape_content_end_backup_2; ?>'>
		<input type=hidden name='scrape_title_start' value='<?php echo $scrape_title_start; ?>'>
		<input type=hidden name='scrape_title_start_backup_1' value='<?php echo $scrape_title_start_backup_1; ?>'>
		<input type=hidden name='scrape_title_start_backup_2' value='<?php echo $scrape_title_start_backup_2; ?>'>
		<input type=hidden name='scrape_title_end' value='<?php echo $scrape_title_end; ?>'>
		<input type=hidden name='scrape_title_end_backup_1' value='<?php echo $scrape_title_end_backup_1; ?>'>
		<input type=hidden name='scrape_title_end_backup_2' value='<?php echo $scrape_title_end_backup_2; ?>'>
		<input type=hidden name='scrape_comments_status' value='<?php echo $scrape_comments_status; ?>'>
		<input type=hidden name='scrape_comments_name_start' value='<?php echo $scrape_comments_name_start; ?>'>
		<input type=hidden name='scrape_comments_name_end' value='<?php echo $scrape_comments_name_end; ?>'>
		<input type=hidden name='scrape_comments_content_start' value='<?php echo $scrape_comments_content_start; ?>'>
		<input type=hidden name='scrape_comments_content_end' value='<?php echo $scrape_comments_content_end; ?>'>
		<input type=hidden name='regex_replace' value='<?php if ($regex_search){ echo implode("***r***",$regex_replace); }?>'>
		<input type=hidden name='regex_search' value='<?php  if ($regex_search){ echo implode("***r***", $regex_search); }?>'>
		<input type=hidden name='credit_source' value='<?php echo $credit_source; ?>'>
		<input type=hidden name='credit_source_nofollow' value='<?php echo $credit_source_nofollow; ?>'>
		<input type=hidden name='credit_source_text' value='<?php echo $credit_source_text; ?>'>
		<input type=hidden name='bookmark_twitter' value='<?php echo $bookmark_twitter; ?>'>
		<input type=hidden name='bookmark_pixelpipe' value='<?php echo $bookmark_pixelpipe; ?>'>
		<input type=hidden name='post_status' value='<?php echo $post_status; ?>'>
		<input type=hidden name='post_type' value='<?php echo $post_type; ?>'>
		<input type=hidden name='comments_include' value='<?php echo $comments_include; ?>'>
		<input type=hidden name='comments_limit' value='<?php echo $comments_limit; ?>'>
		<input type=hidden name='remote_publishing_api_bs' value='<?php echo $remote_publishing_api_bs; ?>'>
		<input type=hidden name='remote_publishing_api_bs_xmlrpc' value='<?php echo $remote_publishing_api_xmlrpc; ?>'>
		<input type=hidden name='remote_publishing_api_pp_email' value='<?php echo $remote_publishing_api_pp_email; ?>'>
		<input type=hidden name='remote_publishing_api_pp_routing' value='<?php echo $remote_publishing_api_pp_routing; ?>'>
		<input type=hidden name='remote_publishing_api_email' value='<?php echo $remote_publishing_api_email; ?>'>
		<input type=hidden name='remote_publishing_api_email_footer' value='<?php echo $remote_publishing_api_email_footer; ?>'>
		<input type=hidden name='post_overwrite' value='<?php echo $post_overwrite; ?>'>
		
	<?php
		
		if ($module_type=='sources')
		{
			$source = urldecode($source);
			if (strstr($keywords,','))
			{
				$keywords = str_replace(',','|',$keywords);
				$keywords = '{'.$keywords.'}';
				$keywords = spyntax($keywords);
			}
			$keywords = str_replace(' ', '+' ,$keywords);
			//echo $keywords; exit;
			$footprint = urlencode($scrape_footprint);
			$keywords = "$keywords+$footprint";
			//get url to includes folder
			$include_url = explode('functions', $current_url);	
			$include_url = "".$include_url[0]."includes/";				
			$site_formated = str_replace("http://", "open*", $source);
			$url = "".$include_url."search3links.php?s=$site_formated&q=$keywords";
			
			$string = quick_curl($url,1);
			
			$link_count = substr_count($string, "<link>");			
			if ($limit_results!=0&&($limit_results<$link_count))
			{
				$link_count = $limit_results;
			}
			//echo $link_count;exit;
			
			for ($i=0;$i<$link_count;$i++)
			{
			   //echo $string; exit;
			   $links[$i] = get_string_between($string, "<link>", "</link>");
			   //echo  $links[$i];
			   $string = str_replace("<link>$links[$i]</link>", "", $string);
			}
			
			//echo get_string_between($string, "<yahoo>", "</yahoo>"); exit;
		}
		if ($module_type=='rss')
		{		    
			if ($campaign_scrape_content==3)
			{
				$source = str_replace('http://', '', $source);
				$source = $blogsense_url."includes/fivefilters/makefulltextfeed.php?url={$source}&max=25&submit=Create+Feed";
			}
			else
			{
				$source = urldecode($source);
			}
			
			$string = quick_curl($source,1);
			$string = htmlspecialchars_decode($string);
			
			//special character work
			if (strstr($string,'gb2312'))
			{
				//echo 1;
				$spc = "gb2312";		   
				$string = mb_convert_encoding($string, 'UTF-8', 'GB2312');
			}
			else
			{
				$spc = "none";
			}
			//echo $string;exit;
			$parameters = discover_rss_parameters($string);
			$string = $parameters['string'];
			$title_start = $parameters['title_start'];
			$title_end = $parameters['title_end'];
			$description_start = $parameters['description_start'];
			$description_end = $parameters['description_end'];
			$link_start = $parameters['link_start'];
			$link_end = $parameters['link_end'];
			$publish_date_start = $parameters['publish_date_start'];
			$publish_date_end = $parameters['publish_date_end'];
			$author_start = $parameters['author_start'];
			$author_end = $parameters['author_end'];
			$google_reader = $parameters['google_reader'];
			

			$link_count = substr_count($string, $link_start);
			if ($limit_results!=0&&($limit_results<$link_count))
			{
				$link_count = $limit_results;
			}
			
			for ($i=0;$i<$link_count;$i++)
			{
				
				$links[$i] = get_string_between($string, $link_start, $link_end);
				//echo $links[$i]; exit;			  
				$string = str_replace("".$link_start."".$links[$i]."".$link_end."", "", $string);
				$links[$i] = clean_cdata($links[$i]);
			}
		}
		
		if ($module_type=='video')
		{	
			if (strstr($keywords,','))
			{
				$keywords = str_replace(',','|',$keywords);
				$keywords = '{'.$keywords.'}';
				$keywords = spyntax($keywords);
			}
			$nukeywords = urlencode($keywords);
			if ($source=='query')
			{
				$source_feed = "http://gdata.youtube.com/feeds/base/videos?q=$nukeywords&key=AI39si6RmbtB6goYpu0MrGKmEeEhg5dIOSdZUClTencT6F_Saf3Wjqp9y55xoJ1PAa_htlx3ArxozpuNiG-jdWzNxMAV-NhvKw";	
			}
			else
			{
				$source_feed = "http://gdata.youtube.com/feeds/api/users/$nukeywords/uploads/?key=AI39si6RmbtB6goYpu0MrGKmEeEhg5dIOSdZUClTencT6F_Saf3Wjqp9y55xoJ1PAa_htlx3ArxozpuNiG-jdWzNxMAV-NhvKw";	
			}
			$string = quick_curl($source_feed,0);

			
			$entry_start = "<feed xmlns=";
			$entry_end ="</generator>";							
			$start = $entry_start;
			$end =  $entry_end;
			$remove = get_string_between($string, $start, $end);
			$string = str_replace ($remove, "" , $string);
			//echo $string; exit;
			
			if ($source=='query')
			{
				$title_start =  "<title type='text'>";
				$title_end =  "</title>";
				$description_start =  "<content type='html'>";
				$description_end =  "From:";
				$link_start = "<link rel='alternate' type='text/html' href='";
				$link_end = "'/>";
				$thumbnail_start = 'src="';
				$thumbnail_end = '"';
			}
			else
			{
				$title_start =  "<title type='text'>";
				$title_end =  "</title>";
				$description_start =  "<content type='text'>";
				$description_end =  "</content>";
				$link_start = "<link rel='alternate' type='text/html' href='";
				$link_end = "'/>";
				$thumbnail_start = "<media:thumbnail url='";
				$thumbnail_end = "'";
			}
		
			$link_count = substr_count($string, $link_start);
			if ($limit_results!=0&&($limit_results<$link_count))
			{
				$link_count = $limit_results;
			}
	
			
			for ($i=0;$i<$link_count;$i++)
			{
				
				$links[$i] = get_string_between($string, $link_start, $link_end);
				//echo $links[$i]; exit;			  
				$string = str_replace("".$link_start."".$links[$i]."".$link_end."", "", $string);
				$links[$i] = clean_cdata($links[$i]);
			}
		}
		
		if($module_type=='yahoo')
		{
			if ($limit_results==0||$limit_results>50) $limit_results=50;
			
			if (strstr($keywords,','))
			{
				$keywords = str_replace(',','|',$keywords);
				$keywords = '{'.$keywords.'}';
				$keywords = spyntax($keywords);
			}
			$keywords = urlencode($keywords);
			$y_feed = "http://answers.yahooapis.com/AnswersService/V1/questionSearch?&appid=YahooDemo&query=$keywords&type=$z_yahoo_option_type&category_id=$z_yahoo_option_category&region=$z_yahoo_option_region&date_range=$z_yahoo_option_date_range&results=$limit_results&sort=$z_yahoo_option_sorting&search_in=all";
			$string = quick_curl($y_feed,0);	

			$item_start =  '<Question';
			$item_end =  '</Question>';
					
			$description_start = '<Content>';
			$description_end =  '</Content>';	
					
			$link_start = "<Link>";
			$link_end = "</Link>";
			
			$title_start = "<Subject>";
			$title_end = "</Subject>";
			
			$link_count = substr_count($string, $link_start);
			if ($limit_results!=0&&($limit_results<$link_count))
			{
				$link_count = $limit_results;
			}
			//echo $link_count;exit;
			
			for ($i=0;$i<$link_count;$i++)
			{
				
				$links[$i] = get_string_between($string, $link_start, $link_end);		  
				$string = str_replace("".$link_start."".$links[$i]."".$link_end."", "", $string);
				$links[$i] = clean_cdata($links[$i]);
			}	
		}			
			
		if($module_type=='amazon')
		{
			$source = urldecode($source);	
			$string = quick_curl($source,0);				
			
			//get blocks
			if (strpos($string, 'zg_centerColumn')&&strpos($string, 'zg_pagination'))
			{
				$search_results = preg_match('/zg_centerColumn(.*?)zg_pagination/si',$string, $match);
				$string = $match[0];
				
				preg_match_all('/zg_rank(.*?)zg_clear/si', $string, $matches );
				$block_batch = $matches[0];
				$block_batch = array_unique($block_batch);
			}
			else if (strpos($string, 'resultCount')&&strpos($string, 'bottomBar'))
			{
				$string = explode('srNum_',$string);
				array_shift($string);
				$string = implode('srNum_',$string);
				if (strpos($string, 'srNum_'))
				{
					preg_match_all('/srNum_(.*?)srNum_/si', $string, $matches );
					$block_batch = $matches[0];
				}
				else
				{

					preg_match_all('/srNum_(.*?)result_/si', $string, $matches );
					$block_batch = $matches[0];
				}				
				$block_batch = array_unique($block_batch);
			}
			else if (strpos($string, 'zg_sparseListItem')&&strpos($string, 'zg_pagination'))
			{
				$search_results = preg_match('/zg_sparseListItem(.*?)zg_pagination/si',$string, $match);
				$string = $match[0];
				
				preg_match_all('/zg_image(.*?)\/div/si', $string, $matches );
				
				foreach ($matches[0] as $aa => $bb)
				{
					$matches[0][$aa] = str_replace('href="','href="http://www.amazon.com', $matches[0][$aa]);
				}
				$block_batch = $matches[0];
				$block_batch = array_unique($block_batch);
			}
			else
			{
				 echo "BlogSense cannot find any items to source. If you using a valid amazon search url please notify the administrator of this error.<br><br><hr>$string";exit;
			}
			
					
			if (strstr($campaign_feed, "amazon.de"))
			{
				 $domain = "de";
			}
			else if (strstr($campaign_feed, "amazon.co.uk"))
			{
				$domain = "co.uk";
			}
			else
			{
				$domain = "com";
			}
			
			if (count($block_batch)==0)
			{
			 echo "BlogSense cannot find any items to source. If you using a valid amazon search url please notify the administrator of this error.<br><br><hr>$string";exit;
			}
			
			if (!$amazon_aws_access_key||!$amazon_secret_access_key)
			{
			 echo "BlogSense cannot detect your aws access key or your amazon secret key. These are required to run this module.";exit;
			}

			$link_count = count($links);
			
			//echo $limit_results;exit;
			foreach($block_batch as $k=>$v)
			{
				if ($limit_results!=0&&$k<$limit_results||$limit_results==0)
				{
					//echo $k; 
					//$v = clean_html($v);
					if (strstr($v,'http://ecx.images-amazon'))
					{
						//get links
						preg_match('/http:\/\/(.*?)dp\/[A-Z0-9.-]{1,10}\//', $v, $match);	
						$links[] = $match[0];
					}	
				}
			}	
		}
		
		if ($module_type=='fileimport')
		{
			if ($campaign_name=='text_import')
			{
				if ($source=="parent")
				{
					$source="";
					$folder = "parent";
				}
				else 
				{ 
					$folder = $source;
				}
				  
				//open folder and discover files
				$links = files_in_directory("./../my-articles/$source");
				$links = bs_simple_sort($links,$z_yahoo_option_sorting);
				$file_count = count($links);
				if ($limit_results!=0&&($limit_results<$file_count))
				{
					$links = array_slice($links, 0 , $limit_results);
				}
				
			}
			else
			{
				$query = ',';
				//$lines = file("./../my-csv-files/$source");
				$lines = csv_to_array("./../my-csv-files/$source", $keywords);
				$cols = $lines[0];
				$col_count = count($cols);
				$line_count = count($lines);
				
				if ($limit_results!=0&&($limit_results<$line_count))
				{
					$line_count = $limit_results;
				}
				
				for ($i=1;$i<$line_count;$i++)
				{
					$this_row = $lines[$i];
					foreach ($cols as $a => $b)
					{
						$b = trim($b);
						$rows[$i][$b] = $this_row[$a];
					}
				}
				
				$links = $rows;
				$links = bs_simple_sort($links,$z_yahoo_option_sorting);
				echo "<input type=hidden name='csv' value='on'>";
			}
		}
		if ($module_type=='keywords')
		{
			if ($source=='wptraffictools')
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
			
				$links = $wptt_keywords;
			}
			if ($source=='ranktracker')
			{
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
				$links = $rt_keywords;
			}
			
			$links = explode(';',$keywords);
		}
		
		echo "<input type=hidden name='links' value='".@implode(";",$links)."'>";
        echo "<div align=middle id=results><div align=left style='width:700px'>";
		echo "<i>".count($links)." Items Sourced</i>";
		echo "<hr>";
		echo " <div id='Pagination' class='pagination'></div>
				<br style='clear:both;' />
				<span  class='class_result_content' id=id_result_content_x>
					<center><br><img src='./../nav/loading.gif'></center>
				</span>
				";
		echo "<div id='hiddenresult' >	";
	
		if (!$links) {echo "no articles found. please revise."; exit;}
		foreach ($links as $key=>$value)
		{
			$title = "";
			$description = "";
			$link = $value; 
			$youtube_vids = "";
			$comments_names = "";
			$comments_content = "";
			
		    if ($module_type=='sources')
			{
				if ($campaign_scrape_content==1)
				{	
					$value = str_replace('http://', '', $value);
					$value = $blogsense_url."includes/fivefilters/makefulltextfeed.php?url={$value}&max=1&links=preserve&submit=Create+Feed";
					$string = quick_curl($value,1);
					
					$string = explode('<item>',$string);
					$string = $string[1];
					//echo $string; exit;
					
					//echo $string; exit;
					$title = get_string_between($string, '<title>','</title>');
					$description = get_string_between($string, '<description>','</description>');
					$description = htmlspecialchars_decode($description);
				}
				else
				{
					$string = quick_curl($value,1);		
					
					$title =  get_string_between($string, $scrape_title_start, $scrape_title_end);

					//run through backups
					if (!$title&&$scrape_title_start_backup_1)
					{
						$title =  get_string_between($string, $scrape_title_start_backup_1, $scrape_title_end_backup_1);
						//echo "<hr>"; 
						//echo $scrape_title_start_backup_1;exit;
					}
					if (!$title&&$scrape_title_start_backup_2)
					{
						$title =  get_string_between($string, $scrape_title_start_backup_2, $scrape_title_end_backup_2);
					}
					$title = special_htmlentities($title);
					
					//check initial
					$description = get_string_between($string, $scrape_content_start, $scrape_content_end);
					$scrape_content_start_search = $scrape_content_start;
					$scrape_content_end_search = $scrape_content_end;
					
					//run through backups
					if (!$description&&$scrape_content_start_backup_1)
					{
						//echo "miss, now doing backup 1<br>";
						$description =  get_string_between($string, $scrape_content_start_backup_1, $scrape_content_end_backup_1);
						$scrape_content_start_search = $scrape_content_start_backup_1;
						$scrape_content_end_search = $scrape_content_end_backup_1;
					}
					if (!$description&&$scrape_content_start_backup_2)
					{
						//echo "miss, now doing backup 2<br>";
						$description =  get_string_between($string, $scrape_content_start_backup_2, $scrape_content_end_backup_2);
						$scrape_content_start_search = $scrape_content_start_backup_2;
						$scrape_content_end_search = $scrape_content_end_backup_2;
					}
					
					//if ($key==1){ echo $scrape_title_start;exit;}			
				
					
					$bc_status = "";
					$ec_status = "";
					if (strstr($string, $scrape_content_start_search))
					{
						$bc_status = 'found';
					}
					else
					{
						$bc_status = 'not found : ( ';
					}
					if (strstr($string, $scrape_content_end_search))
					{
						$ec_status = 'found';
					}
					else
					{
						$ec_status = 'not found';
					}
				
					if ($scrape_comments_status==1&&$comments_include==1)
					{
						//echo $scrape_comments_names_start;exit;
						$string = str_replace($description, "", $string);
						$comments_array = scrape_comments($string,$scrape_comments_name_start,$scrape_comments_name_end,$scrape_comments_content_start,$scrape_comments_content_end);
						$comments_names = $comments_array[0];
						$comments_content = $comments_array[1];
						//$comments_dates = $comments_array[2];
						
						//if translate is on then translate and explode comments				
						if ($campaign_language!="no translation")
						{
								$translate_comments = implode("***", $comments_content);
								$translate_comments = spin_text($translate_comments , $link, $title, $image_floating,$campaign_language);
								$translate_comments = special_htmlentities($translate_comments);
								$comments_content = explode("***", $translate_comments);
						}
						
						if ($spin_text==1||$spin_text==3)
						{
							$language="spin";					
							if ($comments_content)
							{
								if (count($comments_content)>0)
								{
									$comments_content = implode('***', $comments_content);
									$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
									$comments_content = explode('***',$comments_content);
								}
							}
						}	
						
						//if spin text is on then spin comments		
						if ($spin_text==5||$spin_text==6)
						{
							$language="salt";
							
							if ($comments_content)
							{
								if (count($comments_content)>0)
								{
									$comments_content = implode(':::', $comments_content);
									$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
									$comments_content = explode(':::',$comments_content);
								}
							}
						}
						
						
					}
				}
			}
			
			if ($module_type=='rss'||$module_type=='video')
			{						
				//pull the title from rss
				//echo $string; exit;
				$title = get_string_between($string, $title_start, $title_end);
				$string = str_replace("$title_start$title$title_end", "", $string);
				//echo $title; exit;
				
				//pull the description from rss						   
				$description = get_string_between($string, $description_start, $description_end);	
				if ($description)
				{
					$string = str_replace_once("{$description_start}{$description}{$description_end}", "" , $string); 
				}
				else
				{
					$string = str_replace_once("{$description_start}", "" , $string);
				}
				
				//echo $description; exit;
				
				//echo $module_type;exit;
				if ($module_type=='video')
				{	
					if ($source=='query')
					{
						$description = htmlspecialchars_decode($description);
						$thumbnail = get_string_between($description, $thumbnail_start, $thumbnail_end);
						$description = get_string_between($description, '<span>','</span>');
						//echo htmlspecialchars_decode($description); exit;
					}
					else
					{
						$start = "<media:thumbnail url='";		
						$end = "'";
						$thumbnail = get_string_between($string, $thumbnail_start, $thumbnail_end);
						$string = str_replace("$thumbnail_start$thumbnail$thumbnail_end", "" , $string);
					}
					
	
					if ($comments_include==1)
					{
						$scrape_comments_status=1;
						//get video id
						$flag = "http://www.youtube.com/watch?v=";
						$pos_start = strpos($link, $flag) + strlen($flag);
						$vid_id = substr($link, $pos_start, 11);
								
						//echo $vid_id; exit;
						$comment_url = "http://www.youtube.com/all_comments?v=$vid_id";
						
						//echo $comment_url;exit;
						//get string and prepare tag guidelines
						$c_string = quick_curl($comment_url,0);
						//echo $c_string; exit;
						$names_start = '<div class="metadata">';
						$names_end = '</a>';			
						$content_start = '<div class="comment-text">';
						$content_end = '</div>';
						
						if (!strstr($c_string, $content_start))
						{					
							$content_start = '<div class="comment-text" dir="ltr">';
							$content_end = '</div>';
						}
						
						//$string = str_replace($description, "", $c_string);
						$comments_array = scrape_comments($c_string,$names_start,$names_end,$content_start,$content_end);
						$comments_names = $comments_array[0];
						$comments_content = $comments_array[1];
						//print_r($comments_names);exit;
						if ($campaign_language!="no translation")
						{
								$translate_comments = implode("***", $comments_content);
								$translate_comments = spin_text($translate_comments , $link, $title, $image_floating,$campaign_language);
								$translate_comments = special_htmlentities($translate_comments);
								$comments_content = explode("***", $translate_comments);
						}
						
						if ($spin_text==1||$spin_text==3)
						{
							$language="spin";					
							if ($comments_content)
							{
								if (count($comments_content)>0)
								{
									$comments_content = implode('***', $comments_content);
									$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
									$comments_content = explode('***',$comments_content);
								}
							}
						}	
						
						//if spin text is on then spin comments		
						if ($spin_text==5||$spin_text==6)
						{
							$language="salt";
							
							if ($comments_content)
							{
								if (count($comments_content)>0)
								{
									$comments_content = implode(':::', $comments_content);
									$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
									$comments_content = explode(':::',$comments_content);
								}
							}
						}
					}
				}
				
				//pull author				   
				$original_author = get_string_between($string, $author_start, $author_end);			   
				$string = str_replace_once("{$author_start}{$original_author}{$author_end}", " ***a***" , $string); 
				
				//echo $string;exit;				
				$title = clean_cdata($title);
				$description = clean_cdata($description);
				
				//echo $campaign_scrape_content;exit;
				if ($campaign_scrape_content==1)
				{		
					//echo 1; exit;
				    $string_focus = quick_curl($value,1);	
					
					//echo $string_focus;exit;
					//echo $rss_begin_code; exit;
					//echo $rss_end_code; exit;
					$description = get_string_between($string_focus, $campaign_scrape_content_begin_code, $campaign_scrape_content_end_code);
					//echo $description;exit;
			
					if (!stristr($description,"<br>")&&!stristr($description,"<br />")&&!stristr($description,"<p>")&&!stristr($description,"<div>"))
					{
						$description = preg_replace("/\r\n|\n|\r/", "<br>", $description);
						//remove excessive tabbing
						while (strstr($description, "\t"))
						{
							//echo 1;exit;
							$description = str_replace("\t",'',$description);
						}
						while (strstr($description, '<br><br><br>'))
						{
							//echo 1;exit;
							$description = str_replace('<br><br><br>','<br><br>',$description);
						}
					}				
					
					$bc_status = "";
					$ec_status = "";
					if (strstr($string_focus, $rss_begin_code))
					{
						$bc_status = 'found';
					}
					else
					{
						$bc_status = 'not found';
					}
					if (strstr($string_focus, $rss_end_code))
					{
						$ec_status = 'found';
					}
					else
					{
						$ec_status = 'not found';
					}
				}
				
				if ($rss_scrape_comments==1)
				{
					$scrape_comments_status=1;
					//echo $link;exit;
					$comments_string = quick_curl($link,1);
					$comments_string = str_replace($description, "", $comments_string);
					$comments_array = scrape_comments($comments_string,stripslashes($rss_scrape_names_begin_code),stripslashes($rss_scrape_names_end_code),stripslashes($rss_scrape_comments_begin_code),stripslashes($rss_scrape_comments_end_code));
					$comments_names = $comments_array[0];
					$comments_content = $comments_array[1];
					//$comments_dates = $comments_array[2];
					//echo $comments_string;exit;
					//echo $rss_scrape_comments_end_code;
					//print_r($comments_content);exit;
					//if translate is on then translate and explode comments				
					if ($campaign_language!="no translation")
					{
							$translate_comments = implode("***", $comments_content);
							$translate_comments = spin_text($translate_comments , $link, $title, $image_floating,$campaign_language);
							$translate_comments = special_htmlentities($translate_comments);
							$comments_content = explode("***", $translate_comments);
					}
					
					if ($spin_text==1||$spin_text==3)
					{
						$language="spin";					
						if ($comments_content)
						{
							if (count($comments_content)>0)
							{
								$comments_content = implode('***', $comments_content);
								$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
								$comments_content = explode('***',$comments_content);
							}
						}
					}	
					
					//if spin text is on then spin comments		
					if ($spin_text==5||$spin_text==6)
					{
						$language="salt";
						
						if ($comments_content)
						{
							if (count($comments_content)>0)
							{
								$comments_content = implode(':::', $comments_content);
								$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
								$comments_content = explode(':::',$comments_content);
							}
						}
					}
					
				}
				
			}			
			if ($module_type=='fileimport')
			{
				 //echo 1; exit;
				 //open article into string
				if ($campaign_name=='text_import')
				{
					if ($source=="parent")
					{
						 $open = fopen("./../my-articles/$value", "r");
						 $string = fread($open, filesize("./../my-articles/$value"));
						 fclose($open);	
					}
					else
					{ 
						 $open = fopen("./../my-articles/$source/$value", "r");
						 $string = fread($open, filesize("./../my-articles/$source/$value"));
						 fclose($open);
					}
					 
					if ($keywords=='filename')
					{
						$title = explode(".",$value);
						$title = $title[0];
						$description = $string;
					}
					else
					{
						$content = file("./../my-articles/$source/$value");
						$title =  $content[0];
						$description = $string;
						$description = str_replace_once($title, '', $description);
					}
					 
					if (!preg_match("/([\<])([^\>]{1,})*([\>])/i", $description)) {
						//echo 1; exit;
						$description = nl2br($description);
					} 
					$link = $value;
				}
				else
				{
					$title = $title_template;
					$description = $post_template;
					if (strstr($category,'csv:'))
					{
						$this_category = array();
						$this_category = explode(':',$category);
					}
					foreach ($cols as $k=>$v)
					{
						$v = trim($v);
						
						//echo count($cols);
						//echo count($rows[$key]);
						//print_r($cols);
						//print_r($rows[$key]);exit;
						$description = str_replace("%{$v}%", $rows[$key][$v], $description);
						$title = str_replace("%{$v}%", $rows[$key][$v], $title);
						
						if (strstr($category,'csv:'))
						{
							$this_category[1] = str_replace("%{$v}%", $rows[$key][$v], $this_category[1]);
						}
						
						if ($campaign_custom_field_name)
						{
							foreach ($campaign_custom_field_name as $a=>$b)
							{
								$custom_field_value[$a] = str_replace("%{$v}%", $rows[$k][$v], $custom_field_value[$a]);
							}
						}
						//echo $title;
						//echo "<hr>";
					}
					//echo $title; exit;
				}
				
			}
			
			if ($module_type=='yahoo')
			{
				$chunk = get_string_between($string, $item_start, $item_end);
				$string = str_replace_once($item_start, "", $string);
				$question_id = get_string_between($chunk, 'id="', '"');
				
				$title = get_string_between($chunk, $title_start, $title_end);		
				$description = get_string_between($chunk, $description_start, $description_end);		
				
				$answers_count = get_string_between($chunk, "<NumAnswers>", "</NumAnswers>");
				
				$scrape_comments_status=1;
				$comments_link = "http://answers.yahooapis.com/AnswersService/V1/getQuestion?appid=YahooDemo&question_id=$question_id";
				$comments_string = quick_curl($comments_link,0);
				$comments_string = get_string_between($comments_string, "<Answers>", "</Answers>");
				
				for ($n=0;$n<$answers_count;$n++)
				{
					$comments_names[] = get_string_between($comments_string, "<UserNick>", "</UserNick>");
					$comments_string = str_replace_once("<UserNick>" , "", $comments_string);
					
					$comments_content[] = get_string_between($comments_string, "<Content>", "</Content>");
					$comments_string = str_replace_once("<Content>" , "", $comments_string);			
				}
				
				if ($comments_limit!=0)
				{
					$comments_names = array_slice($comments_names, 0, $comments_limit);
					$comments_content = array_slice($comments_content, 0, $comments_limit);
				}
				
				//if translate is on then translate and explode comments				
				if ($campaign_language!="no translation")
				{
						$translate_comments = implode("***", $comments_content);
						$translate_comments = spin_text($translate_comments , $link, $title, $image_floating,$campaign_language);
						$translate_comments = special_htmlentities($translate_comments);
						$comments_content = explode("***", $translate_comments);
				}
				
				if ($spin_text==1||$spin_text==3)
				{
					$language="spin";					
					if ($comments_content)
					{
						if (count($comments_content)>0)
						{
							$comments_content = implode('***', $comments_content);
							$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
							$comments_content = explode('***',$comments_content);
						}
					}
				}	
				
				if ($spin_text==5||$spin_text==6)
				{
					$language="salt";
					
					if ($comments_content)
					{
						if (count($comments_content)>0)
						{
							$comments_content = implode(':::', $comments_content);
							$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
							$comments_content = explode(':::',$comments_content);
						}
					}
				}
			}
			
			if ($module_type=='amazon')
			{
				preg_match_all('/dp\/(.*?)\//',$links[$key],$match);
				$asin = $match[1][0];
				$link = "http://www.amazon.$domain/dp/$asin/?tag={$amazon_affiliate_id}";	
				
				$xml_result_url = "$blogsense_url/includes/i_amazon_calls.php?locale={$domain}&aws_access_key={$amazon_aws_access_key}&secret_access_key={$amazon_secret_access_key}&asin={$asin}";
				$xml_string = quick_curl($xml_result_url,0);

				//echo $xml_result_url;exit;
				
				$title = "";
				$amazon_params = array();
			
				$amazon_params['original_title'] = get_string_between($xml_string, '<Title>', '</Title>');
				$amazon_params['small_image'] = get_string_between($xml_string, '<SmallImage>', '</SmallImage>');
				$amazon_params['small_image'] = get_string_between($amazon_params['small_image'], '<URL>', '</URL>');
				$amazon_params['medium_image'] = get_string_between($xml_string, '<MediumImage>', '</MediumImage>');
				$amazon_params['medium_image'] = get_string_between($amazon_params['medium_image'], '<URL>', '</URL>');
				$amazon_params['large_image'] = get_string_between($xml_string, '<LargeImage>', '</LargeImage>');
				$amazon_params['large_image'] = get_string_between($amazon_params['large_image'], '<URL>', '</URL>');
				$amazon_params['list_price'] = get_string_between($xml_string, '<FormattedPrice>', '</FormattedPrice>');
				$amazon_params['amazon_brand'] = get_string_between($xml_string, '<Studio>', '</Studio>');
				$amazon_params['amazon_model'] = get_string_between($xml_string, '<Model>', '</Model>');
				if (!$amazon_params['amazon_model'])
				{
					$amazon_params['amazon_model'] = get_string_between($xml_string, '<MPN>', '</MPN>');
				}
				if (!$amazon_params['list_price'])
				{ 
					$amazon_params['list_price'] = "price n/a";
				}
		
				
				//get editorial reviews
				$editorial_review_source = array();
				$editorial_review_content = array();		
				$s = 0;
				while (strstr($xml_string, '<EditorialReview>')&&$s<200)
				{
					$block = get_string_between ($xml_string,'<EditorialReview>','</EditorialReview>');
					$editorial_review_source[] =  get_string_between ($block,'<Source>','</Source>');
					$editorial_review_content[] =  get_string_between ($block,'<Content>','</Content>');
					$xml_string = str_replace_once('<EditorialReview>','',$xml_string);
					$s++;
				}
				
				$manufacturer_product_description= "";
				$amazon_product_description = "";
				$amazon_review = "";
				foreach ($editorial_review_source as $k=>$v)
				{
					//echo $v;
					//echo "<hr>";
					if ($v=='Product Description')
					{
						$manufacturer_product_description = $editorial_review_content[$k];
					}
					if ($v=='Amazon.com Product Description')
					{
						$amazon_product_description = $editorial_review_content[$k];
					}
					if ($v=='Amazon.com Review')
					{
						$amazon_review = $editorial_review_content[$k];
					}
				}
				
				//get features
				$amazon_feature = array();
				$s=0;
				while (strstr($xml_string, '<Feature>')&&$s<200)
				{
					$amazon_feature[] = get_string_between ($xml_string,'<Feature>','</Feature>');
					$xml_string = str_replace_once('<Feature>','',$xml_string);
					$s++;
				}
				
				$amazon_features = "<ul>";
				foreach ($amazon_feature as $k=>$v)
				{
					$amazon_features .= "<li>$v</li>";
				}
				$amazon_features .= "</ul>";
				
				
				//open item page and check for comments. 
				$comments_url = "http://www.amazon.{$domain}/product-reviews/$asin/ref=cm_cr_pr_helpful";
				$comments_string = quick_curl($comments_url,0);
				//echo $comments_string;
				//echo "<hr>";
				$comments_string = explode('Most Helpful First',$comments_string);
				$comments_string = $comments_string[1];
				
				//echo $comments_string;exit;
				preg_match_all('/review\/[A-Z0-9.-]{1,14}\//', $comments_string, $matches );
				$comment_links = $matches[0];
				$comment_links = array_unique($comment_links);
				sort($comment_links);
				
				$blocks = explode('<!-- BOUNDARY -->',$comments_string);
				//print_r($blocks);exit;
				array_shift($blocks);
				
				$j=0;
				foreach ($comment_links as $k=>$v)
				{
				

					$this_block = $blocks[$k];
					$this_block = explode('</b>', $this_block);
					$this_block = $this_block[2];
					$content = get_string_between($this_block, '</div>','<div');

					$comments_string = str_replace_once('<!-- BOUNDARY -->','',$comments_string);
					$s=0;
					while (strstr($content, 'div')&&$s<200)
					{
						$content = preg_replace('/div(.*?)\/div/si','', $content);
						$s++;
					}	
					$content = strip_tags($content, '<br>');
					$content = trim($content);
					//echo $content; 
					//echo "<hr>";
					if ($content)
					{
						$comment_content[$j] = $content;	
						$comment_author[$j] = strip_tags(get_string_between($blocks[$k], 'By','</a>'));
						$j++;
					}
				
				}
				
				$amazon_params['product_description'] = $manufacturer_product_description."<br><br>".$amazon_product_description."<br><br>".$amazon_review;
				
				if (!$amazon_features)
				{
					$amazon_params['amazon_features'] ='n/a';
				}
				else
				{
					$amazon_params['amazon_features'] = $amazon_features;
				}
				
				//print_r($comment_content);
				
				if (!$comment_content[0]){$comment_content[0]=='n/a';}
				if (!$comment_content[1]){$comment_content[1]=='n/a';}
				if (!$comment_content[2]){$comment_content[2]=='n/a';}
				
				$amazon_params['comment_content'] = $comment_content;
				$amazon_params['comment_author'] = $comment_author;
				$amazon_params['buyitnow_button'] = $store_images_full_url."btn_amazon.gif";
				
				$description = $post_template;
				$title = $title_template;
				
				$title = hook_amazon($title,$amazon_params);
				$description = hook_amazon($description,$amazon_params);				
				
				@array_shift($comment_author);
				@array_shift($comment_author);
				@array_shift($comment_author);
				@array_shift($comment_content);
				@array_shift($comment_content);
				@array_shift($comment_content);
				$comments_names = $comment_author;
				$comments_content = $comment_content;
				
				if ($comments_limit!=0)
				{
					$comments_names = array_slice($comments_names, 0, $comments_limit);
					$comments_content = array_slice($comments_content, 0, $comments_limit);
				}
				
				$scrape_comments_status=1;
				
				//if translate is on then translate and explode comments				
				if ($campaign_language!="no translation")
				{
						$translate_comments = implode("***", $comments_content);
						$translate_comments = spin_text($translate_comments , $link, $title, $image_floating,$campaign_language);
						$translate_comments = special_htmlentities($translate_comments);
						$comments_content = explode("***", $translate_comments);
				}
				
				
				
				if ($spin_text==1||$spin_text==3)
				{
					$language="spin";					
					if ($comments_content)
					{
						if (count($comments_content)>0)
						{
							$comments_content = implode('***', $comments_content);
							$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
							$comments_content = explode('***',$comments_content);
						}
					}
				}

				if ($spin_text==5||$spin_text==6)
				{
					$language="salt";
					
					if ($comments_content)
					{
						if (count($comments_content)>0)
						{
							$comments_content = implode(':::', $comments_content);
							$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
							$comments_content = explode(':::',$comments_content);
						}
					}
				}
				
			}
			
			if ($module_type=='keywords')
			{
				$this_title_template  = $title_template;
				$this_title_template = str_replace("%keyword%",$value,$this_title_template);
				$this_post_template  = 	$post_template;
				$this_post_template = str_replace("%keyword%",$value,$this_post_template);
			
				$description_content = hook_content($this_post_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$description = $description_content;
				
				//populate title template
				$title_content = hook_content($this_title_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$title= $title_content;	
				
				$link = addslashes($title);
				$links[] = $link;
			}
			
			//check for youtube videos
			$o=0;
			while (strstr($description, '<param name="movie" value="http://www.youtube.com')&&$module_type!='video')
			{
				$obj = get_string_between($description, '<object','</object>');
				$obj = "<object".$obj."</object>";
				
				if (strstr($obj, 'youtube'))
				{
					$youtube_vids[] = $obj;
					$description = str_replace($obj,"***obj:$o***",$description);
					$o++;
				}
			}
			
			//remove links from description if remove_link is on
			if ($strip_links==1)
			{
				$description = strip_tags($description,'<pre><img><div><table><tr><td><i><b><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><object><embed><date><font><li><ul>');
			}
			if ($strip_links==2)
			{				
				//echo $tags; exit;
				$description = links_to_tag_links($description, $blog_url);
				//echo $description; exit;
			}
			if ($strip_links==3)
			{						
				$description = links_to_search_links($description, $blog_url);
			}
			
			//remove links from description if remove_link is on
			if ($strip_images==1)
			{
				$description = strip_tags($description,'<pre><div><table><tr><td><i><b><a><p><span><u><font><tbody><h1><h2><h3><h4><embed><object><center><blockquote><date>');
			}
		

			if (!$title) {$title = "Unable to Source Title";}			
			if (strlen($description)<10) 
			{
			    $description = "Unable to Source Description. <br><br>Begin-Code status : $bc_status <br> End-Code Status: $ec_status"; 				
			}

			//strip post of uneeded html
			$title = replace_trash_characters($title);
			$title = htmlspecialchars_decode($title);
			$title = strip_tags($title);
			$title = trim($title);
										
			//spin text					
			if ($spin_text==1)
			{
				$language="spin";
				$description = spin_text($description, $link, $title, $campaign_image_floating,$language);
				$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
			}	

			//spin text					
			if ($spin_text==2)
			{
				$language="spin";
				$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
			}	
			
			//spin text					
			if ($spin_text==3)
			{
				$language="spin";
				$description = spin_text($description, $link, $title, $campaign_image_floating,$language);
			}	
			
			//spin text					
			if ($spin_text==4)
			{
				$language="salt";
				$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
			}
			
			//spin text					
			if ($spin_text==5)
			{
				$language="salt";
				$description = spin_text($description, $link, $title, $campaign_image_floating, $language);
			}
			
			//spin text					
			if ($spin_text==6)
			{
				$language="salt";
				$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
				$description = spin_text($description, $link, $title, $campaign_image_floating, $language);
			}
			
			//translate						
			if ($campaign_language!="no translation")
			{
				$title = spin_text($title, $link, $title, $image_floating,$campaign_language);
				$description = spin_text($description, $link, $title, $image_floating="right",$campaign_language);
				$title = special_htmlentities($title);
				$description = special_htmlentities($description);
			}

            
			//if video and source is youtube
			if ($module_type=='video')
			{	
				//save thumbnail to own server	
				if ($store_images==1) 
				{
					$thumbnail = save_image($thumbnail,$title, 1);
				}
				//echo $thumbnail; exit;
				//make description pretty
			
				$lite_description = strip_tags($description);
				$lite_description = str_replace($title,'',$lite_description);
				$description = array('video', $thumbnail, $lite_description);
				
				//get video id
				$flag = "http://www.youtube.com/watch?v=";
				$pos_start = strpos($link, $flag) + strlen($flag);
				$vid_id = substr($link, $pos_start, 11);
				
			}			
			
			//store any images found before template formatting
			$images = bs_get_images($description);
			
			if ($module_type=='amazon')
			{
				//echo $description;exit;
				$description_content = hook_content($description,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$description = $description_content;

				$title_content = hook_content($title,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$title = $title_content;		
			}
			if ($module_type=='fileimport'&&$campaign_name=='csv_import')
			{
				$description_content = hook_content($description,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$description = $description_content;
				
				$title_content = hook_content($title,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$title = $title_content;
			}
			if ($module_type=='rss'||$module_type=='sources'||$module_type=='fileimport'&&$campaign_name!='csv_import')
			{
				$description_content = hook_content($post_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$description = $description_content;
				
				$title_content = hook_content($title_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$title = $title_content;	
			}
			if ($module_type=='video')
			{
				//echo print_r($post_template);
				//print_r($description);
				$description_content = hook_content($post_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$description = $description_content;

				$title_content = hook_content($title_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$title = $title_content;	
			}
			
			
			
			//run text manipulation functions
			if ($regex_search)
			{	
				//clean preg if needed
				foreach($regex_search as $k=>$v)
				{
					if (!strstr($regex_search[$k],'/'))
					{
						$regex_search[$k] = "/$regex_search[$k]/";
					}
					
					
				}
				$description = preg_replace($regex_search,$regex_replace, $description);			  
				$title = preg_replace($regex_search,$regex_replace, $title);			  
				// echo $description; exit;
			}
			
			//strip tags, mend unbalanced divs, remove javascript
			//strip tags, mend unbalanced divs, javascript
			if ($google_reader==0||$module_type!='fileimport')
			{
				$description = clean_html($description);
			}

			//reinsert youtube videos if there
			if ($youtube_vids)
			{
				foreach($youtube_vids as $a=>$b)
				{
					$description = str_replace("***obj:$a***","$youtube_vids[$a]", $description);
				}
			}
			
			//credit source 
			if ($credit_source==1)
			{
				if ($credit_source_nofollow==1)
				{
				   $nofollow = "rel=nofollow";
				}
				$description .= "<br><br><a href=\"$value\" target=_blank $nofollow>$credit_source_text</a>";
			}
	
			if (strstr($description,"Unable to Source Description")||strstr($title,"Unable to Source Title")||!$title||!$description)
			{
			   $checked="";
			}
			else
			{
			  $checked="checked=true";
			}
			
			$pass=1;
			
			if ($module_type=='rss')
			{
				if ($include_keywords!="Separate with commas.")
				{
					$n_include_keywords = explode(",",$include_keywords);
					$n_include_keywords = array_filter($n_include_keywords);
					$dc=0;
					$pass=0;
					foreach ($n_include_keywords AS $k=>$v)
					{

						if ($include_keywords_scope==1)
						{
							if (!stristr($title, $v))
							{
								$pass = 1;										
							}	
						}
						else if ($include_keywords_scope==2)
						{
							if (!stristr($description, $v))
							{
								$pass = 1;										
							}	
						}
						else 
						{
							if (!stristr($title, $v)||!stristr($description, $v))
							{
								$pass = 1;										
							}	
						}				
					}		
				}				
				if ($pass==1)
				{ 
					
					if ($exclude_keywords!="Separate with commas.")
					{
						$n_exclude_keywords = explode(",",$exclude_keywords);
						$n_exclude_keywords = array_filter($n_exclude_keywords);
						$dc=0;
						foreach ($n_exclude_keywords AS $k => $v)
						{					
							if ($exclude_keywords_scope==1)
							{
								if (stristr($title, $v))
								{
									$pass = 0;										
								}	
							}
							else if ($exclude_keywords_scope==2)
							{
								if (stristr($description, $v))
								{
									$pass = 0;										
								}	
							}
							else 
							{
								if (stristr($title, $v)||stristr($description, $v))
								{
									$pass = 0;										
								}	
							}
						}
					}
				}
			}
			
			//prepare custom fields
			if ($custom_field_name)
			{
				//echo $custom_field_value;exit;
				//print_r($custom_field_value);exit;
				foreach ($custom_field_value as $k=>$v)
				{
					$v = hook_amazon($v,$amazon_params);	
					$v = hook_content($v,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);					
					echo "<input type=hidden name='custom_field_name_{$key}[]' value='$custom_field_name[$k]'>";
					echo "<input type=hidden name='custom_field_value_{$key}[]' value='$v'>";
				}			
				
			}
			
			
			
			if ($pass==1)
			{
				$title = special_htmlentities($title);
				$description = special_htmlentities($description);
				if (strstr($description,'<date>'))
				{
					$date_placeholder = get_string_between($description, '<date>','</date>');
					$description = str_replace("<date>$date_placeholder</date>",'' ,$description);
				}
				echo "<div class='result' id=id_result_$key style='display:none'><br>";		
				echo "<a href='$value' target=_blank><img src='./../nav/link.gif' border=0 title='Open Link in External Window'></a>&nbsp;<input name=title[] size=95 value=\"$title\"> Include?<input type=checkbox name='include_$include_count' value=1 $checked><br>";
				echo "<br>";
				echo "<div align=left width=700 class=class_cat_selects style='line-height:2;'>";
						
						foreach ($categories as $k=>$v)
						{		
							$j=$k+1;
							if ($j % 11 == 0)
							{
							echo "<br>";
							}
							
							if ($cat_ids[$k]==$category)
							{
								echo "<span class='category_button_on' id='id_category_button_".$key."_".$cat_ids[$k]."'>$v</span>";
							}
							else
							{
								echo "<span class='category_button_off' id='id_category_button_".$key."_".$cat_ids[$k]."'>$v</span>";
							}
						}
						if (is_array($this_category)&&$this_category[0]=='csv')
						{
							echo "<span class='category_button_on' id='id_category_button_".$key."_csv_collumn'>$this_category[1]</span>";
							echo "<input type=hidden name='csv_use_collumn_for_category' value = '$this_category[1]' >";
						}
						
				echo "</div>";		
				echo "<input type=hidden name=categories[] value = '$category' id='id_category_input_$key'>";
				echo "<input type=hidden name=tags[] value = '$tags' >";
				echo "<input type=hidden name=images[] value = '$images' >";
				echo "<input type=hidden name=original_author[] value = '$original_author' >";
				echo "";
				echo "<textarea name='description[]' class='wymeditor'>$description</textarea>";
				echo "<br><hr>";
				
				if ($scrape_comments_status==1)
				{
				  echo "<br><center><h2>Comments Found</h2></center>";
				  
				  if ($comments_names)
				  {
					foreach($comments_names as $k=>$v)
					{
					   if ($k>50)
					   {
					   }
					   else
					   {
							if (strip_tags(trim($comments_names[$k])))
							{
							   echo "<center>
										<div class='rbroundbox' style='width:100%;'>
											<div class='rbtop'>
												<div>
												</div>
											</div>
											<div class='rbcontent'>
												
												
												<div style='width:100%;text-align:right'>
												<img onClick='$(this).parent().parent().parent().remove();' src='./../nav/remove.png' style='cursor:pointer;'><br>
													<textarea name='comments_content[$key][]' style='width:100%;height:100px;'>$comments_content[$k]</textarea>
													<input name='comments_names[$key][]' value='$comments_names[$k]'>
												</div>
											</div>
											<!-- /rbcontent -->
										<div class='rbbot'>
											<div>
											</div>
										</div>
									</center>";
							}
						}
					}
				  }
				  else
				  {
					echo "<center><i>none found</i></center>";
				  }
				}			
				echo "</div>";
				$include_count++;
			}//if pass=1
		}//foreach links
		echo "</div>";
		if ($include_count==0)
		{
			echo "<i>No Items made it through keyword filters. Please revise(add more) keywords.</i>";
		}
		echo "<br><br></div></div>"; 
		echo "<div style=\"position:fixed;top:5px;right:5px;\"><input type=submit value='Publish Articles Now!' class='wymupdate'></div>";
		echo "</form></body></html>";
	}	
	
	
	//*************************************************************************
	//*************************************************************************
	//*************************************************************************
	//*************************************************************************
	//*************************************************************************
	//*************************************************************************
	//*************************************************************************
	//*************************************************************************
	//*************************************************************************
	
	
	
	
	if ($_POST['submit_nature']=='import_publish')
	{	 
	
	   
	   //now publish the articles if they are to be included
	   $titles = $_POST['title'];
	   $links = $_POST['links'];
	   $links = explode(';',$links);
	   $descriptions = $_POST['description'];
	   $images =  $_POST['images'];
	   $original_author =  $_POST['original_author'];
	  // $description = str_replace("<br>", "\r", $description);
	   $comments_names =  $_POST['comments_names'];
	   $comments_content =  $_POST['comments_content'];
	   $categories = $_POST['categories'];	   
	   $original_category = $_POST['category'];	   
	   $autocategorize_search = $_POST['autocategorize_search'];	   
	   $autocategorize_method = $_POST['autocategorize_method'];	   
	   $autocategorize_filter_keywrds = $_POST['autocategorize_filter_keywords'];	   
	   $autocategorize_filter_categories = $_POST['autocategorize_filter_categories'];	   
	   $autocategorize_filter_list = $_POST['autocategorize_filter_list'];	   
	   $nutags = $_POST['tags'];
	   $module_type= $_POST['module_type'];
	   $bookmark_twitter= $_POST['bookmark_twitter'];
	   $bookmark_pixelpipe= $_POST['bookmark_pixelpipe'];
	   $backdating= $_POST['backdating'];
	   $post_frequency_start_date = $_POST['post_frequency_start_date'];
	   $custom_field_name = $_POST['custom_field_name'];
	   $custom_field_value = $_POST['custom_field_value'];
	   $post_status = $_POST['post_status'];
	   $post_type = $_POST['post_type'];
	   $remote_publishing_api_bs = $_POST['remote_publishing_api_bs'];
	   $remote_publishing_api_xmlrpc = $_POST['remote_publishing_api_xmlrpc'];
	   $remote_publishing_api_email = $_POST['remote_publishing_api_email'];
	   $remote_publishing_api_email_footer= $_POST['remote_publishing_api_email_footer'];
	   $remote_publishing_api_email_pp_email= $_POST['remote_publishing_api_email_pp_email'];
	   $remote_publishing_api_email_pp_routing= $_POST['remote_publishing_api_email_pp_routing'];
	   
	   $csv = $_POST['csv'];
	   $campaign_source = $_POST['source'];
	   $csv_use_collumn_for_category = $_POST['csv_use_collumn_for_category'];

	   
	   
		
		//print_r($descriptions);
	    // echo count($descriptions); exit;
	   if (strstr($post_frequency,'.'))
	   {
		   $post_frequency = explode(".",$post_frequency);
		   $freq_day = $post_frequency[1];
		   $freq_day_limit = $post_frequency[0];
		   $freq_day_count = 0;
		   $date_placeholder =   $post_frequency_start_date;
       }
	   
	  
	   foreach ($titles as $key=>$value)
	   {
			$custom_field_name = $_POST["custom_field_name_$key"];
			$custom_field_value = $_POST["custom_field_value_$key"];
			
			//echo $_POST["include_$key"]; exit;
			if ($_POST["include_$key"]==1)
			{
				$title = stripslashes($titles[$key]);
				$description = stripslashes($descriptions[$key]);	
				$link = $links[$key];
				//echo $link; exit;
				//echo $description; exit;
				
				if (strstr($post_frequency,'.'))
				{
				        //echo 2; exit;
					if (!$date_placeholder) 
					{ 
						//echo "***()***";
						$date_placeholder =  $wordpress_date_time;
						$freq_day_count++;
					}
					else 
					{ 
						if ($freq_day==1)
						{
							if ($freq_day_count >= $freq_day_limit)
							{
								$date_placeholder = date ('Y-m-d H:i:s', strtotime ("$date_placeholder + 1 day"));
							}
							else
							{
							  $freq_day_count++;
							}
						}
						else 
						{
						  $date_placeholder = date ('Y-m-d H:i:s', strtotime ("$date_placeholder + $freq_day day"));
						}
					}
				}
				else if ($post_frequency=='feed_date')
				{
					if (!$publish_date)
					{
						echo "No publish date detected in feed. Please change campaign settings to a different scheduling pattern.";exit;
					}
					$date_placeholder = $publish_date;
					$post_date = $date_placeholder;
					//echo $date_placeholder;exit;
				}
				else if (strstr($post_frequency,'min_'))
				{
					$post_frequency = explode("_",$post_frequency);
					$freq_min = $post_frequency[1];
					//echo $date_placeholder;
					//echo $freq_min;
					$date_placeholder = date ('Y-m-d H:i:s', strtotime ("$date_placeholder + $freq_min minute"));
					//echo $date_placeholder;
				}
				else if (strstr($post_frequency,'hour_'))
				{
					$post_frequency = explode("_",$post_frequency);
					$freq_hour = $post_frequency[1];
					
					$date_placeholder = date ('Y-m-d H:i:s', strtotime ("$date_placeholder + $freq_hour hour"));
	
				}
				else  
				{
				        //echo 1; exit;
					$date_placeholder = $post_frequency_start_date;
				}
				
				
					//determine tags
				if ($autotag_method!=0)
				{
					$tags = explode(" ",$title);
					$tags = prepare_tags($tags,$description,$autotag_method,$autotag_custom_tags,$autotag_min,$autotag_max);
					if ($post_tags_typo==1)
					{
						$tags = prepare_tags_typo($tags);
					}
				}
				
				
				//if (!$tags) {echo $title; exit;}
				if ($nutags[$key])
				{
					$nutags[$key] = explode(',',$nutags[$key]);
					$tags = array_merge($tags,$nutags[$key]);
				}
				//print_r($tags); exit;
				
				//prepare link to post for guid field
				$query = "SELECT * FROM ".$table_prefix."posts ORDER BY ID DESC LIMIT 1";	
				$result= mysql_query($query);
				if (!$result){echo $query;exit;}					
								
				while ($array = mysql_fetch_array($result))
				{
					$lid = $array['ID'];
					$nid = $lid+1;
				}
				$guid = "$blog_url?p=$nid";
			
				//prepare post_name for url perma links
				$permalink_name = sanitize_title_with_dashes( $title );	

				if ($csv=='on')
				{
					$link = $permalink_name;
				}
				
				//first make sure the entry isnt already in the database
				$query ="SELECT original_source from ".$table_prefix."posts WHERE original_source='$link' AND post_status!='trash'";
				$result= mysql_query($query);
				if (!$result) { echo $query; echo 1;exit; }
				$row_count = mysql_num_rows($result);
				
				if ($row_count>0&&$post_overwrite==1){$row_count=0;}
				
				//procede if original				
				if ($row_count==0&&$title!="Unable to Source Title"&&$description!="Unable to Source Description")
				{	
					
				     //echo 1; exit;
					//if local image storing is on then do magic
					if ($store_images==1&&$module_type!='video')
					{
						$description = store_images($description,$store_images_relative_path, $store_images_full_url, $title, $link,$blog_url);
					}
					
					$title =  addslashes($title);
					//echo $title;exit;
					$description = addslashes($description);
					$description =replace_trash_characters($description);
					$description = str_replace("£", "&pound;", $description);
					//echo $description;exit;
					
					if (strlen($description)>10) 
					{
							
						if ($cronjob_randomize==1)
						{
							$date_placeholder = randomize_date($date_placeholder,$cronjob_randomize_min,$cronjob_randomize_max);
						}
						
						//determine post status
						if ($post_status=='publish')
						{
							$short_pub_date = date('Y-m-d H', strtotime($date_placeholder));
							$short_current_date = date('Y-m-d H', strtotime($wordpress_date_time));
							if ($short_pub_date >  $short_current_date)
							{
								$post_status = "future";
							}
							else
							{
								$post_status = "publish";
							}
						}
						
						//make a gmdate
						$date_placeholder = date('Y-m-d H:i:s', strtotime($date_placeholder));
						$gmt_date = get_gmt_from_date($date_placeholder);
						//echo $categories;exit;
						//print_r($categories); exit;
						//if cat is array break up
						$cat = $categories[$key];
						//echo $cat; exit;
						if (strstr($cat,','))
						{
							$cat = explode(',',$cat);
							//echo $cat; exit;
						}
						else if (!$cat)
						{
							$cat = array($original_category);
						}
						else 
						{
							$cat = array($cat);
						}
						
						//check for autocategorization
						if ($autocategorize==1)
						{		
							$cat = auto_categorize($cat, $title,$description,$autocategorize_search, $autocategorize_method,$autocategorize_filter_keywords,$autocategorize_filter_categories,$autocategorize_filter_list,$post_id);
						}
						
						if (strlen($csv_use_collumn_for_category)>1)
						{
							$cat  = $csv_use_collumn_for_category;
							//echo $cat;exit;
							$this_cat = get_term_by('name', $cat, 'category');
							//$this_cat = get_term_by('name', 'autoblogging', 'category');
							$this_cat = $this_cat->term_id;
							if (!$this_cat)
							{
								//exit;
								$cat =  wp_insert_term($cat, "category");
								$cat = $cat['term_id'];
								$cat = array($cat);
							}
							else
							{
								$cat = array($this_cat);
							}
						}
						
						if ($cat!='x')
						{
							
							if ($post_tags==1){$tags = implode(',',$tags);}
							
							if ($campaign_author=='keep_author')
							{
								$campaign_author = username_exists($original_author[$key]);
								if (!$campaign_author[$key] )
								{
									//create username
									$secret_phrase ="blogsensecreateusername0123456789";
									$this_key = str_shuffle($secret_phrase);
									$password = substr($this_key, 0, 6);
									$campaign_author = wp_create_user( $original_author[$key], $password, "noreply@noreply-$password.com" );
								}
								unset($original_author);
							}	
							
							if ($campaign_author=='keep_author_domain')
							{
								$domain  = bs_get_domain($link);
								$original_author[$key] = $original_author[$key]." - $domain";
								$campaign_author = username_exists($original_author[$key]);
								if (!$campaign_author[$key] )
								{
									//create username
									$secret_phrase ="blogsensecreateusername0123456789";
									$this_key = str_shuffle($secret_phrase);
									$password = substr($this_key, 0, 6);
									$campaign_author = wp_create_user( $original_author[$key], $password, "noreply@noreply-$password.com" );
								}							
							}	
							
							$domain  = bs_get_domain($link);
							$author_info = get_userdata($campaign_author);
							$author_name = $author_info->display_name;
							$description = str_replace('%author_name%',$author_name, $description);
							$description = str_replace('%domain_name%',$domain, $description);
							
							if ($campaign_author=='domain')
							{
								$domain  = bs_get_domain($link);
								$original_author[$key] = "$domain";
								$campaign_author[$key] = username_exists($original_author[$key]);
								if (!$campaign_author[$key] )
								{
									//create username
									$secret_phrase ="blogsensecreateusername0123456789";
									$this_key = str_shuffle($secret_phrase);
									$password = substr($key, 0, 6);
									$campaign_author = wp_create_user( $original_author[$key], $password, "noreply@noreply-$password.com" );
								}							
							}
							
							if ($campaign_author=='rand')
							{
								$this_key = array_rand($authors_id);
								$campaign_author= $authors_id[$this_key];
							}
							
							if ($module_type=='fileimport')
							{
								$link = $permalink_name;
							}
							
							//insert rss item into database store
							$post = array(		
							  'post_author' => $campaign_author,
							  'post_category' => $cat, 
							  'post_content' => $description, 
							  'post_date' => $date_placeholder,
							  'post_date_gmt' => $gmt_date,
							  'post_name' => $permalink_name,
							  'post_status' => $post_status, 
							  'post_title' => $title,
							  'post_type' => $post_type,
							  'tags_input' => "$tags",
							  'original_source'=> $link
							);  
							
							if ($post_overwrite==1)
							{
								$query ="SELECT ID FROM {$table_prefix}posts WHERE original_source='$link'";
								$result = mysql_query($query);
								$arr = mysql_fetch_array($result);
								$post_id = $arr['ID'];
								$post['ID'] = $post_id;
								if ($post_id)
								{
									wp_update_post( $post, $wp_error );
								}
								else
								{
									$post_id = wp_insert_post( $post, $wp_error );
								}
							}
							else
							{
								$post_id = wp_insert_post( $post, $wp_error );
							}
							
							$description = stripslashes($description);
							
							//add items to bookmarking queue
							if ($post_status=='publish')
							{
								$posts_to_bookmark[] = $post_id;
							}	
							//add items to bookmarking queue
							if ($post_status=='future')
							{
								$future_posts_to_bookmark[] = $post_id;
								$future_dates[] = $date_placeholder;
							}							
							//draft notification queue
							if ($post_status=='draft'&&$draft_notification==1)
							{
								$draft_posts_to_bookmark[] = $post_id;
								$draft_notification_items[] = array($post_id,$title,$description);
							}
							
							//store source as saved				
							$query = "UPDATE ".$table_prefix."posts SET original_source='$link', bs_campaign_id='$campaign_id[$key]' WHERE ID='$post_id'";
							$result = mysql_query($query);					
							if (!$result){echo $query; echo mysql_error(); exit;}
							
							//add custom fields
							if ($custom_field_name)
							{
								$image = bs_get_images($description);
								if (strlen($image[0])<2)
								{
									$image = $images;
								}
								foreach ($custom_field_name as $k=>$v)
								{
									//echo 1;
									if ($custom_field_value[$k]=='%image_1%')
									{
										$image_url = $image[0];
										if ($image_url)
										{
											add_post_meta($post_id, $custom_field_name[$k], $image_url, true);
										}
										
										$post_thumbnail_id = bs_create_post_attachment_from_url($image_url, $post_id);
										if(is_int($post_thumbnail_id)) {
											update_post_meta( $post_id, '_thumbnail_id', $post_thumbnail_id );
										}
									}
									else if ($custom_field_value[$k]=='%image_2%')
									{
										$image_url = $image[1];
										if ($image_url)
										{
											add_post_meta($post_id, $custom_field_name[$k], $image_url, true);
										}
										
										$post_thumbnail_id = bs_create_post_attachment_from_url($image_url, $post_id);
										if(is_int($post_thumbnail_id)) {
											update_post_meta( $post_id, '_thumbnail_id', $post_thumbnail_id );
										}
									}
									else if ($custom_field_value[$k]=='%video_embed%')
									{
										preg_match('/\<object(.*?)\<\/object\>/si', $description, $matches);
										if ($matches[0])
										{
											add_post_meta($post_id, $custom_field_name[$k], $matches[0], true);
										}
									}
									else
									{
										add_post_meta($post_id, $custom_field_name[$k], $custom_field_value[$k], true);
									}
								}
							}	
							
							//add comments
							if ($comments_names[$key])
							{
								
								foreach($comments_names[$key] as $a=>$b)
								{ 		
									$fake_date = date('Y-m-d', strtotime("$date_placeholder +$a day")); 
									$fake_gmt_date = get_gmt_from_date($fake_date);								
									if ($fake_date >  date('Y-m-d'))
									{
										$comment_approved = "2";
									}
									else
									{
										$comment_approved = "1";
									}
									
									$name = $comments_names[$key][$a];
									$name = stripslashes($name);
									$comment = $comments_content[$key][$a];
									$comment =stripslashes($comment);
									$comment = trim($comment);
									if ($name&&$comment)
									{
								
										$data = array(
											'comment_post_ID' => $post_id,
											'comment_author' => $name,
											'comment_author_email' => 'noreply@noreply.com',
											'comment_author_url' => '',
											'comment_content' => $comment,
											'comment_author_IP' => '127.0.0.1',
											'comment_agent' => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; fr; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3',
											'comment_date' => $fake_date,
											'comment_date_gmt' => $fake_gmt_date,
											'comment_approved' => $comment_approved,
										);

										$comment_id = wp_insert_comment($data);
									}
								}
							}
							//exit;
							
							//update date placeholder data					
							$query = "UPDATE ".$table_prefix."campaigns SET schedule_post_date='$date_placeholder', schedule_post_count='$freq_day_count' WHERE id='$campaign_id[$key]'";
							$result = mysql_query($query);					
							if (!$result){echo $query;}
							
							$title = stripslashes($title);
							$description = stripslashes($description);
							if ($campaign_remote_publishing_api_bs)
							{		   
								$remote_publishing_api_bs = explode(';',$campaign_remote_publishing_api_bs);	
								$this_link = get_permalink($post_id);
								
								foreach ($remote_publishing_api_bs as $a=>$b)
								{
									$b = trim($b);
									$this_data['post_title'] = $title;
									$this_data['post_content'] = $description;
									$this_data['post_status'] = $post_status;
									$this_data['post_type'] = $post_type;
									$this_data['post_tags'] = $tags;
									$this_data['post_date'] = $date_placeholder;
									$this_data['link'] = $link;
									$this_data['manual_mode'] = 0;
									$this_data['internal'] = 1;
									
									$url = $b;
									$return = stealth_curl($url, 0, $this_data,'remote_publish');
									if (strstr($return, "Something's missing:"))
									{
										echo "REMOTE PUBLISHING FIRE (blogsense api):  Failure:<br><br>$return<br>exit;";
									}
									else
									{
										$this_permalink = explode('<permalink>',$return);
										$this_permalink = $this_permalink[1];
										$this_permalink = str_replace('</permalink>','',$this_permalink);
										if ($this_permalink)
										{
											$query = "INSERT INTO ".$table_prefix."blogsense_remote_published_urls (`permalink`,`title`,`date`) VALUES ('$this_permalink', '$title','$date_placeholder');";
											$result = mysql_query($query);					
											if (!$result){echo $query;}
										}
										echo "REMOTE PUBLISHING FIRE (blogsense api) : $url<br>";
									}
									
									unset($this_data);
									unset($this_permalink);
									unset($return);
								}
							}
							
							if ($campaign_remote_publishing_api_xmlrpc)
							{
								$remote_publishing_api_xmlrpc = explode(':::',$campaign_remote_publishing_api_xmlrpc);
								$remote_publishing_api_xmlrpc_spin = explode(':::',$campaign_remote_publishing_api_xmlrpc_spin);
								
								foreach ($remote_publishing_api_xmlrpc as $a=>$b)
								{
									$this_array = explode(";",$b);
									$url = $this_array[0];
									$username = $this_array[1];
									$password = $this_array[2];
									$blog_id = $this_array[2];
									
									$this_data['blogid'] = $blog_id;
									$this_data['username'] = $username;
									$this_data['password'] = $password;
									$this_data['content'] = $description;
									$this_data['description'] = $description;
									$this_data['title'] = $title;
									$this_data['mt_keywords'] = $tags;
									$this_data['categories'] = get_cat_slug($cat[0]);
									if ($remote_publishing_api_xmlrpc_spin[$a]=='on')
									{
										$language='spin';
										$this_data['content'] = spin_text($description, $link, $title, $campaign_image_floating, $language);
										$this_data['description'] = $this_data['content'];
										$this_data['title'] = spin_text($title, $link, $title, $campaign_image_floating, $language);
									}
									
									
									$return = bs_xmlrpc($url,$this_data,$username,$password);
									
									if ($return==1)
									{
										echo "REMOTE PUBLISHING FIRE (XMLRPC): $url<br>";
									}
									else
									{
										echo $return."<br>";
									}
									//exit;
									unset($this_array);
									unset($url);
									unset($username);
									unset($password);
									unset($return);
								}
							}
							
							if ($campaign_remote_publishing_api_email)
							{		   
								$remote_publishing_api_email = explode(';',$campaign_remote_publishing_api_email);	
								$remote_publishing_api_email_spin = explode(';',$campaign_remote_publishing_api_email_spin);	
								$this_link = get_permalink($post_id);
							
								$headers = "MIME-Version: 1.0 \r\n";
								$headers .= "Content-Type: text/html ;\n";
								
								foreach ($remote_publishing_api_email as $a=>$b)
								{
									$b = trim($b);
									$description = trim($description);
									
									if (strstr($remote_publishing_api_email_footer[$a],'+spin'))
									{
										$language="spin";
										$description = spin_text($description, $link, $title, $campaign_image_floating[$key], $language);
										$title = spin_text($title, $link, $title, $campaign_image_floating[$key], $language);
										$remote_publishing_api_email_footer[$a] = str_replace('+spin','',$remote_publishing_api_email_footer[$a]);
									}
									
									$description = preg_replace("/\r\n/",'',$description);
									//$b = 'hudson.atwell@gmail.com';
									mail($b,$title,$description."<br><br>".$remote_publishing_api_email_footer[$a],$headers);
		
									echo "REMOTE PUBLISHING FIRE (Email): $b<br>";
									usleep(500000);
									
								}
								unset($headers);
							}
							
							if ($campaign_remote_publishing_api_pp_email)
							{		   
								$remote_publishing_api_pp_email = explode(';',$campaign_remote_publishing_api_pp_email);	
								$remote_publishing_api_pp_routing = explode(';',$campaign_remote_publishing_api_pp_routing);	
								$this_link = get_permalink($post_id);
								
								//echo print_r($campaign_custom_field_name); exit;
								$image = bs_get_images($description);
								
								if (strlen($image[0])<2)
								{
									$image = $images;
								}
								
								$semi_rand = md5(time());
								$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
								$headers = "MIME-Version: 1.0 \r\n";
								$headers .= "Content-Type: multipart/mixed;\n";
								$headers .=	" boundary=\"$mime_boundary\"";
								$description = str_replace(array("</p>","<br><br>",'</div>','</span>','</li>','</ul>','</table>','</tr>'),"\n\n",$description);
								$description = str_replace('<br>',"\n",$description);
								$description = str_replace('<li>',"•",$description);
								$description = strip_tags($description);
								
								
								foreach ($remote_publishing_api_pp_email as $a=>$b)
								{
									$b = trim($b);
									if (stristr($remote_publishing_api_pp_routing[$a],'+spin'))
									{
										$remote_publishing_api_pp_routing[$a] = str_replace('+spin','',$remote_publishing_api_pp_routing[$a]);
										$language="spin";
										$description = spin_text($description, $link, $title, $campaign_image_floating, $language);
										$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
									}
									
									$body = $description." $remote_publishing_api_pp_routing[$a] \n\n";
									
									if ($image[0]&&$images==1)
									{
										$info = pathinfo($image[0]);
										$file_name =  basename($image[0],'.'.$info['extension']);
										
										
										$image_string  = file_get_contents($image[0]);	
										$image_string = chunk_split(base64_encode($image_string));
										
										$pbody = $body;
										
										$body = "--{$mime_boundary}\n" ;
										$body .= "Content-Type: text/plain; charset=UTF-8\n"; 
										$body .= " filename=\"{$file_name}\"\n" ;
										$body .= "Content-Transfer-Encoding: 7bit\n\n" ;

										$body = $body.$pbody;
										
										$body .= "--{$mime_boundary}\n" ;
										$body .= "Content-Type: image/{$info['extension']} name=\"{$file_name}.{$info['extension']}\"\n";
										$body .= "Content-Transfer-Encoding: base64\n" ;
										$body .= "Content-Disposition: attachment; filename=\"{$file_name}.{$info['extension']}\"\n\n" ;

										$body .= $image_string . "\n\n" ;
										$body .= "--{$mime_boundary}--\n";
									}
									else
									{
										$pbody = $body;
										
										$body = "--{$mime_boundary}\n" ;
										$body .= "Content-Type: text/plain; charset=UTF-8\n"; 
										$body .= "Content-Transfer-Encoding: 7bit\n\n" ;

										$body = $body.$pbody."\n\n";
										$body .= "--{$mime_boundary}--\n";
									}
								
								
									
									//$b = 'hudson.atwell@gmail.com';
									mail($b,"=?UTF-8?B?".base64_encode($title)."?=",$body,$headers);
									
									echo "REMOTE PUBLISHING FIRE (pixelpipe): $b<br>";
									
								}
								unset($headers);
								unset($body);
								
							}
							
						}
						else
						{ 
							echo "Item #{$key} was blocked by auto-categorization (no keywords found).";
						}
					}
				}
				
			}//if include==1
						
	   }
	   echo "<center><br><br><br><br><font color=green>Articles Published. </center></font>";
	   
		//schedule new bookmarking jobs
		if ($posts_to_bookmark)
		{
			$return = schedule_bookmarks('publish', NULL, $posts_to_bookmark, $bookmark_pixelpipe, $bookmark_twitter,$bookmark_hellotxt);
			//echo "<br>".count($posts_to_bookmark)." posts scheduled for bookmarking.<br><br>";
		}
		if ($future_posts_to_bookmark)
		{
			$return = schedule_bookmarks('future', $future_dates, $future_posts_to_bookmark, $bookmark_pixelpipe, $bookmark_twitter,$bookmark_hellotxt);
		}
		if($draft_notification_items)
		{
			$return = schedule_bookmarks('draft', $future_dates, $draft_posts_to_bookmark, $bookmark_pixelpipe[$key], $bookmark_twitter[$key],$bookmark_hellotxt[$key]);
			$return = run_draft_notifications($draft_notification_items);
		}
		
	
	}
 }
 ?>