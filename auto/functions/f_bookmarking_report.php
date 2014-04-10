<?php
session_start();
include_once('./../../wp-config.php');
include_once('./../includes/prepare_variables.php');

include("./../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../login.php");

//check for multisite
if (function_exists('switch_to_blog')){
 switch_to_blog(1);
 switch_to_blog($_COOKIE['bs_blog_id']);
}

$mode = $_GET['mode'];
$today = date("Y-m-d", strtotime($wordpress_date_time));
$yesterday = date("Y-m-d", strtotime("$wordpress_date_time - 1 day"));
$tomarrow = date("Y-m-d", strtotime("$wordpress_date_time + 1 day"));
?>

<html>
<head>
<style type='text/css'>
table {
	font: 11px/24px Verdana, Arial, Helvetica, sans-serif;
	border-collapse: collapse;
	width: 100%;
}

th {
	padding: 0 0.5em;
	text-align: left;
	}

tr.yellow td {
	border-top: 1px solid #FB7A31;
	border-bottom: 1px solid #FB7A31;
	border-left: 1px solid #FB7A31;
	border-right: 1px solid #FB7A31;
	background: #FFC;
	}

td {
	border-bottom: 1px solid #CCC;
	border-left: 1px solid #CCC;
	border-right: 1px solid #CCC;
	padding: 0 0.5em;
	}

td:first-child {
	width: 190px;
	}

td+td {
	border-left: 1px solid #CCC;
	text-align: center;
	}
body
{
	background-color:#fff;
}
</style>
</head>

<body >
<img src="./../nav/tip.png" style="cursor:pointer;float:left;padding-right:4px;" border=0 title="Each log displays up to 1000 items.">					
<?php
if ($mode=='scheduled')
{
	$query  = "SELECT * FROM {$table_prefix}posts_to_bookmark WHERE status=0 ORDER BY date ASC LIMIT 1000";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();}
	$count = mysql_num_rows($result);
	
	while ($arr = mysql_fetch_array($result))
	{
		$post_id[] = $arr['post_id'];
		$this_date = date("Y-m-d", strtotime($arr['date']));
		if ($this_date==$today)
		{
			$date[] = "Today @ ".date("g:i a", strtotime($arr['date']));
		}
		else if ($this_date==$tomarrow)
		{
			$date[] = "Tomarrow @ ".date("g:i a", strtotime($arr['date']));
		}
		else
		{
			$date[] = date("F j, Y, g:i a", strtotime($arr['date']));
		}
		$permalink[] = $arr['permalink'];
		$nature[] = $arr['nature'];
		$account[] = $arr['account'];
		$content[] = $arr['content'];
	}
	

?>

	<center>
	<div style='text-align:left;width:100%;font-size:12px;'>
		Boomarking Logs: Scheduled Jobs
	</div>
	<div style='text-align:left;width:100%;font-size:12px;padding-top:3px;padding-bottom:3px''>
		<a href='f_bookmarking_report.php?mode=posted' target='_self'  style="text-decoration:none;color:grey;cursor:pointer;">Posted</a> &nbsp;&nbsp;|&nbsp;&nbsp;
		<i>Scheduled</i> 
		<br>
	</div>
	<table width='100%' class='bookmark_report'>
		<tr class=yellow>
			<td align='left' width='' style='font-size:10px;font-weight:300;width:137px;'>
				UPCOMMING
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				POSTID	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				NATURE
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				ACCOUNT	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;' width='368px'>
				CONTENT		
			</td>
		</tr>
		<?php
		if ($post_id)
		{		
			foreach ($post_id as $key=>$val)
			{
				$content_preview = str_replace("'",'',$content[$key]);
				$content_short = substr($content[$key],0, 50);
				?>
				<tr>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $date[$key]; ?>			
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<a href='<?php echo $permalink[$key]; ?>' target='_blank'><?php echo $post_id[$key]; ?></a>
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $nature[$key]; ?>
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $account[$key]; ?>
					</td>
					<td align='left' style='font-szie:11px;font-weight:300; cursor:pointer;' title='<?php echo $content_preview; ?>'>
						<?php 
							echo $content_short."..."; 
						?>	
					</td>
				</tr>
				
				<?php
			}
		}	
		?>
	<table>
	</center>
	<script>
	parent.resizeIframe(document.body.scrollHeight);
	</script>
