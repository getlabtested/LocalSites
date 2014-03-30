<?php
foreach ($campaign_id as $key=>$val)
{
	$posts_to_bookmark = "";
	
	//make sure we only work with RSS Campaigns
	if ($this_mode=='cron_config'&&$campaign_type[$key]=='amazon'&&$campaign_status[$key]=='1'||$this_mode=='solo'&&$campaign_id[$key]==$cid)
	{	
		echo "<br><br>******************amazon campaign*********************<br>$campaign_name[$key] : $campaign_feed[$key]<br>************************************************<br>";
		
		//reset backdate array
		$backdate = array();
		
		//make arrays of regex if available
		if ($campaign_regex_search[$key])
		{
		   $campaign_regex_search[$key] = explode('***r***',$campaign_regex_search[$key]);
		   $campaign_regex_replace[$key] = explode('***r***',$campaign_regex_replace[$key]);
		}
		
		
		$string = quick_curl($campaign_feed[$key],0);
		
		if (strpos($string, 'zg_centerColumn')&&strpos($string, 'zg_pagination'))
		{
			$search_results = preg_match('/zg_centerColumn(.*?)zg_pagination/si',$string, $match);
			$string = $match[0];
			
			preg_match_all('/zg_rank(.*?)zg_clear/si', $string, $matches );
			$block_batch = $matches[0];
			$block_batch = array_unique($block_batch);
		}
		else if (strpos($string, 'resultCount')&&strpos($string, 'bottomBar'))
		{
			$string = explode('srNum_',$string);
			array_shift($string);
			$string = implode('srNum_',$string);
			if (strpos($string, 'srNum_'))
			{
				preg_match_all('/srNum_(.*?)srNum_/si', $string, $matches );
				$block_batch = $matches[0];
			}
			else
			{

				preg_match_all('/srNum_(.*?)result_/si', $string, $matches );
				$block_batch = $matches[0];
			}
		}
		else if (strpos($string, 'zg_sparseListItem')&&strpos($string, 'zg_pagination'))
		{
			$search_results = preg_match('/zg_sparseListItem(.*?)zg_pagination/si',$string, $match);
			$string = $match[0];
		
			preg_match_all('/zg_image(.*?)\/div/si', $string, $matches );
			
			foreach ($matches[0] as $aa => $bb)
			{
				$matches[0][$aa] = str_replace('href="','href="http://www.amazon.com', $matches[0][$aa]);
			}
			$block_batch = $matches[0];
			$block_batch = array_unique($block_batch);
		}
		else
		{
	
			 echo "BlogSense cannot find any items to source. If you using a valid amazon search url please notify the administrator of this error.<br><br><hr>$string";exit;
		}

	
		
		if (strstr($campaign_feed[$key], "amazon.de"))
		{
			 $amazon_domain = "de";
		}
		else if (strstr($campaign_feed[$key], "amazon.co.uk"))
		{
			$amazon_domain = "co.uk";
		}
		else
		{
			$amazon_domain = "com";
		}
		
		if (count($block_batch)==0)
		{
		 echo "BlogSense cannot find any items to source. If you using a valid amazon search url please notify the administrator of this error.<br><br><hr>$string";exit;
		}
		
		if (!$amazon_aws_access_key||!$amazon_secret_access_key)
		{
		 echo "BlogSense cannot detect your aws access key or your amazon secret key. These are required to run this module.";exit;
		}
		
		foreach($block_batch as $k=>$v)
		{
			$v = clean_html($v);
			if (strstr($v,'http://ecx.images-amazon'))
			{
				//get links
				preg_match('/http:\/\/(.*?)dp\/[A-Z0-9.-]{1,10}\//', $v, $match);	
				$amazon_links[] = $match[0];
			}					
		}

		
		$items_count = count($amazon_links);
		if ($campaign_limit_results[$key]!=0&&($campaign_limit_results[$key]<$items_count))
		{
			$items_count = $campaign_limit_results[$key];
		}
		
		for ($i=0;$i<$items_count;$i++)
		{	
			preg_match_all('/dp\/(.*?)\//',$amazon_links[$i],$match);
			$asin = $match[1][0];
			$link = "http://www.amazon.$amazon_domain/dp/$asin/?tag={$amazon_affiliate_id}";	
			
			$xml_result_url = "$blogsense_url/includes/i_amazon_calls.php?locale={$amazon_domain}&aws_access_key={$amazon_aws_access_key}&secret_access_key={$amazon_secret_access_key}&asin={$asin}";
			$xml_string = quick_curl($xml_result_url,0);
			//echo $xml_result_url;exit;
			//echo $xml_string; exit;

			$title = "";
			$amazon_params = array();
			
			$amazon_params['original_title'] = get_string_between($xml_string, '<Title>', '</Title>');
			$amazon_params['small_image'] = get_string_between($xml_string, '<SmallImage>', '</SmallImage>');
			$amazon_params['small_image'] = get_string_between($amazon_params['small_image'], '<URL>', '</URL>');
			$amazon_params['medium_image'] = get_string_between($xml_string, '<MediumImage>', '</MediumImage>');
			$amazon_params['medium_image'] = get_string_between($amazon_params['medium_image'], '<URL>', '</URL>');
			$amazon_params['large_image'] = get_string_between($xml_string, '<LargeImage>', '</LargeImage>');
			$amazon_params['large_image'] = get_string_between($amazon_params['large_image'], '<URL>', '</URL>');
			$amazon_params['list_price'] = get_string_between($xml_string, '<FormattedPrice>', '</FormattedPrice>');
			$amazon_params['amazon_brand'] = get_string_between($xml_string, '<Studio>', '</Studio>');
			$amazon_params['amazon_model'] = get_string_between($xml_string, '<Model>', '</Model>');
			if (!$amazon_params['amazon_model'])
			{
				$amazon_params['amazon_model'] = get_string_between($xml_string, '<MPN>', '</MPN>');
			}
			if (!$amazon_params['list_price'])
			{ 
				$amazon_params['list_price'] = "price n/a";
			}
			
			
			//get editorial reviews
			$editorial_review_source = array();
			$editorial_review_content = array();	
			$s = 0;
			while (strstr($xml_string, '<EditorialReview>')&&$s<200)
			{
				$block = get_string_between ($xml_string,'<EditorialReview>','</EditorialReview>');
				$editorial_review_source[] =  get_string_between ($block,'<Source>','</Source>');
				$editorial_review_content[] =  get_string_between ($block,'<Content>','</Content>');
				$xml_string = str_replace_once('<EditorialReview>','',$xml_string);
				$s++;
			}
			
			$manufacturer_product_description= "";
			$amazon_product_description = "";
			$amazon_review = "";
			foreach ($editorial_review_source as $k=>$v)
			{
				//echo $v;
				//echo "<hr>";
				if ($v=='Product Description')
				{
					$manufacturer_product_description = $editorial_review_content[$k];
				}
				if ($v=='Amazon.com Product Description')
				{
					$amazon_product_description = $editorial_review_content[$k];
				}
				if ($v=='Amazon.com Review')
				{
					$amazon_review = $editorial_review_content[$k];
				}
			}
			
			//get features
			$amazon_feature = array();
			$s=0;
			while (strstr($xml_string, '<Feature>')&&$s<200)
			{
				$amazon_feature[] = get_string_between ($xml_string,'<Feature>','</Feature>');
				$xml_string = str_replace_once('<Feature>','',$xml_string);
				$s++;
			}
			
			$amazon_features = "<ul>";
			foreach ($amazon_feature as $k=>$v)
			{
				$amazon_features .= "<li>$v</li>";
			}
			$amazon_features .= "</ul>";
		
			
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
			
			//if (!$description||!$title){$nocontent=1;}else{$nocontent=0;}
			
			//procede if original				
			if ($row_count_1==0&&$row_count_2==0)
			{		
				//open item page and check for content. 
				$comments_url = "http://www.amazon.{$amazon_domain}/product-reviews/$asin/ref=cm_cr_pr_helpful";
				$comments_string = quick_curl($comments_url,0);
				$comments_string = get_string_between($comments_string, 'Most Helpful First','Most Helpful First');
				
				
				preg_match_all('/review\/[A-Z0-9.-]{1,14}\//', $comments_string, $matches );
				$comment_links = $matches[0];
				$comment_links = array_unique($comment_links);
				sort($comment_links);
				//print_r($comment_links);
				
				$blocks = explode('<!-- BOUNDARY -->',$comments_string);
				array_shift($blocks);
				
				$j=0;
				foreach ($comment_links as $k=>$v)
				{
					////$comments_url = "http://www.amazon.com/$val";
					//$comment_string = quick_curl($comments_url);
					$this_block = $blocks[$k];
					$this_block = explode('</b>', $this_block);
					$this_block = $this_block[2];
					$content = get_string_between($this_block, '</div>','<div');

					$s=0;
					while (strstr($content, 'div')&&$s<200)
					{
						$content = preg_replace('/div(.*?)\/div/si','', $content);
						$s++;
					}	
					
					$content = strip_tags($content, '<br>');
					$content = trim($content);
					if ($content)
					{
						$comment_content[$j] = 	$content;
						$comment_author[$j] = strip_tags(get_string_between($blocks[$k], 'By','</a>'));
						$j++;
					}
					
				}
				
				
				$amazon_params['product_description'] = $manufacturer_product_description."<br><br>".$amazon_product_description."<br><br>".$amazon_review;
				
				if (!$amazon_features)
				{
					$amazon_params['amazon_features'] ='n/a';
				}
				else
				{
					$amazon_params['amazon_features'] = $amazon_features;
				}
				
				if (!$comment_content[0]){$comment_content[0]=='n/a';}
				if (!$comment_content[1]){$comment_content[1]=='n/a';}
				if (!$comment_content[2]){$comment_content[2]=='n/a';}
				
				$amazon_params['comment_content'] = $comment_content;
				$amazon_params['comment_author'] = $comment_author;
				$amazon_params['buyitnow_button'] = $store_images_full_url."btn_amazon.gif";
				
				$description = $campaign_post_template[$key];
				$title = $campaign_title_template[$key];
				
				$title = hook_amazon($title,$amazon_params);
				$description = hook_amazon($description,$amazon_params);
				
				
				@array_shift($comment_author);
				@array_shift($comment_author);
				@array_shift($comment_author);
				@array_shift($comment_content);
				@array_shift($comment_content);
				@array_shift($comment_content);
				$comments_names = $comment_author;
				$comments_content = $comment_content;			

						
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
					$description = strip_tags($description,'<ul><li><ol><pre><div><table><tr><td><i><b><a><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><font><li><ul><embed><object><small><label><br/><br>');
				}
							
				
				//spin text					
				if ($campaign_spin_text[$key]==1)
				{
					$language="spin";
					$description = spin_text($description, $link, $title, $campaign_image_floating[$key], $language);
					$title = spin_text($title, $link, $title, $campaign_image_floating[$key], $language);
					
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
				
				//echo $campaign_language;exit;
				//translate				
				if ($campaign_language[$key]!="no translation")
				{
					$title = spin_text($title, $link, $title, $campaign_image_floating[$key],$campaign_language[$key]);
					$description = spin_text($description, $link, $title, $campaign_image_floating[$key],$campaign_language[$key]);
					
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
				
				//echo $title; exit;
				
				//populate title template
				$title_content = hook_content($title,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name[$key],$campaign_query[$key],$original_title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$title= $title_content;
				
				//populate post template
				$description_content = hook_content($description,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name[$key],$campaign_query[$key],$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$description = $description_content;		
				
				//run regex search and replace
				if ($campaign_regex_search[$key])
				{		   
					$description = preg_replace($campaign_regex_search[$key],$campaign_regex_replace[$key], $description);			  
					$title = preg_replace($campaign_regex_search[$key],$campaign_regex_replace[$key], $title);			  
					// echo $description; exit;
				}
				
				//echo $description; exit;
				//cloak links 
				if ($campaign_cloak_links[$key]==1)
				{						   
				   $description = cloak_links($description, $table_prefix);
				   //echo "$description";exit;
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
						$date_placeholder =  $wordpress_date_time;
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
					$date_placeholder = $wordpress_date_time;
					$campaign_post_date[$key] = $date_placeholder;
				}
				
							
				//determine tags
				if ($post_tags==1)
				{
					$tags = explode(" ",$title);
					$tags = prepare_tags($tags,$description,$tags_nature,$tags_custom,$tags_min,$tags_max);
					
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
				
				//if local image storing is on then do magic
				if ($store_images==1)
				{
					$description = store_images($description,$store_images_relative_path, $store_images_full_url, $title, $link, $blog_url);
				}
				//echo $description; exit;
		
				//echo $title; exit;				
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
						
						$title = trim($title);
						$title = special_htmlentities($title);
						$description = special_htmlentities($description);
						$description = htmlspecialchars_decode($description);
						
						//prepare post_name for url perma links					
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
						$date_placeholder = date('Y-m-d H:i:s', strtotime($date_placeholder));
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
							if ($post_status=='draft'&&$draft_notification==1)
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
								//echo print_r($campaign_custom_field_value[$key]);
								$image = bs_get_images($description);
								//print_r($image);
								foreach ($campaign_custom_field_value[$key] as $k=>$v)
								{
									if ($v=='%image_1%')
									{
										$image_url = $image[0];
										add_post_meta($post_id, $campaign_custom_field_name[$key][$k], $image_url, true);
										
										$post_thumbnail_id = bs_create_post_attachment_from_url($image_url, $post_id);
										if(is_int($post_thumbnail_id)) {
											update_post_meta( $post_id, '_thumbnail_id', $post_thumbnail_id );
										}
									}
									else if ($v=='%image_2%')
									{
										$image_url = $image[1];
										add_post_meta($post_id, $campaign_custom_field_name[$key][$k], $image_url, true);
										
										$post_thumbnail_id = bs_create_post_attachment_from_url($image_url, $post_id);
										if(is_int($post_thumbnail_id)) {
											update_post_meta( $post_id, '_thumbnail_id', $post_thumbnail_id );
										}
									}
									else if ($v=='%video_embed%')
									{
										preg_match('/\<object(.*?)\<\/object\>/si', $description, $matches);
										if ($matches[0])
										{
											add_post_meta($post_id, $campaign_custom_field_name[$key][$k], $matches[0], true);
										}
									}
									else
									{		
										//echo "<hr>";
										//echo $v;
										//echo "<hr>";
										$v = hook_amazon($v,$amazon_params);	
										$v = hook_content($v,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name[$key],$campaign_query[$key],$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
										add_post_meta($post_id, $campaign_custom_field_name[$key][$k], $v, true);
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
									$comment = trim($comment);
									$name = trim($name);
									
									if (!$name)
									{
										$name= "guest";
									}
									if ($comment)
									{
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
							}
							//exit;
							
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
			}//if blocked or not original
			else
			{			
				if ($row_count_2>0)
				{
					echo "Status: BLOCKED.<hr>";
				}
				else if ($nocontent==1)
				{
					echo "Status: NO CONTENT<hr>";
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
			unset($amazon_params);
			usleep($cronjob_buffer_items);
		}//foreach item
	}//if solo or cronjob detector
	
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