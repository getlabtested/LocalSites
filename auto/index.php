<?php

include_once('../wp-config.php');
session_start();
include_once('includes/build_version.php');
include("functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("login.php");

//get current bs directory
$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
$current_url = explode("index.php",$current_url); 
$blogsense_url = $current_url[0];

//echo $_COOKIE['bs_blog_id'];
//check for multisite
if (!$main_prefix)
{
	$nuprefix = explode('_',$table_prefix);
	$nuprefix= $nuprefix[0]."_";
	$query = "SELECT * FROM ".$nuprefix."blogs";
	$result = mysql_query($query);
}
else
{
	$query = "SELECT * FROM ".$main_prefix."blogs";
	$result = mysql_query($query);
}
if ($result&&!$_GET['blog_id'])
{
	//get blog ids & names
	$i = 0;
	while ($arr = mysql_fetch_array($result))
	{		
		//get blog id
		$multisite_blog_id[$i] = $arr['blog_id'];		
		if (function_exists('switch_to_blog')) 
		{
			switch_to_blog($arr['blog_id']);
		}
		$query2 = "SELECT * FROM ".$table_prefix."options WHERE option_name='blogname'";
		$result2 = mysql_query($query2);
		if (!$result2){echo $query2; echo mysql_error(); exit;}
		$arr2 = mysql_fetch_array($result2);
		$multisite_blog_name[$i] = $arr2['option_value'];
		
		$i++;
	}
	$multisite='on';
	
	//echo 1;exit;
	
	if (!$_COOKIE['bs_blog_id'])
	{
		$_COOKIE['bs_blog_id'] = "1";  
		$_COOKIE['bs_site_url'] = "../";
		$_COOKIE['bs_cronjob_script'] = "heartbeat.php?blog_id=".$_COOKIE['bs_blog_id'];
		if (function_exists('switch_to_blog')) 
		{
			switch_to_blog(1);
		}
	}
	else 
	{ 
		if (function_exists('switch_to_blog')) 
		{
			switch_to_blog(1);
			switch_to_blog($_COOKIE['bs_blog_id']);
		}
	}
	
	$siteurl = "../";
	$heartbeat = "heartbeat.php?blog_id={$$_COOKIE['bs_blog_id']}";
}
else if ($result&&$_GET['blog_id'])
{
	//get blog ids & names
	$i = 0;
	while ($arr = mysql_fetch_array($result))
	{		
		//get blog id
		$multisite_blog_id[$i] = $arr['blog_id'];		
		if (function_exists('switch_to_blog')) 
		{
			switch_to_blog($arr['blog_id']);
		}
		$query2 = "SELECT * FROM ".$table_prefix."options WHERE option_name='blogname'";
		$result2 = mysql_query($query2);
		if (!$result2){echo $query2; echo mysql_error(); exit;}
		$arr2 = mysql_fetch_array($result2);
		$multisite_blog_name[$i] = $arr2['option_value'];
		
		$i++;
	}
	$multisite='on';
	switch_to_blog(1);
	switch_to_blog($_GET['blog_id']);
	setcookie('bs_blog_id',$_GET['blog_id'],0,"/");
	
	//get site url
	$query = "SELECT * FROM ".$table_prefix."options WHERE option_name='siteurl'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); exit;}
	$arr = mysql_fetch_array($result);
	
	$siteurl = $arr['option_value'];
	$heartbeat = "heartbeat.php?blog_id={$_GET['blog_id']}";
	setcookie('bs_site_url',$arr['option_value'],0,"/");
	setcookie('bs_cronjob_script',"heartbeat.php?blog_id={$_GET['blog_id']}",0,"/");
	//set new cronjob script
}
else
{
	setcookie('bs_blog_id',1,0,"/");
	setcookie('bs_site_url',"../",0,"/");
	setcookie('bs_cronjob_script',"heartbeat.php",0,"/");
	$siteurl = "../";
	$heartbeat = "heartbeat.php";
}

//echo $table_prefix;
//echo $_COOKIE['bs_blog_id'];

//update if new files
$query = "SELECT `option_name`, `option_value` FROM ".$table_prefix."blogsense WHERE `option_name` IN (";
$query .= "'blogsense_activation' ,";
$query .= "'blogsense_build_version' )";
$result = mysql_query($query);
if ($result)
{
	$count = mysql_num_rows($result);

	for ($i=0;$i<$count;$i++)
	{
		$array = mysql_fetch_array($result);
		if ($i==0){$act = $array['option_value'];}
		if ($i==1){$bv = $array['option_value'];}
	}

	if (($count==0||$build_version>$bv)&&$act)
	{
	   $bid = $_COOKIE['bs_blog_id'];
	   echo "<div style='display:none'>";
	   include_once('update_sql.php');
	   echo "</div>";
	}
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>BlogSense - Professional</title>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php

// detect browser

$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
if(strstr($useragent, 'msie'))
{
	echo "<style type='text/css'>@import url('styleie.css');</style>";
}
else
{
	echo "<style type='text/css'>@import url('style.css');</style>";
}
?>
<script type="text/javascript" src="./includes/jquery.js"></script>
<link href="./includes/colorbox/colorbox.css" media="screen" rel="stylesheet" type="text/css"/>
<link href="./includes/jquery.custom.frontend.css" media="screen" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>

<script src="./includes/colorbox/colorbox.js" type="text/javascript"></script>  
<?php
  include_once("functions/f_check_for_updates.php");
  echo "<style type=\"text/css\">";
  $p = $_GET['p'];
  if ($p==2)
  {
    echo "#blogsense_setup{display:none;}
	      #wordpress_settings{display:block;}
	       #autoblog_settings{display:none;}
	       #bookmarking_settings{display:none;}
			";
  }
  else if ($p==3)
  {
	echo "#blogsense_setup{display:none;}
	      #wordpress_settings{display:none;}
	      #autoblog_settings{display:block;}
		  #bookmarking_settings{display:none;}
			";
  }
  else if ($p==4)
  {
	echo "#blogsense_setup{display:none;}
	      #wordpress_settings{display:none;}
	      #autoblog_settings{display:none;}
		  #bookmarking_settings{display:block;}
			";
  }
  else
  {
	echo "#blogsense_setup{display:block;}
	     #wordpress_settings{display:none;}
	     #autoblog_settings{display:none;}
		 #bookmarking_settings{display:none;}
		";
  }
  
  if ($_GET['m']=="general"&&$p==2)
  {
  	echo "#wordpress_general{display:block;}
	      #wordpress_rss_tools{display:none;}
		  #wordpress_categories{display:none;}
		  #wordpress_post_tools{display:none;}
		  #wordpress_backup_tools{display:none;}		  
		  #wordpress_api_tools{display:none;}		  
	     ";
  }
  else if ($_GET['m']=="rss_tools"&&$p==2)
  {
  	echo "#wordpress_general{display:none;}
	      #wordpress_rss_tools{display:block;}
		  #wordpress_categories{display:none;}
		  #wordpress_post_tools{display:none;}
		  #wordpress_backup_tools{display:none;}
		  #wordpress_api_tools{display:none;}
	     ";
  }
  else if ($_GET['m']=="backup_tools"&&$p==2)
  {
  	echo "#wordpress_general{display:none;}
	      #wordpress_rss_tools{display:none;}
	      #wordpress_categories{display:none;}
		  #wordpress_post_tools{display:none;}
		  #wordpress_backup_tools{display:block;}
		  #wordpress_api_tools{display:none;}
	     ";
  }
  else if ($_GET['m']=="api_tools"&&$p==2)
  {
  	echo "#wordpress_general{display:none;}
	      #wordpress_rss_tools{display:none;}
	      #wordpress_categories{display:none;}
		  #wordpress_post_tools{display:none;}
		  #wordpress_backup_tools{display:none;}
		  #wordpress_api_tools{display:block;}
	     ";
  }
  else
  {
  	echo "#wordpress_general{display:block;}
	      #wordpress_rss_tools{display:none;}
	      #wordpress_categories{display:none;}
		  #wordpress_post_tools{display:none;}
		  #wordpress_backup_tools{display:none;}
		  #wordpress_api_tools{display:none;}
	     ";
  }
  
  if ($_GET['m']=="rss"&&$p==3)
  {
  	echo "#global_settings{display:none;}
	     #rss_nodes{display:block;}
	     #drop_nodes{display:none;}
	     #sources_nodes{display:none;}
	     #yahoo_nodes{display:none;}
	     #amazon_nodes{display:none;}
	     #keywords_nodes{display:none;}
	     #video_nodes{display:none;}
		 #templates{display:none;}
		 #api{display:none;}
		 ";
  }
  else if ($_GET['m']=="fileimport"&&$p==3)
  {
	echo "#global_settings{display:none;}
	     #rss_nodes{display:none;}
	     #drop_nodes{display:block;}
	     #sources_nodes{display:none;}
	     #yahoo_nodes{display:none;}
	     #amazon_nodes{display:none;}
		 #keywords_nodes{display:none;}
	     #video_nodes{display:none;}
		 #templates{display:none;}
		 #api{display:none;}
		 ";
  }
  else if ($_GET['m']=="sources"&&$p==3)
  {
	echo "#global_settings{display:none;}
	     #rss_nodes{display:none;}
	     #drop_nodes{display:none;}
	     #sources_nodes{display:block;}
	     #yahoo_nodes{display:none;}
	     #amazon_nodes{display:none;}
		 #keywords_nodes{display:none;}
	     #video_nodes{display:none;}
		 #templates{display:none;}
		 #api{display:none;}
		 ";
  }
  else if ($_GET['m']=="yahoo"&&$p==3)
  {
  	echo "#global_settings{display:none;}
	     #rss_nodes{display:none;}
	     #drop_nodes{display:none;}
	     #sources_nodes{display:none;}
	     #yahoo_nodes{display:block;}
	     #amazon_nodes{display:none;}
		 #keywords_nodes{display:none;}
	     #video_nodes{display:none;}
		 #templates{display:none;}
		 #api{display:none;}
		 ";
  }
  else if ($_GET['m']=="amazon"&&$p==3)
  {
  	echo "#global_settings{display:none;}
	     #rss_nodes{display:none;}
	     #drop_nodes{display:none;}
	     #sources_nodes{display:none;}
	     #yahoo_nodes{display:none;}
	     #amazon_nodes{display:block;}
		 #keywords_nodes{display:none;}
	     #video_nodes{display:none;}
		 #templates{display:none;}
		 #api{display:none;}
		 ";
  }
  else if ($_GET['m']=="keywords"&&$p==3)
  {
  	echo "#global_settings{display:none;}
	     #rss_nodes{display:none;}
	     #drop_nodes{display:none;}
	     #sources_nodes{display:none;}
	     #yahoo_nodes{display:none;}
	     #amazon_nodes{display:none;}
		 #keywords_nodes{display:block;}
	     #video_nodes{display:none;}
		 #templates{display:none;}
		 #api{display:none;}
		 ";
  }
  else if ($_GET['m']=="video"&&$p==3)
  {
  	echo "#global_settings{display:none;}
	     #rss_nodes{display:none;}
	     #drop_nodes{display:none;}
	     #sources_nodes{display:none;}
	     #yahoo_nodes{display:none;}
	     #amazon_nodes{display:none;}
		 #keywords_nodes{display:none;}
	     #video_nodes{display:block;}
		 #templates{display:none;}
		 #api{display:none;}
		 ";
  }
  else if ($_GET['m']=="post_templates"&&$p==3)
  {
  	 echo "#global_settings{display:none;}
	     #rss_nodes{display:none;}
	     #drop_nodes{display:none;}
	     #sources_nodes{display:none;}
	     #yahoo_nodes{display:none;}
	     #amazon_nodes{display:none;}
		 #keywords_nodes{display:none;}
	     #video_nodes{display:none;}
		 #templates{display:block;}
		 #api{display:none;}
		 ";
  }
  else if ($_GET['m']=="api"&&$p==3)
  {
  	 echo "#global_settings{display:none;}
	     #rss_nodes{display:none;}
	     #drop_nodes{display:none;}
	     #sources_nodes{display:none;}
	     #yahoo_nodes{display:none;}
	     #amazon_nodes{display:none;}
		 #keywords_nodes{display:none;}
	     #video_nodes{display:none;}
		 #templates{display:none;}
		 #api{display:block;}
		 ";
  }
  else
  {
	echo "#global_settings{display:block;}
	     #rss_nodes{display:none;}
	     #drop_nodes{display:none;}
	     #sources_nodes{display:none;}
	     #yahoo_nodes{display:none;}
	     #amazon_nodes{display:none;}
		 #keywords_nodes{display:none;}
	     #video_nodes{display:none;}
	     #templates{display:none;}
		 #api{display:none;}
		 ";
  }
  
  if ($_GET['m']=="bookmarking_logs"&&$p==4)
  {
  	echo "#id_bookmarking_settings{display:none;}
	      #id_bookmarking_logs{display:block;} 
	     ";
  }
  else 
  {
  	echo "#id_bookmarking_settings{display:block;}
	      #id_bookmarking_logs{display:none;} 
	     ";
  }
  echo "</style>";

?>        
<script type="text/javascript"> 
                                    
$(document).ready(function() 
{
	$("#id_accordion_cronjobs").accordion({
		autoHeight: false,
		collapsible: true,
		active: -1
	});
	
	 $(".add_twitter").colorbox({width:"50%", height:"50%", iframe:true, onClosed:function(){window.location.replace("index.php?p=4");}});
	 $(".add_rss").colorbox({width:"95%", height:"95%", iframe:true, onClosed:function(){window.location.replace("index.php?p=3&m=rss");}});
	 $(".add_amazon").colorbox({width:"95%", height:"95%", iframe:true, onClosed:function(){window.location.replace("index.php?p=3&m=amazon");}});
	 $(".add_keywords").colorbox({width:"95%", height:"95%", iframe:true, onClosed:function(){window.location.replace("index.php?p=3&m=keywords");}});
	 $(".add_video").colorbox({width:"95%", height:"95%", iframe:true, onClosed:function(){window.location.replace("index.php?p=3&m=video");}});
	 $(".pdf_import").colorbox({width:"50%", height:"70%", iframe:true});
	 $(".add_sources").colorbox({width:"95%", height:"95%", iframe:true,iframe:true, onClosed:function(){window.location.replace("index.php?p=3&m=sources"); }});
	 $(".add_yahoo").colorbox({width:"95%", height:"95%", iframe:true,onClosed:function(){window.location.replace("index.php?p=3&m=yahoo");}});		 
	 $(".sync_keywords").colorbox({width:"95%", height:"95%", iframe:true});		 		 
	 $(".add_video").colorbox({width:"95%", height:"95%", iframe:true,onClosed:function(){window.location.replace("index.php?p=3&m=video");}});	
	 $(".add_block").colorbox({width:"95%", height:"95%", iframe:true, onClosed:function(){window.location.replace("index.php?p=3&m=post_templates"); }});
	 $(".add_drop").colorbox({width:"95%", height:"95%", iframe:true, onClosed:function(){window.location.replace("index.php?p=3&m=fileimport"); }});
	 $(".view_bookmarking_report").colorbox({width:"95%", height:"95%", iframe:true});
	 $(".edit_campaign").colorbox({width:"95%", height:"95%", iframe:true}); 
	 $(".import_campaign").colorbox({width:"95%", height:"95%", iframe:true}); 
	 $(".manage_sources").colorbox({width:"95%", height:"95%", iframe:true});
	 $(".database_tools").colorbox({width:"95%", height:"95%", iframe:true});
	 $(".class_bookmarking_rebuild").colorbox({width:"95%", height:"95%", iframe:true, onClosed:function(){window.location.replace("index.php?p=4&m=bookmarking_logs"); }});
	 
	 $(".copy_campaign").click(function(){
		if (confirm('Are you sure you want to copy this campaign?')) {
			var string = this.id.replace('id_copy_campaign_','');
			var array = string.split('_');
			var mode = array[0];
			var cid = array[1];
			
			$.get('functions/f_copy_campaign.php?module_type='+mode+'&copy=1&id='+cid , function(data) {
				window.location.replace("index.php?p=3&m="+mode);
			});
        }
	 });
	 
	 $("#i_button").mouseenter(function(){
	    $("#install_button").css("background-image", "url(nav/btn_button_hover.png)");
	 });	 
	 
	 $("#i_button").mouseleave(function(){
	    $("#install_button").css("background-image", "url(nav/btn_button.png)");
	 });
	 
	 $("#l_button").mouseenter(function(){
	   $("#load_button").css("background-image", "url(nav/btn_button_hover.png)");
     });
	 $("#l_button").mouseleave(function(){
	    $("#load_button").css("background-image", "url(nav/btn_button.png)");
	 });
	 
	 $("#s_button").mouseenter(function(){
	   $("#save_button").css("background-image", "url(nav/btn_button_hover.png)");
	 });
     $("#s_button").mouseleave(function(){
	    $("#save_button").css("background-image", "url(nav/btn_button.png)");
	 });	 
	 
	 $("#r_button").mouseenter(function(){
	    $("#run_button").css("background-image", "url(nav/btn_button_hover.png)");
	 });
     $("#r_button").mouseleave(function(){
	    $("#run_button").css("background-image", "url(nav/btn_button.png)");
	 });
	 
	 $("#s_rss_button").mouseenter(function(){
	    $("#save_rss_button").css("background-image", "url(nav/btn_button_hover.png)");
	 });
     $("#s_rss_button").mouseleave(function(){
	    $("#save_rss_button").css("background-image", "url(nav/btn_button.png)");
	 });	
	 
	 $("#l_button").click(function(){
	    $("#form_load").submit();
	});
	
	 $("#i_button").click(function(){
	    $("#form_install").submit();
	});
	
	$("#s_button").click(function(){
	    $("#form_save").submit();
	});
	
	$("#s_button_bookmarking").click(function(){
	    $("#form_bookmarking_save").submit();
	});
	
	
	$("#s_rss_button").click(function(){
	    $("#form_save_rss").submit();
	});
	
	$("#a_wordpress_general").click(function(){
	     $("#wordpress_general").css("display","block");
	     $("#wordpress_categories").css("display","none");
	     $("#wordpress_rss_tools").css("display","none");
		 $("#wordpress_theme_plugin").css("display","none");
		 $("#wordpress_post_tools").css("display","none");
		 $("#wordpress_backup_tools").css("display","none");
		 $("#wordpress_api_tools").css("display","none");
	     $("#wp_open_module").val("general");
	});
	
	$("#a_wordpress_rss_tools").click(function(){
	     $("#wordpress_general").css("display","none");
	     $("#wordpress_rss_tools").css("display","block");
	     $("#wordpress_categories").css("display","none");
		 $("#wordpress_theme_plugin").css("display","none");
		 $("#wordpress_post_tools").css("display","none");
		 $("#wordpress_backup_tools").css("display","none");
		 $("#wordpress_api_tools").css("display","none");
	     $("#wp_open_module").val("rss_tools");
	});
	

	$("#a_wordpress_theme_plugin").click(function(){
	     $("#wordpress_general").css("display","none");
	     $("#wordpress_categories").css("display","none");
		 $("#wordpress_rss_tools").css("display","none");
		 $("#wordpress_theme_plugin").css("display","block");
		 $("#wordpress_post_tools").css("display","none");
		 $("#wordpress_backup_tools").css("display","none");
		 $("#wordpress_api_tools").css("display","none");
	     $("#wp_open_module").val("theme_plugin");
	});

	$("#a_wordpress_post_tools").click(function(){
	     $("#wordpress_general").css("display","none");
	     $("#wordpress_categories").css("display","none");
		 $("#wordpress_rss_tools").css("display","none");
		 $("#wordpress_theme_plugin").css("display","none");
		 $("#wordpress_post_tools").css("display","block");
		 $("#wordpress_backup_tools").css("display","none");
		 $("#wordpress_api_tools").css("display","none");
	     $("#wp_open_module").val("theme_plugin");
	});	

	$("#a_wordpress_backup_tools").click(function(){
	     $("#wordpress_general").css("display","none");
	     $("#wordpress_categories").css("display","none");
		 $("#wordpress_rss_tools").css("display","none");
		 $("#wordpress_theme_plugin").css("display","none");
		 $("#wordpress_post_tools").css("display","none");
		 $("#wordpress_backup_tools").css("display","block");
		 $("#wordpress_api_tools").css("display","none");
	     $("#wp_open_module").val("backup_tools");
	});
	
	$("#a_wordpress_api_tools").click(function(){
	     $("#wordpress_general").css("display","none");
	     $("#wordpress_categories").css("display","none");
		 $("#wordpress_rss_tools").css("display","none");
		 $("#wordpress_theme_plugin").css("display","none");
		 $("#wordpress_post_tools").css("display","none");
		 $("#wordpress_backup_tools").css("display","none");
		 $("#wordpress_api_tools").css("display","block");
	     $("#wp_open_module").val("api_tools");
	});

	$("#a_global_settings").click(function(){
	     $("#global_settings").css("display","block");
	     $("#rss_nodes").css("display","none");
	     $("#drop_nodes").css("display","none");
	     $("#sources_nodes").css("display","none");
	     $("#yahoo_nodes").css("display","none");
	     $("#amazon_nodes").css("display","none");
	     $("#keywords_nodes").css("display","none");
	     $("#video_nodes").css("display","none");
		 $("#templates").css("display","none");
		 $("#api").css("display","none");
		 $("#open_module").val("global_settings");
	});
	$("#a_rss_nodes").click(function(){
	     $("#global_settings").css("display","none");
	     $("#rss_nodes").css("display","block");
	     $("#drop_nodes").css("display","none");
	     $("#sources_nodes").css("display","none");
	     $("#yahoo_nodes").css("display","none");
	     $("#amazon_nodes").css("display","none");
		 $("#keywords_nodes").css("display","none");
	     $("#video_nodes").css("display","none");
		 $("#templates").css("display","none");
		 $("#api").css("display","none");
		 $("#open_module").val("rss");
	});
	$("#a_drop_nodes").click(function(){
	     $("#global_settings").css("display","none");
	     $("#rss_nodes").css("display","none");
	     $("#drop_nodes").css("display","block");
	     $("#sources_nodes").css("display","none");
	     $("#yahoo_nodes").css("display","none");
	     $("#amazon_nodes").css("display","none");
		 $("#keywords_nodes").css("display","none");
	     $("#video_nodes").css("display","none");
		 $("#templates").css("display","none");
		 $("#api").css("display","none");
		 $("#open_module").val("fileimport");
	});
	$("#a_sources_nodes").click(function(){
	     $("#global_settings").css("display","none");
	     $("#rss_nodes").css("display","none");
	     $("#drop_nodes").css("display","none");
	     $("#sources_nodes").css("display","block");
	     $("#yahoo_nodes").css("display","none");
	     $("#amazon_nodes").css("display","none");
		 $("#keywords_nodes").css("display","none");
	     $("#video_nodes").css("display","none");
		 $("#templates").css("display","none");
		 $("#api").css("display","none");
		 $("#open_module").val("sources");
	});
	$("#a_yahoo_nodes").click(function(){
	     $("#global_settings").css("display","none");
	     $("#rss_nodes").css("display","none");
	     $("#drop_nodes").css("display","none");
	     $("#sources_nodes").css("display","none");
	     $("#yahoo_nodes").css("display","block");
	     $("#amazon_nodes").css("display","none");
		 $("#keywords_nodes").css("display","none");
	     $("#video_nodes").css("display","none");
		 $("#templates").css("display","none");
		 $("#api").css("display","none");
		 $("#open_module").val("yahoo");
	});
	$("#a_amazon_nodes").click(function(){
	     $("#global_settings").css("display","none");
	     $("#rss_nodes").css("display","none");
	     $("#drop_nodes").css("display","none");
	     $("#sources_nodes").css("display","none");
	     $("#yahoo_nodes").css("display","none");
	     $("#amazon_nodes").css("display","block");
		 $("#keywords_nodes").css("display","none");
	     $("#video_nodes").css("display","none");
		 $("#templates").css("display","none");
		 $("#api").css("display","none");
		 $("#open_module").val("amazon");
	});
	$("#a_keywords_nodes").click(function(){
	     $("#global_settings").css("display","none");
	     $("#rss_nodes").css("display","none");
	     $("#drop_nodes").css("display","none");
	     $("#sources_nodes").css("display","none");
	     $("#yahoo_nodes").css("display","none");
	     $("#amazon_nodes").css("display","none");
		 $("#keywords_nodes").css("display","block");
	     $("#video_nodes").css("display","none");
		 $("#templates").css("display","none");
		 $("#api").css("display","none");
		 $("#open_module").val("keywords");
	});
	$("#a_video_nodes").click(function(){
	     $("#global_settings").css("display","none");
	     $("#rss_nodes").css("display","none");
	     $("#drop_nodes").css("display","none");
	     $("#sources_nodes").css("display","none");
	     $("#yahoo_nodes").css("display","none");
	     $("#amazon_nodes").css("display","none");
		 $("#keywords_nodes").css("display","none");
	     $("#video_nodes").css("display","block");
		 $("#templates").css("display","none");
		 $("#api").css("display","none");
		 $("#open_module").val("video");
	});
	
	$("#a_templates").click(function(){
	     $("#global_settings").css("display","none");
	     $("#rss_nodes").css("display","none");
	     $("#drop_nodes").css("display","none");
	     $("#sources_nodes").css("display","none");
	     $("#yahoo_nodes").css("display","none");
	     $("#amazon_nodes").css("display","none");
		 $("#keywords_nodes").css("display","none");
	     $("#video_nodes").css("display","none");
		 $("#templates").css("display","block");
		 $("#api").css("display","none");
		 $("#open_module").val("post_templates");
	});
	
	$("#a_seo").click(function(){
	     $("#global_settings").css("display","none");
	     $("#rss_nodes").css("display","none");
	     $("#drop_nodes").css("display","none");
	     $("#sources_nodes").css("display","none");
	     $("#yahoo_nodes").css("display","none");
	     $("#amazon_nodes").css("display","none");
		 $("#keywords_nodes").css("display","none");
	     $("#video_nodes").css("display","none");
		 $("#templates").css("display","none");
		 $("#api").css("display","none");
		 $("#open_module").val("seo");
	});
	$("#a_api").click(function(){
	     $("#global_settings").css("display","none");
	     $("#rss_nodes").css("display","none");
	     $("#drop_nodes").css("display","none");
	     $("#sources_nodes").css("display","none");
	     $("#yahoo_nodes").css("display","none");
	     $("#amazon_nodes").css("display","none");
		 $("#keywords_nodes").css("display","none");
	     $("#video_nodes").css("display","none");
		 $("#templates").css("display","none");
		 $("#api").css("display","block");
		 $("#open_module").val("api");
	});
	
	$(".class_btn_expand_keywords_module_keywords").click(function(){
		 var cid = this.id.replace('id_btn_expand_keywords_','');
		 //alert($("#id_div_keywords_module_keywords_"+cid).hasClass('hidden'));
		
		 if ($("#id_div_keywords_module_keywords_"+cid).hasClass('class_keywords_hidden'))
		 {
			$("#id_div_keywords_module_keywords_"+cid).css("display","block");
			$("#id_div_keywords_module_keywords_"+cid).removeClass("class_keywords_hidden");
			$("#id_div_keywords_module_keywords_"+cid).addClass('class_keywords_expanded'); 
		 } 
		 else
		 {
			$("#id_div_keywords_module_keywords_"+cid).css("display","none");
			$("#id_div_keywords_module_keywords_"+cid).removeClass("class_keywords_expanded");
			$("#id_div_keywords_module_keywords_"+cid).addClass('class_keywords_hidden'); 
		 }
	    
	});
	
	$("#a_bookmarking_settings").click(function(){
	     $("#id_bookmarking_settings").css("display","block");
	     $("#id_bookmarking_logs").css("display","none");
	});
	
	$("#a_bookmarking_logs").click(function(){
	     $("#id_bookmarking_settings").css("display","none");
	     $("#id_bookmarking_logs").css("display","block");
		 $("#id_iframe_bookmarking_logs").attr('src', '<?php echo $blogsense_url; ?>/functions/f_bookmarking_report.php?mode=posted');
	});
	
	$("#a_bookmarking_logs_ping").click(function(){
	      $("#id_iframe_bookmarking_logs").attr('src', '<?php echo $blogsense_url; ?>functions/f_bookmarking_report.php?mode=ping_posted');
	});
	
	$("#a_bookmarking_logs_twitter").click(function(){
	      $("#id_iframe_bookmarking_logs").attr('src', '<?php echo $blogsense_url; ?>/functions/f_bookmarking_report.php?mode=twitter_posted');
	});
	
	$("#a_bookmarking_logs_pixelpipe").click(function(){
	      $("#id_iframe_bookmarking_logs").attr('src', '<?php echo $blogsense_url; ?>/functions/f_bookmarking_report.php?mode=pixelpipe_posted');
	});
	

	$("#a_bookmarking_logs_all").click(function(){
	      $("#id_iframe_bookmarking_logs").attr('src', '<?php echo $blogsense_url; ?>/functions/f_bookmarking_report.php?mode=posted');
	});
	
	
	$("#add_custom_field_button").click(function(){
	   $('#custom_fields tr:last').after('<tr><td  align=left style=\"font-size:13px;\"><img class=remove_custom_field onClick=\"$(this).parent().parent().remove();" src=\"nav/remove.png\" style=\"cursor:pointer;\"><input size=50 name=\"custom_fields_name[]\" ></td><td  align=right style=\"font-size:13px;\"><select name=\"custom_fields_value[]\" ><option value="image_1">First Image Available</option></select></td></tr>');
	});
	
	$("img.add_rss_string_edit").live("click" ,function(){
       var id = this.id.replace('rss_string_edit_button_','');
	   $('#rss_string_editing_'+id+' tr:last').after("<tr><td  align=left style=\"font-size:13px;\"><img onClick=\"$(this).parent().parent().remove();\" src=\"nav/remove.png\" style=\"cursor:pointer;\"><input size=20 name=\"rss_text_search_"+id+"[]\" ></td><td  align=right style=\"font-size:13px;\"><input size=20 name=\"rss_text_replace_"+id+"[]\"></td></tr>");
	});
	
	
	$("#random_theme_on").click(function(){
	    $("#selected_theme").attr("disabled", true);
	});
	
	$("#random_theme_off").click(function(){
	    $("#selected_theme").attr("disabled", false);
	});
	
	$("img.btn_wp_categories_add").live("click" ,function(){
	   var selects_cats = $('#selects_wp_categories').clone().html(); 
	   selects_cats.replace("200;","100");
	   selects_cats.replace("selected","");
	   $("#tbl_wp_categories tr:last").after("<tr><td><img  onClick='$(this).parent().parent().remove();' src='nav/remove.png' style='cursor:pointer;'>&nbsp;&nbsp;<input type=hidden name='category_id[]' value='x'><input name='category_name[]' size=10 value=''></td><td><input name='category_slug[]' size=10  value=''></td><td>"+selects_cats+"</td><tr>");
	});

	
	$(".scrape_module").change(function(){	   
	   var cur_id = this.id.replace('scrape_module_','');
	   var input =$(this).val();
	   if (input==1)
	   {
		   $("#rss_scrape_temp_"+cur_id).remove();
		   var clone = "<tr><td align=right  style='font-size:13px;'><i>Begin Code:</i></td><td align=right><textarea rows=1 cols=25 name='rss_scrape_begin[]'></textarea></td></tr><tr><td align=right style='font-size:13px;'><i>End Code:</td><td align=right><textarea rows=1 cols=25 name='rss_scrape_end[]'></textarea></td></tr>";
		   $("#rss_scrape_"+cur_id).append(clone);
		   
	   }
	   else
	   {
	        $("#rss_scrape_"+cur_id+" tr:last").remove();
		$("#rss_scrape_"+cur_id+" tr:last").remove();
		var clone = "<tr><td colspan=2><div style='display:none;' id=rss_scrape_temp_"+cur_id+"><input type=hidden name='rss_scrape_begin[]' value=''><input type=hidden name='rss_scrape_end[]' value=''></div></td></tr>";
	        $("#rss_scrape_"+cur_id).append(clone);
	   }
	});
	
	$("#textarea1").click(function(){
	   $("#textarea1").html("");
	});
	
	$("#textarea").click(function(){
	   $("#textarea").html("");
	});
	
	$("#credit_source_off").click(function(){
	    $("#credit_source_text").attr("disabled", true);
		$("#credit_source_nofollow").attr("disabled", true);
	});
	
	$("#credit_source_on").click(function(){
	    $("#credit_source_text").attr("disabled", false);
	    $("#credit_source_nofollow").attr("disabled", false);
	});
	
	$("img.rss_expand").live("click" ,function(){
		var cur_id = this.id.replace('rss_expand_button_','');
		cur_id=eval(cur_id);
		   
		$('#rss_expand_settings_'+cur_id).fadeIn("fast");
		$('#rss_expand_button_'+cur_id).attr("src", "./nav/minus.gif");
		$('#rss_expand_button_'+cur_id).removeClass("rss_expand");
		$('#rss_expand_button_'+cur_id).addClass('rss_minus'); 

	});
	
	$("img.rss_minus").live("click" ,function(){
		var cur_id = this.id.replace('rss_expand_button_','');
		cur_id=eval(cur_id);
		var src = $("img.rss_expand").attr("src");
		   
		$('#rss_expand_settings_'+cur_id).fadeOut("fast");
		$('#rss_expand_button_'+cur_id).attr("src", "./nav/expand.gif");
		$('#rss_expand_button_'+cur_id).removeClass("rss_minus");
		$('#rss_expand_button_'+cur_id).addClass('rss_expand'); 
	
	});
	
	$("img.yahoo_expand").live("click" ,function(){
		var cur_id = this.id.replace('yahoo_expand_button_','');
		cur_id=eval(cur_id);
		   
		$('#yahoo_expand_settings_'+cur_id).fadeIn("fast");
		$('#yahoo_expand_button_'+cur_id).attr("src", "./nav/minus.gif");
		$('#yahoo_expand_button_'+cur_id).removeClass("yahoo_expand");
		$('#yahoo_expand_button_'+cur_id).addClass('yahoo_minus'); 

	});
	
	$("img.yahoo_minus").live("click" ,function(){
		var cur_id = this.id.replace('yahoo_expand_button_','');
		cur_id=eval(cur_id);
		var src = $("img.yahoo_expand").attr("src");
		   
		$('#yahoo_expand_settings_'+cur_id).fadeOut("fast");
		$('#yahoo_expand_button_'+cur_id).attr("src", "./nav/expand.gif");
		$('#yahoo_expand_button_'+cur_id).removeClass("yahoo_minus");
		$('#yahoo_expand_button_'+cur_id).addClass('yahoo_expand'); 
	
	});
	
	if ($("#select_tags_nature").val()=='custom')
	{
		//$('#tags_expand_settings').fadeIn("fast");
		//$('#tags_expand_custom').fadeOut("fast");
	}
	if ($("#select_tags_nature").val()=='wptagsdb')
	{
		$('#tags_expand_settings').fadeIn("fast");
	}
	
	$("#select_tags_nature").change(function(){	   
	   var input =$(this).val();
	   if (input=='custom')
	   {
	        $('#tags_expand_settings').fadeIn("fast");
			$('#tags_expand_custom').fadeIn("fast");
	   }
	   else if (input=='wptagsdb')
	   {
			$('#tags_expand_settings').fadeIn("fast");
			$('#tags_expand_custom').fadeOut("fast");
	   }
	   else
	   {
	        $('#tags_expand_settings').fadeOut("fast");
			$('#tags_expand_custom').fadeOut("fast");
	   }
	});
	
	$(".video_source").change(function(){
	   var cur_id = this.id.replace('video_source_selects_','');		
	   var input =$('#video_source_selects_'+cur_id).val();
	   if (input=='')
	   {
		   $('#source_extra_content_'+cur_id).remove();		   
	   }
	   if (input=='youtube')
	   {
		   $('#source_extra_content_'+cur_id).remove();
		   var clone = "<table id=source_extra_content_"+cur_id+" width='100%'><tr><td  align=left valign=top style='font-size:13px;'><img src='./nav/tip.png' style='cursor:pointer;' border=0 title='Example search phrase: keyword1 keyword2 -excludekeyword.'> Search Terms:</td><td align=right style='font-size:13px;'><input  name='video_input[]' size=28 ></td></tr></table>";
		   $("#source_extra_"+cur_id).append(clone);
	   }
	   else if (input=='hulu')
	   {
	           $('#source_extra_content_'+cur_id).remove();
		   var clone = "<table id=source_extra_content_"+cur_id+" ><tr><td><img src='./nav/tip.png' style='cursor:pointer;' border=0 title='Input feed from hulu.com'> Hulu Feed:  <input name='video_input[]' size=60 value=''></td></tr>";
		   $("#source_extra_"+cur_id).append(clone);
	   }
	   else
	   {
	   }
	});
	
	$("#id_multisite").change(function(){	
	   var input =$('#id_multisite').val();  
	   if (input=='logout')
	   {
			window.location.replace("logout.php");
	   }
	   else
	   {
		  window.location.href = "index.php?blog_id="+input;
		 
	   }
	});
	
	$("#id_select_core_templates").change(function(){	
	   var input =$('#id_select_core_templates').val();  
	   	$('.class_table_core_templates').css("display","none");
	   	$('#id_table_core_template_'+input).fadeIn("fast");
	});
	
	$(".class_btn_bookmarking_pixelpipe").live("click" ,function(){
		//alert(1);
		var cid = this.id.replace('id_btn_add_pixelpipe_','');
		cid = eval(cid);	
		nid = cid+1;
		
		var html = "<table width='530' style='margin-left:auto;margin-right:auto;padding:5px; border: solid 5px #eeeeee'>"
					+"<tr><td align='left'><i>#"+nid+"</i> </td><td align='right'>"
					+"<img  onClick='$(this).parent().parent().parent().parent().remove();' src='nav/remove.png' style='cursor:pointer;'></td>"
					+"</tr><tr><td  align=left valign=top style='font-size:13px;'>"
					+"<img src='./nav/tip.png' style='cursor:pointer;' id=id_add_button_remote_publishing title='Unique PP Email Address. To find: 1. Login to pixelpipe.com 2. Click on Software Solutions. 3. Press \"Tell me More\" under the Email/MMS section. '> "
					+"Pixelpipe Account Email:<br> </td><td align=right style='font-size:13px;'><input name='pixelpipe_email[]' size=30 value=''></td></tr>"
					+"<tr><td  align=left valign=top style='font-size:13px;'>"
					+"<img src='./nav/tip.png' style='cursor:pointer;'  title='PixelPipe Routing Tags. eg: @blogger. To find out what routing tags are available please login to pixelpipe and view \"My Pipes\". Separate routing tags with spaces.'> "
					+"Pixelpipe Routing Tags:<br> </td><td align=right style='font-size:13px;'>"
					+"<input name='pixelpipe_routing[]' size=30 value=''></td></tr></table><br>";
			
		$("#id_table_pixelpipe").append(html);
		$("#id_btn_add_pixelpipe_"+cid).attr("id","id_btn_add_pingfm_"+nid);	
	});
	
}); 
</script>

</head>
<a name=top></a>
<div class="header_container">
    <table>
		
	    <tr>
		    <td align=left> 
				<a id="setup" href="index.php?p=1" class="jquery_header_link">setup</a>  &nbsp;&nbsp;  
				<a id="loader" href="index.php?p=2" class="jquery_header_link">toolbox</a>  &nbsp;&nbsp;  
				<a id='autoblog' href="index.php?p=3" class="jquery_header_link">automation</a>  &nbsp;&nbsp; 
				<a id='bookmarking' href="index.php?p=4" class="jquery_header_link">bookmarking</a>  &nbsp;&nbsp; 
				<a target=_blank  href="../wp-admin/">wp-admin</a> &nbsp;&nbsp;  
				<a target=_blank  href="<?php  echo $siteurl; ?>">launch-blog</a>	 &nbsp;&nbsp;  
				<a target=_blank  href="http://www.hatnohat.com/">forums</a> &nbsp;&nbsp;  
			</td>
			<td  width="401" align=right>
				<?php
				if ($multisite=='on')
				{
				?>							
					<select id='id_multisite' class='multisite_select'>
						<?php
						if ($multisite_blog_id)
						{
							$selected = "";
							if ($_GET['blog_id'])
							{
								$bid = $_GET['blog_id'];
							}
							else
							{
								$bid = $_COOKIE['bs_blog_id'];
							}
							foreach ($multisite_blog_id as $key=>$val)
							{
								
								if ($bid==$val){$selected="selected='true'";}else{ $selected="";}
								echo "<option value='$val' $selected>$multisite_blog_name[$key]</option>";
							}
							echo "<option value='logout'>Logout</option>";
						}
						?>
					</select>					
				<?php
				}
				else
				{
					echo '<a href="logout.php">Logout</a>';
				}
				?>
				
			</td>
		</tr>
	</table>
</div>
<div class="main_container">


<?php
	$query = "SELECT option_value FROM ".$table_prefix."blogsense WHERE option_name='blogsense_activation' || option_name='blogsense_activation_key' || option_name='blogsense_activation_email'";
	$result = mysql_query($query);
	if ($result)
	{
		$count = mysql_num_rows($result);
		for ($i=0;$i<$count;$i++)
		{
		   $array = mysql_fetch_array($result);
		   if ($i==0){ $blogsense_activation = $array['option_value'];}
		   if ($i==2){ $blogsense_activation_key = $array['option_value']; }
		   if ($i==1){ $blogsense_activation_email = $array['option_value']; }
		}
    }
//**********************************************************************************
//**********************************************************************************
//**********************************************************************************
?>

<?php
//*************************************************************
//*****************************blogsense setup********************
//*************************************************************
if ($_GET['p']==1||!$_GET['p'])
{
?>


<div id=blogsense_setup>
		<?php
		if ($_GET['r']=="all")
		{
		  echo "<font style=\"font-size:12px;color:#AAAAAA;\"><center>Please complete all fields to continue.</center></font><br>";
		}
		if ($_GET['a']=="no")
		{
		  echo "<font style=\"font-size:12px;color:#AAAAAA;\"><center>Your license key (and/or) email address is invalid.</center></font><br>";
		}
		if ($_GET['db']=="no")
		{
		  echo "<font style=\"font-size:12px;color:#AAAAAA;\"><center>Your database information is incorrect.</center></font><br>";
		}
		if ($blogsense_activation==1)
		{
		  echo "<center><div class=success style='width:268px'>BlogSense is Installed.</div></center>";
		  ?>
		  <form id='form_install' action="functions/f_activate_blogsense.php" method=POST>
			<table width="335" class='class_center' style="border: solid 1px #eeeeee"> 
			  <tr>
				 <td  align=left style="font-size:13px;">
					BlogSense License:
				 </td>
				 <td  align=left style="font-size:13px;">
					<input name='license' type='password' size=30 value="<?php echo $blogsense_activation_key; ?>">
				 </td>
			  </tr>
			   <tr>
				 <td  align=left style="font-size:13px;">
					BlogSense Email:
				 </td>
				 <td  align=left style="font-size:13px;">
					<input name='license_email' size=30  value="<?php echo $blogsense_activation_email; ?>">
				 </td>
			  </tr>
			  <tr>
				  <td colspan=2>
				  </td>
			  </tr>
			</table> 
			<br>
		
			<center>
		  <?php
		}
		
		if ($blogsense_activation!=1)		
		{
		?>
		<form id='form_install' action="functions/f_activate_blogsense.php" method=POST>
		<table width="335" class='class_center' style="border: solid 1px #eeeeee"> 
		  <tr>
			 <td  align=left style="font-size:13px;">
				BlogSense License:
			 </td>
			 <td  align=left style="font-size:13px;">
				<input name='license' size=30 value="<?php echo $blogsense_activation_key; ?>">
			 </td>
		  </tr>
		   <tr>
			 <td  align=left style="font-size:13px;">
				BlogSense Email:
			 </td>
			 <td  align=left style="font-size:13px;">
				<input name='license_email' size=30  value="<?php echo $blogsense_activation_email; ?>">
			 </td>
		  </tr>
		  <tr>
			  <td colspan=2>
			  </td>
		  </tr>
		</table> 
		<br>
		
		<center>
		<?php
		
		 echo "<div id=i_button class=\"button\" ><a id=\"install_button\" class=\"button\"  style=\"padding-left:51px;padding-right:53px;\" >Install</a></div>";
		}
		else
		{
		 echo "<div id=ui_button class=\"button\" ><a id=\"uninstall_button\" class=\"button\" href=\"functions/f_uninstall.php\" style=\"padding-left:51px;padding-right:53px;\"  onClick=\"return confirm('Are you sure you want to uninstall BlogSense-WP?')\">Uninstall</a></div>";
		}
		?>
		</center>
		</form>
		<br>
</div>

<?php
	echo "</div>";
	
}
if ($_GET['p']&&$_GET['p']!=1&&$blogsense_activation==1)
{
	//*************************************************************
	//*****************************PREPARE VARIABLES***************
	//*************************************************************
	//include variables
	include_once('includes/prepare_variables.php');
	//prepare variables if available
	$query = "SELECT `option_name`, `option_value` FROM ".$table_prefix."options WHERE ";
	$query .= "`option_name`='admin_email' || ";
	$query .= "`option_name`='blogdescription' || ";
	$query .= "`option_name`='blogname' ";
	$result = mysql_query($query);
	if (!$result){echo $query; exit;}
	$count = mysql_num_rows($result);

	for ($i=0;$i<$count;$i++)
	{
	  $arr = mysql_fetch_array($result);
	  if ($i==0){$contact_email =$arr[option_value];}
	  if ($i==1){$blog_subtitle =$arr[option_value];} 
	  if ($i==2){$blog_title = $arr[option_value];} 
	}

	$site_formated = str_replace("http://www.", "open*", $blog_url);
	//echo $blog_comments; exit;
}
//*************************************************************
//*****************************wordpress setup********************
//*************************************************************
if ($_GET['p']==2)
{
?>

<div id=wordpress_settings>	

<?php
if($_GET['success']==1)
{
   echo "<br><center><font style=\"font-size:11;color:green;\">Your Settings Have Been Updated.</font><br><br>";
}
if($_GET['activate']==1)
{
   echo "<br><center><font style=\"font-size:11;color:red;\">Your License Key AND/OR Email is invalid.</font><br><br>";
}
if ($blogsense_activation!=1)
{
    echo "<br><br><br><br><center><font style=\"font-size:11;color:#aaaaaa;\">Please Activate BlogSense</font><br><br><br><br><br>";
}
else
{

?>

		<div id=autoblog_menu class="class_sidemenu" style="width:100%;text-align:center;top:-21px;left:-51px; position:relative;line-height:24px;width:100%;font-size:12px;">
		 <ul>
		    <a id="a_wordpress_general" style="text-decoration:none;color:grey;cursor:pointer;">General Settings</a> &nbsp;&nbsp;|&nbsp;&nbsp;
			 <a id="a_wordpress_rss_tools" style="text-decoration:none;color:grey;cursor:pointer;">RSS Tools</a> &nbsp;&nbsp;|&nbsp;&nbsp;
			<a id="a_wordpress_post_tools" style="text-decoration:none;color:grey;cursor:pointer;">Wordpress Tools</a> &nbsp;&nbsp;|&nbsp;&nbsp;
			<a id="a_wordpress_backup_tools" style="text-decoration:none;color:grey;cursor:pointer;">Backup Tools</a>  &nbsp;&nbsp;|&nbsp;&nbsp;
			<a id="a_wordpress_api_tools" style="text-decoration:none;color:grey;cursor:pointer;">Miscellaneous Tools</a>  
		</ul>
	    </div>
		<br><br><br>
		<form id='form_load' action="functions/f_save_wp_settings.php" method=POST>
		<input type=hidden id=wp_open_module name='open_module' value=general>
		<center>
		<div id='wordpress_general' style="position:relative;top:-77px;width:500px;">
			<br>
			<center><img src='./nav/ico_wordpress.png' border=0><br></center>
			<br>
		    <div class="class_section_header">General Settings</div>
			<hr width="500" style="color:#eeeeee;background-color:#eeeeee;">
			<table width="500" style="margin-left:auto;margin-right:auto;padding:5px; border: solid 5px #eeeeee"> 
			    <tr>
					<td  align=left style="font-size:13px;">
						Blog URL:
					</td>
					<td align=right style="font-size:13px;">
						<input name='blog_url' size=57 value="<?php echo $blog_url; ?>">
					</td>
				</tr>		  
				<tr>
					<td  align=left style="font-size:13px;">
						Blog Title:
					</td>
					<td align=right style="font-size:13px;">
						<input name='blog_title' size=57 value="<?php echo $blog_title; ?>">
					</td>
				</tr>
				<tr>
					<td  align=left style="font-size:13px;">
						Blog Subtitle:
					</td>
					<td align=right style="font-size:13px;">
						<input name='blog_subtitle' size=57 value="<?php echo $blog_subtitle; ?>">
					</td>
				</tr>			  
				<tr>
					<td  align=left style="font-size:13px;">
						Contact Email:
					</td>
					<td align=right style="font-size:13px;">
						<input name='contact_email' size=57 value="<?php echo $contact_email; ?>">
					</td>
				</tr>
				<tr>
					<td  align=left style="font-size:13px;">
						Default Author:
					</td>
					<td  align=right style="font-size:13px;">
						<select name='default_author'>
							<?php
							foreach ($authors_id as $k=>$v)
							{
								if ($default_author==$v)
								{
									echo "<option value=$v selected=true>$authors_usernames[$k]</option>";
								}
								else
								{
									echo "<option value=$v >$authors_usernames[$k]</option>";
								}
							}
							?>
						</select>
					</td>
				</tr>
			</table>
			<br>
			
			<table width="500" style="margin-left:auto;margin-right:auto;padding:5px; border: solid 5px #eeeeee"> 
			    <tr>
					<td  align=left style="font-size:13px;">
						BlogSense Activation Key:
					</td>
					<td align=right style="font-size:13px;">
						<input name='blogsense_activation_key' type='password' size=47 value="<?php echo $blogsense_activation_key; ?>">
					</td>
				</tr>		  
				<tr>
					<td  align=left style="font-size:13px;">
						BlogSense Activation Email:
					</td>
					<td align=right style="font-size:13px;">
						<input name='blogsense_activation_email' type='password' size=47 value="<?php echo $blogsense_activation_email; ?>">
					</td>
				</tr>
			</table> 
			<br>
			
			<?php
					$query = "SELECT option_value FROM {$table_prefix}options WHERE option_name='active_plugins' AND option_value LIKE '%blogsense-connect%'";
					$result = mysql_query($query);
					if (mysql_num_rows($result)==1)
					{
						echo "<div class=success>BlogSense Connect Detected!</div>";
					}
					else
					{
						echo "<div class=info>BlogSense Connect is not Detected!</div>";
					}
				?>
			<div style="margin-left:auto;margin-right:auto;padding:5px; border: solid 5px #eeeeee;width:480px" class='' id='id_accordion_cronjobs'> 
				
					<h3 style='font-size:11px;text-align:left;padding-left:20px;'><a href="#">Download BlogSense Connect</a></h3>
					<div align='left'>
						<font style="font-size:10px;">
						<i><a href='http://wordpress.org/extend/plugins/blogsense-connect/'>http://wordpress.org/extend/plugins/blogsense-connect/</a></i>
						<br><br>
						</font>					
					</div>
					<h3 style='font-size:11px;text-align:left;padding-left:20px;'><a href="#">Cpanel Cronjob Path (If not using BlogSense Connect):</a></h3>
					<div align='left'>
						<font style="font-size:10px;">		
						<u>No Bookmarking & Pinging:</u><br> <i><?php echo dirname(__FILE__); ?>/cron_config.php?blog_id=<?php echo $bid;?><br> (run when you want the campaigns to execute; Running this manually will ignore any internal cronjob settings set within BlogSense)</i> <br>
							
						<br><br>
						</font>					
					</div>
			</div>
			
			<br>
			<center>
			<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='update_sql.php' target=_new>
				[ Repair BlogSense Tables ]
			</a>
			</center>

		</div>
		
		<div id=wordpress_rss_tools style="position:relative;top:-77px;width:500px;">			
			
				<div style='display:inline;width:400px;' align=center>
					<br>
					<font style='text-decoration: none; color: grey; cursor: pointer;font-size:11px'>
					
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./includes/fivefilters/' target='_blank'>
						<img src='./nav/ico_fivefilters.png' border=0><br>
						Five Filters - Full Content RSS
					</a><br><br><br><br>
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./includes/i_class_email2rss.php'  target='_blank'>
					<img src='./nav/ico_emailtorss.png' border=0><br>
						Email to RSS 
					</a>
					<br><br><br>
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./includes/feed-combiner/make_feed.php'  target='_blank'>
					<img src='./nav/ico_combinerss.png' border=0><br>
						Combine RSS Feeds 
					</a>
					
					
					
					</font>
				</div>
			
			
			
			<br>
			
			
			
		</div>
		
		<div id=wordpress_post_tools style="position:relative;top:-77px;width:500px;">			
			
				<div style='display:inline;width:400px;' align=center>
				<br><br>
					<font style='text-decoration: none; color: grey; cursor: pointer;font-size:11px'>
					
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./functions/f_database_functions.php?mode=search_replace' class='database_tools'>
						<img src='./nav/ico_search_replace.png' border=0><br>
						Mass Search & Replace in Posts
					</a>
					
					<br><br><br>
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./functions/f_database_functions.php?mode=mass_edit_data' class='database_tools'>
					<img src='./nav/ico_edit_terms.png' border=0><br>
					Quick Edit Post Titles & Tags
					</a> 
					
					<br><br><br>
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./functions/f_database_functions.php?mode=create_categories' class='database_tools'>
					<img src='./nav/ico_edit_terms.png' border=0><br>
					Create Categories from Keyword List
					</a>

					<br><br><br>
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./functions/f_database_functions.php?mode=delete_duplicate' class='database_tools'  onClick="return confirm('Running this feature will delete all additional posts containing duplicate titles, are you sure you would like to run?')">
					<img src='./nav/ico_trash.png' border=0><br>
					Delete Duplicate Posts
					</a> <br><br><br><br>
					
					</font>
				</div>
			
			
			
			<br>
			
			
			
		</div>
		
		<div id=wordpress_backup_tools style="position:relative;top:-77px;width:550px;">			
			
			
			<div style='display:inline;width:500px;' align=center>
			<br><br>
				<table width=500 style='padding:20px;'>
					<tr>
						<td align=center valign=bottom>
							<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./includes/backup/wordpress_clone.php' class=''>
								<img src='./nav/ico_backup.png' border=0><br>
								Create Backup Profile
							</a>
						</td>
						<td align=center valign=bottom>
							<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./includes/backup/wordpress_restore.php' class='database_tools'>
								<img src='./nav/ico_restore.png' border=0><br>
								Restore/Import Profile
							</a>
						</td>
					</tr>
					<tr>
						<td align=left colspan=2 style='font-size:15px;text-align:left;padding-top:10px'>
							<br>
							<b>Wordpress Comprehensive Backups</b>
						</td>
					</tr>
					<tr>
						<td align=left colspan=2 style='font-size:14px;text-align:justify;padding-top:5px'>
							This feature creates a complete backup profile of your entire wordpress blog, including all plugins, plugin settings, themes, theme settings and Blogsense settings and campaigns. This feature does not backup all sub-blogs of a multisite installation, but instead only backups the sub-blog of focus. Once created, backup profiles are auto-saved to your <i>/my-backup/</i> directory as well as offered to save on your hard drive.
							
						</td>
					</tr>
				</table>
				<br><br>

				<br><br>
				<table width=500 style='padding:20px;'>
					<tr>
						<td align=center valign=bottom >
							<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./includes/backup/backup_campaigns.php?nature=export' target='_blank'>
								<img src='./nav/ico_export_campaigns.png' border=0><br>
								Export Campaigns
							</a>							
						</td>
						<td align=center valign=bottom>
							<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./includes/backup/backup_campaigns.php?nature=import' target='_blank'>
								<img src='./nav/ico_import_campaigns.png' border=0><br>
								Import Campaigns
							</a>							
						</td>
					</tr>
					<tr>
						<td align=left colspan=2 style='font-size:15px;text-align:left;padding-top:10px'>
							<br>
							<b>BlogSense Specific Backup Tools</b>
						</td>
					</tr>
					<tr>
						<td align=left colspan=2 style='font-size:14px;text-align:justify;padding-top:5px'>
							Use these features to help save and import BlogSense campaign data.
						</td>
					</tr>
				</table>
				<br><br><br>
			
				
				
				
				</font>
			</div>
			
			
		</div>
		
		<div id=wordpress_api_tools style="position:relative;top:-77px;width:500px;">			
			
				<div style='display:inline;width:400px;' align=center>
					<br>
					<font style='text-decoration: none; color: grey; cursor: pointer;font-size:11px'>
					
					<h3>BlogSense Logs</h3>
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./api/rpl_interface.php' target='_blank'>
						Remote Publishing - Feedback Logs
					</a><br><br>
					
					<h3>Internal Spinner Tools</h3>
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./api/spin_api.php' target='_blank'>
						Access BlogSense's Internal Spinner
					</a><br><br>
					
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./functions/f_manage_spin_database.php' target='_blank'>
						Manage BlogSense's Internal Spinner
					</a><br><br>
					
					<h3>List Management Tools</h3>
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./includes/feed-combiner/rss2urls.php'  target='_blank'>
					<br>
						Convert RSS to a URL List (With or without Keywords)
					</a><br><br>
					
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./includes/feed-combiner/urllist2formattedurllist.php'  target='_blank'>
					<br>
						Generate Keywords for a URL List
					</a><br><br>
					
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./includes/shorten-urls/index.php'  target='_blank'>
					<br>
						Mass URL Shortening 
					</a><br><br>
					
					<a style='text-decoration: none; color: grey; cursor: pointer;font-size:11px' href='./includes/noreply-email-generator/index.php'  target='_blank'>
					<br>
						Noreply Email List Generator
					</a><br><br>
					
					
					
					<br><br><br><br>
					
					</font>
				</div>
			
			
			
			<br>
			
			
			
		</div>
		
		<center>
		    <div id=l_button class="button" ><a id="load_button" class="button"  style="padding-left:51px;padding-right:53px;" >Save Settings</a></div>		
		</center>
		</form>	
		
		
</center>		
</div>
</div>
<?php
}
}
?>

<?php
//*************************************************************
//*****************************autoblog setup********************
//*************************************************************

if ($_GET['p']==3)
{
?>

<div id=autoblog_settings>	

<?php
if($_GET['success']==1&&$_GET['p']==3)
{
   echo "<br><center><font style=\"font-size:11;color:#aaaaaa;\">Your Settings Have Been Updated.</font><br><br>";
}
if ($_GET['saved']=='y'&&$_GET['p']==3)
{
    echo "<br><center><font style=\"font-size:11;color:green;\">Settings Saved!</font><br><br>";
}
if ($_GET['reset']=='y'&&$_GET['p']==3)
{
    echo "<br><center><font style=\"font-size:11;color:green;\">RSS Counter Reset!</font><br><br>";
}
if ($blogsense_activation!=1)
{
    echo "<br><br><br><br><center><font style=\"font-size:11;color:#aaaaaa;\">Please Activate BlogSense</font><br><br><br><br><br>";
}
else
{


//prepare categoires			
$slugs= array();
$categories = array();
$cat_ids = array();

//module open
if ($_GET['m'])
{
 $open_module = $_GET['m'];
}
else
{
  $open_module = "global_settings";
}

			
$query = "SELECT t.name, t.slug, t.term_id, tt.term_id, tt.term_taxonomy_id FROM ".$table_prefix."term_taxonomy tt,  ".$table_prefix."terms t WHERE tt.term_id = t.term_id AND tt.taxonomy='category'";
$result = mysql_query($query);
if (!$result){echo $query; exit;}
while ($arr = mysql_fetch_array($result))
{
	$slugs[] = $arr['slug'];
	$categories[] = $arr['name'];
	$cat_ids[] = $arr['term_taxonomy_id'];
}


?>
	<a name=top></a>	
	<div id=autoblog_menu class="class_sidemenu" style="top:-21px;left:-51px; text-align:left;position:relative;line-height:24px;width:100%;font-size:12px;">
		 <ul>
			<a id="a_global_settings" style="text-decoration:none;color:grey;cursor:pointer;">Global Settings</a> &nbsp;|&nbsp;
	        <a id="a_rss_nodes" style="text-decoration:none;color:grey;cursor:pointer;">RSS Feeds</a> &nbsp;|&nbsp;
			<a id="a_sources_nodes"  style="text-decoration:none;color:grey;cursor:pointer;">Custom Sources  </a> &nbsp;|&nbsp;
			<a id="a_video_nodes" style="text-decoration:none;color:grey;cursor:pointer;">Videos</a> &nbsp;|&nbsp;
	        <a id="a_drop_nodes"  style="text-decoration:none;color:grey;cursor:pointer;">Drop Posting / CSV Importing</a> &nbsp;|&nbsp;
			<a id="a_yahoo_nodes" style="text-decoration:none;color:grey;cursor:pointer;">Yahoo Answers</a>  &nbsp;|&nbsp;
	        <a id="a_amazon_nodes" style="text-decoration:none;color:grey;cursor:pointer;">Amazon</a>  	&nbsp;|&nbsp;
	        <a id="a_keywords_nodes" style="text-decoration:none;color:grey;cursor:pointer;">Keywords Module</a> <br>			
			<a id="a_templates" style="text-decoration:none;color:grey;cursor:pointer;">Core Templates</a> &nbsp;|&nbsp;
			<a id="a_api" style="text-decoration:none;color:grey;cursor:pointer;">API</a> 
	        
	     </ul>
	</div>
	
       <div style="text-align:center;font-size:12px;width:100%;margin-left:11px;margin-right:auto;font-weight:300;margin-top:0px;">
       <?php
       //check to see if image directory is writeable
		if ($store_images_relative_path&&!is_writable($store_images_relative_path))
		{
			echo "<center><div class='error'>The Images Relative Path Folder is not currently writable. <br>Please set permissions on the folder to 755.</div>";
		}
		if (!$store_images_relative_path)
		{
			echo "<center><div class='warning'><b>Is this a new Install?</b> Please browse and save settings below before adding campaigns for image saving to work correctly.</div><br></center>";
		}
	?>
       <form id='form_save' action="functions/f_save_autoblog_settings.php" method=POST >
	   <input type=hidden id=open_module name="open_module" value="<?php echo $open_module; ?>">  
	   
		<div id="global_settings">
		
		<div class="class_section_header">Global Settings</div>
		<hr style="width:530px;color:#eeeeee;background-color:#eeeeee;">
		
		<div class="class_section_header"><div class='tab'>Image Hosting</div></div>
		<table  class="campaign"> 
			
		  <tr>
			 <td  align=left valign=top style="font-size:13px;">
				Image Hosting:
			</td>
			 <td align=right style="font-size:13px;">
				<input  name='store_images' type=radio value=1 <?php if ($store_images=="1") {echo "checked";}  ?>> on &nbsp; <input name='store_images' type='radio' value=0  <?php if ($store_images=="0") {echo "checked";}  ?>> off
			</td>
		  </tr>
		  <tr>
			 <td  align=left valign=top style="font-size:13px;">
				<img src="nav/tip.png" style="cursor:pointer;" border=0 title="Directory path to directory that you will host your images, relative to this page.">
				Images Folder Relative Path:<br>				
			</td>
			 <td align=right style="font-size:13px;">
				<input  name="store_images_relative_path" size=44 value='<?php if ($store_images_relative_path){ echo $store_images_relative_path; }else { echo "../images/"; }?>'>
			</td>
		  </tr>
		  <tr>
			 <td  align=left valign=top style="font-size:13px;">
				<img src="nav/tip.png" style="cursor:pointer;" border=0 title="Like relative path, but instead the full url to your image hosting directory.">
				Images Full Url:
			</td>
			 <td align=right style="font-size:13px;">
				<input  name="store_images_full_url" size=44 value='<?php if ($store_images_full_url){ echo $store_images_full_url; }else { echo $main_blog_url."/images/"; }?>'>
			</td>
		  </tr>			  	
		</table> 
		<br>
		
		<div class="class_section_header"><div class='tab'>Extra Tagging Options</div></div>
		<table class="campaign"> 
			<tr>
				<td  align=left valign=top style="font-size:13px;">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="For each tag, generate a random typo tag. Currently this feature can only be applied globally to all campaigns.">
					Add Tag Typos
				</td>
				<td align=right style="font-size:13px;">
					<input  name='post_tags_typo' type=radio value='1' <?php if ($post_tags_typo=="1") {echo "checked='checked'";}  ?>>on &nbsp; <input name='post_tags_typo' type=radio value='0'  <?php if ($post_tags_typo=="0") {echo "checked='checked'";}  ?>> off
				</td>
			</tr>
		</table> 		
		<br>
		
		
		<br>
		
		<div class="class_section_header"><div class='tab'>Draft Notification / Approval System</div></div>
		<table class="campaign"> 
			<tr>
				<td colspan=2 align=left valign=top style="font-size:13px;">
					<i>The draft approval system will send automatic email notifications of posts that have been tagged as a draft or for pending review(these notifications will only apply to articles published through BlogSense) to allow quick approval, deletion, or in-wordpress editing. BlogSense automatically applies it's own keyword density reports at the end of each eamil notification for user review. Provide an email address and enable to activate.</i><br><br>
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					Draft notification system<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='draft_notification' type=radio value='1' <?php if ($draft_notification=="1") {echo "checked='checked'";}  ?>>on &nbsp; <input name='draft_notification' type=radio value='0'  <?php if ($draft_notification=="0") {echo "checked='checked'";} ?>> off
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					Email address:<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='draft_notification_email' size=20 value='<?php echo $draft_notification_email; ?>'>
				</td>
			</tr>
		</table> 		
		<br>
		
		
		<br>
		
		
		<div class="class_section_header"><div class='tab'>Cronjob Settings.</div></div>
		<table  class="campaign"> 
			<tbody>
			<tr>
				<td colspan=3 align=left valign=top style="font-size:13px;">
					<i>BlogSense uses the Wordpress Pseudocron to power it's automation, but in order for this to happen the <a href='http://wordpress.org/extend/plugins/blogsense-connect/' target='_blank'>BlogSense Connect</a> Wordpress Plugin must be installed and active. Once active, human/spider traffic to your blog will power your BlogSense system. Use the time-settings below to indicate when BlogSense should fire(run) all your activated campaigns. 
					<br><br>
					Warning! If you are running multiple installs of BlogSense on one server, be careful not to overlap run times as running BlogSense requires a high level of server resources and conflicts might interfere with server performance. It's also good to know that you should not try to do other tasks within BlogSense while running campaings to keep system resources and free as possible. 
					</td>
			</tr>
			<tr>
				<td colspan=3 style="text-align:right;">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="BlogSense is powered by two cronjobs, an external and an internal. The external one is managed by the Wordpress plugin BlogSense Connect, which can be found in your /auto/my-plugins folder or via Google. The second cronjob (internal cronjob) is configured here. This will tell BlogSense how often it should attempt to pull new data from campaigns. Be careful: running this too often may slow down server performance depending on the number of campaigns setup. ">
				</td>
			</tr>
			<tr>
				<td align='left' >
					&nbsp;
					<img src="nav/tip.png"  style="cursor:pointer;" border=0 title="Leave blank to turn notifications off.">&nbsp;
					Notification Email: 
				</td>
				<td colspan=2 style="text-align:right;" align='right'>
					<input name='cron_email' size=51 value="<?php echo $cronjob_email; ?>">
				</td>
			</tr>
			<tr>
				<td colspan=3 style="text-align:right;">
				<br>
				</td>
			</tr>
			<tr>
				<td valign='top'>Minute(s):<br>

					<select multiple="multiple" name="cron_minutes"  style="height:235px;width: 150px;">
					<option value="*/5" <?php if ($cronjob_minutes=='*/5'){echo "selected='true'"; }?>> Every Five Minutes
					</option><option value="*/10" <?php if ($cronjob_minutes=='*/10'){echo "selected='true'"; }?>> Every Ten Minutes
					</option><option value="*/15" <?php if ($cronjob_minutes=='*/15'){echo "selected='true'"; }?>> Every Fifteen Minutes
					</option><option value="0" <?php if ($cronjob_minutes=='0' ||!$cronjob_minutes){echo "selected='true'"; }?>> 0
					</option><option value="1" <?php if ($cronjob_minutes=='1'){echo "selected='true'"; }?>> 1
					</option><option value="2" <?php if ($cronjob_minutes=='2'){echo "selected='true'"; }?>> 2

					</option><option value="3" <?php if ($cronjob_minutes=='3'){echo "selected='true'"; }?>> 3
					</option><option value="4" <?php if ($cronjob_minutes=='4'){echo "selected='true'"; }?>> 4
					</option><option value="5" <?php if ($cronjob_minutes=='5'){echo "selected='true'"; }?>> 5
					</option><option value="6" <?php if ($cronjob_minutes=='6'){echo "selected='true'"; }?>> 6
					</option><option value="7" <?php if ($cronjob_minutes=='7'){echo "selected='true'"; }?>> 7
					</option><option value="8" <?php if ($cronjob_minutes=='8'){echo "selected='true'"; }?>> 8
					</option><option value="9" <?php if ($cronjob_minutes=='9'){echo "selected='true'"; }?>> 9
					</option><option value="10" <?php if ($cronjob_minutes=='10'){echo "selected='true'"; }?>> 10
					</option><option value="11" <?php if ($cronjob_minutes=='11'){echo "selected='true'"; }?>> 11

					</option><option value="12" <?php if ($cronjob_minutes=='12'){echo "selected='true'"; }?>> 12
					</option><option value="13" <?php if ($cronjob_minutes=='13'){echo "selected='true'"; }?>> 13
					</option><option value="14" <?php if ($cronjob_minutes=='14'){echo "selected='true'"; }?>> 14
					</option><option value="15" <?php if ($cronjob_minutes=='15'){echo "selected='true'"; }?>> 15
					</option><option value="16" <?php if ($cronjob_minutes=='16'){echo "selected='true'"; }?>> 16
					</option><option value="17" <?php if ($cronjob_minutes=='17'){echo "selected='true'"; }?>> 17
					</option><option value="18" <?php if ($cronjob_minutes=='18'){echo "selected='true'"; }?>> 18
					</option><option value="19" <?php if ($cronjob_minutes=='19'){echo "selected='true'"; }?>> 19
					</option><option value="20" <?php if ($cronjob_minutes=='20'){echo "selected='true'"; }?>> 20

					</option><option value="21" <?php if ($cronjob_minutes=='21'){echo "selected='true'"; }?>> 21
					</option><option value="22" <?php if ($cronjob_minutes=='22'){echo "selected='true'"; }?>> 22
					</option><option value="23" <?php if ($cronjob_minutes=='23'){echo "selected='true'"; }?>> 23
					</option><option value="24" <?php if ($cronjob_minutes=='24'){echo "selected='true'"; }?>> 24
					</option><option value="25" <?php if ($cronjob_minutes=='25'){echo "selected='true'"; }?>> 25
					</option><option value="26" <?php if ($cronjob_minutes=='26'){echo "selected='true'"; }?>> 26
					</option><option value="27" <?php if ($cronjob_minutes=='27'){echo "selected='true'"; }?>> 27
					</option><option value="28" <?php if ($cronjob_minutes=='28'){echo "selected='true'"; }?>> 28
					</option><option value="29" <?php if ($cronjob_minutes=='29'){echo "selected='true'"; }?>> 29

					</option><option value="30" <?php if ($cronjob_minutes=='30'){echo "selected='true'"; }?>> 30
					</option><option value="31" <?php if ($cronjob_minutes=='31'){echo "selected='true'"; }?>> 31
					</option><option value="32" <?php if ($cronjob_minutes=='32'){echo "selected='true'"; }?>> 32
					</option><option value="33" <?php if ($cronjob_minutes=='33'){echo "selected='true'"; }?>> 33
					</option><option value="34" <?php if ($cronjob_minutes=='34'){echo "selected='true'"; }?>> 34
					</option><option value="35" <?php if ($cronjob_minutes=='35'){echo "selected='true'"; }?>> 35
					</option><option value="36" <?php if ($cronjob_minutes=='36'){echo "selected='true'"; }?>> 36
					</option><option value="37" <?php if ($cronjob_minutes=='37'){echo "selected='true'"; }?>> 37
					</option><option value="38" <?php if ($cronjob_minutes=='38'){echo "selected='true'"; }?>> 38

					</option><option value="39" <?php if ($cronjob_minutes=='39'){echo "selected='true'"; }?>> 39
					</option><option value="40" <?php if ($cronjob_minutes=='40'){echo "selected='true'"; }?>> 40
					</option><option value="41" <?php if ($cronjob_minutes=='41'){echo "selected='true'"; }?>> 41
					</option><option value="42" <?php if ($cronjob_minutes=='42'){echo "selected='true'"; }?>> 42
					</option><option value="43" <?php if ($cronjob_minutes=='43'){echo "selected='true'"; }?>> 43
					</option><option value="44" <?php if ($cronjob_minutes=='44'){echo "selected='true'"; }?>> 44
					</option><option value="45" <?php if ($cronjob_minutes=='45'){echo "selected='true'"; }?>> 45
					</option><option value="46" <?php if ($cronjob_minutes=='46'){echo "selected='true'"; }?>> 46
					</option><option value="47" <?php if ($cronjob_minutes=='47'){echo "selected='true'"; }?>> 47

					</option><option value="48" <?php if ($cronjob_minutes=='48'){echo "selected='true'"; }?>> 48
					</option><option value="49" <?php if ($cronjob_minutes=='49'){echo "selected='true'"; }?>> 49
					</option><option value="50" <?php if ($cronjob_minutes=='50'){echo "selected='true'"; }?>> 50
					</option><option value="51" <?php if ($cronjob_minutes=='51'){echo "selected='true'"; }?>> 51
					</option><option value="52" <?php if ($cronjob_minutes=='52'){echo "selected='true'"; }?>> 52
					</option><option value="53" <?php if ($cronjob_minutes=='53'){echo "selected='true'"; }?>> 53
					</option><option value="54" <?php if ($cronjob_minutes=='54'){echo "selected='true'"; }?>> 54
					</option><option value="55" <?php if ($cronjob_minutes=='55'){echo "selected='true'"; }?>> 55
					</option><option value="56" <?php if ($cronjob_minutes=='56'){echo "selected='true'"; }?>> 56

					</option><option value="57" <?php if ($cronjob_minutes=='57'){echo "selected='true'"; }?>> 57
					</option><option value="58" <?php if ($cronjob_minutes=='58'){echo "selected='true'"; }?>> 58
					</option><option value="59" <?php if ($cronjob_minutes=='59'){echo "selected='true'"; }?>> 59
					</option></select><br><br><center>
				</td>
			<td valign=top>
				Hour(s):<br>
				<select multiple="multiple" name="cron_hours"  style="height:100px;width: 150px;">
					<option value="*" <?php if ($cronjob_hours=='*'){echo "selected='true'"; }?>> Every Hour
					</option><option value="*/2" <?php if ($cronjob_hours=='*/2'){echo "selected='true'"; }?>> Every Other Hour

					</option><option value="*/4" <?php if ($cronjob_hours=='*/4'||!$cronjob_hours){echo "selected='true'"; }?>> Every Four Hours
					</option><option value="*/6" <?php if ($cronjob_hours=='*/6'){echo "selected='true'"; }?>> Every Six Hours
					</option><option value="*/8" <?php if ($cronjob_hours=='*/8'){echo "selected='true'"; }?>> Every Eight Hours
					</option><option value="0" <?php if ($cronjob_hours=='0'){echo "selected='true'"; }?>> 0 = 12 AM/Midnight
					</option><option value="1" <?php if ($cronjob_hours=='1'){echo "selected='true'"; }?>> 1 = 1 AM
					</option><option value="2" <?php if ($cronjob_hours=='2'){echo "selected='true'"; }?>> 2 = 2 AM
					</option><option value="3" <?php if ($cronjob_hours=='3'){echo "selected='true'"; }?>> 3 = 3 AM
					</option><option value="4" <?php if ($cronjob_hours=='4'){echo "selected='true'"; }?>> 4 = 4 AM
					</option><option value="5" <?php if ($cronjob_hours=='5'){echo "selected='true'"; }?>> 5 = 5 AM
					</option><option value="6" <?php if ($cronjob_hours=='6'){echo "selected='true'"; }?>> 6 = 6 AM

					</option><option value="7" <?php if ($cronjob_hours=='7'){echo "selected='true'"; }?>> 7 = 7 AM
					</option><option value="8" <?php if ($cronjob_hours=='8'){echo "selected='true'"; }?>> 8 = 8 AM
					</option><option value="9" <?php if ($cronjob_hours=='9'){echo "selected='true'"; }?>> 9 = 9 AM
					</option><option value="10" <?php if ($cronjob_hours=='10'){echo "selected='true'"; }?>> 10 = 10 AM
					</option><option value="11" <?php if ($cronjob_hours=='11'){echo "selected='true'"; }?>> 11 = 11 AM
					</option><option value="12" <?php if ($cronjob_hours=='12'){echo "selected='true'"; }?>> 12 = 12 PM/Noon
					</option><option value="13" <?php if ($cronjob_hours=='13'){echo "selected='true'"; }?>> 13 = 1 PM
					</option><option value="14" <?php if ($cronjob_hours=='14'){echo "selected='true'"; }?>> 14 = 2 PM
					</option><option value="15" <?php if ($cronjob_hours=='15'){echo "selected='true'"; }?>> 15 = 3 PM

					</option><option value="16" <?php if ($cronjob_hours=='16'){echo "selected='true'"; }?>> 16 = 4 PM
					</option><option value="17" <?php if ($cronjob_hours=='17'){echo "selected='true'"; }?>> 17 = 5 PM
					</option><option value="18" <?php if ($cronjob_hours=='18'){echo "selected='true'"; }?>> 18 = 6 PM
					</option><option value="19" <?php if ($cronjob_hours=='19'){echo "selected='true'"; }?>> 19 = 7 PM
					</option><option value="20" <?php if ($cronjob_hours=='20'){echo "selected='true'"; }?>> 20 = 8 PM
					</option><option value="21" <?php if ($cronjob_hours=='21'){echo "selected='true'"; }?>> 21 = 9 PM
					</option><option value="22" <?php if ($cronjob_hours=='22'){echo "selected='true'"; }?>> 22 = 10 PM
					</option><option value="23" <?php if ($cronjob_hours=='23'){echo "selected='true'"; }?>> 23 = 11 PM
					</option></select>

					<br><br>Day(s):<br>
					<select multiple="multiple" name="cron_days"  style="height:100px;width: 150px;">
					<option value="*"  <?php if ($cronjob_days=='*'||!$cronjob_days){echo "selected='true'"; }?>>  Every Day
					</option><option value="1" <?php if ($cronjob_days=='1'){echo "selected='true'"; }?>> 1
					</option><option value="2" <?php if ($cronjob_days=='2'){echo "selected='true'"; }?>> 2
					</option><option value="3" <?php if ($cronjob_days=='3'){echo "selected='true'"; }?>> 3
					</option><option value="4" <?php if ($cronjob_days=='4'){echo "selected='true'"; }?>> 4
					</option><option value="5" <?php if ($cronjob_days=='5'){echo "selected='true'"; }?>> 5
					</option><option value="6" <?php if ($cronjob_days=='6'){echo "selected='true'"; }?>> 6

					</option><option value="7" <?php if ($cronjob_days=='7'){echo "selected='true'"; }?>> 7
					</option><option value="8" <?php if ($cronjob_days=='8'){echo "selected='true'"; }?>> 8
					</option><option value="9" <?php if ($cronjob_days=='9'){echo "selected='true'"; }?>> 9
					</option><option value="10" <?php if ($cronjob_days=='10'){echo "selected='true'"; }?>> 10
					</option><option value="11" <?php if ($cronjob_days=='11'){echo "selected='true'"; }?>> 11
					</option><option value="12" <?php if ($cronjob_days=='12'){echo "selected='true'"; }?>> 12
					</option><option value="13" <?php if ($cronjob_days=='13'){echo "selected='true'"; }?>> 13
					</option><option value="14" <?php if ($cronjob_days=='14'){echo "selected='true'"; }?>> 14
					</option><option value="15" <?php if ($cronjob_days=='15'){echo "selected='true'"; }?>> 15

					</option><option value="16" <?php if ($cronjob_days=='16'){echo "selected='true'"; }?>> 16
					</option><option value="17" <?php if ($cronjob_days=='17'){echo "selected='true'"; }?>> 17
					</option><option value="18" <?php if ($cronjob_days=='18'){echo "selected='true'"; }?>> 18
					</option><option value="19" <?php if ($cronjob_days=='19'){echo "selected='true'"; }?>> 19
					</option><option value="20" <?php if ($cronjob_days=='20'){echo "selected='true'"; }?>> 20
					</option><option value="21" <?php if ($cronjob_days=='21'){echo "selected='true'"; }?>> 21
					</option><option value="22" <?php if ($cronjob_days=='22'){echo "selected='true'"; }?>> 22
					</option><option value="23" <?php if ($cronjob_days=='23'){echo "selected='true'"; }?>> 23
					</option><option value="24" <?php if ($cronjob_days=='24'){echo "selected='true'"; }?>> 24

					</option><option value="25" <?php if ($cronjob_days=='25'){echo "selected='true'"; }?>> 25
					</option><option value="26" <?php if ($cronjob_days=='26'){echo "selected='true'"; }?>> 26
					</option><option value="27" <?php if ($cronjob_days=='27'){echo "selected='true'"; }?>> 27
					</option><option value="28" <?php if ($cronjob_days=='28'){echo "selected='true'"; }?>> 28
					</option><option value="29" <?php if ($cronjob_days=='29'){echo "selected='true'"; }?>> 29
					</option><option value="30" <?php if ($cronjob_days=='30'){echo "selected='true'"; }?>> 30
					</option><option value="31" <?php if ($cronjob_days=='31'){echo "selected='true'"; }?>> 31
					</option></select><br><br>
				</td>
				<td valign=top>Months(s):<br>
					<select multiple="multiple" name="cron_months"  style="height:100px;width: 150px;">
						<option value="*" <?php if ($cronjob_months=='*'||!$cronjob_months){echo "selected='true'"; }?>> Every Month
						</option><option value="1" <?php if ($cronjob_months=='1'){echo "selected='true'"; }?>> January
						</option><option value="2" <?php if ($cronjob_months=='2'){echo "selected='true'"; }?>> February
						</option><option value="3" <?php if ($cronjob_months=='3'){echo "selected='true'"; }?>> March
						</option><option value="4" <?php if ($cronjob_months=='4'){echo "selected='true'"; }?>> April
						</option><option value="5" <?php if ($cronjob_months=='5'){echo "selected='true'"; }?>> May
						</option><option value="6" <?php if ($cronjob_months=='6'){echo "selected='true'"; }?>> June
						</option><option value="7" <?php if ($cronjob_months=='7'){echo "selected='true'"; }?>> July

						</option><option value="8" <?php if ($cronjob_months=='8'){echo "selected='true'"; }?>> August
						</option><option value="9" <?php if ($cronjob_months=='9'){echo "selected='true'"; }?>> September
						</option><option value="10" <?php if ($cronjob_months=='10'){echo "selected='true'"; }?>> October
						</option><option value="11" <?php if ($cronjob_months=='11'){echo "selected='true'"; }?>> November
						</option><option value="12" <?php if ($cronjob_months=='12'){echo "selected='true'"; }?>> December
						</option></select>
						
						<br><br>Weekday(s):<br>
						<select multiple="multiple" name="cron_weekdays"  style="height:100px;width: 150px;">
						<option value="*"  <?php if ($cronjob_weekdays=='*'||!$cronjob_weekdays){echo "selected='true'"; }?>> Every Week Day
						</option><option value="0" <?php if ($cronjob_weekdays=='0'){echo "selected='true'"; }?>> Sunday
						</option><option value="1" <?php if ($cronjob_weekdays=='1'){echo "selected='true'"; }?>> Monday
						</option><option value="2" <?php if ($cronjob_weekdays=='2'){echo "selected='true'"; }?>> Tuesday
						</option><option value="3" <?php if ($cronjob_weekdays=='3'){echo "selected='true'"; }?>> Wednesday
						</option><option value="4" <?php if ($cronjob_weekdays=='4'){echo "selected='true'"; }?>> Thursday
						</option><option value="5" <?php if ($cronjob_weekdays=='5'){echo "selected='true'"; }?>> Friday
						</option><option value="6" <?php if ($cronjob_weekdays=='6'){echo "selected='true'"; }?>> Saturday
						</option>
					</select>
				</td>
			</tr>
			 <tr>
				<td colspan=2 align=left valign=top style="font-size:13px; width:180px;">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="If this option is toggled to on, every time BlogSense publishes a post it will randomize the post-time for the date it is scheduled to be published For example, if you select to have all posts published at one time, each post-time will be randomized for that day. ">
					Randomize Post Times?<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='cron_randomize' type=radio value='1' <?php if ($cronjob_randomize=="1") {echo "checked='checked'";}  ?>>on &nbsp; <input name='cron_randomize' type=radio value='0'  <?php if ($cronjob_randomize=="0") {echo "checked='checked'";} ?>> off
				</td>
			</tr>
			 <tr>
				<td  colspan=2 align=left valign=top style="font-size:13px; width:180px;">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="BlogSense will choose an hour inbetween these two paramaers. Acceptable paramaters are 1-24. Minutes are always randomly generated. CURRENT WORDPRESS TIME: <?php echo $wordpress_date_time; ?>">
					Hour Paramaters (For Randomized Post Times)<br>
				</td>
				<td align=right style="font-size:13px;">
					Min: <input size=1 name='cron_randomize_min' value='<?php echo $cronjob_randomize_min; ?>'> &nbsp;&nbsp;Max: <input size=1 name='cron_randomize_max' value='<?php echo $cronjob_randomize_max; ?>'>
				</td>
			</tr>
			<tr>
				<td  colspan=2 align=left valign=top style="font-size:13px; width:180px;">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="When looping through a campaign's items, a time-delay will be initiated between each sourced item. It's best to keep this low as increasing it can cause cronjobs running many campaigns to take a very long time to complete, but at the same time, increasing it may give you servers PHP and MYSQL systems a chance to cool down. 1000000 = 1 second.  ">
					Item Delay Time Buffer (in milliseconds)<br>
				</td>
				<td align=right style="font-size:13px;">
				  <input size=7 name='cron_buffer_items' value='<?php echo $cronjob_buffer_items; ?>'> 
				</td>
			</tr>
			<tr>
				<td  colspan=2 align=left valign=top style="font-size:13px; width:180px;">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="When running all campaigns at once, a time-delay will be initiated between each campaign so as to give the PHP/MYSQL systems a small break. It's best to keep this low as increasing it can cause cronjobs that contain many campaigns to take a very long time to complete, but at the same time, increasing it may give you servers PHP and MYSQL systems a chance to cool down. 1000000 = 1 second.  ">
					Campaign Delay Time Buffer (in milliseconds)<br>
				</td>
				<td align=right style="font-size:13px;">
				  <input size=7 name='cron_buffer_campaigns' value='<?php echo $cronjob_buffer_campaigns; ?>'> 
				</td>
			</tr>
			<tr>
				<td  colspan=2 align=left valign=top style="font-size:13px; width:180px;">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="Note: Default setting is usually the best. Running campaigns can take a long time, especially if you have set high delays inbetween campaign and campaign items. Within belogsense we manually set a timeout limit to prevent php's engine from quitting the script prematurely. We can toggle this default setting here. ">
					Script Timeout Limit (in seconds)<br>
				</td>
				<td align=right style="font-size:13px;">
				  <input size=7 name='cron_timeout' value='<?php echo $cronjob_timeout; ?>'> 
				</td>
			</tr>
		</table> 
		<br>
		
		<div class="class_section_header"><div class='tab'>Spin Settings</div></div>
		<table class="campaign"> 
			<tr>
				<td colspan=2 align=left valign=top style="font-size:13px;">
					<i>BlogSense has it's own built in internal spinner that uses an extensive match-phrase and synonym database. 
					The setting below allows you to select the scope that BlogSense spins it's content. If you set the limits to 1-5, then single words to 5 word phrase are spun, maximizing your spin percentage, but potentially sacrificing a measure of readability. 2-4 will improve readability by ignoring single words and only considering word groupings of 2-to-5 word phrases, but will reduce the spin percentage.<br><br>
				</td>
			</tr>
			<tr>
				<td  align=left valign=top style="font-size:13px; width:250px;">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="BlogSense uses a custom spin api that searches through text for phrases that can be replaced and selects alternatives at random. To do this BlogSense contains an internal database of 15,000+ phrases and synonyms. With the options below you can choose how thorough you would like to spin the content. If you choose 1 for minimum, BlogSense will replace single words with synonyms. If you choose 2 for minumum, then BlogSense will only replace phrases with a minumum of two words, and ignore single words. There is no current reason to set maximum higher than 5 word phrases.">
					Word-Phrase Settings<br>
				</td>
				<td align=right style="font-size:13px;">
					Minimum: <input size=1 name='spin_phrase_min' value='<?php echo $spin_phrase_min; ?>'> &nbsp;&nbsp;Maximum: <input size=1 name='spin_phrase_max' value='<?php echo $spin_phrase_max; ?>'>
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="One per line. BlogSense's spinner will ingore these words.">
					Prevent these keyphrases/words from being spun.<br>
				</td>
				<td align=right style="font-size:13px;">
					<textarea  name='spin_exclude_these' cols=29 rows=8 ><?php echo $spin_exclude_these; ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan=2 align=left valign=top style="font-size:13px;">
					<br>
					<b>The Best Spinner</b><br>
					<i>TBS is currently the most popular spinning service available and offers an API to their customers. Customers are currently limited to 250 calls per day.</i><br><br>
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					<a href='http://www.hatnohat.com/blogsense-wp/the-best-spinner/' target='_blank'> 
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="Click here head over to The Best Spinner homepage to signup."></a>
					Activate The Best Spinner API?:<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='tbs_spinning' type=radio value='1' <?php if ($tbs_spinning=="1") {echo "checked='checked'";}  ?>>on &nbsp; <input name='tbs_spinning' type=radio value='0'  <?php if ($tbs_spinning=="0") {echo "checked='checked'";} ?>> off
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					<a href='http://www.hatnohat.com/blogsense-wp/the-best-spinner/' target='_blank'> 
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="Your TBS Email Address"></a>
					TBS username:<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='tbs_username' size=20 value='<?php echo $tbs_username; ?>'>
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					<a href='http://www.hatnohat.com/blogsense-wp/the-best-spinner/' target='_blank'> 
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="Click here to sign up. TBS Password here."></a>
					TBS Password:<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='tbs_password' size=20 value='<?php echo $tbs_password; ?>'>
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					<a href='http://www.hatnohat.com/blogsense-wp/the-best-spinner/' target='_blank'> 
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="This will determine the readability/quality of the spinning and will also affect the % of spin variation from the original text. There is a good chance that selecting the highest quality will yield less than a 10% variation."></a>
					TBS Quality Control:<br>
				</td>
				<td align=right style="font-size:13px;">
					 <select name='tbs_quality'>
						<option value='1' <?php if ($tbs_quality=="1") {echo "selected=true";}  ?>>Good</option>
						<option value='2' <?php if ($tbs_quality=="2") {echo "selected=true";}  ?>>Better</option>
						<option value='3' <?php if ($tbs_quality=="3") {echo "selected=true";}  ?>>Best</option>
					</select></td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					<a href='http://www.hatnohat.com/blogsense-wp/the-best-spinner/' target='_blank'> 
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="This option will limit the number of replacement words/phrases that the TBS api selects randomly from. There is little reason to change this setting."></a>
					Max Replacement Alternatives:<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='tbs_maxsyns' size=1 value='<?php echo $tbs_maxsyns; ?>'>
				</td>
			</tr>
			<tr>
				<td colspan=2>
				</td>
			</tr>
		</table> 		
		<br>
		
		<br>
		
		<div class="class_section_header">
		<img src="nav/tip.png" style="cursor:pointer;" border=0 title="These urls will be ignored when sourcing from campaigns. One per line please."> 
		<div class='tab' style='display:inline'>Blocked Items List</div></div>
		<table id="blocked_urls" class=campaign> 
			<tr>
				<td colspan=2 align=left valign=top style="font-size:13px;">
					<i>Blocked items will not be allowed to be sourced through the BlogSense system. You can manually add urls here (1 per line) or you can automatically block items from within the campaign preview mode.<br><br>
				</td>
			</tr>
		  <tr>
			 <td  align=middle valign=top style="font-size:13px;">
				<textarea name="blocked_urls" style="width:100%;height:300px;max-width:500px;" wrap="off"><?php echo $blocked_urls; ?></textarea>
			</td>
		  </tr>		  	 
		</table> 
		<br>
		<br>
		
		<div class="class_section_header">
		<div class='tab' style='display:inline'>Proxy Settings</div></div>
		<table id="proxy_list" class=campaign> 
			<tr>
				<td colspan=2 align=left valign=top style="font-size:13px;">
					<i>BlogSense offers the use of proxies. It is reccomended to leave this feature off unless you are certain of what you are doing as proxy use can decrease performance. On some shared hosts, outgoing traffic on specific ports are blocked. For this reason some private proxies, including proxies from the Bonanza proxy service, may not work and cause a connection to fail. Consulting with hosting provider may be required. <br><br>
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					<a href='http://www.proxybonanza.com/?aff_id=85&aff_sub_id=blogsense-proxy' target='_blank'> <img src="nav/tip.png" style="cursor:pointer;" border=0 title="Click here to sign up."></a>
					Proxy Bonanza Username:<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='proxy_bonanza_username' size=28 value='<?php echo $proxy_bonanza_username; ?>'>
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					<a href='http://www.proxybonanza.com/?aff_id=85&aff_sub_id=blogsense-proxy' target='_blank'> <img src="nav/tip.png" style="cursor:pointer;" border=0 title="Click here to sign up. This field will contain the password required to use your proxies."></a>
					Proxy Bonanza Password:<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='proxy_bonanza_password' size=28 value='<?php echo $proxy_bonanza_password; ?>'>
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					<a href='http://www.proxybonanza.com/?aff_id=85&aff_sub_id=blogsense-proxy' target='_blank'> <img src="nav/tip.png" style="cursor:pointer;" border=0 title="Click here to sign up."></a>
					Proxy Bonanza Email:<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='proxy_bonanza_email' size=28 value='<?php echo $proxy_bonanza_email; ?>'>
				</td>
			</tr>
			<tr>
				<td colspan=2>
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px; ">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="Will only affect RSS & Source Campaigns.">
					Use Proxies with Campaigns?<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='proxy_campaigns' type=radio value='1' <?php if ($proxy_campaigns=="1") {echo "checked='checked'";}  ?>>on &nbsp; <input name='proxy_campaigns' type=radio value='0'  <?php if ($proxy_campaigns=="0") {echo "checked='checked'";} ?>> off
				</td>
			</tr>
			<tr>
				<td align=left valign=top style="font-size:13px;">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="Affects all bookmarking service connections.">
					Use Proxies with Bookmarking?<br>
				</td>
				<td align=right style="font-size:13px;">
					<input  name='proxy_bookmarking' type=radio value='1' <?php if ($proxy_bookmarking=="1") {echo "checked='checked'";}  ?>>on &nbsp; <input name='proxy_bookmarking' type=radio value='0'  <?php if ($proxy_bookmarking=="0") {echo "checked='checked'";} ?>> off
				</td>
			</tr>
			<tr>
				<td  align=left valign=top style="font-size:13px;">
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="Proxies come in two types, HTTP proxies and SOCK5 proxies. We must select the type we are using here so BlogSense knows the propper way to connect through them.">
					Proxy Type:
				</td>
				<td align=right valign=top style="font-size:13px;">
			        <select name='proxy_type'>
						<option value='http' <?php if ($proxy_type=="http") {echo "selected=true";}  ?>>HTTP</option>
						<option value='socks5' <?php if ($proxy_type=="socks5") {echo "selected=true";}  ?>>SOCK5</option>
					</select>
				</td>
			</tr>	
		    <tr>
				 <td colspan=2  align=left valign=top style="font-size:13px;">
					<BR>
					<img src="nav/tip.png" style="cursor:pointer;" border=0 title="One per line. Acceptable formats are: 177.255.266.119:60099:username:password , 177.255.266.119:60099 , tocnar-norton.cust.smartspb.net:80.">
					<a href='includes/i_test_proxy.php'' target='_blank'><img src="nav/link.gif" style="cursor:pointer;" border=0 title="Click here to test a random proxy."></a>
					
					Proxy List
					<textarea name="proxy_list" style="width:100%;height:300px;max-width:500px;" wrap="off"><?php echo $proxy_list; ?></textarea>
				</td>
			</tr>		  	 
		</table> 
		
		</div>
		
		<div id=rss_nodes>
		<a name="rss_module"></a>
		
		<div class="class_section_header">RSS Campaigns
			<div style='display:inline;float:right;'>
				<a href="./functions/f_add_campaign.php?module_type=rss" class="add_rss" title="Start Campaign Wizard" style="">
				<img src="nav/add.png" style="cursor:pointer;" border=0></a>&nbsp;&nbsp;
				<a href="./functions/f_global_edit.php" class="add_rss" title="Global Edit Mode" style=""  onClick="return confirm('Would you like to open the global campaign editing module?')">
				<img src="nav/global_edit.png" style="cursor:pointer;" border=0></a>
			</div>
		</div>
		<hr style="width:530px;color:#eeeeee;background-color:#eeeeee;">
		<table class=campaign> 
		  <tr>
			 <td  align=left valign=top style="font-size:13px;">
				Activate Module:<br> </td>
			 <td align=right style="font-size:13px;">
			    <input  name='rss_module' type=radio value=1 <?php if ($rss_module=="1") {echo "checked='checked'";}  ?>> on &nbsp; <input name='rss_module' type=radio value=0  <?php if ($rss_module=="0") {echo "checked='checked'";}  ?>> off
			</td>
		  </tr>	 		  
		</table> 
		
		<br>
		
		<div id=rss_nodes_new></div>
		
		<?php
			if ($campaign_id)
			{
				foreach ($campaign_id as $k => $v)
				{
				   if ($campaign_type[$k]=='rss')
				   {
				       ?>					
						<table class='campaign'> 
						  <tr>
							<td colspan=2 align=left style="font-size:13px;">
								<input type=hidden name='campaign[]' value='<?php echo $campaign_id[$k]; ?>'>
								<img  onClick='$(this).parent().parent().parent().parent().remove();' src='nav/remove.png' align=top style='cursor:pointer;' >&nbsp;&nbsp;
								<a href="./functions/f_add_campaign.php?module_type=rss&edit=1&id=<?php echo $campaign_id[$k]; ?>" class="add_rss"><img src="nav/edit.png" border=0 title="Edit this campaign"></a>&nbsp;&nbsp;
								<a id='id_copy_campaign_rss_<?php echo $campaign_id[$k]; ?>' class="copy_campaign" style='cursor:pointer'><img src="nav/copy.png" border=0 title="Copy this campaign"></a>&nbsp;&nbsp;
								<a href="preview.php?mode=rss&id=<?php echo $campaign_id[$k]; ?>" target='_blank'><img src="nav/preview.gif" border=0 title="Preview Mode. Preview how this campaign will behave."></a>&nbsp;
								<a href="./functions/f_add_campaign.php?module_type=rss&import=1&id=<?php echo $campaign_id[$k]; ?>" class="import_campaign"><img src="nav/import.png" border=0 title="Direct Import Mode. Moderate and import items from this campaigns." ></a>&nbsp;
							    <a href="solo_run.php?mode=rss&id=<?php echo $campaign_id[$k]; ?>" target='_blank' onClick="return confirm('Are you sure you want to run this campaign?')"><img src="nav/solorun.png" border=0 title="Solo Run. Run this campaign." ></a>&nbsp;
							    
								<div style='float:right;'>
									<select name='campaign_status_<?php echo $campaign_id[$k]; ?>'>
									 <?php
										if ($campaign_status[$k]==0)
										{
											echo "<option value=1 id='s_active'>Active Campaign</option>";
											echo "<option value=0 id='s_inactive' selected=true>Inactive Campaign</option>";
										}
										if ($campaign_status[$k]==1)
										{
											echo "<option value=1 id='s_active' selected=true>Active Campaign</option>";
											echo "<option value=0 id='s_inactive' >Inactive Campaign</option>";
										}
									?>
									</select>						
									<a href="./includes/i_generate_rss.php?id=<?php echo $campaign_id[$k]; ?>&summary=0&limit=50" target='_blank' onClick="return confirm('Generate RSS Feed?')"><img src="nav/rss.png" align='right'border=0 title="Generate RSS feed of campaign's published items." ></a>&nbsp;
								
								</div>
								
								</td>
							
						  </tr>
						  <tr>
							 <td  align=left valign=top style="font-size:13px; width:300px;">
									 Campaign Name:<br>
							 </td>
							 <td align=right style="font-size:13px;">
								 <input name='campaign_name_<?php echo $campaign_id[$k]; ?>'size=60 value='<?php echo $campaign_name[$k]; ?>'>			
							 </td>
						  </tr>
						  <tr>
							 <td  align=left valign=top style="font-size:13px; width:300px;">
									 Campaign Feed:<br>
							 </td>
							 <td align=right style="font-size:13px;">
								 <input name='campaign_feed_<?php echo $campaign_id[$k]; ?>'size=60 value='<?php echo $campaign_feed[$k]; ?>'>			
							 </td>
						  </tr>
						  <tr>				  
						</table> 
						<br>
						<?php
					   
				   }
				}
			}
			?>
		</div>

		
		<div id=drop_nodes>
			<a name="drop_module"></a>
			
			<div class="class_section_header" style="width:530px">Drop Posting / CSV
				<div style="float:right;font-weight: 600; font-size:10px;">
				<a href="./functions/f_add_campaign.php?module_type=fileimport" class="add_drop" style="">
				<img src="nav/add.png" style="cursor:pointer;" border=0></a>&nbsp;&nbsp;
				<a href="./functions/f_global_edit.php" class="add_rss" title="Global Edit Mode" style=""  onClick="return confirm('Would you like to open the global campaign editing module?')">
				<img src="nav/global_edit.png" style="cursor:pointer;" border=0></a>
			</div>
			</div>
			<hr width="530" style="color:#eeeeee;background-color:#eeeeee;">
			
			<table class=campaign> 
				<tr>
					<td  align=left valign=top style="font-size:13px;">
					Activate Module:<br> 
					</td>
					<td align=right style="font-size:13px;">
						<input  name='drop_module' type=radio value=1 <?php if ($drop_module=="1") {echo "checked='checked'";}  ?>> on &nbsp; <input name='drop_module' type=radio value=0  <?php if ($drop_module=="0") {echo "checked='checked'";}  ?>> off
					</td>
				</tr>	 		  
			</table> 
			
			<br>
			<div id=drop_nodes_new></div>
			<?php
			if ($campaign_id)
			{
				foreach ($campaign_id as $k => $v)
				{
				   if ($campaign_type[$k]=='drop'||$campaign_type[$k]=='fileimport')
				   {
				       ?>					
						<table class='campaign'> 
						  <tr>
							<td colspan=2 align=left style="font-size:13px;">
								<input type=hidden name='campaign[]' value='<?php echo $campaign_id[$k]; ?>'>
								<img  onClick='$(this).parent().parent().parent().parent().remove();' src='nav/remove.png' align=top style='cursor:pointer;' >&nbsp;&nbsp;
								<a href="./functions/f_add_campaign.php?module_type=fileimport&edit=1&id=<?php echo $campaign_id[$k]; ?>" class="add_drop"><img src="nav/edit.png" border=0 title="Edit this campaign"></a>&nbsp;&nbsp;
								<a id='id_copy_campaign_drop_<?php echo $campaign_id[$k]; ?>' class="copy_campaign" style='cursor:pointer'><img src="nav/copy.png" border=0 title="Copy this campaign"></a>&nbsp;&nbsp;
								<a href="preview.php?mode=fileimport&id=<?php echo $campaign_id[$k]; ?>" target='_blank'><img src="nav/preview.gif" border=0 title="Preview Mode. Preview how this campaign will behave."></a>&nbsp;
								<a href="./functions/f_add_campaign.php?module_type=fileimport&import=1&id=<?php echo $campaign_id[$k]; ?>" class="import_campaign"><img src="nav/import.png" border=0 title="Direct Import Mode. Moderate and import items from this campaigns." ></a>&nbsp;
							    <a href="solo_run.php?mode=fileimport&id=<?php echo $campaign_id[$k]; ?>" target='_blank' onClick="return confirm('Are you sure you want to run this campaign?')"><img src="nav/solorun.png" border=0 title="Solo Run. Run this campaign." ></a>&nbsp;
							    
								<div style='float:right;'>
									<select name='campaign_status_<?php echo $campaign_id[$k]; ?>'>
									 <?php
										if ($campaign_status[$k]==0)
										{
											echo "<option value=1 id='s_active'>Active Campaign</option>";
											echo "<option value=0 id='s_inactive' selected=true>Inactive Campaign</option>";
										}
										if ($campaign_status[$k]==1)
										{
											echo "<option value=1 id='s_active' selected=true>Active Campaign</option>";
											echo "<option value=0 id='s_inactive' >Inactive Campaign</option>";
										}
									?>
									</select>
							
									<a href="./includes/i_generate_rss.php?id=<?php echo $campaign_id[$k]; ?>&summary=0&limit=50" target='_blank' onClick="return confirm('Generate RSS Feed?')"><img src="nav/rss.png" align='right'border=0 title="Generate RSS feed of campaign's published items." ></a>&nbsp;
								
								</div>
								<br>
								<div style='float:right;'>
										<i><?php  if ($campaign_name[$k]=='text_import'){ echo "[Articles] /my-articles/";} ELSE {echo "[CSV] /my-csv-files/";} ?><?php echo $campaign_source[$k]; ?></i><br>
								</div>
								</td>
							
						  </tr>			  
						</table> 
						<br>
						<?php
					   
				   }
				}
			}
			?>
		
	
		</div>
		
		

		

		<div id=sources_nodes>
			<a name="sources_module"></a>
			<?php
			 $marker = count($sources_queries)-1;			   
			?>
			<div class="class_section_header">Source Campaigns
				<div style='float:right'>
				<a href="./functions/f_mass_create.php?module_type=sources" class="add_sources" title="Start Mass Campaign Creation Wizard"><img src="nav/mass_create.png" style="cursor:pointer;" border=0></a> &nbsp;
				<a href="./functions/f_add_campaign.php?module_type=sources" class="add_sources" ><img src="nav/add.png" title='Start Campaign Wizard' style="cursor:pointer;" border=0></a>&nbsp;&nbsp;&nbsp;
				<a href="./functions/f_manage_sources.php" class=manage_sources><img src='./nav/tool.gif' border=0 title='Manage/Add Content Sources'></a>&nbsp;&nbsp;
				<a href="./functions/f_global_edit.php" class="add_rss" title="Global Edit Mode" style=""  onClick="return confirm('Would you like to open the global campaign editing module?')">
				<img src="nav/global_edit.png" style="cursor:pointer;" border=0></a>
				</div>
				
			</div>
			<hr style="width:530px;color:#eeeeee;background-color:#eeeeee;">
			<table class=campaign> 
			  <tr>
				 <td  align=left valign=top style="font-size:13px;">
					Activate Module:<br> </td>
				 <td align=right style="font-size:13px;">
					<input  name='sources_module' type=radio value=1 <?php if ($sources_module=="1") {echo "checked='checked'";}  ?>> on &nbsp; <input name='sources_module' type=radio value=0  <?php if ($sources_module=="0") {echo "checked='checked'";}  ?>> off
				</td>
			  </tr>	 		  
			</table> 
			
			<br>
			<div id=sources_nodes_new></div>
			
			<?php
				if ($campaign_id)
				{
					foreach ($campaign_id as $k => $v)
					{
					   if ($campaign_type[$k]=='sources')
					   {
						   ?>					
							<table class='campaign'> 
							  <tr>
								<td colspan=2 align=left style="font-size:13px;">
									<input type=hidden name='campaign[]' value='<?php echo $campaign_id[$k]; ?>'>
									<img  onClick='$(this).parent().parent().parent().parent().remove();' src='nav/remove.png' align=top style='cursor:pointer;' >&nbsp;&nbsp;
									<a href="./functions/f_add_campaign.php?module_type=sources&edit=1&id=<?php echo $campaign_id[$k]; ?>" class="add_sources"><img src="nav/edit.png" border=0 title="Edit this campaign"></a>&nbsp;&nbsp;
									<a id='id_copy_campaign_sources_<?php echo $campaign_id[$k]; ?>' class="copy_campaign" style='cursor:pointer' ><img src="nav/copy.png" border=0 title="Copy this campaign"></a>&nbsp;&nbsp;
									<a href="preview.php?mode=sources&id=<?php echo $campaign_id[$k]; ?>" target='_blank'><img src="nav/preview.gif" border=0 title="Preview Mode. Preview how this campaign will behave."></a>&nbsp;&nbsp;
									<a href="./functions/f_add_campaign.php?module_type=sources&import=1&id=<?php echo $campaign_id[$k]; ?>" class="import_campaign"><img src="nav/import.png" border=0 title="Direct Import Mode. Moderate and import items from this campaigns." ></a>&nbsp;
									<a href="solo_run.php?mode=sources&id=<?php echo $campaign_id[$k]; ?>" target='_blank' onClick="return confirm('Are you sure you want to run this campaign?')"><img src="nav/solorun.png" border=0 title="Solo Run. Run this campaign." ></a>
									
									<div style='float:right;'>
										<select name='campaign_status_<?php echo $campaign_id[$k]; ?>'>
										 <?php
											if ($campaign_status[$k]==0)
											{
												echo "<option value=1 id='s_active'>Active Campaign</option>";
												echo "<option value=0 id='s_inactive' selected=true>Inactive Campaign</option>";
											}
											if ($campaign_status[$k]==1)
											{
												echo "<option value=1 id='s_active' selected=true>Active Campaign</option>";
												echo "<option value=0 id='s_inactive' >Inactive Campaign</option>";
											}
										?>
										</select>
								
										<a href="./includes/i_generate_rss.php?id=<?php echo $campaign_id[$k]; ?>&summary=0&limit=50" target='_blank' onClick="return confirm('Generate RSS Feed?')"><img src="nav/rss.png" align='right'border=0 title="Generate RSS feed of campaign's published items." ></a>&nbsp;
									
									</div>
									</td>
								
							  </tr>
							  <tr>
								 <td  align=left valign=top style="font-size:13px; width:300px;">
										 Campaign Name:<br>
								 </td>
								 <td align=right style="font-size:13px;">
									 <input name='campaign_name_<?php echo $campaign_id[$k]; ?>'size=60 value='<?php echo $campaign_name[$k]; ?>'>			
								 </td>
							  </tr>
							  <tr>
								 <td  align=left valign=top style="font-size:13px; width:300px;">
										 Campaign Source:<br>
								 </td>
								 <td align=left style="font-size:13px;">
									 <input type='hidden' name='campaign_feed_<?php echo $campaign_id[$k]; ?>'size=60 value='<?php echo $campaign_feed[$k]; ?>'>			
									<?php echo $campaign_source[$k]; ?>
								</td>
							  </tr>
							  <tr>				  
							</table> 
							<br>
							<?php
						   
					   }
					}
				}
				?>
			
		</div>
		
		
		
		
		
		<div id=yahoo_nodes>
		<a name="yahoo_answers"></a>
		<?php					
		$cat_select_html = array();		
		$marker = count($yahoo_feeds)-1;
		?>
		
	    <div class="class_section_header">Yahoo Answer Campaings 
			<div style='display:inline;float:right;'>
				<a href="./functions/f_mass_create.php?module_type=yahoo" class="add_yahoo" title="Mass Campaign Creation Wizard"><img src="nav/mass_create.png" style="cursor:pointer;" border=0></a> &nbsp;
				<a href="./functions/f_add_campaign.php?module_type=yahoo" class="add_yahoo" title="Yahoo Answers Campaign Wizard"><img src="nav/add.png" style="cursor:pointer;" border=0></a>&nbsp;&nbsp;
				<a href="./functions/f_global_edit.php" class="add_rss" title="Global Edit Mode" style=""  onClick="return confirm('Would you like to open the global campaign editing module?')">
				<img src="nav/global_edit.png" style="cursor:pointer;" border=0></a>
			</div>
		</div>
		<hr width="530" style="color:#eeeeee;background-color:#eeeeee;">
		<table width="530" style="margin-left:auto;margin-right:auto;padding:5px; border: solid 5px #eeeeee"> 
		  <tr>
			 <td  align=left valign=top style="font-size:13px;">
				Activate Module:<br> </td>
			 <td align=right style="font-size:13px;">
			     <input  name='yahoo_module' type=radio value=1 <?php if ($yahoo_module=="1") {echo "checked='checked'";}  ?>> on &nbsp; <input name='yahoo_module' type=radio value=0  <?php if ($yahoo_module=="0") {echo "checked='checked'";}  ?>> off
				  
			 </td>
		  </tr>	
		   <tr>
				 <td  align=left valign=top style="font-size:13px;">
					<a target='_blank' href="http://developer.apps.yahoo.com"><img src="./nav/tip.png" style="cursor:pointer;" border=0 title="REQUIRED: You must have an Yahoo Answers API Key to run this module."></a>
					 Yahoo API Key:<br> </td>
				 <td align=right style="font-size:13px;">
					<input  name="yahoo_api_key" size=30 value='<?php echo $yahoo_api_key; ?>'>
				</td>
			  </tr>
		</table> 
		<br><br>
		<?php
				if ($campaign_id)
				{
					foreach ($campaign_id as $k => $v)
					{
					   if ($campaign_type[$k]=='yahoo')
					   {
						   ?>					
							<table class='campaign'> 
							  <tr>
								<td colspan=2 align=left style="font-size:13px;">
									<input type=hidden name='campaign[]' value='<?php echo $campaign_id[$k]; ?>'>
									<img  onClick='$(this).parent().parent().parent().parent().remove();' src='nav/remove.png' align=top style='cursor:pointer;' >&nbsp;&nbsp;
									<a href="./functions/f_add_campaign.php?module_type=yahoo&edit=1&id=<?php echo $campaign_id[$k]; ?>" class="add_yahoo"><img src="nav/edit.png" border=0 title="Edit this campaign"></a>&nbsp;&nbsp;
									<a id='id_copy_campaign_yahoo_<?php echo $campaign_id[$k]; ?>' class="copy_campaign" style='cursor:pointer' ><img src="nav/copy.png" border=0 title="Copy this campaign"></a>&nbsp;&nbsp;
									<a href="preview.php?mode=yahoo&id=<?php echo $campaign_id[$k]; ?>" target='_blank'><img src="nav/preview.gif" border=0 title="Preview Mode. Preview how this campaign will behave."></a>&nbsp;&nbsp;
									<a href="./functions/f_add_campaign.php?module_type=yahoo&import=1&id=<?php echo $campaign_id[$k]; ?>" class="import_campaign"><img src="nav/import.png" border=0 title="Direct Import Mode. Moderate and import items from this campaigns." ></a>&nbsp;
									<a href="solo_run.php?mode=yahoo&id=<?php echo $campaign_id[$k]; ?>" target='_blank' onClick="return confirm('Are you sure you want to run this campaign?')"><img src="nav/solorun.png" border=0 title="Solo Run. Run this campaign." ></a>
									
									<div style='float:right;'>
										<select name='campaign_status_<?php echo $campaign_id[$k]; ?>'>
										 <?php
											if ($campaign_status[$k]==0)
											{
												echo "<option value=1 id='s_active'>Active Campaign</option>";
												echo "<option value=0 id='s_inactive' selected=true>Inactive Campaign</option>";
											}
											if ($campaign_status[$k]==1)
											{
												echo "<option value=1 id='s_active' selected=true>Active Campaign</option>";
												echo "<option value=0 id='s_inactive' >Inactive Campaign</option>";
											}
										?>
										</select>	
										
										<a href="./includes/i_generate_rss.php?id=<?php echo $campaign_id[$k]; ?>&summary=0&limit=50" target='_blank' onClick="return confirm('Generate RSS Feed?')"><img src="nav/rss.png" align='right'border=0 title="Generate RSS feed of campaign's published items." ></a>&nbsp;
									
									</div>
									</td>
								
							  </tr>
							  <tr>
								 <td  align=left valign=top style="font-size:13px; width:300px;">
										 Campaign Name:<br>
								 </td>
								 <td align=right style="font-size:13px;">
									 <input name='campaign_name_<?php echo $campaign_id[$k]; ?>'size=60 value='<?php echo $campaign_name[$k]; ?>'>			
								 </td>
							  </tr>
							  <tr>
								 <td  align=left valign=top style="font-size:13px; width:300px;">
										 Campaign Feed:<br>
								 </td>
								 <td align=left style="font-size:13px;">
									 <input type='hidden' name='campaign_feed_<?php echo $campaign_id[$k]; ?>'size=60 value='<?php echo $campaign_feed[$k]; ?>'>			
									<?php echo $campaign_feed[$k]; ?>
								</td>
							  </tr>
							  <tr>				  
							</table> 
							<br>
							<?php
						   
					   }
					}
				}
				?>
		</div>
		
		<div id=amazon_nodes>
			<a name="amazon_module"></a>
			
			<div class="class_section_header">Amazon Campaigns
				<div style='display:inline;float:right;'>
					<a href="./functions/f_add_campaign.php?module_type=amazon" class="add_amazon" title="Amazon Campaign Wizard" style="margin-left: 95px;">
					<img src="nav/add.png" style="cursor:pointer;" border=0></a>&nbsp;&nbsp;
					<a href="./functions/f_global_edit.php" class="add_rss" title="Global Edit Mode" style=""  onClick="return confirm('Would you like to open the global campaign editing module?')">
					<img src="nav/global_edit.png" style="cursor:pointer;" border=0></a>
				</div>
			</div>
			
			<hr width="530" style="color:#eeeeee;background-color:#eeeeee;">
			<table width="530" style="margin-left:auto;margin-right:auto;padding:5px; border: solid 5px #eeeeee"> 
			  <tr>
				 <td  align=left valign=top style="font-size:13px;">
					<img src="./nav/tip.png" style="cursor:pointer;" border=0 title="Toggling this module off will prevent all campaigns from running automatically.">
					Activate Module:<br> </td>
				 <td align=right style="font-size:13px;">
					<input  name='amazon_module' type=radio value=1 <?php if ($amazon_module=="1") {echo "checked='checked'";}  ?>> on &nbsp; <input name='amazon_module' type=radio value=0  <?php if ($amazon_module=="0") {echo "checked='checked'";}  ?>> off
				</td>
			  </tr>
			  <tr>
				 <td  align=left valign=top style="font-size:13px;">
					<a target='_blank' href="http://www.blogsense-wp.com/news/how-to-get-an-amazon-access-key-and-a-amazon-secret-key/"><img src="./nav/tip.png" style="cursor:pointer;" border=0 title="REQUIRED: You must have an Amazon Associates ID to run this module."></a>
					 Affiliate ID:<br> </td>
				 <td align=right style="font-size:13px;">
					<input  name="amazon_affiliate_id" size=30 value='<?php echo $amazon_affiliate_id; ?>'>
				</td>
			  </tr>
			  <tr>
				 <td  align=left valign=top style="font-size:13px;">
					<a target='_blank' href="http://www.blogsense-wp.com/news/how-to-get-an-amazon-access-key-and-a-amazon-secret-key/"><img src="./nav/tip.png" style="cursor:pointer;" border=0 title="REQUIRED: Click this question mark for information on how to obtain these keys."></a>
					 AWS Access Key:<br> </td>
				 <td align=right style="font-size:13px;">
					<input  name="amazon_aws_access_key" size=30 value='<?php echo $amazon_aws_access_key; ?>'>
				</td>
			  </tr>
			  <tr>
				 <td  align=left valign=top style="font-size:13px;">
					<a target='_blank' href="http://www.blogsense-wp.com/news/how-to-get-an-amazon-access-key-and-a-amazon-secret-key/"><img src="./nav/tip.png" style="cursor:pointer;" border=0 title="REQUIRED: Click this question mark for information on how to obtain these keys."></a>
					 Secret Access Key:<br> </td>
				 <td align=right style="font-size:13px;">
					<input  name="amazon_secret_access_key" size=30 value='<?php echo $amazon_secret_access_key; ?>'>
				</td>
			  </tr>	 	
			</table> 
		
		<br>
		
		<?php
			if ($campaign_id)
			{
				foreach ($campaign_id as $k => $v)
				{
				   if ($campaign_type[$k]=='amazon')
				   {
				       ?>					
						<table class='campaign'> 
						  <tr>
							<td colspan=2 align=left style="font-size:13px;">
								<input type=hidden name='campaign[]' value='<?php echo $campaign_id[$k]; ?>'>
								<img  onClick='$(this).parent().parent().parent().parent().remove();' src='nav/remove.png' align=top style='cursor:pointer;' >&nbsp;&nbsp;
								<a href="./functions/f_add_campaign.php?module_type=amazon&edit=1&id=<?php echo $campaign_id[$k]; ?>" class="add_amazon"><img src="nav/edit.png" border=0 title="Edit this campaign"></a>&nbsp;&nbsp;
								<a id='id_copy_campaign_amazon_<?php echo $campaign_id[$k]; ?>' class="copy_campaign" style='cursor:pointer' ><img src="nav/copy.png" border=0 title="Copy this campaign"></a>&nbsp;&nbsp;
								<a href="preview.php?mode=amazon&id=<?php echo $campaign_id[$k]; ?>" target='_blank'><img src="nav/preview.gif" border=0 title="Preview Mode. Preview how this campaign will behave."></a>&nbsp;
							    <a href="./functions/f_add_campaign.php?module_type=amazon&import=1&id=<?php echo $campaign_id[$k]; ?>" class="import_campaign"><img src="nav/import.png" border=0 title="Direct Import Mode. Moderate and import items from this campaigns." ></a>&nbsp;
							    <a href="solo_run.php?mode=amazon&id=<?php echo $campaign_id[$k]; ?>" target='_blank' onClick="return confirm('Are you sure you want to run this campaign?')"><img src="nav/solorun.png" border=0 title="Solo Run. Run this campaign." ></a>&nbsp;
							   
								<div style='float:right;'>
									<select name='campaign_status_<?php echo $campaign_id[$k]; ?>'>
									 <?php
										if ($campaign_status[$k]==0)
										{
											echo "<option value=1 id='s_active'>Active Campaign</option>";
											echo "<option value=0 id='s_inactive' selected=true>Inactive Campaign</option>";
										}
										if ($campaign_status[$k]==1)
										{
											echo "<option value=1 id='s_active' selected=true>Active Campaign</option>";
											echo "<option value=0 id='s_inactive' >Inactive Campaign</option>";
										}
									?>
									</select>
									
									<a href="./includes/i_generate_rss.php?id=<?php echo $campaign_id[$k]; ?>&summary=0&limit=50" target='_blank' onClick="return confirm('Generate RSS Feed?')"><img src="nav/rss.png" align='right'border=0 title="Generate RSS feed of campaign's published items." ></a>&nbsp;
									
								</div>
								</td>
							
						  </tr>
						  <tr>
							 <td  align=left valign=top style="font-size:13px; width:300px;">
									 Campaign Name:<br>
							 </td>
							 <td align=right style="font-size:13px;">
								 <input name='campaign_name_<?php echo $campaign_id[$k]; ?>'size=60 value='<?php echo $campaign_name[$k]; ?>'>			
							 </td>
						  </tr>
						  <tr>
							 <td  align=left valign=top style="font-size:13px; width:300px;">
									 Campaign Feed:<br>
							 </td>
							 <td align=right style="font-size:13px;">
								 <input name='campaign_feed_<?php echo $campaign_id[$k]; ?>'size=60 value='<?php echo $campaign_feed[$k]; ?>'>			
							 </td>
						  </tr>
						  <tr>				  
						</table> 
						<br>
						<?php
					   
				   }
				}
			}
			?>
		</div>
	
		
		<div id=video_nodes style="padding-bottom:10px;">
		<a name="video_module"></a>
		<?php
		$marker = count($video_feeds)-1;		
		?>
		<div class="class_section_header">Video Campaigns
			<div style='display:inline;float:right;'>
				<a href="./functions/f_mass_create.php?module_type=video" class="add_video" title="Mass Campaign Creation Wizard"><img src="nav/mass_create.png" style="cursor:pointer;" border=0></a> &nbsp;
				<a href="./functions/f_add_campaign.php?module_type=video" class="add_video" >
				<img src="nav/add.png" style="cursor:pointer;" border=0></a>&nbsp;&nbsp;
				<a href="./functions/f_global_edit.php" class="add_rss" title="Global Edit Mode" style=""  onClick="return confirm('Would you like to open the global campaign editing module?')">
				<img src="nav/global_edit.png" style="cursor:pointer;" border=0></a>
			</div>
		</div>
		<hr style="width:530px;color:#eeeeee;background-color:#eeeeee;">
		<table class=campaign> 
		  <tr>
			 <td  align=left valign=top style="font-size:13px;">
				Activate Module:<br> </td>
			 <td align=right style="font-size:13px;">
			    <input  name='video_module' type=radio value=1 <?php if ($video_module=="1") {echo "checked='checked'";}  ?>> on &nbsp; <input name='video_module' type=radio value=0  <?php if ($video_module=="0") {echo "checked='checked'";}  ?>> off
			</td>
		  </tr>	 		  
		</table> 
		
		<br>
		<div id=video_nodes_new></div>
		
		<?php
			if ($campaign_id)
			{
				foreach ($campaign_id as $k => $v)
				{
				   if ($campaign_type[$k]=='video')
				   {
				       ?>					
						<table class='campaign'> 
						  <tr>
							<td colspan=2 align=left style="font-size:13px;">
								<input type=hidden name='campaign[]' value='<?php echo $campaign_id[$k]; ?>'>
								<img  onClick='$(this).parent().parent().parent().parent().remove();' src='nav/remove.png' align=top style='cursor:pointer;' >&nbsp;&nbsp;
								<a href="./functions/f_add_campaign.php?module_type=video&edit=1&id=<?php echo $campaign_id[$k]; ?>" class="add_video"><img src="nav/edit.png" border=0 title="Edit this campaign"></a>&nbsp;&nbsp;
								<a id='id_copy_campaign_video_<?php echo $campaign_id[$k]; ?>' class="copy_campaign" style='cursor:pointer'><img src="nav/copy.png" border=0 title="Copy this campaign"></a>&nbsp;&nbsp;
								<a href="preview.php?mode=video&id=<?php echo $campaign_id[$k]; ?>" target='_blank'><img src="nav/preview.gif" border=0 title="Preview Mode. Preview how this campaign will behave."></a>&nbsp;&nbsp;
							    <a href="./functions/f_add_campaign.php?module_type=video&import=1&id=<?php echo $campaign_id[$k]; ?>" class="import_campaign"><img src="nav/import.png" border=0 title="Direct Import Mode. Moderate and import items from this campaigns." ></a>&nbsp;
								<a href="solo_run.php?mode=video&id=<?php echo $campaign_id[$k]; ?>" target='_blank' onClick="return confirm('Are you sure you want to run this campaign?')"><img src="nav/solorun.png" border=0 title="Solo Run. Run this campaign." ></a>
							   
								<div style='float:right;'>
									<select name='campaign_status_<?php echo $campaign_id[$k]; ?>'>
									 <?php
										if ($campaign_status[$k]==0)
										{
											echo "<option value=1 id='s_active'>Active Campaign</option>";
											echo "<option value=0 id='s_inactive' selected=true>Inactive Campaign</option>";
										}
										if ($campaign_status[$k]==1)
										{
											echo "<option value=1 id='s_active' selected=true>Active Campaign</option>";
											echo "<option value=0 id='s_inactive' >Inactive Campaign</option>";
										}
									?>
									</select>
									
									<a href="./includes/i_generate_rss.php?id=<?php echo $campaign_id[$k]; ?>&summary=0&limit=50" target='_blank' onClick="return confirm('Generate RSS Feed?')"><img src="nav/rss.png" align='right'border=0 title="Generate RSS feed of campaign's published items." ></a>&nbsp;
									
								</div>
								</td>
							
						  </tr>
						  <tr>
							 <td  align=left valign=top style="font-size:13px; width:300px;">
									 Campaign Name:<br>
							 </td>
							 <td align=right style="font-size:13px;">
								 <input name='campaign_name_<?php echo $campaign_id[$k]; ?>'size=60 value='<?php echo $campaign_name[$k]; ?>'>			
							 </td>
						  </tr>
						  <tr>
							 <td  align=left valign=top style="font-size:13px; width:300px;">
									 Campaign Feed:<br>
							 </td>
							 <td align=right style="font-size:13px;">
								 <input name='campaign_feed_<?php echo $campaign_id[$k]; ?>'size=60 value='<?php echo $campaign_feed[$k]; ?>'>			
							 </td>
						  </tr>
						  <tr>				  
						</table> 
						<br>
						<?php
					   
				   }
				}
			}
			?>
		</div>		
		
		
		<div id=keywords_nodes>
			<a name="keywords_module"></a>
			
			<div class="class_section_header">Keyword Driven Campaigns
				<div style='display:inline;float:right;'>
					<a href="./functions/f_add_campaign.php?module_type=keywords" class="add_keywords" title="Start Campaign Wizard" style="">
					<img src="nav/add.png" style="cursor:pointer;" border=0></a>&nbsp;&nbsp;
					<a href="./functions/f_global_edit.php" class="add_keywords" title="Global Edit Mode" style=""  onClick="return confirm('Would you like to open the global campaign editing module?')">
					<img src="nav/global_edit.png" style="cursor:pointer;" border=0></a>
				</div>
			</div>
			<hr style="width:530px;color:#eeeeee;background-color:#eeeeee;">
			<table class=campaign> 
			  <tr>
				 <td  align=left valign=top style="font-size:13px;">
					Activate Module:<br> </td>
				 <td align=right style="font-size:13px;">
					<input  name='keywords_module' type=radio value=1 <?php if ($keywords_module=="1") {echo "checked='checked'";}  ?>> on &nbsp; <input name='keywords_module' type=radio value=0  <?php if ($keywords_module=="0") {echo "checked='checked'";}  ?>> off
				</td>
			  </tr>	 		  
			</table> 
			
			<br>
			
			<div id=rss_nodes_new></div>
			
			<?php
				if ($campaign_id)
				{
					foreach ($campaign_id as $k => $v)
					{
					   if ($campaign_type[$k]=='keywords')
					   {
						   ?>					
							<table class='campaign'> 
							  <tr>
								<td colspan=2 align=left style="font-size:13px;">
									<input type=hidden name='campaign[]' value='<?php echo $campaign_id[$k]; ?>'>
									<img  onClick='$(this).parent().parent().parent().parent().remove();' src='nav/remove.png' align=top style='cursor:pointer;' >&nbsp;&nbsp;
									<a href="./functions/f_add_campaign.php?module_type=keywords&edit=1&id=<?php echo $campaign_id[$k]; ?>" class="add_keywords"><img src="nav/edit.png" border=0 title="Edit this campaign"></a>&nbsp;&nbsp;
									<a id='id_copy_campaign_keywords_<?php echo $campaign_id[$k]; ?>' class="copy_campaign" style='cursor:pointer'><img src="nav/copy.png" border=0 title="Copy this campaign"></a>&nbsp;&nbsp;
									<a href="preview.php?mode=keywords&id=<?php echo $campaign_id[$k]; ?>" target='_blank'><img src="nav/preview.gif" border=0 title="Preview Mode. Preview how this campaign will behave."></a>&nbsp;
									<a href="./functions/f_add_campaign.php?module_type=keywords&import=1&id=<?php echo $campaign_id[$k]; ?>" class="import_campaign"><img src="nav/import.png" border=0 title="Direct Import Mode. Moderate and import items from this campaigns." ></a>&nbsp;
									<a href="solo_run.php?mode=keywords&id=<?php echo $campaign_id[$k]; ?>" target='_blank' onClick="return confirm('Are you sure you want to run this campaign?')"><img src="nav/solorun.png" border=0 title="Solo Run. Run this campaign." ></a>&nbsp;
									
									<div style='float:right;'>
										<select name='campaign_status_<?php echo $campaign_id[$k]; ?>'>
										 <?php
											if ($campaign_status[$k]==0)
											{
												echo "<option value=1 id='s_active'>Active Campaign</option>";
												echo "<option value=0 id='s_inactive' selected=true>Inactive Campaign</option>";
											}
											if ($campaign_status[$k]==1)
											{
												echo "<option value=1 id='s_active' selected=true>Active Campaign</option>";
												echo "<option value=0 id='s_inactive' >Inactive Campaign</option>";
											}
										?>
										</select>						
										<a href="./includes/i_generate_rss.php?id=<?php echo $campaign_id[$k]; ?>&summary=0&limit=50" target='_blank' onClick="return confirm('Generate RSS Feed?')"><img src="nav/rss.png" align='right'border=0 title="Generate RSS feed of campaign's published items." ></a>&nbsp;
									
									</div>
									
									</td>
								
							  </tr>
							  <tr>
								 <td  align=left valign=top style="font-size:13px; width:300px;">
										 Campaign Name:<br>
								 </td>
								 <td align=right style="font-size:13px;">
									<input name='campaign_name_<?php echo $campaign_id[$k]; ?>'size=60 value='<?php echo $campaign_name[$k]; ?>'>			
								 </td>
							  </tr>
							  <tr>
								 <td  colspan=2 align='right'>
									<font style="font-size:9px"><i>(Toogle Keyword List)</i></font>
									<img src='nav/expand.gif' class='class_btn_expand_keywords_module_keywords' id='id_btn_expand_keywords_<?php echo $campaign_id[$k]; ?>' style='cursor:pointer;'>
									<div style='display:none;' class='class_keywords_hidden' id='id_div_keywords_module_keywords_<?php echo $campaign_id[$k]; ?>'>
									 <?php 
									 $campaign_query[$k] = str_replace(";","\n",$campaign_query[$k]);
									 echo "<textarea name='campaign_query_{$campaign_id[$k]}' style='width:100%;height:300px;'>{$campaign_query[$k]}</textarea>";
									 
									 ?>		
									 
								 </td>
							  </tr>
							  <tr>				  
							</table> 
							<br>
							<?php
						   
					   }
					}
				}
				?>
		</div>
		
		<div id='templates'>
			
			<a name="template_custom_variables"></a>
			
			<div class="class_section_header" style='width:800px'>Core Templates
			</div>
			<hr width="800" style="color:#eeeeee;background-color:#eeeeee;width:800px">
			<table>
			<tr>
				<td>
					<select id='id_select_core_templates' size='20'>
						<?php
							foreach ($template_id as $k=>$v)
							{
								if ($k==0)
								{
									$selected = 'selected="true"';
								}
								else
								{
									$selected = ''; 
								}
								echo "<option value='{$template_id[$k]}' $selected>{$template_name[$k]}</option>";
							}
						?>
					</select>
				</td>
				<td valign='top'>
					<?php
						if ($template_id)
						{	
							foreach ($template_id as $k=>$v)
							{
								$template_content[$k] = stripslashes($template_content[$k]);
								if ($k==0)
								{
									$display = 'block';
								}
								else
								{
									$display = 'none'; 
								}
							?>
								<table class='class_table_core_templates' id='id_table_core_template_<?php echo $template_id[$k] ?>' style="display:<?php echo $display; ?>;margin-left:auto;margin-right:auto;padding:5px;"> 
									<tr>
										<td align=right style="font-size:13px;">
											<?php echo $template_name[$k]; ?>
											<input name='template_id[]' type='hidden' value='<?php echo $template_id[$k]; ?>'>
										</td>
									</tr>
									<tr>
										 <td  align='right'  valign='top' style="font-size:13px;padding-top:5px;">
											<textarea cols=64 rows=17 name='template_content[]' wrap='off'><?php echo $template_content[$k]; ?></textarea>
										 </td>
									</tr>										
								</table>
							<?php
							
							}
						}
						?>	
				</td>
			</tr>
			</table>
			
			
			<div class="class_section_header"  style='width:800px'>Custom Token Variables
				<div style='float:right;display:inline'>
					<center><a href="./functions/f_add_template.php?type=variable" class="add_block"><img src="nav/add.png" border=0></a></center>
				</div>
			</div>
			<hr width="800" style="color:#eeeeee;background-color:#eeeeee;">
			
			<br>
			<?php
			
			if ($templates_custom_variable_id)
			{
				foreach ($templates_custom_variable_id as $k=>$v)
				{
					$templates_custom_variable_content[$k] = stripslashes($templates_custom_variable_content[$k]);
				?>
				
					<table width="800" style="margin-left:auto;margin-right:auto;padding:5px; border: solid 5px #eeeeee"> 
					 <tr>
						 <td  align=left valign=top style="font-size:13px;">
						 <input type=hidden name='template_custom_variable_id[]' value="<?php echo $templates_custom_variable_id[$k] ?>">	
						   </td>
						 <td align=right style="font-size:13px;">
							<img  onClick='$(this).parent().parent().parent().parent().remove();' src='nav/remove.png' style='cursor:pointer;'>
						 </td>
					</tr>
					<tr>
						<td  align=left valign=top style="font-size:13px;">
							Name/Description:<br> 
						</td>
						<td align=right style="font-size:13px;">
							<input name='template_custom_variable_name[]' size=55 value='<?php echo $templates_custom_variable_name[$k]; ?>'>
						</td>
					</tr>
					<tr>
						<td  align=left valign=top style="font-size:13px;">
							Token Variable:<br> 
						</td>
						<td align=right style="font-size:13px;">
							<input name='template_custom_variable_token[]' size=55 value='<?php echo $templates_custom_variable_token[$k]; ?>'>
						</td>
					</tr>
					<tr>
						 <td  align=left valign=top style="font-size:13px;">
							Replacement Content:
						 </td>
						 <td  align='right'  valign='top' style="font-size:13px;padding-top:5px;">
							<textarea width="100%" cols=77 rows=10 name='template_custom_variable_content[]' wrap='off'><?php echo $templates_custom_variable_content[$k]; ?></textarea>
						 </td>
					</tr>										
				</table>
				<br>
				<?php
				
				}
			}
			?>	
			
			
		</div>
		
		
		<div id='api'>
			<a name="api"></a>
			<div class="class_section_header" style='width:700px;'>
				<img src="nav/tip.png" style="cursor:pointer;" border=0 title="Use this api with custom PHP scripts and Google Reader"> BlogSense Remote Publishing API
			</div>
			<hr width="700" style="color:#eeeeee;background-color:#eeeeee;margin-left:auto;margin-right:auto;">			
			<table width="700" id="table_seo_profiles" style="margin-left:auto;margin-right:auto;padding:20px; border: solid 5px #eeeeee"> 
				<TR>
					<td align='left' colspan='3'>
						<h3 >Your Secret Access Key</h3>	
						<br>
						Secret Key: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input style='width:181px;' name='blogsense_api_secret_key' value='<?php echo $blogsense_api_secret_key; ?>'>
						
						<br><br>
					</td>
				</tr>
				<TR>
					<td align='left' colspan='3'>
						<h3>Access URL</h3>

						<?php 
							if ($_COOKIE['bs_blog_id'])
							{
								$multisite = "?blog_id=".$_COOKIE['bs_blog_id'];
							}
							if  ($blogsense_api_secret_key)
							{
								$secretkey = "&secret_key=$blogsense_api_secret_key";
							}
							echo "{$blogsense_url}blogsense_api.php$multisite$secretkey";
						?>
						<br><br>
						<h3>Mandatory Paramaters</h3>
					</td>
				</tr>
				<tr>
					<td align='center'>
						<b>NAME </B>
					</td>
					<td>
						<b>DESCRIPTION</B>
					</td>					
					<td>
						<b>FORMAT</B>
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						secret_key
					</td>
					<td align='left' valign='top'>
						Secret access key that is unique to this blogsense installation. Required for remote publishing.
					</td>
					<td align='left' valign='top'>
						TEXT
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						link
					</td>
					<td align='left' valign='top'>
						Link to remote item desired for publishing. (1 item per API call)
					</td>
					<td align='left' valign='top'>
						URL
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						cat_id 
					</td>
					<td align='left' valign='top'>
						Numeric id or slug of category to publish to. (Not required if running api in manual mode (see below for details on manual mode)). If a cat slug is detected, and in relation, no category is detected for this slug, then BlogSense will attempt to create this category.
					</td>
					<td align='left' valign='top'>
						STRING
					</td>
				</tr>
				<TR>
					<td colspan='3' align='left' >
						<br><br>
						<h3>Optional Paramaters</h3>
					</td>
				</tr>
				<tr>
					<td align='center'>
						<b>NAME </B>
					</td>
					<td>
						<b>DESCRIPTION</B>
					</td>					
					<td>
						<b>FORMAT</B>
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						author_id
					</td>
					<td align='left' valign='top'>
						Will set authorship of post to this author id. If no author id is set then blog's default author will be used. 
					</td>
					<td align='left' valign='top'>
						BOOLEAN (1 OR 0)
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						post_date
					</td>
					<td align='left' valign='top'>
						If this date-time stamp is detected it will be used as the publish date. Please use the post_status parameter correspondingly with this optional parameter. 
					</td>
					<td align='left' valign='top'>
						DATETIME (Fromat: YYYY-MM-DD HOUR:MINUTE:SECONDS)
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						add_days
					</td>
					<td align='left' valign='top'>
						If this parameter is detected it will be used to add x day(s) to the post_date parameter. If there is no post_date parameter detected then the days will be added onto the date-time-stamp generated by the remote API, which is always same date-time of reception. 
					</td>
					<td align='left' valign='top'>
						INTEGER
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						credit_source
					</td>
					<td align='left' valign='top'>
						Will add the original link in link format to end of description. If the content being deleivered already includes a crediting link please ignore this paramater or set it to zero. Default is 0.
					</td>
					<td align='left' valign='top'>
						BOOLEAN (1 OR 0)
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						lagnuage
					</td>
					<td align='left' valign='top'>
						 Will translate to this language.
					</td>
					<td align='left' valign='top'>
						(see /auto/languages.ini for acceptable paramaters)
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						manual_mode
					</td>
					<td align='left' valign='top'>
						 Will open content in a wysiwyg editor for editing the following before publishing: title, content, categories, tags, post_status. Requires link paramater.
					</td>
					<td align='left' valign='top'>
						BOOLEAN (1 OR 0) 
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						post_content
					</td>
					<td align='left' valign='top'>
						Will use this content as post material instead of auto-detecting content from link.
					</td>
					<td align='left' valign='top'>
						 TEXT BLOB 
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						post_status
					</td>
					<td align='left' valign='top'>
						Decleare post status. Default is 'publish'.
					</td>
					<td align='left' valign='top'>
						 publish,draft,future,trash
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						post_type
					</td>
					<td align='left' valign='top'>
						Decleare post type. Default is 'post'.
					</td>
					<td align='left' valign='top'>
						 post, page, attachment, etc.
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						post_tags
					</td>
					<td align='left' valign='top'>
						Specific tags to be applied to post(s). Please separate tags with commas. 
					</td>
					<td align='left' valign='top'>
						 TEXT, (no question marks or equal signs please)
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						post_title
					</td>
					<td align='left' valign='top'>
						Will use this content as post title instead of auto-detecting title from link.
					</td>
					<td align='left' valign='top'>
						 TEXT BLOB 
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						spin_text
					</td>
					<td align='left' valign='top'>
						Will spin text using internal spin mechanism.
					</td>
					<td align='left' valign='top'>
						NUMERIC (0,1, OR 2).<BR>
						0 = no spinning (default)<br>
						1 = Use BlogSense spin engine.<br>
						2 = Set to this only if content already contains spyntax and needs to be spun.<br>
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						store_images 
					</td>
					<td align='left' valign='top'>
						Will locally store images if found.
					</td>
					<td align='left' valign='top'>
						BOOLEAN (1 OR 0)
					</td>
				</tr>
				<tr>
					<td align='left' valign='top'>
						strip_links
					</td>
					<td align='left' valign='top'>
						 Will strip links from post content.
					</td>
					<td align='left' valign='top'>
						BOOLEAN (1 OR 0)
					</td>
				</tr>
				<tr>
					<td colspan=3 align='left'>
					 <br><br>
					 <h3>Example Call URL</h3>
					 <i><?php 
						if ($_COOKIE['bs_blog_id'])
						{
							$multisite = "&blog_id=".$_COOKIE['bs_blog_id'];
						}
						echo $blogsense_url."blogsense_api.php?secret_key=$blogsense_api_secret_key{$multisite}&link=http%3A%2F%2Fexamplesite.com%2Fhow-to-use-blogsense-api%2F&cat_id=1709&credit_source=1"; 
						
						?></i>
					</td>
				</tr>
				
			</table>
		</div>
		
		</div>
		<br>
		

</form>
		</div>
		
		<br>
		<center>
		    <div id=s_button class="button" ><a id="save_button" class="button"  style="padding-left:51px;padding-right:53px;" title='Save Settings'>Save Settings</a></div>
            <div id=r_button class="button" ><a id="run_button" class="button"  target=_blank href="cron_config.php?blog_id=<?php echo $_COOKIE['bs_blog_id'];?>"  title='Run ALL Campaigns (psuedo cronjob run)' style="padding-left:51px;padding-right:53px;" >Run ALL Campaigns</a></div>
			<br>
		</center>
		
		<br><br>
    </div>
</div>
</div>
<?php
}
}

if ($_GET['p']==4)
{
	echo "<div id=bookmarking_settings>";

	if ($_GET['saved']=='y'&&$_GET['p']==4)
	{
		echo "<br><center><font style=\"font-size:11;color:green;\">Settings Saved!</font><br><br>";
	}
	if ($blogsense_activation!=1)
	{
		echo "<br><br><br><br><center><font style=\"font-size:11;color:#aaaaaa;\">Please Activate BlogSense</font><br><br><br><br><br>";
	}
	else
	{
	?>
		
		<div id=bookmarking_menu class="class_sidemenu" style="top:-21px;left:-51px; text-align:left;position:relative;line-height:24px;width:100%;font-size:12px;">
		 <ul>
		    <a id="a_bookmarking_settings" style="text-decoration:none;color:grey;cursor:pointer;">Accounts & Settings</a> &nbsp;&nbsp;|&nbsp;&nbsp;
			<a id="a_bookmarking_logs" style="text-decoration:none;color:grey;cursor:pointer;">Bookmarking Logs</a> 
		</ul>
	    </div>
		<div id='id_bookmarking_settings'>
		
			<form  action="functions/f_save_bookmarking_settings.php" id='form_bookmarking_save' method=POST>
				<center>
				<a name="settings"></a>
				<div class="class_section_header">Scheduling Settings</div>
					
				<hr style="width: 530px; color: rgb(238, 238, 238); background-color: rgb(238, 238, 238);">
				<table class='campaign'> 
				  <tr>
					 <td  align=left valign=top style="font-size:13px;">
					 <img src="./nav/tip.png" style="cursor:pointer;" border=0 title="BlogSense will schedule the bookmarking of one published post every x minutes, x being a random number between the declared min and max. Note that Onlywire has a 10 minute throttle between posts and Ping.FM has a 5 minute throttle between posts.">
								
						Scheduling Buffer (<i>in minutes.</i>)<br> </td>
					 <td align=right style="font-size:13px;">
						Min: <input  name='bookmarking_minutes_min' size=1 value='<?php echo $bookmarking_minutes_min; ?>'>  &nbsp;
						Max: <input  name='bookmarking_minutes_max' size=1 value='<?php echo $bookmarking_minutes_max; ?>'>  &nbsp;
					</td>
				  </tr>
				  <tr>
					 <td  align=left valign=top style="font-size:13px;">
					 <img src="./nav/tip.png" style="cursor:pointer;" border=0 title="Evertime a campaign is run, this percentage of posts will be bookmarked. There must be more than 2 new posts for this throttle to apply.">
								
						Percentage to Bookmark<br> </td>
					 <td align=right style="font-size:13px;">
						<input  name='bookmarking_percentage' size=1 value='<?php echo $bookmarking_percentage; ?>'><i>%</i>
					</td>
				  </tr>
				  <tr>
					 <td  align=left valign=top style="font-size:13px;">
					 <img src="./nav/tip.png" style="cursor:pointer;" border=0 title="Turning this option on will ping a post in addition to any bookmarking.">
						Ping All Posts?<br> </td>
					 <td align=right style="font-size:13px;">
						<input  name='bookmarking_ping_module' type=radio value=1 <?php if ($ping_module=="1") {echo "checked='checked'";}  ?>> on &nbsp; <input name='bookmarking_ping_module' type=radio value=0  <?php if ($ping_module=="0") {echo "checked='checked'";}  ?>> off
					</td>
				  </tr>
				   <tr>
					 <td  align=left valign=top style="font-size:13px;">
					 <img src="./nav/tip.png" style="cursor:pointer;" border=0 title="Enter in your own bit.ly credentials for link tracking.">
						Bit.ly Username<br> </td>
					 <td align=right style="font-size:13px;">
						<input  name='bookmarking_bitly_username' size=30 value='<?php echo $bookmarking_bitly_username; ?>'>
					</td>
				  </tr>
				  <tr>
					 <td  align=left valign=top style="font-size:13px;">
					 <img src="./nav/tip.png" style="cursor:pointer;" border=0 title="Enter in your own bit.ly credentials for link tracking.">
						Bit.ly API Key<br> </td>
					 <td align=right style="font-size:13px;">
						<input  name='bookmarking_bitly_apikey' size=30 value='<?php echo $bookmarking_bitly_apikey; ?>'>
					</td>
				  </tr>
				  
				</table>
				<br><br>
				<a name="bookmarking"></a>
				
				
			
				<a name="twitter"></a>		
				<div class="class_section_header">Twitter Module 
					<div style='float:right'>
						<a href="./includes/i_twitter_add_account.php" class='add_twitter' ><img src="nav/add.png" style="cursor:pointer;" border=0></a>
					</div>
				</div>
					
				<hr style="width: 530px; color: rgb(238, 238, 238); background-color: rgb(238, 238, 238);">
				<table class='campaign'> 
				  <tr>
					 <td  align=left valign=top style="font-size:13px;">
						<img src="./nav/tip.png" style="cursor:pointer;" border=0 title="Globally enable or disable posting to Twitter accounts. If enabled, campaigns can still be enabled or disabled individually.">
						Activate Module:<br> </td>
					 <td align=right style="font-size:13px;">
						<input  name='twitter_module' type=radio value=1 <?php if ($twitter_module=="1") {echo "checked='checked'";}  ?>> on &nbsp; <input name='twitter_module' type=radio value=0  <?php if ($twitter_module=="0") {echo "checked='checked'";}  ?>> off
					</td>
				  </tr>
				  <tr>
					 <td  align=left valign=top style="font-size:13px;width:148px;">
						<img src="./nav/tip.png" style="cursor:pointer;" border=0 title="Declare weather or not to bookmark posts for every account or to select an account at random for the bookmarking of a post. A natural time-buffer will exists between account postings if 'Post to All Accounts' is selected. Accounts are selected or deselected individually within the campaigns settings area.">
						Campaign Mode:<br> </td>
					 <td align=left style="font-size:13px;">
						<input  name='twitter_mode' type=radio value='random' <?php if ($twitter_mode=="random") {echo "checked='checked'";}  ?>> Alternate post bookmarks between selected accounts. 
						<br>
						<input name='twitter_mode' type=radio value='all'  <?php if ($twitter_mode=="all") {echo "checked='checked'";}  ?>> Do not alternate (Bookmark each post to all selected accounts)
					</td>
				  </tr>
				</table>
			
				<div id="id_table_twitter">
				<br>
				 <?php
				  if (strlen($twitter_user[0])>2)
				  {
					foreach ($twitter_user as $key=>$value)
					{
						$acc = $key +1;
						?>
						<table class='campaign'> 
						<tr>
							 <td align="left">
								 <i>#<?php echo $acc; ?></i> 
							 </td>
							 <td align="right">
								 <img  onClick='$(this).parent().parent().parent().parent().remove();' src='nav/remove.png' style='cursor:pointer;'>
							 </td>
						</tr>
						<tr>
							 <td  align=left valign=top style="font-size:13px;">
								Twitter Username:<br> </td>
							 <td align=right style="font-size:13px;">
								<?php echo $twitter_user[$key]; ?>
								<input type='hidden' name='twitter_user[]' value='<?php echo $twitter_user[$key]; ?>'>
								<input type='hidden' name='twitter_oauth_secret[]' value='<?php echo $twitter_oauth_secret[$key]; ?>'>
								<input type='hidden' name='twitter_oauth_token[]' value='<?php echo $twitter_oauth_token[$key]; ?>'>
							</td>
						</tr>					
						</table>
						<br>
						<?php
					}
				  }
				  ?> 		  
				</div> 
				<br><br>
				
				<a name="pixelpipe"></a>		
				<div class="class_section_header">PixelPipe Module 
					<div style='float:right'>
						<img src="nav/add.png" style="cursor:pointer;" border=0 id='id_btn_add_pixelpipe_0' class='class_btn_bookmarking_pixelpipe'>
					</div>
				</div>
					
				<hr style="width: 530px; color: rgb(238, 238, 238); background-color: rgb(238, 238, 238);">
				<table class='campaign'> 
				  <tr>
					 <td  align=left valign=top style="font-size:13px;">
						<img src="./nav/tip.png" style="cursor:pointer;" border=0 title="Globally enable or disable posting to Pixelpipe accounts. If enabled, campaigns can still be enabled or disabled individually.">
						Activate Module:<br> </td>
					 <td align=right style="font-size:13px;">
						<input  name='pixelpipe_module' type=radio value=1 <?php if ($pixelpipe_module=="1") {echo "checked='checked'";}  ?>> on &nbsp; <input name='pixelpipe_module' type=radio value=0  <?php if ($pixelpipe_module=="0") {echo "checked='checked'";}  ?>> off
					</td>
				  </tr>
				  <tr>
					 <td  align=left valign=top style="font-size:13px;width:148px;">
						<img src="./nav/tip.png" style="cursor:pointer;" border=0 title="Declare weather or not to bookmark posts for every account or to select an account at random for the bookmarking of a post. A natural time-buffer will exists between account postings if 'Post to All Accounts' is selected. Accounts are selected or deselected individually within the campaigns settings area.">
						Campaign Mode:<br> </td>
					 <td align=left style="font-size:13px;">
						<input  name='pixelpipe_mode' type=radio value='random' <?php if ($pixelpipe_mode=="random") {echo "checked='checked'";}  ?>> Alternate post bookmarks between selected accounts. <br>
						<input name='pixelpipe_mode' type=radio value='all'  <?php if ($pixelpipe_mode=="all") {echo "checked='checked'";}  ?>> Do not alternate (Bookmark each post to all selected accounts)
					</td>
				  </tr>
				</table>
			
				<div class='' id="id_table_pixelpipe">
				<br>
				 <?php
				  if (strlen($pixelpipe_email[0])>2)
				  {
					foreach ($pixelpipe_email as $key=>$value)
					{
						$acc = $key +1;
						?>
						<table class='campaign' id='id_tables_pixelpipe'> 
						<tr>
							 <td align="left">
								 <i>#<?php echo $acc; ?></i> 
							 </td>
							 <td align="right">
								 <img  onClick='$(this).parent().parent().parent().parent().remove();' src='nav/remove.png' style='cursor:pointer;'>
							 </td>
						</tr>
						<tr>
							 <td  align=left valign=top style="font-size:13px;">
								<img src="./../nav/tip.png" style="cursor:pointer;" id=id_add_button_remote_publishing title='Unique PP Email Address. To find: 1. Login to pixelpipe.com 2. Click on Software Solutions. 3. Press "Tell me More" under the Email/MMS section. '> 
								Pixelpipe Account Email:<br> </td>
							 <td align=right style="font-size:13px;">
								<?php echo $pixelpipe_email[$key]; ?>
								<input type='hidden' name='pixelpipe_email[]' value='<?php echo $pixelpipe_email[$key]; ?>'>
							</td>
						</tr>					
						<tr>
							 <td  align=left valign=top style="font-size:13px;">
								<img src="./../nav/tip.png" style="cursor:pointer;"  title='PixelPipe Routing Tags. eg: @blogger. To find out what routing tags are available please login to pixelpipe and view "My Pipes". Separate routing tags with spaces.'> 
								Pixelpipe Routing Tags:<br> </td>
							 <td align=right style="font-size:13px;">
								<input name='pixelpipe_routing[]' size=30 value='<?php echo $pixelpipe_routing[$key]; ?>'>
							 </td>
						</tr> 
						</table>
						<br>
						<?php
					}
				  }
				  ?> 		  
				</div> 
				<br><br>
				
				
			<center>
		    <div id=s_button_bookmarking class="button" ><a id="save_button" class="button"  style="padding-left:51px;padding-right:53px;" title='Save Settings'>Save Settings</a></div>
			<br>
			</center>
			</form>
		</div>
		
		<div id='id_bookmarking_logs'>
			<div style='width:801px;font-size:10px;text-align:right;padding-bottom:3px'>
				<a id="a_bookmarking_logs_all" style="text-decoration:none;color:grey;cursor:pointer;">All</a> &nbsp;&nbsp;|&nbsp;&nbsp;
				<a id="a_bookmarking_logs_ping" style="text-decoration:none;color:grey;cursor:pointer;">Pinged</a> &nbsp;&nbsp;|&nbsp;&nbsp;
				<a id="a_bookmarking_logs_twitter" style="text-decoration:none;color:grey;cursor:pointer;">Twitter</a> &nbsp;&nbsp;|&nbsp;&nbsp;
				<a id="a_bookmarking_logs_pixelpipe" style="text-decoration:none;color:grey;cursor:pointer;">Pixelpipe</a> &nbsp;&nbsp;|&nbsp;&nbsp;

				<br>
				<script>
				function resizeIframe(newHeight)
				{
				  document.getElementById('id_iframe_bookmarking_logs').style.height = parseInt(newHeight) + 10 + 'px';
				}

				</script>
				<iframe src='<?php if ($_GET['m']=='bookmarking_logs'){ echo "./functions/f_bookmarking_report.php?mode=posted"; } ?>' width='800' height='100%' style='height:100%;' frameborder='0' id='id_iframe_bookmarking_logs'>Loading Please Wait.</iframe>
			</div>
			
			<Br>
			<center>
				 <div class="button" ><a id="save_button" class='class_bookmarking_rebuild' href='./functions/f_save_bookmarking_settings.php?rebuild=1'  style="padding-left:51px;padding-right:53px;" title='Save Settings'>Rebuild Queue</a></div> &nbsp;&nbsp;
				 <div class="button" ><a id="save_button" class='class_bookmarking_rebuild' href='./functions/f_save_bookmarking_settings.php?empty=1'  style="padding-left:51px;padding-right:53px;" title='Save Settings'>Empty Queue</a></div>
			</center>
		</div>
		
		
		<br><br>
    </div>
</div>
</div>
	<?php
	}
}
?>






<div class="footer_container">
<br><br>

<center><font style="font-size:9px;font-decoration:italics;color:#a9a9a9">BlogSenseWP - Wordpress Automation Platform - <?php echo $build_version; ?>
</font>
<?php
//is blogsense active?
$query = "SELECT option_value FROM ".$table_prefix."blogsense WHERE option_name='blogsense_activation' ";
$result = mysql_query($query);

if ($result)
{
	$array = mysql_fetch_array($result);
	$blogsense_activation = $array['option_value'];

	
	$download_file = "http://www.hatnohat.com/api/blogsense/download.php?license=$blogsense_activation_key&license_email=$blogsense_activation_email";
	$version_check = "http://www.hatnohat.com/api/blogsense/update.php?serve_public=1";
	

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $version_check);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$latest_build_version= curl_exec($ch);
	curl_close($ch);


	if ($latest_build_version>$build_version&&$blogsense_activation==1)
	{
		echo "&nbsp;<br><font style='font-size:9px;font-decoration:italics;color:#fdf5e6;font-weight:600'>NEW VERSION AVAILABLE ($latest_build_version) </font><br>";
		echo "   <a href='http://www.blogsense-wp.com/news/' style='font-size:9px;font-decoration:italics;color:#d3d3d3; text-decoration:none;font-weight:600' target=_blank>[ read more ]</a></font>";
		echo "   <a href='update.php' style='font-size:9px;font-decoration:italics;color:#00FF7F;font-weight:600;text-decoration:none;' target=_blank title='This will attempt to auto-download and update BlogSense.'>[ upgrade now ]</a>";
		echo "   <a href='$download_file' style='font-size:9px;font-decoration:italics;color:#d3d3d3;font-weight:600;text-decoration:none;' target=_blank>[ download ]</a>";
	}
}
?>
</center>
</div>
<br>

<div id=selects_wp_categories style='display:none'>
<select name='category_parent[]'>
<option value=0>none</option>		 
<?php echo $selects_categories; ?>
</select>
</div>

</body>
</html>