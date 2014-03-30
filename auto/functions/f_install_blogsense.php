<?php
//***********************************************************************
//******************helper functions*****************************************
//***********************************************************************	
function get_string_between($string, $start, $end){
   $string = " ".$string;
     $ini = strpos($string,$start);
     if ($ini == 0) return "";
     $ini += strlen($start);   
     $len = strpos($string,$end,$ini) - $ini;
     return substr($string,$ini,$len);
}

function files_in_directory($start_dir)
{
	//returns array of files in directory
     $files = array();
     $dir = opendir($start_dir);
     while(($myfile = readdir($dir)) !== false)
     {
         if($myfile != '.' && $myfile != '..' && !is_file($myfile) && $myfile != 'resource.frk' && !eregi('^Icon',$myfile) )
         {
             $files[] = $myfile;
         }
     }
     closedir($dir);
     return $files;
}

function blogsense_url()
{
	$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
	$current_url = explode("functions/f_activate_blogsense.php",$current_url); 
	$current_url = $current_url[0];
	
	
	return $current_url;
}

function wordpress_url($table_prefix)
{
	$query = "SELECT `option_name`, `option_value` FROM ".$table_prefix."options WHERE ";
	$query .= "`option_name`='siteurl'";
	$result = mysql_query($query);
	if (!$result){echo $query; exit;}
	$count = mysql_num_rows($result);

	for ($i=0;$i<$count;$i++)
	{
	  $arr = mysql_fetch_array($result);
	  if ($i==0){$wordpress_url = $arr[option_value];}
	}
	
	return $wordpress_url;
}

//blogsense_url
$blogsense_url = blogsense_url();
//****************CREATE TABLES**********//	

//autoblog table
  $query = "CREATE TABLE `".$table_prefix."blogsense` (";
  $query .= "`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
  $query .= "`option_name` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`option_value` MEDIUMTEXT NOT NULL, UNIQUE (`option_name`) )";
  $result = mysql_query($query);
  if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
//autoblog table
  $query = "CREATE TABLE `".$table_prefix."autoblog` (";
  $query .= "`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
  $query .= "`module_type` VARCHAR( 50 ) NOT NULL ,";
  $query .= "`feed` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`count` INT( 2 ) NOT NULL ,";
  $query .= "`last_scheduled` DATETIME NOT NULL)";

  $result = mysql_query($query);
  if (!$result){echo $query; echo mysql_error(); echo "<br>";} 

//source data table
  $query = "CREATE TABLE `".$table_prefix."sourcedata` (";
  $query .= "`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
  $query .= "`source_url` VARCHAR( 225 ) NOT NULL UNIQUE,";
  $query .= "`footprint` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`title_start` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`title_start_backup_1` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`title_start_backup_2` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`title_end` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`title_end_backup_1` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`title_end_backup_2` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`content_start` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`content_start_backup_1` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`content_start_backup_2` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`content_end` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`content_end_backup_1` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`content_end_backup_2` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`comments_status` INT( 2 ) NOT NULL ,";
  $query .= "`comments_name_start` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`comments_name_end` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`comments_content_start` VARCHAR( 225 ) NOT NULL ,";
  $query .= "`comments_content_end` VARCHAR( 225 ) NOT NULL ,";  
  $query .= "`regex_search` VARCHAR( 225 ) NOT NULL ,";  
  $query .= "`regex_replace` VARCHAR( 225 ) NOT NULL )";   
  $result = mysql_query($query);
  if (!$result){echo $query; echo mysql_error(); echo "<br>";} 
  