<?php
}
?>

<?php
if ($mode=='posted')
{
	$query  = "SELECT * FROM {$table_prefix}posts_to_bookmark WHERE status=1 ORDER BY date DESC LIMIT 1000";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();}
	$count = mysql_num_rows($result);
	
	while ($arr = mysql_fetch_array($result))
	{
		$post_id[] = $arr['post_id'];
		$this_date = date("Y-m-d", strtotime($arr['date']));
		if ($this_date==$today)
		{
			$date[] = "Today @ ".date("g:i a", strtotime($arr['date']));
		}
		else if ($this_date==$yesterday)
		{
			$date[] = "Yesterday @ ".date("g:i a", strtotime($arr['date']));
		}
		else
		{
			$date[] = date("F j, Y, g:i a", strtotime($arr['date']));
		}
		$permalink[] = $arr['permalink'];
		$nature[] = $arr['nature'];
		$account[] = $arr['account'];
		$content[] = $arr['content'];
	}
	

?>

	<center>
	<div style='text-align:left;width:100%;font-size:12px;'>
		Boomarking Logs: Recently Posted
	</div>
	<div style='text-align:left;width:100%;font-size:12px;padding-top:3px;padding-bottom:3px''>
		<i>Posted</i> &nbsp;&nbsp;|&nbsp;&nbsp;
		<a href='f_bookmarking_report.php?mode=scheduled' target='_self'  style="text-decoration:none;color:grey;cursor:pointer;">Scheduled</a> 
		<br>
	</div>
	<table width='100%' class='bookmark_report'>
		<tr class=yellow>
			<td align='left' width='' style='font-size:10px;font-weight:300;width:137px;'>
				POSTED
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				POSTID	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				NATURE
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				ACCOUNT	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;' width='368px'>
				CONTENT		
			</td>
		</tr>
		<?php
		if ($post_id)
		{		
			foreach ($post_id as $key=>$val)
			{
				$content_preview = str_replace("'",'',$content[$key]);
				$content_short = substr($content[$key],0, 50);
				?>
				<tr>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $date[$key]; ?>			
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<a href='<?php echo $permalink[$key]; ?>' target='_blank'><?php echo $post_id[$key]; ?></a>
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $nature[$key]; ?>
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $account[$key]; ?>
					</td>
					<td align='left' style='text-align:left;font-szie:11px;font-weight:300; cursor:pointer;' title='<?php echo $content_preview; ?>'>
						<?php 
							echo $content_short."..."; 
						?>	
					</td>
				</tr>
				
				<?php
			}
		}	
		?>
	<table>
	</center>
	<script>
	parent.resizeIframe(document.body.scrollHeight);
	</script>
<?php
}
?>

<?php
if ($mode=='twitter_scheduled')
{
	$query  = "SELECT * FROM {$table_prefix}posts_to_bookmark WHERE status=0 AND nature='twitter' ORDER BY date ASC LIMIT 1000";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();}
	$count = mysql_num_rows($result);
	
	while ($arr = mysql_fetch_array($result))
	{
		$post_id[] = $arr['post_id'];
		
		$this_date = date("Y-m-d", strtotime($arr['date']));
		if ($this_date==$today)
		{
			$date[] = "Today @ ".date("g:i a", strtotime($arr['date']));
		}
		else if ($this_date==$tomarrow)
		{
			$date[] = "Tomarrow @ ".date("g:i a", strtotime($arr['date']));
		}
		else
		{
			$date[] = date("F j, Y, g:i a", strtotime($arr['date']));
		}
		$permalink[] = $arr['permalink'];
		$account[] = $arr['account'];
		$content[] = $arr['content'];
	}
	

