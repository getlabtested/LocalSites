<?php
include_once('./../../wp-config.php');
session_start();


$query = "SELECT * FROM ".$table_prefix."blogsense WHERE `option_name` IN (";
$query .= "'blogsense_api_secret_key' , ";
$query .= "'blogsense_spin_exclude_cats' , ";
$query .= "'blogsense_spin_exclude_these' , ";
$query .= "'blogsense_spin_phrase_max' , ";
$query .= "'blogsense_spin_phrase_min',";
$query .= "'blogsense_tbs_maxsyns' ,";
$query .= "'blogsense_tbs_password' ,";
$query .= "'blogsense_tbs_quality' ,";
$query .= "'blogsense_tbs_spinning' ,";
$query .= "'blogsense_tbs_username' ) ORDER BY option_name ASC";


$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error(); exit;}
$count = mysql_num_rows($result);

for ($i=0;$i<$count;$i++)
{
  $arr = mysql_fetch_array($result);
  if ($i==0){$blogsense_api_secret_key = $arr[option_value];} 
  if ($i==1){$spin_exclude_cats = $arr[option_value];}
  if ($i==2){$spin_exclude_these = $arr[option_value];}
  if ($i==3){$spin_phrase_max = $arr[option_value];}
  if ($i==4){$spin_phrase_min = $arr[option_value];}
  if ($i==5){$tbs_maxsyns = $arr[option_value];}
  if ($i==6){$tbs_password = $arr[option_value];}
  if ($i==7){$tbs_quality = $arr[option_value];}
  if ($i==8){$tbs_spinning = $arr[option_value];}
  if ($i==9){$tbs_username = $arr[option_value];}
}
if ($_POST['nature']=='tbs')
{
	$tbs_spinning =1;
}
else
{
	$tbs_spinning = 0;
}

if ($_POST['api_key']!=$blogsense_api_secret_key)
{
	include("./../functions/f_login.php");
	if(checkSession() == false)
	blogsense_redirect("./../login.php");
}

include("./../includes/helper_functions.php");
?>


<html>
	<head>
	<title>Personal Spin API - BlogSenseWp</title>
	<script type="text/javascript" src="./../includes/jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="./../includes/jquery-ui-1.7.2.custom.css">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
	<script type="text/javascript"> 
	$(document).ready(function() 
	{
		$("#id_accordion").accordion({
		autoHeight: false,
		collapsible: true,
		active: 1
	});
	});
	</script>
	</head>
	<body style="font-family:Khmer UI;">
	
	<?php
	$content = $_POST['content'];
	$nature = $_POST['nature'];
	if ($nature=='spyntax')
	{
		$content = spyntax($content);
		$content = stripslashes($content);
		$content = nl2br($content);
		echo $content;exit;
	}
	if ($nature=='tbs'||$nature=='blogsense')
	{
		$content = spin_text($content, '', '', '', 'spin');
		$content = stripslashes($content);
		$content = nl2br($content);
		echo $content;exit;
	}
	
	?>
	
	
	<form action="" id="" name="" method=POST>


	<table width="100%" >
		<tr>
			<td width='50%' valign='top'>
				
					<center>							   
					<div style="font-size:12px;width:100%;text-align:left;margin-left:auto;margin-right:auto;">
					<h3 style=''>The BlogSense Spin API</h3>
					This api script can be used in a couple of ways. It can be used as a traditional POST/RESPONCE API, or can be accessed via browser
					for manual spinning. Please use the paramaters below if you wish to use this location in the POST/REPONSE manner. POST, in this API's case means: to use CURL or an HTML submit form to POST content to this location for an outputted response.  
					Otherwise please fill out the input fields below and press submit to spin your content.<br><br>
					
					<div class='class_accordion' id='id_accordion'>
					<h3 style='padding-left:25px;padding-top:10px;padding-bottom:10px'>POST/RESPONSE API PARAMETERS</h3>
						<DIV ALIGN='LEFT'>
						<table style='font-size:12px;'>
							<tr>
								<td align='left' VALIGN='TOP' width='200'>
									<i>PARAMETER</I>
								</td>
								<td align='left' VALIGN='TOP'>
									<i>DESCRIPTION</I>
								</td>
							</tr>
							<tr>
								<td align='left' VALIGN='TOP'>
									api_key (mandatory)
								</td>
								<td align='left' VALIGN='TOP'>
									This is your private access key that is required in order to prevent non-customers from using your API. Your API key is located at BlogSense->Automation->API.<br><br>
									Your current API key is '<i><?php echo $blogsense_api_secret_key; ?></i>'<br><br>
									
									
									
								</td>
							</tr>
							<tr>
								<td align='left' VALIGN='TOP'>
									nature (mandatory)
								</td>
								<td align='left' VALIGN='TOP'>
									This parameter defines how we intend to spin the content. The following parameters are accepted: tbs,spyntax,blogsense<br><br>
									
									<h4>Acceptible Parameters</h4>
									tbs : We will use The Best Spinner to spin the content if account credentials are available within the appropriate BlogSense settings area.<br><br> 
									
									blogsense : We will use BlogSense's internal spinner to spin the content posted to this api.<br><br> 
									
									spyntax : If the nature is set to this parameter, BlogSense will expect the 'content' parameter to contain spin ready formatted content. eg: {words|words|words}<br><br> 
								</td>
							</tr>
							<tr>
								<td align='left' VALIGN='TOP'>
									content (mandatory)
								</td>
								<td align='left' VALIGN='TOP'>
									This parameter accepts the post content to be spun. 
								</td>
							</tr>
						</table>
					</DIV>
					
					<H3 style='padding-left:25px;padding-top:10px;padding-bottom:10px'>SPIN THIS TEXT!</H3>
					<DIV ALIGN='LEFT'>
					 
						<table  style="width:100%;margin-left:auto;margin-right:auto;border: solid 1px #eeeeee;"> 
							<tr>
								<td  align=left valign='middle' style="font-size:13px;">
									Nature: <br> 
								</td>
								<td valign='middle' align=right style="font-size:13px;">
									<input type='radio' name='nature' value='spyntax' >Spin Ready Article</input>
									<input type='radio' name='nature' value='blogsense' CHECKED>Spin with BlogSense</input>
									<input type='radio' name='nature' value='tbs'>The Best Spinner</input>							
								</td>
							</tr>
							<tr>
								<td  align=left valign='middle' style="font-size:13px;">
									Paste Content: <br> 
								</td>
								<td valign='middle' align=right style="font-size:13px;">
									<textarea name='content' style='width:100%;height:300px;'></textarea>
								</td>
							</tr>
							<tr>
								<td  align=left valign='middle' style="font-size:13px;">
								</td>
								<td valign='middle' align=right style="font-size:13px;">
									<INPUT type='submit' value='Submit'>
								</td>
							</tr>
						</table>
					<div>
				</DIV>
			</table>
			</td>
		</tr>
	</table>
	</form>
	</div>