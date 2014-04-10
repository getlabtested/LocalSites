<?php 
session_start();

include("../wp-config.php");
include("../wp-includes/class-phpass.php");
include("../wp-includes/pluggable.php");
include("functions/f_login.php");

if (function_exists('switch_to_blog')) switch_to_blog(1);

//echo $table_prefix; exit;
$user_level = $table_prefix."user_level";
if(isset($_POST['txtSubmit']))
{
	
	$username = $_POST['txtUsername'];
	$password = ($_POST['txtPassword']);
	$user = wp_authenticate($username, $password);
	//$user_string = var_dump($user);
	//die();
	if (isset($user->errors))
	{
		$msg = "User log in failed.";
	}
	else
	{
		if (!$user->$user_level)
		{
			$user_level = $table_prefix."capabilities";
			$admin  =  $user->$user_level; 
		}
		if(isset($user->$user_level) && $user->$user_level ==10||$admin['administrator']==1)
		{
			log_user_in($username,$password); 
			//echo $_SESSION['wp_custom_session']; exit;
			session_write_close();
			header("Location: index.php");
			echo " ";
			exit;
		}
		else
			$msg = "You dont have privilege to access these contents.";
	}
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>BlogSense-WP Professional Login</title>
<style>
body{
	font-family:Arial, Helvetica, sans-serif;
	font-size:13px;
}
.error
{
	padding:5px;
	border: 1px solid #FF9966;
	background-color: #FFCCCC;
	color:#FF0000;
	float:left;
}

</style>
<style type="text/css">@import url("style.css");</style>
</head>

<body>
<br><br>

<div class="main_container">
	<center>
	<?php if($msg !="") echo '<span class="error">'.$msg.'</span>';?>
	<div style="clear:both"></div>
	
	<div style="font-size:13px;width:400;text-align:center;margin-left:auto;margin-right:auto;font-weight:600;">
	<img src="nav/tip.png" style="cursor:pointer;" border=0 title="Use your wordpress administration details for access.">
				
	BlogSense-WP Login</div>
	<hr width=240 style="color:#eeeeee;background-color:#eeeeee;">
	<form name="loginForm" action="login.php" method="post">	
	<table width="200">
	<tr><td>Username:</td><td><input type="text" size="20" name="txtUsername" id="txtUsername" value="<?php echo $username;?>" /></td></tr>
	<tr><td>Password:</td><td><input type="password" size="20" name="txtPassword" id="txtPassword" value="" /></td></tr>
	<tr><td colspan="2" align=right><input type="submit" name="txtSubmit" value="Login" style="color:#000000;" /></td></tr>
	</table>
	</center>
	</form>
	<br><br><br>
</div>
<div class="footer_container">
<br><br><br><br><br></div>
</div>
<br>
</body>
</html>