?>

	<center>
	<div style='text-align:left;width:100%;font-size:12px;'>
		Twitter
	</div>
	<div style='text-align:left;width:100%;font-size:12px;padding-top:3px;padding-bottom:3px''>
		<a href='f_bookmarking_report.php?mode=twitter_posted' target='_self'  style="text-decoration:none;color:grey;cursor:pointer;">Posted</a> &nbsp;&nbsp;|&nbsp;&nbsp;
		<i>Scheduled</i> 
		<br>
	</div>
	<table width='100%' class='bookmark_report'>
		<tr class=yellow>
			<td align='left' width='' style='font-size:10px;font-weight:300;width:137px;'>
				UPCOMMING
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				POSTID	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				TWITTER ACCOUNT	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;' width='368px'>
				TWEET		
			</td>
		</tr>
		<?php
			if ($post_id)
			{
				foreach ($post_id as $key=>$val)
				{
					$content_preview = str_replace("'",'',$content[$key]);
					$content_short = substr($content[$key],0, 50);
					?>
					<tr>
						<td align='left' style='font-szie:13px;font-weight:300;'>
							<?php echo $date[$key]; ?>			
						</td>
						<td align='left' style='font-szie:13px;font-weight:300;'>
							<a href='<?php echo $permalink[$key]; ?>' target='_blank'><?php echo $post_id[$key]; ?></a>
						</td>
						<td align='left' style='font-szie:13px;font-weight:300;'>
							<a href='http://www.twitter.com/<?php echo $account[$key]; ?>/' target='_blank'><?php echo $account[$key]; ?></a>		
						</td>
						<td align='left' style='text-align:left;font-szie:11px;font-weight:300; cursor:pointer;' title='<?php echo $content_preview; ?>'>
							<?php 
								echo $content_short."..."; 
							?>	
						</td>
					</tr>
					
					<?php
				}
			}
			
		?>
	<table>
	</center>
	<script>
	parent.resizeIframe(document.body.scrollHeight);
	</script>
<?php
}
?>

<?php
if ($mode=='twitter_posted')
{
	$query  = "SELECT * FROM {$table_prefix}posts_to_bookmark WHERE status=1 AND nature='twitter' ORDER BY date DESC LIMIT 1000";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();}
	$count = mysql_num_rows($result);
	
	while ($arr = mysql_fetch_array($result))
	{
		$post_id[] = $arr['post_id'];
		$this_date = date("Y-m-d", strtotime($arr['date']));
		if ($this_date==$today)
		{
			$date[] = "Today @ ".date("g:i a", strtotime($arr['date']));
		}
		else if ($this_date==$yesterday)
		{
			$date[] = "Yesterday @ ".date("g:i a", strtotime($arr['date']));
		}
		else
		{
			$date[] = date("F j, Y, g:i a", strtotime($arr['date']));
		}
		$permalink[] = $arr['permalink'];
		$account[] = $arr['account'];
		$content[] = $arr['content'];
	}
	

?>

	<center>
	<div style='text-align:left;width:100%;font-size:12px;'>
		Twitter
	</div>
	<div style='text-align:left;width:100%;font-size:12px;padding-top:3px;padding-bottom:3px''>
		<i>Posted</i> &nbsp;&nbsp;|&nbsp;&nbsp;
		<a href='f_bookmarking_report.php?mode=twitter_scheduled' target='_self' style="text-decoration:none;color:grey;cursor:pointer;">Scheduled</a> 
		<br>
	</div>
	<table width='100%' class='bookmark_report'>
		<tr class=yellow>
			<td align='left' width='' style='font-size:10px;font-weight:300;width:137px;'>
				POSTED
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				POSTID	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				TWITTER ACCOUNT	
			</td>
			<td align='left' style='text-align:left;font-size:10px;font-weight:300;' width='368px'>
				TWEET		
			</td>
		</tr>
		<?php
			if ($post_id)
			{
				foreach ($post_id as $key=>$val)
				{
					$content_preview = str_replace("'",'',$content[$key]);
					$content_short = substr($content[$key],0, 50);
					?>
					<tr>
						<td align='left' style='font-szie:13px;font-weight:300;'>
							<?php echo $date[$key]; ?>			
						</td>
						<td align='left' style='font-szie:13px;font-weight:300;'>
							<a href='<?php echo $permalink[$key]; ?>' target='_blank'><?php echo $post_id[$key]; ?></a>
						</td>
						<td align='left' style='font-szie:13px;font-weight:300;'>
							<a href='http://www.twitter.com/<?php echo $account[$key]; ?>/' target='_blank'><?php echo $account[$key]; ?></a>		
						</td>
						<td align='left' style='text-align:left;font-szie:11px;font-weight:300; cursor:pointer;' title='<?php echo $content_preview; ?>'>
							<?php 
								echo $content_short."..."; 
							?>	
						</td>
					</tr>
					
					<?php
				}
			}
			
		?>
	<table>
	</center>
	<script>
	parent.resizeIframe(document.body.scrollHeight);
	</script>
