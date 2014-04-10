<html>
<head>
<title>Generate Keywords for URL List - BlogSenseWP</title>
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
<form action='urllist2formattedurllist_make.php' method='POST' id='id_form'>
<h3>Generate Keywords for a URL List</h3>
<br>
<span style='background-color:#1E90FF;border-style:solid;border-width:1px;padding:2px;border-color:#000000;color:#fff;cursor:pointer;' id='id_button_form_submit'>Create List</span>
<br><br>
<table id='id_table_rss_feeds' style='width:600px;'>

	<tr>
		<td>
			Sorting:  <select name='sort'>
						<option value='none'>Leave Order</option>
						<option value='random'>Randomize</option>
						
					  </select>
		</td>
	</tr>
	<tr>
		<td>
			Format:  <select name='format'>
						<option value='1'>URL;Title</option>
						<option value='2'>URL{Title}</option>
						<option value='3'>Title [linebreak] URL ...(Scrapebox Trackback format)</option>
						<option value='4'>URL;Tag1,Tag2,Tag3  ...(Generated from content by Yahoo Tagging system)</option>
						<option value='5'>URL{Tag1|Tag2|Tag3} ...(Generated from content by Yahoo Tagging system)</option>
					  </select>
		</td>
	</tr>
	<tr>
		<td>
		URL List:<br>
			<textarea name='url_list' style='width:90%;height:400px;' wrap='off'></textarea>
		</td>
	</tr>
</table>
</form>
</body>
</html>