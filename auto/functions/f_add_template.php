<?php
set_time_limit(200);
include_once('./../../wp-config.php');
session_start();
include_once('./../includes/helper_functions.php');
include_once('./../includes/prepare_variables.php');
$pass = $_POST['pass'];
$template_name = $_POST['template_name'];
$template_content = $_POST['template_content'];

$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
$blogsense_url = explode('includes',$current_url);
$blogsense_url = $blogsense_url[0];

$type = $_GET['type'];

if ($_POST['pass']==1)
{
	$template_custom_variable_name = addslashes($_POST['template_custom_variable_name']);
	$template_custom_variable_token = addslashes($_POST['template_custom_variable_token']);
	$template_custom_variable_content = addslashes($_POST['template_custom_variable_content']);
	
	$query2 = "INSERT INTO `".$table_prefix."custom_tokens` (`id` ,`name` ,`token`,`content`)";
	$query2 .="VALUES ('', '$template_custom_variable_name', '$template_custom_variable_token' , '$template_custom_variable_content')";
	$result2 = mysql_query($query2);
	
	echo "<br><br><br><center><font color=green>Custom Variable Token Added! Please close this window to refreash.</center></font>";
	exit;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<script type="text/javascript" src="./../includes/jquery.js"></script>
<script type="text/javascript"> 



$(document).ready(function() 
{
 
	
});
</script>


</head>
<body style="font-family:Khmer UI;">
<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td align="center" valign="middle">
	<form action="" method="POST">
	<input type=hidden name=pass value=1>
	<input type=hidden name=type value='<?php echo $type; ?>'>

	<div style="font-size:14px;width:400;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">Create Custom Variable Token
	</div>
	<hr width=400 style="color:#eeeeee;background-color:#eeeeee;">

	<table width=400 style="margin-left:auto;margin-right:auto;border: solid 1px #eeeeee"> 
		<tr>
			<td  align=left width=100% valign=top style="font-size:13px;">
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Insert a name or discription for this token."> 
				Description:<br> 
			</td>
			<td align=right style="font-size:13px;">
				<input name='template_custom_variable_name' size=39 value='<?php echo $template_custom_variable_name;?>'>
			</td>
		</tr>
		<tr>
			<td  align=left width=100% valign=top style="font-size:13px;">
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="Create a custom %token% for use within your post templates."> 
				Variable Token:<br> 
			</td>
			<td align=right style="font-size:13px;">
				<input name='template_custom_variable_token' size=39 value='<?php echo $template_custom_variable_token;?>'>
			</td>
		</tr>
		<tr>
			 <td  colspan=2 align=left  valign=top style="font-size:13px;">
				<img src="./../nav/tip.png" style="cursor:pointer;" border=0 title="This content will replace your %token% variable when used within your post templates. At the time other tokens cannot be used within this token template.  If you are wanting to use spyntax please enclose the spyntax formatted content in [spyntax][/spyntax]."> 
				Replacement Content:  
			 </td>
		</tr>
		<tr>
			 <td colspan=2 align=right style="font-size:13px;">
				<textarea width=100% cols=45 rows=14 name='template_custom_variable_content' id=id_template_content><?php echo $template_custom_varibale_content;?></textarea>
			 </td>
		</tr>
		<tr>
			 <td colspan=2 align=center style="font-size:13px;">
				<input type=submit value="Add Token "> &nbsp;&nbsp;&nbsp; 
			 </td>
		</tr> 	 					
	</table>
	<br>

	</form>
		
</td>
</tr>
</table>
</body>