//place in source data
  $query = "INSERT INTO `".$table_prefix."sourcedata` (`id`, `source_url`, `footprint`, `title_start`, `title_end`, `content_start`, `content_end`, `comments_status`, `comments_name_start`, `comments_name_end`, `comments_content_start`, `comments_content_end`) VALUES
			(1, 'http://www.articlemonkeys.com/', '' , '<title>', '</title>', '<script type=\"text/javascript\" src=\"http://tweetmeme.com/i/scripts/button.js\"></script></span>', '<!-- google_ad_section_end -->', 0, '', '', '', ''),
			(2, 'http://www.amazines.com/', 'articleid -related -\"search results\" -\"author information\"' ,'<span name=KonaFilter><font size=\"4\"><b>', '</b>', '<tr><td colspan=2><img src=\"/images/spacer.gif\" height=\"5\" width=\"1\"></td></tr>', '<p><table border=0 width=100%><tr>', 0, '', '', '', ''),
			(3, 'http://www.articlealley.com/', '', '<title>', '</title>', '<td class=\"articletext\"><div class=\"KonaBody\">', '<!-- google_ad_section_end -->', 0, '', '', '', ''),
			(4, 'http://www.buzzle.com/', '' ,'<title>', '</title>', '</h1>', '<!-- google_ad_section_end -->', 0, '', '', '', ''),
			(5, 'http://www.theallineed.com/', '', '<TD COLSPAN=3 CLASS=titulo1 WIDTH=100% HEIGHT=15><H1>', '</H1>', '<TR><TD CLASS=contenido WIDTH=100%>', '<TD COLSPAN=3 CLASS=subtitulo2 WIDTH=100% HEIGHT=15>About the Author</TD></TR>', 0, '', '', '', ''),
			(6, 'http://www.articlecity.com/', '', '<b class=titler>', '</b>', '---- End Ad Box --->', '<p><b>About The Author</b><br>', 0, '', '', '', ''),
			(7, 'http://www.articlesbase.com/', 'originurlextension:html' , '<title>', '</title>', '<div class=\"article_cnt KonaBody\">', '<div class=\"printfooter\">', 1, '<div class=\"text_field\">', '</strong>', '<div class=\"comment_text\">', '</div>'),
			('8', 'http://news.yahoo.com/', 'site:news.yahoo.com -inurl:topics' , '<h1 id=\"yn-title\">', '</h1>', '<div class=\"yn-story-content\">', '</div>', 1, '</span></div><cite>', '</strong>', '<blockquote class=\"mwpphu-commenttext\">', '</blockquote>')
			";

  $result = mysql_query($query);
  if (!$result){echo $query; echo mysql_error(); echo "<br>";} 
  
//link cloaking table

  $query = "CREATE TABLE `".$nuprefix."cloaking` (";
  $query .= "`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
  $query .= "`ref` VARCHAR( 50 ) NOT NULL ,";
  $query .= "`url` VARCHAR( 225 ) NOT NULL)";
  $result = mysql_query($query);
  //if (!$result){echo $query; echo mysql_error(); echo "<br>";} 
  
