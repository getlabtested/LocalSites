<html>
<head>
<script type="text/javascript" src="./includes/jquery.js"></script>
<script type="text/javascript"> 
$(document).ready(function() 
{
	$("#add_post").click(function(){
		   $('#post_setup tr:last').after('<tr><td  align=left style=\"font-size:13px;\"><img onclick=\"$(this).parent().parent().remove();\" src=\"./nav/remove.png\" style=\"cursor:pointer;\"><input size=33 name=\"post_titles[]\" ></td></td><td align=right><input size=1 name="post_page_begin[]" >&nbsp;&nbsp; :&nbsp;<input size=1 name="post_page_end[]" ></td></tr>');
		});
});
</script>
</head>
<body>
<center>
<table >
	<tr>
		<td>
		    <?php 
			if ($_GET['s']==3)
			{
			?>
				<div style="font-size:14px;width:400;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">PDF URL</div>
				<hr width=400 style="color:#eeeeee;background-color:#eeeeee;">
				<font style="color:green;">Success! Please close window & refresh page.</font>
			<?php
			}
			else if ($_GET['s']!=2)
			{
			?>
				<div style="font-size:14px;width:400;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">PDF URL</div>
				<hr width=400 style="color:#eeeeee;background-color:#eeeeee;">
				<form action="pdf_prepare.php" method=POST>
				<input name="pdf" size=62>
				<input type=hidden name=m value=2>
				<div align=right><input type=submit value=Import></div>
				</form>
			<?php
			}
			else
			{
			  $page_count = $_GET['p'];
			  $pdf = $_GET['pdf'];
			  $pdf = urldecode($pdf);
			  $titles = $_GET['titles'];
			  $titles = explode(",", $titles);
			  $post_begin = $_GET['starts'];
			  $post_begin = explode(",", $post_begin);
			  $post_end = $_GET['ends'];
			  $post_end = explode(",", $post_end);
			  
			?>
			    <div style="font-size:14px;width:400;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">PDF Post Setup (<i><?php echo $page_count; ?> pages</i>)</div>
				<hr width=400 style="color:#eeeeee;background-color:#eeeeee;">
				<form action="pdf_prepare.php" method=POST>
				<input type=hidden name=m value=3>
				<input type=hidden name=pdf value="<?php echo $pdf; ?>">
				<table id=post_setup width=100%>
					<tr>
						<td  align=middle style="font-size:13px;">
							<i>Post Title</i>
						</td>
						<td align=right>
							 <font style="font-size:10px;"><i> Page Start : Page End</i></font>
						</td>
					</tr>
					<?php
					if ($titles)
					{
					  foreach ($titles as $k=>$v)
					  {
					  ?>
						 <tr>
							<td  align=left style="font-size:13px;">
								<img onclick="$(this).parent().parent().remove();" src="./nav/remove.png" style="cursor:pointer;">
								<input size=33 name="post_titles[]" value="<?php echo $titles[$k]; ?>" ></td>
							</td>
							<td align=right>
								<input size=1 name="post_page_begin[]" value="<?php echo $post_begin[$k]; ?>" >&nbsp;&nbsp; :&nbsp;
								<input size=1 name="post_page_end[]"value="<?php echo $post_end[$k]; ?>"  >
							</td>
						</tr>
					  <?php
					  }
					}
					else
					{
					?>
					<tr>
						<td  align=left style="font-size:13px;">
							<img onclick="$(this).parent().parent().remove();" src="./nav/remove.png" style="cursor:pointer;">
							<input size=33 name="post_titles[]" ></td>
						</td>
						<td align=right>
							<input size=1 name="post_page_begin[]" >&nbsp;&nbsp; :&nbsp;
							<input size=1 name="post_page_end[]" >
						</td>
					</tr>
					<?php
					}
					?>
				</table>
				<center><img src="nav/add.png" style="cursor:pointer;" id=add_post></center>
				<br>
				<div align=right><input type=submit name="preview" value=Preview><input type=submit name="import" value=Import></div>
				</form>
			<?php
			}
			?>
		</td>
	</tr>
</table>
</center>
</body>
</html>