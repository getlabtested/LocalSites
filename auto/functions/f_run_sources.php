
<?php
foreach ($campaign_id as $key=>$val)
{
	$posts_to_bookmark = "";
	
	//make sure we only work with Source Campaigns
	if ($this_mode=='cron_config'&&$campaign_type[$key]=='sources'&&$campaign_status[$key]=='1'||$this_mode=='solo'&&$campaign_id[$key]==$cid)
	{
		if (strstr($campaign_feed[$key], '%5Bspyntax%5D'))
		{
			$get = get_string_between($campaign_feed[$key], '%5Bspyntax%5D','%5B%2Fspyntax%5D');
			$decodeget = urldecode($get);
			$spun = spyntax($decodeget);
			$campaign_query[$key] = $spun;
			$spun = urlencode($spun);
			$campaign_feed[$key] = str_replace_once("%5Bspyntax%5D".$get."%5B%2Fspyntax%5D", $spun ,$campaign_feed[$key]);
			//echo $campaign_feed;exit;
		}
		
		if (strstr($campaign_feed[$key], '[spyntax]'))
		{
			$get = get_string_between($campaign_feed[$key], '[spyntax]','[/spyntax]');
			$decodeget = urldecode($get);
			$spun = spyntax($decodeget);
			$campaign_query[$key] = $spun;
			$spun = urlencode($spun);
			$campaign_feed[$key] = str_replace_once("[spyntax]".$get."[/spyntax]", $spun ,$campaign_feed[$key]);
			//echo $campaign_feed;exit;
		}
		
		echo "<br><br>************************************************<br>$campaign_source[$key] : $campaign_name[$key] <br>************************************************<br>";
		
		//***reset backdate array just in case
		$backdate = array();
		$links = array();
		
		
		//***prepare scrape profile variables for campaign
		$query = "SELECT * FROM ".$table_prefix."sourcedata WHERE id=$campaign_scrape_profile[$key]";
		$result = mysql_query($query);
		if (!$result){echo $query; exit;}
		while ($arr = mysql_fetch_array($result))
		{
			//echo 1; exit;
			$campaign_scrape_content_start[$key] = $arr['content_start'];
			$campaign_scrape_content_start_backup_1[$key] = $arr['content_start_backup_1'];
			$campaign_scrape_content_start_backup_2[$key] = $arr['content_start_backup_2'];
			$campaign_scrape_content_end[$key] = $arr['content_end'];
			$campaign_scrape_content_end_backup_1[$key] = $arr['content_end_backup_1'];
			$campaign_scrape_content_end_backup_2[$key] = $arr['content_end_backup_2'];
			$campaign_scrape_title_start[$key] = $arr['title_start'];
			$campaign_scrape_title_start_backup_1[$key] = $arr['title_start_backup_1'];
			$campaign_scrape_title_start_backup_2[$key] = $arr['title_start_backup_2'];
			$campaign_scrape_title_end[$key] = $arr['title_end'];
			$campaign_scrape_title_end_backup_1[$key] = $arr['title_end_backup_1'];
			$campaign_scrape_comments_status[$key] = $arr['comments_status'];
			$campaign_scrape_comments_name_start[$key] = $arr['comments_name_start'];
			$campaign_scrape_comments_name_end[$key] = $arr['comments_name_end'];
			$campaign_scrape_comments_content_start[$key] = $arr['comments_content_start'];
			$campaign_scrape_comments_content_end[$key] = $arr['comments_content_end'];		
			
			if ($campaign_regex_search[$key])
			{
				$source_regex_search[$key] = $arr['regex_search'];
				$source_regex_replace[$key] = $arr['regex_replace'];
			}
			else
			{
				$campaign_regex_search[$key] = $arr['regex_search'];
				$campaign_regex_replace[$key] = $arr['regex_replace'];
			}
		}
	
		//make arrays of regex if available; combine additional regex for sources
		if ($campaign_regex_search[$key])
		{
		   $campaign_regex_search[$key] = explode('***r***',$campaign_regex_search[$key]);
		   $campaign_regex_replace[$key] = explode('***r***',$campaign_regex_replace[$key]);
		   
		   if ($source_regex_search[$key])
		   {
				$source_regex_search[$key] = explode('***r***',$source_regex_search[$key]);
				$source_regex_replace[$key] = explode('***r***',$source_regex_replace[$key]);
				
				$campaign_regex_search[$key] = array_merge( $campaign_regex_search[$key] , $source_regex_search[$key]);
				$campaign_regex_replace[$key] = array_merge($campaign_regex_replace[$key] , $source_regex_replace[$key]);
				
				$campaign_regex_search[$key] = array_unique($campaign_regex_search[$key]);
				$campaign_regex_replace[$key] = array_unique($campaign_regex_replace[$key]);			
		   }
		}
		//***done preparing additional variables
		
		//build list of links from feed
		$string_links = quick_curl($campaign_feed[$key],1);	
		$link_count = substr_count($string_links, "<link>");			
		//echo $string_links; exit;	
		
		if ($campaign_limit_results[$key]!=0&&($campaign_limit_results[$key]<$link_count))
		{
			$link_count = $campaign_limit_results[$key];
		}
		
		for ($i=0;$i<$link_count;$i++)
		{
		   $links[$i] = get_string_between($string_links, "<link>", "</link>");
		   $string_links = str_replace("<link>$links[$i]</link>", "", $string_links);
		}
		
		if (count($links)==0)
		{
		 echo "Campaign ( $campaign_name[$key] ) produces no results. ";
		}
		
		echo count($links); exit;		
		foreach ($links as $k=>$v)
		{
			$title = "";
			$description = "";
			$link = $v;
			$comments_names = "";
			$comments_content = "";
			$nutags = "";
			$stop = 0; 
			
			if ($campaign_scrape_content[$key]==1)
			{
				$this_value = str_replace('http://', '', $v);
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
					$stop=1;
				}
			}
			else
			{
				$string = quick_curl($v,1);

				if (!$string)
				{
					echo "Something went wrong. Source returned an empty result. Please contact administrator. Exiting Script.";
					exit;
				}
			
				//special character work
				if (strstr($string,'gb2312'))
				{
				   //echo 1;
				   $string = mb_convert_encoding($string, 'UTF-8', 'GB2312');
				}
				
				//Try the initial scrape paramater
				$title =  get_string_between($string, $campaign_scrape_title_start[$key], $campaign_scrape_title_end[$key]);
				
				//run through backups
				if (!$title&&$campaign_scrape_title_start_backup_1[$key])
				{
					$title =  get_string_between($string, $campaign_scrape_title_start_backup_1[$key], $campaign_scrape_title_end_backup_1[$key]);
				}
				if (!$title&&$campaign_scrape_title_start_backup_2[$key])
				{
					$title =  get_string_between($string, $campaign_scrape_title_start_backup_2[$key], $campaign_scrape_title_end_backup_2[$key]);
				}
				$title = special_htmlentities($title);
				
				//Try the initial scrape paramater
				$description = get_string_between($string, $campaign_scrape_content_start[$key], $campaign_scrape_content_end[$key]);
				$campaign_scrape_content_start_search = $campaign_scrape_content_start[$key];
				$campaign_scrape_content_end_search = $campaign_scrape_content_end[$key];
				
				//run through backups
				if (!$description&&$campaign_scrape_content_start_backup_1[$key])
				{
					$description =  get_string_between($string, $campaign_scrape_content_start_backup_1[$key], $campaign_scrape_content_end_backup_1[$key]);
					$campaign_scrape_content_start_search  = $campaign_scrape_content_start_backup_1[$key];
					$campaign_scrape_content_end_search  = $campaign_scrape_content_end_backup_1[$key];
				}
				if (!$description&&$campaign_scrape_content_start_backup_2[$key])
				{
					$description =  get_string_between($string, $campaign_scrape_content_start_backup_2[$key], $campaign_scrape_content_end_backup_2[$key]);
					$campaign_scrape_content_start_search = $campaign_scrape_content_start_backup_2[$key];
					$campaign_scrape_content_end_search = $campaign_scrape_content_end_backup_2[$key];
				}
				$description = special_htmlentities($description);
				
				if (strstr($string, $campaign_scrape_content_start_search)||$description)
				{
					$bc_status = 'found';
					$stop = 0;
				}
				else
				{
					//echo 1;
					$bc_status = 'not found';
					$stop=1;
				}
				if (strstr($string, $campaign_scrape_content_end_search)||$description)
				{
					$ec_status = 'found';
					$stop=0;
				}
				else
				{
					//echo 2;
					$ec_status = 'not found';
					$stop=1;
				}
			}
			//exit;
			
			//first make sure the entry isnt already in the database
			$thislink = addslashes($link);
			$query ="SELECT original_source from ".$table_prefix."posts WHERE original_source='$thislink'";
			$result= mysql_query($query);
			if (!$result) { echo $query; echo 1;exit; }
			$row_count_1 = mysql_num_rows($result);
			
			$query ="SELECT * from ".$table_prefix."blocked_urls WHERE url='$thislink'";
			$result= mysql_query($query);
			if (!$result) { echo $query; echo 1;exit; }
			$row_count_2 = mysql_num_rows($result);
					
			//procede if original				
			if ($row_count_1==0&&$row_count_2==0&&$stop!=1&&$title)
			{	
				$images = bs_get_images($description);
				
				if ($campaign_scrape_comments_status[$key]==1)
				{
					//echo $campaign_scrape_comments_name_start[$key];exit;
					$string = str_replace($description, "", $string);
					$comments_array = scrape_comments($string,$campaign_scrape_comments_name_start[$key],$campaign_scrape_comments_name_end[$key],$campaign_scrape_comments_content_start[$key],$campaign_scrape_comments_content_end[$key]);
					//print_r($comments_array);exit;
					$comments_names = $comments_array[0];
					$comments_content = $comments_array[1];
					//$comments_dates = $comments_array[2];
					
					//print_r($comments_names); exit;
				}
				
				
				if ($campaign_strip_links[$key]==1)
				{
					$description = strip_tags($description,'<ul><li><ol><pre><img><div><table><tr><td><i><b><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><font><li><ul><br><embed><object><small><label><br/>');
				}
				
				if ($campaign_strip_links[$key]==2)
				{				
					$description = links_to_tag_links($description, $blog_url);
				}	
				
				if ($campaign_strip_links[$key]==3)
				{						
					$description = links_to_search_links($description, $blog_url);
				}	
				
				//remove links from description if remove_link is on
				if ($campaign_strip_images[$key]==1)
				{
					$description = strip_tags($description,'<ul><li><ol><pre><div><table><tr><td><i><b><a><p><span><u><font><center><blockquote><font><h1><h2><h3><font><li><ul><br><embed><object><small><label><br/>');
				}
				else
				{
					//echo $description; exit;
					$description = apply_floating($description, $campaign_image_floating, $v);
				}		

				//strip post of uneeded html

				$description = clean_html($description);
				$title = htmlspecialchars_decode($title);
				$title = strip_tags($title);
				$title = replace_trash_characters($title);
				$link = $v;
							
				//spin text					
				if ($campaign_spin_text[$key]==1)
				{
					$language="spin";
					$description = spin_text($description, $link, $title, $campaign_image_floating[$key],$language);
					$title = spin_text($title, $link, $title, $campaign_image_floating[$key], $language);
					
					if ($comments_content)
					{
						if (count($comments_content)>0)
						{
							$comments_content = implode('::c::', $comments_content);
							$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating[$key], $language);
							$comments_content = explode('::c::',$comments_content);
						}
					}
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
					$description = spin_text($description, $link, $title, $campaign_image_floating[$key],$language);

					if ($comments_content)
					{
						if (count($comments_content)>0)
						{
							$comments_content = implode('::c::', $comments_content);
							$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating[$key], $language);
							$comments_content = explode('::c::',$comments_content);
						}
					}
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
					
					if ($comments_content)
					{
						if (count($comments_content)>0)
						{
							$comments_content = implode('***', $comments_content);
							$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating[$key], $language);
							$comments_content = explode('***',$comments_content);
						}
					}
				}
					
				//spin text					
				if ($campaign_spin_text[$key]==6)
				{
					$language="salt";
					$title = spin_text($title, $link, $title, $campaign_image_floating[$key], $language);
					$description = spin_text($description, $link, $title, $campaign_image_floating[$key], $language);
					
					if ($comments_content)
					{
						if (count($comments_content)>0)
						{
							$comments_content = implode('***', $comments_content);
							$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating[$key], $language);
							$comments_content = explode('***',$comments_content);
						}
					}
				}
							
				//translate				
				if ($campaign_language[$key]!="no translation")
				{
					//echo $title; exit;
					$title = spin_text($title, $link, $title, $campaign_image_floating[$key],$campaign_language[$key]);
					//echo $title; exit;
					$description = spin_text($description, $link, $title, $campaign_image_floating[$key],$campaign_language[$key]);
					//echo $title; exit;
					$title = special_htmlentities($title);
					$description = special_htmlentities($description);
					
					if ($comments_content)
					{
						if (count($comments_content)>0)
						{
							$comments_content = implode('***', $comments_content);
							$comments_content = spin_text($comments_content, $link, $title, $campaign_image_floating[$key], $campaign_language[$key]);
							$comments_content = special_htmlentities($comments_content);
							$comments_content = explode('***',$comments_content);
						}
					}
				}
				
				//populate title template
				$title_content = hook_content($campaign_title_template[$key],$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name[$key],$campaign_query[$key],$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$title= $title_content;
				
				//populate post template
				$description_content = hook_content($campaign_post_template[$key],$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name[$key],$campaign_query[$key],$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$description = $description_content;	
				
					
				
				//run regex search and replace
				if ($campaign_regex_search[$key])
				{		 
					//echo $description; exit;
					$description = preg_replace($campaign_regex_search[$key],$campaign_regex_replace[$key], $description);		
					//echo $description; exit;					
					$title = preg_replace($campaign_regex_search[$key],$campaign_regex_replace[$key], $title);			  
					 
				}

				if ($campaign_post_date[$key]=='0000-00-00 00:00:00')
				{
					$campaign_post_date[$key] = $wordpress_date_time;
				}
				
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
					
					$campaign_post_date[$key] = date ('Y-m-d H:i:s', strtotime ("$campaign_post_date[$key] + $freq_hour hour"));
					$date_placeholder = $campaign_post_date[$key];
				}
				else
				{
						//echo 1; exit;
					$date_placeholder =  $wordpress_date_time;
					$campaign_post_date[$key] = $date_placeholder;
				}
				
				if ($campaign_autotag_method[$key]!=0)
				{
					$tags = explode(" ",$title);
					//echo $title;
					$tags = prepare_tags($tags,$description,$campaign_autotag_method[$key],$campaign_autotag_custom_tags[$key],$campaign_autotag_mmin[$key],$campaign_autotag_max[$key]);
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
				
				
				
				//prepare link to post for guid field
				$query = "SELECT * FROM ".$table_prefix."posts ORDER BY ID DESC LIMIT 1";	
				$result= mysql_query($query);
				if (!$result){echo $query;exit;}					
								
				while ($array = mysql_fetch_array($result))
				{
					$lid = $array['ID'];
					$nid = $lid+1;
				}
				$guid = "$blog_url?p=$nid";					
				
				//if local image storing is on then do magic
				if ($store_images==1)
				{
					//echo $description; exit;
					$description = store_images($description,$store_images_relative_path, $store_images_full_url, $title, $link, $blog_url );
					//echo $description; exit;
				}
				
				//make a gmdate
				$date_placeholder = date('Y-m-d H:i:s', strtotime($date_placeholder));
				$gmt_date = get_gmt_from_date($date_placeholder);
				
				if (strlen($description)>10&&$title) 
				{
					$include_post=0;
					$exclude_post=0;
					$include_keywords=0;
					$exclude_keywords=0;
					
					if ($campaign_include_keywords[$key]!="Separate with commas.")
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
						//discover default author if neccecary
						if (!$campaign_author[$key]){$campaign_author[$key]=$default_author;}
						if ($campaign_author[$key]=='rand')
						{
							$randkey = array_rand($authors_id);
							$campaign_author[$key]= $authors_id[$randkey];
						}
							
						$domain  = bs_get_domain($link);
						$author_info = get_userdata($campaign_author[$key]);
						$author_name = $author_info->display_name;
						$description = str_replace('%author_name%',$author_name, $description);
						$description = str_replace('%domain_name%',$domain, $description);
						
						//echo $campaign_post_status[$key];exit;						
						//prepare post_name for url perma links
						$title = trim($title);
						$title = special_htmlentities($title);
						$description = special_htmlentities($description);
						$permalink_name = sanitize_title_with_dashes( $title );						
						$title =  addslashes($title);
						$description = addslashes($description);
						
						if ($cronjob_randomize==1)
						{
							$date_placeholder = randomize_date($date_placeholder,$cronjob_randomize_min,$cronjob_randomize_max);
						}
						
						
						
						//determine post status
						if ($campaign_post_status[$key]=='publish')
						{
							$short_pub_date = date('Y-m-d H', strtotime($date_placeholder));
							$short_current_date = date('Y-m-d H', strtotime($wordpress_date_time));
							if ($short_pub_date >  $short_current_date)
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
						$gmt_date = get_gmt_from_date($date_placeholder);

						//if cat is array break up
						$cat  =  $campaign_category[$key];
						if (!is_array($cat))
						{
							$cat = array($cat);
						}
						
						//check for autocategorization
						if ($campaign_autocategorize[$key]==1)
						{		
							$cat = auto_categorize($cat, $title,$description,$campaign_autocategorize_search[$key], $campaign_autocategorize_method[$key], $campaign_autocategorize_filter_keywords[$key], $campaign_autocategorize_filter_categories[$key], $campaign_autocategorize_filter_list[$key],$post_id);
						}
						
						if ($cat!='x')
						{
							//implode tags
							if ($tags){$tags = implode(',',$tags);}
							
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
							$post_id = wp_insert_post( $post, $wp_error );
							
							
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
								foreach ($campaign_custom_field_name[$key] as $k=>$v)
								{
									if ($campaign_custom_field_value[$key][$k]=='%image_1%')
									{
										$image_url = $image[0];
										if ($image_url)
										{
											add_post_meta($post_id, $campaign_custom_field_name[$key][$k], $image_url, true);
										}
										
										$post_thumbnail_id = bs_create_post_attachment_from_url($image_url, $post_id);
										if(is_int($post_thumbnail_id)) {
											update_post_meta( $post_id, '_thumbnail_id', $post_thumbnail_id );
										}
									}
									else if ($campaign_custom_field_value[$key][$k]=='%image_2%')
									{
										$image_url = $image[1];
										if ($image_url)
										{
											add_post_meta($post_id, $campaign_custom_field_name[$key][$k], $image_url, true);
										}
										
										$post_thumbnail_id = bs_create_post_attachment_from_url($image_url, $post_id);
										if(is_int($post_thumbnail_id)) {
											update_post_meta( $post_id, '_thumbnail_id', $post_thumbnail_id );
										}
									}
									else if ($campaign_custom_field_value[$key][$k]=='%video_embed%')
									{
										preg_match('/\<object(.*?)\<\/object\>/si', $description, $matches);
										if ($matches[0])
										{
											add_post_meta($post_id, $campaign_custom_field_name[$key][$k], $matches[0], true);
										}
									}
									else
									{
										$campaign_custom_field_value[$key][$k] = hook_content($campaign_custom_field_value[$key][$k],$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name[$key],$campaign_query[$key],$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
										add_post_meta($post_id, $campaign_custom_field_name[$key][$k], $campaign_custom_field_value[$key][$k], true);
									}
								}
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
									$fake_gmt_date = gmdate('Y-m-d H:i:s', strtotime($fake_date));
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
							//exit;
							
							//update date placeholder data					
							$query = "UPDATE ".$table_prefix."campaigns SET schedule_post_date='$date_placeholder', schedule_post_count='$freq_day_count' WHERE id='$campaign_id[$key]'";
							$result = mysql_query($query);					
							if (!$result){echo $query;}
							
							echo "<br><br><u>$title</u><br>";
							echo "<i>$link</i><br>";	
							echo "<br><br>".stripslashes($description)."<br><br>";
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
				}
			}
			else
			{
				if ($row_count_2>0)
				{
					echo "Status: BLOCKED.<hr>";
				}
				else if ($stop==1||!$title)
				{
					echo "Status: NO CONTENT SCRAPED / IGNORE<hr>";
				}
				else
				{
					echo "Status: ALREADY PUBLISHED.<hr>";
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
			unset($comment_names);
			unset($comment_content);
			unset($comment);
			unset($name);
			unset($nutags);
			unset($tags);
			unset($image);
			unset($date_placeholder);
			unset($gmt_date);
			unset($fake_date);
			unset($fake_date_gmt);
			unset($cat);
			unset($this_link);
			unset($this_link);
			unset($url);
			usleep($cronjob_buffer_items);
		}//FOREACH LINK
	
	}//DETERMINE MODE & SELECT CAMPAIGN
	
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
	usleep($cronjob_buffer_campaigns);
}//FOREACH CAMPAIGN 
	



?>