//add post_templates  table
	$query = "CREATE TABLE `".$table_prefix."post_templates` (";
	$query .= "`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
	$query .= "`name` VARCHAR( 200 ) NOT NULL ,";
	$query .= "`content` MEDIUMTEXT NOT NULL,";
	$query .= "`type` VARCHAR( 200 ) NOT NULL)";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";} 
	
	$token_rss_template = "<br><br><strong>%title%</strong><br><br>%description%<br><br>Original Source: %link%";
	
	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Shortcode Template: RSS', '$token_rss_template' , 'token_rss_template')";
	$result2 = mysql_query($query2);
	
	$token_flickr_template = addslashes("<br><img src='%link%' class='fklr_images' id='id_flkr_image_%count%' style='max-width:%maxwidth%px;max-height:%maxheight%px;padding:5px;' ><br><br>");
	
	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Shortcode Template: Flickr', '$token_flickr_template' , 'token_flickr_template')";
	$result2 = mysql_query($query2);
	
	$token_google_images_template = addslashes("<br><img src='%imgsrc%' class='glg_images' id='id_glg_image_%count%' alt='%imgtitle%' style='max-width:%maxwidth%px;max-height:%maxheight%px;padding:5px;' ><br><br>");

	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Shotcode Template: Google Images', '$token_google_images_template' , 'token_google_images_template')";
	$result2 = mysql_query($query2);
	
	$youtube_object_template = "<object width=\"425\" height=\"344\"><param name=\"movie\" value=\"%embed_link%\">";
	$youtube_object_template .="</param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\">";
	$youtube_object_template .="</param><embed src=\"%embed_link%\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"425\" height=\"344\"></embed></object>";
			
	$youtube_object_template = addslashes($youtube_object_template);

	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Post Template: Youtube Object', '$youtube_object_template' , 'youtube_object_template')";
	$result2 = mysql_query($query2);
		
	//add amazon widget token template
	$token_amazon_widget_template = "";
	$token_amazon_widget_template .= "<div style='width: 20%;padding:10px;float:left;' class='amn_items\' >\n";
	$token_amazon_widget_template .= "		<div class='amn_items_image' style='padding-bottom:5px;'>\n";
	$token_amazon_widget_template .= "		<a href='%link%' target=_blank><img alt='%keywords%' src='%image_link%' style='width:75px;height:75px' border=0></a>\n";
	$token_amazon_widget_template .= "		</div>\n";
	$token_amazon_widget_template .= "		  <div class='amn_items_title'  style='height:110px;padding-bottom:10px;'>\n";
	$token_amazon_widget_template .= "	 	  <div>\n";
	$token_amazon_widget_template .= "	 	     <a href='%link%' target=_blank class='amn_links'>%title%</a> \n";
	$token_amazon_widget_template .= "	 	  </div>\n";
	$token_amazon_widget_template .= "	 	 </div>\n";
	$token_amazon_widget_template .= "  		<div class='amn_items_button' align='center'><a href='%link%'><img height='17' border='0' width='56' alt='Get Item' src='{$blogsense_url}/nav/btn_a_get_item.gif'></a></div>\n";
	$token_amazon_widget_template .= "</div>\n";
	$token_amazon_widget_template = addslashes($token_amazon_widget_template);
	
	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Shortcode Template: Amazon Widget', '$token_amazon_widget_template' , 'token_amazon_widget_template')";
	$result2 = mysql_query($query2);
	
	$token_amazon_review_template = "<br><strong><a href='%link%' style='text-transform:uppercase;'>%title%</a></strong> <b>(%price%)</b><br><br>\n";
	$token_amazon_review_template .= "<div class='amn_wrapper' >\n";
	$token_amazon_review_template .= "		<div style='float:left;padding-right:10px;padding-bottom:10px;' >\n";
	$token_amazon_review_template .= "			<a href='%link%' rel=nofollow target=_blank><img height='75'  width='75' border='0' src='%image_link%' ></a>\n";
	$token_amazon_review_template .= "		</div>\n";
	$token_amazon_review_template .= "  		<div class='amz_review' align='left' style=''>\n";
	$token_amazon_review_template .= "  		%customer_review%\n";
	$token_amazon_review_template .= "  		</div>\n";
	$token_amazon_review_template .= "</div>\n";
	$token_amazon_review_template = addslashes($token_amazon_review_template);

	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Shortcode Template: Amazon Reviews', '$token_amazon_review_template' , 'token_amazon_review_template')";
	$result2 = mysql_query($query2);

	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Default Title Template', '%title%' , 'default_title_template')";
	$result2 = mysql_query($query2);
	
	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Default RSS Post Template', '%postbody%' , 'default_rss_post_template')";
	$result2 = mysql_query($query2);
	
	$default_video_template = "<center>%video_embed%</center>

<div style='margin-top:29px;text-align:left;width:100%;overflow:hidden;'>
  <br>
  <img src='%video_thumbnail%' align='right' border='0' style='padding-left:10px;max-width:150px;'>
  <div >%video_description%</div>
</div>";
	
	
	$default_video_template = addslashes($default_video_template);
	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Default Video Post Template', '$default_video_template' , 'default_video_post_template')";
	$result2 = mysql_query($query2);
	
	
	
	$youtube_shortcode_template = "<center>%video_embed%</center>

<div style='margin-top:29px;text-align:left;width:100%;overflow:hidden;'>
  <br>
  <img src='%video_thumbnail%' align='right' border='0' style='padding-left:10px;max-width:150px;'>
  <div >%video_description%</div>
</div>";
	
	
	$default_video_template = addslashes($default_video_template);
	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Shortcode Template: Youtube', '$youtube_shortcode_template' , 'token_youtube_template')";
	$result2 = mysql_query($query2);
	
	$query = "ALTER IGNORE TABLE `".$table_prefix."post_templates` ADD UNIQUE INDEX(name) "; 
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		
		
		$youtube_shortcode_template = "<center>%video_embed%</center>

<div style='margin-top:29px;text-align:left;width:100%;overflow:hidden;'>
  <br>
  <img src='%video_thumbnail%' align='right' border='0' style='padding-left:10px;max-width:150px;'>
  <div >%video_description%</div>
