<?php
	include_once('../../wp-config.php');
	session_start();

	//check for multisite
	//check for multisite
	if (function_exists('switch_to_blog')){
	 switch_to_blog(1);
	 switch_to_blog($_COOKIE['bs_blog_id']);
	}
	
	//check for queue dump
	if ($_GET['empty']==1)
	{
		$query =  "DELETE FROM ".$table_prefix."posts_to_bookmark  WHERE status='0'";
		$result = mysql_query($query);
		if (!$result) {echo $query; exit;}
		
		$count = mysql_affected_rows();
		echo "<br><br><br><center> <font style='color:green'><i>$count bookmarking items have been deleted</i></font></center>";
		exit;
	}
	
	//check for queue dump
	if ($_GET['rebuild']==1||$_POST['rebuild_yes']==1)
	{
		if ($_POST['rebuild_yes']=='1')
		{
			$buffer_min = $_POST['buffer_min'];
			$buffer_max = $_POST['buffer_max'];
			
			
			//check and rebuild twitter
			$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
			$wordpress_date_time =  date_i18n($timezone_format);
			$this_date_time = $wordpress_date_time;
			
			$query =  "SELECT * FROM ".$table_prefix."posts_to_bookmark  WHERE status='0' AND nature='twitter'";
			$result = mysql_query($query);
			if (!$result) {echo $query; exit;}
			
			$count = mysql_num_rows($result);
			
			
			for ($i=0;$i<$count;$i++)
			{
				$array = mysql_fetch_array($result);
				$post_id = $array['id'];
				
				$rand = rand($buffer_min,$buffer_max);
				
				$this_date_time = date('Y-m-d H:i:s', strtotime("$this_date_time +$rand minutes")); 
				$query2 = "UPDATE ".$table_prefix."posts_to_bookmark set date='$this_date_time' WHERE id='$post_id'";
				$result2 = mysql_query($query2);
				if (!$result2) {echo $query2; exit;}
			}
			echo "<font style='color:green'><i>Twitter: $count bookmarking items have been rebuilt. </i></font><br>";
			
			
			//check and rebuild pixelpipe
			$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
			$wordpress_date_time =  date_i18n($timezone_format);
			$this_date_time = $wordpress_date_time;
			
			$query =  "SELECT * FROM ".$table_prefix."posts_to_bookmark  WHERE status='0' AND nature='pixelpipe'";
			$result = mysql_query($query);
			if (!$result) {echo $query; exit;}
			
			$count = mysql_num_rows($result);
			
			for ($i=0;$i<$count;$i++)
			{
				$array = mysql_fetch_array($result);
				$post_id = $array['id'];
				
				$rand = rand($buffer_min,$buffer_max);
				
				$this_date_time = date('Y-m-d H:i:s', strtotime("$this_date_time +$rand minutes")); 
				$query2 = "UPDATE ".$table_prefix."posts_to_bookmark set date='$this_date_time' WHERE id='$post_id'";
				$result2 = mysql_query($query2);
				if (!$result2) {echo $query2; exit;}
			}
			echo "<font style='color:green'><i>Pixelpipe: $count bookmarking items have been rebuilt. </i></font><br>";
			
			
			//check and rebuild pings
			$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
			$wordpress_date_time =  date_i18n($timezone_format);
			$this_date_time = $wordpress_date_time;
			
			$query =  "SELECT * FROM ".$table_prefix."posts_to_bookmark  WHERE status='0' AND nature='ping'";
			$result2 = mysql_query($query2);
			if (!$result2) {echo $query2; exit;}
			
			$count = mysql_num_rows($result);
			
			for ($i=0;$i<$count;$i++)
			{
				$array = mysql_fetch_array($result);
				$post_id = $array['id'];
				
				$rand = rand($buffer_min,$buffer_max);
				
				$this_date_time = date('Y-m-d H:i:s', strtotime("$this_date_time +$rand minutes")); 
				$query2 = "UPDATE ".$table_prefix."posts_to_bookmark set date='$this_date_time' WHERE id='$post_id'";
				$result2 = mysql_query($query2);
				if (!$result2) {echo $query2; exit;}
			}
			echo "<font style='color:green'><i>Pinging: $count items have been rebuilt. </i></font><br>";
			
			exit;
			
		}
		else
		{
			//check and rebuild twitter
			$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
			$wordpress_date_time =  date_i18n($timezone_format);
			?>
				<br><br>
				<center>
				<form action='' method='POST'>
				<input type='hidden' name='rebuild_yes' value=1>
				<table style='width:400px;'>
					<tr>
						<td align='left'>
							Buffer Minumum (In minutes)
						</td>
						<td align='left'>
							<input name='buffer_min' value=5 size=1>
						</td>
					</tr>
					<tr>
						<td align='left'>
							Buffer Maximum (In minutes)
						</td>
						<td align='left'>
							<input name='buffer_min' value=10 size=1>
						</td>
					</tr>
					<tr>
						<td align='left' colspan=2>
							<br>
							<i> The paramaters above will provide a floor and ceiling for selecting a numbers at random. The random generated numbers will help space out bookmarking tasks so as to prevent flooding. 
							<br>
						</td>
					</tr>
					<tr>
						<td align='center' colspan=2>
							<br>
							<input type='submit' value='Rebuild Scheduled Items'>
						</td>
					</tr>
					
				</table>
				</form>
				</center>
				<br><br>
				
			<?php
			exit;
		}
	}
	
	$twitter = $_POST['twitter_module'];  
	$twitter_mode = $_POST['twitter_mode'];  
	$pixelpipe = $_POST['pixelpipe_module'];
	$pixelpipe_mode = $_POST['pixelpipe_mode'];
	
	
	//Bookmarking
	$bookmarking_minutes_min = $_POST['bookmarking_minutes_min'];
	$bookmarking_minutes_max = $_POST['bookmarking_minutes_max'];
	$bookmarking_percentage = $_POST['bookmarking_percentage'];
	$bookmarking_ping_module = $_POST['bookmarking_ping_module'];
	$bookmarking_bitly_apikey = $_POST['bookmarking_bitly_apikey'];
	$bookmarking_bitly_username = $_POST['bookmarking_bitly_username'];
	

	$twitter_user = $_POST['twitter_user']; 
	$twitter_oauth_token = $_POST['twitter_oauth_token']; 
	$twitter_oauth_secret = $_POST['twitter_oauth_secret']; 
	$twitter_hash = $_POST['twitter_hash']; 
	$pixelpipe_email = $_POST['pixelpipe_email']; 
	$pixelpipe_routing = $_POST['pixelpipe_routing']; 
	
	if ($twitter_user)
	{
		$twitter_user = array_filter($twitter_user);
		$twitter_oauth_secret = array_filter($twitter_oauth_secret);
		$twitter_oauth_token = array_filter($twitter_oauth_token);
		
		$twitter_user = implode(";", $twitter_user);
		$twitter_oauth_secret = implode(";", $twitter_oauth_secret);
		$twitter_oauth_token = implode(";", $twitter_oauth_token);
		
		
	}
	
	
	if ($pixelpipe_email)$pixelpipe_email = implode(";", $pixelpipe_email);
	if ($pixelpipe_routing)$pixelpipe_routing = implode(";", $pixelpipe_routing);
	
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$pixelpipe' WHERE option_name='blogsense_pixelpipe'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$pixelpipe_mode' WHERE option_name='blogsense_pixelpipe_mode'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$pixelpipe_routing' WHERE option_name='blogsense_pixelpipe_routing'";
	$result = mysql_query($query);
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$pixelpipe_email' WHERE option_name='blogsense_pixelpipe_email'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}

	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$bookmarking_minutes_min' WHERE option_name='blogsense_bookmarking_minutes_min'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$bookmarking_minutes_max' WHERE option_name='blogsense_bookmarking_minutes_max'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$bookmarking_percentage' WHERE option_name='blogsense_bookmarking_percentage'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$bookmarking_ping_module' WHERE option_name='blogsense_bookmarking_ping_module'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$bookmarking_bitly_apikey' WHERE option_name='blogsense_bookmarking_bitly_apikey'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$bookmarking_bitly_username' WHERE option_name='blogsense_bookmarking_bitly_username'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$twitter' WHERE option_name='blogsense_twitter'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$twitter_mode' WHERE option_name='blogsense_twitter_mode'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$twitter_user' WHERE option_name='blogsense_twitter_user'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$twitter_oauth_secret' WHERE option_name='blogsense_twitter_oauth_secret'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$twitter_oauth_token' WHERE option_name='blogsense_twitter_oauth_token'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$twitter_hash' WHERE option_name='blogsense_twitter_hash'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	//done send back
	header("Location: ../index.php?p=4&saved=y");

?>
