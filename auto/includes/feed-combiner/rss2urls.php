<html>
<head>
<title>Combine RSS Feeds to Create URL List- BlogSenseWP</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript">
	
	$(document).ready(function() 
	{		
		$('#id_button_add_rss_feed').click(function() {
			var html = '<tr><td><img  onClick=\'$(this).parent().parent().remove();\' src=\'../../nav/remove.png\' style=\'cursor:pointer;\'>&nbsp;&nbsp;<input name=\'rss_feeds[]\' value=\'\' size=\'70\'></td></tr>';
			$('#id_table_rss_feeds tr:last').after(html);
		});
		
		$('#id_button_form_submit').click(function() {
			$("#id_form").submit();
		});
	});
	


</script>

</head>
<body>
<form action='rss2urls_make.php' method='GET' id='id_form'>
<h3>Generate URL List From Feeds</h3>
<br>
<span style='background-color:#1E90FF;border-style:solid;border-width:1px;padding:2px;border-color:#000000;color:#fff;cursor:pointer;' id='id_button_add_rss_feed'>Add Feed</span>&nbsp&nbsp;&nbsp&nbsp;
<span style='background-color:#1E90FF;border-style:solid;border-width:1px;padding:2px;border-color:#000000;color:#fff;cursor:pointer;' id='id_button_form_submit'>Create List</span>
<br><br>
<table id='id_table_rss_feeds' style='width:600px;'>
	<tr>
		<td>
			Max Items:  <input name='max_items' value=70>
		</td>
	</tr>
	<tr>
		<td>
			Sorting:  <select name='sort'>
						<option value='random'>Randomize</option>
						<option value='bydate_desc'>By Date (Newest First)</option>
						<option value='bydate_asc'>By Date (Oldest First)</option>
					  </select>
		</td>
	</tr>
	<tr>
		<td>
			Format:  <select name='format'>
						<option value='1'>URL</option>
						<option value='2'>URL;Title</option>
						<option value='3'>URL{Title}</option>
						<option value='4'>URL;Tag1,Tag2,Tag3 (Generated from content by Yahoo Tagging system)</option>
						<option value='5'>URL{Tag1|Tag2|Tag3} (Generated from content by Yahoo Tagging system)</option>
					  </select>
		</td>
	</tr>
	<tr>
		<td>
			<input name='rss_feeds[]' value='' size='70'>
		</td>
	</tr>
</table>
</form>
</body>
</html>