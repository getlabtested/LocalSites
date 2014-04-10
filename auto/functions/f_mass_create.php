<?php
include_once('./../../wp-config.php');

session_start();

include("./../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../login.php");

include_once("../includes/helper_functions.php");
include_once('./../includes/prepare_variables.php');

//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}

$module_type = $_GET['module_type'];
if (!$module_type){ $_POST['module_type']; }
$submit_nature = $_POST['submit_nature'];
$mode = $_GET['mode'];

$categories_list = implode(',',$categories);

$campaign_title_template="";
$campaign_post_template="";
$campaign_custom_field_name="";
$campaign_custom_field_value="";
$campaign_include_keywords = "Separate with commas.";
$campaign_exclude_keywords = "Separate with commas.";
$campaign_autocategorize = "";
$campaign_autocategorize_search = "";
$campaign_autocategorize_method = "";
$campaign_autocategorize_filter_keywords = "";
$campaign_autocategorize_filter_categories= "";
$campaign_autocategorize_filter_list= "";


if ($mode=='mass_update_keywords')
{
	$pid = $_GET['id'];
	$query = "SELECT * FROM ".$table_prefix."seoprofiles WHERE id='$pid'";		
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();exit;}
	

	while ($arr = mysql_fetch_array($result))
	{
		$keyphrase = $arr['keyphrase'];
		$decoration = $arr['decoration'];
		$href = $arr['url'];
		$class = $arr['class'];
		$rel = $arr['rel'];
		$limit = $arr['limit'];
		$target= $arr['target'];		
	}
	
	//echo $keyphrase; exit;
	$query = "SELECT * FROM ".$table_prefix."posts ";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); }
	$total_count = mysql_num_rows($result);
	$post_count = 0;
	
	$query = "SELECT * FROM ".$table_prefix."posts WHERE post_content LIKE '% $keyphrase %'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); }
	$count = mysql_num_rows($result);
	
	while ($arr = mysql_fetch_array($result))
	{
		$post_count++;
		$post_id = $arr['ID'];
		$post_content = $arr['post_content'];
		
		$post_ids_affected[] = $post_id;
		$post_content = dress_keywords($keyphrase, $decoration, $href, $class, $rel, $target, $limit,  $post_content);
		//echo $post_content; exit;
		$post_content = addslashes($post_content);
		
		$query2 = "UPDATE ".$table_prefix."posts SET post_content = '$post_content' WHERE ID = '$post_id'";
		$result2 = mysql_query($query2);
		//if ($result2){echo $query2; echo mysql_error(); exit; }
		
	}
	
	echo "<center><br><br><br><br><font color=green>$post_count Posts Affected. (Out of ".$total_count.")</center></font>";
	
	foreach ($post_ids_affected as $key=>$val)
	{
		$permalink = get_permalink($post_id);
		echo "<br> Post Affected: ID-$val : $permalink";
	}
	exit;
}
if ($submit_nature=='create')
{
	if (!$keyphrase)
	{
	  $keyphrase = array();
	}
	
	//get variables
	$source =  $_POST['source'];
	$keyword_option =  $_POST['keyword_option'];
	$batch_keywords =  $_POST['batch_keywords'];
	$batch_keywords = nl2br($batch_keywords);
	$batch_keywords = explode('<br />', $batch_keywords);
	//print_r($batch_keywords);exit;
	$keyword_tag = $_POST['keyword_tag'];
	$limit_results = $_POST['limit_results'];
	$include_keywords = $_POST['include_keywords'];
	$exclude_keywords = $_POST['exclude_keywords'];
	$author = urlencode($_POST['author']);
	if ($_POST['links']){ $links = explode(";", $_POST['links']);}
	$post_frequency = $_POST['post_frequency'];
	$post_frequency_start_date = $_POST['post_frequency_start_date'];
	$cloak_links = $_POST['cloak_links'];
	$strip_links = $_POST['strip_links'];
	$strip_images = $_POST['strip_images'];
	$language = $_POST['language'];
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
	$scrape_content_end = stripslashes($_POST['scrape_content_end']);
	$scrape_title_start = stripslashes($_POST['scrape_title_start']);
	$scrape_title_end = stripslashes($_POST['scrape_title_end']);
	$scrape_comments_status = stripslashes($_POST['scrape_comments_status']);
	$scrape_comments_name_start = stripslashes($_POST['scrape_comments_name_start']);
	$scrape_comments_name_end = stripslashes($_POST['scrape_comments_name_end']);
	$scrape_comments_content_start = stripslashes($_POST['scrape_comments_content_start']);
	$scrape_comments_content_end = stripslashes($_POST['scrape_comments_content_end']);
	$regex_search = $_POST['regex_search'];
	$regex_replace = $_POST['regex_replace'];	
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
	$title_template = stripslashes($_POST['title_template']);
	$post_template = stripslashes($_POST['post_template']);
	$z_bookmark_twitter = $_POST['bookmark_twitter'];
	$comments_include = $_POST['comments_include'];
	$comments_limit = $_POST['comments_limit'];
	$post_status = $_POST['post_status'];
	$remote_publishing_api_bs =$_POST['remote_publishing_api_bs'];
	$remote_publishing_api_email =$_POST['remote_publishing_api_email'];
	$remote_publishing_api_email_footer =$_POST['remote_publishing_api_email_footer'];
	$remote_publishing_api_pp_email =$_POST['remote_publishing_api_pp_email'];
	$remote_publishing_api_pp_routing =$_POST['remote_publishing_api_pp_routing'];
	$remote_publishing_api_xmlrpc =$_POST['remote_publishing_api_xmlrpc'];
	$remote_publishing_api_xmlrpc_spin =$_POST['remote_publishing_api_xmlrpc_spin'];

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
	
	$rss_scrape_content_begin_code = addslashes($rss_scrape_content_begin_code);
	$rss_scrape_content_end_code = addslashes($rss_scrape_content_end_code);
	$rss_scrape_names_begin_code = addslashes($rss_scrape_names_begin_code);
	$rss_scrape_names_end_code = addslashes($rss_scrape_names_end_code);
	$rss_scrape_comments_begin_code = addslashes($rss_scrape_comments_begin_code);
	$rss_scrape_comments_end_code = addslashes($rss_scrape_comments_end_code);

	if (!$include_keywords) { $include_keywords = "Separate with commas."; }
	if (!$exclude_keywords) { $exclude_keywords = "Separate with commas."; }
	
	$campaign_remote_publishing_api_bs = "";
	$campaign_remote_publishing_api_pp_email = "";
	$campaign_remote_publishing_api_email = "";
	
	//prepare regex searches if needed
	if ($regex_search)
	{
		$in_regex_search = "";
		$in_regex_replace = "";
		foreach($regex_search as $k=>$v)
		{
			//echo $regex_search; exit;
			if (!strstr($regex_search[$k],'/'))
			{
				$regex_search[$k] = "/$regex_search[$k]/";
			}
		}
		
		$in_regex_search = implode('***r***',$regex_search);
		$in_regex_replace = implode('***r***',$regex_replace);
	}
	
	//add slashes if needed
	$rss_begin_code = addslashes($rss_begin_code);
	$rss_end_code = addslashes($rss_end_code);
	$post_template = addslashes($post_template);
	$title_template = addslashes($title_template);
	
	$source = urldecode($source);
	$original_cat = $category;
	foreach ($batch_keywords as $key=>$val)
	{
		$val = trim($val);
		//make original source url
		$keywords = "$val $keyword_tag";
		$campaign_name = $keywords;
		if ($keyword_option=='categories')
		{
			$term = get_term_by('name', $val, 'category');
			$category = $term->term_id;
		}
		else
		{
			$category = $original_cat;
		}
		
		
		$keywords = urlencode($keywords);
		//echo $keywords; exit;
		
		if ($module_type=='sources')
		{	   
			//build the special feed link
			$include_url = explode('functions', $current_url);	
			$include_url = "".$include_url[0]."includes/";	
			$site_formated = str_replace("http://", "open*", $source);
			$footprint = urlencode($scrape_footprint);
			$nukeywords = "$keywords+$footprint";
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
		if($module_type=='rss')
		{
			$source_feed = $source;
		}
		if($module_type=='video')
		{
			if ($source=='youtube')
			{
				$nukeywords = explode(" ", $keywords);
				$nukeywords = implode("+", $nukeywords);
				$source_feed = "http://gdata.youtube.com/feeds/base/videos?q=$nukeywords&key=AI39si6RmbtB6goYpu0MrGKmEeEhg5dIOSdZUClTencT6F_Saf3Wjqp9y55xoJ1PAa_htlx3ArxozpuNiG-jdWzNxMAV-NhvKw";	
			}
			if ($source=='hulu')
			{
				$source_feed = $keywords;
			}
		}
		
		if($module_type=='yahoo')
		{
			if ($limit_results==0||$limit_results>50) $limit_results=50;
			$y_feed = "http://answers.yahooapis.com/AnswersService/V1/questionSearch?&appid=YahooDemo&query=$keywords&type=$z_yahoo_option_type&category_id=$z_yahoo_option_category&region=$z_yahoo_option_region&date_range=$z_yahoo_option_date_range&results=$limit_results&sort=$z_yahoo_option_sorting&search_in=all";
			$source_feed = $y_feed;
			//echo $source_feed; exit;
		}

		//build start date
		if ($post_frequency=='backdate')
		{
			$time = date('H:i:s');
			$post_frequency_start_date = "$post_frequency_start_date $time";
		}
		else
		{
			$post_frequency_start_date = '0000-00-00 00:00:00';
		}
		
		$query = "INSERT INTO ".$table_prefix."campaigns (`id`,`name`,`campaign_status`,`module_type`,`source`,`query`,`feed`,`limit_results`,`author`,`include_keywords`,`exclude_keywords`,`category`,`autocategorize`,`autocategorize_search`,`autocategorize_method`,`autocategorize_filter_keywords`,`autocategorize_filter_categories`,`autocategorize_filter_list`,`autotag_method`,`autotag_custom_tags`,`autotag_min`,`autotag_max`,`language`,`spin_text`,`strip_images`,`strip_links`,`image_floating`,`scrape_profile`,`regex_search`,`regex_replace`,`credit_source`,`credit_source_nofollow`,`credit_source_text`,`schedule_backdating`,`schedule_post_frequency`,`schedule_post_date`,`schedule_post_count`,`custom_field_name`,`custom_field_value`,`z_affiliate_id`,`z_bookmark_twitter`,`z_bookmark_pixelpipe`,`z_rss_scrape_content`,`z_rss_scrape_content_begin_code`,`z_rss_scrape_content_end_code`,`z_rss_scrape_comments`,`z_rss_scrape_names_begin_code`,`z_rss_scrape_names_end_code`,`z_rss_scrape_comments_begin_code`,`z_rss_scrape_comments_end_code`,`z_video_include_description`,`z_yahoo_option_category`,`z_yahoo_option_date_range`,`z_yahoo_option_region`,`z_yahoo_option_results_limit`,`z_yahoo_option_sorting`,`z_yahoo_option_type`,`z_post_template`,`z_title_template`,`z_post_status`,`z_post_type`,`z_comments_include`,`z_comments_limit`,`z_remote_publishing_api_bs`,`z_remote_publishing_api_xmlrpc`,`z_remote_publishing_api_xmlrpc_spin`,`z_remote_publishing_api_pp_email`,`z_remote_publishing_api_pp_routing`,`z_remote_publishing_api_email`,`z_remote_publishing_api_email_footer`,`z_post_overwrite`,`z_include_keywords_scope`,`z_exclude_keywords_scope`)";
	    $query .= "VALUES ('','$campaign_name','1','$module_type','$source','$keywords','$source_feed','$limit_results','$author','$include_keywords','$exclude_keywords','$category','$autocategorize','$autocategorize_search','$autocategorize_method','$autocategorize_filter_keywords','$autocategorize_filter_categories','$autocategorize_filter_list','$autotag_method','$autotag_custom_tags','$autotag_min','$autotag_max','$campaign_language','$spin_text','$strip_images','$strip_links','$image_floating','$scrape_profile','$regex_search','$regex_replace','$credit_source','$credit_source_nofollow','$credit_source_text','$backdating','$post_frequency','$post_frequency_start_date','0','$custom_field_name','$custom_field_value','$z_affiliate_id','$z_bookmark_twitter','$z_bookmark_pixelpipe','$rss_scrape_content','$rss_scrape_content_begin_code','$rss_scrape_content_end_code','$rss_scrape_comments','$rss_scrape_names_begin_code','$rss_scrape_names_end_code','$rss_scrape_comments_begin_code','$rss_scrape_comments_end_code','$video_include_description','$z_yahoo_option_category','$z_yahoo_option_date_range','$z_yahoo_option_region','$z_yahoo_option_results_limit','$z_yahoo_option_sorting','$z_yahoo_option_type','$post_template','$title_template','$post_status','$post_type','$comments_include','$comments_limit', '$remote_publishing_api_bs','$remote_publishing_api_xmlrpc','$remote_publishing_api_xmlrpc_spin','$remote_publishing_api_pp_email','$remote_publishing_api_pp_routing','$remote_publishing_api_email','$remote_publishing_api_email_footer', '$post_overwrite','$include_keywords_scope','$exclude_keywords_scope')";
	    $result = mysql_query($query);
	    if (!$result){ echo $query; echo mysql_error(); exit; }
	
	   
	}//end foreach batch_keyword
	
	 echo "<center><br><br><br><br><font color=green>Multiple Campaigns Created. </center></font>";
	 exit;
}

//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//YAHOO ANSWERS
if ($module_type=='yahoo'&&!$nature)
{
	//reset array variables
	$query = "SELECT content FROM ".$table_prefix."post_templates WHERE `type` IN ( 'default_rss_post_template' ,'default_title_template') ORDER BY type ASC";
	$result = mysql_query($query);
	if (!$result){echo $query; exit;}
	while ($arr = mysql_fetch_array($result))
	{
		$new_array[] = $arr[0];
	}
	$default_post_template = $new_array[0];
	$default_title_template = $new_array[1];

	//echo $default_post_template;exit;

	?>
	<html>
		<head>

		<script type="text/javascript" src="./../includes/jquery.js"></script>
		<link rel="stylesheet" type="text/css" href="./../includes/jquery-ui-1.7.2.custom.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
		<script type="text/javascript"> 
		$(document).ready(function() 
		{
			$("#id_templating").accordion({
					autoHeight: false,
					collapsible: true,
					 active: 1
			});

			$('.class_inputs').attr('disabled', 'disabled');
			
			$("#id_button_create_campaign").click(function(){
				$("#nature").val("create");
				$("#form_yahoo").submit();
			 });
			 
			 $("#id_keyword_options").change(function(){	   
				var input =$(this).val();
				if (input=='categories')
				{
				
				   $('.class_inputs').removeAttr('disabled');
				   var cats = "<?php echo $categories_list; ?>";
				   cats = cats.replace(/,/g,'\n');
				   $("#id_keywords").val(cats);
				}
				else if (input=='custom_list')
				{
					$('.class_inputs').removeAttr('disabled');
					var clone = "<?php echo $categories_list; ?>";
					$("#id_keywords").val('');
				}
				else
				{
					$('.class_inputs').attr('disabled', 'disabled');
					
				}
			});
			
			$("img.add_articles_string_edit").live("click" ,function(){
			   var id = this.id.replace('articles_string_edit_button_','');
			   $('#id_table_regex tr:last').after('<tr><td  align=left style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><input size=20 name=\"regex_search[]\" ></td><td  align=right style=\"font-size:13px;\"><input size=20 name=\"regex_replace[]\"></td></tr>');
			});
			
			$( "#datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
			
			$('#id_query').bind('keyup keypress blur', function() {
				var a = $('#id_campaign_name').val();
				var b = $('#id_query').val();
				b = b.slice(0, -1);

				if (a==b)
				{
					$('#id_campaign_name').val($(this).val());
				}				
			});
			
			$(".comments_include_select").change(function(){	   
			   var input =$(this).val();
			   if (input==1)
			   {
				   var clone = "<tr><td  align=left valign=top style='font-size:13px;'>&nbsp;&nbsp&nbsp;<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Limit the number of comments allowed to be auto-scheduled. Leave at 0 to schedule/publish all available comments.'> Limit Comments:<br> </td><td align=right style='font-size:13px;'><input  name=comments_limit size=1 value='0'></td></tr>";
				   $("#id_table_comments_include").append(clone);
			   }
			   else
			   {
				  $("#id_table_comments_include tr:last").remove();
			   }
			});
			
			<?php
			//add hook jquery
			include_once('./../includes/i_hook_jquery.js');
			?>
			
		});
		</script>
		<style type="text/css" media="screen">
				ul#grid li {
					list-style: none outside;
					float: left;
					margin-right: 20px;
					margin-bottom: 20px;
					font-size: 50px;
					width: 5em;
					height: 5em;
					line-height: 5em;
					text-align: center;
				}
					ul#grid li img {
						vertical-align: middle;
					}
				.ui-slider-handle { left: 45%; }
				
				.token_items 
				{
					font-size:11px;
					color:grey;
					text-decoration:none;
				}
				
				.token_items a:active
				{
					font-size:11px;
				}
				.class_custom_var_post
				{
					font-size:11px;
					color:grey;
					text-decoration:none;
				}
				.class_custom_var_post:hover
				{
					font-size:12px;
				}
				.class_custom_var_post a:active
				{
					font-size:11px;
				}
		</style>
		</head>
		<body style="font-family:Khmer UI;">
		<form action="" id="form_yahoo" name="form_yahoo" method=POST>	
		<input type=hidden name=submit_nature id=nature value='create'>
		<input type=hidden name=module_type  value='<?php echo $module_type; ?>'>

		
		<table width="100%">
		<tr>
			<td width='50%' valign='top'>
				<center>		
						   
				<div style="font-size:14px;width:500;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">Yahoo Answers : Mass Import
					<div style='float:right;'>
					</div>
				</div>
				<hr width=500 style="color:#eeeeee;background-color:#eeeeee;">
				 
				<table width=500 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee;"> 
					<tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select the mode of importing a batch of keywords for campaign creation">
							Keyword Options:<br>
						 </td>
						 <td align=right style="font-size:13px;">		
							<select  id='id_keyword_options' name='keyword_option' style='width:341px;'>
								<option value='x'>Please Select</option>
								<option value='categories'>Use Categories as  Base Keywords</option>
								<option value='custom_list'>Use a List of Custom Keywords</option>
								</select>
						 </td>
					</tr>
					<tr>
						<td  align=left valign=top style="font-size:13px; width:300px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This keyword if filled out will be added onto each keyword gathered when creating multiple campaigns. eg: 'keyword keyword_tag'">
							 Keyword Tag:<br>
						</td>
						<td align=right style="font-size:13px;">
							<input class='class_inputs' id='id_keyword_tag'  name='keyword_tag'  size=52 value=''>			
						</td>
					</tr>
					<tr>
						<td  align=left valign=top style="font-size:13px; width:300px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="1 per line.">
							 Keywords:<br>
						</td>
						<td align=right style="font-size:13px;">
							<textarea class='class_inputs'  name='batch_keywords' id='id_keywords' cols=40 rows=8 >
							</textarea>
						</td>
					</tr>			
					<tr>
						<td colspan=2 align=left valign=top style="font-size:13px; width:300px;">
							<br>	
						</td>
					</tr>
					<tr>
						<td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Determines the nature of yahoo answer - question results.">
							Question Nature<br>
						</td>
						<td align=right style="font-size:13px;">
							<select name=z_yahoo_option_type>				 
								<option value='all' <?php if ($z_yahoo_option_type=='all') echo "selected=true"; ?>>All</option>
								<option value='resolved' <?php if ($z_yahoo_option_type=='resolved') echo "selected=true"; ?>>Resolved Questions </option>
								<option value='open' <?php if ($z_yahoo_option_type=='open') echo "selected=true"; ?>>Undecided Questions</option>
								<option value='undecided' <?php if ($z_yahoo_option_type=='undecided') echo "selected=true"; ?>>Open Questions </option>
							</select>
						</td>
					</tr>
					<tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Determines the nature of yahoo answer - question results.">
							Target Region<br> </td>
						<td align=right style="font-size:13px;">
							<select name=z_yahoo_option_region>				 
								<option value='us' <?php if ($z_yahoo_option_region=='us') echo "selected=true"; ?>>United States</option>
								<option value='uk' <?php if ($z_yahoo_option_region=='uk') echo "selected=true"; ?>>United Kingdom</option>
								<option value='ca' <?php if ($z_yahoo_option_region=='ca') echo "selected=true"; ?>>Canada</option>
								<option value='au' <?php if ($z_yahoo_option_region=='au') echo "selected=true"; ?>>Australia</option>
								<option value='in' <?php if ($z_yahoo_option_region=='in') echo "selected=true"; ?>>India</option>
								<option value='es' <?php if ($z_yahoo_option_region=='es') echo "selected=true"; ?>>Spain</option>
								<option value='br' <?php if ($z_yahoo_option_region=='br') echo "selected=true"; ?>>Brazil</option>
								<option value='ar' <?php if ($z_yahoo_option_region=='ar') echo "selected=true"; ?>>Argentina</option>
								<option value='mx' <?php if ($z_yahoo_option_region=='mx') echo "selected=true"; ?>>Mexico</option>
								<option value='e1' <?php if ($z_yahoo_option_region=='e1') echo "selected=true"; ?>>In Espanol</option>
								<option value='it' <?php if ($z_yahoo_option_region=='it') echo "selected=true"; ?>>Italy</option>
								<option value='de' <?php if ($z_yahoo_option_region=='de') echo "selected=true"; ?>>Germany</option>
								<option value='fr' <?php if ($z_yahoo_option_region=='fr') echo "selected=true"; ?>>France</option>
								<option value='sg' <?php if ($z_yahoo_option_region=='sg') echo "selected=true"; ?>>Singapore</option>
							</select>
						 </td>
					</tr>
					<tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Determines the the timeline to pull from.">
							Date Range<br> </td>
						 <td align=right style="font-size:13px;">
							 <select name=z_yahoo_option_date_range>				 
							   <option value=all <?php if ($z_yahoo_option_date_range=='all') echo "selected=true"; ?>>Anytime</option>
							   <option value='7' <?php if ($z_yahoo_option_date_range=='7') echo "selected=true"; ?>>Within 7 Days</option>
							   <option value='7-30' <?php if ($z_yahoo_option_date_range=='7-30') echo "selected=true"; ?>>Within 7-30 Days</option>
							   <option value='40-60' <?php if ($z_yahoo_option_date_range=='30-60') echo "selected=true"; ?>>Within 30-60 Days</option>
							   <option value='60-90' <?php if ($z_yahoo_option_date_range=='60-90') echo "selected=true"; ?>>Within 60-90 Days</option>
							 </select>
						 </td>
					</tr>
					<tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Determines the nature of yahoo answer - question results.">
							Sorting<br> </td>
						 <td align=right style="font-size:13px;">
							 <select name=z_yahoo_option_sorting>				 
								<option value='relevance' <?php if ($z_yahoo_option_sorting=='relevance') echo "selected=true"; ?>>By Relevance</option>
								<option value='date_desc' <?php if ($z_yahoo_option_sorting=='date_desc') echo "selected=true"; ?>>By date, newest first</option>
								<option value='date_asc' <?php if ($z_yahoo_option_sorting=='date_asc') echo "selected=true"; ?>>By date, oldest first.</option>
							 </select>
						 </td>
					</tr>
					<tr>
						<td  align=left valign=top style="font-size:13px; width:300px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Define number of items to pull from RSS feed. Leave at 0 for unlimited.">
							 Limit Feed Results:<br>
						</td>
						<td align=right style="font-size:13px;">
							<input  name=limit_results size=5 value='0'>			
						</td>
					</tr>
					<tr>
						<td align=left valign=top style='font-size:13px;'>
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select of often you want items from this feed to be published.">
							Post Frequency:<br> 
						</td>
						<td align=right style="font-size:13px;">
							<select id='articles_selects_post_frequency' name='post_frequency' style='width:200px;'>
								<option <?php if($campaign_post_frequency==='min_1'){echo "selected=true";}?> value='min_1'>1 / minute</option>
								<option <?php if($campaign_post_frequency==='min_5'){echo "selected=true";}?> value='min_5'>1 / 5 minutes</option>
								<option <?php if($campaign_post_frequency==='min_10'){echo "selected=true";}?> value='min_10'>1 / 10 minutes</option>
								<option <?php if($campaign_post_frequency==='min_15'){echo "selected=true";}?> value='min_15'>1 / 15 minutes</option>
								<option <?php if($campaign_post_frequency==='min_20'){echo "selected=true";}?> value='min_20'>1 / 20 minutes</option>
								<option <?php if($campaign_post_frequency==='min_25'){echo "selected=true";}?> value='min_25'>1 / 25 minutes</option>
								<option <?php if($campaign_post_frequency==='min_30'){echo "selected=true";}?> value='min_30'>1 / 30 minutes</option>
								<option <?php if($campaign_post_frequency==='hour_1'){echo "selected=true";}?> value='hour_1'>1 / hour</option>
								<option <?php if($campaign_post_frequency==='1.1'){echo "selected=true";}?> value='1.1'>1 / day</option>
								<option <?php if($campaign_post_frequency==='2.1'){echo "selected=true";}?> value='2.1'>2 / day</option>
								<option <?php if($campaign_post_frequency==='3.1'){echo "selected=true";}?> value='3.1'>3 / day</option>
								<option <?php if($campaign_post_frequency==='4.1'){echo "selected=true";}?> value='4.1'>4 / day</option>
								<option <?php if($campaign_post_frequency==='5.1'){echo "selected=true";}?> value='5.1'>5 / day</option>
								<option <?php if($campaign_post_frequency==='6.1'){echo "selected=true";}?> value='6.1'>6 / day</option>
								<option <?php if($campaign_post_frequency==='7.1'){echo "selected=true";}?> value='7.1'>7 / day</option>
								<option <?php if($campaign_post_frequency==='8.1'){echo "selected=true";}?> value='8.1'>8 / day</option>
								<option <?php if($campaign_post_frequency==='9.1'){echo "selected=true";}?> value='9.1'>9 / day</option>
								<option <?php if($campaign_post_frequency==='10.1'){echo "selected=true";}?> value='10.1'>10 / day</option>
								<option <?php if($campaign_post_frequency==='11.1'){echo "selected=true";}?> value='11.1'>11 / day</option>
								<option <?php if($campaign_post_frequency==='12.1'){echo "selected=true";}?> value='12.1'>12 / day</option>
								<option <?php if($campaign_post_frequency==='13.1'){echo "selected=true";}?> value='13.1'>13 / day</option>
								<option <?php if($campaign_post_frequency==='14.1'){echo "selected=true";}?> value='14.1'>14 / day</option>
								<option <?php if($campaign_post_frequency==='15.1'){echo "selected=true";}?> value='15.1'>15 / day</option>
								<option <?php if($campaign_post_frequency==='16.1'){echo "selected=true";}?> value='16.1'>16 / day</option>
								<option <?php if($campaign_post_frequency==='1.2'){echo "selected=true";}?> value='1.2'>1 every 2 days</option>
								<option <?php if($campaign_post_frequency==='1.3'){echo "selected=true";}?> value='1.3'>1 every 3 days</option>
								<option <?php if($campaign_post_frequency==='1.4'){echo "selected=true";}?> value='1.4'>1 every 4 days</option>
								<option <?php if($campaign_post_frequency==='1.5'){echo "selected=true";}?> value='1.5'>1 every 5 days</option>
								<option <?php if($campaign_post_frequency==='1.6'){echo "selected=true";}?> value='1.6'>1 every 6 days</option>
								<option <?php if($campaign_post_frequency==='1.7'){echo "selected=true";}?> value='1.7'>1 every 7 days</option>
								<option <?php if($campaign_post_frequency==='1.8'){echo "selected=true";}?> value='1.8'>1 every 8 days</option>
								<option <?php if($campaign_post_frequency==='1.9'){echo "selected=true";}?> value='1.9'>1 every 9 days</option>
								<option <?php if($campaign_post_frequency==='1.10'){echo "selected=true";}?> value='1.10'>1 every 10 days</option>
								<option <?php if($campaign_post_frequency==='1.11'){echo "selected=true";}?> value='1.11'>1 every 11 days</option>
								<option <?php if($campaign_post_frequency==='1.12'){echo "selected=true";}?> value='1.12'>1 every 12 days</option>
								<option <?php if($campaign_post_frequency==='1.13'){echo "selected=true";}?> value='1.13'>1 every 13 days</option>
								<option <?php if($campaign_post_frequency==='1.14'){echo "selected=true";}?> value='1.14'>1 every 14 days</option>
								<option <?php if($campaign_post_frequency==='1.15'){echo "selected=true";}?> value='1.15'>1 every 15 days</option>
								<option <?php if($campaign_post_frequency==='1.16'){echo "selected=true";}?> value='1.16'>1 every 16 days</option>
								<option <?php if($campaign_post_frequency==='1.17'){echo "selected=true";}?> value='1.17'>1 every 17 days</option>
								<option <?php if($campaign_post_frequency==='1.18'){echo "selected=true";}?> value='1.18'>1 every 18 days</option>
								<option <?php if($campaign_post_frequency==='1.19'){echo "selected=true";}?> value='1.19'>1 every 19 days</option>
								<option <?php if($campaign_post_frequency==='1.20'){echo "selected=true";}?> value='1.20'>1 every 20 days</option>
								<option <?php if($campaign_post_frequency=='all'){echo "selected=true";}?> value='all'>All at once.</option>
								<option <?php if($campaign_post_frequency=='backdate'){echo "selected=true";}?> value='backdate'>Backdate.</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan=2>
							<table id='id_table_datepicker' width='100%'></table>
					</td>
				</tr>
				<tr>
					 <td  align=left valign=top style="font-size:13px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Would you like to remove hyperlinks found within the article?">
						Post Status:<br> </td>
					 <td align=right style="font-size:13px;">
						<select name=post_status>
							 <option value='publish' <?php if ($campaign_post_status=='publish'){ echo "selected=true"; } ?>>Publish</option>
							 <option value='draft' <?php if ($campaign_post_status=='draft'){ echo "selected=true"; } ?>>Draft</option>
							 <option value='private' <?php if ($campaign_post_status=='private'){ echo "selected=true"; } ?>>Private</option>
							 <option value='pending' <?php if ($campaign_post_status=='pending'){ echo "selected=true"; } ?>>Pending Review</option>
						 </select>	
					</td>
				</tr> 
				  <tr>
					 <td  align=left valign=top style="font-size:13px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Would you like to remove hyperlinks found within the article?">
						Link Options:<br> </td>
					 <td align=right style="font-size:13px;">
						<select name=strip_links>
							 <option value='0' <?php if ($campaign_strip_links=='0'){ echo "selected=true"; } ?>>Leave In-tact</option>
							 <option value='1' <?php if ($campaign_strip_links=='1'){ echo "selected=true"; } ?>>Strip Links</option>
							 <option value='2' <?php if ($campaign_strip_links=='2'){ echo "selected=true"; } ?>>Convert Anchor to Tag-Search.</option>
							 <option value='3' <?php if ($campaign_strip_links=='3'){ echo "selected=true"; } ?>>Convert Anchor to Keyword-Search. </option>
						 </select>	
					</td>
				  </tr>
				  <tr>
					 <td  align=left valign=top style="font-size:13px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Would you like to remove images found within the article?">
						Strip Images:<br> </td>
					 <td align=right style="font-size:13px;">
						<select name=strip_images>					
							<option value=1>on</option>
							<option value=0 selected=true>off</option>					
						 </select>	
					</td>
				  </tr>
				  <tr>
					 <td  align=left valign=top style="font-size:13px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Turn link cloaking on?">
						Cloak Links:<br> </td>
					 <td align=right style="font-size:13px;">
						<select name=cloak_links>
							<option value=1>on</option>
							<option value=0 selected=true>off</option>
						 </select>	
					</td>
				  </tr>
				  <tr>
					 <td  align=left valign=top style="font-size:13px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This will translate your content from English to the selected=true language. If english leave as English.">
						Language:<br> </td>
					 <td align=right style="font-size:13px;">
						<select id=articles_selects_languages name="language" tabindex="0">					
							<option value='no translation' <?php if ($campaign_language=='no translation'){ echo "selected=true"; } ?>>No Translation</option>
							<option value='af' <?php if ($campaign_language=='af'){ echo "selected=true"; } ?>>Afrikaans</option>
							<option value="sq" <?php if ($campaign_language=='sq'){ echo "selected=true"; } ?>>Albanian</option>
							<option value="ar" <?php if ($campaign_language=='ar'){ echo "selected=true"; } ?>>Arabic</option>
							<option value="be" <?php if ($campaign_language=='be'){ echo "selected=true"; } ?>>Belarusian</option>
							<option value="bg" <?php if ($campaign_language=='bg'){ echo "selected=true"; } ?>>Bulgarian</option>
							<option value="ca" <?php if ($campaign_language=='ca'){ echo "selected=true"; } ?>>Catalan</option>
							<option value="zh-CN" <?php if ($campaign_language=='zh-CN'){ echo "selected=true"; } ?>>Chinese</option>
							<option value="hr" <?php if ($campaign_language=='hr'){ echo "selected=true"; } ?>>Croatian</option>
							<option value="cs" <?php if ($campaign_language=='cs'){ echo "selected=true"; } ?>>Czech</option>
							<option value="da" <?php if ($campaign_language=='da'){ echo "selected=true"; } ?>>Danish</option>
							<option value="nl" <?php if ($campaign_language=='nl'){ echo "selected=true"; } ?>>Dutch</option>
							<option value="en" <?php if ($campaign_language=='en'){ echo "selected=true"; } ?>>English</option>
							<option value="et" <?php if ($campaign_language=='et'){ echo "selected=true"; } ?>>Estonian</option>
							<option value="tl" <?php if ($campaign_language=='tl'){ echo "selected=true"; } ?>>Filipino</option>
							<option value="fi" <?php if ($campaign_language=='fi'){ echo "selected=true"; } ?>>Finnish</option>
							<option value="fr" <?php if ($campaign_language=='fr'){ echo "selected=true"; } ?>>French</option>
							<option value="gl" <?php if ($campaign_language=='gl'){ echo "selected=true"; } ?>>Galician</option>
							<option value="de" <?php if ($campaign_language=='de'){ echo "selected=true"; } ?>>German</option>
							<option value="el" <?php if ($campaign_language=='el'){ echo "selected=true"; } ?>>Greek</option>
							<option value="iw" <?php if ($campaign_language=='iw'){ echo "selected=true"; } ?>>Hebrew</option>
							<option value="hi" <?php if ($campaign_language=='hi'){ echo "selected=true"; } ?>>Hindi</option>
							<option value="hu" <?php if ($campaign_language=='hu'){ echo "selected=true"; } ?>>Hungarian</option>
							<option value="is" <?php if ($campaign_language=='is'){ echo "selected=true"; } ?>>Icelandic</option>
							<option value="id" <?php if ($campaign_language=='id'){ echo "selected=true"; } ?>>Indonesian</option>
							<option value="ga" <?php if ($campaign_language=='ga'){ echo "selected=true"; } ?>>Irish</option>
							<option value="it" <?php if ($campaign_language=='it'){ echo "selected=true"; } ?>>Italian</option>
							<option value="ja" <?php if ($campaign_language=='jp'){ echo "selected=true"; } ?>>Japanese</option>
							<option value="ko" <?php if ($campaign_language=='ko'){ echo "selected=true"; } ?>>Korean</option>
							<option value="lv" <?php if ($campaign_language=='lv'){ echo "selected=true"; } ?>>Latvian</option>
							<option value="lt" <?php if ($campaign_language=='lt'){ echo "selected=true"; } ?>>Lithuanian</option>
							<option value="mk" <?php if ($campaign_language=='mk'){ echo "selected=true"; } ?>>Macedonian</option>
							<option value="ms" <?php if ($campaign_language=='ms'){ echo "selected=true"; } ?>>Malay</option>
							<option value="mt" <?php if ($campaign_language=='mt'){ echo "selected=true"; } ?>>Maltese</option>
							<option value="no" <?php if ($campaign_language=='no'){ echo "selected=true"; } ?>>Norwegian</option>
							<option value="fa" <?php if ($campaign_language=='fa'){ echo "selected=true"; } ?>>Persian</option>
							<option value="pl" <?php if ($campaign_language=='pl'){ echo "selected=true"; } ?>>Polish</option>
							<option value="pt" <?php if ($campaign_language=='pt'){ echo "selected=true"; } ?>>Portuguese</option>
							<option value="ro" <?php if ($campaign_language=='ro'){ echo "selected=true"; } ?>>Romanian</option>
							<option value="ru" <?php if ($campaign_language=='ru'){ echo "selected=true"; } ?>>Russian</option>
							<option value="sr" <?php if ($campaign_language=='sr'){ echo "selected=true"; } ?>>Serbian</option>
							<option value="sk" <?php if ($campaign_language=='sk'){ echo "selected=true"; } ?>>Slovak</option>
							<option value="sl" <?php if ($campaign_language=='sl'){ echo "selected=true"; } ?>>Slovenian</option>
							<option value="es" <?php if ($campaign_language=='es'){ echo "selected=true"; } ?>>Spanish</option>
							<option value="sw" <?php if ($campaign_language=='sw'){ echo "selected=true"; } ?>>Swahili</option>
							<option value="sv" <?php if ($campaign_language=='sv'){ echo "selected=true"; } ?>>Swedish</option>
							<option value="th" <?php if ($campaign_language=='th'){ echo "selected=true"; } ?>>Thai</option>
							<option value="tr" <?php if ($campaign_language=='tr'){ echo "selected=true"; } ?>>Turkish</option>
							<option value="uk" <?php if ($campaign_language=='uk'){ echo "selected=true"; } ?>>Ukrainian</option>
							<option value="vi" <?php if ($campaign_language=='vi'){ echo "selected=true"; } ?>>Vietnamese</option>
							<option value="cy" <?php if ($campaign_language=='cy'){ echo "selected=true"; } ?>>Welsh</option>
							<option value="yi" <?php if ($campaign_language=='yi'){ echo "selected=true"; } ?>>Yiddish</option>
						</select>
					 </td>
					</tr>	
					<tr>
						<td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Attempt to re-write text to make it appear unique.">
							Spin Text:<br> </td>
						<td align=right style="font-size:13px;">
							 <select name=spin_text>
								<option value=1>on</option>
								<option value=0 selected=true>off</option>						
							</select>			 
						</td>
					</tr>
					<tr>
						<td  align=left valign=top style='font-size:13px;'>
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="All posts from this feel will go into this category. If you only want select items, then use the include and exclude keywords to target your items.">
							Post Author:<br> 
						</td>
						<td align=right style='font-size:13px;'>
							<select name=author>
								<?php
								if ($campaign_author)
								{
									foreach ($authors_id as $k=>$v)
									{
										if ($campaign_author==$v)
										{
											echo "<option value=$v selected=true>$authors_usernames[$k]</option>";
										}
										else
										{
											echo "<option value=$v >$authors_usernames[$k]</option>";
										}
									}
								}
								else
								{
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
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td  align=left valign=top style='font-size:13px;'>
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="All posts from this feel will go into this category. If you only want select items, then use the include and exclude keywords to target your items.">
							Category:<br> 
						</td>
						<td  align=right style='font-size:13px;' id='id_td_selects_category'>
							<?php 
							 wp_dropdown_categories(array(selected=>$campaign_category,name=>'category' ,hierarchical=>1,id=>'articles_selects_cats',hide_empty=>0)); 
							 ?>
						</td>
					</tr>
					<tr>
						<td colspan=2>
						<table id="id_table_comments_include" width=100%>
							<tr>			 
								<td  align=left valign=top style='font-size:13px;'>
									<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="If comments are available for this module should we include them?">
									Include Comments:<br> 
								</td>
								<td  align=right style='font-size:13px;'>
									<select name=comments_include id='id_comments_include_select' class='comments_include_select'>
											<option value=1>on</option>";
											<option value=0 selected=true>off</option>
									</select>	
								</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td align=center colspan=2 style='font-size:13px;' valign=top >
							<button class='class_inputs' id='id_submit'  value='Create Campaigns'><span>Create Campaigns</span></button>
							</td>
					</tr>					
				</table>
			</td>
			<td valign='top'style=''>
				


				<?php
					include('./../includes/i_templates_setup.php');
					
					include('./../includes/i_tokens_blogsense_dialogs.php');
					
					if ($phpBay==1)
					{
						include('./../includes/i_tokens_phpbay_dialogs.php');
					}
					
					if ($phpZon==1)
					{
						include('./../includes/i_tokens_phpzon_dialogs.php');
					}
					
					if ($wpMage==1)
					{
						include('./../includes/i_tokens_wpmage_dialogs.php');
					}	
					
					if ($wpRobot==1)
					{
						include('./../includes/i_tokens_wprobot_dialogs.php');
					}	
				
				?>
				
			</td>
		</tr>
		</table>
	</form>
	</div>
</body>
</html>
	<?php
}
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//SOURCES MASS CREATE
if ($module_type=='sources'&&!$nature)
{
   	//reset array variables
	$query = "SELECT content FROM ".$table_prefix."post_templates WHERE `type` IN ( 'default_rss_post_template' ,'default_title_template') ORDER BY type ASC";
	$result = mysql_query($query);
	if (!$result){echo $query; exit;}
	while ($arr = mysql_fetch_array($result))
	{
		$new_array[] = $arr[0];
	}
	$default_post_template = $new_array[0];
	$default_title_template = $new_array[1];
	
	$query = "SELECT * FROM ".$table_prefix."sourcedata";
	$result = mysql_query($query);
	if (!$result){echo $query; exit;}
	
	
	function s_encode($input)
	{
	  $input = htmlspecialchars($input);
	  $input = str_replace(chr(13), "**13**", $input);
	  $input = str_replace(chr(10), "**10**", $input);
	  return $input;
	}

	while ($array = mysql_fetch_array($result))
	{
		$source_id[] = s_encode($array['id'], ENT_QUOTES);
		$source_url[] = s_encode($array['source_url'], ENT_QUOTES);
		$footprint[] = s_encode($array['footprint'], ENT_QUOTES);
		$title_start[] = s_encode($array['title_start'], ENT_QUOTES);
		$title_start_backup_1[] = s_encode($array['title_start_backup_1'], ENT_QUOTES);
		$title_start_backup_2[] = s_encode($array['title_start_backup_2'], ENT_QUOTES);
		$title_end[] = s_encode($array['title_end'], ENT_QUOTES);
		$content_start[] = s_encode($array['content_start'], ENT_QUOTES);
		$content_start_backup_1[] = s_encode($array['content_start_backup_1'], ENT_QUOTES);
		$content_start_backup_2[] = s_encode($array['content_start_backup_2'], ENT_QUOTES);
		$content_end[] = s_encode($array['content_end'], ENT_QUOTES);
		$content_end_backup_1[] = s_encode($array['content_end_backup_1'], ENT_QUOTES);
		$content_end_backup_2[] = s_encode($array['content_end_backup_2'], ENT_QUOTES);
		$comments_status[] = s_encode($array['comments_status'], ENT_QUOTES);
		$comments_name_start[] = s_encode($array['comments_name_start'], ENT_QUOTES);
		$comments_name_end[] = s_encode($array['comments_name_end'], ENT_QUOTES);
		$comments_content_start[] = s_encode($array['comments_content_start'], ENT_QUOTES);
		$comments_content_end[] = s_encode($array['comments_content_end'], ENT_QUOTES);
		$regex_search[] = explode("***r***", s_encode($array['regex_search'], ENT_QUOTES));
		$regex_replace[] = explode("***r***", s_encode($array['regex_replace'], ENT_QUOTES));
	}	


	?>
	<html>
		<head>
		<script type="text/javascript" src="./../includes/jquery.js"></script>
		<link rel="stylesheet" type="text/css" href="./../includes/jquery-ui-1.7.2.custom.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
		<script type="text/javascript"> 
		$(document).ready(function() 
		{
			$("#id_templating").accordion({
					autoHeight: false,
					collapsible: true,
					 active: 1
			});
			
			$('.class_inputs').attr('disabled', 'disabled');
			
			$("#id_button_create_campaign").click(function(){
				$("#nature").val("create");
				$("#form_yahoo").submit();
			 });
			 
			 $("#id_keyword_options").change(function(){	   
				var input =$(this).val();
				if (input=='categories')
				{
				
				   $('.class_inputs').removeAttr('disabled');
				   var cats = "<?php echo $categories_list; ?>";
				   cats = cats.replace(/,/g,'\n');
				   $("#id_keywords").val(cats);
				}
				else if (input=='custom_list')
				{
					$('.class_inputs').removeAttr('disabled');
					var clone = "<?php echo $categories_list; ?>";
					$("#id_keywords").val('');
				}
				else
				{
					$('.class_inputs').attr('disabled', 'disabled');
					
				}
			});
			
			$("img.add_articles_string_edit").live("click" ,function(){
			   var id = this.id.replace('articles_string_edit_button_','');
			   $('#id_table_regex tr:last').after('<tr><td  align=left style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><input size=20 name=\"regex_search[]\" ></td><td  align=right style=\"font-size:13px;\"><input size=20 name=\"regex_replace[]\"></td></tr>');
			});
			
			$("img.advanced_expand").live("click" ,function(){
				   
				   var src = $("img.advanced_expand").attr("src");
				   
				   if (src=="./../nav/plus.gif")
				   {
				   $('#advanced_settings').fadeIn("fast");
				   $("#expand_button").attr("src", "./../nav/minus.gif");
				   }
				   else
				   {
				   $('#advanced_settings').fadeOut("fast");
				   $("#expand_button").attr("src", "./../nav/plus.gif");
				   }
			});
			$('.credit_sources_selects').val('0');
			$(".credit_sources_selects").change(function(){	   
			   var input =$(this).val();
			   if (input==1)
			   {
				   var clone = "<tr><td  align=left valign=top style='font-size:13px;'>&nbsp;&nbsp&nbsp;<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='This text will be the anchor text for your link back.'>Anchor Text:<br> </td><td align=right style='font-size:13px;'><input  name=credit_source_text size=30 value='Check out the original source here.'></td></tr><tr><td  align=left valign=top style='font-size:13px;'>&nbsp;&nbsp&nbsp;<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Should the link back to the article be a nofollow link?'>Nofollow?<br> </td><td align=right style='font-size:13px;'><select name=credit_source_nofollow><option value=1>on</option><option value=0 selected>off</option></select></td></tr>";
				   $("#credit_sources").append(clone);
			   }
			   else
			   {
				  $("#credit_sources tr:last").remove();
				  $("#credit_sources tr:last").remove();
			   }
			});
			
			$("#id_sources").change(function(){
			var site = $('#id_sources').val();
			switch (site){
				case "x":				
						$("#id_table_regex").find("tr").remove();
						$('#id_form_nature').val('x');					
						break;
				<?php
				foreach ($source_url as $key=>$v)
				{
					echo" case '$source_url[$key]':
					$('#id_table_regex').find('tr').remove();			
					$('#id_table_regex').append('<tr><td colspan=2 align=middle style=\"font-size:13px;\"><a href=\"./../includes/pdfs/Using_Regular_Expressions.pdf\" target=_blank><img src=\"./../nav/tip.png\" style=\"cursor:pointer;\" border=0 title=\"Information on Regular Expressions.\" border=0></a>Regex Search & Replace </td></tr><tr><td  align=middle style=\"font-size:11px;color:#aaaaaa\"><i>Search String</i></td><td  align=middle style=\"font-size:11px;color:#aaaaaa\"><i>Replace String</i></td></tr><tr><td colspan=2 align=middle style=\"font-size:13px;\"><center><img src=\"./../nav/add.png\" style=\"cursor:pointer;\" id=\"articles_string_edit_button_0\" class=\"add_articles_string_edit\"></center></td></tr>');
					";
					
					$count_regex_search = count($regex_search);
					
					
					
					if (strlen($regex_search[$key][0])>1)
					{			    
						foreach ($regex_search[$key] as $k=>$v)
						{
							echo "$('#id_table_regex tr:last').after('<tr><td  align=left style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><input size=20 name=\"regex_search[]\" value=\"$v\"></td><td  align=right style=\"font-size:13px;\"><input size=20 name=\"regex_replace[]\" value=\"".$regex_replace[$key][$k]."\"></td></tr>');";
				
						}
					}
					echo "break;";
				}
				?>
				}			
				 
			});
			
			$( "#datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
			
			$('#id_query').bind('keyup keypress blur', function() {
				var a = $('#id_campaign_name').val();
				var b = $('#id_query').val();
				b = b.slice(0, -1);

				if (a==b)
				{
					$('#id_campaign_name').val($(this).val());
				}				
			});
			
			$(".comments_include_select").change(function(){	   
			   var input =$(this).val();
			   if (input==1)
			   {
				   var clone = "<tr><td  align=left valign=top style='font-size:13px;'>&nbsp;&nbsp&nbsp;<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Limit the number of comments allowed to be auto-scheduled. Leave at 0 to schedule/publish all available comments.'> Limit Comments:<br> </td><td align=right style='font-size:13px;'><input  name=comments_limit size=1 value='0'></td></tr>";
				   $("#id_table_comments_include").append(clone);
			   }
			   else
			   {
				  $("#id_table_comments_include tr:last").remove();
			   }
			});
		
			<?php
			//add hook jquery
			include_once('./../includes/i_hook_jquery.js');
			?>
		});
		</script>
		<style type="text/css" media="screen">
				ul#grid li {
					list-style: none outside;
					float: left;
					margin-right: 20px;
					margin-bottom: 20px;
					font-size: 50px;
					width: 5em;
					height: 5em;
					line-height: 5em;
					text-align: center;
				}
					ul#grid li img {
						vertical-align: middle;
					}
				.ui-slider-handle { left: 45%; }
				
				.token_items 
				{
					font-size:11px;
					color:grey;
					text-decoration:none;
				}
			
				.token_items a:active
				{
					font-size:11px;
				}
				.class_custom_var_post
				{
					font-size:11px;
					color:grey;
					text-decoration:none;
				}
				.class_custom_var_post:hover
				{
					font-size:12px;
				}
				.class_custom_var_post a:active
				{
					font-size:11px;
				}
		</style>
		</head>
		<body style="font-family:Khmer UI;">
		<form action="" id="form_yahoo" name="form_yahoo" method=POST>
		<input type=hidden name=submit_nature id=nature value='create'>
		<input type=hidden name=module_type  value='<?php echo $module_type; ?>'>

		<table width="100%">
			<tr>
				<td width='50%' valign='top'>
					<center>		
							   
					<div style="font-size:14px;width:500;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">Sources Campaigns : Mass Import
						<div style='float:right;'>
						</div>
					</div>
					<hr width=500 style="color:#eeeeee;background-color:#eeeeee;">
					 
					<table width=500 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee;"> 
						<tr>
							 <td  align=left valign=top style="font-size:13px;">
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select the mode of importing a batch of keywords for campaign creation">
								Keyword Options:<br>
							 </td>
							 <td align=right style="font-size:13px;">		
								<select  id='id_keyword_options' name='keyword_option' style='width:341px;'>
									<option value='x'>Please Select</option>
									<option value='categories'>Use Categories as  Base Keywords</option>
									<option value='custom_list'>Use a List of Custom Keywords</option>
									</select>
							 </td>
						</tr>
						<tr>
							<td  align=left valign=top style="font-size:13px; width:300px;">
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This keyword if filled out will be added onto each keyword gathered when creating multiple campaigns. eg: 'keyword keyword_tag'">
								 Keyword Tag:<br>
							</td>
							<td align=right style="font-size:13px;">
								<input class='class_inputs' id='id_keyword_tag'  name='keyword_tag'  size=52 value=''>			
							</td>
						</tr>
						<tr>
							<td  align=left valign=top style="font-size:13px; width:300px;">
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="1 per line.">
								 Keywords:<br>
							</td>
							<td align=right style="font-size:13px;">
								<textarea class='class_inputs'  name='batch_keywords' id='id_keywords' cols=40 rows=8 >
								</textarea>
							</td>
						</tr>			
						<tr>
							<td colspan=2 align=left valign=top style="font-size:13px; width:300px;">
								<br>	
							</td>
						</tr>
						<tr>
						 <td  align=left valign=top style="font-size:13px; width:300px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Input RSS URL here.">
								 Source:<br>
						 </td>
						 <td align=right style="font-size:13px;">
							<select name="source" id="id_sources">
								
								<?php
								echo "<option value='x' selected=true>Please Select Source</option>";
								foreach ($source_url as $k=>$v)
								{
									echo "<option value='$v'>$v</option>";
								}
								?>
							</select>
								
						 </td>
					  </tr>		  
					  <tr>
						 <td  align=left valign=top style="font-size:13px; width:300px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Define number of items to pull from RSS feed. Leave at 0 for unlimited.">
								 Limit Results:<br>
						 </td>
						 <td align=right style="font-size:13px;">
							 <input  name=limit_results size=5 value='0'>			
						 </td>
						</tr>
					 	<tr>
							<td align=left valign=top style='font-size:13px;'>
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select of often you want items from this feed to be published.">
								Post Frequency:<br> 
							</td>
							 <td align=right style="font-size:13px;">
								<select id='articles_selects_post_frequency' name='post_frequency' style='width:200px;'>
									<option <?php if($campaign_post_frequency==='1.1'){echo "selected=true";}?> value='1.1'>1 / day</option>
									<option <?php if($campaign_post_frequency==='2.1'){echo "selected=true";}?> value='2.1'>2 / day</option>
									<option <?php if($campaign_post_frequency==='3.1'){echo "selected=true";}?> value='3.1'>3 / day</option>
									<option <?php if($campaign_post_frequency==='4.1'){echo "selected=true";}?> value='4.1'>4 / day</option>
									<option <?php if($campaign_post_frequency==='5.1'){echo "selected=true";}?> value='5.1'>5 / day</option>
									<option <?php if($campaign_post_frequency==='6.1'){echo "selected=true";}?> value='6.1'>6 / day</option>
									<option <?php if($campaign_post_frequency==='7.1'){echo "selected=true";}?> value='7.1'>7 / day</option>
									<option <?php if($campaign_post_frequency==='8.1'){echo "selected=true";}?> value='8.1'>8 / day</option>
									<option <?php if($campaign_post_frequency==='9.1'){echo "selected=true";}?> value='9.1'>9 / day</option>
									<option <?php if($campaign_post_frequency==='10.1'){echo "selected=true";}?> value='10.1'>10 / day</option>
									<option <?php if($campaign_post_frequency==='11.1'){echo "selected=true";}?> value='11.1'>11 / day</option>
									<option <?php if($campaign_post_frequency==='12.1'){echo "selected=true";}?> value='12.1'>12 / day</option>
									<option <?php if($campaign_post_frequency==='13.1'){echo "selected=true";}?> value='13.1'>13 / day</option>
									<option <?php if($campaign_post_frequency==='14.1'){echo "selected=true";}?> value='14.1'>14 / day</option>
									<option <?php if($campaign_post_frequency==='15.1'){echo "selected=true";}?> value='15.1'>15 / day</option>
									<option <?php if($campaign_post_frequency==='16.1'){echo "selected=true";}?> value='16.1'>16 / day</option>
									<option <?php if($campaign_post_frequency==='1.2'){echo "selected=true";}?> value='1.2'>1 every 2 days</option>
									<option <?php if($campaign_post_frequency==='1.3'){echo "selected=true";}?> value='1.3'>1 every 3 days</option>
									<option <?php if($campaign_post_frequency==='1.4'){echo "selected=true";}?> value='1.4'>1 every 4 days</option>
									<option <?php if($campaign_post_frequency==='1.5'){echo "selected=true";}?> value='1.5'>1 every 5 days</option>
									<option <?php if($campaign_post_frequency==='1.6'){echo "selected=true";}?> value='1.6'>1 every 6 days</option>
									<option <?php if($campaign_post_frequency==='1.7'){echo "selected=true";}?> value='1.7'>1 every 7 days</option>
									<option <?php if($campaign_post_frequency==='1.8'){echo "selected=true";}?> value='1.8'>1 every 8 days</option>
									<option <?php if($campaign_post_frequency==='1.9'){echo "selected=true";}?> value='1.9'>1 every 9 days</option>
									<option <?php if($campaign_post_frequency==='1.10'){echo "selected=true";}?> value='1.10'>1 every 10 days</option>
									<option <?php if($campaign_post_frequency==='1.11'){echo "selected=true";}?> value='1.11'>1 every 11 days</option>
									<option <?php if($campaign_post_frequency==='1.12'){echo "selected=true";}?> value='1.12'>1 every 12 days</option>
									<option <?php if($campaign_post_frequency==='1.13'){echo "selected=true";}?> value='1.13'>1 every 13 days</option>
									<option <?php if($campaign_post_frequency==='1.14'){echo "selected=true";}?> value='1.14'>1 every 14 days</option>
									<option <?php if($campaign_post_frequency==='1.15'){echo "selected=true";}?> value='1.15'>1 every 15 days</option>
									<option <?php if($campaign_post_frequency==='1.16'){echo "selected=true";}?> value='1.16'>1 every 16 days</option>
									<option <?php if($campaign_post_frequency==='1.17'){echo "selected=true";}?> value='1.17'>1 every 17 days</option>
									<option <?php if($campaign_post_frequency==='1.18'){echo "selected=true";}?> value='1.18'>1 every 18 days</option>
									<option <?php if($campaign_post_frequency==='1.19'){echo "selected=true";}?> value='1.19'>1 every 19 days</option>
									<option <?php if($campaign_post_frequency==='1.20'){echo "selected=true";}?> value='1.20'>1 every 20 days</option>
									<option <?php if($campaign_post_frequency=='all'){echo "selected=true";}?> value='all'>All at once.</option>
									<option <?php if($campaign_post_frequency=='backdate'){echo "selected=true";}?> value='backdate'>Backdate.</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<table id='id_table_datepicker' width='100%'></table>

							</td>
					  </tr>
					  <tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Would you like to remove hyperlinks found within the article?">
							Post Status:<br> </td>
						 <td align=right style="font-size:13px;">
							<select name=post_status>
								 <option value='publish' <?php if ($campaign_post_status=='publish'){ echo "selected=true"; } ?>>Publish</option>
								 <option value='draft' <?php if ($campaign_post_status=='draft'){ echo "selected=true"; } ?>>Draft</option>
								 <option value='private' <?php if ($campaign_post_status=='private'){ echo "selected=true"; } ?>>Private</option>
								 <option value='pending' <?php if ($campaign_post_status=='pending'){ echo "selected=true"; } ?>>Pending Review</option>
							 </select>	
						</td>
					</tr> 
					  <tr>
						<td colspan=2>
							<table id="credit_sources" width=100%>
								<tr>			 
									<td  align=left valign=top style='font-size:13px;'>
										<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Provide a link to original source at footer of posts.">
										Credit Sources:<br> 
									</td>
									<td  align=right style='font-size:13px;'>
										<select name=credit_source id='credit_sources_id' class='credit_sources_selects'>
											<option value=1>on</option>
											<option value=0 selected=true>off</option>
										</select>	
									</td>
								</tr>
				
							</table>
						</td>
					  </tr>
					  <tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Would you like to remove hyperlinks found within the article?">
							Link Options:<br> </td>
						 <td align=right style="font-size:13px;">
							<select name=strip_links>
								 <option value='0' <?php if ($campaign_strip_links=='0'){ echo "selected=true"; } ?>>Leave In-tact</option>
								 <option value='1' <?php if ($campaign_strip_links=='1'){ echo "selected=true"; } ?>>Strip Links</option>
								 <option value='2' <?php if ($campaign_strip_links=='2'){ echo "selected=true"; } ?>>Convert Anchor to Tag-Search.</option>
								 <option value='3' <?php if ($campaign_strip_links=='3'){ echo "selected=true"; } ?>>Convert Anchor to Keyword-Search. </option>
							</select>		
						</td>
					  </tr>
					  <tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Would you like to remove images found within the article?">
							Strip Images:<br> </td>
						 <td align=right style="font-size:13px;">
							<select name=strip_images>
								<option value=1 >on</option>
								<option value=0 selected=true>off</option>
							 </select>	
						</td>
					  </tr>
					  <tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Turn link cloaking on?">
							Cloak Links:<br> </td>
						 <td align=right style="font-size:13px;">
							<select name=cloak_links>
								<option value=1 >on</option>
								<option value=0 selected=true>off</option>
							 </select>	
						</td>
					  </tr>
					  <tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This will translate your content from English to the selected=true language. If english leave as English.">
							Language:<br> </td>
						 <td align=right style="font-size:13px;">
							<select id=articles_selects_languages name="language" tabindex="0">
									<option value='no translation' <?php if ($campaign_language=='no translation'){ echo "selected=true"; } ?>>No Translation</option>
								<option value='af' <?php if ($campaign_language=='af'){ echo "selected=true"; } ?>>Afrikaans</option>
								<option value="sq" <?php if ($campaign_language=='sq'){ echo "selected=true"; } ?>>Albanian</option>
								<option value="ar" <?php if ($campaign_language=='ar'){ echo "selected=true"; } ?>>Arabic</option>
								<option value="be" <?php if ($campaign_language=='be'){ echo "selected=true"; } ?>>Belarusian</option>
								<option value="bg" <?php if ($campaign_language=='bg'){ echo "selected=true"; } ?>>Bulgarian</option>
								<option value="ca" <?php if ($campaign_language=='ca'){ echo "selected=true"; } ?>>Catalan</option>
								<option value="zh-CN" <?php if ($campaign_language=='zh-CN'){ echo "selected=true"; } ?>>Chinese</option>
								<option value="hr" <?php if ($campaign_language=='hr'){ echo "selected=true"; } ?>>Croatian</option>
								<option value="cs" <?php if ($campaign_language=='cs'){ echo "selected=true"; } ?>>Czech</option>
								<option value="da" <?php if ($campaign_language=='da'){ echo "selected=true"; } ?>>Danish</option>
								<option value="nl" <?php if ($campaign_language=='nl'){ echo "selected=true"; } ?>>Dutch</option>
								<option value="en" <?php if ($campaign_language=='en'){ echo "selected=true"; } ?>>English</option>
								<option value="et" <?php if ($campaign_language=='et'){ echo "selected=true"; } ?>>Estonian</option>
								<option value="tl" <?php if ($campaign_language=='tl'){ echo "selected=true"; } ?>>Filipino</option>
								<option value="fi" <?php if ($campaign_language=='fi'){ echo "selected=true"; } ?>>Finnish</option>
								<option value="fr" <?php if ($campaign_language=='fr'){ echo "selected=true"; } ?>>French</option>
								<option value="gl" <?php if ($campaign_language=='gl'){ echo "selected=true"; } ?>>Galician</option>
								<option value="de" <?php if ($campaign_language=='de'){ echo "selected=true"; } ?>>German</option>
								<option value="el" <?php if ($campaign_language=='el'){ echo "selected=true"; } ?>>Greek</option>
								<option value="iw" <?php if ($campaign_language=='iw'){ echo "selected=true"; } ?>>Hebrew</option>
								<option value="hi" <?php if ($campaign_language=='hi'){ echo "selected=true"; } ?>>Hindi</option>
								<option value="hu" <?php if ($campaign_language=='hu'){ echo "selected=true"; } ?>>Hungarian</option>
								<option value="is" <?php if ($campaign_language=='is'){ echo "selected=true"; } ?>>Icelandic</option>
								<option value="id" <?php if ($campaign_language=='id'){ echo "selected=true"; } ?>>Indonesian</option>
								<option value="ga" <?php if ($campaign_language=='ga'){ echo "selected=true"; } ?>>Irish</option>
								<option value="it" <?php if ($campaign_language=='it'){ echo "selected=true"; } ?>>Italian</option>
								<option value="ja" <?php if ($campaign_language=='jp'){ echo "selected=true"; } ?>>Japanese</option>
								<option value="ko" <?php if ($campaign_language=='ko'){ echo "selected=true"; } ?>>Korean</option>
								<option value="lv" <?php if ($campaign_language=='lv'){ echo "selected=true"; } ?>>Latvian</option>
								<option value="lt" <?php if ($campaign_language=='lt'){ echo "selected=true"; } ?>>Lithuanian</option>
								<option value="mk" <?php if ($campaign_language=='mk'){ echo "selected=true"; } ?>>Macedonian</option>
								<option value="ms" <?php if ($campaign_language=='ms'){ echo "selected=true"; } ?>>Malay</option>
								<option value="mt" <?php if ($campaign_language=='mt'){ echo "selected=true"; } ?>>Maltese</option>
								<option value="no" <?php if ($campaign_language=='no'){ echo "selected=true"; } ?>>Norwegian</option>
								<option value="fa" <?php if ($campaign_language=='fa'){ echo "selected=true"; } ?>>Persian</option>
								<option value="pl" <?php if ($campaign_language=='pl'){ echo "selected=true"; } ?>>Polish</option>
								<option value="pt" <?php if ($campaign_language=='pt'){ echo "selected=true"; } ?>>Portuguese</option>
								<option value="ro" <?php if ($campaign_language=='ro'){ echo "selected=true"; } ?>>Romanian</option>
								<option value="ru" <?php if ($campaign_language=='ru'){ echo "selected=true"; } ?>>Russian</option>
								<option value="sr" <?php if ($campaign_language=='sr'){ echo "selected=true"; } ?>>Serbian</option>
								<option value="sk" <?php if ($campaign_language=='sk'){ echo "selected=true"; } ?>>Slovak</option>
								<option value="sl" <?php if ($campaign_language=='sl'){ echo "selected=true"; } ?>>Slovenian</option>
								<option value="es" <?php if ($campaign_language=='es'){ echo "selected=true"; } ?>>Spanish</option>
								<option value="sw" <?php if ($campaign_language=='sw'){ echo "selected=true"; } ?>>Swahili</option>
								<option value="sv" <?php if ($campaign_language=='sv'){ echo "selected=true"; } ?>>Swedish</option>
								<option value="th" <?php if ($campaign_language=='th'){ echo "selected=true"; } ?>>Thai</option>
								<option value="tr" <?php if ($campaign_language=='tr'){ echo "selected=true"; } ?>>Turkish</option>
								<option value="uk" <?php if ($campaign_language=='uk'){ echo "selected=true"; } ?>>Ukrainian</option>
								<option value="vi" <?php if ($campaign_language=='vi'){ echo "selected=true"; } ?>>Vietnamese</option>
								<option value="cy" <?php if ($campaign_language=='cy'){ echo "selected=true"; } ?>>Welsh</option>
								<option value="yi" <?php if ($campaign_language=='yi'){ echo "selected=true"; } ?>>Yiddish</option>
							</select>
						 </td>
						</tr>	
						<tr>
							<td  align=left valign=top style="font-size:13px;">
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Attempt to re-write text to make it appear unique.">
								Spin Text:<br> </td>
							<td align=right style="font-size:13px;">
								 <select name=spin_text>
									<option value=1 selected=true>on</option>
									<option value=0 >off</option>
								</select>			 
							</td>
						</tr>
						<tr>
							<td  align=left valign=top style='font-size:13px;'>
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="All posts from this feel will go into this category. If you only want select items, then use the include and exclude keywords to target your items.">
								Post Author:<br> 
							</td>
							<td align=right style='font-size:13px;'>
								<select name=author>
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
						<tr>
							<td  align=left valign=top style='font-size:13px;'>
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="All posts from this feel will go into this category. If you only want select items, then use the include and exclude keywords to target your items.">
								Category:<br> 
							</td>
							<td  align=right style='font-size:13px;' id='id_td_selects_category'>
								<?php 
								wp_dropdown_categories(array(selected=>$campaign_category,name=>'category' ,hierarchical=>1,id=>'articles_selects_cats',hide_empty=>0)); 
								?>
							</td>
						</tr>
						<tr>
							<td colspan=2>
							<table id="id_table_comments_include" width=100%>
								<tr>			 
									<td  align=left valign=top style='font-size:13px;'>
										<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="If comments are available for this module should we include them?">
										Include Comments:<br> 
									</td>
									<td  align=right style='font-size:13px;'>
										<select name=comments_include id='id_comments_include_select' class='comments_include_select'>
											<option value=1>on</option>";
											<option value=0 selected=true>off</option>
										</select>	
									</td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
								<td align=center colspan=2 style='font-size:13px;' valign=top >
									<button class='class_inputs' id='id_submit'  value='Create Campaigns'><span>Create Campaigns</span></button>
									</td>
						</tr>
					</table>
				</td>
				
				<td valign='top'style=''>
					
					


					<?php
						include('./../includes/i_templates_setup.php');
						 
						include('./../includes/i_tokens_blogsense_dialogs.php');
						
						if ($phpBay==1)
						{
							include('./../includes/i_tokens_phpbay_dialogs.php');
						}
						
						if ($phpZon==1)
						{
							include('./../includes/i_tokens_phpzon_dialogs.php');
						}
						
						if ($wpMage==1)
						{
							include('./../includes/i_tokens_wpmage_dialogs.php');
						}	
						
						if ($wpRobot==1)
						{
							include('./../includes/i_tokens_wprobot_dialogs.php');
						}	
					
					?>
					
				</td>
			</tr>
		</table>
</form>
</div>
</body>
</html>
				
	<?php
}
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//VIDEO MASS CREATE
if ($module_type=='video'&&!$nature)
{
	//reset array variables
	$query = "SELECT content FROM ".$table_prefix."post_templates WHERE `type` IN ( 'default_video_post_template' ,'default_title_template') ORDER BY type ASC";
	$result = mysql_query($query);
	if (!$result){echo $query; exit;}
	while ($arr = mysql_fetch_array($result))
	{
		$new_array[] = $arr[0];
	}
	$default_title_template = $new_array[0];
	$default_post_template = $new_array[1];

	//echo $default_rss_post_template;exit;

	?>
	<html>
		<head>

		<script type="text/javascript" src="./../includes/jquery.js"></script>
		<link rel="stylesheet" type="text/css" href="./../includes/jquery-ui-1.7.2.custom.css">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
		<script type="text/javascript"> 
		$(document).ready(function() 
		{
			$("#id_templating").accordion({
					autoHeight: false,
					collapsible: true,
					 active: 1
			});
			
			$('.class_inputs').attr('disabled', 'disabled');
			
			$("#id_button_create_campaign").click(function(){
				$("#nature").val("create");
				$("#form_yahoo").submit();
			 });
			 
			 $("#id_keyword_options").change(function(){	   
				var input =$(this).val();
				if (input=='categories')
				{
				
				   $('.class_inputs').removeAttr('disabled');
				   var cats = "<?php echo $categories_list; ?>";
				   cats = cats.replace(/,/g,'\n');
				   $("#id_keywords").val(cats);
				}
				else if (input=='custom_list')
				{
					$('.class_inputs').removeAttr('disabled');
					var clone = "<?php echo $categories_list; ?>";
					$("#id_keywords").val('');
				}
				else
				{
					$('.class_inputs').attr('disabled', 'disabled');
					
				}
			});
			
			$("img.add_articles_string_edit").live("click" ,function(){
			   var id = this.id.replace('articles_string_edit_button_','');
			   $('#id_table_regex tr:last').after('<tr><td  align=left style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><input size=20 name=\"regex_search[]\" ></td><td  align=right style=\"font-size:13px;\"><input size=20 name=\"regex_replace[]\"></td></tr>');
			});
			
			$(".comments_include_select").change(function(){	   
			   var input =$(this).val();
			   if (input==1)
			   {
				   var clone = "<tr><td  align=left valign=top style='font-size:13px;'>&nbsp;&nbsp&nbsp;<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Limit the number of comments allowed to be auto-scheduled. Leave at 0 to schedule/publish all available comments.'> Limit Comments:<br> </td><td align=right style='font-size:13px;'><input  name=comments_limit size=1 value='0'></td></tr>";
				   $("#id_table_comments_include").append(clone);
			   }
			   else
			   {
				  $("#id_table_comments_include tr:last").remove();
			   }
			});
			
			$( "#datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
			
			<?php
			//add hook jquery
			include_once('./../includes/i_hook_jquery.js');
			?>
			
		});
		</script>
		<style type="text/css" media="screen">
				ul#grid li {
					list-style: none outside;
					float: left;
					margin-right: 20px;
					margin-bottom: 20px;
					font-size: 50px;
					width: 5em;
					height: 5em;
					line-height: 5em;
					text-align: center;
				}
					ul#grid li img {
						vertical-align: middle;
					}
				.ui-slider-handle { left: 45%; }
				
				.token_items 
				{
					font-size:11px;
					color:grey;
					text-decoration:none;
				}
			
				.token_items a:active
				{
					font-size:11px;
				}
				.class_custom_var_post
				{
					font-size:11px;
					color:grey;
					text-decoration:none;
				}
				.class_custom_var_post:hover
				{
					font-size:12px;
				}
				.class_custom_var_post a:active
				{
					font-size:11px;
				}
		</style>
		</head>
		<body style="font-family:Khmer UI;">
		<form action="" id="form_yahoo" name="form_yahoo" method=POST>
		<input type=hidden name=submit_nature id=nature value='create'>
		<input type=hidden name=module_type  value='<?php echo $module_type; ?>'>

		
		<table width="100%">
			<tr>
				<td width='50%' valign='top'>
					<center>		
							   
					<div style="font-size:14px;width:500;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">Sources Campaigns : Mass Import
						<div style='float:right;'>
						</div>
					</div>
					<hr width=500 style="color:#eeeeee;background-color:#eeeeee;">
					 
					<table width=500 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee;"> 
						<tr>
							 <td  align=left valign=top style="font-size:13px;">
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select the mode of importing a batch of keywords for campaign creation">
								Keyword Options:<br>
							 </td>
							 <td align=right style="font-size:13px;">		
								<select  id='id_keyword_options' name='keyword_option' style='width:341px;'>
									<option value='x'>Please Select</option>
									<option value='categories'>Use Categories as  Base Keywords</option>
									<option value='custom_list'>Use a List of Custom Keywords</option>
									</select>
							 </td>
						</tr>
						<tr>
							<td  align=left valign=top style="font-size:13px; width:300px;">
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This keyword if filled out will be added onto each keyword gathered when creating multiple campaigns. eg: 'keyword keyword_tag'">
								 Keyword Tag:<br>
							</td>
							<td align=right style="font-size:13px;">
								<input class='class_inputs' id='id_keyword_tag'  name='keyword_tag'  size=52 value=''>			
							</td>
						</tr>
						<tr>
							<td  align=left valign=top style="font-size:13px; width:300px;">
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="1 per line.">
								 Keywords:<br>
							</td>
							<td align=right style="font-size:13px;">
								<textarea class='class_inputs'  name='batch_keywords' id='id_keywords' cols=40 rows=8 >
								</textarea>
							</td>
						</tr>			
						<tr>
							<td colspan=2 align=left valign=top style="font-size:13px; width:300px;">
								<br>	
							</td>
						</tr>
						<tr>
						 <td  align=left valign=top style='font-size:13px;'>
							Video Source:<br> </td>
						 <td align=right style="font-size:13px;">
							<select class=video_source_selects name='source' style='width:200px;'>
								<option <?php if($campaign_source=='youtube'){echo "selected=true";}?> value='youtube'>Youtube.com</option>
							</select>
						</td>
						</tr>
						<tr>
						 <td  align=left valign=top style="font-size:13px; width:300px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Define number of items to pull from RSS feed. Leave at 0 for unlimited.">
								 Limit Results:<br>
						 </td>
						 <td align=right style="font-size:13px;">
							 <input  name=limit_results size=5 value='0'>			
						 </td>
						</tr>
						<tr>
							<td align=left valign=top style='font-size:13px;'>
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select of often you want items from this feed to be published.">
								Post Frequency:<br> 
							</td>
							 <td align=right style="font-size:13px;">
								<select id='articles_selects_post_frequency' name='post_frequency' style='width:200px;'>
									<option <?php if($campaign_post_frequency==='1.1'){echo "selected=true";}?> value='1.1'>1 / day</option>
									<option <?php if($campaign_post_frequency==='2.1'){echo "selected=true";}?> value='2.1'>2 / day</option>
									<option <?php if($campaign_post_frequency==='3.1'){echo "selected=true";}?> value='3.1'>3 / day</option>
									<option <?php if($campaign_post_frequency==='4.1'){echo "selected=true";}?> value='4.1'>4 / day</option>
									<option <?php if($campaign_post_frequency==='5.1'){echo "selected=true";}?> value='5.1'>5 / day</option>
									<option <?php if($campaign_post_frequency==='6.1'){echo "selected=true";}?> value='6.1'>6 / day</option>
									<option <?php if($campaign_post_frequency==='7.1'){echo "selected=true";}?> value='7.1'>7 / day</option>
									<option <?php if($campaign_post_frequency==='8.1'){echo "selected=true";}?> value='8.1'>8 / day</option>
									<option <?php if($campaign_post_frequency==='9.1'){echo "selected=true";}?> value='9.1'>9 / day</option>
									<option <?php if($campaign_post_frequency==='10.1'){echo "selected=true";}?> value='10.1'>10 / day</option>
									<option <?php if($campaign_post_frequency==='11.1'){echo "selected=true";}?> value='11.1'>11 / day</option>
									<option <?php if($campaign_post_frequency==='12.1'){echo "selected=true";}?> value='12.1'>12 / day</option>
									<option <?php if($campaign_post_frequency==='13.1'){echo "selected=true";}?> value='13.1'>13 / day</option>
									<option <?php if($campaign_post_frequency==='14.1'){echo "selected=true";}?> value='14.1'>14 / day</option>
									<option <?php if($campaign_post_frequency==='15.1'){echo "selected=true";}?> value='15.1'>15 / day</option>
									<option <?php if($campaign_post_frequency==='16.1'){echo "selected=true";}?> value='16.1'>16 / day</option>
									<option <?php if($campaign_post_frequency==='1.2'){echo "selected=true";}?> value='1.2'>1 every 2 days</option>
									<option <?php if($campaign_post_frequency==='1.3'){echo "selected=true";}?> value='1.3'>1 every 3 days</option>
									<option <?php if($campaign_post_frequency==='1.4'){echo "selected=true";}?> value='1.4'>1 every 4 days</option>
									<option <?php if($campaign_post_frequency==='1.5'){echo "selected=true";}?> value='1.5'>1 every 5 days</option>
									<option <?php if($campaign_post_frequency==='1.6'){echo "selected=true";}?> value='1.6'>1 every 6 days</option>
									<option <?php if($campaign_post_frequency==='1.7'){echo "selected=true";}?> value='1.7'>1 every 7 days</option>
									<option <?php if($campaign_post_frequency==='1.8'){echo "selected=true";}?> value='1.8'>1 every 8 days</option>
									<option <?php if($campaign_post_frequency==='1.9'){echo "selected=true";}?> value='1.9'>1 every 9 days</option>
									<option <?php if($campaign_post_frequency==='1.10'){echo "selected=true";}?> value='1.10'>1 every 10 days</option>
									<option <?php if($campaign_post_frequency==='1.11'){echo "selected=true";}?> value='1.11'>1 every 11 days</option>
									<option <?php if($campaign_post_frequency==='1.12'){echo "selected=true";}?> value='1.12'>1 every 12 days</option>
									<option <?php if($campaign_post_frequency==='1.13'){echo "selected=true";}?> value='1.13'>1 every 13 days</option>
									<option <?php if($campaign_post_frequency==='1.14'){echo "selected=true";}?> value='1.14'>1 every 14 days</option>
									<option <?php if($campaign_post_frequency==='1.15'){echo "selected=true";}?> value='1.15'>1 every 15 days</option>
									<option <?php if($campaign_post_frequency==='1.16'){echo "selected=true";}?> value='1.16'>1 every 16 days</option>
									<option <?php if($campaign_post_frequency==='1.17'){echo "selected=true";}?> value='1.17'>1 every 17 days</option>
									<option <?php if($campaign_post_frequency==='1.18'){echo "selected=true";}?> value='1.18'>1 every 18 days</option>
									<option <?php if($campaign_post_frequency==='1.19'){echo "selected=true";}?> value='1.19'>1 every 19 days</option>
									<option <?php if($campaign_post_frequency==='1.20'){echo "selected=true";}?> value='1.20'>1 every 20 days</option>
									<option <?php if($campaign_post_frequency=='all'){echo "selected=true";}?> value='all'>All at once.</option>
									<option <?php if($campaign_post_frequency=='backdate'){echo "selected=true";}?> value='backdate'>Backdate.</option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<table id='id_table_datepicker' width='100%'></table>

							</td>
						</tr>
						<tr>
							 <td  align=left valign=top style="font-size:13px;">
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Would you like to remove hyperlinks found within the article?">
								Post Status:<br> </td>
							 <td align=right style="font-size:13px;">
								<select name=post_status>
									 <option value='publish' <?php if ($campaign_post_status=='publish'){ echo "selected=true"; } ?>>Publish</option>
									 <option value='draft' <?php if ($campaign_post_status=='draft'){ echo "selected=true"; } ?>>Draft</option>
									 <option value='private' <?php if ($campaign_post_status=='private'){ echo "selected=true"; } ?>>Private</option>
									 <option value='pending' <?php if ($campaign_post_status=='pending'){ echo "selected=true"; } ?>>Pending Review</option>
								 </select>	
							</td>
					  </tr> 
					  <tr>
						<td colspan=2>
							<table id="credit_sources" width=100%>
								<tr>			 
									<td  align=left valign=top style='font-size:13px;'>
										<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Provide a link to original source at footer of posts.">
										Credit Sources:<br> 
									</td>
									<td  align=right style='font-size:13px;'>
										<select name=credit_source id='credit_sources_id' class='credit_sources_selects'>
											<option value=1>on</option>
											<option value=0 selected=true>off</option>
										</select>	
									</td>
								</tr>
				
							</table>
						</td>
					  </tr>
					  <tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Would you like to remove hyperlinks found within the article?">
							Link Options:<br> </td>
						 <td align=right style="font-size:13px;">
							<select name=strip_links>
								 <option value='0' <?php if ($campaign_strip_links=='0'){ echo "selected=true"; } ?>>Leave In-tact</option>
								 <option value='1' <?php if ($campaign_strip_links=='1'){ echo "selected=true"; } ?>>Strip Links</option>
								 <option value='2' <?php if ($campaign_strip_links=='2'){ echo "selected=true"; } ?>>Convert Anchor to Tag-Search.</option>
								 <option value='3' <?php if ($campaign_strip_links=='3'){ echo "selected=true"; } ?>>Convert Anchor to Keyword-Search. </option>
							</select>		
						</td>
					  </tr>
					  <tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Would you like to remove images found within the article?">
							Strip Images:<br> </td>
						 <td align=right style="font-size:13px;">
							<select name=strip_images>
								<option value=1 >on</option>
								<option value=0 selected=true>off</option>
							 </select>	
						</td>
					  </tr>
					  <tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Turn link cloaking on?">
							Cloak Links:<br> </td>
						 <td align=right style="font-size:13px;">
							<select name=cloak_links>
								<option value=1 >on</option>
								<option value=0 selected=true>off</option>
							 </select>	
						</td>
					  </tr>
					  <tr>
						 <td  align=left valign=top style="font-size:13px;">
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This will translate your content from English to the selected=true language. If english leave as English.">
							Language:<br> </td>
						 <td align=right style="font-size:13px;">
							<select id=articles_selects_languages name="language" tabindex="0">
									<option value='no translation' <?php if ($campaign_language=='no translation'){ echo "selected=true"; } ?>>No Translation</option>
								<option value='af' <?php if ($campaign_language=='af'){ echo "selected=true"; } ?>>Afrikaans</option>
								<option value="sq" <?php if ($campaign_language=='sq'){ echo "selected=true"; } ?>>Albanian</option>
								<option value="ar" <?php if ($campaign_language=='ar'){ echo "selected=true"; } ?>>Arabic</option>
								<option value="be" <?php if ($campaign_language=='be'){ echo "selected=true"; } ?>>Belarusian</option>
								<option value="bg" <?php if ($campaign_language=='bg'){ echo "selected=true"; } ?>>Bulgarian</option>
								<option value="ca" <?php if ($campaign_language=='ca'){ echo "selected=true"; } ?>>Catalan</option>
								<option value="zh-CN" <?php if ($campaign_language=='zh-CN'){ echo "selected=true"; } ?>>Chinese</option>
								<option value="hr" <?php if ($campaign_language=='hr'){ echo "selected=true"; } ?>>Croatian</option>
								<option value="cs" <?php if ($campaign_language=='cs'){ echo "selected=true"; } ?>>Czech</option>
								<option value="da" <?php if ($campaign_language=='da'){ echo "selected=true"; } ?>>Danish</option>
								<option value="nl" <?php if ($campaign_language=='nl'){ echo "selected=true"; } ?>>Dutch</option>
								<option value="en" <?php if ($campaign_language=='en'){ echo "selected=true"; } ?>>English</option>
								<option value="et" <?php if ($campaign_language=='et'){ echo "selected=true"; } ?>>Estonian</option>
								<option value="tl" <?php if ($campaign_language=='tl'){ echo "selected=true"; } ?>>Filipino</option>
								<option value="fi" <?php if ($campaign_language=='fi'){ echo "selected=true"; } ?>>Finnish</option>
								<option value="fr" <?php if ($campaign_language=='fr'){ echo "selected=true"; } ?>>French</option>
								<option value="gl" <?php if ($campaign_language=='gl'){ echo "selected=true"; } ?>>Galician</option>
								<option value="de" <?php if ($campaign_language=='de'){ echo "selected=true"; } ?>>German</option>
								<option value="el" <?php if ($campaign_language=='el'){ echo "selected=true"; } ?>>Greek</option>
								<option value="iw" <?php if ($campaign_language=='iw'){ echo "selected=true"; } ?>>Hebrew</option>
								<option value="hi" <?php if ($campaign_language=='hi'){ echo "selected=true"; } ?>>Hindi</option>
								<option value="hu" <?php if ($campaign_language=='hu'){ echo "selected=true"; } ?>>Hungarian</option>
								<option value="is" <?php if ($campaign_language=='is'){ echo "selected=true"; } ?>>Icelandic</option>
								<option value="id" <?php if ($campaign_language=='id'){ echo "selected=true"; } ?>>Indonesian</option>
								<option value="ga" <?php if ($campaign_language=='ga'){ echo "selected=true"; } ?>>Irish</option>
								<option value="it" <?php if ($campaign_language=='it'){ echo "selected=true"; } ?>>Italian</option>
								<option value="ja" <?php if ($campaign_language=='jp'){ echo "selected=true"; } ?>>Japanese</option>
								<option value="ko" <?php if ($campaign_language=='ko'){ echo "selected=true"; } ?>>Korean</option>
								<option value="lv" <?php if ($campaign_language=='lv'){ echo "selected=true"; } ?>>Latvian</option>
								<option value="lt" <?php if ($campaign_language=='lt'){ echo "selected=true"; } ?>>Lithuanian</option>
								<option value="mk" <?php if ($campaign_language=='mk'){ echo "selected=true"; } ?>>Macedonian</option>
								<option value="ms" <?php if ($campaign_language=='ms'){ echo "selected=true"; } ?>>Malay</option>
								<option value="mt" <?php if ($campaign_language=='mt'){ echo "selected=true"; } ?>>Maltese</option>
								<option value="no" <?php if ($campaign_language=='no'){ echo "selected=true"; } ?>>Norwegian</option>
								<option value="fa" <?php if ($campaign_language=='fa'){ echo "selected=true"; } ?>>Persian</option>
								<option value="pl" <?php if ($campaign_language=='pl'){ echo "selected=true"; } ?>>Polish</option>
								<option value="pt" <?php if ($campaign_language=='pt'){ echo "selected=true"; } ?>>Portuguese</option>
								<option value="ro" <?php if ($campaign_language=='ro'){ echo "selected=true"; } ?>>Romanian</option>
								<option value="ru" <?php if ($campaign_language=='ru'){ echo "selected=true"; } ?>>Russian</option>
								<option value="sr" <?php if ($campaign_language=='sr'){ echo "selected=true"; } ?>>Serbian</option>
								<option value="sk" <?php if ($campaign_language=='sk'){ echo "selected=true"; } ?>>Slovak</option>
								<option value="sl" <?php if ($campaign_language=='sl'){ echo "selected=true"; } ?>>Slovenian</option>
								<option value="es" <?php if ($campaign_language=='es'){ echo "selected=true"; } ?>>Spanish</option>
								<option value="sw" <?php if ($campaign_language=='sw'){ echo "selected=true"; } ?>>Swahili</option>
								<option value="sv" <?php if ($campaign_language=='sv'){ echo "selected=true"; } ?>>Swedish</option>
								<option value="th" <?php if ($campaign_language=='th'){ echo "selected=true"; } ?>>Thai</option>
								<option value="tr" <?php if ($campaign_language=='tr'){ echo "selected=true"; } ?>>Turkish</option>
								<option value="uk" <?php if ($campaign_language=='uk'){ echo "selected=true"; } ?>>Ukrainian</option>
								<option value="vi" <?php if ($campaign_language=='vi'){ echo "selected=true"; } ?>>Vietnamese</option>
								<option value="cy" <?php if ($campaign_language=='cy'){ echo "selected=true"; } ?>>Welsh</option>
								<option value="yi" <?php if ($campaign_language=='yi'){ echo "selected=true"; } ?>>Yiddish</option>
							</select>
						 </td>
						</tr>	
						<tr>
							<td  align=left valign=top style="font-size:13px;">
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Attempt to re-write text to make it appear unique.">
								Spin Text:<br> 
							</td>
							<td align=right style="font-size:13px;">
								 <select name=spin_text>
									<option value=0 <?php  if ($campaign_spin_text==0){ echo 'selected="true"'; } ?>>Off</option>
									<option value=1 <?php  if ($campaign_spin_text==1){ echo 'selected="true"'; } ?>>Spin both titles & postbody</option>
									<option value=2 <?php  if ($campaign_spin_text==2){ echo 'selected="true"'; } ?>>Spin titles only</option>
									<option value=3 <?php  if ($campaign_spin_text==3){ echo 'selected="true"'; } ?>>Spin postbody only</option>
									<option value=4 <?php  if ($campaign_spin_text==4){ echo 'selected="true"'; } ?>>Salt title with ASCII</option>
									<option value=5 <?php  if ($campaign_spin_text==5){ echo 'selected="true"'; } ?>>Salt postbody with ASCII</option>
									<option value=6 <?php  if ($campaign_spin_text==6){ echo 'selected="true"'; } ?>>Salt title & postbody with ASCII</option>
									
								</select>			 
							</td>
						</tr>
						<tr>
							<td  align=left valign=top style='font-size:13px;'>
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="All posts from this feel will go into this category. If you only want select items, then use the include and exclude keywords to target your items.">
								Post Author:<br> 
							</td>
							<td align=right style='font-size:13px;'>
								<select name=author>
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
						<tr>
							<td  align=left style="font-size:13px;">
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Videos usually come with a prepared description, would you like BlogSense to exclude this?">
								Include Description:
							</td>
							<td  align=right >
								<select name="video_include_description" id=video_include_description>
									<?php
										 if ($z_video_include_description==0&&$campaign_source)
										 {
											echo "<option value=1>yes</option>";
											echo "<option value=0 selected=true>no</option>";
										 }
										else
										 {
											echo "<option value=1 selected=true>yes</option>";
											echo "<option value=0 >no</option>";
										 }
										 ?>			
								</select>
							</td>
						</tr>
						<tr>
							<td  align=left valign=top style='font-size:13px;'>
								<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="All posts from this feel will go into this category. If you only want select items, then use the include and exclude keywords to target your items.">
								Category:<br> 
							</td>
							<td  align=right style='font-size:13px;' id='id_td_selects_category'>
								<?php 
								wp_dropdown_categories(array(selected=>$campaign_category,name=>'category' ,hierarchical=>1,id=>'articles_selects_cats',hide_empty=>0)); 
								?>
							</td>
						</tr>
						<tr>
							<td colspan=2>
							<table id="id_table_comments_include" width=100%>
								<tr>			 
									<td  align=left valign=top style='font-size:13px;'>
										<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="If comments are available for this module should we include them?">
										Include Comments:<br> 
									</td>
									<td  align=right style='font-size:13px;'>
										<select name=comments_include id='id_comments_include_select' class='comments_include_select'>
											<option value=1>on</option>";
											<option value=0 selected=true>off</option>
										</select>	
									</td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td align=center colspan=2 style='font-size:13px;' valign=top >
								<button class='class_inputs' id='id_submit'  value='Create Campaigns'><span>Create Campaigns</span></button>
								</td>
						</tr>
					</table>
			</td>
					<td valign='top'style=''>
			
			
						<?php
							
							include('./../includes/i_templates_setup.php');
							
							include('./../includes/i_tokens_blogsense_dialogs.php');
							
							if ($phpBay==1)
							{
								include('./../includes/i_tokens_phpbay_dialogs.php');
							}
							
							if ($phpZon==1)
							{
								include('./../includes/i_tokens_phpzon_dialogs.php');
							}
							
							if ($wpMage==1)
							{
								include('./../includes/i_tokens_wpmage_dialogs.php');
							}	
							
							if ($wpRobot==1)
							{
								include('./../includes/i_tokens_wprobot_dialogs.php');
							}	
						
						?>
			
		</td>
	</tr>
	</table>
