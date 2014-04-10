<?php
include_once('../../wp-config.php');
session_start();

//check for multisite
//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}

function fix_encoding($in_str)
{
  $cur_encoding = mb_detect_encoding($in_str) ;
  if($cur_encoding == "UTF-8" && mb_check_encoding($in_str,"UTF-8"))
    return $in_str;
  else
    return utf8_encode($in_str);
}

function redundant_work($input){

  $input = str_replace("Ã©","é", $input);
  $input = str_replace("Ã£", "ã",$input);
  $input = str_replace("Ã§", "ç",$input);
  $input = str_replace("Ã³", "ó",$input);
  $input = str_replace("Ãµ", "a",$input);
  $input = str_replace('â??', '"',$input);
  $input = str_replace("Ã´", "ô",$input);
  $input = str_replace("Ã", "í",$input);
  $input = str_replace("Ãº", "ú",$input); 
  $input = str_replace("íª", "ê",$input);
  $input = str_replace("í¡", "á",$input);
  $input = str_replace("íº", "ú",$input);
  
  
   return $input;
} 


function special_htmlentities($data)
{
   //$data = htmlentities($data);
   $data = str_replace("&lt;","<",$data);
   $data = str_replace("&gt;",">",$data);
   $data = str_replace("&amp;","&",$data);
   $data = str_replace('&quot;','"', $data);
   $data = fix_encoding($data);
   $data = redundant_work($data);
   return $data;
}

function quick_curl($link)
{
	$agents[] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; Media Center PC 5.0)";
	$agents[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
	$agents[] = "Opera/9.63 (Windows NT 6.0; U; ru) Presto/2.1.1";
	$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5?";
	$agents[] = "Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.18) Gecko/20081203 Firefox/2.0.0.18";
	$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16";
	$agents[] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1";
	 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_HEADER, false);
	//echo ini_get('open_basedir');exit;
	//print_r(ini_get('safe_mode'));exit;
	if (!ini_get('open_basedir') && !ini_get('safe_mode'))
	{
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	}
   // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_USERAGENT, $agents[rand(0,(count($agents)-1))]);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

//find all categories where count=0 and update them if there are posts
$query = "SELECT * FROM ".$table_prefix."term_taxonomy WHERE taxonomy='category' AND count=0";
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

//module open before save
$open_module = $_POST['open_module'];

