<?php
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
	
	echo "<br><br>******************Yahoo campaign*********************<br>$campaign_name : $campaign_feed <br>************************************************<br>";
	
	
	
	$string = quick_curl($campaign_feed,0);	
			
	$description_start = '<Content>';
	$description_end =  '</Content>';	
			
	$link_start = "<Link>";
	$link_end = "</Link>";
	
	$title_start = "<Subject>";
	$title_end = "</Subject>";
	
	$link_count = substr_count($string, $link_start);
	if ($limit_results!=0&&($limit_results<$link_count))
	{
		$link_count = $limit_results;
	}
	//echo $link_count;exit;
	
	for ($i=0;$i<$link_count;$i++)
	{
		
		$links[$i] = get_string_between($string, $link_start, $link_end);		  
		$string = str_replace("".$link_start."".$links[$i]."".$link_end."", "", $string);
		$links[$i] = clean_cdata($links[$i]);
	}
	
	if (count($links)==0)
	{
	 echo "Campaign produces no results. ";exit;
	}
	

	
    foreach ($links as $key=>$value)
	{
		$comments_names = "";
		$comments_content = "";
		
		$chunk = get_string_between($string, "<Question ", "</Question>");
		$string = str_replace_once("<Question ", "", $string);
		$question_id = get_string_between($chunk, 'id="', '"');
		
		$title = get_string_between($chunk, $title_start, $title_end);		
		$description = get_string_between($chunk, $description_start, $description_end);		
		$link = get_string_between($chunk, $link_start, $link_end);
		
		$answers_count = get_string_between($chunk, "<NumAnswers>", "</NumAnswers>");
		
		$scrape_comments_status=1;
		$comments_link = "http://answers.yahooapis.com/AnswersService/V1/getQuestion?appid=YahooDemo&question_id=$question_id";
		$comments_string = quick_curl($comments_link,0);
		$comments_string = get_string_between($comments_string, "<Answers>", "</Answers>");
		
		for ($n=0;$n<$answers_count;$n++)
		{
			$comments_names[] = get_string_between($comments_string, "<UserNick>", "</UserNick>");
			$comments_string = str_replace_once("<UserNick>" , "", $comments_string);
			
			$comments_content[] = get_string_between($comments_string, "<Content>", "</Content>");
			$comments_string = str_replace_once("<Content>" , "", $comments_string);			
		}
		
		//echo $description; exit;
		//strip tags, mend unbalanced divs, remove javascript
		$description = clean_html($description);
		
		if ($campaign_strip_links==1)
		{
				$description = strip_tags($description,'<ul><li><ol><pre><img><div><table><tr><td><i><b><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><font><li><ul><embed><object><small><label><br/>');
		}
		
		if ($campaign_strip_links==2)
		{				
			$description = links_to_tag_links($description, $blog_url);
		}	
		
		if ($campaign_strip_links==3)
		{						
			$description = links_to_search_links($description, $blog_url);
		}	
		
		//spin text					
		if ($campaign_spin_text==1)
		{
			$language="spin";
			$description = spin_text($description, $link, $title, $campaign_image_floating, $language);
			$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
			
			if ($comments_content)
			{
				if (count($comments_content)>0)
				{
					$comments_content = implode('***', $comments_content);
					$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
					$comments_content = explode('***',$comments_content);
				}
			}
		}

		//spin text	: title only				
		if ($campaign_spin_text==2)
		{
			$language="spin";
			$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
		}

		//spin text : postbody only					
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
		if ($campaign_spin_text==5)
		{
			$language="salt";
			$description = spin_text($description, $link, $title, $campaign_image_floating, $language);
			
			if ($comments_content)
			{
				if (count($comments_content)>0)
				{
					$comments_content = implode('***', $comments_content);
					$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
					$comments_content = explode('***',$comments_content);
				}
			}
		}
			
		//spin text					
		if ($campaign_spin_text==6)
		{
			$language="salt";
			$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
			$description = spin_text($description, $link, $title, $campaign_image_floating, $language);
			
			if ($comments_content)
			{
				if (count($comments_content)>0)
				{
					$comments_content = implode('***', $comments_content);
					$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $language);
					$comments_content = explode('***',$comments_content);
				}
			}
		}
		
		//remove links from description if remove_link is on
		if ($campaign_strip_images==1)
		{
			$description = strip_tags($description,'<ul><li><ol><pre><div><table><tr><tbody><td><i><b><a><p><span><u><font><h1><h2><h3><font><ul><center><blockquote><li><embed><object><small><label><br/>');
		}
		
		//translate				
		if ($campaign_language!="no translation")
		{
			$title = spin_text($title, $link, $title, $campaign_image_floating,$campaign_language);
			$description = spin_text($description, $link, $title, $campaign_image_floating,$campaign_language);
			
			if ($comments_content)
			{
				if (count($comments_content)>0)
				{
					$comments_content = implode('***', $comments_content);
					$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating, $campaign_language);
					$comments_content = explode('***',$comments_content);
				}
			}
		}
		
		//strip post of uneeded html
		$description = clean_html($description);
		$title = htmlspecialchars_decode($title);
		$title = strip_tags($title);
		$title = replace_trash_characters($title);
		$link = $value;	
		
		//populate title template
		$title_content = hook_content($campaign_title_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
		$title= $title_content;
		
		//populate post template
		$description_content = hook_content($campaign_post_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
		$description = $description_content;	
		
		//run regex search and replace
		if ($campaign_regex_search)
		{		
			//print_r($regex_search);exit;
			//$regex_search = explode("***r***",$regex_search[$key]);
			//$scrape_regex_replace = explode("***r***",$regex_replace[$key]);
			//echo $scrape_regex_search[0]; echo "<br><br>";	   
			$description = preg_replace($campaign_regex_search,$campaign_regex_replace, $description);			  
			$title = preg_replace($campaign_regex_search,$campaign_regex_replace, $title);			  
			// echo $description; exit;
		}
		
		//credit source 
		if ($campaign_credit_source==1)
		{
			if ($campaign_credit_source_nofollow==1)
			{
			   $nofollow = "rel=nofollow";
			}
			$description .= "<br><br><a href=\"$link\" target=_blank $nofollow>$campaign_credit_source_text</a>";
		}	
		
		
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
		
		if (!$description||!$title){$nocontent=1;}else{$nocontent=0;}
		//echo $description; exit;
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
				
				$title = special_htmlentities($title);
				
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
				echo "<i>".count($comments_names)." comments found.</i><br>";			
				echo "<br><br>$description<br><br>";
					
				if (count($comments_names)>0)
				{
					echo "<ul>";
					foreach ($comments_names as $k=>$v)
					{
						echo "<li><font style='font-size:10px'>\" $comments_content[$k] \"- $v </li>";
					}
					echo "</ul>";
					
				}
				echo "<br><br><hr>";
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
			
			//$title =  addslashes($title);
			//$description = addslashes($description);


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