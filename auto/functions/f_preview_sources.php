<?php
	if (strstr($campaign_feed, '%5Bspyntax%5D'))
	{
		$get = get_string_between($campaign_feed, '%5Bspyntax%5D','%5B%2Fspyntax%5D');
		$decodeget = urldecode($get);
		$spun = spyntax($decodeget);
		$campaign_query = $spun;
		$spun = urlencode($spun);
		$campaign_feed = str_replace_once("%5Bspyntax%5D".$get."%5B%2Fspyntax%5D", $spun ,$campaign_feed);
		
	}
	
	if (strstr($campaign_feed, '[spyntax]'))
	{
		$get = get_string_between($campaign_feed, '[spyntax]','[/spyntax]');
		$decodeget = urldecode($get);
		$spun = spyntax($decodeget);
		$campaign_query = $spun;
		$spun = urlencode($spun);
		$campaign_feed = str_replace_once("[spyntax]".$get."[/spyntax]", $spun ,$campaign_feed);
		//echo $campaign_feed;exit;
	}
		
	echo "<br><br>******************sources campaign*********************<br>$campaign_name<br>************************************************<br>";
	
	
	
	$string_links = quick_curl($campaign_feed,1);
	
	$link_count = substr_count($string_links, "<link>");			
	
	if ($campaign_limit_results!=0&&($campaign_limit_results<$link_count))
	{
		$link_count = $campaign_limit_results;
	}
	
	for ($i=0;$i<$link_count;$i++)
	{
	   $links[$i] = get_string_between($string_links, "<link>", "</link>");
	   $string_links = str_replace("<link>$links[$i]</link>", "", $string_links);
	}
	
	if (count($links)==0)
	{
	 echo "Campaign produces no results. <br>Campaign Feed: $campaign_feed";exit;
	}
	
	//echo $link_count;exit;

	
    foreach ($links as $key=>$value)
	{
		$title = "";
		$description = "";
		$link = "";
		$nocontent = 0;
		
		if ($campaign_scrape_content==1)
		{	
			$this_value = str_replace('http://', '', $value);
			$this_value = urlencode($this_value);
			$this_value = $blogsense_url."includes/fivefilters/makefulltextfeed.php?url={$this_value}&max=1&links=preserve&submit=Create+Feed";
			$string = quick_curl($this_value,1);
			unset($this_value);
			$string = explode('<item>',$string);
			$string = $string[1];
			//echo $string; exit;
			
			//echo $string; exit;
			$title = get_string_between($string, '<title>','</title>');
			$description = get_string_between($string, '<description>','</description>');
			$description = htmlspecialchars_decode($description);
			
			if (strstr($description,'Sorry, Readability was unable to parse this page for content.'))
			{
				$nocontent=1;
			}
		}
		else
		{
			$string = quick_curl($value,1);
			
			if (!$string)
			{
				echo "Something went wrong. Source returned an empty result. Please contact administrator. Exiting Script.";
				exit;
			}
			
			//special character work
			if (strstr($string,'gb2312'))
			{
			   $string = mb_convert_encoding($string, 'UTF-8', 'GB2312');
			}
			
			$title =  get_string_between($string, $campaign_scrape_title_start, $campaign_scrape_title_end);
			$description = get_string_between($string, $campaign_scrape_content_start, $campaign_scrape_content_end);
			$title = special_htmlentities($title);
			
			if (stristr($string,trim($campaign_scrape_content_start)))
			{
				$bc_status = 'found';
			}
			else
			{
				//echo 1;
				//echo $campaign_scrape_content_start;
				//echo $string
				$bc_status = 'not found';
			}
			if (strstr($string, $campaign_scrape_content_end))
			{
				$ec_status = 'found';
			}
			else
			{
				//echo 2;
				$ec_status = 'not found';
			}
		}
		//echo 1; exit;
		//echo $description; exit;
		
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
			//$error[] = "<a name='error_$key'></a><hr><b>Title of Entry : $title</b><br><b>HTML Code Returned:</b><br><br><pre>".$s."</pre><br><hr>";
		}
		
		if ($campaign_strip_links==1)
		{
			$description = strip_tags($description,'<ul><li><ol><pre><img><div><table><tr><td><i><b><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><font><li><ul><br><embed><object><small><label><br/>');
		}
		
		if ($campaign_strip_links==2)
		{				
			$description = links_to_tag_links($description, $blog_url);
		}	
		
		if ($campaign_strip_links==3)
		{						
			$description = links_to_search_links($description, $blog_url);
		}	
			
		//strip post of uneeded html
		$title = htmlspecialchars_decode($title);
		$title = strip_tags($title);
		$title = replace_trash_characters($title);
		$link = $value;
		//remove links from description if remove_link is on
		if ($campaign_strip_images==1)
		{
			$description = strip_tags($description,'<ul><li><ol><pre><div><table><tr><tbody><td><i><b><a><p><span><u><font><h1><h2><h3><font><ul><center><blockquote><li><br><embed><object><small><label><br/>');
		}
		
	
		//strip tags, mend unbalanced divs, remove javascript
		$description = clean_html($description);
		//echo $description;	exit;	
		//credit source 
		if ($campaign_credit_source==1)
		{
			if ($campaign_credit_source_nofollow==1)
			{
			   $nofollow = "rel=nofollow";
			}
			$description .= "<br><br><a href=\"$link\" target=_blank $nofollow>$campaign_credit_source_text</a>";
		}
		
		//spin text					
		if ($campaign_spin_text==1)
		{
			$language="spin";
			$description = spin_text($description, $link, $title, $campaign_image_floating, $language);
			$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
		}

		//spin text				
		if ($campaign_spin_text==2)
		{
			$language="spin";
			$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
		}	
		
		//spin text					
		if ($campaign_spin_text==3)
		{
			$language="spin";
			$description = spin_text($description, $link, $title, $campaign_image_floating, $language);
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
			$title = spin_text($title, $link, $title, $campaign_image_floating,$campaign_language);
			$description = spin_text($description, $link, $title, $campaign_image_floating,$campaign_language);
		}		
		
		//populate title template
		$title_content = hook_content($campaign_title_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
		$title= $title_content;
		
		//populate post template
		$description_content = hook_content($campaign_post_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
		$description = $description_content;	
		
	
		//run regex search and replace
		if ($campaign_regex_search)
		{		
			//echo $description; exit;	
			$description = preg_replace($campaign_regex_search,$campaign_regex_replace, $description);	
			//echo $description; exit;	
			$title = preg_replace($campaign_regex_search,$campaign_regex_replace, $title);			  
			// echo $description; exit;
		}
		
		//first make sure the entry isnt already in the database
		$thislink = addslashes($link);
		$query ="SELECT original_source from ".$table_prefix.$_SESSION['second_prefix']."posts WHERE original_source='$thislink' AND post_status!='trash'";
		$result= mysql_query($query);
		if (!$result) { echo $query; echo mysql_error(); exit; }
		$row_count_1 = mysql_num_rows($result);
		
		$query ="SELECT * from ".$table_prefix.$_SESSION['second_prefix']."blocked_urls WHERE url='$thislink'";
		$result= mysql_query($query);
		if (!$result) { echo $query; echo mysql_error();exit; }
		$row_count_2 = mysql_num_rows($result);
		
		if (!$description||!$title&&$nocontent==0){$nocontent=1;}
		
		//procede if original				
		if ($row_count_1==0&&$row_count_2==0&&$nocontent!=1)
		{	
			
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
			
			//***********PASSING INCLUDE GATEWAY**********//
			if ($include_post==1&&$exclude_post===0)
			{ 					
				//cloak links 
				if ($campaign_cloak_links==1)
				{						   
				   $description = cloak_links($description, $table_prefix);
				   //echo "$description";exit;
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
		}//if original
		else
		{
			if ($row_count_2>0)
			{
				echo "Stats: BLOCKED.<br>";
			}
			else if ($nocontent==1)
			{
				echo "Stats: NO CONTENT SCRAPED / IGNORE.<br> <hr>";
			}
		    else
			{
				echo "Stats: ALREADY PUBLISHED.<br><hr>";
			}
		}	
	}//foreach item

?>