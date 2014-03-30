<html>
<head>
<title>Combine RSS Feeds - BlogSenseWP</title>
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
<form action='index.php' method='GET' id='id_form'>
<h3>Combine RSS Feeds</h3>
<br>
<span style='background-color:#1E90FF;border-style:solid;border-width:1px;padding:2px;border-color:#000000;color:#fff;cursor:pointer;' id='id_button_add_rss_feed'>Add Feed</span>&nbsp&nbsp;&nbsp&nbsp;
<span style='background-color:#1E90FF;border-style:solid;border-width:1px;padding:2px;border-color:#000000;color:#fff;cursor:pointer;' id='id_button_form_submit'>Create New Feed</span>
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
			<input name='rss_feeds[]' value='' size='70'>
		</td>
	</tr>
</table>
</form>
</body>
</html>