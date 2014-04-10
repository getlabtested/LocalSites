<?php
include_once('../../wp-config.php');
session_start();
include_once('../../wp-admin/includes/class-pclzip.php');

//check for multisite
//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}



	$m = $_POST['open_module'];
	$blog_url = $_POST['blog_url'];
	$blog_title = $_POST['blog_title'];
	$blog_subtitle = $_POST['blog_subtitle'];
	$contact_email = $_POST['contact_email'];
	$default_author = $_POST['default_author'];
	$plugin_loading = $_POST['plugin_loading'];
	$theme_loading = $_POST['theme_loading'];
	$selected_theme = $_POST['selected_theme'];

	$default_category =  $_POST['category_default'];
	$category_ids = $_POST['category_id'];
	$category_parents = $_POST['category_parent'];
	$category_names = $_POST['category_name'];
	$category_slugs = $_POST['category_slug'];
	
	$blogsense_activation_key = $_POST['blogsense_activation_key'];
	$blogsense_activation_email = $_POST['blogsense_activation_email'];
	
	
	//update activation key and email
	$ch = curl_init();
	$wordpress_url = get_bloginfo('url');
	$query = "http://www.hatnohat.com/api/blogsense/validate.php?key=$blogsense_activation_key&email=$blogsense_activation_email";
	curl_setopt($ch, CURLOPT_URL, $query);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('url' => $wordpress_url));	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close ($ch);		
	
	//echo $blogsense_activation_key;
	//echo "<br>";
	//echo $blogsense_activation_email;exit;
	if ($output!=1)
	{
		//$query =  "UPDATE ".$table_prefix."blogsense SET option_value='0' WHERE option_name='blogsense_activation'";
		//$result = mysql_query($query);
		//if (!$result) {echo $query; exit;}
		
		header ("Location: ../index.php?p=2&activate=1&m=$m");
		exit;
	}
	else
	{
		$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$blogsense_activation_key' WHERE option_name='blogsense_activation_key'";
		$result = mysql_query($query);
		if (!$result) {echo $query; exit;}

		$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$blogsense_activation_email' WHERE option_name='blogsense_activation_email'";
		$result = mysql_query($query);
		if (!$result) {echo $query; exit;}
	}
	
	//update blogsense settings
	$query =  "UPDATE ".$table_prefix."options SET option_value='$blog_url' WHERE option_name='siteurl'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."options SET option_value='$blog_title' WHERE option_name='blogname'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."options SET option_value='$blog_subtitle' WHERE option_name='blogdescription'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	$query =  "UPDATE ".$table_prefix."options SET option_value='$contact_email' WHERE option_name='admin_email'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}	
	$query =  "UPDATE ".$table_prefix."options SET option_value='$default_category' WHERE option_name='default_category'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	
	
	//$query =  "UPDATE ".$table_prefix."options SET option_value='$permalink_settings' WHERE option_name='permalink_structure'";
	//$result = mysql_query($query);
	//if (!$result) {echo $query; exit;}
	
	$query =  "UPDATE ".$table_prefix."blogsense SET option_value='$default_author' WHERE option_name='blogsense_default_author'";
	$result = mysql_query($query);
	if (!$result) {echo $query; exit;}
	
	//updates categories
	$query = "SELECT t.name, t.slug, t.term_id, tt.term_taxonomy_id  FROM ".$table_prefix."term_taxonomy tt,  ".$table_prefix."terms t WHERE tt.term_taxonomy_id = t.term_id AND tt.taxonomy='category'";
	$result = mysql_query($query);
	if (!$result){ echo $query; exit; }



	//modify .htaccess for permalinks
	//$update_htaccess =  change_permalink_structure();
	//if (!$update_htaccess)
	//{
	//echo "problem rewriting .htaccess for permalink restructuring. ";
	//exit;
	//}

	
	header ("Location: ../index.php?p=2&success=1&m=$m");



	

	//helper functions
	function str_replace_once($remove , $replace , $string)
	{
		// Looks for the first occurence of $needle in $haystack
		// and replaces it with $replace.
		$pos = strpos($string, $remove);
		if ($pos === false) 
		{
		// Nothing found
		return $haystack;
		}
		return substr_replace($string, $replace, $pos, strlen($remove));
	} 

	function change_permalink_structure()
	{    
		$htaccess = "../../.htaccess";
		$open = @fopen($htaccess, 'w'); 
		
		$slash_count = substr_count ($blog_url, "/");
		
		$base = substr($blog_url, -5);
		if($slash_count==3)
		{
		  $base_1 = "/";
		  $base_2 = "/index.php [L]";
		}
		else
		{
		  $parts = explode("/",$blog_url);
		  $base_1 = "/".$parts[3];
		  foreach ($parts as $key=>$value)
		  {
			if ($key>3)
			{
			$base_1 = $base_1."/".$value;
			
			}		
		  }
		  // echo $base_1;  echo $value; exit;
		  $base_2 = $base_1."/index.php";
		}

		$string = "	# BEGIN WordPress
						<IfModule mod_rewrite.c>
						RewriteEngine On
						RewriteBase $base_1
						RewriteCond %{REQUEST_FILENAME} !-f
						RewriteCond %{REQUEST_FILENAME} !-d
						RewriteRule . $base_2
						</IfModule>
						# END WordPress
					";


		if ($open) 
		{ 
			fwrite($open, $string);
			fclose($open);
			return 1;
		}
		else
		{
		   echo "no open";
		   return 2;
		}

	}



?>