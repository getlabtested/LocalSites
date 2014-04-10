<?php
include_once('./../../wp-config.php');
session_start();
include("./../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../login.php");


?>
<html>
	<head>
	<title>Remote Publishing Logs - BlogSenseWp</title>
	<?php
	
	if ($_POST['nature']=='generate')
	{
		$hour_parameter = $_POST['hour_parameter'];
		$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
		$wordpress_date_time =  date_i18n($timezone_format);
		if ($hour_parameter==0)
		{
			$all =1;
		}
	
		if ($all == 1)
		{
			$query = "SELECT * FROM {$table_prefix}blogsense_remote_published_urls";
			$result = mysql_query($query);
			if (!$result){echo $query; echo mysql_error(); exit;}
		}
		else
		{
			$date_param = date('Y-m-d G:i:s', strtotime("$wordpress_date_time - $hour_parameter hour")); 
			$query = "SELECT * FROM {$table_prefix}blogsense_remote_published_urls WHERE date>'$date_param'";
			$result = mysql_query($query);
			if (!$result){echo $query; echo mysql_error();}
		}
		
		$row_count = mysql_num_rows($result);
		
		echo "<script type='text/javascript' src='https://www.google.com/jsapi'></script>";
		echo "	<script type='text/javascript'> \n";
		echo "	  google.load('visualization', '1', {packages:['table']});\n";
		echo "	  google.setOnLoadCallback(drawTable);\n";
		echo "	  google.setOnLoadCallback(drawToolbar);\n";
		echo "	  function drawTable() {\n";
		echo "		var data = new google.visualization.DataTable();\n";
		echo "		data.addColumn('string', 'Permalink');\n";
		echo "		data.addColumn('string', 'Title');\n";
		echo "		data.addColumn('string', 'Date Published');\n";
		echo "		data.addRows({$row_count});\n";
		
		$i = 0;
		while ($arr = mysql_fetch_array($result))
		{	
			
			echo "data.setCell({$i}, 0 , '{$arr['permalink']}');\n";
			echo "data.setCell({$i}, 1 ,'{$arr['title']}');\n";
			echo "data.setCell({$i}, 2 ,'{$arr['date']}');\n";
			
			$i++;
		}
		
		echo "var table = new google.visualization.Table(document.getElementById('table_div'));\n";
		echo "table.draw(data, {showRowNumber: true});\n";
		echo "} \n";
	
		echo " function drawToolbar() {";
		echo "	var components = [";
		echo "		  {type: 'html', datasource: 'https://spreadsheets.google.com/tq?key=pCQbetd-CptHnwJEfo8tALA'},";
		echo "		  {type: 'csv', datasource: 'https://spreadsheets.google.com/tq?key=pCQbetd-CptHnwJEfo8tALA'}";
				 
		echo "	];";
		  
		echo "	var container = document.getElementById('toolbar_div');";
		echo "	google.visualization.drawToolbar(container, components);";
		echo "};";

		echo "</script>\n";
		
	}
	?>
	<script type="text/javascript" src="./../includes/jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="./../includes/jquery-ui-1.7.2.custom.css">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
	<script type="text/javascript"> 
	$(document).ready(function() 
	{
		
	});
	</script>
	</head>
	<body style="font-family:Khmer UI;">
	<form action="" id="" name="" method=POST>
	<input type=hidden name='nature' value='generate'>


	<table width="400" >
		<tr>
			<td width='50%' valign='top'>
				<center>							   
				<div style="font-size:12px;width:100%;text-align:left;margin-left:auto;margin-right:auto;">
				<h3>Remote Publishing: Generate URL List</h3>
				When publishing content to other BlogSense blogs using their remote publishing API, this BlogSense installation will collect the URL and titles of all successful remote publications. 
				Set the hour paramater to 0 to call all the URLs in the database. 

				<hr style="width:100%;color:#eeeeee;background-color:#eeeeee;">
				 
				<table  style="width:100%;margin-left:auto;margin-right:auto;border: solid 1px #eeeeee;"> 
				  <tr>
					 <td  align=left valign='middle' style="font-size:13px;">
						Hours Range: <br> </td>
					 <td valign='middle' align=right style="font-size:13px;">
						<input name='hour_parameter' value='24' size=1> Hours
					</td>
					<td align=center valign='middle' style='font-size:13px;padding:20px;'>
							<input type=submit value='Generate Log'>
						 </td>
					</tr>
				
			</table>
		</td>
	</table>
	</form>
	</div>
	
	<center>
	
	<br><br>
	<div id="toolbar_div"  style='width:90%;text-size:12px;'></div>
	<div id='table_div' style='width:100%;' ></div>
	
	
	</center>

	
	</body>
	</html>