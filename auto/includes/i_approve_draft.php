<?php
include_once('./../../wp-config.php');
require_once ('./../../wp-blog-header.php');
require_once ('./../../wp-includes/registration.php');

if (!isset($_SESSION)) { session_start();}

$bid = $_GET['blog_id'];
$post_id = $_GET['post_id'];
$nature = $_GET['nature'];
if (function_exists('switch_to_blog')) switch_to_blog($bid);


if ($nature=='approve')
{
	$query = "UPDATE {$table_prefix}posts SET post_status='publish' WHERE ID = '$post_id' AND post_status='draft'";
	$result = mysql_query($query);
	if (!$result){ echo $query; echo mysql_error(); } 
	else
	{
		$url = get_permalink ($post_id);
		?>
		<html>
		<body>
		<font color=green>Post Approved!:  <br><?php echo "<a href='$url'>$url</a>"; ?></font>
		</body>
		</html>
		<?php
	}
	
}

if ($nature=='trash')
{
	$query = "UPDATE {$table_prefix}posts SET post_status='trash' WHERE ID = '$post_id' AND post_status='draft'";
	$result = mysql_query($query);
	if (!$result){ echo $query; echo mysql_error(); } 
	else
	{
		$url = get_permalink ($post_id);
		?>
		<html>
		<body>
		<font color=red>Post Trashed!: <br><?php echo "<a href='$url'>$url</a>"; ?></font>
		</body>
		</html>
		<?php
	}
}
?>