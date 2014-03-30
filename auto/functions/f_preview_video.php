<?php
echo "<br><br>";
echo "***************************************************************************************<br>";
echo "***************************VIDEO MODULE************************************************<br>";
echo "***************************************************************************************<br>";
	if (strstr($campaign_feed, '%5Bspyntax%5D'))
	{
		$get = get_string_between($campaign_feed, '%5Bspyntax%5D','%5B%2Fspyntax%5D');
		$decodeget = urldecode($get);
		$spun = spyntax($decodeget);
		$campaign_query = $spun;
		$spun = urlencode($spun);
		$campaign_feed = str_replace_once("%5Bspyntax%5D".$get."%5B%2Fspyntax%5D", $spun ,$campaign_feed);
		//echo $campaign_feed;exit;
	}
echo "<br><br>******************video campaign*********************<br>$campaign_name: $campaign_feed<br>************************************************<br>";
	
	
	
	$string = quick_curl($campaign_feed,0);
	$string = htmlspecialchars_decode($string);

	$entry_start = "<feed xmlns=";
	$entry_end ="</generator>";							
	$start = $entry_start;
	$end =  $entry_end;
	$remove = get_string_between($string, $start, $end);
	$string = str_replace ($remove, "" , $string);
	//echo $string; exit;
	
	if ($campaign_source=='query')
	{
		$title_start =  "<title type='text'>";
		$title_end =  "</title>";
		$description_start =  "<content type='html'>";
		$description_end =  "From:";
		$link_start = "<link rel='alternate' type='text/html' href='";
		$link_end = "'/>";
		$thumbnail_start = 'src="';
		$thumbnail_end = '"';
	}
	else
	{
		$title_start =  "<title type='text'>";
		$title_end =  "</title>";
		$description_start =  "<content type='text'>";
		$description_end =  "</content>";
		$link_start = "<link rel='alternate' type='text/html' href='";
		$link_end = "'/>";
		$thumbnail_start = "<media:thumbnail url='";
		$thumbnail_end = "'";
	}
	
	$link_count = substr_count($string, $link_start);
			
	if ($campaign_limit_results!=0&&($campaign_limit_results<$link_count))
	{
		$link_count = $campaign_limit_results;
	}
	//echo $link_count;exit;
	
	for ($i=0;$i<$link_count;$i++)
	{
		
		$links[$i] = get_string_between($string, $link_start, $link_end);
		//echo $links[$i]; exit;			  
		$string = str_replace("".$link_start."".$links[$i]."".$link_end."", "", $string);
		$links[$i] = clean_cdata($links[$i]);
	}
	
	if (count($links)==0)
	{
	 echo "Campaign produces no results. ";exit;
	}
    foreach ($links as $key=>$value)
	{
		$title = "";
		$description = "";
		$link = "";
		$youtube_vids = "";
		
		//pull the title from rss
		$title = get_string_between($string, $title_start, $title_end);
		$string = str_replace("$title_start$title$title_end", "", $string);
		//echo $title; exit;
		
		//echo $string;exit;
		
		//pull the description from rss						   
		$description = get_string_between($string, $description_start, $description_end);			   
		$string = str_replace_once("$description_start$description", "" , $string);		
		//echo $description; exit;
		
		if ($campaign_source=='query')
		{
			$thumbnail = get_string_between($description, $thumbnail_start, $thumbnail_end);
		}
		else
		{
			$start = "<media:thumbnail url='";		
			$end = "'";

			$thumbnail = get_string_between($string, $thumbnail_start, $thumbnail_end);
			$string = str_replace_once("$thumbnail_start", "" , $string);
			$string = str_replace_once("$thumbnail_start", "" , $string);
			$string = str_replace_once("$thumbnail_start", "" , $string);
			$string = str_replace_once("$thumbnail_start", "" , $string);

		}

		//echo $string;exit;
		$title = clean_cdata($title);
		$description = clean_cdata($description);
		$title = htmlspecialchars_decode($title);
		$title = strip_tags($title);
		$title = replace_trash_characters($title);
		$title = trim($title);
		$link = $value;
		
		//first make sure the entry isnt already in the database
		$thislink = addslashes($link);
		$query ="SELECT original_source from ".$table_prefix.$_SESSION['second_prefix']."posts WHERE original_source='$thislink' AND post_status!='trash'";
		$result= mysql_query($query);
		if (!$result) { echo $query; echo 1;exit; }
		$row_count_1 = mysql_num_rows($result);
		
		$query ="SELECT * from ".$table_prefix.$_SESSION['second_prefix']."blocked_urls WHERE url='$thislink'";
		$result= mysql_query($query);
		if (!$result) { echo $query; echo 1;exit; }
		$row_count_2 = mysql_num_rows($result);
		
		//procede if original				
		if ($row_count_1==0&&$row_count_2==0&&$title)
		{	
			
			//save thumbnail to own server	
			if ($store_images==1) 
			{
				$thumbnail = save_image($thumbnail,$title, 1);
			}
			//echo $thumbnail; exit;
			//make description pretty
		
			$lite_description = strip_tags($description);
			//echo $lite_description; exit;
			$lite_description = str_replace($title,'',$lite_description);
			
			
			//echo $description;exit;
			
			//get video id
			$flag = "http://www.youtube.com/watch?v=";
			$pos_start = strpos($link, $flag) + strlen($flag);
			$vid_id = substr($link, $pos_start, 11);
			
			//spin text					
			if ($campaign_spin_text==1)
			{
				$language="spin";
				$lite_description = spin_text($lite_description, $link, $title, 'youtube', $language);
				$title = spin_text($title, $link, $title, 'youtube', $language);
			}

			//spin text					
			if ($campaign_spin_text==2)
			{
				$language="spin";
				$title = spin_text($title, $link, $title, 'youtube', $language);
			}	
			
			//spin text					
			if ($campaign_spin_text==3)
			{
				$language="spin";
				$lite_description = spin_text($lite_description, $link, $title, 'youtube', $language);
			}
			
			//spin text					
			if ($campaign_spin_text==4)
			{
				$language="salt";
				$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
			}
			
			//spin text					
			if ($campaign_spin_text[$key]==5)
			{
				$language="salt";
				$description = spin_text($description, $link, $title, $campaign_image_floating, $language);
			}
			
			//spin text					
			if ($campaign_spin_text==6)
			{
				$language="salt";
				$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
				$description = spin_text($description, $link, $title, $campaign_image_floating, $language);
			}
			
			//translate				
			if ($campaign_language!="no translation")
			{
				$title = spin_text($title, $link, $title, 'youtube',$campaign_language);
				$lite_description = spin_text($lite_description, $link, $title, 'youtube',$campaign_language);
			}	
			
			$description = array('video', $thumbnail, $lite_description);
			
			//populate title template
			$title_content = hook_content($campaign_title_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,NULL,$templates_custom_variable_token,$templates_custom_variable_content);	
			$title= $title_content;

			//populate post template
			//echo $campaign_post_template; exit;
			$description_content = hook_content($campaign_post_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
			$description = $description_content;			
	
			//echo $description;exit;
			if (!$title) {$title = "Unable to Source Title";}			
			if (strlen($description)<10) 
			{
				if ($string_focus)
				{
					$s = htmlspecialchars($string_focus);
				}
				else
				{
					$s = htmlspecialchars($string);
				}
				$description = "Unable to Source Description. <br><br>Begin-Code status : $bc_status <br> End-Code status: $ec_status<br><br> <a href='#error_$key'>Click here to view html feedback</a>"; 
				$error[] = "<a name='error_$key'></a><hr><b>Title of Entry : $title</b><br><b>HTML Code Returned:</b><br><br><pre>".$s."</pre><br><hr>";
			}

			
			$include_post=0;
			$exclude_post=0;
			$include_keywords=0;
			$exclude_keywords=0;
			
			if ($campaign_include_keywords!="Separate with commas.")
			{
				$include_keywords=1;
			}
			else
			{
				$include_post=1;
			}
			
			if ($campaign_exclude_keywords!="Separate with commas.")
			{
				$exclude_keywords=1;
			}
			
			if ($include_keywords==1)
			{
				$include_keywords = explode(",",$campaign_include_keywords);
				$include_keywords = array_filter($include_keywords);
				foreach ($include_keywords AS $k=>$v)
				{
					if ($campaign_include_keywords_scope==1)
					{
						if (stristr($title, $v))
						{
							$include_post = 1;										
						}	
					}
					else if ($campaign_include_keywords_scope==2)
					{
						if (stristr($description, $v))
						{
							$include_post = 1;										
						}	
					}
					else
					{
						if (stristr($title, $v)||stristr($description, $v))
						{
							$include_post = 1;										
						}	
					}				
				}		
			}	
			
			if ($exclude_keywords==1)
			{
				$exclude_keywords = explode(",",$campaign_exclude_keywords);
				$exclude_keywords = array_filter($exclude_keywords);
				foreach ($exclude_keywords AS $k=>$v)
				{
					if ($campaign_exclude_keywords_scope==1)
					{
						if (stristr($title, $v))
						{
							$exclude_post = 1;										
						}	
					}
					else if ($campaign_exclude_keywords_scope==2)
					{
						if (stristr($description, $v))
						{
							$exclude_post = 1;										
						}	
					}
					else 
					{
						if (stristr($title, $v)||stristr($description, $v))
						{
							$exclude_post = 1;										
						}	
					}				
				}		
			}	
			
			if ($include_post==1&&$exclude_post===0)
			{
						    
				//cloak links 
				if ($campaign_cloak_links==1)
				{						   
				   $description = cloak_links($description, $table_prefix);
				   //echo "$description";exit;
				}
				
				//discover default author if neccecary
				if ($campaign_author=='rand')
				{
					$randkey = array_rand($authors_id);
					$campaign_author[$key]= $authors_id[$randkey];
				}
				
				$domain  = bs_get_domain($link);
				$author_info = get_userdata($campaign_author);
				$author_name = $author_info->display_name;
				$description = str_replace('%author_name%',$author_name, $description);
				$description = str_replace('%domain_name%',$domain, $description);
				$description = remove_empty_images($description);
				$description = apply_filters('the_content',$description);
				
				//announce to log
				$travel_link = str_replace('http://','***',$link);
				$travel_link = urlencode($link);
				//echo $link; echo "<br>";
				//echo urldecode($travel_link);exit;
				
				echo "<a name='$key'></a><br>";
				echo "<br><div align=right>";
				echo "<span style='border-type:dotted;borer-size:1px;border-color:#000000'><a href='functions/f_block_content.php?id=$id&link=$travel_link' target=_blank style='text-decoration:none;color:#000000;font-weight:600;'><img src='nav/remove.png' border=0 align=top>&nbsp; Block Article</a></span></div>";
				echo "<br><br><b>$title</b><br>";
				echo "<i>$link</i><br>";	
				echo "<br><br>$description<br><br>";
				echo "<br><br><hr>";
			
				//$title =  addslashes($title);
				//$description = addslashes($description);					
			}
			else
			{
				if ($include_post!=1)
				{
					echo "<b>$title</b><br>";
					echo "Stats: NOT INCLUDED BY KEYWORD.<br>";
					echo "Link: $link<br><hr><br>";	
				}
				else
				{
					echo "<b>$title</b><br>";
					echo "Stats: EXCLUDED BY KEYWORD.<br>";
					echo "Link: $link<br><hr><br>";	
				}
			}			
		}//if blocked or not original
		else
		{			
			if ($row_count_2>0)
			{
				echo "Stats: BLOCKED.<br>";
			}
			else
			{
				echo "Stats: ALREADY PUBLISHED.<br>";
			}
		}
		    
	}//foreach item

?>