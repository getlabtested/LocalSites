<?php
//echo $table_prefix;exit;
//******************************************************************************
//PREPARE VARIABLES *******************************************************
//******************************************************************************
$query = "SELECT * FROM ".$table_prefix."blogsense WHERE `option_name` IN (";
$query .= "'blogsense_activation' ,";
$query .= "'blogsense_activation_email' ,";
$query .= "'blogsense_activation_key' ,";
$query .= "'blogsense_amazon' ,";
$query .= "'blogsense_amazon_affiliate_id' ,";
$query .= "'blogsense_amazon_aws_access_key' ,";
$query .= "'blogsense_amazon_secret_access_key' ,";
$query .= "'blogsense_api_secret_key' , ";
$query .= "'blogsense_blog_comments' , ";
$query .= "'blogsense_blog_url' , ";
$query .= "'blogsense_bookmarking_bitly_apikey' ,";
$query .= "'blogsense_bookmarking_bitly_username' ,";
$query .= "'blogsense_bookmarking_minutes_min' ,";
$query .= "'blogsense_bookmarking_minutes_max' ,";
$query .= "'blogsense_bookmarking_percentage' ,";
$query .= "'blogsense_bookmarking_ping_module' ,";
$query .= "'blogsense_build_version' ,";
$query .= "'blogsense_cloaking_redirect' , ";
$query .= "'blogsense_cron_buffer_items' ,";
$query .= "'blogsense_cron_buffer_campaigns' ,";
$query .= "'blogsense_cron_days' ,";
$query .= "'blogsense_cron_email' ,";
$query .= "'blogsense_cron_hours' ,";
$query .= "'blogsense_cron_minutes' ,";
$query .= "'blogsense_cron_months' ,";
$query .= "'blogsense_cron_randomize' ,";
$query .= "'blogsense_cron_randomize_min' ,";
$query .= "'blogsense_cron_randomize_max' ,";
$query .= "'blogsense_cron_timeout' ,";
$query .= "'blogsense_cron_weekdays' ,";
$query .= "'blogsense_default_author' , ";
$query .= "'blogsense_draft_notification' , ";
$query .= "'blogsense_draft_notification_email' , ";
$query .= "'blogsense_drop' ,";
$query .= "'blogsense_keywords_module' ,";
$query .= "'blogsense_pixelpipe' ,";
$query .= "'blogsense_pixelpipe_email' ,";
$query .= "'blogsense_pixelpipe_mode' ,";
$query .= "'blogsense_pixelpipe_routing' ,";
$query .= "'blogsense_post_tags' ,";
$query .= "'blogsense_post_tags_typo' ,";
$query .= "'blogsense_proxy_bonanza_email' ,";
$query .= "'blogsense_proxy_bonanza_password' ,";
$query .= "'blogsense_proxy_bonanza_username' ,";
$query .= "'blogsense_proxy_bookmarking' ,";
$query .= "'blogsense_proxy_campaigns' ,";
$query .= "'blogsense_proxy_list' ,";
$query .= "'blogsense_proxy_type' ,";
$query .= "'blogsense_rss_module' , ";
$query .= "'blogsense_spin_exclude_cats' , ";
$query .= "'blogsense_spin_exclude_these' , ";
$query .= "'blogsense_spin_phrase_max' , ";
$query .= "'blogsense_spin_phrase_min' , ";
$query .= "'blogsense_sources_module' ,";
$query .= "'blogsense_store_images' ,";
$query .= "'blogsense_store_images_relative_path' , ";
$query .= "'blogsense_store_images_full_url' , ";
$query .= "'blogsense_tags_nature' ,";
$query .= "'blogsense_tags_custom' ,";
$query .= "'blogsense_tags_min' ,";
$query .= "'blogsense_tags_max' ,";
$query .= "'blogsense_tbs_maxsyns' ,";
$query .= "'blogsense_tbs_password' ,";
$query .= "'blogsense_tbs_quality' ,";
$query .= "'blogsense_tbs_spinning' ,";
$query .= "'blogsense_tbs_username' ,";
$query .= "'blogsense_twitter' ,";
$query .= "'blogsense_twitter_hash' ,";
$query .= "'blogsense_twitter_mode' ,";
$query .= "'blogsense_twitter_oauth_apikey' ,";
$query .= "'blogsense_twitter_oauth_consumer_key' ,";
$query .= "'blogsense_twitter_oauth_consumer_secret' ,";
$query .= "'blogsense_twitter_oauth_secret' ,";
$query .= "'blogsense_twitter_oauth_token' ,";
$query .= "'blogsense_twitter_user' ,";
$query .= "'blogsense_yahoo' ,";
$query .= "'blogsense_yahoo_api_key' ,";
$query .= "'blogsense_video') ORDER BY option_name ASC";


