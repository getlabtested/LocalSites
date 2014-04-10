<?php
include_once('./../../wp-config.php');
session_start();

include("./../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../login.php");

//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}

if ($_GET['saved']==y)
{
	echo "<center><br><br><br><br><font color=green>Settings Saved. </center></font>";
	exit;
}
if (!$_POST['nature'])
{
    
	$query = "SELECT * FROM ".$table_prefix."sourcedata";
	$result = mysql_query($query);
	if (!$result){echo $query; exit;}

	function s_encode($input)
	{
	  $input = htmlspecialchars($input);
	  $input = str_replace(chr(13), "**13**", $input);
	  $input = str_replace(chr(10), "**10**", $input);
	  $input = str_replace("'", "\'", $input);
	  return $input;
	}
	
	while ($array = mysql_fetch_array($result))
	{
		$source_id[] = s_encode($array['id'], ENT_QUOTES);
		$source_url[] = s_encode($array['source_url'], ENT_QUOTES);
		$title_start[] = s_encode($array['title_start'], ENT_QUOTES);
		$title_start_backup_1[] = s_encode($array['title_start_backup_1'], ENT_QUOTES);
		$title_start_backup_2[] = s_encode($array['title_start_backup_2'], ENT_QUOTES);
		$title_end[] = s_encode($array['title_end'], ENT_QUOTES);
		$title_end_backup_1[] = s_encode($array['title_end_backup_1'], ENT_QUOTES);
		$title_end_backup_2[] = s_encode($array['title_end_backup_2'], ENT_QUOTES);
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
		$footprint[] = s_encode($array['footprint'], ENT_QUOTES);
	}
    
	?>
	<html>
	<head>
	<script type="text/javascript" src="./../includes/jquery.js"></script>
	<script type="text/javascript"> 
	
	function urlencode(str) {
		return escape(str).replace(/\+/g,'%2B').replace(/%20/g, '+').replace(/\*/g, '%2A').replace(/\//g, '%2F').replace(/@/g, '%40');
	}
	
	function Chr(AsciiNum)
	{
		return String.fromCharCode(AsciiNum);
	}

	function decode(string, quote_style) 
	{
		var optTemp = 0, i = 0, noquotes= false;
		if (typeof quote_style === 'undefined') {        quote_style = 2;
		}
		string = string.toString().replace(/&lt;/g, '<').replace(/&gt;/g, '>');
		var OPTS = {
			'ENT_NOQUOTES': 0,        'ENT_HTML_QUOTE_SINGLE' : 1,
			'ENT_HTML_QUOTE_DOUBLE' : 2,
			'ENT_COMPAT': 2,
			'ENT_QUOTES': 3,
			'ENT_IGNORE' : 4    };
		if (quote_style === 0) {
			noquotes = true;
		}
		if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags        quote_style = [].concat(quote_style);
			for (i=0; i < quote_style.length; i++) {
				// Resolve string input to bitwise e.g. 'PATHINFO_EXTENSION' becomes 4
				if (OPTS[quote_style[i]] === 0) {
					noquotes = true;            }
				else if (OPTS[quote_style[i]]) {
					optTemp = optTemp | OPTS[quote_style[i]];
				}
			}        quote_style = optTemp;
		}

		string = string.replace(/&#039;/g, "'"); 
		while (string.match(/\*\*10\*\*/))
		{
			string = string.replace(/\*\*10\*\*/, Chr(10));
		}
		while (string.match(/\*\*13\*\*/))
		{
			string = string.replace(/\*\*13\*\*/, Chr(13));
		}
		
		//string = "4";
		// string = string.replace(/&apos;|&#x0*27;/g, "'"); // This would also be useful here, but not a part of PHP    }

		if (!noquotes) {
			string = string.replace(/&quot;/g, '"');
		}
		
		// Put this in last place to avoid escape being double-decoded    string = string.replace(/&amp;/g, '&');
		return string;
	}
	$(document).ready(function() 
	{
		$("img.add_articles_string_edit").live("click" ,function(){
		   $('#id_table_regex tr:last').after('<tr><td  align=left style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><textarea name=\"regex_search[]\" ></textarea></td><td  align=right style=\"font-size:13px;\"><textarea name=\"regex_replace[]\"></textarea></td></tr>');
		});
		$('.class_inputs').attr('disabled', 'disabled');
		$('#id_form_nature').val('x');
		$('#id_sources').val('x');
		$("#id_sources").change(function(){
		    $('#id_form_2').fadeOut("fast", function(){
				//delay(1);
				$('#id_form_1').fadeIn("slow");
			});
			var site = $('#id_sources').val();
			switch (site){
				case "x":
						$('.class_inputs').attr('disabled', 'disabled');
						$('#id_source_id').val('x');
						$('#id_source_url').val('');
						$('#id_footprint').val('');
						$('#id_title_start').val('');
						$('#id_title_start_backup_1').val('');
						$('#id_title_start_backup_2').val('');
						$('#id_title_end').val('');
						$('#id_title_end_backup_1').val('');
						$('#id_title_end_backup_2').val('');
						$('#id_content_start').val('');
						$('#id_content_start_backup_1').val('');
						$('#id_content_start_backup_2').val('');
						$('#id_content_end').val('');
						$('#id_content_end_backup_1').val('');
						$('#id_content_end_backup_2').val('');
						$('#id_comments_name_start').val('');
						$('#id_comments_name_end').val('');
						$('#id_comments_content_start').val('');
						$('#id_comments_content_end').val('');
						$('#id_comments_status_2').attr('checked', true);
						$('#id_form_nature').val('x');	
						<?php
						echo"   $('#id_table_regex').find('tr').remove();";		
						?>
					break;
				case "new":
						$('.class_inputs').removeAttr('disabled');
						$('#id_source_id').val('x');
						$('#id_source_url').val('');
						$('#id_footprint').val('');
						$('#id_title_start_backup_1').val('');
						$('#id_title_start_backup_2').val('');
						$('#id_title_end').val('');
						$('#id_title_end_backup_1').val('');
						$('#id_title_end_backup_2').val('');
						$('#id_content_start').val('');
						$('#id_content_start_backup_1').val('');
						$('#id_content_start_backup_2').val('');
						$('#id_content_end').val('');
						$('#id_content_end_backup_1').val('');
						$('#id_content_end_backup_2').val('');
						$('#id_comments_name_start').val('');
						$('#id_comments_name_end').val('');
						$('#id_comments_content_start').val('');
						$('#id_comments_content_end').val('');					
						$('#id_comments_status_2').attr('checked', true);
						$('#id_form_nature').val('new');
				<?php
				echo"   $('#id_table_regex').find('tr').remove();			
						$('#id_table_regex').append('<tr><td colspan=2 align=middle style=\"font-size:13px;\"><a href=\"./../includes/pdfs/Using_Regular_Expressions.pdf\" target=_blank><img src=\"./../nav/tip.png\" style=\"cursor:pointer;\" border=0 title=\"Information on Regular Expressions.\" border=0></a>Regex Search & Replace </td></tr><tr><td  align=middle style=\"font-size:11px;color:#aaaaaa\"><i>Search String</i></td><td  align=middle style=\"font-size:11px;color:#aaaaaa\"><i>Replace String</i></td></tr><tr><td colspan=2 align=middle style=\"font-size:13px;\"><center><img src=\"./../nav/add.png\" style=\"cursor:pointer;\" id=\"articles_string_edit_button\" class=\"add_articles_string_edit\"></center></td></tr>');
						break;
					";	
				if ($source_url)
				{
					foreach ($source_url as $key=>$v)
					{
						echo"case '$source_url[$key]':
							var source_id = '$source_id[$key]';
							var source_url = '$source_url[$key]';
							var footprint = '$footprint[$key]';
							var title_start = '$title_start[$key]';
							var title_start_backup_1 = '$title_start_backup_1[$key]';
							var title_start_backup_2 = '$title_start_backup_2[$key]';
							var title_end = '$title_end[$key]';
							var title_end_backup_1 = '$title_end_backup_1[$key]';
							var title_end_backup_2 = '$title_end_backup_2[$key]';
							var content_start = '$content_start[$key]';
							var content_start_backup_1 = '$content_start_backup_1[$key]';
							var content_start_backup_2 = '$content_start_backup_2[$key]';
							var content_end = '$content_end[$key]';
							var content_end_backup_1 = '$content_end_backup_1[$key]';
							var content_end_backup_2 = '$content_end_backup_2[$key]';
							var comments_status = '$comments_status[$key]';
							var comments_name_start = '$comments_name_start[$key]';
							var comments_name_end = '$comments_name_end[$key]';
							var comments_content_start = '$comments_content_start[$key]';
							var comments_content_end = '$comments_content_end[$key]';
						";
						$count_regex_search = count($regex_search);
						echo"   $('#id_table_regex').find('tr').remove();			
								$('#id_table_regex').append('<tr><td colspan=2 align=middle style=\"font-size:13px;\"><a href=\"./../includes/pdfs/Using_Regular_Expressions.pdf\" target=_blank><img src=\"./../nav/tip.png\" style=\"cursor:pointer;\" border=0 title=\"Information on Regular Expressions.\" border=0></a>Regex Search & Replace </td></tr><tr><td  align=middle style=\"font-size:11px;color:#aaaaaa\"><i>Search String</i></td><td  align=middle style=\"font-size:11px;color:#aaaaaa\"><i>Replace String</i></td></tr><tr><td colspan=2 align=middle style=\"font-size:13px;\"><center><img src=\"./../nav/add.png\" style=\"cursor:pointer;\" id=\"articles_string_edit_button\" class=\"add_articles_string_edit\"></center></td></tr>');
						";	
						echo"
							
							$('.class_inputs').removeAttr('disabled');
							$('#id_source_id').val(decode(source_id));
							$('#id_source_url').val(decode(source_url));
							$('#id_footprint').val(decode(footprint));
							$('#id_title_start').val(decode(title_start));
							$('#id_title_start_backup_1').val(decode(title_start_backup_1));
							$('#id_title_start_backup_2').val(decode(title_start_backup_2));
							$('#id_title_end').val(decode(title_end));
							$('#id_title_end_backup_1').val(decode(title_end_backup_1));
							$('#id_title_end_backup_2').val(decode(title_end_backup_2));
							$('#id_content_start').val(decode(content_start));
							$('#id_content_start_backup_1').val(decode(content_start_backup_1));
							$('#id_content_start_backup_2').val(decode(content_start_backup_2));
							$('#id_content_end').val(decode(content_end));
							$('#id_content_end_backup_1').val(decode(content_end_backup_1));
							$('#id_content_end_backup_2').val(decode(content_end_backup_2));
							$('#id_comments_name_start').val(decode(comments_name_start));
							$('#id_comments_name_end').val(decode(comments_name_end));
							$('#id_comments_content_start').val(decode(comments_content_start));
							$('#id_comments_content_end').val(decode(comments_content_end));
							$('#id_form_nature').val('update');
						
							var yahoo_link = 'http://search.yahoo.com/search?n=40&p=site%3A'+urlencode(source_url)+' '+footprint+'&fr2=sb-top&fr=siteexplorer&sao=1';
							$('#id_yahoo_search').html('<a href=\"'+yahoo_link+'\" target=_new><img src=\"./../nav/yahoo.png\" border=0  title = \"Test this profiles search capability\" ></a>');
							";
						if (strlen($regex_search[$key][0])>1)
						{			    
							foreach ($regex_search[$key] as $k=>$v)
							{
								echo "$('#id_table_regex tr:last').after('<tr><td  align=left style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./../nav/remove.png\" style=\"cursor:pointer;\"><textarea name=\"regex_search[]\">".addslashes($v)."</textarea></td><td  align=right style=\"font-size:13px;\"><textarea name=\"regex_replace[]\">".addslashes($regex_replace[$key][$k])."</textarea></td></tr>');";
							}
						}
							
						echo "	
							if (comments_status==1)
							{
								$('#id_comments_status_1').attr('checked', true);
							}
							else
							{
								$('#id_comments_status_2').attr('checked', true);
							}
							
							break;";
					}
				}
				?>
			}			
		 
		});
		$('#id_import_sql').live('click',function(){
		    $('#id_submit_sql').removeAttr('disabled');
		    $('#id_form_1').fadeOut("fast", function(){
				//delay(1);
				$('#id_form_2').fadeIn("slow");
			});
		});
		
		$("#id_submit").click(function(){
			$("#id_form_source").submit();			
		});
		
		$("#id_delete").click(function(){
			$('#id_form_nature').val('delete');
		    if (confirm('Would you like to delete this source?'))
			{
				$("#id_form_source").submit();
			}
			else
			{
				$('#id_form_nature').val('update');
			}
			
		 });
		 
		 $("#id_export").click(function(){
			$('#id_form_nature').val('export');
		    $("#id_form_source").submit();	
			
		 });
	});
	</script>
	</head>
	<body style="font-family:Khmer UI;">

	<div style="font-size:14px;width:400;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">Manage Sources<div style='float:right'><img src='./../nav/import.gif' border=0 title='Import SQL' id=id_import_sql style='cursor:pointer;'></div>
			<hr width=400 style="color:#eeeeee;background-color:#eeeeee;">
			 
			<table width=400 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee"> 
			  <tr>
				 <td  align=left valign=top style="font-size:13px; width:300px;">
					<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Input RSS URL here.">
						 Source Selection:<br>
				 </td>
				 <td align=left style="font-size:13px;">
					 <select name="sources" id="id_sources">
						<option value='x' selected>Please Select Source</option>
						<?php
						foreach ($source_url as $k=>$v)
						{
						   echo "<option value='$v'>$v</option>";
						}
						?>
						<option value='new'>Add New Source</option>						
					 </select>
						
				 </td>
			  </tr>
			</table>
			<table width=400 style="margin-left:auto;margin-right:auto;"> 
			  <tr>				 
				 <td align=left style="font-size:13px;">
					<?php
						$example = "<div id='post_body_%wildcard%'>";
						$example = htmlspecialchars($example);
					?>
					<font style='font-size:10px;'>
						<i>Note: When setting up scrape paramaters, you can use the token %wildcard% to represent dynamic parts of code. 
						<br>Example Begin Code: <?php echo $example; ?>.</i> 
					</font>
				 </td>
			  </tr>
			</table>
			<br>
			<div id=id_form_1>
			<form action='' method=POST name='save_source' id='#id_form_source'>
			<input type=hidden name='nature' value='x' id='id_form_nature'>
			<input type=hidden name='source_id' value='x' id='id_source_id'>
			<div id=source_settings style=''>
				<table id=id_source_data width=400 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee"> 
					<tr>
						<td style='font-size:13px;'>
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This URL will be used for searching out material and scraping.">
							<i>Source URL:</i></td><td align=right><input class='class_inputs' id=id_source_url name=source_url size=33>
						</td>
					</tr>
					<tr>
						<td style='font-size:13px;'>
						<a href='http://www.blogsense-wp.com/hosted/testblog/auto/includes/pdfs/Advanced_Search_Commands.pdf' target='_blank'>
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Use advanced Yahoo Search syntax to narrow down content with footprints. Click this link to find out about advanced Yahoo Search Commands, but it is still reccomended that you test your footprints on Yahoo.com yourself first."></a>
							<div id="id_yahoo_search" style="display:inline;"></div>
							<i>Footprint:</i></td><td align=right><input class='class_inputs' id=id_footprint name=footprint size=33>
						</td>
					</tr>
					<tr>						
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Scrape Paramater.">
							<i>Title Begin Code:</i>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_title_start' rows=1 cols=25 name=title_start></textarea>
						</td>
					</tr>
					<tr>
						<td align=left style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Scrape Paramater.">
							<i>Title End Code:</id>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_title_end' rows=1 cols=25 name=title_end ></textarea>
						</td>
					</tr>
					<tr>
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Scrape Paramater.">
							<i>Post Begin Code:</i>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_content_start' rows=3 cols=25 name=content_start></textarea>
						</td>
					</tr>
					<tr>
						<td align=left style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Scrape Paramater.">
							<i>Post End Code:
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_content_end' rows=3 cols=25 name=content_end></textarea>
						</td>
					</tr>
					<tr>
						<td colspan=2 align=left  style='font-size:13px;' valign=top >
						<h4>Backup Paramaters #1</h4><hr>
						</td>
					</tr>
					<tr>						
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional Scrape Paramater. If the first code sequence is not found there is a good chance we are working with an irregular template pattern. If this is something that can be expected due to the nature of the source, we can have backup paramaters to catch the exceptions.">
							<i><font style='font-size:10px;'>(TBC Backup #1)</font>:</i>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_title_start_backup_1' rows=1 cols=25 name=title_start_backup_1></textarea>
						</td>
					</tr>
					<tr>						
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional Scrape Paramater. If the first code sequence is not found there is a good chance we are working with an irregular template pattern. If this is something that can be expected due to the nature of the source, we can have backup paramaters to catch the exceptions.">
							<i><font style='font-size:10px;'>(TEC Backup #1)</font>:</i>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_title_end_backup_1' rows=1 cols=25 name=title_end_backup_1></textarea>
						</td>
					</tr>
					<tr>
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional Scrape Paramater. If the first code sequence is not found there is a good chance we are working with an irregular template pattern. If this is something that can be expected due to the nature of the source, we can have backup paramaters to catch the exceptions.">
							<i><font style='font-size:10px;'>(PBC Backup #1)</font>:</i>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_content_start_backup_1' rows=3 cols=25 name=content_start_backup_1></textarea>
						</td>
					</tr>
					<tr>
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional Scrape Paramater. If the first code sequence is not found there is a good chance we are working with an irregular template pattern. If this is something that can be expected due to the nature of the source, we can have backup paramaters to catch the exceptions.">
							<i><font style='font-size:10px;'>(PEC Backup #1)</font>:</i>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_content_end_backup_1' rows=3 cols=25 name=content_end_backup_1></textarea>
						</td>
					</tr>
					<tr>
						<td colspan=2 align=left  style='font-size:13px;' valign=top >
						<h4>Backup Paramaters #2</h4><hr>
						</td>
					</tr>
					<tr>						
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional Scrape Paramater. If the first code sequence is not found there is a good chance we are working with an irregular template pattern. If this is something that can be expected due to the nature of the source, we can have backup paramaters to catch the exceptions.">
							<i><font style='font-size:10px;'>(TBC Backup #2)</font>:</i>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_title_start_backup_2' rows=1 cols=25 name=title_start_backup_2></textarea>
						</td>
					</tr>
					<tr>						
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional Scrape Paramater. If the first code sequence is not found there is a good chance we are working with an irregular template pattern. If this is something that can be expected due to the nature of the source, we can have backup paramaters to catch the exceptions.">
							<i><font style='font-size:10px;'>(TEC Backup #2)</font>:</i>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_title_end_backup_2' rows=1 cols=25 name=title_end_backup_2></textarea>
						</td>
					</tr>
					<tr>
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional Scrape Paramater. If the first code sequence is not found there is a good chance we are working with an irregular template pattern. If this is something that can be expected due to the nature of the source, we can have backup paramaters to catch the exceptions.">
							<i><font style='font-size:10px;'>(PBC Backup #2)</font>:</i>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_content_start_backup_2' rows=3 cols=25 name=content_start_backup_2></textarea>
						</td>
					</tr>
					<tr>
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Optional Scrape Paramater. If the first code sequence is not found there is a good chance we are working with an irregular template pattern. If this is something that can be expected due to the nature of the source, we can have backup paramaters to catch the exceptions.">
							<i><font style='font-size:10px;'>(PEC Backup #2)</font>:</i>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_content_end_backup_2' rows=3 cols=25 name=content_end_backup_2></textarea>
						</td>
					</tr>
					<tr>
						<td colspan=2 align=left  style='font-size:13px;' valign=top >
						<h4>Comment Scraping</h4><hr>
						</td>
					</tr>
					
					<tr>
						<td align=left style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="We can scrape comments if our source has them.">
							<i>Comments Available:
						</td>
						<td align=left>
							<input class='class_inputs' type=radio id='id_comments_status_1' name=comments_status value=1>yes <input class='class_inputs' type=radio name=comments_status id='id_comments_status_2' value=0>no
						</td>
					</tr>
					<tr>
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Scrape Paramater. Commenter Name.">
							<i>Usernames Begin Code:</i>
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_comments_name_start' rows=1 cols=25 name=comments_name_start></textarea>
						</td>
					</tr>
					<tr>
						<td align=left style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Scrape Paramater. Commenter Name.">
							<i>Username End Code:
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_comments_name_end' rows=1 cols=25 name=comments_name_end></textarea>
						</td>
					</tr>
					<tr>
						<td align=left  style='font-size:13px;' valign=top >
							<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Scrape Paramater. The Comment.">
							<i>Comments Content Begin Code:</i>
						</td>
						<td align=left>
			
							<textarea class='class_inputs' id='id_comments_content_start' rows=1 cols=25 name=comments_content_start></textarea>
						</td>
					</tr>
					<tr>
						<td align=left style='font-size:13px;' valign=top >
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Scrape Paramater. The Comment.">
							<i>Comments Content End Code:
						</td>
						<td align=left>
							<textarea class='class_inputs' id='id_comments_content_end' rows=1 cols=25 name=comments_content_end></textarea>
						</td>
					</tr>
					<tr>
						<td colspan=2 align=middle style="font-size:13px;">
							<table id=id_table_regex>
								<tr>
									<td colspan=2 align=middle style="font-size:13px;">
										<a href="./../includes/pdfs/Using_Regular_Expressions.pdf" target=_blank><img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Information on Regular Expressions." border=0></a>
										Regex Search & Replace 
									</td>
								</tr>
								<tr>
									<td  align=middle style="font-size:11px;color:#aaaaaa">
										<i>Search String</i>
									</td>
									<td  align=middle style="font-size:11px;color:#aaaaaa">
										<i>Replace String</i>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td align=center colspan=2 style='font-size:13px;' valign=top >
							<button class='class_inputs' id='id_submit'  value='Save Item'><span>Save Item</span></button>
							<button class='class_inputs' id='id_delete'  value='Remove Source'><span>Remove Source</span></button>
							<button class='class_inputs' id='id_export'  value='Export SQL'><span>Export SQL</span></button>
						</td>
					</tr>
				</table>
			</div>
			</form>
			</div>
			<div id=id_form_2 style='display:none'>
			<form action='' method=POST>
			<input type=hidden name=nature value='sql'>
			<table id=id_source_data width=400 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee;"> 
					<tr>
						<td style='font-size:11px;'>
							<i>Note: <b><?php echo $table_prefix; ?></b> is you database prefix</i><br>
							<textarea name='sql' style='width:100%;height:200px;'></textarea>
						</td>
					</tr>
					<tr>
						<td align=center colspan=2 style='font-size:13px;' valign=top >
							<input type=submit id=id_submit_sql value='Import SQL'>
						</td>
					</tr>
			</table>
			</form>
			</div>
		</body>
	</html>
<?php
}
else
{
  $nature = $_POST['nature'];
  $source_id = $_POST['source_id'];
  $source_url = $_POST['source_url'];
  $footprint = $_POST['footprint'];
  $title_start = $_POST['title_start'];
  $title_start_backup_1 = $_POST['title_start_backup_1'];
  $title_start_backup_2 = $_POST['title_start_backup_2'];
  $title_end = $_POST['title_end'];
  $title_end_backup_1 = $_POST['title_end_backup_1'];
  $title_end_backup_2 = $_POST['title_end_backup_2'];
  $content_start = $_POST['content_start'];
  $content_start_backup_1 = $_POST['content_start_backup_1'];
  $content_start_backup_2 = $_POST['content_start_backup_2'];
  $content_end = $_POST['content_end'];
  $content_end_backup_1 = $_POST['content_end_backup_1'];
  $content_end_backup_2 = $_POST['content_end_backup_2'];
  $comments_status = $_POST['comments_status'];
  $comments_name_start = $_POST['comments_name_start'];
  $comments_name_end = $_POST['comments_name_end'];
  $comments_content_start = $_POST['comments_content_start'];
  $comments_content_end = $_POST['comments_content_end'];
  if ($_POST['regex_search']){$regex_search = implode("***r***", $_POST['regex_search']);}
  if ($_POST['regex_search']){$regex_replace = implode("***r***", $_POST['regex_replace']);}
 
  if ($nature=='new')
  {
    $query = "INSERT INTO ".$table_prefix."sourcedata (`id`,`source_url`,`footprint`,`title_start`,`title_start_backup_1`,`title_start_backup_2`,`title_end`,`title_end_backup_1`,`title_end_backup_2`,`content_start`,`content_start_backup_1`,`content_start_backup_2`,`content_end`,`content_end_backup_1`,`content_end_backup_2`,`comments_status`,`comments_name_start`,`comments_name_end`,`comments_content_start`,`comments_content_end`,`regex_search`,`regex_replace`)";
	$query .= "VALUES ('','$source_url','$footprint','$title_start','$title_start_backup_1','$title_start_backup_2','$title_end','$title_end_backup_1','$title_end_backup_2','$content_start','$content_start_backup_1','$content_start_backup_2','$content_end','$content_end_backup_1','$content_end_backup_2','$comments_status','$comments_name_start','$comments_name_end','$comments_content_start','$comments_content_end','$regex_search','$regex_replace')";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); exit;}
  }
  if ($nature=='update')
  {
	$query = "UPDATE ".$table_prefix."sourcedata  SET source_url = '$source_url', footprint = '$footprint', title_start = '$title_start', title_start_backup_1 = '$title_start_backup_1', title_start_backup_2 = '$title_start_backup_2', title_end = '$title_end' ,title_end_backup_1 = '$title_end_backup_1' ,title_end_backup_2 = '$title_end_backup_2' , content_start = '$content_start' , content_start_backup_1 = '$content_start_backup_1' , content_start_backup_2 = '$content_start_backup_2' , content_end = '$content_end' ,  content_end_backup_1 = '$content_end_backup_1' , content_end_backup_2 = '$content_end_backup_2' , comments_status = '$comments_status' ,comments_name_start = '$comments_name_start' , comments_name_end = '$comments_name_end' , comments_content_start = '$comments_content_start' ,comments_content_end = '$comments_content_end' , regex_search='$regex_search', regex_replace='$regex_replace' WHERE id='$source_id'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); exit;}
  }
  if ($nature=='sql')
  {
	$sql = stripslashes($_POST['sql']);
	$result = mysql_query($sql);
	if (!$result){echo $sql; echo mysql_error(); exit;}
  }
  if ($nature=='export')
  {
	$query = "INSERT INTO ".$table_prefix."sourcedata (`id`,`source_url`,`footprint`,`title_start`,`title_start_backup_1`,`title_start_backup_2`,`title_end`,`title_end_backup_1`,`title_end_backup_2`,`content_start`,`content_start_backup_1`,`content_start_backup_2`,`content_end`,`content_end_backup_1`,`content_end_backup_2`,`comments_status`,`comments_name_start`,`comments_name_end`,`comments_content_start`,`comments_content_end`,`regex_search`,`regex_replace`)";
	$query .= "VALUES ('','$source_url','$footprint','$title_start','$title_start_backup_1','$title_start_backup_2','$title_end','$title_end_backup_1','$title_end_backup_2','$content_start','$content_start_backup_1','$content_start_backup_2','$content_end','$content_end_backup_1','$content_end_backup_2','$comments_status','$comments_name_start','$comments_name_end','$comments_content_start','$comments_content_end','$regex_search','$regex_replace')";
	
	echo "<a href=''>[back]</a><br><br><textarea rows=30 cols=100>$query</textarea>";
	exit;
  }
  if ($nature=='delete')
  {
	//echo 1; exit;
	$query = "DELETE FROM ".$table_prefix."sourcedata   WHERE id='$source_id'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); exit;}
  }
  
  header('Location: f_manage_sources.php?saved=y');
  exit;
}

?>