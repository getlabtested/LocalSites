<?php
$array= scandir("./../my-articles/");
foreach ($array as $v)
{
	if ($v != '.'&&$v!= '..')
	{
		//echo "my-articles/$v";
		if (!is_dir("./../my-articles/$v"))
		{				    
		}
		else{
		$article_folders[] = $v;
		}
	}
}
if (count($article_folders)==0)
{
   $article_folders[] = "parent";
}	

$array= scandir("./../my-csv-files/");
foreach ($array as $v)
{
	if ($v != '.'&&$v!= '..')
	{
		if (is_dir("./../my-csv-files/$v"))
		{				    
		}
		else{
		$csv_files[] = $v;
		}
	}
}
if (count($csv_files)==0)
{
   $csv_files[] = "no files found";
}	

//load default templates
$array = array();
$query = "SELECT content FROM ".$table_prefix."post_templates WHERE `type` IN ( 'default_rss_post_template' ,'default_title_template') ORDER BY type ASC";
$result = mysql_query($query);
if (!$result){echo $query; exit;}
while ($arr = mysql_fetch_array($result))
{
	$array[] = $arr[0];
}
$default_post_template = $array[0];
$default_title_template = $array[1];
//echo $default_rss_post_template;exit;

?>
<html>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<script type="text/javascript" src="./../includes/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="./../includes/jquery-ui-1.7.2.custom.css">
<style type='text/css'>
		body
		{
			background-color:#ffffff;
		}
		select
		{
			max-width:300px;
		}
