<?php
    include_once('../wp-config.php');
	if (!isset($_SESSION)) { session_start();}
	//check for multisite
	if (function_exists('switch_to_blog')){
	 switch_to_blog(1);
	 switch_to_blog($_COOKIE['bs_blog_id']);
	}
	require_once('includes/build_version.php');
	require_once ('../wp-blog-header.php');
	require_once ('../wp-includes/registration.php');
	//require_once('includes/prepare_variables.php');
	
	$nuprefix = explode('_',$table_prefix);
	$nuprefix= $nuprefix[0]."_";
	
	//echo $table_prefix;exit;
	//PREPARE FUNCTIONS
	if (!function_exists('updatesql_bsense_url')) 
	{
		function updatesql_bsense_url()
		{ 
			$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";

			if (strstr($current_url,'index.php'))
			{
				$current_url = explode("index.php",$current_url); 
			}
			else if(strstr($current_url,'update.php'))
			{
				$current_url = explode("update.php",$current_url); 
			}
			else
			{
				$current_url = explode("update_sql.php",$current_url); 
			}
			$current_url = $current_url[0];
			
			
			return $current_url;
		}
	}

	if (!function_exists('wordpress_url')) 
	{
		function wordpress_url($table_prefix)
		{
			$wordpress_url = get_bloginfo( url);
			return $wordpress_url;
		}
		
	}
	
	//blogsense_url
	$blogsense_url = updatesql_bsense_url();
	
	//CREATE TABLES
		$query = "CREATE TABLE `".$table_prefix."blogsense` (";
		$query .= "`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
		$query .= "`option_name` VARCHAR( 255 ) NOT NULL ,";
		$query .= "`option_value` LONGTEXT NOT NULL , UNIQUE(`option_name`))";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); exit;}
		
		//alter content block table due to mistake in old version
		$query = "ALTER TABLE `".$table_prefix."blogsense` MODIFY option_value LONGTEXT NOT NULL";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();exit;} 
		
		
		//add post_templates  table
		$query = "CREATE TABLE `".$table_prefix."post_templates` (";
		$query .= "`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
		$query .= "`name` VARCHAR( 200 ) NOT NULL UNIQUE ,";
		$query .= "`type` VARCHAR( 200 ) NOT NULL ,";
		$query .= "`content` VARCHAR( 6000 ) NOT NULL)";
		$result = mysql_query($query);
		
		$query = "ALTER IGNORE TABLE `".$table_prefix."post_templates` ADD UNIQUE INDEX(name) "; 
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		
		$youtube_shortcode_template = "<center>%video_embed%</center>

<div style='margin-top:29px;text-align:left;width:100%;overflow:hidden;'>
  <br>
  <img src='%video_thumbnail%' align='right' border='0' style='padding-left:10px;max-width:150px;'>
  <div >%video_description%</div>
</div>";

		$query = "ALTER IGNORE TABLE `".$table_prefix."post_templates` ADD UNIQUE INDEX(name) "; 
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		
		
		$knolgoogle_template = "<h3>%title%</h3><br>
%postbody%
";
		$knolgoogle_template = addslashes($knolgoogle_template);
		$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
		$query2 .="VALUES ('', 'Shortcode Template: Knol.Google', '$knolgoogle_template' , 'token_knolgoogle_template')";
		$result2 = mysql_query($query2);
		
		
		
		$articlebase_template = "<h3>%title%</h3><br>
%postbody%
";
		$articlebase_template = addslashes($articlebase_template);
		$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
		$query2 .="VALUES ('', 'Shortcode Template: Articlebase', '$articlebase_template' , 'token_articlebase_template')";
		$result2 = mysql_query($query2);
		
		$query = "ALTER IGNORE TABLE `".$table_prefix."post_templates` ADD UNIQUE INDEX(name) "; 
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		
		$ezine_template = "<h3>%title%</h3><br>
%postbody%
";
		$knolgoogle_template = addslashes($knolgoogle_template);
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
		
		$youtube_shortcode_template = "<center>%video_embed%</center>

<div style='margin-top:29px;text-align:left;width:100%;overflow:hidden;'>
  <br>
  <img src='%video_thumbnail%' align='right' border='0' style='padding-left:10px;max-width:150px;'>
  <div >%video_description%</div>