</div>";
	
	$default_amazon_template = "<h3>%title% - %amazon_price%</h3>

<a href=\"%link%\" target=_blank><img src='%amazon_medium_image_url%' style='float:right;padding:20px' alt='%tag_title%' border=0></a>

%amazon_customer_review_content_1%


<h3>More Details</h3>

%amazon_product_features%

%amazon_product_description%


<div style='text-align:right'><a href='%link%' target=_blank><img src='%amazon_buyitnow_button%' border=0></a></div>";
	$default_amazon_template = addslashes($default_amazon_template);
	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Default Amazon Post Template', '$default_amazon_template' , 'default_amazon_post_template')";
	$result2 = mysql_query($query2);
	
	
	$knolgoogle_template = "<h3>%title%</h3><br>
%postbody%
";
	$knolgoogle_template = addslashes($knolgoogle_template);
	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Shortcode Template: Knol.Google', '$knolgoogle_template' , 'token_articlebase_template')";
	$result2 = mysql_query($query2);
	
	$articlebase_template = "<h3>%title%</h3><br>
%postbody%
";
	$articlebase_template = addslashes($articlebase_template);
	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Shortcode Template: Articlebase', '$articlebase_template' , 'token_articlebase_template')";
	$result2 = mysql_query($query2);
	
	
	$ezine_template = "<h3>%title%</h3><br>
%postbody%
";
	$ezine_template = addslashes($ezine_template);
	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Shortcode Template: EzineArticles', '$ezine_template' , 'token_ezine_template')";
	$result2 = mysql_query($query2);
	
	$associatedcontent_template = "<h3>%title%</h3><br>
%postbody%
";
	$associatedcontent_template = addslashes($associatedcontent_template);
	$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
	$query2 .="VALUES ('', 'Shortcode Template: AssociatedContent', '$associatedcontent_template' , 'token_associatedcontent_template')";
	$result2 = mysql_query($query2);
	

//add post_templates  table
	$query = "CREATE TABLE `".$table_prefix."custom_tokens` (";
	$query .= "`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
	$query .= "`name` VARCHAR( 200 ) NOT NULL ,";
	$query .= "`token` VARCHAR( 200 ) NOT NULL,";
	$query .= "`content` MEDIUMTEXT NOT NULL)";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";} 
	
//add seoprofile table
	$query = "CREATE TABLE `".$table_prefix."seoprofiles` (";
	$query .= "`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
	$query .= "`keyphrase` VARCHAR( 225 ) NOT NULL ,";
	$query .= "`decoration` VARCHAR( 100 ) NOT NULL,";
	$query .= "`url` VARCHAR( 200 ) NOT NULL,";
	$query .= "`limit` VARCHAR( 2 ) NOT NULL,";
	$query .= "`class` VARCHAR( 100 ) NOT NULL,";
	$query .= "`rel` VARCHAR( 20 ) NOT NULL,";
	$query .= "`target` VARCHAR( 20 ) NOT NULL,";
	$query .= "`status` INT( 1 ) NOT NULL)";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";} 
	