</style>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
<script type="text/javascript"> 
$(document).ready(function() 
{
	$("#id_templating").accordion({
			autoHeight: false,
			collapsible: true,
			 active: 1
	});
	
	<?php 
	if ($_GET['edit']!=1&&$_GET['import']!=1)
	{	
	    ?>
		var html_select_title = '<td  align=left valign=top style="font-size:13px; width:300px;"><img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select method of grabbing post title."> Post Title:<br></td><td align=right style="font-size:13px;"><select  name=\'query\' style=\'width:200px;\'><option  value=\'filename\'>Filename</option><option  value=\'firstline\'>First line of text</option></td>';
		var text_import_html = '<select id="id_select_folder" name="source"><?php
		foreach ($article_folders as $v)
		{			   
			echo "<option value=\'$v\'>$v</option>";				  
		}	
		echo "</select>";?>';
		$('#id_td_select_folder_label').html('');
		$('#id_td_select_folder_label').html('Select Folder');
		$('#id_td_select_folder').html('');
		$('#id_td_select_folder').html(text_import_html);
		$('#id_tr_select_title').html(html_select_title);
		<?php
	}
	?>
	$("#id_select_import_type").change(function(){	   
	   var input =$(this).val();
	   //alert(1);
	   if (input=='text_import')
	   {
			var html_select_title = '<td  align=left valign=top style="font-size:13px; width:300px;"><img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select method of grabbing post title."> Post Title:<br></td><td align=right style="font-size:13px;"><select  name=\'query\' style=\'width:200px;\'><option  value=\'filename\'>Filename</option><option  value=\'firstline\'>First line of text</option></td>';
			var text_import_html = '<select id="id_select_folder" name="source"><?php
			foreach ($article_folders as $v)
			{			   
				echo "<option value=\'$v\'>$v</option>";				  
			}	
			echo "</select>";?>';
			$('#id_td_select_folder_label').html('');
			$('#id_td_select_folder_label').html('Select Folder');
			$('#id_td_select_folder').html('');
			$('#id_td_select_folder').html(text_import_html);
			$('#id_tr_select_title').html(html_select_title);
			$('.class_tr_use_collumn_for_category').css('display','none');
	   }
	   else
	   {
			var html_input_delimiter = '<td  align=left valign=top style="font-size:13px; width:300px;"><img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select delimiter"> Delimiter:<br></td><td align=right style="font-size:13px;"><input name="query" size=5 value=""></td>';
			var text_import_html = '<select id="id_select_folder" name="source"><?php
			foreach ($csv_files as $v)
			{			   
				echo "<option value=\'$v\'>$v</option>";				  
			}	
			echo "</select>";
			?>';
			$('#id_td_select_folder_label').html('');
			$('#id_td_select_folder_label').html('Select File');
			$('#id_td_select_folder').html('');
			$('#id_td_select_folder').html(text_import_html);
			$('#id_tr_select_title').html('');
			$('#id_tr_select_title').html(html_input_delimiter);
			$('.class_tr_use_collumn_for_category').css('display','table-row');
	   }
	});
	
    $("#nature").val("input");
    $("#id_button_preview").click(function(){
		$("#nature").val("import");
		$("#form_articles").submit();
		//$("#form_articles").attr("target", "f_add_campaign.php");
		$("#div_import").css("display", "none");
		$("#div_loading").css("display", "block");
     });
	
	$("#id_button_create_campaign").click(function(){
		$("#nature").val("create");
		$("#form_articles").submit();
		//$("#form_articles").attr("target", "f_add_campaign.php");
		$("#div_import").css("display", "none");
		$("#div_loading").css("display", "block");
     });
	 
	 $("#id_button_save_campaign").click(function(){
		$("#nature").val("save");
		$("#form_articles").submit();
		//$("#form_articles").attr("target", "f_add_campaign.php");
		$("#div_import").css("display", "none");
		$("#div_loading").css("display", "block");
     });
	
	$("img.add_articles_string_edit").live("click" ,function(){
       var id = this.id.replace('articles_string_edit_button_','');
	   $('#id_table_regex tr:last').after('<tr><td  align=left style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><input size=20 name=\"regex_search[]\" ></td><td  align=right style=\"font-size:13px;\"><input size=20 name=\"regex_replace[]\"></td></tr>');
	});
	
	
	$( "#datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
	
	$('#id_source').bind('blur', function() {
			var a = $('#id_campaign_name').val();
			var b = $('#id_source').val();
			

			if (!a)
			{
				$('#id_campaign_name').val($(this).val());
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
<form action="" id="" name="" method=POST>
<input type=hidden name=submit_nature id=nature value=input>
<input type=hidden name=module_type  value='<?php echo $module_type; ?>'>
<input type=hidden name=campaign_id  value='<?php echo $campaign_id; ?>'>


<table width="100%">
	<tr>
		<td width='50%' valign='top'>
			<center>							   
			<div style="font-size:14px;width:500;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">Import Articles from Directory 

			<hr width=500 style="color:#eeeeee;background-color:#eeeeee;">
			 
			<table width=500 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee;"> 
				<tr>
					 <td  align=left valign=top style="font-size:13px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="'CSV Import' for CSV files (Located in /auto/my-csv-files/, 'Text Import' for preformatted text content (Located in /auto/my-articles/">
						Select Import Type<br> </td>
					 <td align=right style="font-size:13px;">
						<select id='id_select_import_type' name='campaign_name'>
							<option value='text_import' <?php if ($campaign_name=='text_import'){echo 'selected="true"';}?>>Text Import</option>
							<option value='csv_import' <?php if ($campaign_name=='csv_import'){echo 'selected="true"';}?>>CSV Import</option>
						</select>
					</td>
				</tr>
				<tr>
					 <td  align=left valign=top style="font-size:13px;"  id='id_td_select_folder_label'>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Create and organize your articles into folders for maximum flexibility. Articles & folders are to be uploaded into /my-articles/ ">
						<?php
						if ($campaign_name=='csv_import')
						{
							echo "Select File";
						}
						else
						{
							echo "Select Folder";
						}
						?>
					</td>
					 <td align=right  style="font-size:13px;" id='id_td_select_folder'>
					 <?php
						if ($campaign_name=='csv_import')
						{
							echo "<select id='id_select_folder' name='source'>";
							foreach ($csv_files as $v)
							{		
								if ($v==$campaign_source)
								{
									$selected = 'selected="true"';
								}
								else
								{
									$selected= '';
								}
								echo "<option value='$v' $selected>$v</option>";				  
							}						
							echo "</select>";
						}
						else
						{
							echo "<select id='id_select_folder' name='source'>";
							foreach ($article_folders as $v)
							{			   
								if ($v==$campaign_source)
								{
									$selected = 'selected="true"';
								}
								else
								{
									$selected= '';
								}
								echo "<option value='$v' $selected>$v</option>";					  
							}						
							echo "</select>";
						}
						?>
					</td>
				</tr>
				<tr id='id_tr_select_title'>
				<?php
				if ($campaign_name=='text_import')
				{
				?>
					<td  align=left valign=top style="font-size:13px; width:300px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select method of grabbing post title.">
						Post Title:<br>
					 </td>
					<td align=right style="font-size:13px;">
						<select  name='query' style='width:200px;'>
							<option  value='filename' <?php if ($campaign_query=='filename'){echo 'selected="true"';}?>>Filename</option>
							<option  value='firstline' <?php if ($campaign_query=='firstline'){echo 'selected="true"';}?>>First line of text</option>	
						</select>
					</td>				
				<?php
				}
				else
				{
				?>
					<td  align=left valign=top style="font-size:13px; width:300px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Input column delimiter.">
						Delimiter:<br>
					 </td>
					<td align=right style="font-size:13px;">
						<input name='query' size=5 value='<?php echo $campaign_query; ?>'>
					</td>	
				<?php
				}
				?>
				</tr>
				 <tr>
					<td  align=left valign=top style="font-size:13px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This feature will update already published items. For CSV the title must be the same and for text files the filename must be the same for an update to occur. Otherwise no post will be published.">
						Update Old Posts:<br> 
					</td>
					<td align=right style="font-size:13px;">
						<select name=post_overwrite>
						<?php
						 if ($campaign_post_overwrite==0)
						 {
							echo "<option value=1>on</option>";
							echo "<option value=0 selected=true>off</option>";
						 }
						 if ($campaign_post_overwrite==1)
						 {
							echo "<option value=1 selected=true>on</option>";
							echo "<option value=0 >off</option>";
						 }
						 ?>
						</select>	
					</td>
				</tr>
				<tr>
					<td  align=left valign=top style="font-size:13px; width:300px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Define number of items to pull from RSS feed. Leave at 0 for unlimited.">
						 Limit Feed Results:<br>
					</td>
					<td align=right style="font-size:13px;">
						<input  name=limit_results size=5 value='<?php if ($campaign_limit_results){ echo $campaign_limit_results; } else { echo 0; }?>'>			
					</td>
				</tr>		
				<tr>
					<td align=left valign=top style='font-size:13px;'>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Select of often you want items from this feed to be published.">
						Post Frequency:<br> 
					</td>
					 <td align=right style="font-size:13px;" id='id_td_selects_post_fequency'>
						<select id='articles_selects_post_frequency' name='post_frequency' style='width:200px;'>
							<option <?php if($campaign_post_frequency=='all'){echo "selected=true";}?> value='all'>All at once.</option>
							<option <?php if($campaign_post_frequency==='min_1'){echo "selected=true";}?> value='min_1'>1 / minute</option>
							<option <?php if($campaign_post_frequency==='min_5'){echo "selected=true";}?> value='min_5'>1 / 5 minutes</option>
							<option <?php if($campaign_post_frequency==='min_10'){echo "selected=true";}?> value='min_10'>1 / 10 minutes</option>
							<option <?php if($campaign_post_frequency==='min_15'){echo "selected=true";}?> value='min_15'>1 / 15 minutes</option>
							<option <?php if($campaign_post_frequency==='min_20'){echo "selected=true";}?> value='min_20'>1 / 20 minutes</option>
							<option <?php if($campaign_post_frequency==='min_25'){echo "selected=true";}?> value='min_25'>1 / 25 minutes</option>
							<option <?php if($campaign_post_frequency==='min_30'){echo "selected=true";}?> value='min_30'>1 / 30 minutes</option>
							<option <?php if($campaign_post_frequency==='hour_1'){echo "selected=true";}?> value='hour_1'>1 / hour</option>
							<option <?php if($campaign_post_frequency==='hour_2'){echo "selected=true";}?> value='hour_2'>1 every 2 hours</option>
							<option <?php if($campaign_post_frequency==='hour_3'){echo "selected=true";}?> value='hour_3'>1 every 3 hours</option>
							<option <?php if($campaign_post_frequency==='hour_4'){echo "selected=true";}?> value='hour_4'>1 every 4 hours</option>
							<option <?php if($campaign_post_frequency==='hour_5'){echo "selected=true";}?> value='hour_5'>1 every 5 hours</option>
							<option <?php if($campaign_post_frequency==='hour_6'){echo "selected=true";}?> value='hour_6'>1 every 6 hours</option>
							<option <?php if($campaign_post_frequency==='hour_7'){echo "selected=true";}?> value='hour_7'>1 every 7 hours</option>
							<option <?php if($campaign_post_frequency==='hour_8'){echo "selected=true";}?> value='hour_8'>1 every 8 hours</option>
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
						</select>
					</td>
				</tr>
				<tr>
					<td colspan=2>
						<?php
						$date = date('Y-m-d');
						if (($date>$campaign_post_frequency_start_date&&$campaign_backdating!=1)||$_GET['edit']!='1')
						{
							$campaign_post_frequency_start_date = $date;
						}
						?>
							<table id='id_table_datepicker' width='100%'>
								<tr>
									<td  align=left valign=top style='font-size:13px;'>
										&nbsp;&nbsp&nbsp;<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='This date will select when BlogSense should start publishing. When editing non-backdating campaigns, dates set into the past will be ignored.'>
										&nbsp; Start Date:  
									</td>
									<td align=right style='font-size:13px;'>
										<input name='post_frequency_start_date' id="datepicker" type="text" value=<?php echo $campaign_post_frequency_start_date; ?>>
									</td>
								</tr>
								<tr>
									<td  align=left valign=top style='font-size:13px;'>
										&nbsp;&nbsp&nbsp;<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='If we set this to yes then BlogSense will not skip past days (BlogSense does this typically in attempts to catch the scheduling system back up when days have past without any postings). Set to on if you are wanting to start postings from a past date. '>
										&nbsp; Are we backdating?  
									</td>
									<td align=right style='font-size:13px;'>
										<select name=backdating id='id_backdating' class='class_select_backdating'>
											 <?php
											 if ($campaign_backdating!=1)
											 {
												echo "<option value=1>yes</option>";
												echo "<option value=0 selected=true>no</option>";
											 }
											 if ($campaign_backdating==1)
											 {
												echo "<option value=1 selected=true>yes</option>";
												echo "<option value=0>no</option>";
											 }
											 ?>
										</select>	
									</td>
								</tr>
							</table>
					</td>
				</tr>
				<tr>
					 <td  align=left valign=top style="font-size:13px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Please select the post type.">
						Post Type:<br> </td>
					 <td align=right style="font-size:13px;">
						<select name=post_type>
							<?php
							$post_types=get_post_types('','names'); 
							foreach ($post_types as $value ) {
							  if ($campaign_post_type==$value)
							  {
								$selected = "selected='true'";
							  }
							  else
							  {
								$selected = "";
							  }
							  echo "<option value='$value' $selected>$value</option>";
							}
							?>
						 </select>	
					</td>
				  </tr>
				<tr>
					<td  align=left valign=top style="font-size:13px;">
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Would you like to remove hyperlinks found within the article?">
						Post Status:<br> 
					</td>
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
						 <option value='0' <?php if ($campaign_strip_links=='0'){ echo "selected=true"; } ?>>Leave In-tact.</option>
						 <option value='1' <?php if ($campaign_strip_links=='1'){ echo "selected=true"; } ?>>Strip Links.</option>
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
						<?php
						 if ($campaign_strip_images==0)
						 {
							echo "<option value=1>on</option>";
							echo "<option value=0 selected=true>off</option>";
						 }
						 if ($campaign_strip_images==1)
						 {
							echo "<option value=1 selected=true>on</option>";
							echo "<option value=0 >off</option>";
						 }
						 ?>
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
						<option value="pt-PT" <?php if ($campaign_language=='pt-PT'){ echo "selected=true"; } ?>>Portuguese</option>
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
							<option value='rand' <?php if ($campaign_author=='rand'){echo "selected=true"; } ?> >Random Author</option>
						</select>
						</td>
				</tr>
				<tr>
					<td  align=left valign=top style='font-size:13px;'>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="All posts from this feel will go into this category. If you only want select items, then use the include and exclude keywords to target your items.">
						Category:<br> 						
					</td>
					<td  align=right style='font-size:13px;'>
					<?php 
						 wp_dropdown_categories(array(selected=>$campaign_category,name=>'category' ,hierarchical=>1,id=>'articles_selects_cats',hide_empty=>0)); 
						?>
					</td>
				</tr>
				<tr style='font-size:13px;display:none;'>
					<td  align=left valign=top style='font-size:13px;display:none;'>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This order will arrange the way BlogSense reads the files from the directory and queues items for publishing.">
						File Order Sorting <br> 						
					</td>
					<td  align=right style='font-size:13px;'>
						<select name='z_yahoo_option_sorting'>
							<option value='original' <?php if ($z_yahoo_option_sorting=='original') echo "selected=true"; ?>>Original Order</option>
							<option value='rand' <?php if ($z_yahoo_option_sorting=='rand') echo "selected=true"; ?>>Randomize</option>
						</select>
					</td>
				</tr>
				<?php
				if ($campaign_name=='csv_import')
				{
					$display='table-row';
				}
				else
				{
					$display='none';
				}
				?>
				<tr class='class_tr_use_collumn_for_category' style='display:<?php echo $display; ?>'>
					<td  align=left valign=top style='font-size:13px;'>
					<?php 
					if (strstr($campaign_category,'csv:'))
					{
						$campaign_category = explode(':',$campaign_category);
					}
					?>
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Use the token variables from the Body Template area to select a dynamic field to use as the category selector for this campaign. If unchecked BlogSense will use the category listed above as the default cateogry.">
						Use csv column value as category: <input type='checkbox' name='csv_use_collumn_for_category' value=1  <?php if ($campaign_category[0]=='csv'){echo "checked='true'";}?>><br> 						
					</td>
					<td  align=right style='font-size:13px;'>
						<input name='csv_collumn_category' size=25 value='<?php if ($campaign_category[0]=='csv') { echo $campaign_category[1]; }?>'>
					</td>
				</tr>
				<tr>
					<td colspan=2 align=center valign=top style='font-size:13px;padding:20px;'>
						<?php
						if ($_GET['edit']==1)
						{
							echo '<button  id=id_button_save_campaign><span>Save Campaign</span></button>';
						}
						else if ($_GET['import']==1)
						{
							echo ' <button  id=id_button_preview><span>Direct Import</span></button>';
						}
						else
						{
							echo '<button  id=id_button_create_campaign><span>Create Campaign</span></button>&nbsp;&nbsp;
								  <button  id=id_button_preview><span>Direct Import</span></button>';
						}
						?>
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
				
				if ($prosperent==1)
				{
					include('./../includes/i_tokens_prosperent_dialogs.php');
				}
				
			?>
			
		</td>
	</tr>
</table>
</form>
</div>
</body>
</html>