<?php
}
?>

<?php
if ($mode=='pixelpipe_posted')
{
	$query  = "SELECT * FROM {$table_prefix}posts_to_bookmark WHERE status=1 AND nature='pixelpipe' ORDER BY date DESC LIMIT 1000";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();}
	$count = mysql_num_rows($result);
	
	while ($arr = mysql_fetch_array($result))
	{
		$post_id[] = $arr['post_id'];
		$this_date = date("Y-m-d", strtotime($arr['date']));
		if ($this_date==$today)
		{
			$date[] = "Today @ ".date("g:i a", strtotime($arr['date']));
		}
		else if ($this_date==$yesterday)
		{
			$date[] = "Yesterday @ ".date("g:i a", strtotime($arr['date']));
		}
		else
		{
			$date[] = date("F j, Y, g:i a", strtotime($arr['date']));
		}
		$permalink[] = $arr['permalink'];
		$pixelpipe_account[] = $arr['account'];
		$pixelpipe_content[] = $arr['content'];
	}
	

?>

	<center>
	<div style='text-align:left;width:100%;font-size:12px;'>
		Ping.FM
	</div>
	<div style='text-align:left;width:100%;font-size:12px;padding-top:3px;padding-bottom:3px''>
		<i>Posted</i> &nbsp;&nbsp;|&nbsp;&nbsp;
		<a href='f_bookmarking_report.php?mode=pixelpipe_scheduled' target='_self' style="text-decoration:none;color:grey;cursor:pointer;">Scheduled</a> 
		<br>
	</div>
	<table width='100%' class='bookmark_report'>
		<tr class=yellow>
			<td align='left' width='' style='font-size:10px;font-weight:300;width:137px;'>
				POSTED
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				POSTID	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				PIXELPIPE ACCOUNT	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;' width='368px'>
				BOOKMARK		
			</td>
		</tr>
		<?php
		if ($post_id)
		{	
			foreach ($post_id as $key=>$val)
			{
				$content_preview = str_replace("'",'',$content[$key]);
				$content_short = substr($content[$key],0, 50);
				?>
				<tr>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $date[$key]; ?>			
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<a href='<?php echo $permalink[$key]; ?>' target='_blank'><?php echo $post_id[$key]; ?></a>
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $pixelpipe_account[$key]; ?>	
					</td>
					<td align='left' style='text-align:left;font-szie:11px;font-weight:300; cursor:pointer;' title='<?php echo $content_preview; ?>'>
						<?php 
							echo $pixelpipe_content[$key]; 
						?>	
					</td>
				</tr>
				
				<?php
			}
		}	
		?>
	<table>
	</center>
	<script>
	parent.resizeIframe(document.body.scrollHeight);
	</script>

<?php
}
?>

<?php
if ($mode=='pixelpipe_scheduled')
{
	$query  = "SELECT * FROM {$table_prefix}posts_to_bookmark WHERE status=0 AND nature='pixelpipe' ORDER BY date ASC LIMIT 1000";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();}
	$count = mysql_num_rows($result);
	
	while ($arr = mysql_fetch_array($result))
	{
		$post_id[] = $arr['post_id'];
		$this_date = date("Y-m-d", strtotime($arr['date']));
		if ($this_date==$today)
		{
			$date[] = "Today @ ".date("g:i a", strtotime($arr['date']));
		}
		else if ($this_date==$tomarrow)
		{
			$date[] = "Tomarrow @ ".date("g:i a", strtotime($arr['date']));
		}
		else
		{
			$date[] = date("F j, Y, g:i a", strtotime($arr['date']));
		}
		$permalink[] = $arr['permalink'];
		$pixelpipe_account[] = $arr['account'];
		$pixelpipe_content[] = $arr['content'];
	}
	

