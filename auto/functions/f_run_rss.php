<?php
include_once(ABSPATH . WPINC . '/registration.php');


foreach ($campaign_id as $key=>$val)
{

	//make sure we only work with RSS Campaigns
	if ($this_mode=='cron_config'&&$campaign_type[$key]=='rss'&&$campaign_status[$key]=='1'||$this_mode=='solo'&&$campaign_id[$key]==$cid)
	{	
		echo "<br><br>************************************************<br>$campaign_name[$key] : $campaign_feed[$key]<br>************************************************<br>";
		
		//reset backdate array
		$backdate = array();
		
		//make arrays of regex if available
		if ($campaign_regex_search[$key])
		{
		   $campaign_regex_search[$key] = explode('***r***',$campaign_regex_search[$key]);
		   $campaign_regex_replace[$key] = explode('***r***',$campaign_regex_replace[$key]);
		}
		//***done preparing additional variables
		
		if ($campaign_scrape_content[$key]==3)
		{
			$campaign_feed[$key] = str_replace('http://', '', $campaign_feed[$key]);
			$campaign_feed[$key] = urlencode($campaign_feed[$key]);
			$campaign_feed[$key] = $blogsense_url."includes/fivefilters/makefulltextfeed.php?url={$campaign_feed[$key]}&max=25&links=preserve&submit=Create+Feed";
		}
	
		$string = quick_curl($campaign_feed[$key],1);
		$string = htmlspecialchars_decode($string);

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
		

		$parameters = discover_rss_parameters($string);
		$string = $parameters['string'];
		$google_reader = $parameters['google_reader'];
		
		$link_count = @substr_count($string, $parameters['link_start']);
		if ($campaign_limit_results[$key]!=0&&($campaign_limit_results[$key]<$link_count))
		{
			$link_count = $campaign_limit_results[$key];
		}
		//echo $link_count;exit;
		
		for ($i=0;$i<$link_count;$i++)
		{
			
			$links[$i] = get_string_between($string, $parameters['link_start'], $parameters['link_end']);
			//echo $links[$i]; exit;			  
			$string = str_replace("{$parameters['link_start']}{$links[$i]}{$parameters['link_end']}","", $string);
			$links[$i] = clean_cdata($links[$i]);
		}
		
		if (count($links)==0)
		{
		 echo "Campaign produces no results. ";
		 $links = array();
		}		

		foreach ($links as $k=>$v)
		{

			//pull the title from rss
			$title = get_string_between($string, $parameters['title_start'], $parameters['title_end']);
			$string = str_replace_once("{$parameters['title_start']}{$title}{$parameters['title_end']}", "", $string);
			//echo $title; exit;
			
			//pull the description from rss						   
			$description = get_string_between($string, $parameters['description_start'],  $parameters['description_end']);			
			if ($description)
			{
				$string = str_replace($parameters['description_start']."$description".$parameters['description_end'], "" , $string); 
			}
			else
			{
				$string = str_replace_once("{$description_start}", "" , $string);
			} 
			//echo $description; exit;

			//pull publish date from rss					   
			$publish_date = get_string_between($string, $parameters['publish_date_start'], $parameters['publish_date_end']);			   
			$string = str_replace_once("{$parameters['publish_date_start']}{$publish_date}{$parameters['publish_date_end']}", " ***pd***" , $string); 
			$publish_date =  date_normalize($publish_date);
			
			//pull author				   
			$original_author = get_string_between($string, $parameters['author_start'], $parameters['author_end']);			   
			$string = str_replace_once("{$parameters['author_start']}{$original_author}{$parameters['author_end']}", " ***a***" , $string); 
			
			//echo $original_author; exit;
			//echo $publish_date; exit;
			
			//echo $string;exit;
			$title = clean_cdata($title);
			$description = clean_cdata($description);
			$title = htmlspecialchars_decode($title);
			$title = strip_tags($title);
			$title = replace_trash_characters($title);
			$title = trim($title);
			$link = $v;		
			$link = str_replace('http://www.google.com/url?sa=X&q=','',$link);
			
						
			if ($campaign_scrape_content[$key]==1)
			{		
				//echo 1; exit;
				$string_focus = quick_curl($v,1);	
				//special character work
				if (strstr($string_focus,'gb2312'))
				{
				   //echo 1;
				   $string_focus = mb_convert_encoding($string, 'UTF-8', 'GB2312');
				}
				
				//echo  $campaign_scrape_content_begin_code; 
				//echo 9;
				//scrape
				$description = get_string_between($string_focus, $campaign_scrape_content_begin_code[$key], $campaign_scrape_content_end_code[$key]);			
				$description = utf8_encode($description);
				$bc_status = "";
				$ec_status = "";
				if (strstr($string_focus, $campaign_scrape_content_begin_code[$key]))
				{
					$bc_status = 'found';
					$stop = 0;
				}
				else
				{
					$bc_status = 'not found';
					$stop=1;
				}
				if (strstr($string_focus, $campaign_scrape_content_end_code[$key]))
				{
					$ec_status = 'found';
					$stop=0;
				}
				else
				{
					$ec_status = 'not found';
					$stop=1;
				}
			}//end rss scraping	

			if ($campaign_scrape_comments[$key]==1)
			{
				$string_focus = quick_curl($v,1);	
				$string_focus = str_replace($description, "", $string_focus);
				$comments_array = scrape_comments($string_focus,$campaign_scrape_names_begin_code[$key],$campaign_scrape_names_end_code[$key],$campaign_scrape_comments_begin_code[$key],$campaign_scrape_comments_end_code[$key]);
				$comments_names = $comments_array[0];
				$comments_content = $comments_array[1];
			}
			
			if (!$description||strstr($description,'Sorry, Readability was unable to parse this page for content.'))
			{
				$stop=1;
			}
			else
			{
				$stop=0;
			}
			
			if (strstr($link,'.html')&&$google_reader==1)
			{
				$link = explode('.html',$link);
				$link = $link[0].'.html';
			}

			
			$query ="SELECT original_source from ".$table_prefix."posts WHERE original_source='$link' AND post_status!='inherit'";
			$result= mysql_query($query);
			if (!$result) { echo $query; echo mysql_error();echo 1;exit; }
			$row_count_1 = mysql_num_rows($result);
			if ($row_count_1>0&&$campaign_post_overwrite[$key]!=1){$row_count_1=0;}
			
			$query ="SELECT * from ".$table_prefix."blocked_urls WHERE url='$link'";
			$result= mysql_query($query);
			if (!$result) { echo $query; echo 1;exit; }
			$row_count_2 = mysql_num_rows($result);
			
			
			//procede if original				
			if ($row_count_1==0&&$row_count_2==0&&$title&&$stop==0&&!strstr($link, $blog_url))
			{	
				$images = bs_get_images($description);
				
				//check for youtube videos
				$o=0;
				while (strstr($description, '<param name="movie" value="http://www.youtube.com')&&$j!='1000')
				{
					$obj = get_string_between($description, '<object','</object>');
					$obj = "<object".$obj."</object>";
					
					if (strstr($obj, 'youtube'))
					{
						$youtube_vids[] = $obj;
						$description = str_replace($obj,"***obj:$o***",$description);
						$o++;
					}	
					else
					{
						$description = str_replace($obj,"",$description);
					}
				}
				
				//echo $description; exit;
				//strip tags, mend unbalanced divs, javascript
				if ($google_reader==0)
				{
					$description = clean_html($description);
				}						
				//echo $description; exit;
				if ($campaign_strip_links[$key]==1)
				{
					$description = strip_tags($description,'<ul><li><ol><pre><img><div><table><tr><td><i><b><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><font><li><ul><object><embed><br><small><label><br/>');
				}
				
				if ($campaign_strip_links[$key]==2)
				{				
					$description = links_to_tag_links($description, $blog_url);
				}	

				if ($campaign_strip_links[$key]==3)
				{						
					$description = links_to_search_links($description, $blog_url);
				}	

				//echo $description; exit;
				//remove links from description if remove_link is on
				if ($campaign_strip_images[$key]==1)
				{
					$description = strip_tags($description,'<ul><li><ol><pre><div><table><tr><td><i><b><a><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><font><li><ul><embed><object><br><small><label><br/>');
				}				
				
				if ($campaign_post_date[$key]=='0000-00-00 00:00:00')
				{
					$campaign_post_date[$key] = $wordpress_date_time;
				}
				
				//echo $title; exit;
				if (strstr($campaign_post_frequency[$key],'.'))
				{
					//echo 1; exit;
					
					$post_frequency = explode(".",$campaign_post_frequency[$key]);
					$freq_day = $post_frequency[1];
					$freq_day_limit = $post_frequency[0];
					$freq_day_count = $campaign_post_count[$key];
					if ($freq_day_count==0){$freq_day_count++;}
					$date_today = $wordpress_date_time;
					$date_placeholder =   $campaign_post_date[$key];
					
					if ($date_placeholder<$date_today&&$campaign_backdating[$key]!=1)
					{
						$date_placeholder = $date_today;
					}
				
					//echo $freq_day; exit;
					if ($date_placeholder=='0000-00-00 00:00:00') 
					{ 
						$date_placeholder = $wordpress_date_time;
						$campaign_post_date[$key] = $date_placeholder;
						$freq_day_count++;
						$campaign_post_count[$key]++;					
					}
					else 
					{ 
						if ($freq_day==1)
						{
							
							if ($freq_day_count >= $freq_day_limit)
							{
								
								$date_placeholder = date ('Y-m-d H:i:s', strtotime ("$date_placeholder + 1 day"));
								$campaign_post_date[$key] = $date_placeholder;
								$campaign_post_count[$key]=0;
								//echo a; 
								//echo $date_placeholder;
								//exit;
							}
							else
							{
							  //echo $freq_day_count;
							  //echo $freq_day_limit;
							  $campaign_post_count[$key]++;	
							  //echo 2;
							  // echo $freq_day_count;exit;
							}
						}
						else 
						{
							//echo "why"; exit;
							$date_placeholder = date ('Y-m-d H:i:s', strtotime ("$date_placeholder + $freq_day day"));
							$campaign_post_date[$key] = $date_placeholder;
						}
					}	
				}
				else if ($campaign_post_frequency[$key]=='backdate')
				{
					$date_placeholder =   $campaign_post_date[$key];
					if ($date_placeholder=='0000-00-00 00:00:00') 
					{ 
						$date_placeholder = $backdate[$k];				
					}
					else 
					{ 
						$date_placeholder = $campaign_post_date[$key];
						$date_placeholder = date ('Y-m-d H:i:s', strtotime ("$date_placeholder - 1 day"));
						$campaign_post_date[$key] = $date_placeholder;
					}
				}
				else if ($campaign_post_frequency[$key]=='feed_date')
				{
					if (!$publish_date)
					{
						echo "No publish date detected in feed. Please change campaign settings to a different scheduling pattern.";exit;
					}
					$date_placeholder = $publish_date;
					$campaign_post_date[$key] = $date_placeholder;
					//echo $date_placeholder;exit;
				}
				else if (strstr($campaign_post_frequency[$key],'min_'))
				{
					$post_frequency = explode("_",$campaign_post_frequency[$key]);
					$freq_min = $post_frequency[1];
					//echo $date_placeholder;
					//echo $freq_min;
					$campaign_post_date[$key] = date ('Y-m-d H:i:s', strtotime ("$campaign_post_date[$key] + $freq_min minute"));
					$date_placeholder = $campaign_post_date[$key];
					//echo $date_placeholder;
				}
				else if (strstr($campaign_post_frequency[$key],'hour_'))
				{
				
					$post_frequency = explode("_",$campaign_post_frequency[$key]);
					$freq_hour = $post_frequency[1];
					
					//echo $freq_hour;
					//exit;
					$campaign_post_date[$key] = date ('Y-m-d H:i:s', strtotime ("$campaign_post_date[$key] + $freq_hour hour"));
					$date_placeholder = $campaign_post_date[$key];
				}
				else
				{
					//echo 1; exit;
					$date_placeholder =  $wordpress_date_time;
					$campaign_post_date[$key] = $date_placeholder;
				}
				
				//echo $tags_nature; exit;
				//determine tags
				if ($campaign_autotag_method[$key]!=0)
				{
					$tags = explode(" ",$title);
					//echo $title;
					$tags = prepare_tags($tags,$description,$campaign_autotag_method[$key],$campaign_autotag_custom_tags[$key],$campaign_autotag_min[$key],$campaign_autotag_max[$key]);
					//print_r($tags); exit;
					if ($post_tags_typo==1)
					{
						$tags = prepare_tags_typo($tags);
					}
					
					if ($nutags)
					{
						$nutags = explode(',',$nutags);
						$tags = array_merge($tags,$nutags);
					}
				}
				
				$include_post=0;
				$exclude_post=0;
				$include_keywords=0;
				$exclude_keywords=0;
				
				if ($campaign_include_keywords[$key]!="Separate with commas."||!$campaign_include_keywords[$key])
				{
					$include_keywords=1;
				}
				else
				{
					$include_post=1;
				}
				
				if ($campaign_exclude_keywords[$key]!="Separate with commas.")
				{
					$exclude_keywords=1;
				}
				
				if ($include_keywords==1)
				{

					
					$include_keywords = explode(",",$campaign_include_keywords[$key]);
					$include_keywords = array_filter($include_keywords);
					foreach ($include_keywords AS $k=>$v)
					{
						if ($campaign_include_keywords_scope[$key]==1)
						{
							if (stristr($title, $v))
							{
								$include_post = 1;										
							}	
						}
						else if ($campaign_include_keywords_scope[$key]==2)
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
					$exclude_keywords = explode(",",$campaign_exclude_keywords[$key]);
					$exclude_keywords = array_filter($exclude_keywords);
					foreach ($exclude_keywords AS $k=>$v)
					{
						if ($campaign_exclude_keywords_scope[$key]==1)
						{
							if (stristr($title, $v))
							{
								$exclude_post = 1;										
							}	
						}
						else if ($campaign_exclude_keywords_scope[$key]==2)
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
					
					//run regex search and replace
					if ($campaign_regex_search[$key])
					{		
						$description = preg_replace($campaign_regex_search[$key],$campaign_regex_replace[$key], $description);			  
						$title = preg_replace($campaign_regex_search[$key],$campaign_regex_replace[$key], $title);							
					}
					
					
					//spin text					
					if ($campaign_spin_text[$key]==1)
					{
						$language="spin";
						$description = spin_text($description, $link, $title, $campaign_image_floating[$key], $language);
						$title = spin_text($title, $link, $title, $campaign_image_floating[$key], $language);
					}	

					//spin text					
					if ($campaign_spin_text[$key]==2)
					{
						$language="spin";
						$title = spin_text($title, $link, $title, $campaign_image_floating[$key], $language);
					}	
					
					//spin text					
					if ($campaign_spin_text[$key]==3)
					{
						$language="spin";
						$description = spin_text($description, $link, $title, $campaign_image_floating[$key], $language);
					}	
					
					//spin text					
					if ($campaign_spin_text[$key]==4)
					{
						$language="salt";
						$title = spin_text($title, $link, $title, $campaign_image_floating[$key], $language);
					}
					
					//spin text					
					if ($campaign_spin_text[$key]==5)
					{
						$language="salt";
						$description = spin_text($description, $link, $title, $campaign_image_floating[$key], $language);
					}
					
					//spin text					
					if ($campaign_spin_text[$key]==6)
					{
						$language="salt";
						$title = spin_text($title, $link, $title, $campaign_image_floating[$key], $language);
						$description = spin_text($description, $link, $title, $campaign_image_floating[$key], $language);
					}
					
					
					//credit source 
					if ($campaign_credit_source[$key]==1)
					{
						if ($campaign_credit_source_nofollow[$key]==1)
						{
						   $nofollow = "rel=nofollow";
						}
						$description .= "<br><br><a href=\"$link\" target=_blank $nofollow>$campaign_credit_source_text[$key]</a><br>";
					}
					
					//translate				
					if ($campaign_language[$key]!="no translation")
					{
						//echo $title;
						$title = spin_text($title, $link, $title, $campaign_image_floating[$key],$campaign_language[$key]);
						//echo $title;exit;
						$description = spin_text($description, $link, $title, $campaign_image_floating[$key],$campaign_language[$key]);
						//echo $description;exit;
					}	
					
					//echo 1; 
					//populate post template
					//echo $campaign_post_template[$key];exit;
					$description_content = hook_content($campaign_post_template[$key],$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name[$key],$campaign_query[$key],$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
					$description = $description_content;

					//populate title template
					$title_content = hook_content($campaign_title_template[$key],$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name[$key],$campaign_query[$key],$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
					$title= $title_content;	
					
					//if local image storing is on then do magic
					if ($store_images==1)
					{
						$description = store_images($description,$store_images_relative_path, $store_images_full_url, $title, $link, $blog_url);
					}
					
					
					//reinsert youtube videos if there
					if ($youtube_vids)
					{
						foreach($youtube_vids as $a=>$b)
						{
							$description = str_replace("***obj:$a***","$youtube_vids[$a]", $description);
						}
						if ($campaign_language[$key]!="no translation")
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
				
					
					if (!$title)
					{
						echo "no title got throught on $key => $link";
						exit;
					}
					
					$raw_description = strip_tags($description);
					if (strlen($raw_description)>30) 
					{
					
						$domain  = bs_get_domain($link);
						
						if ($campaign_author[$key]=='keep_author')
						{
							$campaign_author[$key] = username_exists($original_author);
							if (!$campaign_author[$key] )
							{
								//create username
								$secret_phrase ="blogsensecreateusername0123456789";
								$this_key = str_shuffle($secret_phrase);
								$password = substr($this_key, 0, 6);
								$campaign_author[$key] = wp_create_user( $original_author, $password, "noreply@noreply-$password.com" );
							}
							unset($original_author);
						}	
						
						if ($campaign_author[$key]=='keep_author_domain')
						{
							$original_author = $original_author." - $domain";
							$campaign_author[$key] = username_exists($original_author);
							if (!$campaign_author[$key] )
							{
								//create username
								$secret_phrase ="blogsensecreateusername0123456789";
								$this_key = str_shuffle($secret_phrase);
								$password = substr($this_key, 0, 6);
								$campaign_author[$key] = wp_create_user( $original_author, $password, "noreply@noreply-$password.com" );
							}							
						}	
						
						if ($campaign_author[$key]=='domain')
						{
							$original_author = "$domain";
							$campaign_author[$key] = username_exists($original_author);
							if (!$campaign_author[$key] )
							{
								//create username
								$secret_phrase ="blogsensecreateusername0123456789";
								$this_key = str_shuffle($secret_phrase);
								$password = substr($this_key, 0, 6);
								$campaign_author[$key] = wp_create_user( $original_author, $password, "noreply@noreply-$password.com" );
							}							
						}	
						
						if ($campaign_author[$key]=='rand')
						{
							$this_key = array_rand($authors_id);
							$campaign_author[$key]= $authors_id[$this_key];
						}
						
						$author_info = get_userdata($campaign_author[$key]);
						$author_name = $author_info->display_name;
						$description = str_replace('%author_name%',$author_name, $description);
						$description = str_replace('%domain_name%',$domain, $description);
						
						if ($cronjob_randomize==1)
						{
							$date_placeholder = randomize_date($date_placeholder,$cronjob_randomize_min,$cronjob_randomize_max);
						}
						
						//prepare post_name for url perma links
						
						$title = special_htmlentities($title);
						$title = trim($title);
						$description = special_htmlentities($description);
						$permalink_name = sanitize_title_with_dashes( $title );
						$title =  addslashes($title);
						$description = addslashes($description);
						
						//determine post status
						if ($campaign_post_status[$key]=='publish')
						{
							$short_pub_date = date('Y-m-d H', strtotime($date_placeholder));
							$short_current_date = date('Y-m-d H', strtotime($wordpress_date_time));
							if ($short_pub_date  >  $short_current_date)
							{
								$post_status = "future";
							}
							else
							{
								$post_status = "publish";
							}
						}
						else
						{
							$post_status = $campaign_post_status[$key];
						}
								
						//make a gmdate
						$date_placeholder = date('Y-m-d H:i:s', strtotime($date_placeholder));
						$gmt_date = get_gmt_from_date($date_placeholder);
						
						
						//if cat is array break up
						$cat  =  $campaign_category[$key];
						if (!is_array($cat))
						{
							$cat = array($cat);
						}
						
						//echo $$campaign_autocategorize_filter_list[$key],$post_id[$key];exit;
						//check for autocategorization
						if ($campaign_autocategorize[$key]==1)
						{		
							$cat = auto_categorize($cat, $title,$description,$campaign_autocategorize_search[$key], $campaign_autocategorize_method[$key], $campaign_autocategorize_filter_keywords[$key], $campaign_autocategorize_filter_categories[$key], $campaign_autocategorize_filter_list[$key],$post_id[$key],$post_id);
						}
						//echo $cat;
						//echo $campaign_limit_results[$key];exit;
						//print_r($cat);
						
						
						if ($cat!='x')
						{
							//implode tags
							if ($tags){$tags = implode(',',$tags);}
							
							//discover default author if neccecary
							if (!$campaign_author[$key]){$campaign_author[$key]=$default_author;}

							//insert rss item into database store	
							$post = array(		
							  'post_author' => $campaign_author[$key],
							  'post_category' => $cat, 
							  'post_content' => $description, 
							  'post_date' => $date_placeholder,
							  'post_date_gmt' => $gmt_date,
							  'post_name' => $permalink_name,
							  'post_status' => $post_status, 
							  'post_title' => $title,
							  'post_type' => $campaign_post_type[$key],
							  'tags_input' => "$tags",
							  'original_source'=> $link
							);  
							
							//print_r($post);
							$post_id = wp_insert_post( $post, $wp_error );
							
							echo $wp_error;
							
							$description = stripslashes($description);
							
							//add items to bookmarking queue
							if ($post_status=='publish')
							{
								$posts_to_bookmark[] = $post_id;
							}	

							//add items to bookmarking queue
							if ($post_status=='future')
							{
								$future_posts_to_bookmark[] = $post_id;
								$future_dates[] = $date_placeholder;
							}
							
							//draft notification queue
							if (($post_status=='draft'||$post_status=='pending')&&$draft_notification==1)
							{
								$draft_posts_to_bookmark[] = $post_id;
								$draft_notification_items[] = array($post_id,$title,$description);
							}
							
							//store source as saved				
							$query = "UPDATE ".$table_prefix."posts SET original_source='$link', bs_campaign_id='$campaign_id[$key]' WHERE ID='$post_id'";
							$result = mysql_query($query);					
							if (!$result){echo $query; echo mysql_error(); exit;}
						
							//add custom fields
							if ($campaign_custom_field_name[$key])
							{
								//echo print_r($campaign_custom_field_name[$key]); exit;
								$image = bs_get_images($description);
								
								if (strlen($image[0])<2)
								{
									$image = $images;
								}
								//print_r($image);
								//print_r($campaign_custom_field_value[$key]);exit;
								foreach ($campaign_custom_field_value[$key] as $a=>$b)
								{
									
									if ($b=='%image_1%')
									{
										$image_url = $image[0];
										if ($image_url)
										{
											add_post_meta($post_id, $campaign_custom_field_name[$key][$a], $image_url, true);
											$post_thumbnail_id = bs_create_post_attachment_from_url($image_url, $post_id);
											if(is_int($post_thumbnail_id)) {
												update_post_meta( $post_id, '_thumbnail_id', $post_thumbnail_id );
											}
										}
										
									}
									else if ($b=='%image_2%')
									{
										$image_url = $image[1];
										if ($image_url)
										{
											add_post_meta($post_id, $campaign_custom_field_name[$key][$a], $image_url, true);
											$post_thumbnail_id = bs_create_post_attachment_from_url($image_url, $post_id);
											
											if(is_int($post_thumbnail_id)) {
												update_post_meta( $post_id, '_thumbnail_id', $post_thumbnail_id );
											}
										}
									}
									else if ($b=='%video_embed%')
									{
										preg_match('/\<object(.*?)\<\/object\>/si', $description, $matches);
										if ($matches[0])
										{
											add_post_meta($post_id, $campaign_custom_field_name[$key][$a], $matches[0], true);
										}
									}
									else
									{
										$campaign_custom_field_value[$key][$a] = hook_content($campaign_custom_field_value[$key][$a],$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name[$key],$campaign_query[$key],$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
										add_post_meta($post_id, $campaign_custom_field_name[$key][$a], $campaign_custom_field_value[$key][$a], true);
									}
								}
							}

							//update date placeholder data					
							$query = "UPDATE ".$table_prefix."campaigns SET schedule_post_date='$date_placeholder', schedule_post_count='$freq_day_count' WHERE id='$campaign_id[$key]'";
							$result = mysql_query($query);					
							if (!$result){echo $query;}
							
							
							echo "<br><br><u>$title</u><br>";
							echo "<i>$link</i><br>";	
							echo "<br><br>$description<br><br>";
							if ($campaign_scrape_comments[$key]==1)
							{
								echo "<ul>";
								foreach ($comments_names as $k=>$v)
								{
									echo "<li><font style='font-size:10px'>\" $comments_content[$k] \"- $v </li>";
								}
								echo "</ul>";
							}
							
							
							//add comments
							if ($comments_names&&$campaign_comments_include[$key]==1)
							{
								if ($campaign_comments_limit!=0)
								{
									$comments_names = array_slice($comments_names, 0, $campaign_comments_limit);
									$comments_content = array_slice($comments_content, 0, $campaign_comments_limit);
								}
								
								foreach($comments_names as $a=>$b)
								{ 							
									$fake_date = date('Y-m-d', strtotime("$date_placeholder +$a day")); 
									$fake_gmt_date = get_gmt_from_date($fake_date);
									if ($fake_date >  date('Y-m-d'))
									{
										$comment_approved = "2";
									}
									else
									{
										$comment_approved = "1";
									}
									
									$name = $comments_names[$a];
									$name = addslashes($name);
									$comment = $comments_content[$a];
									$comment = addslashes($comment);
									
									if (!$name)
									{
										$name= "guest";
									}
									
									$data = array(
										'comment_post_ID' => $post_id,
										'comment_author' => $name,
										'comment_author_email' => 'noreply@noreply.com',
										'comment_author_url' => '',
										'comment_content' => $comment,
										'comment_author_IP' => '127.0.0.1',
										'comment_agent' => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; fr; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3',
										'comment_date' => $fake_date,
										'comment_date_gmt' => $fake_gmt_date,
										'comment_approved' => $comment_approved,
									);

									$comment_id = wp_insert_comment($data);
								}
							}
							
							if ($campaign_remote_publishing_api_bs[$key])
							{		   
								$remote_publishing_api_bs = explode(';',$campaign_remote_publishing_api_bs[$key]);	
								$this_link = get_permalink($post_id);
								
								foreach ($remote_publishing_api_bs as $a=>$b)
								{
									$b = trim($b);
									$this_data['post_title'] = $title;
									$this_data['post_content'] = $description;
									$this_data['post_status'] = $post_status;
									$this_data['post_type'] = $campaign_post_type[$key];
									$this_data['post_tags'] = $tags;
									$this_data['post_date'] = $date_placeholder;
									$this_data['cat_id'] = get_cat_slug($cat[0]);
									$this_data['link'] = $link;
									$this_data['manual_mode'] = 0;
									$this_data['internal'] = 1;
									
									$url = $b;
									$return = stealth_curl($url, 0, $this_data,'remote_publish');
									
									if (strstr($return, "Something's missing:"))
									{
										echo "REMOTE PUBLISHING FIRE (blogsense api):  Failure:<br><br>$return<br>exit;";
									}
									else
									{
										$this_permalink = explode('<permalink>',$return);
										$this_permalink = $this_permalink[1];
										$this_permalink = str_replace('</permalink>','',$this_permalink);
										if ($this_permalink)
										{
											$title = addslashes($title);
											$query = "INSERT INTO ".$table_prefix."blogsense_remote_published_urls (`permalink`,`title`,`date`) VALUES ('$this_permalink', '$title','$date_placeholder');";
											$result = mysql_query($query);					
											if (!$result){echo $query;}
										}
										echo "REMOTE PUBLISHING FIRE (blogsense api) : $url<br>";
									}
									
									unset($this_data);
									unset($this_permalink);
									unset($return);
								}
							}
							
							if ($campaign_remote_publishing_api_xmlrpc[$key])
							{
								//echo 1; exit;
								$remote_publishing_api_xmlrpc = explode(':::',$campaign_remote_publishing_api_xmlrpc[$key]);
								$remote_publishing_api_xmlrpc_spin = explode(':::',$campaign_remote_publishing_api_xmlrpc_spin[$key]);
								
								foreach ($remote_publishing_api_xmlrpc as $a=>$b)
								{
									$this_array = explode(";",$b);
									$url = $this_array[0];
									$username = $this_array[1];
									$password = $this_array[2];
									$blog_id = $this_array[2];
									
									$this_data['blogid'] = $blog_id;
									$this_data['username'] = $username;
									$this_data['password'] = $password;
									$this_data['content'] = $description;
									$this_data['description'] = $description;
									$this_data['title'] = $title;
									$this_data['mt_keywords'] = $tags;
									$this_data['categories'] = get_cat_slug($cat[0]);
									if ($remote_publishing_api_xmlrpc_spin[$a]=='on')
									{
										$language='spin';
										$this_data['content'] = spin_text($description, $link, $title, $campaign_image_floating, $language);
										$this_data['description'] = $this_data['content'];
										$this_data['title'] = spin_text($title, $link, $title, $campaign_image_floating, $language);
									}
									
									
									$return = bs_xmlrpc($url,$this_data,$username,$password);
									
									if ($return==1)
									{
										echo "REMOTE PUBLISHING FIRE (XMLRPC): $url<br>";
									}
									else
									{
										echo $return."<br>";
										print_r( $return)."<br>";
									}
									//exit;
									unset($this_array);
									unset($url);
									unset($username);
									unset($password);
									unset($return);
								}
							}
							
							if ($campaign_remote_publishing_api_email[$key])
							{	
								$remote_publishing_api_email = explode(';',$campaign_remote_publishing_api_email[$key]);	
								$remote_publishing_api_email_footer = explode(';',$campaign_remote_publishing_api_email_footer[$key]);	
								$this_link = get_permalink($post_id);
								
								$description = str_replace('â€¢','-',$description);
								
								$headers = "MIME-Version: 1.0 \r\n";
								$headers .= "Content-Type: text/html ;\n";
								
								foreach ($remote_publishing_api_email as $a=>$b)
								{
									$b = trim($b);
									$description = trim($description);
									
									if (strstr($remote_publishing_api_email_footer[$a],'+spin'))
									{
										$language="spin";
										$description = spin_text($description, $link, $title, $campaign_image_floating[$key], $language);
										$title = spin_text($title, $link, $title, $campaign_image_floating[$key], $language);
										$remote_publishing_api_email_footer[$a] = str_replace('+spin','',$remote_publishing_api_email_footer[$a]);
									}
									
									$description = preg_replace("/\r\n/",'',$description);
									//$b = 'hudson.atwell@gmail.com';
									mail($b,$title,$description."<br><br>".$remote_publishing_api_email_footer[$a],$headers);
		
									echo "REMOTE PUBLISHING FIRE (Email): $b<br>";
									usleep(500000);
									
								}
								unset($headers);
							}
						
							
							if ($campaign_remote_publishing_api_pp_email[$key])
							{		   
								$remote_publishing_api_pp_email = explode(';',$campaign_remote_publishing_api_pp_email[$key]);	
								$remote_publishing_api_pp_routing = explode(';',$campaign_remote_publishing_api_pp_routing[$key]);	
								$this_link = get_permalink($post_id);
								
								//echo print_r($campaign_custom_field_name[$key]); exit;
								$image = bs_get_images($description);
								
								if (strlen($image[0])<2)
								{
									$image = $images;
								}
								
								$semi_rand = md5(time());
								$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
								$headers = "MIME-Version: 1.0 \r\n";
								$headers .= "Content-Type: multipart/mixed;\n";
								$headers .=	" boundary=\"$mime_boundary\"";
								$description = str_replace(array("</p>","<br><br>",'</div>','</span>','</li>','</ul>','</table>','</tr>'),"\n\n",$description);
								$description = str_replace('<br>',"\n",$description);
								$description = str_replace('<li>',"•",$description);
								$description = strip_tags($description);
								
								
								foreach ($remote_publishing_api_pp_email as $a=>$b)
								{
									$b = trim($b);
									if (stristr($remote_publishing_api_pp_routing[$a],'+spin'))
									{
										$remote_publishing_api_pp_routing[$a] = str_replace('+spin','',$remote_publishing_api_pp_routing[$a]);
										$language="spin";
										$description = spin_text($description, $link, $title, $campaign_image_floating[$key], $language);
										$title = spin_text($title, $link, $title, $campaign_image_floating[$key], $language);
									}
									
									$body = $description." $remote_publishing_api_pp_routing[$a] \n\n";
									
									if ($image[0]&&$images==1)
									{
										$info = pathinfo($image[0]);
										$file_name =  basename($image[0],'.'.$info['extension']);
										
										
										$image_string  = file_get_contents($image[0]);	
										$image_string = chunk_split(base64_encode($image_string));
										
										$pbody = $body;
										
										$body = "--{$mime_boundary}\n" ;
										$body .= "Content-Type: text/plain; charset=UTF-8\n"; 
										$body .= " filename=\"{$file_name}\"\n" ;
										$body .= "Content-Transfer-Encoding: 7bit\n\n" ;

										$body = $body.$pbody;
										
										$body .= "--{$mime_boundary}\n" ;
										$body .= "Content-Type: image/{$info['extension']} name=\"{$file_name}.{$info['extension']}\"\n";
										$body .= "Content-Transfer-Encoding: base64\n" ;
										$body .= "Content-Disposition: attachment; filename=\"{$file_name}.{$info['extension']}\"\n\n" ;

										$body .= $image_string . "\n\n" ;
										$body .= "--{$mime_boundary}--\n";
									}
									else
									{
										$pbody = $body;
										
										$body = "--{$mime_boundary}\n" ;
										$body .= "Content-Type: text/plain; charset=UTF-8\n"; 
										$body .= "Content-Transfer-Encoding: 7bit\n\n" ;

										$body = $body.$pbody."\n\n";
										$body .= "--{$mime_boundary}--\n";
									}
								
								
									
									//$b = 'hudson.atwell@gmail.com';
									mail($b,"=?UTF-8?B?".base64_encode($title)."?=",$body,$headers);
									
									echo "REMOTE PUBLISHING FIRE (pixelpipe): $b<br>";
									
								}
								unset($headers);
								unset($body);
								
							}
							echo "<br><br><hr>";
						}
						else
						{
							echo "<u>$title</u><br>";
							echo "Link: <i>$link</i><br>";						
							echo "Status: <b>IGNORED BY AUTOCATEGORIZATION: No Placement</b><br><hr><br>";
						}
					}
					else
					{
						echo "<u>$title</u><br>";
						echo "Link: <i>$link</i><br>";						
						echo "Status: <b>IGNORED: NO DESCRIPTION</b><br><hr><br>";
					}
				}//if allowed through include gateway
				else
				{
					if ($include_post!=1)
					{
						echo "<b>$title</b><br>";
						echo "Status: NOT INCLUDED BY KEYWORD.<br>";
						echo "Link: $link<br><hr><br>";	
					}
					else
					{
						echo "<b>$title</b><br>";
						echo "Status: EXCLUDED BY KEYWORD.<br>";
						echo "Link: $link<br><hr><br>";	
					}
				}
			}//if blocked or not original
			else
			{			
				if ($row_count_2>0)
				{
					echo "<u>$title</u><br>";
					echo "Link: <i>$link</i><br>";
					echo "Status: <b>BLOCKED.</b><hr><br>";
				}
				else if ($stop==1)
				{
					echo "<u>$title</u><br>";
					echo "Link: <i>$link</i><br>";
					echo "Status: <b>POST RETURNED EMPTY CONTENT.</b><hr><br>";
				}
				else if(strstr($link, $blog_url))
				{
					echo "<u>$title</u><br>";
					echo "Link: <i>$link</i><br>";
					echo "Status: <b>LOOK! YOU OWN LINKS ARE BEING IMPORTED. THIS IS USUALLY A GOOD SIGN. (ITEM SKIPPED)</b><hr><br>";
				}
				else
				{
					echo "<u>$title</u><br>";
					echo "Link: <i>$link</i><br>";	
					echo "Status: <b>Status: ALREADY PUBLISHED.</b><hr><br>";
				}
			}
			
			
			unset($query);
			unset($matches);
			unset($result);
			unset($count);
			unset($row_count_1);
			unset($row_count_2);
			unset($description);
			unset($title);
			unset($link);
			unset($thislink);
			unset($this_key);
			unset($youtube_vids);
			unset($comment_names);
			unset($comment_content);
			unset($comment);
			unset($name);
			unset($author_name);
			unset($domain_name);
			unset($author_info);
			unset($nutags);
			unset($tags);
			unset($image);
			unset($date_placeholder);
			unset($gmt_date);
			unset($fake_date);
			unset($fake_date_gmt);
			unset($cat);
			unset($this_link);
			unset($url);
			unset($post);
			unset($domain);
			unset($original_author);
			unset($randkey);
			unset($post_status);
			unset($row_count_1);
			unset($row_count_2);
			
			
			usleep($cronjob_buffer_items);
		}//foreach item
	}//if solo or cronjob selector
	
	//schedule new bookmarking jobs
	if ($posts_to_bookmark)
	{
		$return = schedule_bookmarks('publish', NULL, $posts_to_bookmark, $bookmark_pixelpipe[$key], $bookmark_twitter[$key],$bookmark_hellotxt[$key]);
		//echo "<br>".count($posts_to_bookmark)." posts scheduled for bookmarking.<br><br>";
	}
	if ($future_posts_to_bookmark)
	{
		$return = schedule_bookmarks('future', $future_dates, $future_posts_to_bookmark, $bookmark_pixelpipe[$key], $bookmark_twitter[$key],$bookmark_hellotxt[$key]);
	}
	
	if($draft_notification_items)
	{
		$return = schedule_bookmarks('draft', $future_dates, $draft_posts_to_bookmark, $bookmark_pixelpipe[$key], $bookmark_twitter[$key],$bookmark_hellotxt[$key]);
		$return = run_draft_notifications($draft_notification_items);
	}
	
	unset($string);
	unset($posts_to_bookmark);
	unset($future_posts_to_bookmark);
	unset($future_dates);
	unset($draft_notification_items);
	unset($links);
	unset($parameters);
	unset($string_focus);
	usleep($cronjob_buffer_campaigns);
	
	if ($debug==1)
	{
		echo "Current Memory Usage: ".memory_get_usage(true)."<br>";
		echo "Peak Memory Usage: ".memory_get_peak_usage()."";
	}
	
}//foreach campaign


?>