</div>";
		
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
	
		//alter content block table due to mistake in old version
		$query = "ALTER TABLE `".$table_prefix."post_templates` CHANGE `content` `content` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error();exit;} 
		
		//if (!$result){echo $query; echo mysql_error();exit;} 
		$query = "ALTER TABLE `".$table_prefix."post_templates` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();exit;} 
		
		//add post_templates  table
		$query = "CREATE TABLE `".$table_prefix."custom_tokens` (";
		$query .= "`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
		$query .= "`name` VARCHAR( 225 ) NOT NULL ,";
		$query .= "`token` VARCHAR( 225 ) NOT NULL,";
		$query .= "`content` MEDIUMTEXT NOT NULL)";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";} 
		
		//alter content block table due to mistake in old version
		$query = "ALTER TABLE `".$table_prefix."custom_tokens` CHANGE `content` `content` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error();exit;} 
		
		//alter content block table due to mistake in old version
		$query = "ALTER TABLE `".$table_prefix."campaigns` MODIFY image_floating VARCHAR(10) NOT NULL";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();exit;} 
		
		//alter campaigns table
		$query = "ALTER TABLE `".$table_prefix."campaigns` CHANGE `header_profile` `header_template` int(2) NOT NULL";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();exit;} 
		
		//alter campaigns table
		$query = "ALTER TABLE `".$table_prefix."campaigns` CHANGE `footer_profile` `footer_template` int(2) NOT NULL";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();exit;} 
		
		//alter campaigns table
		$query = "ALTER TABLE `".$table_prefix."campaigns` CHANGE `z_remote_publishing_api` `z_remote_publishing_api_bs` TEXT NOT NULL";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();exit;} 
			
		//add link cloaking table
		$query = "CREATE TABLE `".$nuprefix."cloaking` (";
		$query .= "`id` INT( 3 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,";
		$query .= "`ref` VARCHAR( 50 ) NOT NULL ,";
		$query .= "`url` VARCHAR( 225 ) NOT NULL)";

		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();exit;} 
		
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
		//if (!$result){echo $query; echo mysql_error();exit;}
		
	
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
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		$query = "CREATE TABLE `".$table_prefix."blogsense_remote_published_urls` (
				`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`title` VARCHAR ( 225 ) NOT NULL , 
				`permalink` VARCHAR( 225 ) NOT NULL ,
				`date` DATETIME NOT NULL
				) ENGINE = MYISAM ;";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		
		//blocked urls
		$query = "CREATE TABLE `".$table_prefix."blocked_urls` (
				`id` INT( 10 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`campaign_id` INT( 10 ) NOT NULL ,
				`url` VARCHAR( 225 ) NOT NULL UNIQUE
				)ENGINE = MYISAM ";
		$result = mysql_query($query);
		
		
	//UPDATE VERSION NUMBER
		$query = "SELECT * FROM `".$table_prefix."blogsense` WHERE option_name='blogsense_build_version'";
		$result = mysql_query($query);
		$count = mysql_num_rows($result);
		if ($count==0)
		{	
			$query = "INSERT INTO `".$table_prefix."blogsense` (`id` ,`option_name` ,`option_value` )";
			$query .="VALUES ('', 'blogsense_build_version', '$build_version')";
			$result = mysql_query($query);
			if (!$result){echo $query; echo mysql_error();  exit;}
			
			$query = "SELECT * FROM `".$table_prefix."options` WHERE option_name='blogsense_build_version'";
			$result = mysql_query($query);
			$count = mysql_num_rows($result);
			if ($count==1)
			{
				$array = mysql_fetch_array($result);
				$old_version = $array['option_value'];
			}
		}
		else
		{
			$array = mysql_fetch_array($result);
			$old_version = $array['option_value'];
			//echo $old_version; exit;
			$query = "UPDATE `".$table_prefix."blogsense` SET option_value='$build_version' WHERE option_name='blogsense_build_version'";
			$result = mysql_query($query);
			if (!$result){echo $query; echo mysql_error();  exit;}
		}
		
		//**********************************************//
		//**********************************************//
		//**********************************************//
		//**********************************************//
		//**************** SOURCE PROFILES *******//
		
		//NEW SOURCE - news.yahoo.com
		$query = "SELECT * FROM `".$table_prefix."sourcedata` WHERE source_url='http://news.yahoo.com/'";
		$result = mysql_query($query);
		$count = mysql_num_rows($result);
		if ($count==0)
		{	
			$query = "INSERT INTO `".$table_prefix."sourcedata` (`id`, `source_url`, `footprint`, `title_start`, `title_end`, `content_start`, `content_end`, `comments_status`, `comments_name_start`, `comments_name_end`, `comments_content_start`, `comments_content_end`) VALUES";
			$query .= "('', 'http://news.yahoo.com/', 'site:news.yahoo.com -inurl:topics' , '<h1 id=\"yn-title\">', '</h1>', '<div class=\"yn-story-content\">', '</div>', 1, '</span></div><cite>', '</strong>', '<blockquote class=\"mwpphu-commenttext\">', '</blockquote>')";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error();  exit;}
		}
		
		//UPDATE OLDER SOURCE PROFILES
		//change buzzle source data
		$query = "UPDATE `".$table_prefix."sourcedata` SET title_start='<title>', title_end = '</title>', content_start='</h1>', content_end='<!-- google_ad_section_end -->', footprint=' [Published:]' WHERE source_url='http://www.buzzle.com/'";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		
		//change amazines source data
		$query = "UPDATE `".$table_prefix."sourcedata` SET title_start='<span name=KonaFilter style=\"width:100%;\"><center><font size=\"4\"><b>', title_end = '</b>', content_start='alt=\"Is this free article relevant to this category?\" onclick=\"flag();\">', content_end='<script type=\"text/javascript\">', footprint='article_detail.cfm' WHERE source_url='http://www.amazines.com/'";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		
		//change articlemonkeys source data
		$query = "UPDATE `".$table_prefix."sourcedata` SET title_start='<title>', title_end = '</title>', content_start='<script type=\"text/javascript\" src=\"http://tweetmeme.com/i/scripts/button.js\"></script></span>', content_end='<!-- google_ad_section_end -->', footprint='\"Word Count\"', regex_search='/[\\r\\n]+/', regex_replace='' WHERE source_url='http://www.articlemonkeys.com/'";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		
		//change articlealley source data
		$query = "UPDATE `".$table_prefix."sourcedata` SET title_start='<title>', title_end = '</title>', content_start='<div class=\"KonaBody\">', content_end='<!-- google_ad_section_end -->', footprint='[Date Published: ]' WHERE source_url='http://www.articlealley.com/'";
		$result = mysql_query($query);		
		//if (!$result){echo $query; echo mysql_error();  exit;}
		
		//change articlesbase source data
		$query = "UPDATE `".$table_prefix."sourcedata` SET title_start='<title>', title_end = '</title>', content_start='<div class=\"KonaBody\">', content_end='</div>', footprint='[posted:] -inurl:article-tags' , comments_status='1' , comments_name_start='<div class=\"text_field\">', comments_name_end='</strong>', comments_content_start='<div class=\"comment_text\">', comments_content_end='</div>' WHERE source_url='http://www.articlesbase.com/'";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		
		//change articlesbase source data
		$query = "UPDATE `".$table_prefix."sourcedata` SET title_start='<b class=titler>', title_end = '</b>', content_start='---- End Ad Box --->', content_end='<p><b>About The Author</b>', footprint='' , comments_status='0' , comments_name_start='', comments_name_end='', comments_content_start='', comments_content_end='', source_url='http://www.articlecity.com/'  WHERE source_url='http://www.articlecity.com/'";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		
		//change news.yahoo.com source data
		$query = "UPDATE `".$table_prefix."sourcedata` SET title_start='<h1 id=\"yn-title\">', title_end = '</h1>',";
		$query .= "title_start='<h1 id=\"yn-title\">',title_start_backup_1='<h1 class=\"printer-headline\">',title_start_backup_2='<H1>', title_start='<h1 id=\"yn-title\">', title_end = '</h1>',title_end_backup_1 = '</h1>',title_end_backup_2 = '</h1>',";
		$query .= "content_start='<div class=\"yn-story-content\">', content_start_backup_1='<div class=\"text printer-body\">' , content_start_backup_2='<!-- phugc -->' , content_end_backup_1='<!-- aunz freetext -->' ,  content_end_backup_2='<!-- /phugc -->' , ";
		$query .= "footprint='site:news.yahoo.com -inurl:topics' , comments_status='1' , comments_name_start='</span></div><cite>', comments_name_end='</strong>', comments_content_start='<blockquote class=\"mwpphu-commenttext\">', comments_content_end='</blockquote>' WHERE source_url='http://news.yahoo.com/'";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		
		//Change Youtube to Video
		$query = "UPDATE  `".$table_prefix."blogsense` SET option_name = REPLACE(option_name,'blogsense_youtube','blogsense_video')";
		$result = mysql_query($query);
		
		//**************************************************//
		//**************************************************//
		//**************************************************//
		//**************************************************//
		//**************************BLOGSENSE OPTIONS*******//
		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_blogsense_url','$blogsense_url')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		$query = "UPDATE ".$table_prefix."blogsense  SET option_value='$blogsense_url' WHERE option_name ='blogsense_blogsense_url' ";
		$result = mysql_query($query);
		
		//wordpress_url
		$wordpress_url = wordpress_url($table_prefix);
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_blog_url','$wordpress_url')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//blogsense_api_secret_key
		$secret_phrase ='blogsenseapisecretkey0123456789$';
		$key = str_shuffle($secret_phrase);
		$secret_key = substr($key, 0, 20);
		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_api_secret_key','$secret_key')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cronjob primer		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_primer','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cronjob running
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_running','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cronjob running
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_running_safety_count','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cronjob script url
		$cron_script_url = $blogsense_url."heartbeat.php?blog_id=".$_COOKIE['bs_blog_id'];
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_script_heartbeat','$cron_script_url')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		$query = "UPDATE ".$table_prefix."blogsense SET option_value='$cron_script_url' WHERE option_name='blogsense_cron_script_heartbeat'";
		$result = mysql_query($query);
		
		//cronjob script url
		$cron_script_url = $blogsense_url."cron_config.php?blog_id=".$_COOKIE['bs_blog_id'];
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_script_cronconfig','$cron_script_url')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}		
		
		//cron email
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_email','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}

		//cron minutes
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_minutes','20')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cron hours
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_hours','*/4')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cron days
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_days','*')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cron months
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_months','*')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cron weekdays
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_weekdays','*')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cron randomize
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_randomize','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cron randomize min
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_randomize_min','7')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cron randomize min
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_randomize_max','20')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cron item buffer
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_buffer_items','500000')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cron item buffer
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_buffer_campaigns','1000000')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//cron timeout
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cron_timeout','1000')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
	
		//Bitly Username
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_bookmarking_bitly_username','atwellpub')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Bitly APIKey
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_bookmarking_bitly_apikey','R_12b42b15a2adb9e2c088b12f4f9103d8')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Bookmarking ping module
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_bookmarking_ping_module','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Bookmarking minutes min
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_bookmarking_minutes_min','20')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Bookmarking minutes max
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_bookmarking_minutes_max','45')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Bookmarking percentage
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_bookmarking_percentage','100')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}

		//Tag to Cateogry
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_tag_to_category','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//twitter module
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_twitter','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		//twitter mode
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_twitter_mode','random')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		//Twitter Usernames
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_twitter_user','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Twitter oauth
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_twitter_oauth_token','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Twitter oauth
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_twitter_oauth_secret','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Twitter oauth
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_twitter_oauth_apikey','UQtFLhrsnw9dRfJHVhFFw')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Twitter oauth
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_twitter_oauth_consumer_secret','qKs6dMnE6OoOqraJfoSZ60wSogQACwmscT21bybRHE')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Twitter oauth
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_twitter_oauth_consumer_key','6NohqJxwqiDMm2xWfzdYGg')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Twitter Hash Additions
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_twitter_hash','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//PingFM Module
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_pixelpipe','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//PingFm mode
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_pixelpipe_mode','random')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		//PingFM Application Key
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_pixelpipe_email','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//PingFM Application Key
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_pixelpipe_routing','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Proxy List
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_proxy_list','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Proxy type
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_proxy_type','http')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Use proxies with bookmarking
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_proxy_bookmarking','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//User proxies with campaings
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_proxy_campaigns','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Proxy Bonanza Username
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_proxy_bonanza_username','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Proxy Bonanza Password
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_proxy_bonanza_password','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//Proxy Bonanza Password
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_proxy_bonanza_email','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}

		//add place to turn sources campaigns on or off
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_cloaking_redirect','random')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//add place to turn sources campaigns on or off
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_sources_module','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_default_author','1')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}

		
		//blog comments
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_blog_comments','1')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}


		//rss module
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_rss_module','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//rss module
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_keywords_module','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//post tags module
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_post_tags','1')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//post tags typo
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_post_tags_typo','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//store images locally module
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_store_images','1')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}

		//store images relative page
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_store_images_relative_path','$store_images_relative_path')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//store images full url
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_store_images_full_url','$store_images_full_url')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}

		//image floating
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_image_floating','left')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//image alt setup
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_image_alt_setup','1')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		

		//amazon module
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_amazon','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query;echo mysql_error(); echo "<br>";}

		//amazon affiliate-id
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_amazon_affiliate_id','jakolorbbookc-20')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//amazon aws access key
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_amazon_aws_access_key','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}

		//amazon secret access key
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_amazon_secret_access_key','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}


		//Custom Fields 
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_custom_fields_name','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		//Custom Fields 
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_custom_fields_value','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }	

		//Drop Posting Module
		$query = "INSERT INTO `".$table_prefix."blogsense` (`id` ,`option_name` ,`option_value` )";
		$query .="VALUES ('', 'blogsense_drop', '0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; }
		
		//Yahoo Answers Module
		$query = "INSERT INTO `".$table_prefix."blogsense` (`id` ,`option_name` ,`option_value` )";
		$query .="VALUES ('', 'blogsense_yahoo', '0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; }
		
		//Yahoo Answers Module
		$query = "INSERT INTO `".$table_prefix."blogsense` (`id` ,`option_name` ,`option_value` )";
		$query .="VALUES ('', 'blogsense_yahoo_api_key', 'YahooDemo')";
		$result = mysql_query($query);
		//if (!$result){echo $query; }
		
		//video module
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_video','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		//custom tagging
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_tags_nature','titles')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }

		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_tags_custom','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }

		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_tags_min','5')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_tags_max','7')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		//****************Inset SQL into Wordpress***********//
		//blogsense build
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_build_version','$buld_version')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}

		//blog installation
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_activation','$blogsense_activation')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}


		//blog activation
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_activation_key','$blogsense_activation_key')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}

		//blog activation email
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_activation_email','$blogsense_activation_email')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}	
		
		//spin phrase variable
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_spin_phrase_min','2')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		//spin phrase variable
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_spin_phrase_max','5')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_spin_exclude_cats','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_spin_exclude_these','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
			
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_tbs_spinning','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_tbs_maxsyns','5')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_tbs_quality','1')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }

		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_tbs_username','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_tbs_password','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>"; }
		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_draft_notification','0')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		$query = "INSERT INTO ".$table_prefix."blogsense (id, option_name, option_value)";
		$query.= "VALUES ('', 'blogsense_draft_notification_email','')";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
		
		//add collumnh to wp_posts
		$query = "ALTER TABLE `".$table_prefix."posts` CHANGE original_source original_source TEXT";
		$result = mysql_query($query);
		//if (!$result){echo $query; echo mysql_error();  exit;}
		//echo "success. new tables added. ready.";
		
		//********************************************************************//
		//********************************************************************//
		//********************************************************************//
		//***********************TOKEN DATABASE CHANGES***********************//
		
		//update categories and post & Title Templates
		if ($old_version>1&&$old_version<'9.8.3.7'||$_GET['test']==1||$_GET['all']==1)
		{	
			//alter collumn in wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` CHANGE query query TEXT";
			$result = mysql_query($query);
		}
		
		if ($old_version>1&&$old_version<'9.8.3.5'||$_GET['test']==1||$_GET['all']==1)
		{	
			//add collumn to wp_campaigns
			$query = "UPDATE `".$table_prefix."campaigns` set z_post_overwrite='1' WHERE module_type='RSS'";
			$result = mysql_query($query);
		}
		
		//update categories and post & Title Templates
		if ($old_version>1&&$old_version<'9.8.3.2'||$_GET['test']==1||$_GET['all']==1)
		{	
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_post_type VARCHAR (225)";
			$result = mysql_query($query);
		}
		
		if ($old_version>1&&$old_version<'9.8.1.7'||$_GET['all']==1)
		{	
			
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD autotag_method VARCHAR(225) ";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD autotag_custom_tags MEDIUMTEXT ";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD autotag_min int(2) ";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD autotag_max int(2) ";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			if ($_GET['all']!=1)
			{
				$query = "SELECT * FROM ".$table_prefix."blogsense WHERE option_name='blogsense_tags_nature' ";
				$result = mysql_query($query);
				if (!$result){echo $query; echo mysql_error(); exit;}
				$arr = mysql_fetch_array($result);
				$nature = $arr['option_value'];
				
				if ($nature=='yahoo_terms')
				{
					$query = "UPDATE ".$table_prefix."campaigns SET autotag_method = '1'";
					$result = mysql_query($query);
				}
				if ($nature=='titles')
				{
					$query = "UPDATE ".$table_prefix."campaigns SET autotag_method = '2'";
					$result = mysql_query($query);
				}
				if ($nature=='wptagsdb')
				{
					$query = "UPDATE ".$table_prefix."campaigns SET autotag_method = '3'";
					$result = mysql_query($query);
				}
				if ($nature=='custom')
				{
					$query = "UPDATE ".$table_prefix."campaigns SET autotag_method = '4'";
					$result = mysql_query($query);
				}
				
				$query = "UPDATE ".$table_prefix."campaigns SET autotag_min = '5' , autotag_max = '7'";
				$result = mysql_query($query);

				$query = "SELECT * FROM ".$table_prefix."blogsense WHERE option_name='blogsense_tags_custom' ";
				$result = mysql_query($query);
				if (!$result){echo $query; echo mysql_error(); exit;}
				$arr = mysql_fetch_array($result);
				$custom_tags = $arr['option_value'];
	
				$query = "UPDATE ".$table_prefix."campaigns SET autotag_custom_tags = '$custom_tags'";
				$result = mysql_query($query);
			}
		}
		
		if ($old_version>1&&$old_version<'9.4.5.1'||$_GET['test']==1||$_GET['all']==1)
		{
			//alter collumn in wp_campaigns
			$query = "UPDATE `".$table_prefix."post_templates`SET name= replace(name,'Token','Shortcode') ";
			$result = mysql_query($query);
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_xmlrpc TEXT ";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_xmlrpc_spin TEXT ";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_email_footer TEXT ";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD schedule_backdating INT(1)";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_email TEXT ";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_email_spin TEXT ";
			$result = mysql_query($query);
		}
		
		//Replace old tokens with new tokens
		if ($old_version>1&&$old_version<'9.2.0.1'||$_GET['spin_database']==1)
		{
			include('./includes/importer.php');
		}
	
		
		if ($old_version>1&&$old_version<'9.2.0.1'||$_GET['test']==1||$_GET['all']==1)
		{
		
			
			
		}
		
		if ($old_version>1&&$old_version<'9.1.3.5'||$_GET['all']==1)
		{
		
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_xmlrpc TEXT ";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_xmlrpc_spin TEXT ";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_email_footer TEXT ";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD schedule_backdating INT(1)";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_email TEXT ";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_email_spin TEXT ";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_pp_routing TEXT ";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_pp_email TEXT";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_exclude_keywords_scope INT(1)";
			$result = mysql_query($query);
			
			//add collumn to wp_autoblog
			$query = "ALTER TABLE `".$table_prefix."seoprofiles` ADD target VARCHAR( 20 )";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error();exit;}
			
			//add collumn to wp_autoblog
			$query = "ALTER TABLE `".$table_prefix."autoblog` ADD count INT(3)";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error();  exit;}
			//echo "success. new tables added. ready.";		

			//delete idea markers
			$query = "DELETE FROM `".$table_prefix."sourcedata` WHERE source_url='http://www.ideamarketers.com/' "; 
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); echo "<br>";}
			
			//add collumn to sourcedata
			$query = "ALTER TABLE `".$table_prefix."sourcedata` ADD `footprint` VARCHAR( 200 ) NOT NULL";
			$result = mysql_query($query);

			
			//remove any duplicate tables
			$query = "ALTER IGNORE TABLE `".$table_prefix."blogsense` ADD UNIQUE INDEX(option_name) "; 
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error();  exit;}
			
			//add collumn to wp_posts_to_bookmark
			$query = "ALTER TABLE `".$table_prefix."posts_to_bookmark` DROP index `post_id` ";
			$result = mysql_query($query);		
			
			//add collumn to wp_posts_to_bookmark
			$query = "ALTER TABLE `".$table_prefix."posts_to_bookmark` ADD `status` INT( 1 ) NOT NULL";
			$result = mysql_query($query);		
			
			//add collumn to wp_posts_to_bookmark
			$query = "ALTER TABLE `".$table_prefix."posts_to_bookmark` ADD `nature` varchar( 25 ) NOT NULL";
			$result = mysql_query($query);		
			
			//add collumn to wp_posts_to_bookmark
			$query = "ALTER TABLE `".$table_prefix."posts_to_bookmark` ADD `permalink` VARCHAR( 230 ) NOT NULL";
			$result = mysql_query($query);	

			//add collumn to wp_posts_to_bookmark
			$query = "ALTER TABLE `".$table_prefix."posts_to_bookmark` ADD `account` VARCHAR( 230 ) NOT NULL";
			$result = mysql_query($query);		
			
			//add collumn to wp_posts_to_bookmark
			$query = "ALTER TABLE `".$table_prefix."posts_to_bookmark` ADD `content` TEXT NOT NULL";
			$result = mysql_query($query);	
			
			//add collumnh to wp_posts
			$query = "ALTER TABLE `".$table_prefix."posts` ADD original_source TEXT";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error();  exit;}
			//echo "success. new tables added. ready.";
			
			//add collumn to wp_posts
			$query = "ALTER TABLE `".$table_prefix."posts` ADD bs_campaign_id INT( 4 )";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error();  exit;}
			//echo "success. new tables added. ready.";
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_post_template text";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_title_template text";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_bookmark_pixelpipe TEXT";
			$result = mysql_query($query);
			
			//alter collumn in wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."blogsense` CHANGE blogsense_pingfm_mode blogsense_pixelpipe_mode TEXT";
			$result = mysql_query($query);
			
			//alter collumn in wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."blogsense` CHANGE blogsense_pingfm blogsense_pixelpipe TEXT";
			$result = mysql_query($query);
			
			
			//alter collumn in wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` CHANGE z_bookmark_twitter z_bookmark_titter TEXT";
			$result = mysql_query($query);
			
			
			//alter collumn in wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` CHANGE z_bookmark_pingfm z_bookmark_pixelpipe TEXT";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error();  exit;}
			
			//alter collumn in wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` CHANGE z_rss_scraping z_rss_scrape_content INT(1)";
			$result = mysql_query($query);
			
			//alter collumn in wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` CHANGE z_rss_begin_code z_rss_scrape_content_begin_code text";
			$result = mysql_query($query);
			
			//alter collumn in wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` CHANGE z_rss_end_code z_rss_scrape_content_end_code text";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_rss_scrape_comments INT(1)";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_rss_scrape_names_begin_code text";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_rss_scrape_names_end_code text";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_rss_scrape_comments_begin_code text";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_rss_scrape_comments_end_code text";
			$result = mysql_query($query);
			
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_bookmark_twitter TEXT";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_bookmark_pixelpipe TEXT";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_post_overwrite INT(1)";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_exclude_keywords_scope INT(1)";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_include_keywords_scope INT(1)";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error(); exit;}

			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD scrape_profile INT(3)";
			$result = mysql_query($query);
			
			//add collumn to wp_autoblog
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD autocategorize INT(1)";
			$result = mysql_query($query);
			
			//add collumn to wp_autoblog
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD autocategorize_search INT(1)";
			$result = mysql_query($query);
			
			//add collumn to wp_autoblog
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD autocategorize_method INT(1)";
			$result = mysql_query($query);
			
			//add collumn to wp_autoblog
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD autocategorize_filter_keywords VARCHAR(225)";
			$result = mysql_query($query);
			
			//add collumn to wp_autoblog
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD autocategorize_filter_categories VARCHAR(225)";
			$result = mysql_query($query);
			
			//add collumn to wp_autoblog
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD autocategorize_filter_list MEDIUMTEXT";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD title_template INT(2)";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD limit_results INT(4)";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD custom_field_name TEXT";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD custom_field_value TEXT";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_post_status VARCHAR (225)";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_post_type VARCHAR (225)";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_comments_include INT (3)";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_comments_limit INT (3)";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_bs TEXT ";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_email TEXT ";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_email_spin TEXT ";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_pp_routing TEXT ";
			$result = mysql_query($query);
			
			//add collumn to wp_campaigns
			$query = "ALTER TABLE `".$table_prefix."campaigns` ADD z_remote_publishing_api_pp_email TEXT";
			$result = mysql_query($query);
			
			
			$query = "ALTER TABLE `".$table_prefix."sourcedata` ADD title_start_backup_1 VARCHAR(300)";
			$result = mysql_query($query);		
			//if (!$result){echo $query; echo mysql_error();  exit;}		
			$query = "ALTER TABLE `".$table_prefix."sourcedata` ADD title_start_backup_2 VARCHAR(300)";
			$result = mysql_query($query);		
			//if (!$result){echo $query; echo mysql_error();  exit;}
			$query = "ALTER TABLE `".$table_prefix."sourcedata` ADD title_end_backup_1 VARCHAR(300)";
			$result = mysql_query($query);		
			//if (!$result){echo $query; echo mysql_error();  exit;}
			$query = "ALTER TABLE `".$table_prefix."sourcedata` ADD title_end_backup_2 VARCHAR(300)";
			$result = mysql_query($query);		
			//if (!$result){echo $query; echo mysql_error();  exit;}
			
			$query = "ALTER TABLE `".$table_prefix."sourcedata` ADD content_start_backup_1 VARCHAR(300)";
			$result = mysql_query($query);		
			//if (!$result){echo $query; echo mysql_error();  exit;}		
			$query = "ALTER TABLE `".$table_prefix."sourcedata` ADD content_start_backup_2 VARCHAR(300)";
			$result = mysql_query($query);		
			//if (!$result){echo $query; echo mysql_error();  exit;}
			$query = "ALTER TABLE `".$table_prefix."sourcedata` ADD content_end_backup_1 VARCHAR(300)";
			$result = mysql_query($query);		
			//if (!$result){echo $query; echo mysql_error();  exit;}
			$query = "ALTER TABLE `".$table_prefix."sourcedata` ADD content_end_backup_2 VARCHAR(300)";
			$result = mysql_query($query);		
			//if (!$result){echo $query; echo mysql_error();  exit;}
			
			$query = "ALTER TABLE `".$table_prefix."sourcedata` ADD regex_search VARCHAR(300) AFTER comments_content_end";
			$result = mysql_query($query);		
			//if (!$result){echo $query; echo mysql_error();  exit;}

			$query = "ALTER TABLE `".$table_prefix."sourcedata` ADD regex_replace VARCHAR(300) AFTER regex_search";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error();  exit;}

			$token_rss_template = "<br><br><strong>%title%</strong><br><br>%description%<br><br>Original Source: %link%";
	
			$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
			$query2 .="VALUES ('', 'Shortcode Template: RSS', '$token_rss_template' , 'token_rss_template')";
			$result2 = mysql_query($query2);
			
			$youtube_object_template = "<object width=\"425\" height=\"344\"><param name=\"movie\" value=\"%embed_link%\">";
			$youtube_object_template .="</param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\">";
			$youtube_object_template .="</param><embed src=\"%embed_link%\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"425\" height=\"344\"></embed></object>";
					
			$youtube_object_template = addslashes($youtube_object_template);

			$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
			$query2 .="VALUES ('', 'Post Template: Youtube Object', '$youtube_object_template' , 'youtube_object_template')";
			$result2 = mysql_query($query2);
			
			$token_flickr_template = addslashes("<br><img src='%link%' class='fklr_images' id='id_flkr_image_%count%' style='max-width:%maxwidth%px;max-height:%maxheight%px;padding:5px;' ><br><br>");
		
			$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
			$query2 .="VALUES ('', 'Shortcode Template: Flickr', '$token_flickr_template' , 'token_flickr_template')";
			$result2 = mysql_query($query2);
			
			$token_google_images_template = addslashes("<br><img src='%imgsrc%' class='glg_images' id='id_glg_image_%count%' alt='%imgtitle%' style='max-width:%maxwidth%px;max-height:%maxheight%px;padding:5px;' ><br><br>");
		
			$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
			$query2 .="VALUES ('', 'Shortcode Template: Google Images', '$token_google_images_template' , 'token_google_images_template')";
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
			
			//add default post templates
			$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
			$query2 .="VALUES ('', 'Default Title Template', '%title%' , 'default_title_template')";
			$result2 = mysql_query($query2);
			
			//add default post templates
			$query2 = "UPDATE `".$table_prefix."post_templates` SET type='default_title_template' WHERE name='Default Title Template'";
			$result2 = mysql_query($query2);
			
			$query2 = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
			$query2 .="VALUES ('', 'Default RSS Post Template', '%postbody%' , 'default_rss_post_template')";
			$result2 = mysql_query($query2);
			
			
			$default_video_template = "<center>%video_embed%</center>

	<div style='margin-top:29px;text-align:left;width:100%'>
	  <br>
	  <img src='%video_thumbnail%' align='right' border='0' style='padding-left:10px;'>
	  <div >%video_description%</div>
	</div>";
			$query = "INSERT INTO `".$table_prefix."post_templates` (`id` ,`name` ,`content`, `type`)";
			$query .="VALUES ('', 'Default Video Post Template', '".addslashes($default_video_template)."' , 'default_video_post_template')";
			$result = mysql_query($query);
			//if (!$result){echo $query; echo mysql_error();exit;} 
			
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
			
		}
		
		
		
		//update categories and post & Title Templates
		if ($old_version>1&&$old_version<'8.9.4.0')
		{	
			$query = "UPDATE ".$table_prefix."blogsense SET option_value='15' WHERE option_value ='' and option_name='blogsense_cron_minutes'";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error();}
		}
		
		//update categories and post & Title Templates
		if ($old_version>1&&$old_version<'8.9.2.3')
		{	
			$query = "UPDATE ".$table_prefix."campaigns SET z_include_keywords_scope='3' ";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error();}
			
			$query = "UPDATE ".$table_prefix."campaigns SET z_exclude_keywords_scope='3' ";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error();}
		}
		
		if ($old_version>1&&$old_version<'8.6.3.3')
		{
			if (!function_exists('bsense_url'))
			{
				include('includes/prepare_variables.php');
				include('includes/helper_functions.php');
			}
			
			echo "Preparing one time bookmarking update:<br>";
			$query = "SELECT * FROM ".$table_prefix."campaigns ";
			$result = mysql_query($query);
			if (!$result){ echo $query; exit; }
			
			while ($array = mysql_fetch_array($result))
			{
			
				$campaign_id = $array['id'];
				$bookmark_twitter = $array['z_bookmark_twitter'];
				$bookmark_pingfm = $array['z_bookmark_pingfm'];
				$bookmark_hellotxt = $array['z_bookmark_hellotxt'];
				if ($campaign_id)
				{
					$query2 = "SELECT * FROM ".$table_prefix."posts  WHERE post_status='future' AND  bs_campaign_id='$campaign_id'";
					$result2 = mysql_query($query2);
					if (!$result2){ echo $query2; echo mysql_error(); exit; }
					
					$future_dates = array();
					$future_posts_to_bookmark = array();
					
					while ($arr = mysql_fetch_array($result2))
					{
						$post_id = $arr['ID'];
						$future_dates[] = $arr['post_date'];
						$future_posts_to_bookmark[] = $post_id;
					}
					
					//print_r($future_dates);exit;
					$return = schedule_bookmarks('future', $future_dates, $future_posts_to_bookmark, $bookmark_pingfm, $bookmark_twitter,$bookmark_hellotxt);
					
				}
			}

		}
		
		if ($old_version>1&&$old_version<'8.6.2.2')
		{
			$query = "UPDATE ".$table_prefix."campaigns SET z_post_template = replace(z_post_template,'++{','{') ";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error();}	
		}
		
		if ($old_version>1&&$old_version<'8.2.1.1')
		{
			$query = "UPDATE ".$table_prefix."campaigns SET autocategorize='1', autocategorize_method='1', autocategorize_search='1' WHERE tags_to_category='1' ";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error();}	
		}
		
		if ($old_version>1&&$old_version<'7.8.0.0')
		{
			$query = "DELETE FROM `".$table_prefix."post_templates` WHERE type = 'header_footer'";
			$result = mysql_query($query);
			
			$query = "UPDATE ".$table_prefix."campaigns SET z_comments_include='1',z_comments_limit='0' ";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error();}
			
		}
		
		//update categories and post & Title Templates
		if ($old_version>1&&$old_version<'7.6.7.3')
		{	
			$query = "UPDATE ".$table_prefix."campaigns SET z_post_status='publish' ";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error();}
		}
		
		//update categories and post & Title Templates
		if ($old_version>1&&$old_version<'7.4.3.1')
		{	
			$query = "UPDATE ".$table_prefix."campaigns SET source='query' WHERE module_type='video' ";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error();}
		}
		
		//update categories and post & Title Templates
		if ($old_version>1&&$old_version<'7.0.6.2')
		{	
			$query = "SELECT * FROM ".$table_prefix."blogsense WHERE option_name='blogsense_custom_fields_name'";
			$result = mysql_query($query);
			//print_r($result);exit;
			if (!$result){ echo $query; mysql_error();}
			else
			{			
				$array = mysq_fetch_array($result);
				$name = $array['option_value'];
				
				if ($name)
				{
					$query = "UPDATE ".$table_prefix."campaigns SET custom_field_name='$name',custom_field_value='%image_1%'";
					$result = mysql_query($query);
					if (!$result){ echo $query; echo mysql_error();}
				}
			}
		}
		
		//update categories and post & Title Templates
		if ($old_version>1&&$old_version<'7.2.0.1')
		{	
			
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
			if (!$result2){ echo $query2; echo mysql_error();}
			
			//include('includes/helper_functions.php');
			echo "<br>7.x One-time updating of amazon post templates, please wait.<br>";
			$query = "SELECT * FROM ".$table_prefix."campaigns ";
			$result = mysql_query($query);
			//print_r($result);exit;
			if (!$result){ echo $query; echo mysql_error();}
			
			while ($array = mysql_fetch_array($result))
			{
				$campaign_id = $array['id'];
				$module_type = $array['module_type'];

				//create post template for campaigns
				if ($module_type=='amazon')
				{
					$amazon_template = "<h3>%title% - %amazon_price%</h3>

<a href=\"%link%\" target=_blank><img src='%amazon_medium_image_url%' style='float:right;padding:20px' alt='%tag_title%' border=0></a>

%amazon_customer_review_content_1%


<h3>More Details</h3>

%amazon_product_features%

%amazon_product_description%


<div style='text-align:right'><a href='%link%' target=_blank><img src='%amazon_buyitnow_button%' border=0></a></div>
";
					$amazon_template = addslashes($amazon_template);
					
					$query2 = "UPDATE ".$table_prefix."campaigns SET z_post_template = '$amazon_template' WHERE id='$campaign_id'";
					$result2 = mysql_query($query2);
				}				
			}
			echo "Corrected.<br><br>";
		}
		
		
		//update categories and post & Title Templates
		if ($old_version>1&&$old_version<'7.7.1.2')
		{	
			$query = "DELETE FROM ".$table_prefix."post_templates WHERE type='header_footer' OR type='title'";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error();}
		}
		
		
		//update categories and post & Title Templates
		if ($old_version>1&&$old_version<'7.0.0.1')
		{	
			$query = "UPDATE ".$table_prefix."blogsense SET option_value='' WHERE option_name='twitter_user'";
			$result = mysql_query($query);
			
			$query = "UPDATE ".$table_prefix."blogsense SET option_value='' WHERE option_name='twitter_pass'";
			$result = mysql_query($query);
			
			//include('includes/helper_functions.php');
			echo "<br>6.x-7.x One-time updating of campaign category ids, please wait.<br>";
			$query = "SELECT * FROM ".$table_prefix."campaigns ";
			$result = mysql_query($query);
			//print_r($result);exit;
			if (!$result){ echo $query; mysql_error();}
			
			while ($array = mysql_fetch_array($result))
			{
				$category = $array['category'];
				$campaign_id = $array['id'];
				$module_type = $array['module_type'];
				
				//update categories to new id system
				$query2 = "SELECT term_id FROM ".$table_prefix."term_taxonomy WHERE term_taxonomy_id='$category' ";
				$result2 = mysql_query($query2);
				$array = mysql_fetch_array($result2);
				$term_id = $array['term_id'];
				
				$query2 = "UPDATE ".$table_prefix."campaigns SET category = '$term_id'  WHERE id='$campaign_id'";
				$result2 = mysql_query($query2);
				
				//create post template for campaigns
				if ($module_type!='video')
				{
					
					$query2 = "UPDATE ".$table_prefix."campaigns SET z_title_template = '%title%' WHERE id='$campaign_id'";
					$result2 = mysql_query($query2);
					
					$query2 = "UPDATE ".$table_prefix."campaigns SET z_post_template = '%postbody%' WHERE id='$campaign_id'";
					$result2 = mysql_query($query2);
				}
				else
				{
					$query2 = "UPDATE ".$table_prefix."campaigns SET z_title_template = '%title%' WHERE id='$campaign_id'";
					$result2 = mysql_query($query2);
					
					$vid_template = "<center>%video_embed%</center>

<div style='margin-top:29px;text-align:left;width:100%;overflow:hidden;'>
  <br>
  <img src='%video_thumbnail%' align='right' border='0' style='padding-left:10px;'>
  <div >%video_description%</div>
</div>";
					$vid_template = addslashes($vid_template);
					
					$query2 = "UPDATE ".$table_prefix."campaigns SET z_post_template = '$vid_template' WHERE id='$campaign_id'";
					$result2 = mysql_query($query2);
				}
				
			}
			echo "Corrected.";
		}
		
		
		if (!headers_sent())
		{
			echo "<html><head><title>Database Repair - BlogSenseWP</title></head><body></body></html>";
		}
		
		if ($multisite=='on')
		{
			echo "Tables for Blog Updated.<br>";
		}
		else
		{
			echo "All tables updated. Please close this page and refresh blogsense.";
		}
		
		
		
?>
