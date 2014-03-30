<?php

	echo "<br><br>************************************************<br>$campaign_name : $campaign_source<br>************************************************<br>";
	
	//reset backdate array
	$backdate = array();
	
	//make arrays of regex if available
	if ($campaign_regex_search)
	{
	   $campaign_regex_search = explode('***r***',$campaign_regex_search);
	   $campaign_regex_replace = explode('***r***',$campaign_regex_replace);
	}
	//***done preparing additional variables
	
	if ($z_rss_scraping==3)
	{
		$campaign_feed = str_replace('http://', '', $campaign_feed);
		$campaign_feed = $blogsense_url."includes/fivefilters/makefulltextfeed.php?url={$campaign_feed}&max=25&links=preserve&submit=Create+Feed";
	}

	$string = quick_curl($campaign_feed,1);
	$string = htmlspecialchars_decode($string);
	

	if ($campaign_name=='text_import')
	{
		if ($campaign_source=="parent")
		{
			$source="";
			$folder = "parent";
		}
		else 
		{ 
			$folder = $source;
		}
		  
		  //echo $z_yahoo_option_sorting;exit;
		//open folder and discover files
		$links = files_in_directory("./my-articles/$campaign_source");
		$links = bs_simple_sort($links,$z_yahoo_option_sorting);
		
		//print_r($links);exit;
		$link_count = count($links);
		if ($campaign_limit_results>0&&($campaign_limit_results<$link_count))
		{
			$links = array_slice($links, 0 , $campaign_limit_results);
		}
	}
	else
	{
		//$lines = file("./my-csv-files/$source");
		$lines = csv_to_array("./my-csv-files/$campaign_source", $campaign_query);
		$cols = $lines[0];
		$col_count = count($cols);
		//print_r($lines);exit;
		$link_count = count($lines);
		if ($campaign_limit_results!=0&&($campaign_limit_results<$link_count))
		{
			$link_count = $campaign_limit_results;
		}
		
		for ($i=1;$i<$link_count;$i++)
		{
			$this_row = $lines[$i];
			//echo $this_row;exit;
			foreach ($cols as $a => $b)
			{
				$b = trim($b);
				$rows[$i][$b] = $this_row[$a];
			}
		}
			
		$links = $rows;
		echo $z_yahoo_option_sorting;
		$links = bs_simple_sort($links,$z_yahoo_option_sorting);
	}
	
	if (count($links)==0)
	{
		echo "Campaign produces no results. ";
		$links = array();
	}
	
	//if backdating on then prepare backdates
	if ($campaign_post_frequency=="backdate")
	{
		$count = count($links);
		$date_today = $date_placeholder =  $wordpress_date_time;
		for ($i = 0 ; $i<$count;$i++)
		{
			$p = $i - 1;
			if ($i!=0)
			{
					$backdate[] = date ('Y-m-d H:i:s', strtotime ("$backdate[$p] - 1 day"));
			}
			else
			{
					$backdate[] = date ('Y-m-d H:i:s', strtotime ("$date_today - 1 day"));
			}
		}
		   
	}
	
	foreach ($links as $key=>$value)
	{
		
		$title = "";
		$description = "";
		$link = "";
		$youtube_vids = "";
		$nutags = "";
		

		if ($campaign_name=='text_import')
		{
			if ($campaign_source=="parent")
			{
				 $open = fopen("./my-articles/$value", "r");
				 $string = fread($open, filesize("./my-articles/$value"));
				 fclose($open);	
			}
			else
			{ 
				 $open = fopen("./my-articles/$campaign_source/$value", "r");
				 $string = fread($open, filesize("./my-articles/$campaign_source/$value"));
				 fclose($open);
			}
			 
			if ($campaign_query=='filename')
			{
				$title = explode(".",$value);
				$title = $title[0];
				$content = $string;
			}
			else
			{
				$content = file("./my-articles/$campaign_source/$value");
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
			$title = str_replace('%title%',$title,$campaign_title_template);
			$description = str_replace('%postbody%',$content,$campaign_post_template);
			$link = $value;
			//echo $value;exit;
		}
		else
		{
			//echo $campaign_title_template;exit;
			$title = $campaign_title_template;
			$description = $campaign_post_template;
			foreach ($cols as $k=>$v)
			{
				$v = trim($v);
				//print_r($rows);
				$description = str_replace("%{$v}%", $rows[$key][$v], $description);
				$title = str_replace("%{$v}%", $rows[$key][$v], $title);
			}
			
			$format_title = sanitize_title_with_dashes( $title );
			$value = $format_title;
			//echo $value;exit;
		}
		
		$query ="SELECT original_source from ".$table_prefix."posts WHERE original_source='$value' AND post_status!='trash'";
		$result= mysql_query($query);
		if (!$result) { echo $query; echo 1;exit; }
		$row_count_1 = mysql_num_rows($result);
		if ($row_count_1>0&&$campaign_post_overwrite==1){$row_count_1=0;}
		
		$query ="SELECT * from ".$table_prefix."blocked_urls WHERE url='$value'";
		$result= mysql_query($query);
		if (!$result) { echo $query; echo 1;exit; }
		$row_count_2 = mysql_num_rows($result);
		
		//echo $title;exit;
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
			if ($campaign_strip_links==1)
			{
				$description = strip_tags($description,'<ul><li><ol><pre><img><div><table><tr><td><i><b><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><date><font><li><ul><object><embed><br><small><label><br/>');
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
				$description = strip_tags($description,'<ul><li><ol><pre><div><table><tr><td><i><b><a><p><span><u><font><tbody><h1><h2><h3><h4><center><blockquote><date><font><li><ul><embed><object><br><small><label><br/>');
			}				
			
			//echo $title; exit;
			if ($campaign_post_frequency!="all"&&$campaign_post_frequency!="backdate"&&$campaign_post_frequency!='feed_date')
			{
				//echo 1; exit;
				$post_frequency = explode(".",$campaign_post_frequency);
				$freq_day = $post_frequency[1];
				$freq_day_limit = $post_frequency[0];
				$freq_day_count = $campaign_post_count;
				if ($freq_day_count==0){$freq_day_count++;}
				$date_today = $server_date_time;
				$date_placeholder =   $campaign_post_date;
				
				if ($date_placeholder<$date_today)
				{
					$date_placeholder = $date_today;
				}
			
				//echo $freq_day; exit;
				if ($date_placeholder=='0000-00-00 00:00:00') 
				{ 
					$date_placeholder = $wordpress_date_time;
					$campaign_post_date = $date_placeholder;
					$freq_day_count++;
					$campaign_post_count++;					
				}
				else 
				{ 
					if ($freq_day==1)
					{
						
						if ($freq_day_count >= $freq_day_limit)
						{
							
							$date_placeholder = date ('Y-m-d H:i:s', strtotime ("$date_placeholder + 1 day"));
							$campaign_post_date = $date_placeholder;
							$campaign_post_count=0;
							//echo a; 
							//echo $date_placeholder;
							//exit;
						}
						else
						{
						  //echo $freq_day_count;
						  //echo $freq_day_limit;
						  $campaign_post_count++;	
						  //echo 2;
						  // echo $freq_day_count;exit;
						}
					}
					else 
					{
						//echo "why"; exit;
						$date_placeholder = date ('Y-m-d H:i:s', strtotime ("$date_placeholder + $freq_day day"));
						$campaign_post_date = $date_placeholder;
					}
				}	
			}
			else if ($campaign_post_frequency=='backdate')
			{
				$date_placeholder =   $campaign_post_date;
				if ($date_placeholder=='0000-00-00 00:00:00') 
				{ 
					$date_placeholder = $backdate[$k];				
				}
				else 
				{ 
					$date_placeholder = $campaign_post_date;
					$date_placeholder = date ('Y-m-d H:i:s', strtotime ("$date_placeholder - 1 day"));
					$campaign_post_date = $date_placeholder;
				}
			}
			else
			{
				//echo 1; exit;
				$date_placeholder =  $wordpress_date_time;
				$campaign_post_date = $date_placeholder;
			}
			
			if (strstr($description,'<date>'))
			{
				$date_placeholder = get_string_between($description, '<date>','</date>');
				$description = str_replace("<date>$date_placeholder</date>",'' ,$description);
			}
			
			//echo $tags_nature; exit;
			//determine tags
			if ($post_tags==1)
			{
				$tags = explode(" ",$title);
				//echo $title;
				$tags = prepare_tags($tags,$description,$tags_nature,$tags_custom,$tags_min,$tags_max);
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
				
				//run regex search and replace
				if ($campaign_regex_search)
				{		
					$description = preg_replace($campaign_regex_search,$campaign_regex_replace, $description);			  
					$title = preg_replace($campaign_regex_search,$campaign_regex_replace, $title);							
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
					
					$title = special_htmlentities($title);
					$description = special_htmlentities($description);
				}	
			
				//populate post template
				$description_content = hook_content($description,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$description = $description_content;
				
				$title_content = hook_content($title,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
				$title = $title_content;

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
			
				
				if (!$title)
				{
					echo "no title got throught on $key => $link";
					exit;
				}
				
				if (strstr($description,'<date'))
				{
					$description = preg_replace('/\<date(.*)date\>/si','',$description);
					//echo $description;exit;
				}
				if (strlen($description)>10) 
				{
					
					$author_info = get_userdata($campaign_author);
					$author_name = $author_info->display_name;
					$description = str_replace('%author_name%',$author_name, $description);
					$description = remove_empty_images($description);
					$description = apply_filters('the_content',$description);
					
					echo "<br><br><u>$title</u><br>";
					echo "<i>$link</i><br>";	
					echo "<br><br>$description<br><br>";
					echo "<br><br><hr>";
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
	}
?>