<?php
include_once('./../../../wp-config.php');
if (!isset($_SESSION)) 
{ 
session_start();
}

include("./../../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../../login.php");
//include_once("../helper_functions.php");
if (function_exists('switch_to_blog')) switch_to_blog($_SESSION['blog_id']);
include_once('./../prepare_variables.php');

function clean_cdata($input)
{
  if (strstr($input, "<![CDATA["))
  {
	$input = str_replace ('<![CDATA[','',$input);
	$input = str_replace (array(']]>',']]&gt;'),'',$input);
  }
  return $input;
}

if ($_FILES["file"])
{
	//echo 1; exit;
	if ($_FILES["file"]["error"] > 0)
	{
		echo "Error: " . $_FILES["file"]["error"] . "<br />";
	}
	else
	{
		
		$string = file_get_contents($_FILES['file']['tmp_name']);
		//echo $string;exit;
		$campaigns = explode('<campaign>',$string);
		array_shift($campaigns);
		//print_r($campaigns);
		foreach ($campaigns as $key=>$val)
		{
			$val = str_replace('</campaign>','',$val);
			$val = clean_cdata($val);
			$this_array = json_decode($val,true);
			//print_r($this_array);exit;
			$query = "INSERT INTO ".$table_prefix."campaigns (`name`,`campaign_status`,`module_type`,`source`,`query`,";
			$query .= "`feed`,`limit_results`,`author`,`include_keywords`,`exclude_keywords`,`category`,`autocategorize`,";
			$query .= "`autocategorize_search`,`autocategorize_method`,`autocategorize_filter_keywords`,`autocategorize_filter_categories`,";
			$query .= "`autocategorize_filter_list`,`language`,`spin_text`,`strip_images`,`strip_links`,`image_floating`,`scrape_profile`,";
			$query .= "`regex_search`,`regex_replace`,`credit_source`,`credit_source_nofollow`,`credit_source_text`,`schedule_backdating`,";
			$query .= "`schedule_post_frequency`,`schedule_post_date`,`schedule_post_count`,`custom_field_name`,`custom_field_value`,`z_affiliate_id`,";
			$query .= "`z_bookmark_twitter`,`z_bookmark_pixelpipe`,`z_rss_scrape_content`,`z_rss_scrape_content_begin_code`,`z_rss_scrape_content_end_code`,";
			$query .= "`z_rss_scrape_comments`,`z_rss_scrape_names_begin_code`,`z_rss_scrape_names_end_code`,`z_rss_scrape_comments_begin_code`,";
			$query .= "`z_rss_scrape_comments_end_code`,`z_yahoo_option_category`,`z_yahoo_option_date_range`,`z_yahoo_option_region`,`z_yahoo_option_results_limit`,";
			$query .= "`z_yahoo_option_sorting`,`z_yahoo_option_type`,`z_post_template`,`z_title_template`,`z_post_status`,`z_comments_include`,";
			$query .= "`z_comments_limit`,`z_remote_publishing_api_bs`,`z_remote_publishing_api_xmlrpc`,`z_remote_publishing_api_xmlrpc_spin`,";
			$query .= "`z_remote_publishing_api_pp_email`,`z_remote_publishing_api_pp_routing`,`z_remote_publishing_api_email`,";
			$query .= "`z_remote_publishing_api_email_footer`,`z_post_overwrite`,`z_include_keywords_scope`,`z_exclude_keywords_scope`)";
			
			$query .= "VALUES ('{$this_array['name']}','{$this_array['campaign_status']}','{$this_array['module_type']}','{$this_array['source']}',";
			$query .= "'{$this_array['query']}','{$this_array['feed']}','{$this_array['limit_results']}','{$this_array['author']}',";
			$query .= "'{$this_array['include_keywords']}','{$this_array['exclude_keywords']}','{$this_array['category']}','{$this_array['autocategorization']}',";
			$query .= "'{$this_array['autocategorization_search']}','{$this_array['autocategorization_method']}','{$this_array['autocategorize_filter_keywords']}',";
			$query .= "'{$this_array['autocategorize_filter_categories']}','{$this_array['autocategorize_filter_list']}','{$this_array['language']}',";
			$query .= "'{$this_array['spin_text']}','{$this_array['strip_images']}','{$this_array['strip_links']}','{$this_array['image_floating']}',";
			$query .= "'{$this_array['scrape_profile']}','{$this_array['regex_search']}','{$this_array['regex_replace']}','{$this_array['credit_source']}',";
			$query .= "'{$this_array['credit_source_nofollow']}','{$this_array['credit_source_text']}','{$this_array['schedule_backdating']}',";
			$query .= "'{$this_array['schedule_post_frequency']}','{$this_array['schedule_post_date']}','0','{$this_array['custom_field_name']}',";
			$query .= "'{$this_array['custom_field_value']}','{$this_array['affiliate_id']}','{$this_array['z_bookmark_twitter']}',";
			$query .= "'{$this_array['z_bookmark_pixelpipe']}','{$this_array['z_rss_scrape_content']}','{$this_array['z_rss_scrape_content_begin_code']}',";
			$query .= "'{$this_array['z_rss_scrape_content_end_code']}','{$this_array['z_rss_scrape_comments']}','{$this_array['z_rss_scrape_names_begin_code']}',";
			$query .= "'{$this_array['z_rss_scrape_names_end_code']}','{$this_array['z_rss_scrape_comments_begin_code']}','{$this_array['z_rss_scrape_comments_end_code']}',";
			$query .= "'{$this_array['z_yahoo_option_category']}','{$this_array['z_yahoo_option_date_range']}','{$this_array['z_yahoo_option_region']}','{$this_array['z_yahoo_option_results_limit']}',";
			$query .= "'{$this_array['z_yahoo_option_sorting']}','{$this_array['z_yahoo_option_type']}','".mysql_real_escape_string($this_array['z_post_template'])."','".mysql_real_escape_string($this_array['z_title_template'])."',";
			$query .= "'{$this_array['z_post_status']}','{$this_array['z_comments_include']}','{$this_array['z_comments_limit']}', '{$this_array['z_remote_publishing_api_bs']}','{$this_array['z_remote_publishing_api_xmlrpc']}',";
			$query .= "'{$this_array['z_remote_publishing_api_xmlrpc_spin']}','{$this_array['remote_publishing_api_pp_email']}','{$this_array['z_remote_publishing_api_pp_routing']}','{$this_array['z_remote_publishing_api_email']}','{$this_array['z_remote_publishing_api_email_footer']}', '{$this_array['z_post_overwrite']}','{$this_array['z_include_keywords_scope']}','{$this_array['z_exclude_keywords_scope']}')";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error(); exit; }
			
			
		}
		echo "<br><br><br><center><i><font color='green'>Success! ".count($campaigns)." campaigns imported!</font></center>";
		exit;
	}	
}

