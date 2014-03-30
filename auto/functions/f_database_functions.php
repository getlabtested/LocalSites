<?php
require_once('./../../wp-config.php');
session_start();
require_once ('./../../wp-blog-header.php');
require_once ('./../../wp-includes/registration.php');
require_once ('./../../wp-admin/includes/taxonomy.php');
require_once ('./../includes/prepare_variables.php');

include("f_login.php");
if(checkSession() == false){
blogsense_redirect("login.php");
}

$mode = $_GET['mode'];
$nature = $_POST['nature'];

$select_html = "";
foreach($categories as $k=>$v)
{	
	if (!$select_html)
	{
		$select_html = "<option value='$cat_ids[$k]' $selected>$v</option>";
	}
	else
	{
		$select_html .= "<option value='$cat_ids[$k]' $selected>$v</option>";
	}				
}

if ($mode=='search_replace')
{
	if ($nature=='search_replace')
	{
		$search = stripslashes($_POST['search']);
		$replace = stripslashes($_POST['replace']);
		$search = addslashes($search);
		$replace = addslashes($replace);
		
		$query = "SELECT * FROM ".$table_prefix."posts ";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error(); }
		$total_count = mysql_num_rows($result);
		$post_count = 0;
		
		$query = "UPDATE ".$table_prefix."posts SET post_content = replace(post_content,'$search','$replace') WHERE post_status='publish' OR post_status='future'";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error(); }
		$post_count = mysql_affected_rows(); 
		//exit;
		
		echo "<center><br><br><br><br><font color=green>$post_count Posts Affected. (Out of ".$total_count.")</center></font>";
		exit;
	}
	else
	{
		?>
			<form action='' method=POST >
			<input type=hidden name='nature' value='search_replace' id='id_form_nature'>
			
			<div style="font-size:14px;width:400;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">
				Mass Search & Replace
				<div style='float:right'>
					<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Use this tool to do a mass search and replace on all posts within your wordpress database. You may want to backup your database before executing.">
				</div>
			</div>
			<hr width=400 style="color:#eeeeee;background-color:#eeeeee;">
			 
			
			<table id=id_search_replace width=400 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee"> 
				<tr>						
					<td align=left  style='font-size:13px;' valign=top >
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Text to search out for replacement.">
						<i>Search String:</i>
					</td>
					<td align=left>
						<textarea class='class_inputs'  rows=10 cols=25 name=search></textarea>
					</td>
				</tr>
				<tr>						
					<td align=left  style='font-size:13px;' valign=top >
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Text to replace search text with.">
						<i>Replace String:</i>
					</td>
					<td align=left>
						<textarea class='class_inputs' rows=10 cols=25 name=replace></textarea>
					</td>
				</tr>
				<tr>
					<td align=center colspan=2 style='font-size:13px;' valign=top >
						<br><br>
						<button class='class_inputs' id='id_submit' onClick="return confirm('Are you sure you want to do this? ')" value='Run Replacement'><span>Run Replacement!</span></button>
					</td>
				</tr>
			</table>
			</form>
			
			
		<?php
	}
	
}

if ($mode=='delete_duplicate')
{
	$query = "SELECT ID,post_title from {$table_prefix}posts WHERE ( post_status='publish' AND post_type='post' ) OR ( post_status='future' AND post_type='post' )";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); }
	
	$deleted=0;
	while ($array = mysql_fetch_array($result))
	{
		//echo 1;
		$id = $array['ID'];
		$title = addslashes($array['post_title']);
		
		$query2 = "SELECT ID FROM {$table_prefix}posts WHERE post_title = '$title'";
		$result2 = mysql_query($query2);
		$count = mysql_num_rows($result2);
		//echo 1;exit;
		if ($count>1)
		{
		
			//echo 1; exit;
			$i = 0;
			while ($arr=mysql_fetch_array($result2))
			{
				if ($i != 0)
				{
					//echo 1; exit;
					$this_id = $arr['ID'];
					$q3 = "DELETE FROM {$table_prefix}posts WHERE ID='$this_id'";
					$r3 = mysql_query($q3);
					$deleted++;
				}
				$i++;
			}
		}
	}
	
	echo "<center><br><br><br><br><font color=green>Congratulations $deleted duplicate posts have been deleted. </center></font>";
}