?>

	<center>
	<div style='text-align:left;width:100%;font-size:12px;'>
		Ping.FM
	</div>
	<div style='text-align:left;width:100%;font-size:12px;padding-top:3px;padding-bottom:3px''>
		<a href='f_bookmarking_report.php?mode=pixelpipe_posted' target='_self' style="text-decoration:none;color:grey;cursor:pointer;">Posted</a> &nbsp;&nbsp;|&nbsp;&nbsp;
		<i>Scheduled</i> 
		<br>
	</div>
	<table width='100%' class='bookmark_report'>
		<tr class=yellow>
			<td align='left' width='' style='font-size:10px;font-weight:300;width:137px;'>
				UPCOMMING
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				POSTID	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				PIXELPIPE ACCOUNT	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;' width='368px'>
				BOOKMARK		
			</td>
		</tr>
		<?php
		if ($post_id)
		{		
			foreach ($post_id as $key=>$val)
			{
				$content_preview = str_replace("'",'',$content[$key]);
				?>
				<tr>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $date[$key]; ?>			
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<a href='<?php echo $permalink[$key]; ?>' target='_blank'><?php echo $post_id[$key]; ?></a>
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $pixelpipe_account[$key]; ?>	
					</td>
					<td align='left' style='text-align:left;font-szie:11px;font-weight:300; cursor:pointer;' title='<?php echo $content_preview; ?>'>
						<?php 
							echo $pixelpipe_content[$key]; 
						?>	
					</td>
				</tr>
				
				<?php
			}
		}	
		?>
	<table>
	</center>
	<script>
	parent.resizeIframe(document.body.scrollHeight);
	</script>

<?php
}
?>

<?php
if ($mode=='hellotxt_posted')
{
	$query  = "SELECT * FROM {$table_prefix}posts_to_bookmark WHERE status=1 AND nature='hellotxt' ORDER BY date DESC LIMIT 1000";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();}
	$count = mysql_num_rows($result);
	
	while ($arr = mysql_fetch_array($result))
	{
		$post_id[] = $arr['post_id'];
		$this_date = date("Y-m-d", strtotime($arr['date']));
		if ($this_date==$today)
		{
			$date[] = "Today @ ".date("g:i a", strtotime($arr['date']));
		}
		else if ($this_date==$yesterday)
		{
			$date[] = "Yesterday @ ".date("g:i a", strtotime($arr['date']));
		}
		else
		{
			$date[] = date("F j, Y, g:i a", strtotime($arr['date']));
		}
		$permalink[] = $arr['permalink'];
		$pixelpipe_account[] = $arr['account'];
		$pixelpipe_content[] = $arr['content'];
	}
	

?>

	<center>
	<div style='text-align:left;width:100%;font-size:12px;'>
		HelloTXT
	</div>
	<div style='text-align:left;width:100%;font-size:12px;padding-top:3px;padding-bottom:3px''>
		<i>Posted</i> &nbsp;&nbsp;|&nbsp;&nbsp;
		<a href='f_bookmarking_report.php?mode=helloTXT_scheduled' target='_self' style="text-decoration:none;color:grey;cursor:pointer;">Scheduled</a> 
		<br>
	</div>
	<table width='100%' class='bookmark_report'>
		<tr class=yellow>
			<td align='left' width='' style='font-size:10px;font-weight:300;width:137px;'>
				POSTED
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				POSTID	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				pixelpipe ACCOUNT	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;' width='368px'>
				BOOKMARK		
			</td>
		</tr>
		<?php
		if ($post_id)
		{		
			foreach ($post_id as $key=>$val)
			{
				$content_preview = str_replace("'",'',$content[$key]);
				$content_short = substr($content[$key],0, 50);
				?>
				<tr>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $date[$key]; ?>			
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<a href='<?php echo $permalink[$key]; ?>' target='_blank'><?php echo $post_id[$key]; ?></a>
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<a href='http://www.hellotxt.com/<?php echo $account[$key]; ?>/' target='_blank'><?php echo $account[$key]; ?></a>		
					</td>
					<td align='left' style='text-align:left;font-szie:11px;font-weight:300; cursor:pointer;' title='<?php echo $content_preview; ?>'>
						<?php 
							echo $content_short."..."; 
						?>	
					</td>
				</tr>
				
				<?php
			}
		}	
		?>
	<table>
	</center>
	<script>
	parent.resizeIframe(document.body.scrollHeight);
	</script>