//campigns table
	$query = "CREATE TABLE IF NOT EXISTS `".$table_prefix."campaigns` (";
	$query .= "  `id` int(11) NOT NULL AUTO_INCREMENT,";
	$query .="  `name` varchar(400) NOT NULL,";
	$query .="  `campaign_status` int(1) NOT NULL,";
	$query .="  `module_type` varchar(15) NOT NULL,";
	$query .="  `source` varchar(400) NOT NULL,";
	$query .="  `query` TEXT NOT NULL,";
	$query .="  `feed` varchar(500) NOT NULL,";
	$query .="  `limit_results` int(4) NOT NULL,";
	$query .="  `author` varchar(50) NOT NULL,";
	$query .="  `include_keywords` varchar(700) NOT NULL,";
	$query .="  `exclude_keywords` varchar(700) NOT NULL,";
	$query .="  `category` varchar(100) NOT NULL,";
	$query .="  `autocategorize` int(1) NOT NULL,";
	$query .="  `autocategorize_search` int(1) NOT NULL,";
	$query .="  `autocategorize_method` int(1) NOT NULL,";
	$query .="  `autocategorize_filter_keywords` VARCHAR(225) NOT NULL,";
	$query .="  `autocategorize_filter_categories` varchar(225) NOT NULL,";
	$query .="  `autocategorize_filter_list` MEDIUMTEXT NOT NULL,";
	$query .="  `autotag_method` INT(1) NOT NULL,";
	$query .="  `autotag_custom_tags` MEDIUMTEXT NOT NULL,";
	$query .="  `autotag_min` INT(2) NOT NULL,";
	$query .="  `autotag_max` INT(2) NOT NULL,";
	$query .="  `language` varchar(20) NOT NULL,";
	$query .="  `spin_text` int(1) NOT NULL,";
	$query .="  `strip_images` int(1) NOT NULL,";
	$query .="  `strip_links` int(1) NOT NULL,";
	$query .="  `cloak_links` int(1) NOT NULL,";
	$query .="  `image_floating` varchar(10) NOT NULL,";
	$query .="  `scrape_profile` int(2) NOT NULL,";
	$query .="  `regex_search` varchar(1000) NOT NULL,";
	$query .="  `regex_replace` varchar(1000) NOT NULL,";
	$query .="  `credit_source` int(1) NOT NULL,";
	$query .="  `credit_source_nofollow` int(1) NOT NULL,";
	$query .="  `credit_source_text` varchar(400) NOT NULL,";
	$query .="  `schedule_backdating` INT(1) NOT NULL,";
	$query .="  `schedule_post_frequency` varchar(20) NOT NULL,";
	$query .="  `schedule_post_date` datetime NOT NULL,";
	$query .="  `schedule_post_count` varchar(5) NOT NULL,";
	$query .="  `custom_field_name` TEXT NOT NULL,";
	$query .="  `custom_field_value` TEXT NOT NULL,";
	$query .="  `z_affiliate_id` varchar(20) NOT NULL,";
	$query .="  `z_bookmark_pixelpipe` TEXT NOT NULL,";
	$query .="  `z_bookmark_twitter` TEXT NOT NULL,";
	$query .="  `z_rss_scrape_content` int(1) NOT NULL,";
	$query .="  `z_rss_scrape_comments` int(1) NOT NULL,";
	$query .="  `z_rss_scrape_content_begin_code` text NOT NULL,";
	$query .="  `z_rss_scrape_content_end_code` text NOT NULL,";
	$query .="  `z_rss_scrape_comments_begin_code` text NOT NULL,";
	$query .="  `z_rss_scrape_comments_end_code` text NOT NULL,";
	$query .="  `z_rss_scrape_names_begin_code` text NOT NULL,";
	$query .="  `z_rss_scrape_names_end_code` text NOT NULL,";
	$query .="  `z_video_include_description` int(1) NOT NULL,";
	$query .="  `z_yahoo_option_category` varchar(20) NOT NULL,";
	$query .="  `z_yahoo_option_date_range` varchar(15) NOT NULL,";
	$query .="  `z_yahoo_option_region` varchar(15) NOT NULL,";
	$query .="  `z_yahoo_option_results_limit` varchar(15) NOT NULL,";
	$query .="  `z_yahoo_option_sorting` varchar(15) NOT NULL,";
	$query .="  `z_yahoo_option_type` varchar(15) NOT NULL,";
	$query .="  `z_title_template` TEXT (1000) NOT NULL,";
	$query .="  `z_post_template` TEXT (1000) NOT NULL,";
	$query .="  `z_post_status` VARCHAR (225) NOT NULL,";
	$query .="  `z_post_type` VARCHAR (225) NOT NULL,";
	$query .="  `z_comments_include`  INT(1) NOT NULL,";
	$query .="  `z_comments_limit`  INT(3) NOT NULL,";
	$query .="  `z_remote_publishing_api_bs` TEXT NOT NULL,";
	$query .="  `z_remote_publishing_api_pp_email` TEXT NOT NULL,";
	$query .="  `z_remote_publishing_api_pp_routing` TEXT NOT NULL,";
	$query .="  `z_remote_publishing_api_email` TEXT NOT NULL,";
	$query .="  `z_remote_publishing_api_email_footer` TEXT NOT NULL,";
	$query .="  `z_remote_publishing_api_xmlrpc` TEXT NOT NULL,";
	$query .="  `z_remote_publishing_api_xmlrpc_spin` TEXT NOT NULL,";
	$query .="  `z_post_overwrite` INT(1) NOT NULL,";
	$query .="  `z_exclude_keywords_scope` INT(1) NOT NULL,";
	$query .="  `z_include_keywords_scope` INT(1) NOT NULL,";
	$query .="  PRIMARY KEY (`id`)";
	$query .=") ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); exit;}