if ($mode=='mass_edit_data')
{
	if ($nature=='mass_edit_data')
	{
		$post_ids = $_POST['post_ids'];
		$post_titles = $_POST['post_titles'];
		$post_tags = $_POST['post_tags'];
	   
		
		foreach ($post_tags as $key=>$val)
		{
			
			$tags = trim($post_tags[$key]);
		}
		
		foreach ($post_ids as $key=>$val)
		{
			$post = array();
			$post['ID'] = $val;
			$post['post_title'] = $post_titles[$key];
			$post['post_name'] = sanitize_title_with_dashes( $post_titles[$key] );
			wp_update_post( $post );
			
			$tags = explode(',' ,$post_tags[$key]);
			foreach ($tags as $k=>$v)			
			{
				$tags[$k] = trim($v);
			}
			//delete tags associated to post id
			$query = "SELECT * FROM ".$table_prefix."term_relationships tr JOIN ".$table_prefix."term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id WHERE tr.object_id='$val' AND tt.taxonomy='post_tag'";
			$result = mysql_query($query);
			if (!$result){echo $query; echo mysql_error();}
			
			while ($arr = mysql_fetch_array($result))
			{
				$tid = $arr['term_taxonomy_id'];
				$query2 = "DELETE FROM ".$table_prefix."term_relationships WHERE term_taxonomy_id='$tid'";
				$result2 = mysql_query($query2);
				if (!$result2){echo $query2; echo mysql_error();}
			}
			
			
			//add tags to post id			
			wp_add_post_tags($val,$tags);
		}
		
		//exit;
		
		echo "<center><br><br><br><br><font color=green> Posts updated.</center></font>";
		exit;
	}
	else
	{
		
		if ($_POST['nature']=='reload')
		{
			$category_term_id = $_POST['category'];
			
			//get term id from tax id
			$query = "SELECT term_id FROM ".$table_prefix."term_taxonomy WHERE term_taxonomy_id='$category_term_id'";
			$result = mysql_query($query);
			if (!$result){echo $query; echo mysql_error();}
			$arr = mysql_fetch_array($result);
			$category = $arr['term_id'];
			
			$posts = get_posts("numberposts=1000&category=$category");
			//print_r($posts);exit;
			
			$select_html = "";
			foreach($categories as $k=>$v)
			{	
				
				if ($category==$cat_ids[$k])
				{
					$selected = "selected=true";
				}
				else
				{
				   $selected = "";
				}
				
				if (!$select_html)
				{
					$select_html = "<option value='$cat_ids[$k]' $selected>$v</option>";
				}
				else
				{
					$select_html .= "<option value='$cat_ids[$k]' $selected>$v</option>";
				}				
			}

		}
		else
		{		
			//prepare latest 1000 posts
			$posts = get_posts("numberposts=500&order=ASC");
			//print_r($posts);exit;
			//echo count($posts);exit;
		}
		
		
		
		$post_titles = "";
		$post_tags = "";
		$post_urls = "";
		//echo count($posts);
		//print_r($posts[1]);
		foreach ($posts as $key=>$val)
		{
			
			$tag_names = "";
			$post_ids[$key] = $val->ID;
			$pid = $val->ID;
			//echo get_permalink($pid);
			$post_titles[$key] = $val->post_title;
			$post_urls[$key] = get_permalink($pid);
			$tags = get_the_tags($pid);
			
			if ($tags)
			{
				foreach($tags as $k=>$v)
				{
					//print_r($val);exit;
					$tag_names[] = $v->name;
				}
				$post_tags[$key] = implode(' , ',$tag_names);
			}
			
		}
		
		?>
			<html>
			<head>

			<script type="text/javascript" src="./../includes/jquery.js"></script>
			<script type="text/javascript"> 
			$(document).ready(function() 
			{
				
				
				 $("#id_button_filter").click(function(){
					$('#id_form_nature').val('reload');
					$("#id_form_mass_edit_data").submit();
				
				 });
	
				$("#id_button_save").click(function(){
					$('#id_form_nature').val('mass_edit_data');
					$("#id_form_mass_edit_data").submit();
				 });
				 
			
			});
			</script>
			</head>
			<body style="font-family:Khmer UI;">
			<form action='' method=POST id='id_form_mass_edit_data' >
			<input type=hidden name='nature' value='mass_edit_data' id='id_form_nature'>
			<input type=hidden name='category' value='all' id='id_form_category'>
			
			<div style="font-size:14px;width:900;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">
				Mass Edit Post Titles, Categories, & Tags
				<div style='float:right'>
					<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="No tip just yet.">
				</div>
			</div>
			<hr width=900 style="color:#eeeeee;background-color:#eeeeee;">
			 
			
			<table id=id_search_replace width=900 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee"> 
			<tr>
				<td  align=left valign=top style='font-size:13px;'>
					<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="All posts from this feel will go into this category. If you only want select items, then use the include and exclude keywords to target your items.">
					Select Category:<br> 
				</td>
				<td  align=right style='font-size:13px;'>
					<select id=id_category_selects name='category' style="width:200px;">			 
						<?php echo $select_html; ?> 
					</select> <img src='./../nav/btn_filter.png' style='margin-bottom:-3px;cursor:pointer;' id='id_button_filter' >
				</td>
			</tr>
			</table>
			<?php
			if (count($posts)==0)
			{
				echo "<br><br><center><i>no posts found</i></center>";
			}
			else
			{
			?>
				<br>
				<table id=id_search_replace width=900 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee"> 
					<?php
					if (count($posts)>500)
					{
						?>						
						<tr>
							<td colspan=3 align=center valign=top style='font-size:13px;padding:20px;'>
								<button  id=id_button_save><span>Save Data</span></button>				
							</td>
						</tr>						
						<?php
					}
					?>
					<tr>
						<td  align=left valign=top style='font-size:13px;'>
							
						</td>
						<td  align=middle valign=top style='font-size:13px;'>
							<i>Post Title</i>
						</td>
						<td  align=middle style='font-size:13px;'>
							<i>Post Tags</i>
						</td>
					</tr>
					<?php
					foreach ($post_titles as $key=>$val)
					{
					?>
						<tr>
							<td  align=left valign=top style='font-size:13px;'>
								<a href="<?php echo $post_urls[$key]; ?>" target='_blank'><img src='./../nav/link.gif' border=0></a>
								<input type='hidden' name="post_ids[]"  value="<?php echo $post_ids[$key]; ?>">
							</td>
							<td  align=left valign=top style='font-size:13px;'>
								<input name="post_titles[]" size="68" value="<?php echo $post_titles[$key]; ?>">
							</td>
							<td  align=right style='font-size:13px;'>
								<input name="post_tags[]" size="69" value="<?php echo $post_tags[$key]; ?>">
							</td>
						</tr>
					<?php
					}
					?>
				<tr>
				 <td colspan=3 align=center valign=top style='font-size:13px;padding:20px;'>
				   <button  id=id_button_save><span>Save Data</span></button>				
				 </td>
				</tr>
				</table>
			
		<?php
			}//end if have posts
	}//end display form
}