<?php
}
?>

<?php
if ($mode=='hellotxt_scheduled')
{
	$query  = "SELECT * FROM {$table_prefix}posts_to_bookmark WHERE status=0 AND nature='hellotxt' ORDER BY date ASC LIMIT 1000";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();}
	$count = mysql_num_rows($result);
	
	while ($arr = mysql_fetch_array($result))
	{
		$post_id[] = $arr['post_id'];
		$this_date = date("Y-m-d", strtotime($arr['date']));
		if ($this_date==$today)
		{
			$date[] = "Today @ ".date("g:i a", strtotime($arr['date']));
		}
		else if ($this_date==$tomarrow)
		{
			$date[] = "Tomarrow @ ".date("g:i a", strtotime($arr['date']));
		}
		else
		{
			$date[] = date("F j, Y, g:i a", strtotime($arr['date']));
		}
		$permalink[] = $arr['permalink'];
		$pixelpipe_account[] = $arr['account'];
		$pixelpipe_content[] = $arr['content'];
	}
	

?>

	<center>
	<div style='text-align:left;width:100%;font-size:12px;'>
		HelloTXT
	</div>
	<div style='text-align:left;width:100%;font-size:12px;padding-top:3px;padding-bottom:3px''>
		<a href='f_bookmarking_report.php?mode=hellotxt_posted' target='_self' style="text-decoration:none;color:grey;cursor:pointer;">Posted</a> &nbsp;&nbsp;|&nbsp;&nbsp;
		<i>Scheduled</i> 
		<br>
	</div>
	<table width='100%' class='bookmark_report'>
		<tr class=yellow>
			<td align='left' width='' style='font-size:10px;font-weight:300;width:137px;'>
				UPCOMMING
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				POSTID	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				pixelpipe ACCOUNT	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;' width='368px'>
				BOOKMARK		
			</td>
		</tr>
		<?php
		if ($post_id)
		{		
			foreach ($post_id as $key=>$val)
			{
				$content_preview = str_replace("'",'',$content[$key]);
				$content_short = substr($content[$key],0, 50);
				?>
				<tr>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $date[$key]; ?>			
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<a href='<?php echo $permalink[$key]; ?>' target='_blank'><?php echo $post_id[$key]; ?></a>
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<a href='http://www.hellotxt.com/<?php echo $account[$key]; ?>/' target='_blank'><?php echo $account[$key]; ?></a>		
					</td>
					<td align='left' style='text-align:left;font-szie:11px;font-weight:300; cursor:pointer;' title='<?php echo $content_preview; ?>'>
						<?php 
							echo $content_short."..."; 
						?>	
					</td>
				</tr>
				
				<?php
			}
		}	
		?>
	<table>
	</center>
	<script>
	parent.resizeIframe(document.body.scrollHeight);
	</script>

<?php
}
?>


<?php
if ($mode=='ping_posted')
{
	$query  = "SELECT * FROM {$table_prefix}posts_to_bookmark WHERE status=1 AND nature='ping' ORDER BY date DESC LIMIT 1000";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();}
	$count = mysql_num_rows($result);
	
	while ($arr = mysql_fetch_array($result))
	{
		$post_id[] = $arr['post_id'];
		$this_date = date("Y-m-d", strtotime($arr['date']));
		if ($this_date==$today)
		{
			$date[] = "Today @ ".date("g:i a", strtotime($arr['date']));
		}
		else if ($this_date==$yesterday)
		{
			$date[] = "Yesterday @ ".date("g:i a", strtotime($arr['date']));
		}
		else
		{
			$date[] = date("F j, Y, g:i a", strtotime($arr['date']));
		}
		$permalink[] = $arr['permalink'];
	}
	

