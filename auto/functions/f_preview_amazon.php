<?php
echo "<br><br>***********************************************************************************************<br>";
echo "***********************************AMAZON MODULE****************************************<br>";
echo "*********************************************************************************************<br>";

	echo "<br><br>******************amazon campaign*********************<br>$campaign_name : $campaign_feed<br>************************************************<br>";
	
	//make arrays of regex if available
	if ($campaign_regex_search[$key])
	{
	   $campaign_regex_search[$key] = explode('***r***',$campaign_regex_search[$key]);
	   $campaign_regex_replace[$key] = explode('***r***',$campaign_regex_replace[$key]);
	}	
	
	$string = quick_curl($campaign_feed,0);
	//echo $string;
	//echo "<hr>";
	
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
		
		$block_batch = array_unique($block_batch);
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
		echo "BlogSense cannot find any items to source. If you using a valid amazon search url please notify the administrator of this error.<br><br><hr>$string";
		echo $string;exit;
	}



	
	if (strstr($campaign_feed, "amazon.de"))
	{
	     $amazon_domain = "de";
	}
	else if (strstr($campaign_feed, "amazon.co.uk"))
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
	
	$amazon_links = "";
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
	if ($campaign_limit_results!=0&&($campaign_limit_results<$items_count))
	{
		$items_count = $campaign_limit_results;
	}
	
	for ($i=0;$i<$items_count;$i++)
	{	

		preg_match_all('/dp\/(.*?)\//',$amazon_links[$i],$match);
		$asin = $match[1][0];
		$link = "http://www.amazon.$amazon_domain/dp/$asin/?tag={$amazon_affiliate_id}";	
		
		$xml_result_url = "$blogsense_url/includes/i_amazon_calls.php?locale={$amazon_domain}&aws_access_key={$amazon_aws_access_key}&secret_access_key={$amazon_secret_access_key}&asin={$asin}";
		$xml_string = quick_curl($xml_result_url,0);
		
		if (strstr($xml_string,'The AWS Access Key Id you provided does not exist in our records.'))
		{
			echo "The AWS Access Key Id you provided does not exist in our records.";exit;
		}
		//echo $xml_result_url; exit;
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
		
		//echo $title;		
		//echo $small_image;
		//echo $medium_image;
		//echo $large_image;
		//exit;
		
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
		//exit;
		
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
		$query ="SELECT original_source from ".$table_prefix.$_SESSION['second_prefix']."posts WHERE original_source='$thislink' AND post_status!='trash'";
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
			$comments_url = "http://www.amazon.$amazon_domain/product-reviews/$asin/ref=cm_cr_pr_helpful";
			$comments_string = quick_curl($comments_url,0);
			$comments_string = get_string_between($comments_string, 'Most Helpful First','Most Helpful First');
			
			//echo $comments_string; exit;
			//get individual comment links
			preg_match_all('/review\/[A-Z0-9.-]{1,14}\//', $comments_string, $matches );
			$comment_links = $matches[0];
			$comment_links = array_unique($comment_links);
			sort($comment_links);
			//print_r($comment_links);
			
			
			$j=0;
			foreach ($comment_links as $key=>$val)
			{
				
				if ($j<4)
				{
					////$comments_url = "http://www.amazon.com/$val";
					//$comment_string = quick_curl($comments_url);
					$block = get_string_between($comments_string, '<!-- BOUNDARY -->','votingPrompt');
					$content = get_string_between($block, '</div>','Help other customers');
					$comments_string = str_replace_once('<!-- BOUNDARY -->','',$comments_string);
					$s=0;
					while (strstr($content, 'div')&&$s<200)
					{
						$content = preg_replace('/div(.*?)\/div/si','', $content);
						$s++;
					}	
					
					if ($content)
					{
						$comment_content[$j] = strip_tags($content, '<br>');	
						$comment_author[$j] = strip_tags(get_string_between($block, 'By','</a>'));
						$j++;
					}
				}
			}
			//print_r($comment_content); exit;
			
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
			
			//print_r($comment_content);exit;
			
			$amazon_params['comment_content'] = $comment_content;
			$amazon_params['comment_author'] = $comment_author;
			$amazon_params['buyitnow_button'] = $store_images_full_url."btn_amazon.gif";
			
			$description = $campaign_post_template;
			$title = $campaign_title_template;
			
			$title = hook_amazon($title,$amazon_params);
			$description = hook_amazon($description,$amazon_params);		
			
			if ($campaign_strip_links==1)
			{
				$description = strip_tags($description,'<ul><li><ol><pre><img><div><table><tr><td><i><b><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><font><li><ul><img><strong><embed><object><small><label><br/>');
			}
			
			if ($campaign_strip_links==2)
			{				
				$description = links_to_tag_links($description, $blog_url);
			}	
			
			if ($campaign_strip_links==3)
			{						
				$description = links_to_search_links($description, $blog_url);
			}	
			
			//remove links from description if remove_link is on
			if ($campaign_strip_images==1)
			{
				$description = strip_tags($description,'<ul><li><ol><pre><div><table><tr><td><i><b><a><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><font><li><ul><embed><object><small><label><br/><br>');
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
				$title = spin_text($title, $link, $title, $campaign_image_floating, $language);
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
			
			//echo $campaign_language;exit;
			//translate				
			if ($campaign_language!="no translation")
			{
				$title = spin_text($title, $link, $title, $campaign_image_floating,$campaign_language);
				$description = spin_text($description, $link, $title, $campaign_image_floating,$campaign_language);
			}	
			
			//populate title template
			$title_content = hook_content($title,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$original_title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
			$title= $title_content;
			//echo $campaign_title_template; exit;
			//echo $title; exit;
			
			$post_content = hook_content($description,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
			$description= $post_content;	
			
			
			//run regex search and replace
			if ($campaign_regex_search)
			{		   
				$description = preg_replace($campaign_regex_search,$campaign_regex_replace, $description);			  
				$title = preg_replace($campaign_regex_search,$campaign_regex_replace, $title);			  
				// echo $description; exit;
			}
			
			//cloak links 
			if ($campaign_cloak_links==1)
			{						   
			   $description = cloak_links($description, $table_prefix);
			   //echo "$description";exit;
			}
					
			//urlprepare link for blocking 
			$travel_link = urlencode($link);
			
			if (strlen($description)>5)
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
					
					$description = htmlspecialchars_decode($description);
				
					$travel_link = str_replace('http://','***',$link);
					$travel_link = urlencode($link);
				
					echo "<a name='$key'></a><br>";
					echo "<br><div align=right>";
					echo "<span style='border-type:dotted;borer-size:1px;border-color:#000000'><a href='functions/f_block_content.php?id=$id&link=$travel_link' target=_blank style='text-decoration:none;color:#000000;font-weight:600;'><img src='nav/remove.png' border=0 align=top>&nbsp; Block Article</a></span></div>";
					echo "<br><br><b>$title</b><br>";
					echo "<i>$link</i><br>";	
					?>
					<!--- item start--->
					<?php
					echo "<br><br>$description<br><br>";
					?>
					<!--- item end--->
					<?php					
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
			}
			//if ($i==3)
			//{
			//exit;
			//}
		}//if blocked or not original
		else
		{			
			if ($row_count_2>0)
			{
				echo "Stats: BLOCKED.<br>";
			}
			else if ($nocontent==1)
			{
				echo "Stats: NO CONTENT<br>";
			}
			else
			{
				echo "Stats: ALREADY PUBLISHED.<br>";
			}
		}
		    
	}//foreach item

?>