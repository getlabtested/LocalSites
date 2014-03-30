<?php

echo "<br><br>******************rss campaign*********************<br>$campaign_name : $campaign_feed<br>************************************************<br>";
	if ($campaign_scrape_content==3)
	{
		$campaign_feed = str_replace('http://', '', $campaign_feed);
		$campaign_feed = urlencode($campaign_feed);
		$campaign_feed = $blogsense_url."includes/fivefilters/makefulltextfeed.php?url={$campaign_feed}&max=25&links=preserve&submit=Create+Feed";
		//echo $campaign_feed;exit;
	}

	//echo $campaign_feed;exit;
	//echo $campaign_scrape_content;exit;
	$string = quick_curl($campaign_feed,1);
	$string = htmlspecialchars_decode($string);
	
	//echo $string;exit;
	//special character work
	if (strstr($string,'gb2312'))
	{
		//echo 1;
		$spc = "gb2312";		   
		$string = mb_convert_encoding($string, 'UTF-8', 'GB2312');
	}
	else
	{
		$spc = "none";
	}
	
	//prepare haircut

	//echo 2;
	
	$parameters = discover_rss_parameters($string);
	$string = $parameters['string'];
	$title_start = $parameters['title_start'];
	$title_end = $parameters['title_end'];
	$description_start = $parameters['description_start'];
	$description_end = $parameters['description_end'];
	$link_start = $parameters['link_start'];
	$link_end = $parameters['link_end'];
	$publish_date_start = $parameters['publish_date_start'];
	$publish_date_end = $parameters['publish_date_end'];
	$author_start = $parameters['author_start'];
	$author_end = $parameters['author_end'];
	$google_reader = $parameters['google_reader'];
	
	//echo $description_start;
	//echo "<hr>";
	
	$link_count = @substr_count($string, $link_start);
	if ($campaign_limit_results!=0&&($campaign_limit_results<$link_count))
	{
		$link_count = $campaign_limit_results;
	}

	for ($i=0;$i<$link_count;$i++)
	{
		
		$links[$i] = get_string_between($string, $link_start, $link_end);
		//echo $links[$i]; exit;			  
		$string = str_replace("".$link_start."".$links[$i]."".$link_end."", "", $string);
		$links[$i] = clean_cdata($links[$i]);
	}
	
	//echo count($links);exit;
	
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
		$comments_names = "";
		$comments_content = "";
		
		//echo $string;exit;
		
		//pull the title from rss
		$title = get_string_between($string, $title_start, $title_end);

		$string = str_replace_once("$title_start$title$title_end", "", $string);
		
		//pull the description from rss						   
		$description = get_string_between($string, $description_start, $description_end);
		
		if ($description)
		{
			$string = str_replace("{$description_start}{$description}{$description_end}", "" , $string); 
		}
		else
		{
			$string = str_replace_once("{$description_start}", "" , $string);
		}
		//echo $description;exit;

		//pull publish date from rss					   
		$publish_date = get_string_between($string, $publish_date_start, $publish_date_end);	
		
		$string = str_replace_once("{$publish_date_start}{$publish_date}{$publish_date_end}", " ***pd***" , $string); 
		$publish_date =  date_normalize($publish_date);

		$original_author = get_string_between($string, $author_start, $author_end);			   
		$string = str_replace_once("{$author_start}{$original_author}{$author_end}", " ***a***" , $string); 
		
		
		//echo $string;exit;
		$title = clean_cdata($title);
		$description = clean_cdata($description);
		$tivle = buffer_text($title);
		$title = htmlspecialchars_decode($title);						
		$title = strip_tags($title);
		$title = replace_trash_characters($title);
		$title = trim($title);
		$link = $value;
		$link = str_replace('http://www.google.com/url?sa=X&q=','',$link);
	
		
		if (strstr($link,'.html')&&$google_reader==1)
		{
			$link = explode('.html',$link);
			$link = $link[0].'.html';
		}
		//prepare post_name for url perma links
		//$permalink_name = string_url_prepare($title);						
		//$c_title = addslashes($title);
		//first make sure the entry isnt already in the database
		$thislink = addslashes($link);
		$query ="SELECT original_source from ".$table_prefix.$_SESSION['second_prefix']."posts WHERE original_source='$thislink' ";
		$result= mysql_query($query);
		if (!$result) { echo $query; echo 1;exit; }
		$row_count_1 = mysql_num_rows($result);
		
		$query ="SELECT * from ".$table_prefix.$_SESSION['second_prefix']."blocked_urls WHERE url='$thislink'";
		$result= mysql_query($query);
		if (!$result) { echo $query; echo 1;exit; }
		$row_count_2 = mysql_num_rows($result);
		//echo $key;
		//procede if original				
		if ($row_count_1==0&&$row_count_2==0&&$title&&!strstr($thislink, $blog_url))
		{
			if ($campaign_scrape_comments==1)
			{
				$string_focus = quick_curl($value,1);	
				$string_focus = str_replace($description, "", $string_focus);
				$comments_array = scrape_comments($string_focus,$campaign_scrape_names_begin_code,$campaign_scrape_names_end_code,$campaign_scrape_comments_begin_code,$campaign_scrape_comments_end_code);
				$comments_names = $comments_array[0];
				$comments_content = $comments_array[1];
				//print_r($comments_array[0]); exit;
			}
			
			if ($campaign_scrape_content==1)
			{
				//echo 1; exit;
				$string_focus = quick_curl($value,1);	
				//echo $string_focus; exit;
				
				//special character work
				if (strstr($string_focus,'gb2312'))
				{
				   //echo 1;
				   $string = mb_convert_encoding($string, 'UTF-8', 'GB2312');
				}
				//echo $scampaigntring_focus;exit;
				//echo $rss_begin_code; exit;
				//echo $rss_end_code; exit;
				$description = get_string_between($string_focus, $campaign_scrape_content_begin_code, $campaign_scrape_content_end_code);
				//echo $description; exit;
				$bc_status = "";
				$ec_status = "";
				if (strstr($string_focus, $campaign_scrape_content_begin_code))
				{
					$bc_status = 'found';
				}
				else
				{
					$bc_status = 'not found';
				}
				if (strstr($string_focus, $campaign_scrape_content_end_code))
				{
					$ec_status = 'found';
				}
				else
				{
					$ec_status = 'not found';
				}
			}//end rss scraping==1
			
			//check for youtube videos
			$o=0;
			while (strstr($description, '<param name="movie" value="http://www.youtube.com'))
			{
				$obj = get_string_between($description, '<object','</object>');
				$obj = "<object".$obj."</object>";
				
				if (strstr($obj, 'youtube'))
				{
					$youtube_vids[] = $obj;
					$description = str_replace($obj,"***obj:$o***",$description);
					$o++;
				}
			}
			

			
			
			//strip tags, mend unbalanced divs, htmlspecial char decode
			if ($google_reader==0)
			{
				$description = clean_html($description);
			}

			//echo $campaign_strip_links;exit;
			if ($campaign_strip_links==1)
			{
				//echo 1; exit;
				$description = strip_tags($description,'<ul><li><ol><pre><img><div><table><tr><td><i><b><p><span><u><font><tbody><h1><h2><h3><h4><blockquote><font><li><ul><embed><object><small><label><br/><br>');
			}

			
			if ($campaign_strip_links==2)
			{			
				$description = links_to_tag_links($description, $blog_url);
			}	

			if ($campaign_strip_links==3)
			{	
				$description = links_to_search_links($description, $blog_url);
			}		

			if (!$title) {$title = "Unable to Source Title";}			
			
			$raw_description = strip_tags($description);
			if (strlen($raw_description)<15) 
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
			
			//remove links from description if remove_link is on
			if ($campaign_strip_images==1)
			{
				$description = strip_tags($description,'<ul><li><ol><pre><div><table><tr><td><i><b><a><p><span><u><font><tbody><h1><h2><h3><h4><ul><blockquote><li><br><embed><object><small><label><br/>');
			}

			//echo $title; exit;
			//spin text					
			if ($campaign_spin_text==1)
			{
				$language="spin";
				$description = spin_text($description, $link, $title, $campaign_image_floating, $language);
				$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
			}	
			
			if ($campaign_spin_text==2)
			{
				$language="spin";
				$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
			}	
			
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

			//credit source 
			if ($campaign_credit_source==1)
			{
				if ($campaign_credit_source_nofollow==1)
				{
				   $nofollow = "rel=nofollow";
				}
				$description .= "<br><br><a href=\"$link\" target=_blank $nofollow>$campaign_credit_source_text</a><br>";
			}

			//translate				
			if ($campaign_language!="no translation")
			{

				$title = spin_text($title, $link, $title, $campaign_image_floating,$campaign_language);
				$description = spin_text($description, $link, $title, $campaign_image_floating,$campaign_language);
			}	

			
			$title_content = hook_content($campaign_title_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
			$title = $title_content;
						
			//populate post template
			$description_content = hook_content($campaign_post_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
			$description = $description_content;
			//echo 1; exit;
			
			//run regex search and replace
			if ($campaign_regex_search)
			{	
	 
				$description = preg_replace($campaign_regex_search,$campaign_regex_replace, $description);			  
				$title = preg_replace($campaign_regex_search,$campaign_regex_replace, $title);			  
				//echo $description; exit;
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
					//echo $campaign_include_keywords_scope;exit;
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
			
			if (!$description||strstr($description,'Sorry, Readability was unable to parse this page for content.'))
			{
				$stop=1;
			}
			else
			{
				$stop=0;
			}
			
			//***********PASSING INCLUDE GATEWAY**********//
			if ($include_post==1&&$exclude_post===0&&$stop!=1)
			{ 
				$domain  = bs_get_domain($link);
				//discover default author if neccecary
				if ($campaign_author=='rand')
				{
					$randkey = array_rand($authors_id);
					$campaign_author= $authors_id[$randkey];
				}
				
				if ($campaign_author=='keep_author')
				{
					$campaign_author = username_exists($original_author);
					if (!$campaign_author )
					{
						//create username
						$secret_phrase ="blogsensecreateusername0123456789";
						$this_key = str_shuffle($secret_phrase);
						$password = substr($this_key, 0, 6);
						$campaign_author = wp_create_user( $original_author, $password, "noreply@noreply-$password.com" );
					}
					unset($original_author);
				}	
				
				if ($campaign_author=='keep_author_domain')
				{
					$original_author = $original_author." - $domain";
					$campaign_author = username_exists($original_author);
					if (!$campaign_author )
					{
						//create username
						$secret_phrase ="blogsensecreateusername0123456789";
						$this_key = str_shuffle($secret_phrase);
						$password = substr($this_key, 0, 6);
						$campaign_author = wp_create_user( $original_author, $password, "noreply@noreply-$password.com" );
					}							
				}	
				
				if ($campaign_author=='domain')
				{
					$original_author = "$domain";
					$campaign_author = username_exists($original_author);
					if (!$campaign_author )
					{
						//create username
						$secret_phrase ="blogsensecreateusername0123456789";
						$this_key = str_shuffle($secret_phrase);
						$password = substr($this_key, 0, 6);
						$campaign_author = wp_create_user( $original_author, $password, "noreply@noreply-$password.com" );
					}							
				}
				
				$author_info = get_userdata($campaign_author);
				$author_name = $author_info->display_name;
				$description = str_replace('%author_name%',$author_name, $description);
				$description = str_replace('%domain_name%',$domain, $description);
				
				//cloak links 
				if ($campaign_cloak_links==1)
				{						   
				   $description = cloak_links($description, $table_prefix);
				   //echo "$description";exit;
				}
				
				//reinsert youtube videos if there
				if ($youtube_vids)
				{
					foreach($youtube_vids as $a=>$b)
					{
						$description = str_replace("***obj:$a***","$youtube_vids[$a]", $description);
					}
					if ($campaign_language!="no translation")
					{
						foreach($youtube_vids as $a=>$b)
						{
						$description = str_replace("*** Obj: $a ***","$youtube_vids[$a]", $description);
						}
					}
				}

				
				//check if vimeo feed and build video
				if (strstr($link, 'vimeo.com'))
				{
					$clip_id = str_replace('http://vimeo.com/', '', $link);
					$object = "<object width='400' height='225'><param name='allowfullscreen' value='true' /><param name='allowscriptaccess' value='always' /><param name='movie' value='http://vimeo.com/moogaloop.swf?clip_id={$clip_id}&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=&amp;fullscreen=1&amp;autoplay=0&amp;loop=0' /><embed src='http://vimeo.com/moogaloop.swf?clip_id={$clip_id}&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=&amp;fullscreen=1&amp;autoplay=0&amp;loop=' type='application/x-shockwave-flash' allowfullscreen='true' allowscriptaccess='always' width='600' height='400'></embed></object>";
					$description = "<center>$object</center><br><br>$description";
				}
				
				//announce to log
				$travel_link = str_replace('http://','***',$link);
				$travel_link = urlencode($link);
				//echo $link; echo "<br>";
				//echo urldecode($travel_link);exit;
				
				$title = special_htmlentities($title);
				$description = stripslashes($description);
				
				$description = remove_empty_images($description);
				
				$description = apply_filters('the_content',$description);
				
				echo "<a name='$key'></a><br>";
				echo "<br><div align=right>";
				echo "<span style='border-type:dotted;borer-size:1px;border-color:#000000'><a href='functions/f_block_content.php?id=$id&link=$travel_link' target=_blank style='text-decoration:none;color:#000000;font-weight:600;'><img src='nav/remove.png' border=0 align=top>&nbsp; Block Article</a></span></div>";
				echo "<br><br><b>$title</b><br>";
				if ($campaign_scrape_comments==1)
				{
					echo "<i>".count($comments_names)." comments found.</i><br>";
				}
				echo "<i>$link</i><br>";	
				?>
				<!--- item start--->
				<?php
				echo "<br><br>$description<br><br>";
				if ($campaign_scrape_comments==1)
				{
					echo "<ul>";
					foreach ($comments_names as $k=>$v)
					{
						echo "<li><font style='font-size:10px'>\" $comments_content[$k] \"- $v </li>";
					}
					echo "</ul>";
				}				
				echo "<br><br><hr>";

			}//if allowed through include gateway
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
			else if(strstr($thislink, $blog_url))
			{
				echo "<u>$title</u><br>";
				echo "Link: <i>$link</i><br>";
				echo "Status: <b>LOOK! YOU OWN LINKS ARE BEING IMPORTED. THIS IS USUALLY A GOOD SIGN. (ITEM SKIPPED)</b><hr><br>";
			}
			else
			{
				echo "Stats: ALREADY PUBLISHED.<br>";
			}
		}
		    
	}//foreach item
?>