$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error(); exit;}
$count = mysql_num_rows($result);

for ($i=0;$i<$count;$i++)
{
  $arr = mysql_fetch_array($result);
  if ($i==0){$blogsense_activation =$arr[option_value];}
  if ($i==1){$blogsense_activation_email =$arr[option_value];}
  if ($i==2){$blogsense_activation_key =$arr[option_value];}
  if ($i==3){$amazon_module =$arr[option_value];}
  if ($i==4){$amazon_affiliate_id =$arr[option_value];}
  if ($i==5){$amazon_aws_access_key =$arr[option_value];}
  if ($i==6){$amazon_secret_access_key =$arr[option_value];}
  if ($i==7){$blogsense_api_secret_key = $arr[option_value];}  
  if ($i==8){$blog_comments = $arr[option_value];}
  if ($i==9){$blog_url = $arr[option_value];}  
  if ($i==10){$bookmarking_bitly_apikey =$arr[option_value];}
  if ($i==11){$bookmarking_bitly_username =$arr[option_value];}
  if ($i==12){$bookmarking_minutes_max =$arr[option_value];}
  if ($i==13){$bookmarking_minutes_min =$arr[option_value];}
  if ($i==14){$bookmarking_percentage =$arr[option_value];}
  if ($i==15){$ping_module =$arr[option_value];}
  if ($i==16){$build_version = $arr[option_value];}
  if ($i==17){$cloaking_redirect = $arr[option_value];}
  if ($i==18){$cronjob_buffer_campaigns =$arr[option_value];}
  if ($i==19){$cronjob_buffer_items =$arr[option_value];}
  if ($i==20){$cronjob_days =$arr[option_value];}
  if ($i==21){$cronjob_email =$arr[option_value];}
  if ($i==22){$cronjob_hours =$arr[option_value];}
  if ($i==23){$cronjob_minutes =$arr[option_value];}
  if ($i==24){$cronjob_months =$arr[option_value];}
  if ($i==25){$cronjob_randomize =$arr[option_value];}
  if ($i==26){$cronjob_randomize_max =$arr[option_value];}
  if ($i==27){$cronjob_randomize_min =$arr[option_value];}
  if ($i==28){$cronjob_timeout =$arr[option_value];}
  if ($i==29){$cronjob_weekdays =$arr[option_value];}
  if ($i==30){$default_author= $arr[option_value];}
  if ($i==31){$draft_notification = $arr[option_value];}
  if ($i==32){$draft_notification_email = $arr[option_value];}
  if ($i==33){$drop_module = $arr[option_value];}
  if ($i==34){$keywords_module = $arr[option_value];}
  if ($i==35){$pixelpipe_module = $arr[option_value];}
  if ($i==36){$pixelpipe_email = $arr[option_value];}
  if ($i==37){$pixelpipe_mode = $arr[option_value];}
  if ($i==38){$pixelpipe_routing = $arr[option_value];}
  if ($i==39){$post_tags = $arr[option_value];}
  if ($i==40){$post_tags_typo = $arr[option_value];}
  if ($i==41){$proxy_bonanza_email = $arr[option_value];}
  if ($i==42){$proxy_bonanza_password = $arr[option_value];}
  if ($i==43){$proxy_bonanza_username = $arr[option_value];}
  if ($i==44){$proxy_bookmarking = $arr[option_value];}
  if ($i==45){$proxy_campaigns = $arr[option_value];}
  if ($i==46){$proxy_list = $arr[option_value];}
  if ($i==47){$proxy_type = $arr[option_value];}
  if ($i==48){$rss_module = $arr[option_value];}
  if ($i==49){$sources_module = $arr[option_value]; }  
  if ($i==50){$spin_exclude_cats = $arr[option_value];}
  if ($i==51){$spin_exclude_these = $arr[option_value];}
  if ($i==52){$spin_phrase_max = $arr[option_value];}
  if ($i==53){$spin_phrase_min = $arr[option_value];}
  if ($i==54){$store_images = $arr[option_value];}
  if ($i==55){$store_images_full_url = $arr[option_value]; }
  if ($i==56){$store_images_relative_path = $arr[option_value]; }  
  if ($i==57){$tags_custom = $arr[option_value];}
  if ($i==58){$tags_max = $arr[option_value];}
  if ($i==59){$tags_min = $arr[option_value];}
  if ($i==60){$tags_nature = $arr[option_value];}
  if ($i==61){$tbs_maxsyns = $arr[option_value];}
  if ($i==62){$tbs_password = $arr[option_value];}
  if ($i==63){$tbs_quality = $arr[option_value];}
  if ($i==64){$tbs_spinning = $arr[option_value];}
  if ($i==65){$tbs_username = $arr[option_value];}
  if ($i==66){$twitter_module = $arr[option_value];}
  if ($i==67){$twitter_hash = $arr[option_value];}
  if ($i==68){$twitter_mode = $arr[option_value];}
  if ($i==69){$twitter_oauth_apikey = $arr[option_value];}
  if ($i==70){$twitter_oauth_consumer_key = $arr[option_value];}
  if ($i==71){$twitter_oauth_consumer_secret = $arr[option_value];}
  if ($i==72){$twitter_oauth_secret = $arr[option_value];}
  if ($i==73){$twitter_oauth_token = $arr[option_value];}
  if ($i==74){$twitter_user = $arr[option_value];}
  if ($i==75){$video_module = $arr[option_value];}
  if ($i==76){$yahoo_module = $arr[option_value];}
  if ($i==77){$yahoo_api_key = $arr[option_value];}
}
//echo $yahoo_module_api_key;exit;
//echo $blog_comments;exit;
//echo $post_tags_typo; exit;
//echo $blogsense_api_secret_key;exit;
//echo $pixelpipe_mode;exit;

