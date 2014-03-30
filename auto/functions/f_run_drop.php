<?php
require_once(ABSPATH . WPINC . '/registration.php');


foreach ($campaign_id as $key=>$val)
{
	$posts_to_bookmark = "";
	$links = "";
	

	//make sure we only work with RSS Campaigns
	if ($this_mode=='cron_config'&&$campaign_type[$key]=='fileimport'&&$campaign_status[$key]=='1'||$this_mode=='solo'&&$campaign_id[$key]==$cid)
	{	
		echo "<br><br>************************************************<br>$campaign_name[$key] : $campaign_source[$key]<br>************************************************<br>";
		
		//reset backdate array
		$backdate = array();
		
		//make arrays of regex if available
		if ($campaign_regex_search[$key])
		{
		   $campaign_regex_search[$key] = explode('***r***',$campaign_regex_search[$key]);
		   $campaign_regex_replace[$key] = explode('***r***',$campaign_regex_replace[$key]);
		}
		//***done preparing additional variables
		
		if ($z_rss_scraping[$key]==3)
		{
			$campaign_feed[$key] = str_replace('http://', '', $campaign_feed[$key]);
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
		

		if ($campaign_name[$key]=='text_import')
		{
			if ($campaign_source[$key]=="parent")
			{
				$source="";
				$folder = "parent";
			}
			else 
			{ 
				$folder = $source;
			}
			  
			//open folder and discover files
			$links = files_in_directory("./my-articles/$campaign_source[$key]");
			$links = bs_simple_sort($links,$z_yahoo_option_sorting[$key]);
			
			$link_count = count($links);
			if ($campaign_limit_results>0&&($campaign_limit_results<$link_count))
			{
				$links = array_slice($links, 0 , $campaign_limit_results[$key]);
			}
			
		}
		else
		{
			$query = ',';
			//$lines = file("./my-csv-files/$source");
			$lines = csv_to_array("./my-csv-files/$campaign_source[$key]", $campaign_query[$key]);
			
			$cols = $lines[0];
			$col_count = count($cols);
			//print_r($lines);exit;
			$link_count = count($lines);
			if ($campaign_limit_results[$key]!=0&&($campaign_limit_results[$key]<$link_count))
			{
				$link_count = $campaign_limit_results[$key];
			}
			
			for ($i=1;$i<$link_count;$i++)
			{
				$this_row = $lines[$i];
				foreach ($cols as $a => $b)
				{
					$b = trim($b);
					$rows[$i][$b] = $this_row[$a];
				}
			}
				
			$links = $rows;
			$links = bs_simple_sort($links,$z_yahoo_option_sorting[$key]);
		}
		
		if (count($links)==0)
		{
			echo "Campaign produces no results. ";
			$links = array();
		}
		
		if ($campaign_limit_results[$key]!=0&&count($links)>$campaign_limit_results[$key])
		{
			//echo 1; exit;
			$links = array_slice($links, 0 ,$campaign_limit_results[$key]);
		}
		
		foreach ($links as $k=>$v)
		{

			$title = "";
			$description = "";
			$link = "";
			$youtube_vids = "";
			$nutags = "";

			if ($campaign_name[$key]=='text_import')
			{
				if ($campaign_source[$key]=="parent")
				{
					 $open = fopen("./my-articles/$v", "r");
					 $string = fread($open, filesize("./my-articles/$v"));
					 fclose($open);	
				}
				else
				{ 
					 $open = fopen("./my-articles/$campaign_source[$key]/$v", "r");
					 $string = fread($open, filesize("./my-articles/$campaign_source[$key]/$v"));
					 fclose($open);
				}
				 
				if ($campaign_query[$key]=='filename')
				{
					$title = explode(".",$v);
					$title = $title[0];
					$content = $string;
				}
				else
				{
					$content = file("./my-articles/$campaign_source[$key]/$v");
					$title =  $content[0];
					$content = $string;
					$content = str_replace_once($title, '', $content);
				}
				 
				if (!strpos($content,"<p>")&&!strpos($content,"<br>")&&!strpos($content,"<br />")) 
				{	
					$content = nl2br($content);
				}
				
				$content = special_htmlentities($content);
				$title = strip_tags($title);
				$title = str_replace('%title%',$title,$campaign_title_template[$key]);
				$description = str_replace('%postbody%',$content,$campaign_post_template[$key]);
				//echo $description;exit;
				$link = $v;
			}
			else
			{
				//echo $campaign_title_template;exit;
				$rows = array_values($rows);
				$title = $campaign_title_template[$key];
				$description = $campaign_post_template[$key];
				if (strstr($campaign_category[$key],'csv:'))
				{
					unset($this_category);
					$this_category = explode(':',$campaign_category[$key]);
				}
				
				foreach ($cols as $a=>$b)
				{
					$b = trim($b);
					$description = str_replace("%{$b}%", $rows[$k][$b], $description);
					$title = str_replace("%{$b}%", $rows[$k][$b], $title);
					if ($campaign_autotag_method[$key]==4)
					{
						$campaign_autotag_custom_tags[$key] = str_replace("%{$b}%", $rows[$k][$b],$campaign_autotag_custom_tags[$key]);
					}
					
					if (is_array($this_category))
					{
						$this_category[1] = str_replace("%{$b}%", $rows[$k][$b], $this_category[1]);
					}
					
				}
				
				$format_title = sanitize_title_with_dashes( $title );
				$v = $format_title;
				//echo $title; exit;
			}
			//echo $v; exit;
			$query ="SELECT original_source from ".$table_prefix."posts WHERE original_source='$v' ";
			$result= mysql_query($query);
			if (!$result) { echo $query; echo mysql_error();exit; }
			$row_count_1 = mysql_num_rows($result);
			if ($row_count_1>0&&$campaign_post_overwrite[$key]==1){$row_count_1=0;}
			
			$query ="SELECT * from ".$table_prefix."blocked_urls WHERE url='$value'";
			$result= mysql_query($query);
			if (!$result) { echo $query; echo 1;exit; }
			$row_count_2 = mysql_num_rows($result);
			
			
			//procede if original				
			if ($row_count_1==0&&$row_count_2==0&&$title&&$stop==0)
			{	
				
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
				if ($campaign_strip_links[$key]==1)
				{
					$description = strip_tags($description,'<ul><li><ol><pre><img><div><table><tr><td><i><b><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><date><font><li><ul><object><embed><br><small><label><br/>');
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
					$description = strip_tags($description,'<ul><li><ol><pre><div><table><tr><td><i><b><a><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><date><font><li><ul><embed><object><br><small><label><br/>');
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
					$date_today = $server_date_time;
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
				
				
				//echo $tags_nature; exit;
				//determine tags
				if ($campaign_autotag_method[$key]!=0)
				{
					$tags = explode(" ",$title);
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
						$title = spin_text($title, $link, $title, $campaign_image_floating[$key],$campaign_language[$key]);
						$description = spin_text($description, $link, $title, $campaign_image_floating[$key],$campaign_language[$key]);
						
						$title = special_htmlentities($title);
						$description = special_htmlentities($description);
					}	
					
					//save original if contains spyntax
					if (strstr($description,'[spyntax]'))
					{
						$description_spyntax = $description;
					}
					if (strstr($title,'[spyntax]'))
					{
						$title_spyntax = $title;
					}
					
					//populate post template
					$description_content = hook_content($description,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
					$description = $description_content;
					
					$title_content = hook_content($title,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
					$title = $title_content;
					
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
					
			
					if (strlen($description)>10) 
					{
						
						//if local image storing is on then do magic
						if ($store_images==1)
						{
							$description = store_images($description,$store_images_relative_path, $store_images_full_url, $title, $link, $blog_url);
						}
						
						//discover default author if neccecary
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
							
						//prepare post_name for url perma links
						$title = trim($title);
						$title = special_htmlentities($title);
						$description = special_htmlentities($description);
						$permalink_name = sanitize_title_with_dashes( $title );
						$title =  addslashes($title);
						$description = addslashes($description);
					
						if ($campaign_author[$key]=='keep_author')
						{
							$campaign_author[$key] = username_exists($original_author);
							if (!$campaign_author[$key] )
							{
								//create username
								$secret_phrase ="blogsensecreateusername0123456789";
								$key = str_shuffle($secret_phrase);
								$password = substr($key, 0, 6);
								$campaign_author[$key] = wp_create_user( $original_author, $password, "noreply@noreply-$password.com" );
							}
						}						
						
						if (strstr($description,'<date>'))
						{
							$date_placeholder = get_string_between($description, '<date>','</date>');
							$description = str_replace("<date>$date_placeholder</date>",'' ,$description);
						}	
						
						if ($cronjob_randomize==1)
						{
							$date_placeholder = randomize_date($date_placeholder,$cronjob_randomize_min,$cronjob_randomize_max);
						}
						
						
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
						$gmt_date = gmdate('Y-m-d H:i:s', strtotime($date_placeholder));
						
						
						//if cat is array break up
						$cat  =  $campaign_category[$key];
						if (!is_array($cat))
						{
							$cat = array($cat);
						}
						
						//check for autocategorization
						if ($campaign_autocategorize[$key]==1)
						{		
							$cat = auto_categorize($cat, $title,$description,$campaign_autocategorize_search[$key], $campaign_autocategorize_method[$key], $campaign_autocategorize_filter_keywords[$key], $campaign_autocategorize_filter_categories[$key], $campaign_autocategorize_filter_list[$key],$post_id[$key],$post_id);
						}
						
						if ($this_category[0]=='csv')
						{
							//echo 1;exit;
							$cat  = $this_category[1];
							$this_cat = get_term_by('name', $cat, 'category');
							$this_cat = $this_cat->term_id;
							if (!$this_cat)
							{
								$cat = str_replace(array("'","\"","/","(",")","?",".","!",":",";"),"",$cat);
								
								$cat =  wp_insert_term($cat, "category");
								//echo $cat;
								$cat = $cat['term_id'];
								$cat = array($cat);
							}
							else
							{
								$cat = array($this_cat);
							}
						}
						
							
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
							  'original_source'=> $permalink_name
							);  
							
							if ($campaign_post_overwrite[$key]==1)
							{
								$query ="SELECT ID FROM {$table_prefix}posts WHERE original_source='$permalink_name'";
								$result = mysql_query($query);
								$arr = mysql_fetch_array($result);
								$post_id = $arr['ID'];
								$post['ID'] = $post_id;
								
								if ($post_id)
								{
									wp_update_post( $post, $wp_error );
								}
								else
								{
									$post_id = wp_insert_post( $post, $wp_error );
								}
							}
							else
							{
								$post_id = wp_insert_post( $post, $wp_error );
								//echo 1; exit;
							}
							
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
							$query = "UPDATE ".$table_prefix."posts SET original_source='{$permalink_name}', bs_campaign_id='{$campaign_id[$key]}' WHERE ID='{$post_id}'";
							$result = mysql_query($query);					
							if (!$result){echo $query; echo mysql_error(); exit;}
							//exit;
							//add custom fields
							if ($campaign_custom_field_name[$key])
							{
								//echo print_r($campaign_custom_field_name[$key]); exit;
								$image = bs_get_images($description);
								//print_r($image);
								foreach ($campaign_custom_field_value[$key] as $kk=>$vv)
								{
									if ($vv=='%image_1%')
									{
										$image_url = $image[0];
										add_post_meta($post_id, $campaign_custom_field_name[$key][$kk], $image_url, true);
										
										$post_thumbnail_id = bs_create_post_attachment_from_url($image_url, $post_id);
										if(is_int($post_thumbnail_id)) {
											update_post_meta( $post_id, '_thumbnail_id', $post_thumbnail_id );
										}
									}
									else if ($vv=='%image_2%')
									{
										$image_url = $image[1];
										add_post_meta($post_id, $campaign_custom_field_name[$key][$kk], $image_url, true);
										
										$post_thumbnail_id = bs_create_post_attachment_from_url($image_url, $post_id);
										if(is_int($post_thumbnail_id)) {
											update_post_meta( $post_id, '_thumbnail_id', $post_thumbnail_id );
										}
									}
									else if ($vv=='%video_embed%')
									{
										preg_match('/\<object(.*?)\<\/object\>/si', $description, $matches);
										if ($matches[0])
										{
											add_post_meta($post_id, $campaign_custom_field_name[$key][$kk], $matches[0], true);
										}
									}
									else
									{
										if ($cols)
										{
											foreach ($cols as $a=>$b)
											{
												$vv = str_replace("%{$b}%", $rows[$k][$b], $vv);
											}
											
											$vv = hook_content($vv,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name[$key],$campaign_query[$key],$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
											add_post_meta($post_id, $campaign_custom_field_name[$key][$kk], $vv, true);
											//echo $vv; 
											//echo "<hr>";
										}
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
							if ($campaign_remote_publishing_api_bs[$key])
							{		   
								$remote_publishing_api_bs = explode(';',$campaign_remote_publishing_api_bs[$key]);	
								$this_link = get_permalink($post_id);
								
								foreach ($remote_publishing_api_bs as $a=>$b)
								{
									
									if ($description_spyntax)
									{
										$description = hook_content($description_spyntax,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
										unset($description_spyntax);
									}
									if ($title_spyntax)
									{
										$title = hook_content($title_spyntax,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
										unset($title_spyntax);
									}
									
									$b = trim($b);
									$this_data['post_title'] = $title;
									$this_data['post_content'] = $description;
									$this_data['post_status'] = $post_status;
									$this_data['post_type'] = $campaign_post_type[$key];
									$this_data['post_date'] = $date_placeholder;
									$this_data['post_tags'] = $tags;
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
									
									if (($description_spyntax||$title_spyntax)&&$remote_publishing_api_xmlrpc_spin[$a]=='on')
									{
										if ($title_spyntax)
										{
											$this_data['title'] = hook_content($title_spyntax,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
											unset($title_spyntax);
										}
										if ($description_spyntax)
										{
											$this_data['content'] = hook_content($description_spyntax,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
											$this_data['description'] = $this_data['content'];
											unset($description_spyntax);
										}											
										
									}
									else if ($remote_publishing_api_xmlrpc_spin[$a]=='on')
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
									if ($description_spyntax)
									{
										$title = hook_content($title_spyntax,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
										$description = hook_content($description_spyntax,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
										unset($title_spyntax);
										unset($description_spyntax);
										$remote_publishing_api_email_footer[$a] = str_replace('+spin','',$remote_publishing_api_email_footer[$a]);
									}
									else if (strstr($remote_publishing_api_email_footer[$a],'+spin'))
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
			unset($url);
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
	usleep($cronjob_buffer_campaigns);
	
}//foreach campaign
?>