//blocked urls table
	$query = "CREATE TABLE `".$table_prefix."blocked_urls` (
				`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`campaign_id` INT( 10 ) NOT NULL ,
				`url` VARCHAR( 230 ) NOT NULL UNIQUE
				) TYPE=MyISAM";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
//bookmarks table
	$query = "CREATE TABLE `".$table_prefix."posts_to_bookmark` (
				`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`post_id` INT( 10 ) NOT NULL ,
				`permalink` VARCHAR( 225 ) NOT NULL ,
				`date` DATETIME NOT NULL,
				`nature` VARCHAR( 225 ) NOT NULL,
				`account` VARCHAR( 225 ) NOT NULL,
				`content` TEXT NOT NULL,
				`status` INT(1) NOT NULL
				) ENGINE = MYISAM ;";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	$query = "CREATE TABLE `".$table_prefix."blogsense_remote_published_urls` (
				`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`permalink` VARCHAR ( 225 ) NOT NULL,
				`title` VARCHAR ( 225 ) NOT NULL , 
				`date` DATETIME NOT NULL
				) ENGINE = MYISAM ;";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}

	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_draft_notification','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_draft_notification_email','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
   
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_blogsense_url','$blogsense_url')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//wordpress_url
	$wordpress_url = wordpress_url($table_prefix);
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_blog_url','$wordpress_url')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//blogsense_api_secret_key
	$secret_phrase ='blogsenseapisecretkey0123456789$';
	$key = str_shuffle($secret_phrase);
	$secret_key = substr($key, 0, 20);
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_api_secret_key','$secret_key')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	
	//cronjob primer
	$cron_script_url = $blogsense_url."heartbeat.php?blog_id=".$_COOKIE['bs_blog_id'];
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_primer','0')";
	$result = mysql_query($query);
	
	//cronjob running
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}//cronjob script url
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_running','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cronjob running safety count
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}//cronjob script url
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_running_safety_count','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	
	//cronjob script url
	$cron_script_url = $blogsense_url."heartbeat.php?blog_id=".$_COOKIE['bs_blog_id'];
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_script_heartbeat','$cron_script_url')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cronjob script url
	$cron_script_url = $blogsense_url."cron_config.php?blog_id=".$_COOKIE['bs_blog_id'];
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_script_cronconfig','$cron_script_url')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}

	//cron email
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_email','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}

	//cron minutes
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_minutes','20')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cron hours
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_hours','*/4')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cron days
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_days','*')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cron months
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_months','*')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cron weekdays
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_weekdays','*')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cron randomize
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_randomize','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cron randomize min
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_randomize_min','7')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cron randomize min
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_randomize_max','20')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cron item buffer
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_buffer_items','500000')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cron campaign buffer
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_buffer_campaigns','1000000')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//cron timeout
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cron_timeout','1000')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Bitly Username
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_bookmarking_bitly_username','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Bitly APIKey
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_bookmarking_bitly_apikey','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Bookmarking minutes min
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_bookmarking_ping_module','1')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Bookmarking minutes min
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_bookmarking_minutes_min','20')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Bookmarking minutes max
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_bookmarking_minutes_max','45')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Bookmarking percentage
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_bookmarking_percentage','100')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
			
//****************4.5.x addins**********//

	//Tag to Cateogry
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_tag_to_category','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//twitter module
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_twitter','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	//twitter mode
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_twitter_mode','random')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	//Twitter Usernames
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_twitter_user','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Twitter oauth
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_twitter_oauth_token','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Twitter oauth
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_twitter_oauth_secret','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Twitter oauth
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_twitter_oauth_apikey','UQtFLhrsnw9dRfJHVhFFw')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Twitter oauth
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_twitter_oauth_consumer_secret','qKs6dMnE6OoOqraJfoSZ60wSogQACwmscT21bybRHE')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Twitter oauth
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_twitter_oauth_consumer_key','6NohqJxwqiDMm2xWfzdYGg')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Twitter Hash Additions
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_twitter_hash','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//PingFM Module
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_pixelpipe','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//PingFM mode
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_pixelpipe_mode','random')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	//PingFM mode
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_pixelpipe_email','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	//PingFM mode
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_pixelpipe_routing','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	
	//Proxy List
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_proxy_list','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Proxy type
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_proxy_type','http')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Proxy type
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_proxy_bookmarking','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Proxy type
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_proxy_campaigns','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Proxy Bonanza Username
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_proxy_bonanza_username','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//Proxy Bonanza Password
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_proxy_bonanza_password','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}

	//add place to turn sources campaigns on or off
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_cloaking_redirect','none')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//add place to turn sources campaigns on or off
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_sources_module','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_default_author','1')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
	//add collumn to wp_posts
	$query = "ALTER TABLE `".$table_prefix."posts` ADD original_source TEXT";
	$result = mysql_query($query);
	
	//add collumn to wp_posts
	$query = "ALTER TABLE `".$table_prefix."posts` ADD bs_campaign_id INT( 4 )";
	$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		//echo "success. new tables added. ready.";
	
//****************Inset SQL into Wordpress***********//
//blogsense build
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_build_version','$buld_version')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}

//blog installation
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_activation','1')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}


//blog activation
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_activation_key','$license_key')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}

//blog activation email
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_activation_email','$license_email')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}	



	
//blog comments
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_blog_comments','1')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}


//rss module
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_rss_module','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
//rss module
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_keywords_module','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
//post tags module
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_post_tags','1')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
//post tags typo
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_post_tags_typo','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
//store images locally module
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_store_images','1')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}

//store images relative page
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_store_images_relative_path','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
//store images full url
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_store_images_full_url','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}

//image floating
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_image_floating','left')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
//image alt setup
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_image_alt_setup','1')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	

//amazon module
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_amazon','0')";
	$result = mysql_query($query);
	if (!$result){echo $query;echo mysql_error(); echo "<br>";}

	
//amazon affiliate-id
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_amazon_affiliate_id','httpwwwblogse-20')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
//amazon aws access key
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_amazon_aws_access_key','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}

//amazon secret access key
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_amazon_secret_access_key','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>";}


 //Drop Posting Module
	$query = "INSERT INTO `".$table_prefix."blogsense` (`id` ,`option_name` ,`option_value` )";
	$query .="VALUES ('', 'blogsense_drop', '0')";
	$result = mysql_query($query);
	if (!$result){echo $query; }
	
 //Yahoo Answers Module
	$query = "INSERT INTO `".$table_prefix."blogsense` (`id` ,`option_name` ,`option_value` )";
	$query .="VALUES ('', 'blogsense_yahoo', '0')";
	$result = mysql_query($query);
	if (!$result){echo $query; }
	
 //Yahoo Answers Module
	$query = "INSERT INTO `".$table_prefix."blogsense` (`id` ,`option_name` ,`option_value` )";
	$query .="VALUES ('', 'blogsense_yahoo_api_key', 'YahooDemo')";
	$result = mysql_query($query);
	if (!$result){echo $query; }

	
//video module
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_video','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	

//custom tagging
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_tags_nature','yahoo_terms')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }

	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_tags_custom','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }

	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_tags_min','5')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_tags_max','7')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_spin_phrase_min','2')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_spin_phrase_max','5')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_spin_exclude_cats','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_spin_exclude_these','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_tbs_spinning','0')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_tbs_maxsyns','3')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_tbs_quality','1')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }

	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_tbs_username','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
	
	$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
	$query.= "VALUES ('', 'blogsense_tbs_password','')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); echo "<br>"; }

	
	
//***********************************************************************
//******************Load Plugins*******************************************
//***********************************************************************	
$plugins_folder = "../includes/plugins/";
$plugins = files_in_directory($plugins_folder);	

if (count($plugins>0))
{
  foreach ($plugins as $key => $value)
  {
        $include = $plugins_folder.$value;
		include_once($include);
   }
}

include_once('../includes/importer.php');
//***********************************************************************
//******************Success!**********************************************
//***********************************************************************	

header("Location: ../index.php");



?>