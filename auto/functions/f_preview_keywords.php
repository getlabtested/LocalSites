<?php

echo "<br><br>******************keywords campaign*********************<br>$campaign_name : $campaign_feed<br>************************************************<br>";
	


	
	$keywords = explode(';',$campaign_query);

	
    foreach ($keywords as $key=>$value)
	{		
		$this_title_template  = 	$campaign_title_template;
		$this_title_template = str_replace("%keyword%",$value,$this_title_template);
		$this_post_template  = 	$campaign_post_template;
		$this_post_template = str_replace("%keyword%",$value,$this_post_template);
	
		$title_content = hook_content($this_title_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
		$title = $title_content;
						
		//populate post template
		$description_content = hook_content($this_post_template,$table_prefix, $store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$templates_custom_variable_token,$templates_custom_variable_content);	
		$description = $description_content;

		$link = $title;
		//prepare post_name for url perma links
		//$permalink_name = string_url_prepare($title);						
		//$c_title = addslashes($title);
		//first make sure the entry isnt already in the database
		$thislink = addslashes($link);
		$query ="SELECT original_source from ".$table_prefix.$_SESSION['second_prefix']."posts WHERE original_source='$thislink' AND post_status!='trash'";
		$result= mysql_query($query);
		if (!$result) { echo $query; echo 1;exit; }
		$row_count_1 = mysql_num_rows($result);
		
		$query ="SELECT * from ".$table_prefix.$_SESSION['second_prefix']."blocked_urls WHERE url='$link'";
		$result= mysql_query($query);
		if (!$result) { echo $query; echo 1;exit; }
		$row_count_2 = mysql_num_rows($result);
		//echo $key;
		//procede if original				
		if ($row_count_1==0&&$row_count_2==0&&$title&&$campaign_post_overwrite!=1)
		{
			
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
			

			$description = clean_html($description);
			
			
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

		

			//translate				
			if ($campaign_language!="no translation")
			{

				$title = spin_text($title, $link, $title, $campaign_image_floating,$campaign_language);
				$description = spin_text($description, $link, $title, $campaign_image_floating,$campaign_language);
			}	

			
			
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
			
			//echo strlen($description);
			
			if (!$description||strstr($description,'Sorry, Readability was unable to parse this page for content.')||strlen($description)<15)
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
				$description = special_htmlentities($description);
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
				else if ($stop==1)
				{
					echo "<b>$title</b><br>";
					echo "Stats: EXCLUDED: DESCRIPTION IS LESS THAT 25 CHARACTERS.<br>";
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