$proxy_array = explode("\r",$proxy_list);


$twitter_user = explode(";", $twitter_user);
$twitter_oauth_secret = explode(";", $twitter_oauth_secret);
$twitter_oauth_token = explode(";", $twitter_oauth_token);
$twitter_hash = explode(";", $twitter_hash);

$pixelpipe_email = explode(";", $pixelpipe_email);
$pixelpipe_routing = explode(";", $pixelpipe_routing);

$custom_fields_name = explode(",", $custom_fields_name);
$custom_fields_value = explode(",", $custom_fields_value);

if ($blog_comments=='1')
{
	$blog_comments='open';
}
else
{
	$blog_comments = 'closed';
}

//get blog url
$query = "SELECT option_value FROM ".$table_prefix."options WHERE option_name ='siteurl'";
$result = mysql_query($query);
$arr = mysql_fetch_array($result);
$blog_url = $arr['option_value'];
if (substr($blog_url,-1,1)!='/')
{
	$blog_url = $blog_url."/";
	//echo $blog_url;
}

if (function_exists('switch_to_blog')) switch_to_blog(1);
$query = "SELECT option_value FROM ".$table_prefix."options WHERE option_name ='siteurl'";
$result = mysql_query($query);
$arr = mysql_fetch_array($result);
$main_blog_url = $arr['option_value'];
if (substr($blog_url, -1 , 1)!='/')
{
	$main_blog_url = $blog_url."/";

}
if (function_exists('switch_to_blog')) switch_to_blog($_COOKIE['bs_blog_id']);

$query = "SELECT option_value FROM ".$table_prefix."options WHERE option_name ='default_category'";
$result = mysql_query($query);
$arr = mysql_fetch_array($result);
$default_category = $arr['option_value'];

//call seo profiles
$query = "SELECT * FROM ".$table_prefix."seoprofiles WHERE status=1";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error();exit;}