if ($_GET['nature']=='import')
{
	?>
	<html>
	<title>Import Campaigns - BlogSenseWp</title>
	<head>
	<script type="text/javascript" src="./../jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="./../jquery-ui-1.7.2.custom.css">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="./../i_hook_jquery.js"></script>
	<script type='text/css'>
	.body
	{
		background-color:#ffffff;
	}
	</script>
	<script type="text/javascript">
	
	$(document).ready(function() 
	{
		
	});
	</script>

	<?php


	?>
	<center>
	<h3>Browse for BlogSense Campaign XML file.</h3>
	<form action='' method='POST' id='form_global_settings' enctype="multipart/form-data">
		
		
		<table width='400' align=middle>
		<tr>
			<td valign=top align='right' >
			<input type="file" name="file" id="file" />				
			</td>
		</tr>
		<tr>
			<td valign=top align='right' >
				<input type='submit'  value='Import XML Backup'>
			</td>
		</tr>
		</table>
		
	</form></center>
	<?php
}
else 
{

	if ($_POST['nature']=='generate_xml')
	{
		ini_set('memory_limit', '128M');
		//include_once('xml_parser.php');
		include_once('./../../../wp-admin/includes/class-pclzip.php');
		
		$these_ids = $_POST['campaign_id'];
		$count = count($campaign_id);
		
		//print_r($these_ids);exit;
		$this_xml ="<?xml version='1.0' ?>\n<rss version='2.0'>\n<channel>\n";
		$this_xml .="<title>BlogSense Campaign Backup File</title>\n";
		foreach ($campaign_id as $key=>$val)
		{
			foreach ($these_ids as $k=>$v)
			{
				if ($v==$val)
				{
					//echo 1; exit;
					$this_array['name'] = $campaign_name[$key];
					$this_array['campaign_status'] = $campaign_status[$key];
					$this_array['module_type'] = $campaign_type[$key];
					$this_array['source'] = $campaign_source[$key];
					$this_array['query'] = $campaign_query[$key];
					$this_array['feed'] = $campaign_feed[$key];
					$this_array['limit_results'] = $campaign_limit_results[$key];
					$this_array['author'] = $campaign_author[$key];
					$this_array['include_keywords'] = $campaign_include_keywords[$key];
					$this_array['exclude_keywords'] = $campaign_exclude_keywords[$key];
					$this_array['category'] = $campaign_category[$key];
					$this_array['language'] = $campaign_language[$key];
					$this_array['autocategorize'] = $campaign_autocategorize[$key];
					$this_array['autocategorize_search'] = $campaign_autocategorize_search[$key];
					$this_array['autocategorize_method'] = $campaign_autocategorize_method[$key];
					$this_array['autocategorize_filter_keywords'] = $campaign_autocategorize_filter_keywords[$key];
					$this_array['autocategorize_filter_categories'] = $campaign_autocategorize_filter_categories[$key];
					$this_array['autocategorize_filter_list'] = $campaign_autocategorize_filter_list[$key];
					$this_array['spin_text'] = $campaign_spin_text[$key];
					$this_array['strip_images'] = $campaign_spin_images[$key];
					$this_array['strip_links'] = $campaign_spin_links[$key];
					$this_array['cloak_links'] = $campaign_cloak_links[$key];
					$this_array['scrape_profile'] = $campaign_scrape_profile[$key];
					$this_array['regex_search'] = $campaign_regex_search[$key];
					$this_array['regex_replace'] = $campaign_regex_replace[$key];
					$this_array['credit_source'] = $campaign_credit_source[$key];
					$this_array['credit_source_nofollow'] = $campaign_credit_source_nofollow[$key];
					$this_array['credit_source_text'] = $campaign_credit_source_text[$key];
					$this_array['schedule_post_frequency'] = $campaign_post_frequency[$key];
					$this_array['schedule_post_date'] = $campaign_post_date[$key];
					$this_array['schedule_post_count'] = $campaign_post_count[$key];
					$this_array['z_rss_scrape_content'] = $campaign_scrape_content[$key];
					$this_array['z_rss_scrape_content_begin_code'] = $campaign_scrape_content_begin_code[$key];
					$this_array['z_rss_scrape_content_end_code'] = $campaign_scrape_content_end_code[$key];
					$this_array['z_rss_scrape_comments'] = $campaign_scrape_comments[$key];
					$this_array['z_rss_scrape_comments_begin_code'] = $campaign_scrape_comments_begin_code[$key];
					$this_array['z_rss_scrape_comments_end_code'] = $campaign_scrape_comments_end_code[$key];
					$this_array['z_yahoo_option_category'] = $z_yahoo_option_category[$key];
					$this_array['z_yahoo_option_date_range'] = $z_yahoo_option_date_range[$key];
					$this_array['z_yahoo_option_region'] = $z_yahoo_option_region[$key];
					$this_array['z_yahoo_option_results_limit'] = $z_yahoo_option_results_limit[$key];
					$this_array['z_yahoo_option_sorting'] = $z_yahoo_option_sorting[$key];
					$this_array['z_yahoo_option_type'] = $z_yahoo_option_type[$key];
					$this_array['z_title_template'] = $campaign_title_template[$key];
					$this_array['z_post_template'] = $campaign_post_template[$key];
					$this_array['z_bookmark_twitter'] = $bookmark_twitter[$key];
					$this_array['z_bookmark_pixelpipe'] = $bookmark_pixelpipe[$key];
					$this_array['z_post_status'] = $campaign_post_status[$key];
					$this_array['z_comments_include'] = $campaign_comments_include[$key];
					$this_array['z_comments_limit'] = $campaign_comments_limit[$key];
					$this_array['z_remote_publishing_api_bs'] = $campaign_remote_publishing_api_bs[$key];
					$this_array['z_remote_publishing_api_xmlrpc'] = $campaign_remote_publishing_api_xmlrpc[$key];
					$this_array['z_remote_publishing_api_xmlrpc_spin'] = $campaign_remote_publishing_api_xmlrpc_spin[$key];
					$this_array['z_remote_publishing_api_email'] = $campaign_remote_publishing_api_email[$key];
					$this_array['z_remote_publishing_api_email_footer'] = $campaign_remote_publishing_api_email_footer[$key];
					$this_array['z_remote_publishing_api_pp_email'] = $campaign_remote_publishing_api_pp_email[$key];
					$this_array['z_remote_publishing_api_pp_routing'] = $campaign_remote_publishing_api_pp_routing[$key];
					$this_array['z_post_overwrite'] = $campaign_post_overwrite[$key];
					$this_array['z_include_keywords'] = $campaign_include_keywords_scope[$key];
					$this_array['z_exclude_keywords'] = $campaign_exclude_keywords_scope[$key];
					
					
					$this_json = json_encode($this_array);
					//echo 1;
					$this_xml .="<campaign>\n";
					$this_xml .="<![CDATA[ {$this_json} ]]>\n";
					$this_xml .="</campaign>\n";
					
					//echo $this_xml;exit;
					unset($this_json);
					unset($this_array);
				}
			}
		}
		
		//echo $xml; exit;
	
			
		$xml_backup = "../../my-backups/bs-campaigns-{$table_prefix}backup-".date('m-d-Y')."-".time().".xml";
		
		//echo $backup;exit;
		$handle = fopen($xml_backup,'w+');
		fwrite($handle,$this_xml);
		fclose($handle);
		
		$this_site = sanitize_title_with_dashes($site_url);
		//now compile final backup zip
		$filename = "bs-campaigns-{$table_prefix}backup-".date('m-d-Y')."-".time().".xml";
		
		  
		//print_r($v_list);exit;
		//unlink($xml_backup);
		//create headers and download file
		header('Content-type: application/xml');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		readfile($xml_backup);
		
		exit;
	}
	
	?>

	<html>
	<head>
	<title>Export Campaigns - BlogSenseWp</title>
	<script type="text/javascript" src="./../jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="./../jquery-ui-1.7.2.custom.css">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
	<script type="text/javascript" src="./../i_hook_jquery.js"></script>
	<script type='text/css'>
	.body
	{
		background-color:#ffffff;
	}
	</script>
	<script type="text/javascript">
	
	$(document).ready(function() 
	{
		
	});
	</script>

	<?php


	?>
	<form action='' method='POST' id='form_global_settings'>
		<input type='hidden' name='nature' value='generate_xml'>
		<table width=100%>
		<tr>
			<td colspan=2 valign=top>
				<h2>Select Campaigns</h2>
				<select multiple='multiple' name='campaign_id[]' style='width:100%;height:400px;'>
					<?php
					foreach ($campaign_id as $key=>$val)
					{
					
						echo "<option value='$val'>[{$campaign_type[$key]}] {$campaign_name[$key]}</option>";
						
					}
					?>
				</select>
				<br><br>
				
			</td>
		</tr>
		<tr>
			<td valign=top align='right'width='300px' colspan=2>
				<input type='submit'  value='Generate XML Backup'>
			</td>
		</tr>
		</table>
	</form>
<?php
}
?>