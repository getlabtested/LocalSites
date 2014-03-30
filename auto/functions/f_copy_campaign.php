<?php
//include_once("../includes/php_version_check.php");
include_once('./../../wp-config.php');
session_start();
include("./../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../login.php");
set_time_limit(700);
ini_set("magic_quotes_runtime", 0);
include_once("../includes/helper_functions.php");
//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}
include_once('./../includes/prepare_variables.php');
$module_type = $_GET['module_type'];

$campaign_id = $_GET['id'];
$query = "INSERT INTO `{$table_prefix}campaigns` (`name`,`campaign_status`,`module_type`,`source`,`query`,`feed`,`limit_results`,`author`,`include_keywords`,`exclude_keywords`,`category`,`autocategorize`,`autocategorize_search`,`autocategorize_method`,`autocategorize_filter_keywords`,`autocategorize_filter_categories`,`autotag_method`,`autotag_custom_tags`,`autotag_min`,`autotag_max`,`language`,`spin_text`,`strip_images`,`strip_links`,`cloak_links`,`image_floating`,`scrape_profile`,`regex_search`,`regex_replace`,`credit_source`,`credit_source_nofollow`,`credit_source_text`,`schedule_post_frequency`,`schedule_post_date`,`schedule_post_count`,`custom_field_name`,`custom_field_value`,`z_affiliate_id`,`z_bookmark_twitter`,`z_rss_scrape_content`,`z_rss_scrape_content_begin_code`,`z_rss_scrape_content_end_code`,`z_video_include_description`,`z_yahoo_option_category`,`z_yahoo_option_date_range`,`z_yahoo_option_region`,`z_yahoo_option_results_limit`,`z_yahoo_option_sorting`,`z_yahoo_option_type`,`z_post_template`,`z_title_template`,`z_post_status`,`z_comments_include`,`z_comments_limit`,`z_include_keywords_scope`,`z_exclude_keywords_scope`,`z_post_overwrite`) ( SELECT name,campaign_status,module_type,source,query,feed,limit_results,author,include_keywords,exclude_keywords,category,autocategorize,autocategorize_search,autocategorize_method,autocategorize_filter_keywords,autocategorize_filter_categories,autotag_method,autotag_custom_tags,autotag_min,autotag_max,language,spin_text,strip_images,strip_links,cloak_links,image_floating,scrape_profile,regex_search,regex_replace,credit_source,credit_source_nofollow,credit_source_text,schedule_post_frequency,schedule_post_date,schedule_post_count,custom_field_name,custom_field_value,z_affiliate_id,z_bookmark_twitter,z_rss_scrape_content,z_rss_scrape_content_begin_code,z_rss_scrape_content_end_code,z_video_include_description,z_yahoo_option_category,z_yahoo_option_date_range,z_yahoo_option_region,z_yahoo_option_results_limit,z_yahoo_option_sorting,z_yahoo_option_type,z_post_template,z_title_template,z_post_status,z_comments_include,z_comments_limit,z_include_keywords_scope,z_exclude_keywords_scope,z_post_overwrite FROM `{$table_prefix}campaigns` WHERE id='{$campaign_id}')";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error(); exit;}

echo "<center><br><br><br><br><font color=green>Campaign Copied. Please Exit to Refresh. </center></font>";
exit;
?>