while ($arr = mysql_fetch_array($result))
{
   $keyphrase[] = $arr['keyphrase'];
   $decoration[] = $arr['decoration'];
   $href[] = $arr['url'];
   $class[] = $arr['class'];
   $rel[] = $arr['rel'];
   $target[] = $arr['target'];
   $limit[] = $arr['limit'];
}

if (!$keyphrase) {$keyphrase = array();}

//pull categories if there
$slugs= array();
$categories = array();
$cat_ids = array();
$query = "SELECT t.name, t.slug, t.term_id, tt.term_id, tt.term_taxonomy_id FROM ".$table_prefix."term_taxonomy tt,  ".$table_prefix."terms t WHERE tt.term_id = t.term_id AND tt.taxonomy='category' ORDER BY NULL";
$result = mysql_query($query);
if (!$result){echo $query; exit;}
while ($arr = mysql_fetch_array($result))
{
	$slugs[] = $arr['slug'];
	$categories[] = $arr['name'];
	$cat_ids[] = $arr['term_id'];
}

//echo print_r($categories);exit;
//pull authors
//prepare author list
$query = "SELECT * FROM ".$table_prefix."users";
$result = mysql_query($query);
if (!$result)
{
	if ($main_prefix)
	{
		$tp = $main_prefix;
	}
	else
	{
		$tp = explode("_" , $table_prefix);
		$tp= $tp[0]."_";
	}
	$query = "SELECT * FROM ".$tp."users";
	$result = mysql_query($query);
	
}

while ($arr = mysql_fetch_array($result))
{
   $authors_id[] = $arr['ID'];
   $authors_usernames[] = $arr['user_nicename'];
}

//prepare bookmarking queue
$query = "SELECT * FROM ".$table_prefix."posts_to_bookmark ORDER BY ID";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error(); exit;}

while ($arr = mysql_fetch_array($result))
{
   $bookmarks_id[] = $arr['id'];
   $bookmarks_post_id[] = $arr['post_id'];
   $bookmarks_date[] = $arr['date'];
   $bookmarks_hellotxt_status[] = $arr['hellotxt_status'];
   $bookmarks_twitter_status[] = $arr['twitter_status'];
   $bookmarks_pingfm_status[] = $arr['pingfm_status'];
}