</form>
</body>
</html>
				
	<?php
}

//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//*********************************************************************************************
//Keyword Profiles
if ($module_type=='keywords'&&!$nature)
{
	
	?>
	<html>
		<head>

		<script type="text/javascript" src="./../includes/jquery.js"></script>
		<script type="text/javascript"> 
		$(document).ready(function() 
		{

			$("#id_button_create_campaign").click(function(){
				$("#nature").val("create_keywords");
				$("#form_yahoo").submit();
			 });
			 
			$("#id_keyword_options").change(function(){	   
				var input =$(this).val();
				if (input=='categories')
				{
				
				   $('.class_inputs').removeAttr('disabled');
				   var cats = "<?php echo $categories_list; ?>";
				   cats = cats.replace(/,/g,'\n');
				   $("#id_keywords").val(cats);
				}
				else if (input=='custom_list')
				{
					$('.class_inputs').removeAttr('disabled');
					var clone = "<?php echo $categories_list; ?>";
					$("#id_keywords").val('');
				}
				else
				{
					$('.class_inputs').attr('disabled', 'disabled');
					
				}
			});
			
		});
		</script>
		</head>
		<body style="font-family:Khmer UI;">
		<form action="" id="form_yahoo" name="form_yahoo" method=POST>
		<input type=hidden name=submit_nature id=nature value='create_keywords'>
		<input type=hidden name=module_type  value='<?php echo $module_type; ?>'>

		<center>		
				   
		<div style="font-size:14px;width:530;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">Keyword Alterations : Mass Profile Creation
			<div style='float:right;'>
			</div>
		</div>
		<hr width=530 style="color:#eeeeee;background-color:#eeeeee;">
		 
		<table width=530 style="margin-left: auto; margin-right: auto; padding: 5px; border: 5px solid rgb(238, 238, 238);" id="table_seo_profiles"> 
			<tr>
				 <td  align=left valign=top style="font-size:13px;">
					<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select the mode of importing a batch of keywords for campaign creation">
					Keyword Options:<br>
				 </td>
				 <td align=right style="font-size:13px;">		
					<select  id='id_keyword_options' name='keyword_option' style='width:341px;'>
						<option value='x'>Please Select</option>
						<option value='categories'>Use Categories as  Base Keywords</option>
						<option value='custom_list'>Use a List of Custom Keywords</option>
						</select>
				 </td>
			</tr>
			<tr>
				<td  align=left valign=top style="font-size:13px; width:300px;">
					<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="1 per line.">
					 Keywords:<br>
				</td>
				<td align=right style="font-size:13px;">
					<textarea class='class_inputs'  name='batch_keywords' id='id_keywords' cols=40 rows=8 >
					</textarea>
				</td>
			</tr>
		</table>
		<br>
		<table width="530" style="margin-left: auto; margin-right: auto; padding: 5px; border: 5px solid rgb(238, 238, 238);" id="table_seo_profiles">
			<tr>
				<td align="right" style="font-size: 13px;">
				 
				</td>
				 <td valign="top" align="left" style="font-size: 11px;">
					<i>keyphrase</i> 
				 </td>
				 <td align="center" style="font-size: 11px;">
					<i>decoration</i> 
				 </td>
				 <td align="center" style="font-size: 11px;">
					<i>href</i> 
				 </td>
				 <td align="center" style="font-size: 11px;">
					<i>class</i> 
				 </td>
				 <td align="center" style="font-size: 11px;">
					<i>rel</i> 
				 </td>		
				 <td align="center" style="font-size: 11px;">
					<i>target</i> 
				 </td>	
				 <td align="center" style="font-size: 11px;">
					<i>limit</i> 
				 </td>
				 <td align="center" style="font-size: 11px;">
					<i>status</i> 
				 </td>
			</tr>
			<tr>
				<td align="right" style="font-size: 13px;">
				</td>
				<td valign="top" align="middle" style="font-size: 11px;">
					<i>***</i>
				</td>
				<td align="center" style="font-size: 11px;">
					<select name="seo_decoration">
						<option value="i">none</option>
						<option value="i">itlalics</option>
						<option value="b">bold</option>
						<option value="s">strong</option>
						<option value="u">underline</option>
					</select>
				</td>
				<td align="center" style="font-size: 11px;">
					<input size="21" name="seo_href">
				</td>
				<td align="center" style="font-size: 11px;">
					<input size="3" name="seo_class">
				</td>
				<td align="center" style="font-size: 11px;">
					<input size="1" name="seo_rel">
				</td>
				 <td align="center" style="font-size:11px;">
					<select name='seo_target[]'>
						<option value='_blank'<?php if ($target=='_blank'){echo "selected=true";}?>>New</option>
						<option value='_self' <?php if ($target=='_self'){echo "selected=true";}?>>Self</option>
					</select>
				 </td>
				<td align="center" style="font-size: 11px;">
					<select name="seo_limit">
						<option>-</option>
						<option>1</option>
						<option>2</option>
						<option>3</option>
						<option>4</option>
					</select>
				</td>
				<td align="center" style="font-size: 11px;">
					<select name="seo_status">
						<option selected="true" value="1">on</option>
						<option value="0">off</option>
					</select> 
				</td>
			</tr>	
		
			<tr>
				<td align=center colspan=8 style='font-size:13px;' valign=top >
					<br>
				</td>
			</tr>	
			<tr>
				<td align=center colspan=8 style='font-size:13px;' valign=top >
					<button class='class_inputs' id='id_submit'  value='Create Campaigns'><span>Create Prolfiles</span></button>
				</td>
			</tr>	
		</table>
<?php
}
?>