?>

	<center>
	<div style='text-align:left;width:100%;font-size:12px;'>
		Pings
	</div>
	<div style='text-align:left;width:100%;font-size:12px;padding-top:3px;padding-bottom:3px''>
		<i>Posted</i> &nbsp;&nbsp;|&nbsp;&nbsp;
		<a href='f_bookmarking_report.php?mode=ping_scheduled' target='_self' style="text-decoration:none;color:grey;cursor:pointer;">Scheduled</a> 
		<br>
	</div>
	<table width='100%' class='bookmark_report'>
		<tr class=yellow>
			<td align='left' width='' style='font-size:10px;font-weight:300;width:137px;'>
				POSTED
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				POSTID	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				
			</td>
			<td align='left' style='font-size:10px;font-weight:300;' width='368px'>
					
			</td>
		</tr>
		<?php
		if ($post_id)
		{		
			foreach ($post_id as $key=>$val)
			{
				$content_preview = str_replace("'",'',$content[$key]);
				$content_short = substr($content[$key],0, 50);
				?>
				<tr>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $date[$key]; ?>			
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<a href='<?php echo $permalink[$key]; ?>' target='_blank'><?php echo $post_id[$key]; ?></a>
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
					</td>
					<td align='left' style='text-align:left;font-szie:13px;font-weight:300;'>
			
					</td>
				</tr>
				
				<?php
			}
		}
		?>
	<table>
	</center>
	<script>
	parent.resizeIframe(document.body.scrollHeight);
	</script>

<?php
}
?>

<?php
if ($mode=='ping_scheduled')
{
	$query  = "SELECT * FROM {$table_prefix}posts_to_bookmark WHERE status=0 AND nature='ping' ORDER BY date ASC LIMIT 1000";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error();}
	$count = mysql_num_rows($result);
	
	while ($arr = mysql_fetch_array($result))
	{
		$post_id[] = $arr['post_id'];
		$this_date = date("Y-m-d", strtotime($arr['date']));
		if ($this_date==$today)
		{
			$date[] = "Today @ ".date("g:i a", strtotime($arr['date']));
		}
		else if ($this_date==$tomarrow)
		{
			$date[] = "Tomarrow @ ".date("g:i a", strtotime($arr['date']));
		}
		else
		{
			$date[] = date("F j, Y, g:i a", strtotime($arr['date']));
		}
		$permalink[] = $arr['permalink'];
	}
	

?>

	<center>
	<div style='text-align:left;width:100%;font-size:12px;'>
		Pings
	</div>
	<div style='text-align:left;width:100%;font-size:12px;padding-top:3px;padding-bottom:3px''>
		<a href='f_bookmarking_report.php?mode=ping_posted' target='_self' style="text-decoration:none;color:grey;cursor:pointer;">Posted</a> &nbsp;&nbsp;|&nbsp;&nbsp;
		<i>Scheduled</i> 
		<br>
	</div>
	<table width='100%' class='bookmark_report'>
		<tr class=yellow>
			<td align='left' width='' style='font-size:10px;font-weight:300;width:137px;'>
				SCHEDULED
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
				POSTID	
			</td>
			<td align='left' style='font-size:10px;font-weight:300;'>
		
			</td>
			<td align='left' style='font-size:10px;font-weight:300;' width='368px'>
			
			</td>
		</tr>
		<?php
		if ($post_id)
		{		
			foreach ($post_id as $key=>$val)
			{
				$content_preview = str_replace("'",'',$content[$key]);
				$content_short = substr($content[$key],0, 50);
				?>
				<tr>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<?php echo $date[$key]; ?>			
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<a href='<?php echo $permalink[$key]; ?>' target='_blank'><?php echo $post_id[$key]; ?></a>
					</td>
					<td align='left' style='font-szie:13px;font-weight:300;'>
						<a href='http://www.twitter.com/<?php echo $ping_username[$key]; ?>/' target='_blank'><?php echo $ping_username[$key]; ?></a>		
					</td>
					<td align='left' style='text-align:left;font-szie:11px;font-weight:300; cursor:pointer;' title='<?php echo $content_preview; ?>'>
					</td>
				</tr>
				
				<?php
			}
		}	
		?>
	<table>
	</center>
	<script>
	parent.resizeIframe(document.body.scrollHeight);
	</script>

<?php
}
?>


</body>
</html>