//prepare campaigns
$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
if (!strstr($current_url, 'f_add_campaign.php'))
{
	$query = "SELECT * FROM ".$table_prefix."campaigns ORDER BY module_type ASC";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); exit;}

	while ($arr = mysql_fetch_array($result))
	{
		$campaign_id[] = $arr['id'];	
		$campaign_name[] = $arr['name'];	  
		$campaign_type[] = $arr['module_type'];	
		$campaign_status[] = $arr['campaign_status'];
		$campaign_source[] =  $arr['source'];	
		$campaign_query[] = $arr['query'];
		$campaign_feed[] = $arr['feed'];
		$campaign_author[] = $arr['author'];
		$campaign_limit_results[] = $arr['limit_results'];
		$campaign_include_keywords[] = $arr['include_keywords'];
		$campaign_exclude_keywords[] = $arr['exclude_keywords'];
		$campaign_category[] = $arr['category'];
		$campaign_autocategorize[] = $arr['autocategorize'];
		$campaign_autocategorize_search[] = $arr['autocategorize_search'];
		$campaign_autocategorize_method[] = $arr['autocategorize_method'];
		$campaign_autocategorize_filter_keywords[] = $arr['autocategorize_filter_keywords'];
		$campaign_autocategorize_filter_categories[] = $arr['autocategorize_filter_categories'];
		$campaign_autocategorize_filter_list[] = $arr['autocategorize_filter_list'];
		$campaign_autotag_method[] = $arr['autotag_method'];
		$campaign_autotag_custom_tags[] = $arr['autotag_custom_tags'];
		$campaign_autotag_min[] = $arr['autotag_min'];
		$campaign_autotag_max[] = $arr['autotag_max'];
		$campaign_language[] = $arr['language'];
		$campaign_spin_text[] = $arr['spin_text'];
		$campaign_strip_images[] = $arr['strip_images'];			
		$campaign_strip_links[] = $arr['strip_links'];
		$campaign_cloak_links[] = $arr['cloak_links'];
		$campaign_scrape_profile[] = $arr['scrape_profile'];
		$campaign_regex_search[] = $arr['regex_search'];
		$campaign_regex_replace[] = $arr['regex_replace'];
		$campaign_credit_source[] = $arr['credit_source'];
		$campaign_credit_source_nofollow[] = $arr['credit_source_nofollow'];
		$campaign_credit_source_text[] = $arr['credit_source_text'];
		$campaign_backdating[] = $arr['schedule_backdating'];
		$campaign_post_frequency[] = $arr['schedule_post_frequency'];
		$campaign_post_date[] = $arr['schedule_post_date'];
		$campaign_post_count[] = $arr['schedule_post_count'];
		$campaign_custom_field_name[] = $arr['custom_field_name'];
		$campaign_custom_field_value[] = $arr['custom_field_value'];
		$z_affiliate_id[] = $arr['z_affiliate_id'];
		$campaign_scrape_content[] = $arr['z_rss_scrape_content'];
		$campaign_scrape_content_begin_code[] =stripslashes($arr['z_rss_scrape_content_begin_code']);
		$campaign_scrape_content_end_code[] = stripslashes($arr['z_rss_scrape_content_end_code']);
		$campaign_scrape_comments[] = $arr['z_rss_scrape_comments'];
		$campaign_scrape_names_begin_code[] = stripslashes($arr['z_rss_scrape_names_begin_code']);
		$campaign_scrape_names_end_code[] = stripslashes($arr['z_rss_scrape_names_end_code']);
		$campaign_scrape_comments_begin_code[] = stripslashes($arr['z_rss_scrape_comments_begin_code']);
		$campaign_scrape_comments_end_code[] = stripslashes($arr['z_rss_scrape_comments_end_code']);
		$z_yahoo_option_category[] = $arr['z_yahoo_option_category'];
		$z_yahoo_option_date_range[] = $arr['z_yahoo_option_category'];
		$z_yahoo_option_region[] = $arr['z_yahoo_option_category'];
		$z_yahoo_option_results_limit[] = $arr['z_yahoo_option_category'];
		$z_yahoo_option_sorting[] = $arr['z_yahoo_option_sorting'];
		$z_yahoo_option_type[] = $arr['z_yahoo_option_category'];		
		$campaign_title_template[] = $arr['z_title_template'];
		$campaign_post_template[] = $arr['z_post_template'];
		$bookmark_twitter[] = $arr['z_bookmark_twitter'];
		$bookmark_pixelpipe[] = $arr['z_bookmark_pixelpipe'];
		$campaign_post_status[] = $arr['z_post_status'];
		$campaign_post_type[] = $arr['z_post_type'];
		$campaign_comments_include[] = $arr['z_comments_include'];
		$campaign_comments_limit[] = $arr['z_comments_limit'];
		$campaign_remote_publishing_api_bs[] = $arr['z_remote_publishing_api_bs'];
		$campaign_remote_publishing_api_xmlrpc[] = $arr['z_remote_publishing_api_xmlrpc'];
		$campaign_remote_publishing_api_xmlrpc_spin[] = $arr['z_remote_publishing_api_xmlrpc_spin'];
		$campaign_remote_publishing_api_email[] = $arr['z_remote_publishing_api_email'];
		$campaign_remote_publishing_api_email_footer[] = $arr['z_remote_publishing_api_email_footer'];
		$campaign_remote_publishing_api_pp_email[] = $arr['z_remote_publishing_api_pp_email'];
		$campaign_remote_publishing_api_pp_routing[] = $arr['z_remote_publishing_api_pp_routing'];
		$campaign_post_overwrite[] = $arr['z_post_overwrite'];
		$campaign_include_keywords_scope[] = $arr['z_include_keywords_scope'];
		$campaign_exclude_keywords_scope[] = $arr['z_exclude_keywords_scope'];
	}
}
//print_r($campaign_autocategorize_filter_list); exit;
if ($campaign_custom_field_name)
{
	foreach ($campaign_custom_field_name as $k=>$v)
	{
		$campaign_custom_field_name[$k] = explode('***', $campaign_custom_field_name[$k]);
		$campaign_custom_field_value[$k] = explode('***', $campaign_custom_field_value[$k]);
	}
}