if ($open_module=='global_settings')
{
	//Global Settings
	$draft_notification =  $_POST['draft_notification']; 
	$draft_notification_email =  $_POST['draft_notification_email']; 
	
	$cronjob_buffer_items =  $_POST['cron_buffer_items']; 
	$cronjob_buffer_campaigns =  $_POST['cron_buffer_campaigns']; 
	$cronjob_minutes =  $_POST['cron_minutes']; 
	$cronjob_hours =  $_POST['cron_hours']; 
	$cronjob_days =  $_POST['cron_days']; 
	$cronjob_weekdays = $_POST['cron_weekdays']; 
	$cronjob_months = $_POST['cron_months']; 
	$cronjob_email = $_POST['cron_email'];  
	$cronjob_randomize = $_POST['cron_randomize'];  
	$cronjob_randomize_min = $_POST['cron_randomize_min'];  
	$cronjob_randomize_max = $_POST['cron_randomize_max'];  
	$cronjob_timeout = $_POST['cron_timeout'];  
	//echo $cronjob_timeout;exit;
	$image_floating = $_POST['image_floating'];  
	$image_alt_setup = $_POST['image_alt_setup'];
	$store_images = $_POST['store_images']; 
	//$store_images_pass =  fread($ebay_post, filesize('f_activate_blogsense.php'));
	$store_images_relative_path = $_POST['store_images_relative_path'];  
	$store_images_full_url = $_POST['store_images_full_url'];  
	
	if (substr($store_images_full_url,-1, 1)!='/')
	{
		$store_images_full_url = $store_images_full_url."/";
	}
	
	//Tagging Options
	$post_tags_typo = $_POST['post_tags_typo'];

	//spin settings
	$spin_phrase_min = $_POST['spin_phrase_min'];
	$spin_phrase_max = $_POST['spin_phrase_max'];
	
	$spin_exclude_cats = $_POST['spin_exclude_cats'];
	$spin_exclude_these = $_POST['spin_exclude_these'];
	
	
	$spin_exclude_these = strtolower($spin_exclude_these."\n".$spin_exclude_these_cats);
	$spin_exclude_these = strtolower($spin_exclude_these);
	
	$tbs_username = $_POST['tbs_username'];
	$tbs_password = $_POST['tbs_password'];
	$tbs_spinning = $_POST['tbs_spinning'];
	$tbs_quality = $_POST['tbs_quality'];
	$tbs_maxsyns = $_POST['tbs_maxsyns'];
	
	//cloaking redirect
	$cloaking_redirect = $_POST['cloaking_redirect'];
	
	//proxy list
	$proxy_list = $_POST['proxy_list'];
	$proxy_type = $_POST['proxy_type'];
	$proxy_bonanza_username = $_POST['proxy_bonanza_username'];
	$proxy_bonanza_password = $_POST['proxy_bonanza_password'];
	$proxy_bonanza_email = $_POST['proxy_bonanza_email'];
	$proxy_bookmarking = $_POST['proxy_bookmarking'];
	$proxy_campaigns = $_POST['proxy_campaigns'];

	//get proxy list from proxy bonanza
	if ($proxy_bonanza_username)
	{
		$bonanza_url = "http://proxybonanza.com/ips/list?user={$proxy_bonanza_username}&email={$proxy_bonanza_email}&type={$proxy_type}";
		$proxy_list = quick_curl($bonanza_url);
		$proxy_list = trim($proxy_list);
		$proxy_list = str_replace("<br>",":$proxy_bonanza_username:$proxy_bonanza_password \r",$proxy_list);
		$proxy_list = str_replace("  ","",$proxy_list);
		//echo $bonanza_url;exit;
		//echo $proxy_list; exit;
		
	}

	//blocked items list
	$blocked_urls = $_POST['blocked_urls'];
	if ($blocked_urls)
	{
		
		$blocked_urls = explode("\n", $blocked_urls);
		$blocked_urls = array_filter($blocked_urls);
		
		//delete current blocked urls
		$query = "TRUNCATE TABLE  ".$table_prefix."blocked_urls";
		$result = mysql_query($query);
		if (!$result) {echo $query; exit;}
		
		//add new blocked urls
		foreach ($blocked_urls as $key=>$val)
		{
			if (trim($val))
			{
				$query = "INSERT INTO  ".$table_prefix."blocked_urls (`id`,`campaign_id`,`url`)";
				$query .= "VALUES ('','','$val')";
				$result = mysql_query($query);
				//if (!$result) {echo $query; echo mysql_error(); exit;}
			}
			
		}
	}
	
	if ($tags_custom=='Separate with commas.')
	{
	$tags_custom = '';
	}

	if ($tags_nature=='wptagsdb')
	{
	   $query = "SELECT * FROM ".$table_prefix."terms t JOIN  ".$table_prefix."term_taxonomy tt ON (t.term_id=tt.term_id) AND tt.taxonomy='post_tag'";
	   $result = mysql_query($query);
	   while ($arr = mysql_fetch_array($result))
	   {
		  $tags_db[] = $arr['name'];
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
	   $tags_custom = $tags_db;			
	}
	$tags_custom = addslashes($tags_custom);

	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$draft_notification' WHERE option_name='blogsense_draft_notification'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$draft_notification_email' WHERE option_name='blogsense_draft_notification_email'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$image_floating' WHERE option_name='blogsense_image_floating'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$image_alt_setup' WHERE option_name='blogsense_image_alt_setup'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$post_tags' WHERE option_name='blogsense_post_tags'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$post_tags_typo' WHERE option_name='blogsense_post_tags_typo'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$tags_nature' WHERE option_name='blogsense_tags_nature'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$tags_custom' WHERE option_name='blogsense_tags_custom'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$tags_min' WHERE option_name='blogsense_tags_min'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$tags_max' WHERE option_name='blogsense_tags_max'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
		
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$tag_to_category' WHERE option_name='blogsense_tag_to_category'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cloaking_redirect' WHERE option_name='blogsense_cloaking_redirect'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	

	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$proxy_list' WHERE option_name='blogsense_proxy_list'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$proxy_type' WHERE option_name='blogsense_proxy_type'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$proxy_bookmarking' WHERE option_name='blogsense_proxy_bookmarking'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$proxy_campaigns' WHERE option_name='blogsense_proxy_campaigns'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$proxy_bonanza_username' WHERE option_name='blogsense_proxy_bonanza_username'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$proxy_bonanza_password' WHERE option_name='blogsense_proxy_bonanza_password'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$proxy_bonanza_email' WHERE option_name='blogsense_proxy_bonanza_email'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$spin_phrase_min' WHERE option_name='blogsense_spin_phrase_min'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$spin_phrase_max' WHERE option_name='blogsense_spin_phrase_max'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$spin_exclude_cats' WHERE option_name='blogsense_spin_exclude_cats'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$spin_exclude_these' WHERE option_name='blogsense_spin_exclude_these'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$tbs_username' WHERE option_name='blogsense_tbs_username'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$tbs_password' WHERE option_name='blogsense_tbs_password'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$tbs_spinning' WHERE option_name='blogsense_tbs_spinning'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$tbs_quality' WHERE option_name='blogsense_tbs_quality'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$tbs_maxsyns' WHERE option_name='blogsense_tbs_maxsyns'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_buffer_items' WHERE option_name='blogsense_cron_buffer_items'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_buffer_campaigns' WHERE option_name='blogsense_cron_buffer_campaigns'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_timeout' WHERE option_name='blogsense_cron_timeout'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_minutes' WHERE option_name='blogsense_cron_minutes'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_hours' WHERE option_name='blogsense_cron_hours'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_days' WHERE option_name='blogsense_cron_days'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_weekdays' WHERE option_name='blogsense_cron_weekdays'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_months' WHERE option_name='blogsense_cron_months'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_email' WHERE option_name='blogsense_cron_email'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_randomize' WHERE option_name='blogsense_cron_randomize'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_randomize_min' WHERE option_name='blogsense_cron_randomize_min'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$cronjob_randomize_max' WHERE option_name='blogsense_cron_randomize_max'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$store_images' WHERE option_name='blogsense_store_images'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$store_images_relative_path' WHERE option_name='blogsense_store_images_relative_path'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$store_images_full_url' WHERE option_name='blogsense_store_images_full_url'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
}



if ($open_module=='post_templates')
{
	//post templates
	$template_ids = $_POST['template_id'];
    $template_content = $_POST['template_content'];

	foreach ($template_ids as $key=>$val)
	{
		$content = stripslashes($template_content[$key]);
		$content = addslashes($content);
		$query = "UPDATE  ".$table_prefix."post_templates SET content='$content' WHERE id=$val";
		$result = mysql_query($query);
		if (!$result) {echo $query; exit;}
	}
	
	//custom templates
    $template_custom_variable_ids = $_POST['template_custom_variable_id'];
    $template_custom_variable_name = $_POST['template_custom_variable_name'];
    $template_custom_variable_token = $_POST['template_custom_variable_token'];
    $template_custom_variable_content = $_POST['template_custom_variable_content'];
	
	//find all ids currently in table
	$query = "SELECT id FROM  ".$table_prefix."custom_tokens";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	while ($arr = mysql_fetch_array($result))
	{
	   $template_custom_variable_ids_db[] = $arr['id'];
	}
	//echo count($template_ids_in); exit;
	if ($template_custom_variable_ids)
	{
		//echo 1;
		//delete appropriate blocks
		foreach ($template_custom_variable_ids_db as $k=>$v)
		{
			if (!in_array($v,$template_custom_variable_ids))
			{
				$query = "DELETE FROM  ".$table_prefix."custom_tokens WHERE id='$v'";
				$result = mysql_query($query);
				if (!$result) {echo $query; exit;}
			}
		}
		
		//update remaining blocks
		foreach ($template_custom_variable_ids as $k=>$v)
		{
			//echo $v; exit;
		    //$template_content[$k] = special_htmlentities($template_content[$k]);
			$template_custom_variable_name[$k] = mysql_real_escape_string($template_custom_variable_name[$k]);
			$template_custom_variable_content[$k] = mysql_real_escape_string($template_custom_variable_content[$k]);
			
			$query =  "UPDATE ".$table_prefix."custom_tokens SET name='$template_custom_variable_name[$k]', token='$template_custom_variable_token[$k]', content='$template_custom_variable_content[$k]' WHERE id='$v' ";
			$result = mysql_query($query);
			if (!$result) {echo $query; exit;}
		}
	}
	else
	{
		//echo 2;
		$query = "DELETE FROM  ".$table_prefix."custom_tokens";
		$result = mysql_query($query);
		if (!$result) {echo $query; exit;}
	}
}

	
if ($open_module=='sources'||$open_module=='rss'||$open_module=='video'||$open_module=='yahoo'||$open_module=='amazon'||$open_module=='keywords'||$open_module=='fileimport'||$open_module=='api')
{
	//rss module
	$rss_module = $_POST['rss_module'];
	$keywords_module = $_POST['keywords_module'];
	
	//Sources Module
	$sources_module = $_POST['sources_module'];
	
	//Drop Posting
	$drop_module =$_POST['drop_module'];
	$drop_folder = $_POST['drop_folder'];
	
	//Yahoo
	$yahoo_module =$_POST['yahoo_module'];
	$yahoo_api_key =$_POST['yahoo_api_key'];
	
	//video
	$video = $_POST['video_module'];
	
	//Amazon Module
	$amazon =$_POST['amazon_module'];
	$amazon_affiliate_id = $_POST['amazon_affiliate_id'];
	$amazon_aws_access_key = $_POST['amazon_aws_access_key'];
	$amazon_secret_access_key = $_POST['amazon_secret_access_key'];
	
	//blogsense api secret key
	$blogsense_api_secret_key = $_POST['blogsense_api_secret_key'];
	
	
	//Get Campaign IDS coming in
	$campaign_id = $_POST['campaign'];

	//build list of current campaigns in database
	$query = "SELECT * FROM ".$table_prefix."campaigns";
	$result = mysql_query($query);
	while ($arr = mysql_fetch_array($result))
	{
	   $stored[] = $arr['id'];
	}

	//if the campaign stored is not in the campaign id list then delte it
	if ($stored)
	{	
		foreach ($stored as $key=>$val)
		{
			if (!$campaign_id)
			{
				$query = "DELETE FROM ".$table_prefix."campaigns WHERE id='$val'";
				$result = mysql_query($query);
				if (!$result) {echo $query; exit;}
			}
			else
			{
				if (!in_array($val, $campaign_id))
				{
					$query = "DELETE FROM ".$table_prefix."campaigns WHERE id='$val'";
					$result = mysql_query($query);
					if (!$result) {echo $query; exit;}
				}
			}
		}
	}


	//update campaign information
	if ($campaign_id)
	{
		foreach ($campaign_id as $key=>$val)
		{ 
			
			$campaign_name[$key] = $_POST["campaign_name_$val"];
			$campaign_feed[$key] = $_POST["campaign_feed_$val"];
			$campaign_status[$key] = $_POST["campaign_status_$val"];
			
		
			
			$query =  "UPDATE ".$table_prefix."campaigns SET name='$campaign_name[$key]', campaign_status='$campaign_status[$key]', feed='$campaign_feed[$key]' WHERE id='$val' AND module_type!='fileimport'";
			$result = mysql_query($query);
			if (!$result) {echo $query; echo mysql_error(); exit;}
			
			if ($open_module=='fileimport')
			{
				$query =  "UPDATE ".$table_prefix."campaigns SET  campaign_status='$campaign_status[$key]' WHERE id='$val' ";
				$result = mysql_query($query);
				if (!$result) {echo $query; echo mysql_error(); exit;}
			}	
			if ($open_module=='keywords')
			{
				//echo 1; exit;
				$campaign_query[$key] = $_POST["campaign_query_$val"];
				$campaign_query[$key] = str_replace("\n",";",$campaign_query[$key]);
				
				$query =  "UPDATE ".$table_prefix."campaigns SET  `query`='$campaign_query[$key]' WHERE id='$val' ";
				$result = mysql_query($query);
				if (!$result) {echo $query; echo mysql_error(); exit;}
			}
		}
	}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$blogsense_api_secret_key' WHERE option_name='blogsense_api_secret_key'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$sources_module' WHERE option_name='blogsense_sources_module'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$amazon' WHERE option_name='blogsense_amazon'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$amazon_affiliate_id' WHERE option_name='blogsense_amazon_affiliate_id'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$amazon_aws_access_key' WHERE option_name='blogsense_amazon_aws_access_key'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$amazon_secret_access_key' WHERE option_name='blogsense_amazon_secret_access_key'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}

	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$rss_module' WHERE option_name='blogsense_rss_module'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$keywords_module' WHERE option_name='blogsense_keywords_module'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$video' WHERE option_name='blogsense_video'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$drop_module' WHERE option_name='blogsense_drop'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$yahoo_module' WHERE option_name='blogsense_yahoo'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$yahoo_api_key' WHERE option_name='blogsense_yahoo_api_key'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
}





//done send back
header("Location: ../index.php?p=3&m=$open_module&saved=y");
?>