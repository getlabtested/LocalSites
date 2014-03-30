<?php
include_once('../wp-config.php');
session_start();
echo "<html>";
include_once('includes/helper_functions.php');
$this_blog_id = $_GET['blog_id'];
if ($this_blog_id)
{
	$_COOKIE['bs_blog_id'] = $this_blog_id;
	if (function_exists('switch_to_blog')) switch_to_blog($this_blog_id);
	//echo $table_prefix;exit;
}
include_once('includes/prepare_variables.php');

$secret_key = $blogsense_api_secret_key;

if ($_GET['secret_key']==$secret_key)
{
	//echo 1; exit;
	
	$link = $_GET['link'];
	$credit_source = $_GET['credit_source'];
	$strip_links = $_GET['strip_links'];
	$store_images = $_GET['store_images'];
	$language = $_GET['language'];
	$cloak_links = $_GET['cloak_links'];
	$post_content = $_GET['post_content'];
	$post_title = $_GET['post_title'];
	$add_days = $_GET['add_days'];
	$post_date = $_GET['post_date'];
	$post_status = $_GET['post_status'];
	$post_type = $_GET['post_type'];
	$tags = $_GET['post_tags'];
	$category_id = $_GET['cat_id'];
	$spin_text = $_GET['spin_text'];
	$category_id  = explode(',', $category_id);
	$category_id  = array_filter($category_id);
	
	$post_status = $_GET['post_status'];
	$manual_mode = $_GET['manual_mode'];
	
	if ($_POST['internal']==1)
	{
		$post_status =  $_POST['post_status'];
		$post_type =  $_POST['post_type'];
		$post_date =  $_POST['post_date'];
		if ($_GET['cat_id'])
		{
			$category_id = $_POST['cat_id'];
			$category_id  = explode(',', $category_id);
			$category_id  = array_filter($category_id);
		}
		
		$post_title = stripslashes($_POST['post_title']);
		$post_content = stripslashes($_POST['post_content']);
		$link = urldecode($_POST['link']);
		$post_author = $_POST['author_id'];
		
		if (!$tags)
		{
			$tags = $_POST['post_tags'];
		}

	}
	
	$travel_link = urlencode($link);
	
	$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
	$current_url = explode("blogsense_api.php",$current_url); 
	$current_url = $current_url[0];

	if (!$post_content)
	{
		$ff_url = $current_url."includes/fivefilters/makefulltextfeed.php?url=$travel_link";
		$string = quick_curl($ff_url,0);
		$string = explode('<item>',$string);
		$string = $string[1];
	
		//echo $string; exit;
		$title = get_string_between($string, '<title>','</title>');
		$description = get_string_between($string, '<description>','</description>');
		$description = htmlspecialchars_decode($description);
	}
	else
	{
		$title = urldecode($post_title);
		$description = urldecode($post_content);
	}
	
	//echo $description; exit;
	
	if ($post_content)
	{
		$description = $post_content;
	}
	if ($post_title)
	{
		$title = $post_title;
	}
	
	if (!$post_date)
	{
		$date_placeholder = $wordpress_date_time;
		if ($add_days)
		{
			//echo 1; exit;
			$date_placeholder = date('Y-m-d H:i:s', strtotime($date_placeholder . " " . $add_days . " day"));
			$gmt_date = gmdate('Y-m-d H:i:s', strtotime($date_placeholder));
			
		}
		else
		{
			$gmt_date = gmdate('Y-m-d H:i:s', strtotime($date_placeholder));
		}
	}
	else
	{
		$date_placeholder = $post_date;
		if ($add_days)
		{
			$date_placeholder = date('Y-m-d H:i:s', strtotime($date_placeholder . " " . $add_days . " day"));
			$gmt_date = gmdate('Y-m-d H:i:s', strtotime($date_placeholder));
			
		}
		else
		{
			$gmt_date = gmdate('Y-m-d H:i:s', strtotime($date_placeholder));
		}
		
	}
	
	
	if (!$post_author)
	{
		$post_author= $default_author;
	}
	
	if ($strip_links==1)
	{
		$description = strip_tags($description,'<pre><embed><center><img><div><table><tr><td><i><b><iframe><p><span><u><font><tbody><h1><h2><h3><h4><blockquote><font><li><ul><object><br><small><label><br/>');
	}
	
	if ($credit_source==1)
	{
		$description = "$description<br><br><a href='$link' target='_blank'>$link</a>";
	}
	
	//cloak links 
	if ($cloak_links==1)
	{						   
	   $description = cloak_links($description, $table_prefix);
	   //echo "$description";exit;
	}
	
	//if local image storing is on then do magic
	if ($store_images==1)
	{
		$description = store_images($description,$store_images_relative_path, $store_images_full_url, $title, $link, $blog_url);
	}
	
	if ($spin_text==1)
	{
		$title = spin_text($title, $link, $title, NULL,"spin");
		$description = spin_text($description, $link, $title, NULL,"spin");
		
		$title = special_htmlentities($title);
		$description = special_htmlentities($description);
	}
	
	if ($spin_text==2)
	{
		$title = spyntax($title);
		$description = spyntax($description);
		
		$title = special_htmlentities($title);
		$description = special_htmlentities($description);
	}
	
	if ($language)
	{
		//echo $language;exit;
		$title = spin_text($title, $link, $title, NULL,$language);
		$description = spin_text($description, $link, $title, NULL,$language);
		
		$title = special_htmlentities($title);
		$description = special_htmlentities($description);
	}
	
	if (strstr($title,'Unable to perform Translation:')||strstr($description,'Unable to perform Translation:'))
	{
		$title = "";
		$description = "";
		$translation_fail=1;
	}
	
	
	if (!$post_status)
	{
		$post_status='publish';
	}
	
	$cat = $category_id;
	
	foreach ($cat as $k=>$v)
	{
		IF (!is_numeric($v))
		{
			$category = get_category_by_slug($v);
			
			if ($category->term_id<1)
			{
				$cat[$k] =  wp_insert_term($v, "category");
				$category = get_category_by_slug($v);
				$cat[$k] = $category->term_id;
			}
			else
			{
			
				$cat[$k] = $category->term_id;
			}
		}
	}
	
	//get tags
	if ($description)
	{
		if (!$tags)
		{
			//prepare tags
			$url = "http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction";
			$paramaters = array('appid'=>'ZAlzNRjV34H56QbVJk7fRvu_yAP8bYHxG9Q77nNjaDsj9aelNCiTlo2bGiO_m2do1ic-', 'context'=>$description );
			$nature = 'yahoo_tags';
			$result = stealth_curl($url, $proxies, $paramaters , $nature);
			//echo $result; exit;
			while (strstr($result,'<Result>'))
			{
				$tags[] = get_string_between($result, '<Result>','</Result>');
				$result = str_replace_once('<Result','',$result);
			}
			
			if ($tags)
			{
				$array = array_unique($tags);
				usort($array ,'specialsort');
				$array = array_filter($array);
				$tags = array_slice($array, 0 , 5);
			}
			$tags = implode(',',$tags);
		}
	}
	
	$query ="SELECT original_source from ".$table_prefix."posts WHERE original_source='$link'";
	$result= mysql_query($query);
	if (!$result) { echo $query; echo mysql_error(); exit; }
	$row_count = mysql_num_rows($result);
	
	if ($title&&$description&&$cat&&$link&&$row_count<1)
	{
		if ($_GET['manual_mode']!=1)
		{
			$permalink_name = sanitize_title_with_dashes($title);
			//$title =  addslashes($title);
			//$description = addslashes($description);
			
			//echo 1; exit;
			$post = array(		
			  'post_author' => $default_author, 
			  'post_category' => $cat, 
			  'post_content' => $description, 
			  'post_date' => $date_placeholder,
			  'post_date_gmt' => $gmt_date,
			  'post_name' => $permalink_name,
			  'post_status' => $post_status, 
			  'post_title' => $title,
			  'post_type' => $post_type,
			  'tags_input' => "$tags",
			  'original_source'=> $link
			);  
			
			$post_id = wp_insert_post( $post, $wp_error );
			$permalink = get_permalink( $post_id );
			
			//store source as saved				
			$query = "UPDATE ".$table_prefix."posts SET original_source='$link' WHERE ID='$post_id'";
			$result = mysql_query($query);					
			if (!$result){echo $query; echo mysql_error(); exit;}
			
			
			echo "<body>";
			echo "Success!";	
			echo "<br><br><u>$title</u><br>";
			echo "<i>$link</i><br>";	
			echo "<br><br>$description<br><br>";
			echo "<br><br><hr>";
			echo "<body>";
			echo "<permalink>$permalink</permalink>";
			exit;
		}
		else
		{
			?>
			<head>
			<title>API Remote Publishing</title>
			<link href="./includes/pagination/pagination.css" media="screen" rel="stylesheet" type="text/css"/>
			<link rel="stylesheet" type="text/css" href="./includes/openwysiwyg/docs/style.css">
			<script type="text/javascript" src="./includes/jquery.js"></script>
			<script type="text/javascript" src="./includes/openwysiwyg/scripts/wysiwyg.js"></script>
			<script type="text/javascript" src="./includes/openwysiwyg/scripts/wysiwyg-settings.js"></script>
			
			

			<script type='text/css'>
			.body
			{
				background-color:#ffffff;
			}
			</script>
			
			<script type="text/javascript">
				 
				$(document).ready(function()
				{ 						
					WYSIWYG.attach('id_textarea', full); 
					
					$('.category_button_on').live("click" ,function(){
						//get page id
						var cat_id = this.id.replace('id_category_button_','');
						
						//get page inputs values
						var theinput = $('#id_category_input').val();
						
						//count commas
						var commas = theinput.split(/,/g).length - 1;
						//alert(commas);
						if (commas>0)
						{
							//remove ,cat_id from inputs value
							new_input = theinput.replace(','+cat_id,'');
						
							//replace old input value with new input value
							$('#id_category_input').val(new_input);
							//alert(new_input);
							
							//change class of item to off
							$('#id_category_button_'+cat_id).removeClass('category_button_on');
							$('#id_category_button_'+cat_id).addClass('category_button_off'); 
						//else abort
						}
					});
					$('.category_button_off').live("click" ,function(){
						//get page id
						var cat_id = this.id.replace('id_category_button_','');
						//alert(cat_id);
						
						//get page inputs values
						var theinput = $('#id_category_input').val();
						
						//add ,cat_id to inputs value
						var new_input = theinput + ","+cat_id;
						//alert(new_input);
						
						//replace old input value with new input value
						$('#id_category_input').val(new_input);
							
						//change class of item to off
						$('#id_category_button_'+cat_id).removeClass('category_button_off');
						$('#id_category_button_'+cat_id).addClass('category_button_on'); 
											
						
					});
				});
			</script>			
			</head>
			<body>
			<?php
			echo "<form name='form_REQUEST' action='' method='POST'>";	
			echo "<input type=hidden name='form_REQUEST' value='1'>";	
			echo "<input type=hidden name='post_status' value='{$post_status}'>";	
			echo "<input type=hidden name='link' value='{$link}'>";	
			echo "<h3>Title:</h3> ";
			echo "<input name=title style='width:98%;' value=\"$title\"><a href='$link' target=_blank>&nbsp;<img src='./nav/link.gif' border=0 title='Open Link in External Window'></a>";
			echo "<br><br>";
			echo "<div align=left width=700 class=class_cat_selects style='line-height:2;'>";
			echo "<h3>Categories: </h3>";
					
					foreach ($categories as $k=>$v)
					{		
						$j=$k+1;
						if ($j % 11 == 0)
						{
						echo "<br>";
						}
						
						if ($k==0)
						{
							echo "<span class='category_button_on' id='id_category_button_".$cat_ids[$k]."'>$v</span>";
						}
						else
						{
							echo "<span class='category_button_off' id='id_category_button_".$cat_ids[$k]."'>$v</span>";
						}
					}
			echo "</div><br>";		
			echo "<input type=hidden name=category value ='{$cat_ids[0]}' id='id_category_input'>";
			echo "<h3>Tags: </h3><input name=tags value ='$tags' style='width:100%;'><br><br>";
			
			echo "<h3>Post Status: </h3><select name='post_status'>";
			$post_status_array = array('publish','future','draft');
			foreach ($post_status_array as $k=>$v)
			{
				if ($v==$post_status)
				{
					$selected = 'slected="true"';
				}
				else
				{
					$selected = '';
				}
				echo "<option value='$v' $selected>$v</option>";
			}
			echo "</select><br><br>";
			
			echo "<textarea name='description' id='id_textarea' style=\"width:100%;height:400px;\">$description</textarea>";
			echo "<br>";
			echo "<input type=submit value='POST'>";
			echo "</form>";
			
		}
	}
	else
	{
		if ($translation_fail==1)
		{
			echo "Failed at detecting language for translation.";
			exit;
		}
		else if($row_count>0)
		{
			echo "Duplicate Post, ignore.";
			exit;
		}
		else
		{
			echo "Something's missing:<br><br>";
			echo "Title $title<br>";
			echo "Post Content:  $description<br>";
			echo "Category ID: ".$cat[0]."<br>";
			echo "Original Link: $link <br>";
			exit;
		}
	}
	
					
}
else
{
	echo "Access to API requires secret key. ";
}


?>
</body>
</html>