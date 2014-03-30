<?php
include_once('./../../wp-config.php');
session_start();

include("./../functions/f_login.php");
if(checkSession() == false)
{
blogsense_redirect("./../login.php");
}
set_time_limit(700);
ini_set("magic_quotes_runtime", 0);
include_once("../includes/helper_functions.php");
//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}
include_once('./../includes/prepare_variables.php');

//echo $table_prefix;exit;
?>

<html>
<head>

<script type="text/javascript" src="./../includes/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="./../includes/jquery-ui-1.7.2.custom.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="./../includes/i_hook_jquery.js"></script>
<script type='text/css'>
.body
{
	background-color:#ffffff;
}
</script>
<script type="text/javascript">
function submit_form()
{
		if (confirm('Are you sure you want to apply these settings globally?')) 
		{
			$("#form_global_settings").submit();
		}
 };
$(document).ready(function() 
{
	
	
	$('#id_select_criteria').live('change', function(){	
		$('.class_settings_area').remove();
		var input =$(this).val();
		
		var loader = "<img class='class_settings_area' src='./../nav/loading.gif'>";
		$(".class_edit_area").append(loader);
		
		var save = "<div class='class_settings_area'><button id='id_button_save_campaign' onclick='submit_form(); return false;'><span>Save Settings</span></button></div>";
		if (input=='limit')
		{
			$('.class_settings_area').remove();
			var html = "<tr class='class_settings_area'>"
					+"<td  align=left valign=top style='font-size:13px; width:300px;'>"
					+"	<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Limit number of potential incoming items. Set at zero to imply no limit.'>"
					+"	 Limit Feed Results:<br> "
					+"</td> "
					+"<td align=right style='font-size:13px;'>"
					+"  <input type='hidden' name='nature' value='limit'>"
					+"	<input  name=limit_results size=5 value='0'> "			
					+"</td> "
					+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='enable_disable')
		{
			$('.class_settings_area').remove();
			var html = "<tr class='class_settings_area'>"
					+"<td  align=left valign=top style='font-size:13px; width:300px;'>"
					+"	<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Enable or disabled campaigns for automation.'>"
					+"	 Status:<br> "
					+"</td> "
					+"<td align=right style='font-size:13px;'> "
					+"  <input type='hidden' name='nature' value='enable_disable'>"
					+"	<select name='campaign_status'>"
					+" 	 <option value=1 id='s_active'>Active Campaign</option>"
					+"  	<option value=0 id='s_inactive' selected=true>Inactive Campaign</option>"		
					+"	</select>	"		
					+"</td> "
					+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='title_template')
		{
			$('.class_settings_area').remove();
			var html = 	"<tr class='class_settings_area'>"
						+"<td colspan=2 align=right style='font-size:13px;'>"
						+"<input type='hidden' name='nature' value='title_template'>Title Template<br>"
						+"<textarea width=100% cols=71 rows=4 name='title_template'>%title%</textarea>"
						+"</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='postbody_template')
		{
			$('.class_settings_area').remove();
			var html = 	"<tr class='class_settings_area'>"
						+"<td colspan=2 align=right style='font-size:13px;'>"
						+"<input type='hidden' name='nature' value='postbody_template'>Postbody Template<br>"
						+"<textarea width=100% cols=71 rows=4 name='postbody_template'>%postbody%</textarea>"
						+"</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='limit')
		{
			$('.class_settings_area').remove();
			var html = 	"<tr class='class_settings_area'>"
						+"	<td  align=left valign=top style='font-size:13px; width:300px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Define number of items to pull from available items. Leave at 0 for unlimited.'>"
						+"		 Limit Results:<br>"
						+"	</td>"
						+"	<td align=right style='font-size:13px;'>"
						+"  	<input type='hidden' name='nature' value='limit'>"
						+"		<input  name=limit_results size=5 value=''>	"		
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='post_frequency')
		{
			$('.class_settings_area').remove();
			var html = 	"<tr class='class_settings_area'>"
						+"	<td align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Select of often you want items from this feed to be published.'>"
						+"		Post Frequency:<br> "
						+"	</td>"
						+"	 <td align=right style='font-size:13px;'>"
						+"  	<input type='hidden' name='nature' value='post_frequency'>"
						+"		<select id='articles_selects_post_frequency' name='post_frequency' style='width:200px;'>"
						+"			<option value='min_1'>1 / minute</option>"
						+"			<option value='min_5'>1 / 5 minutes</option>"
						+"			<option value='min_10'>1 / 10 minutes</option>"
						+"			<option value='min_15'>1 / 15 minutes</option>"
						+"			<option value='min_20'>1 / 20 minutes</option>"
						+"			<option value='min_25'>1 / 25 minutes</option>"
						+"			<option value='min_30'>1 / 30 minutes</option>"
						+"			<option value='hour_1'>1 / hour</option>"
						+"			<option value='1.1'>1 / day</option>"
						+"			<option value='2.1'>2 / day</option>"
						+"			<option value='3.1'>3 / day</option>"
						+"			<option value='4.1'>4 / day</option>"
						+"			<option value='5.1'>5 / day</option>"
						+"			<option value='6.1'>6 / day</option>"
						+"			<option value='7.1'>7 / day</option>"
						+"			<option value='8.1'>8 / day</option>"
						+"			<option value='9.1'>9 / day</option>"
						+"			<option value='10.1'>10 / day</option>"
						+"			<option value='11.1'>11 / day</option>"
						+"			<option value='12.1'>12 / day</option>"
						+"			<option value='13.1'>13 / day</option>"
						+"			<option value='14.1'>14 / day</option>"
						+"			<option value='15.1'>15 / day</option>"
						+"			<option value='16.1'>16 / day</option>"
						+"			<option value='1.2'>1 every 2 days</option>"
						+"			<option value='1.3'>1 every 3 days</option>"
						+"			<option value='1.4'>1 every 4 days</option>"
						+"			<option value='1.5'>1 every 5 days</option>"
						+"			<option value='1.6'>1 every 6 days</option>"
						+"			<option value='1.7'>1 every 7 days</option>"
						+"			<option value='1.8'>1 every 8 days</option>"
						+"			<option value='1.9'>1 every 9 days</option>"
						+"			<option value='1.10'>1 every 10 days</option>"
						+"			<option value='1.11'>1 every 11 days</option>"
						+"			<option value='1.12'>1 every 12 days</option>"
						+"			<option value='1.13'>1 every 13 days</option>"
						+"			<option value='1.14'>1 every 14 days</option>"
						+"			<option value='1.15'>1 every 15 days</option>"
						+"			<option value='1.16'>1 every 16 days</option>"
						+"			<option value='1.17'>1 every 17 days</option>"
						+"			<option value='1.18'>1 every 18 days</option>"
						+"			<option value='1.19'>1 every 19 days</option>"
						+"			<option value='1.20'>1 every 20 days</option>"
						+"			<option value='all'>All at once.</option>"
						+"			<option value='backdate'>Backdate.</option>"
						+"			<option value='feed_date'>Keep Feed Dates</option>"
						+"		</select>"
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='post_frequency_start_date')
		{
			$('.class_settings_area').remove();
			var html = 	"<tr class='class_settings_area'>"
						+"	<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='This date will select when BlogSense should start publishing. When editing non-backdating campaigns, dates set into the past will be ignored.'>"
						+"		Start Date:  "
						+"	</td>"
						+"	<td align=right style='font-size:13px;'>"
						+"  	<input type='hidden' name='nature' value='post_frequency_start_date'>"
						+"		<input name='post_frequency_start_date' id='datepicker' type='text' value='<?php echo $wordpress_date_time; ?>'>"
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
			$( "#datepicker" ).datepicker({dateFormat: 'yy-mm-dd'});
		}
		else if (input=='post_overwrite')
		{
			$('.class_settings_area').remove();
			var html = 	" <tr>"
						+"	<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='BlogSense tracks duplicate content by source URL and never allows content with the same originating URL to be published twice. Turn this feature off if you wish to allow content from the same source URL to be published more than once, otherwise leave this feature on. If this feature is enabled BlogSense will prevent posts with the same source url to be published into the Wordpress database.'>"
						+"		Duplicate Content Check:<br> "
						+"	</td>"
						+"	<td align=right style='font-size:13px;'>"
						+"  	<input type='hidden' name='nature' value='post_overwrite'>"
						+"		<select name=post_overwrite>"
						+"			<option value=1>on</option>"
						+"			<option value=0 selected=true>off</option>	"
						+"		</select>	"
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='post_status')
		{
			$('.class_settings_area').remove();
			var html = 	"<tr class='class_settings_area'>"
						+"	 <td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Sets all posts into the following mode.'>"
						+"		Post Status:<br> </td>"
						+"	 <td align=right style='font-size:13px;'>"
						+"  	<input type='hidden' name='nature' value='post_status'>"
						+"		<select name=post_status>"
						+"			 <option value='publish'>Publish</option>"
						+"			 <option value='draft'>Draft</option>"
						+"			 <option value='private' >Private</option>"
						+"			 <option value='pending' >Pending Review</option>"
						+"		 </select>	"
						+"	</td>"
						 +" </tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='link_options')
		{
			$('.class_settings_area').remove();
			var html = 	"<tr class='class_settings_area'>"
						+"<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Would you like to remove hyperlinks found within the article?'>"
						+"		Link Options:<br> </td> "
						+"	 <td align=right style='font-size:13px;'> "
						+"  	<input type='hidden' name='nature' value='link_options'>"
						+"		<select name=strip_links> "
						+"			 <option value='0'>Leave In-tact.</option> "
						+"			 <option value='1'>Strip Links.</option> "
						+"			 <option value='2'>Convert Anchor to Tag-Search.</option> "
						+"			 <option value='3'>Convert Anchor to Keyword-Search. </option> "
						+"		 </select>	"
						+"	</td>"
						+"  </tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='strip_images')
		{
			$('.class_settings_area').remove();
			var html = 	"<tr class='class_settings_area'>"
						+"<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Remove all images found in content'>"
						+"		Strip Images:<br> </td> "
						+"	 <td align=right style='font-size:13px;'> "
						+"  	<input type='hidden' name='nature' value='strip_images'>"
						+"		<select name=strip_images> "
						+"			 <option value='0'>Off</option> "
						+"			 <option value='1'>On</option> "
						+"		 </select>	"
						+"	</td>"
						+"  </tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='strip_images')
		{
			$('.class_settings_area').remove();
			var html = 	"<tr class='class_settings_area'>"
						+"<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Remove all images found in content'>"
						+"		Strip Images:<br> </td> "
						+"	 <td align=right style='font-size:13px;'> "
						+"  	<input type='hidden' name='nature' value='strip_images'>"
						+"		<select name=strip_images> "
						+"			 <option value='0'>Off</option> "
						+"			 <option value='1'>On</option> "
						+"		 </select>	"
						+"	</td>"
						+"  </tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='language')
		{
			$('.class_settings_area').remove();
			var html = 	"<tr class='class_settings_area'>"
						+"	<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Leave as No Translation to leave the content as is and not attempt any translation. '>"
						+"		Select Translation:<br> "
						+"  </td> "
						+"	 <td align=right style='font-size:13px;'> "
						+"  	<input type='hidden' name='nature' value='language'>"
						+"		<select id=articles_selects_languages name='language' tabindex='0'>"					
						+"		<option value='no translation' >No Translation</option>"
						+"		<option value='af' >Afrikaans</option>"
						+"		<option value='sq' >Albanian</option>"
						+"		<option value='ar' >Arabic</option>"
						+"		<option value='be' >Belarusian</option>"
						+"		<option value='bg' >Bulgarian</option>"
						+"		<option value='ca' >Catalan</option>"
						+"		<option value='zh-CN' >Chinese</option>"
						+"		<option value='hr' >Croatian</option>"
						+"		<option value='cs' >Czech</option>"
						+"		<option value='da' >Danish</option>"
						+"		<option value='nl' >Dutch</option>"
						+"		<option value='en' >English</option>"
						+"		<option value='et' >Estonian</option>"
						+"		<option value='tl' >Filipino</option>"
						+"		<option value='fi' >Finnish</option>"
						+"		<option value='fr' >French</option>"
						+"		<option value='gl' >Galician</option>"
						+"		<option value='de' >German</option>"
						+"		<option value='el' >Greek</option>"
						+"		<option value='iw' >Hebrew</option>"
						+"		<option value='hi' >Hindi</option>"
						+"		<option value='hu' >Hungarian</option>"
						+"		<option value='is' >Icelandic</option>"
						+"		<option value='id' >Indonesian</option>"
						+"		<option value='ga' >Irish</option>"
						+"		<option value='it' >Italian</option>"
						+"		<option value='ja' >Japanese</option>"
						+"		<option value='ko' >Korean</option>"
						+"		<option value='lv' >Latvian</option>"
						+"		<option value='lt' >Lithuanian</option>"
						+"		<option value='mk' >Macedonian</option>"
						+"		<option value='ms' >Malay</option>"
						+"		<option value='mt' >Maltese</option>"
						+"		<option value='no' >Norwegian</option>"
						+"		<option value='fa' >Persian</option>"
						+"		<option value='pl' >Polish</option>"
						+"		<option value='pt-PT' >Portuguese</option>"
						+"		<option value='ro' >Romanian</option>"
						+"		<option value='ru' >Russian</option>"
						+"		<option value='sr' >Serbian</option>"
						+"		<option value='sk' >Slovak</option>"
						+"		<option value='sl' >Slovenian</option>"
						+"		<option value='es' >Spanish</option>"
						+"		<option value='sw' >Swahili</option>"
						+"		<option value='sv' >Swedish</option>"
						+"		<option value='th' >Thai</option>"
						+"		<option value='tr' >Turkish</option>"
						+"		<option value='uk' >Ukrainian</option>"
						+"		<option value='vi' >Vietnamese</option>"
						+"		<option value='cy' >Welsh</option>"
						+"		<option value='yi' >Yiddish</option>"
						+"		</select>"
						+"	</td>"
						+"  </tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='spin_text')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"			 
						+"	<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Attempt to re-write text to make it appear unique.'>"
						+"		Spin Text:<br> "
						+"	</td>"
						+"	<td align=right style='font-size:13px;'>"
						+"  	 <input type='hidden' name='nature' value='spin_text'>"
						+"		 <select name=spin_text>"
						+"			<option value=0 >Off</option>"
						+"			<option value=1 >Spin both titles & postbody</option>"
						+"			<option value=2 >Spin titles only</option>"
						+"			<option value=3 >Spin postbody only</option>"
						+"		</select>	"		 
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='post_author')
		{
			$('.class_settings_area').remove();
			var html = 	"<tr class='class_settings_area'>"
						+"	<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='All posts from this campaign will go into this category. If you only want select items, then use the include and exclude keywords to target your items.'>"
						+"		Post Author:<br> "
						+"	</td>"
						+"	<td align=right style='font-size:13px;'>"
						+"  	<input type='hidden' name='nature' value='post_author'>"
						+"		<select name=author>"<?php
										foreach ($authors_id as $k=>$v)
										{
											
												echo "+\"<option value=$v >$authors_usernames[$k]</option>\"";
										}
						?>
						+"  		<option value='keep_author' >[RSS ONLY] Keep Original</option>"
						+"  		<option value='rand' >Random Author</option>"
						+"		</select>"
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='category')
		{
			$('.class_settings_area').remove();
			<?php
					$get=   wp_dropdown_categories(array(selected=>$campaign_category,name=>'category' ,hierarchical=>1,id=>'articles_selects_cats', 'hide_empty'=>0, 'echo' =>0)); 
					$get =  nl2br($get);
					$get = str_replace(array("\r","\n"), '', $get);
					$get = str_replace('"', "'", $get);
			?>
			var html = 	"<tr class='class_settings_area'>"
						+"	<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='All posts from this campaign will go into this category. If you only want select items, then use the include and exclude keywords to target your items.'>"
						+"		Designated Category:<br> "
						+"	</td>"
						+"	<td align=right style='font-size:13px;'>"
						+"  	<input type='hidden' name='nature' value='category'>"
						+"  	<?php echo $get; ?>"
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='limit_comments')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"			 
						+"		<td  align=left valign=top style='font-size:13px;'>"
						+"			<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='If comments are available for this module should we include them?'>"
						+"			Include Comments:<br> "
						+"		</td>"
						+"		<td  align=right style='font-size:13px;'>"
						+"			<select name=comments_include id='id_comments_include_select' class='comments_include_select'>	"
						+"				<option value=1 selected=true>on</option>"
						+"				<option value=0>off</option>	"		 
						+"			</select>	"
						+"		</td>"
						+"	</tr>"
						+"<tr class='class_settings_area'>"
						+"		<td style='font-size: 13px;' align='left' valign='top'>"
						+"		<img src='./../nav/tip.png' style='cursor: pointer;' title='Will limit the number of sourced comments to publish. Leave at zero to source all possible comments.' border='0'>"
						+"  	<input type='hidden' name='nature' value='limit_comments'>"
						+"		Limit Comments:<br> </td><td style='font-size: 13px;' align='right'><input name='comments_limit' size='1' value='0'>"
						+"		</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='include_keywords')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"			 
						+"	<td  align=left valign=top style='font-size:11px; '>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='These keywords will determine what content makes into your blog. Leaving this field alone (or blank) to include all.'>"
						+"		Must Contain:<br>"
						+"		<input type=radio name='include_keywords_scope' value='1' > Search Title "
						+"		<input type=radio name='include_keywords_scope' value='2' > Search Body"
						+"		<input type=radio name='include_keywords_scope' value='3' > Search Both "
						+"	</td>"
						+"</tr>"
						+"<tr class='class_settings_area'>"
						+"	<td align=right style='font-size:13px;'>"
						+"  	<input type='hidden' name='nature' value='include_keywords'>"
						+"		<textarea name=include_keywords style='width:100%'></textarea>"
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='exclude_keywords')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"			 
						+"	<td  align=left valign=top style='font-size:11px; '>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='These keywords will determine what content makes into your blog. Leaving this field alone (or blank) to include all.'>"
						+"		Must Contain:<br>"
						+"		<input type=radio name='exclude_keywords_scope' value='1' > Search Title "
						+"		<input type=radio name='exclude_keywords_scope' value='2' > Search Body"
						+"		<input type=radio name='exclude_keywords_scope' value='3' > Search Both "
						+"	</td>"
						+"</tr>"
						+"<tr class='class_settings_area'>"
						+"	<td align=right style='font-size:13px;'>"
						+"  	<input type='hidden' name='nature' value='exclude_keywords'>"
						+"		<textarea name=exclude_keywords style='width:100%'></textarea>"
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='autocategorization')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"			 
						+"	<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Searches for keywords in titles and matches them to post categories. If there is a match the post is additionally categorized into that catgeory. If no match is found then post is categorized into the category set above.'>"
						+"		Auto-Categorize:<br> "
						+"	</td>"
						+"	<td  align=right style='font-size:13px;'>"
						+"  	<input type='hidden' name='nature' value='autocategorization'>"
						+"		<select name=autocategorize >"
						+"				<option value=1 selected=true>on</option>"
						+"				<option value=0>off</option>"
						+"		</select>"
						+"	</td>"
						+"</tr>"
						+"<tr class='class_settings_area'>	"		 
						+"	<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='This will tell blogsense where to look for matches in order to auto-sort into categories.'>"
						+"		Filtering Method:<br> "
						+"	</td>"
						+"	<td  align=right style='font-size:13px;'>"
						+"		<select name=autocategorize_search >"
						+"				<option value=1 >Search Titles Only</option>"
						+"				<option value=2 selected=true>Search Content & Titles</option>"
						+"		</select>	"
						+"	</td>"
						+"</tr>"
						+"<tr class='class_settings_area'>			 "
						+"	<td  align=left valign=top style='font-size:13px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='This will tell Blogsense how to add categories to posts. When automatching, BlogSense will search content for occurances of category slug in the content.'>"
						+"		Categorization Method:<br> "
						+"	</td>"
						+"	<td  align=right style='font-size:13px;'>"
						+"		<select name=autocategorize_method id='id_select_autocategorization_method'>"
						+"				<option value=1 >Automatch:First Category Only</option>"
						+"				<option value=2 >Automatch ALL: Include Parent Category</option>"
						+"				<option value=3 >Automatch ALL: Exclude Parent Category</option>"
						+"				<option value=5 selected=true>Batch List</option>"
						+"			"
						+"		</select>	"
						+"	</td>"
						+"</tr>"
						+"<tr class='class_tr_autocategorize_custom_filters' style='display:none''>"
						+"	<td colspan=2 align='left' style='font-size:13px;'>"
						+"	<br>"
						+"	</td>"
						+"</tr>"
						+"<tr class='class_settings_area'  style='display:none'>"
						+"	<td style='font-size:11px;'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='One per line please. If this keyword is detected in the content, then the post will be applied to the responding category.'>"
						+"		<i>Keyword</i>"
						+"	</td>"
						+"	<td style='font-size:11px;'>"
						+"		<i>Category Designation </i>"
						+"		&nbsp;"
						+"		&nbsp;"
						+"		"
						+"	</td>"
						+"	<td>"
						+"	<img src='./../nav/add.png' style='cursor:pointer;' id=id_add_custom_filters_button> <font style='text-decoration:italics; color:#aaaaaa; font-size:11px'>"
						+"	 "
						+"	</td>"
						+"</tr>"
						+"<tr class='class_settings_area' >"
						+"	<td style='font-size:12px;' colspan=2>"
						+"		<br><b>Below is for the batch list option only</b><br>"
						+"		This feature allows for a quicker categorization filter setup for blogs with many categories and keywords.<br><br>"
						+"		<i>Example Line Format: keyword,keyword,keyword:cat_slug</i>"
						+"	</td>"
						+"</tr>"
						+"<tr class='class_settings_area'>"
						+"	<td style='font-size:13px;' colspan=2>"															
						+"		<textarea name='autocategorize_filter_list' style='font-size:12px;width:480px;height:400px;'></textarea>"
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='regex')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"	
						+"	<td  align=middle style='font-size:11px;color:#aaaaaa'>"
						+"  	<input type='hidden' name='nature' value='regex'>"
						+"		<i>Search String</i>"
						+"	</td>"
						+"	<td  align=middle style='font-size:11px;color:#aaaaaa'>"
						+"		<i>Replace String</i>"
						+"	</td>"
						+"</tr>"
						+"<tr class='class_settings_area'><td  align=left valign=middle style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><textarea  name=\"regex_search[]\" cols=26></textarea></td><td  align=right style=\"font-size:13px;\"><textarea cols=26  name=\"regex_replace[]\" ></textarea></td></tr>"
						+"<tr class='class_settings_area'><td  align=left valign=middle style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><textarea  name=\"regex_search[]\" cols=26></textarea></td><td  align=right style=\"font-size:13px;\"><textarea cols=26  name=\"regex_replace[]\" ></textarea></td></tr>"
						+"<tr class='class_settings_area'><td  align=left valign=middle style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><textarea  name=\"regex_search[]\" cols=26></textarea></td><td  align=right style=\"font-size:13px;\"><textarea cols=26  name=\"regex_replace[]\" ></textarea></td></tr>"
						+"<tr class='class_settings_area'><td  align=left valign=middle style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><textarea  name=\"regex_search[]\" cols=26></textarea></td><td  align=right style=\"font-size:13px;\"><textarea cols=26  name=\"regex_replace[]\" ></textarea></td></tr>"
						+"<tr class='class_settings_area'><td  align=left valign=middle style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><textarea  name=\"regex_search[]\" cols=26></textarea></td><td  align=right style=\"font-size:13px;\"><textarea cols=26  name=\"regex_replace[]\" ></textarea></td></tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='custom_fields')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"	
						+"		<td colspan=2 align='left' style='font-size:13px;'>"
						+"  		<input type='hidden' name='nature' value='custom_fields'>"
						+"			<br><br>"
						+"			 %image_1% will call the url of the first image found.<br>%image_2% will call the second image url.</font>"
						+"			<br>"
						+"		 </td>"
						+"	</tr>"
						+"	<tr class='class_settings_area'>"
						+"		<td colspan=2>"
						+"			<table id='id_custom_fields' width=100%>"
						+"				<tr>"
						+"					<td  align='center' style='font-size:11px;color:#aaaaaa' width='50%'>"
						+"						<i>Field Name</i>"
						+"					 </td>"
						+"					  <td  align='center' style='font-size:11px;color:#aaaaaa' width='50%'>"
						+"						<i>Value</i>"
						+"					</td>"
						+"				</tr>"
						+"				<tr>"
						+"					 <td  align=left style=\"font-size:13px;\">"
						+"						<img class=remove_custom_field onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">"
						+"						&nbsp;<input size=28 name=\"custom_field_name[]\" value=''>"
						+"					 </td>"
						+"					 <td  align=right style=\"font-size:13px;\">"
						+"						<input size=35 name=\"custom_field_value[]\" value=''>"
						+"					</td>"
						+"				  </tr>"
						+"				<tr>"
						+"					 <td  align=left style=\"font-size:13px;\">"
						+"						<img class=remove_custom_field onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">"
						+"						&nbsp;<input size=28 name=\"custom_field_name[]\" value=''>"
						+"					 </td>"
						+"					 <td  align=right style=\"font-size:13px;\">"
						+"						<input size=35 name=\"custom_field_value[]\" value=''>"
						+"					</td>"
						+"				  </tr>"
						+"				<tr>"
						+"					 <td  align=left style=\"font-size:13px;\">"
						+"						<img class=remove_custom_field onClick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\">"
						+"						&nbsp;<input size=28 name=\"custom_field_name[]\" value=''>"
						+"					 </td>"
						+"					 <td  align=right style=\"font-size:13px;\">"
						+"						<input size=35 name=\"custom_field_value[]\" value=''>"
						+"					</td>"
						+"				  </tr>"
						+"			</table>"
						+"		</td>"
						+"	</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='bookmarking')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"	
						+"	<td colspan=2 align='left' style='font-size:13px;' width='400'>"
						+"  		<input type='hidden' name='nature' value='bookmarking'>"
						+"<div>"
						+"	<table width=489 style='margin-left:auto;margin-right:auto;line-height:17px;'> "
						+"		<tr>"
						+"			<td colspan=3 align='left' style='font-size:13px;'>"
						+"				<img src='./../nav/logo_twitter.png' style='max-width:100px;max-height:200px;'>"
						+"				<br>"
						+"				<font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> "
						+"				</font>					"
						+"			 </td>"
						+"		</tr>"
						+"		<tr>"
						+"			<td  align='left' style='font-size:11px;color:#aaaaaa' width='10%'>"
						+"				<i>Enable</i>"
						+"			 </td>	"
						+"			<td  align='left' style='font-size:11px;color:#aaaaaa' width='50%'>"
						+"				<i>Twitter Account</i>"
						+"			 </td>		"
						+"			<td  align='left' style='font-size:11px;color:#aaaaaa' width='50%'>"
						+"				<img src='./../nav/tip.png' style='cursor:pointer;'  title='Mark this campaign's tweets with a hashtag for further exposure.' border=0>"
						+"				<i>Hashtag</i>"
						+"			 </td>"	
						+"		</tr>"
								<?php
									if (strlen($twitter_user[0])>2)
									{
						
										
										//print_r($bookmark_twitter_status);
										 foreach ($twitter_user as $key=>$value)
										 {
											?>
											+"<tr>"
											+"	<td  align=left style='font-size:13px;color:#aaaaaa; font-size:11px'>"
											+"		<input type='checkbox' name='bookmark_twitter_status_<?php echo $key; ?>' value='on'>"
											+"	</td>"
											+"	<td  align=left style='font-size:13px;color:#aaaaaa; font-size:11px'>"
											+"		<i><?php echo $value; ?></i>"
											+"	</td>"
											+"	<td  align=left style='font-size:13px;color:#aaaaaa; font-size:11px'>"
											+"		<input size=25 name='bookmark_twitter_hash_<?php echo $key; ?>' value=''>"
											+"	</td>"
											+"</tr>"
											<?php							
										 }
									}
								 ?>	
							+"</table>"
							+"<table width=489 id='id_table_remote_publishing_pp' style='margin-left:auto;margin-right:auto;line-height:17px;'>"
							+"	<tr>"
							+"		<td colspan=3 align='left' style='font-size:13px;'>"
							+"			<br><br>"
							+"			<img src='./../nav/logo_pixelpipe.png' style='max-width:100px;max-height:200px;'>"
							+"			<font style='text-decoration:italics; color:#aaaaaa; font-size:11px'> "
							+"			</font>	"		
							+"		 </td>"
							+"	</tr>"
							+"	<tr>"
							+"		<td  align='left' style='font-size:11px;color:#aaaaaa' width='10%'>"
							+"			<i>Enable</i>"
							+"		 </td>	"
							+"		<td  align='left' style='font-size:11px;color:#aaaaaa' width='50%'>"
							+"			<i>Pixelpipe Account Email</i>"
							+"		 </td>		"
							+"		<td  align='left' style='font-size:11px;color:#aaaaaa' width='50%'>"
							+"			<i>Pixelpipe Routing Tags</i>"
							+"		 </td>	"
							+"	</tr>"
								<?php
									if (strlen($pixelpipe_email[0])>2)
									{
																				
										//print_r($bookmark_pixelpipe_status);exit;
										 foreach ($pixelpipe_email as $key=>$value)
										 {
											?>
											+"<tr>"
											+"	<td  align=left style='font-size:13px;color:#aaaaaa; font-size:11px'>"
											+"		<input type='checkbox' name='bookmark_pixelpipe_status_<?php echo $key; ?>' value='on' >"
											+"	</td>"
											+"	<td  align=left style='font-size:13px;color:#aaaaaa; font-size:11px'>"
											+"		<i><?php echo $value; ?></i>"
											+"	</td>"
											+"	<td  align=left style='font-size:13px;color:#aaaaaa; font-size:11px'>"
											+"		<?php echo $pixelpipe_routing[$key]; ?>"
											+"	</td>"
											+"</tr>"
											<?php							
										 }
									}
								 ?>				
							+"</table>"
						+"</div>"
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='yahoo_question_nature')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"	
						+"	<td  align=left valign=top style='font-size:13px;'>"
						+"  		<input type='hidden' name='nature' value='yahoo_question_nature'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Determines the nature of yahoo answer - question results.'>"
						+"		Question Nature<br>"
						+"	</td>"
						+"	<td align=right style='font-size:13px;'>"
						+"		<select name=z_yahoo_option_type>"				 
						+"			<option value='all' >All</option>"
						+"			<option value='resolved' >Resolved Questions </option>"
						+"			<option value='open' >Undecided Questions</option>"
						+"			<option value='undecided'>Open Questions </option>"
						+"		</select>"
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='yahoo_date_range')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"	
						+"	 <td  align=left valign=top style='font-size:13px;'>"
						+"  		<input type='hidden' name='nature' value='yahoo_date_range'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Determines the the timeline to pull from.'>"
						+"		Date Range<br> </td>"
						+"	 <td align=right style='font-size:13px;'>"
						+"		 <select name=z_yahoo_option_date_range>"			 
						+"		   <option value=all >Anytime</option>"
						+"		   <option value='7' >Within 7 Days</option>"
						+"		   <option value='7-30' >Within 7-30 Days</option>"
						+"		   <option value='40-60' >Within 30-60 Days</option>"
						+"		   <option value='60-90'Within 60-90 Days</option>"
						+"		 </select>"
						+"	 </td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='yahoo_target_region')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"	
						+"	 <td  align=left valign=top style='font-size:13px;'>"
						+"  		<input type='hidden' name='nature' value='yahoo_target_region'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Determines the nature of yahoo answer - question results.'>"
						+"		Target Region<br> </td>"
						+"	<td align=right style='font-size:13px;'>"
						+"		<select name=z_yahoo_option_region>	"		 
						+"			<option value='us' >United States</option>"
						+"			<option value='uk' >United Kingdom</option>"
						+"			<option value='ca' >Canada</option>"
						+"			<option value='au' >Australia</option>"
						+"			<option value='in' >India</option>"
						+"			<option value='es' >Spain</option>"
						+"			<option value='br' >Brazil</option>"
						+"			<option value='ar' >Argentina</option>"
						+"			<option value='mx' >Mexico</option>"
						+"			<option value='e1' >In Espanol</option>"
						+"			<option value='it' >Italy</option>"
						+"			<option value='de' >Germany</option>"
						+"			<option value='fr' >France</option>"
						+"			<option value='sg' >Singapore</option>"
						+"		</select>"
						+"	 </td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='yahoo_sorting')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"	
						+"	 <td  align=left valign=top style='font-size:13px;'>"
						+"  		<input type='hidden' name='nature' value='yahoo_sorting'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='Determines the nature of yahoo answer - question results.'>"
						+"		Sorting<br> </td>"
						+"	 <td align=right style='font-size:13px;'>"
						+"		 <select name=z_yahoo_option_sorting>"		 
						+"			<option value='relevance' >By Relevance</option>"
						+"			<option value='date_desc' >By date, newest first</option>"
						+"			<option value='date_asc' >By date, oldest first.</option>"
						+"		 </select>"
						+"	 </td>"
						+"</tr>	";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
		else if (input=='fileimport_update_old_posts')
		{
			$('.class_settings_area').remove();
			
			var html = 	"<tr class='class_settings_area'>"	
						+"	<td  align=left valign=top style='font-size:13px;'>"
						+"  		<input type='hidden' name='nature' value='fileimport_update_old_posts'>"
						+"		<img src='./../nav/tip.png' style='cursor:pointer;' border=0 title='This feature will update already published items. For CSV the title must be the same and for text files the filename must be the same for an update to occur. Otherwise no post will be published.'>"
						+"		Update Old Posts:<br> "
						+"	</td>"
						+"	<td align=right style='font-size:13px;'>"
						+"		<select name=post_overwrite>"
						+"			<option value=1>on</option>"
						+"			<option value=0 selected=true>off</option>"
						+"		</select>	"
						+"	</td>"
						+"</tr>";
				
			$(".class_edit_area").append(html);
			$(".class_edit_area").append(save);
		}
	});
});
</script>

<?php
 if ($_GET['global_save']==1&&$_GET['testing']!=1)
 {
	//get variables
	$campaign_id = $_POST['campaign_id'];
	$count = count($campaign_id);
	
	//echo $_POST['nature'];exit;
	if ($_POST['nature']=='enable_disable')
	{
		$campaign_status = $_POST['campaign_status'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET campaign_status='{$campaign_status}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>Post status has been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='title_template')
	{
		$title_template = stripslashes($_POST['title_template']);
		$title_template = addslashes($title_template);
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_title_template='{$title_template}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The title template has been altered for $count items.</center>";
		exit;
		
	}
	
	if ($_POST['nature']=='postbody_template')
	{
		$postbody_template = stripslashes($_POST['postbody_template']);
		$postbody_template = addslashes($postbody_template);
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_post_template='{$postbody_template}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The post-body template has been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='limit')
	{
		$limit_results = $_POST['limit_results'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET limit_results='{$limit_results}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The limit has been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='post_frequency')
	{
		$post_frequency = $_POST['post_frequency'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET schedule_post_frequency='{$post_frequency}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The post frequency has been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='post_frequency_start_date')
	{
		$post_frequency_start_date = $_POST['post_frequency_start_date'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET schedule_post_date='{$post_frequency_start_date}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The post frequency date pointer has been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='post_overwrite')
	{
		$post_status = $_POST['post_status'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_post_overwrite='{$post_status}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The post status setting has been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='post_status')
	{
		$post_status = $_POST['post_status'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_post_status='{$post_status}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The post status setting has been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='link_options')
	{
		$strip_links = $_POST['strip_links'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET strip_links='{$strip_links}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The link-handling option has been altered for $count items.</center>";
		exit;
	}
	
	
	if ($_POST['nature']=='strip_images')
	{
		$strip_images = $_POST['strip_images'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET strip_images='{$strip_images}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The image stripping option has been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='language')
	{
		$language = $_POST['language'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET language='{$language}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The translation setting has been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='spin_text')
	{
		$spin_text = $_POST['spin_text'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET spin_text='{$spin_text}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The text spinning settings have been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='post_author')
	{
		$author = urlencode($_POST['author']);
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET author='{$author}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The author setting has been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='include_keywords')
	{
		$include_keywords = $_POST['include_keywords'];
		$include_keywords = addslashes($include_keywords);
		$include_keywords_scope =$_POST['include_keywords_scope'];
		if (!$include_keywords) { $include_keywords = "Separate with commas."; }
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET include_keywords='{$include_keywords}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The include keywords have been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='exclude_keywords')
	{
		$exclude_keywords = $_POST['exclude_keywords'];
		$exclude_keywords = addslashes($exclude_keywords);
		$exclude_keywords_scope =$_POST['exclude_keywords_scope'];
		if (!$exclude_keywords) { $exclude_keywords = "Separate with commas."; }
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET exclude_keywords='{$exclude_keywords}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The exclude keywords have been altered for $count items.</center>";
		exit;
		
	}

	if ($_POST['nature']=='category')
	{
		$category = $_POST['category'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET category='{$category}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The category setting has been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='autocategorize')
	{
		$autocategorize = $_POST['autocategorize'];
		$autocategorize_search = $_POST['autocategorize_search'];
		$autocategorize_method = $_POST['autocategorize_method'];
		$autocategorize_filter_keywords = $_POST['autocategorize_filter_keywords'];
		$autocategorize_filter_categories = $_POST['autocategorize_filter_categories'];
		$autocategorize_filter_list = addslashes($_POST['autocategorize_filter_list']);
	
		if ($autocategorize_filter_keywords)
		{
			$autocategorize_filter_keywords = implode(';',$autocategorize_filter_keywords);
			$autocategorize_filter_categories = implode(';',$autocategorize_filter_categories);
		}
		
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET autocategorize='{$autocategorize}' WHERE id='{$val}'");
			$query = bs_query("UPDATE {$table_prefix}campaigns SET autocategorize_search='{$autocategorize_search}' WHERE id='{$val}'");
			$query = bs_query("UPDATE {$table_prefix}campaigns SET autocategorize_method='{$autocategorize_method}' WHERE id='{$val}'");
			$query = bs_query("UPDATE {$table_prefix}campaigns SET autocategorize_filter_keywords='{$autocategorize_keywords}' WHERE id='{$val}'");
			$query = bs_query("UPDATE {$table_prefix}campaigns SET autocategorize_filter_categories='{$autocategorize_categories}' WHERE id='{$val}'");
			$query = bs_query("UPDATE {$table_prefix}campaigns SET autocategorize_filter_list='{$autocategorize_list}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The autocategorization settings have been altered for $count items.</center>";
		exit;
		
		
	}
	
	if ($_POST['nature']=='regex')
	{
		$regex_search = $_POST['regex_search'];
		$regex_replace = $_POST['regex_replace'];	
		$regex_search = implode('***r***', $regex_search);
		$regex_replace = implode('***r***', $regex_replace);
		
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET regex_search='{$regex_search}' WHERE id='{$val}'");
			$query = bs_query("UPDATE {$table_prefix}campaigns SET regex_replace='{$regex_replace}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The regex filters have been altered for $count items.</center>";
		exit;
	}

	if ($_POST['nature']=='custom_fields')
	{
		$custom_field_name = $_POST['custom_field_name'];
		$custom_field_value = $_POST['custom_field_value'];
		
		if ($custom_field_name)
		{
			$custom_field_name = implode('***',$custom_field_name);
			$custom_field_value = implode('***',$custom_field_value);
		}
		
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET custom_field_name='{$custom_field_name}' WHERE id='{$val}'");
			$query = bs_query("UPDATE {$table_prefix}campaigns SET custom_field_value='{$custom_field_value}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>Custom fields have been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='include_comments')
	{
		$comments_include = $_POST['comments_include'];
		$comments_limit = $_POST['comments_limit'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_comments_include='{$comments_include}' WHERE id='{$val}'");
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_comments_limit='{$comments_limit}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The comment limits have have been altered for $count items.</center>";
		exit;
	}
	
	if ($_POST['nature']=='bookmarking')
	{
		$bookmark_twitter= $_POST['bookmark_twitter'];
		$bookmark_pixelpipe= $_POST['bookmark_pixelpipe'];
		
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
		
		foreach ($campaign_id as $key=>$val)
		{
			
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_bookmark_twitter='{$z_bookmark_twitter}' AND z_bookmark_pixelpipe='{$z_bookmark_pixelpipe}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>The bookmarking settings have been altered for $count items.</center>";
		exit;
	}

	if ($_POST['nature']=='yahoo_question_nature')
	{
		$z_yahoo_option_nature = $_POST['z_yahoo_option_nature'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_yahoo_option_type='{$z_yahoo_option_nature}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>This Yahoo setting has been applied to $count campaigns.</center>";
		exit;
	}
	
	if ($_POST['nature']=='yahoo_date_range')
	{
		$z_yahoo_option_date_range = $_POST['z_yahoo_option_date_range'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_yahoo_option_date_range='{$z_yahoo_option_date_range}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>This Yahoo setting has been applied to $count campaigns.</center>";
		exit;
	}
	
	if ($_POST['nature']=='yahoo_region')
	{
		$z_yahoo_option_region = $_POST['z_yahoo_option_region'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_yahoo_option_region='{$z_yahoo_option_region}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>This Yahoo setting has been applied to $count campaigns.</center>";
		exit;
	}
	
	if ($_POST['nature']=='yahoo_sorting')
	{
		$z_yahoo_option_sorting = $_POST['z_yahoo_option_sorting'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_yahoo_option_sorting='{$z_yahoo_option_sorting}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>This Yahoo setting has been applied to $count campaigns.</center>";
		exit;
	}
	

	if ($_POST['nature']=='fileimport_update_old_post')
	{
		$post_overwrite =$_POST['post_overwrite'];
		foreach ($campaign_id as $key=>$val)
		{
			$query = bs_query("UPDATE {$table_prefix}campaigns SET z_post_overwtite='{$post_overwrite}' WHERE id='{$val}'");
		}
		echo "<center><br><br><br><font style='color:green;'>This post overwrite setting has been applied to $count campaigns.</center>";
		exit;
	}
	
	
 }

?>
<form action='?global_save=1' method='POST' id='form_global_settings'>
<table width=100%>
<tr>
	<td colspan=2 valign=top>
		<h2>Select Campaigns</h2>
		<select multiple='multiple' name='campaign_id[]' style='width:100%;height:230px;'>
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
	<td valign=top width='300px'>
		<h2>Edit Criteria</h2>
		<select id='id_select_criteria' size='25'>
			<option value='enable_disable'>Activate/Deactivate</option>
			<option value='title_template'>Title Template</option>
			<option value='postbody_template'>Postbody Template</option>
			<option value='limit'>Results Limit</option>
			<option value='post_frequency'>Post Frequency</option>
			<option value='post_frequency_start_date'>Post Sheduling Start Date</option>
			<option value='post_status'>Post Status</option>
			<option value='post_overwrite'>[RSS Module] Duplicate Content Check </option>
			<option value='link_options'>Link Options</option>
			<option value='strip_images'>Strip Images</option>
			<option value='language'>Translation</option>
			<option value='spin_text'>Text Spinning</option>
			<option value='post_author'>Author</option>
			<option value='category'>Category</option>
			<option value='limit_comments'>Limit Comments</option>
			<option value='include_keywords'>Include Keywords</option>
			<option value='exclude_keywords'>Exclude Keywords</option>
			<option value='autocategorization'>Auto-Categorization</option>
			<option value='regex'>Regular Expressions</option>
			<option value='custom_fields'>Custom Fields</option>
			<option value='bookmarking'>Bookmarking</option>
			<option value='yahoo_question_nature'>[Yahoo Answers] Question Nature</option>
			<option value='yahoo_date_range'>[Yahoo Answers] Date Range</option>
			<option value='yahoo_target_region'>[Yahoo Answers] Target Region</option>
			<option value='yahoo_sorting'>[Yahoo Answers] Sorting</option>
			<option value='fileimport_update_old_posts'>[FileImprot/CSV] Update old posts</option>

		</select>
	</td>
	<td  valign=top class='class_edit_area'>
		
		<h2>Settings Area</h2>
		<div class='class_settings_area'>
			<i>Please select the campaigns and the setting you would like to apply globally.</i>
		</div>
	</td>
</tr>
</table>
</form>