if ($mode=='create_categories')
{
	if ($nature=='create_categories')
	{
		$categories = $_POST['categories'];
		$categories =  preg_split("/[\r\n,]+/", $categories, -1, PREG_SPLIT_NO_EMPTY);
		
		foreach ($categories as $k=>$v)
		{
			wp_create_category( $v, 0 );
		}
		
		$count = count($categories);
		
		echo "<center><br><br><br><br><font color=green>$count Categories Created. </center></font>";
		exit;
	}
	else
	{
		?>
			<form action='' method=POST >
			<input type=hidden name='nature' value='create_categories' id='id_form_nature'>
			
			<div style="font-size:14px;width:400;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">
				Create Categories From Keyword List
				<div style='float:right'>
					<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Use this tool to easily create many categories at once.">
				</div>
			</div>
			<hr width=400 style="color:#eeeeee;background-color:#eeeeee;">
			 
			
			<table id=id_search_replace width=400 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee"> 
				<tr>						
					<td align=left  style='font-size:13px;' valign=top >
						<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="One per line please.">
						<i>Categories:</i>
					</td>
					<td align=left>
						<textarea class='class_inputs'  rows=10 cols=25 name=categories></textarea>
					</td>
				</tr>
				<tr>
					<td align=center colspan=2 style='font-size:13px;' valign=top >
						<br><br>
						<button class='class_inputs' id='id_submit' onClick="return confirm('Are you sure you want to do this? ')" value='Run Replacement'><span>Create Categories!</span></button>
					</td>
				</tr>
			</table>
			</form>
			
			
		<?php
	}
	
}
?>