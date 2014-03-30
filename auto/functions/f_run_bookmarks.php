<?php
	$query = "SELECT * FROM ".$table_prefix."blogsense WHERE `option_name` IN (";
	$query .= "'blogsense_bookmarking_bitly_apikey' ,";
	$query .= "'blogsense_bookmarking_bitly_username' ,";
	$query .= "'blogsense_bookmarking_ping_module' ,";
	$query .= "'blogsense_pixelpipe' ,";
	$query .= "'blogsense_pixelpipe_mode' ,";
	$query .= "'blogsense_pixelpipe_email' ,";
	$query .= "'blogsense_pixelpipe_routing' ,";
	$query .= "'blogsense_proxy_bookmarking' ,";
	$query .= "'blogsense_proxy_list' ,";
	$query .= "'blogsense_proxy_type' ,";
	$query .= "'blogsense_twitter' ,";
	$query .= "'blogsense_twitter_mode' ,";
	$query .= "'blogsense_twitter_oauth_apikey' ,";
	$query .= "'blogsense_twitter_oauth_consumer_key' ,";
	$query .= "'blogsense_twitter_oauth_consumer_secret' ,";
	$query .= "'blogsense_twitter_oauth_secret' ,";
	$query .= "'blogsense_twitter_oauth_token' ,";
	$query .= "'blogsense_twitter_oauth_token' ,";
	$query .= "'blogsense_twitter_user' ) ORDER BY option_name ASC";

	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); exit;}
	$count = mysql_num_rows($result);

	for ($i=0;$i<$count;$i++)
	{
	$arr = mysql_fetch_array($result);
	if ($i==0){$bookmarking_bitly_apikey =$arr[option_value];}
	if ($i==1){$bookmarking_bitly_username =$arr[option_value];}
	if ($i==2){$ping_module =$arr[option_value];}
	if ($i==3){$pixelpipe_module =$arr[option_value];}
	if ($i==4){$pixelpipe_email =$arr[option_value];}
	if ($i==5){$pixelpipe_mode =$arr[option_value];}
	if ($i==6){$pixelpipe_routing =$arr[option_value];}
	if ($i==7){$proxy_bookmarking = $arr[option_value];}
	if ($i==8){$proxy_list = $arr[option_value];}
	if ($i==9){$proxy_type = $arr[option_value];}
	if ($i==10){$twitter_module = $arr[option_value];}
	if ($i==11){$twitter_mode = $arr[option_value];}
	if ($i==12){$twitter_oauth_apikey = $arr[option_value];}
	if ($i==13){$twitter_oauth_consumer_key = $arr[option_value];}
	if ($i==14){$twitter_oauth_consumer_secret = $arr[option_value];}
	if ($i==15){$twitter_oauth_secret = $arr[option_value];}
	if ($i==16){$twitter_oauth_token = $arr[option_value];}
	if ($i==17){$twitter_user = $arr[option_value];}
	}

	$blogsense_url = blogsense_url();
	//echo $twitter_module;
	//echo "<br>";
	//echo $pixelpipe_module;
	//echo "<br>";
	
	$twitter_user = explode(";", $twitter_user);
	$twitter_oauth_secret = explode(";", $twitter_oauth_secret);
	$twitter_oauth_token = explode(";", $twitter_oauth_token);
	$twitter_hash = explode(";", $twitter_hash);

	$pixelpipe_email = explode(";", $pixelpipe_email);
	$pixelpipe_routing = explode(";", $pixelpipe_routing);

	//******************************************************************************
	//PING MODULE*******************************************************************
	//******************************************************************************

	if ($ping_module==1)
	{			
		$date_simple = date('Y-m-d', strtotime("$wordpress_date_time"));
		$date_time = $wordpress_date_time;
		$date_today = date('Y-m-d H:i:s', strtotime("$date_time + 1 minutes"));
		$query = "SELECT * FROM ".$table_prefix."posts_to_bookmark WHERE DATE(date)='$date_simple' AND date!='0000-00-00 00:00:00' AND nature='ping' AND status='0' ORDER BY date ASC LIMIT 1";
		$result = mysql_query($query);
		$count = mysql_num_rows($result);
		if (!$result){echo $query; echo mysql_error(); }

		$arr = mysql_fetch_array($result);
		$bookmark_id = $arr['id'];
		$date= $arr['date'];
		
		if ($count>0)
		{
			if ($date<=$wordpress_date_time)
			{
				echo "---------------------<br>Pinging: $count posts.<br>---------------------<br><br>";

				generic_ping( $post_id );
				$link = get_permalink($post_id);
				
				$query = "UPDATE ".$table_prefix."posts_to_bookmark SET status='1', permalink='$link' WHERE id='$bookmark_id'";
				$result = mysql_query($query);
				if (!$result){echo $query; echo mysql_error(); }
				
			}
		}
	}
		
	//******************************************************************************
	//TWITTER MODULE*******************************************************
	//******************************************************************************


	//if twitter status post is on then POST!
	if ($twitter_module==1)
	{
	
		$date_simple = date('Y-m-d', strtotime("$wordpress_date_time"));
		$date_time = $wordpress_date_time;
		$date_today = date('Y-m-d H:i:s', strtotime("$date_time + 1 minutes"));
		$query = "SELECT * FROM ".$table_prefix."posts_to_bookmark WHERE date <='$date_today'  AND date!='0000-00-00 00:00:00'  AND status=0 AND nature='twitter' ORDER BY date ASC LIMIT 1";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error(); }
		$count = mysql_num_rows($result);
		
		$arr = mysql_fetch_array($result);
		$bookmark_id = $arr['id'];
		$post_id = $arr['post_id'];
		$account = $arr['account'];
		$account = explode(';',$account);
		$twitter_username = $account[0];
		$twitter_hash = $account[1];
		$date= $arr['date'];
		
		if ($count>0)
		{
			if ($date<=$wordpress_date_time)
			{
				echo "---------------------<br>Twitter Posting on.<br>---------------------<br><br>";
				
					
			   $query = "SELECT * FROM ".$table_prefix."posts  WHERE ID='$post_id' ";
			   $result = mysql_query($query);
			   if (!$result){echo $query; exit;}
			   if (mysql_num_rows($result)==0)
			   {
					//delete the bookmark because its gone
					$query = "DELETE FROM ".$table_prefix."posts_to_bookmark WHERE post_id='$post_id' ";
					$result = mysql_query($query);
					if (!$result){echo $query; echo mysql_error(); exit;}
					
					echo "<br>";
					echo "Twitter Status : Fail : Post to Bookmark not found.<br>";	
			   }
			   else
			   {
				   while ($arr = mysql_fetch_array($result))
				   {
						$title = $arr['post_title'];
						$title = substr($title, 0, 90);
						$short_text = $arr['post_content'];
						$post_name = $arr['post_name'];
						$slash = substr($blog_url, 0, -1);
									   
					   
						//print_r($twitter_user); exit;
						$key = array_search($twitter_username, $twitter_user);
						$twitter_oauth_token = $twitter_oauth_token[$key];
						$twitter_oauth_secret = $twitter_oauth_secret[$key];
					   
						//build tweet
						$link = get_permalink($post_id);
						$short_url = bs_shorten_url($link, $table_prefix);						
						$tweet = stripslashes("$title - $short_url $twitter_hash");
						
						$url =$blogsense_url."/includes/i_twitter_post_status.php"; 
						$paramaters = array('status'=>$message, 'tweet'=>$tweet, 'oauth_token'=>$twitter_oauth_token,'oauth_secret'=>$twitter_oauth_secret);
							   
		
						$twitter_data = stealth_curl($url, $proxy_bookmarking, $paramaters, $nature='twitter', $twitter_userusername, $twitter_pass);	
						
						echo $twitter_data;
						
						if (strstr($twitter_data,'Tweeted to'))
						{
							echo "<br>";
							echo "Account: Tweeted : $twitter_username<br>";
							echo "Tweet : $tweet<br>";
						
						
							$tweet = addslashes($tweet);
							$query2 = "UPDATE ".$table_prefix."posts_to_bookmark SET status='1', content='$tweet', permalink='$link' WHERE id='$bookmark_id'";
							$result2 = mysql_query($query2);
							if (!$result2){echo $query2; echo mysql_error(); }
						}
				
					}
				}
			}
		}
	}
	
	//******************************************************************************
	//PIXELPIPE MODULE*******************************************************
	//******************************************************************************


	//if twitter status post is on then POST!
	if ($pixelpipe_module==1)
	{
		
		$date_simple = date('Y-m-d', strtotime("$wordpress_date_time"));
		$date_time = $wordpress_date_time;
		$date_today = date('Y-m-d H:i:s', strtotime("$date_time + 1 minutes"));
		$query = "SELECT * FROM ".$table_prefix."posts_to_bookmark WHERE date <='$date_today'  AND date!='0000-00-00 00:00:00'  AND status=0 AND nature='pixelpipe' ORDER BY date ASC LIMIT 1";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error(); }
		$count = mysql_num_rows($result);
		
		$arr = mysql_fetch_array($result);
		$bookmark_id = $arr['id'];
		$post_id = $arr['post_id'];
		$account = $arr['account'];
		$account = explode(';',$account);
		$pixelpipe_email = $account[0];
		$pixelpipe_routing = $account[1];
		$date= $arr['date'];
		
		if ($count>0)
		{
			if ($date<=$wordpress_date_time)
			{
				
				echo "<br><br>---------------------<br>PixelPipe Posting on.<br>---------------------<br><br>";
				
					
			   $query = "SELECT * FROM ".$table_prefix."posts  WHERE ID='$post_id' ";
			   $result = mysql_query($query);
			   if (!$result){echo $query; exit;}
			   if (mysql_num_rows($result)==0)
			   {
					//delete the bookmark because its gone
					$query = "DELETE FROM ".$table_prefix."posts_to_bookmark WHERE post_id='$post_id' ";
					$result = mysql_query($query);
					if (!$result){echo $query; echo mysql_error(); exit;}
					
					echo "<br>";
					echo "PixelPipe Status : Fail : Post to Bookmark not found.<br>";	
			   }
			   else
			   {
				   while ($arr = mysql_fetch_array($result))
				   {
						$title = $arr['post_title'];
						$title = substr($title, 0, 90);
						$short_text = $arr['post_content'];
						$post_name = $arr['post_name'];
						$slash = substr($blog_url, 0, -1);									   
					   
						//build tweet
						$link = get_permalink($post_id);
						$short_url = bs_shorten_url($link, $table_prefix);						
						$tweet = stripslashes("$short_url $pixelpipe_routing");
						
						$headers  = 'MIME-Version: 1.0' . "\r\n";
						$headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
						
						$email_result = mail($pixelpipe_email,$title,$tweet,$headers);
					
						if ($email_result)
						{
							echo "<br>";
							echo "PixelPipe Account Email : $pixelpipe_email<br>";
							echo "PixelPipe Routing Tags : $pixelpipe_routing<br>";
							echo "Status Message : $tweet<br>";
						}
						else
						{
							echo "<br> There was an error sending the status update<br>";
						}
						
						$tweet = addslashes($tweet);
						$query2 = "UPDATE ".$table_prefix."posts_to_bookmark SET status='1', content='$tweet', permalink='$link' WHERE id='$bookmark_id'";
						$result2 = mysql_query($query2);
						if (!$result2){echo $query2; echo mysql_error(); }
				
					}
				}
			}
		}
	}

?>