//prepare_templates
$query = "SELECT * FROM ".$table_prefix."post_templates ORDER BY id DESC  ";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error();  exit;}
$count = mysql_num_rows($result);

//echo $count; exit;
if ($count>0)
{
   while ($arr = mysql_fetch_array($result))
   {
	 $template_id[] = $arr['id'];
	 $template_name[] = stripslashes($arr['name']);
	 $template_content[] = stripslashes($arr['content']);
	 $template_type[] = stripslashes($arr['type']);
   }
}

//prepare custom variables
$query = "SELECT * FROM ".$table_prefix."custom_tokens ORDER BY id DESC  ";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error();  exit;}
$count = mysql_num_rows($result);

//echo $count; exit;
if ($count>0)
{
   while ($arr = mysql_fetch_array($result))
   {
	$templates_custom_variable_id[] = $arr['id'];
	$templates_custom_variable_name[] = $arr['name'];
	$templates_custom_variable_token[] = $arr['token'];
	$templates_custom_variable_content[] = stripslashes($arr['content']);
   }
}


//prepare blocked items list
$query ="SELECT * from ".$table_prefix."blocked_urls";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error();  exit;}
while ($arr = mysql_fetch_array($result)){	$blocked_urls[] = $arr['url'];	}
if ($blocked_urls){ $blocked_urls = implode("\n",$blocked_urls); }

//check for phpZon
$query ="SELECT * from ".$table_prefix."options WHERE option_name='widget_phpzon_widget'";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error();  exit;}
if (mysql_num_rows($result)==1){ $phpZon = 1; }



//check for wpRobot
$query ="SELECT * from ".$table_prefix."options WHERE option_name='active_plugins'";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error();  exit;}
$array = mysql_fetch_array($result);
$plugins_string = $array['option_value']; 

if (strstr($plugins_string, 'WPRobot3'))
{
	$wpRobot = 1;
}
if (strstr($plugins_string, 'content-mage'))
{
	$wpMage = 1;
}	
if (strstr($plugins_string, 'phpBay'))
{
	$phpBay = 1;
}
if (strstr($plugins_string, 'phpZon'))
{
	$phpZon = 1;
}	
if (strstr($plugins_string, 'wpprosperent'))
{
	$prosperent = 1;
}		

if ($phpBay==1)
{
	$query ="SELECT * from ".$table_prefix."options WHERE option_name='PB_ebay_pid'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();  exit;}
	$array = mysql_fetch_array($result);
	$phpbay_campaign_id = $array['option_value'];	
}


function bsense_url()
{ 
	$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
	if (strstr($current_url, 'f_add_campaign.php'))	{ 
		$current_url = explode("functions/f_add_campaign.php",$current_url); 
		$current_url = $current_url[0];
	}
	if (strstr($current_url, 'f_activate_blogsense.php'))	{ 
		$current_url = explode("functions/f_activate_blogsense.php",$current_url); 
		$current_url = $current_url[0];
	}
	if (strstr($current_url, 'solo_run.php'))	{ 
		$current_url = explode("solo_run.php",$current_url); 
		$current_url = $current_url[0];
	}
	if (strstr($current_url, 'preview.php'))	{ 
		$current_url = explode("preview.php",$current_url); 
		$current_url = $current_url[0];
	}
	if (strstr($current_url, 'cron_config.php'))	{ 
		$current_url = explode("cron_config.php",$current_url); 
		$current_url = $current_url[0];
	}

	if (strstr($current_url, 'heartbeat.php'))	{ 
		$current_url = explode("heartbeat.php",$current_url); 
		$current_url = $current_url[0];
	}

	if (strstr($current_url, 'prepare_variables.php'))	{ 
		$current_url = explode("includes/prepare_varaibles.php",$current_url); 
		$current_url = $current_url[0];
	}
	
	if (strstr($current_url, 'i_twitter_add_account.php'))	{ 
		$current_url = explode("includes/i_twitter_add_account.php",$current_url); 
		$current_url = $current_url[0];
	}
	
	if (strstr($current_url, 'index.php'))	{ 
		$current_url = explode("index.php",$current_url); 
		$current_url = $current_url[0];
	}

	return $current_url;
}

$blogsense_url = bsense_url();
$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
$wordpress_date_time =  date_i18n($timezone_format);


?>