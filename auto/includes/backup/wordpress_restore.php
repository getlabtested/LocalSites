<?php
include_once('./../../../wp-config.php');
session_start();
include_once('./../../includes/build_version.php');
include("./../../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../../login.php");

ini_set('memory_limit', '128M');
//include_once('xml_parser.php');
include_once('./../../../wp-admin/includes/class-pclzip.php');

$array= scandir("./../../my-backups/");
foreach ($array as $v)
{
	if ($v != '.'&&$v!= '..')
	{
		if (strstr($v, '.zip'))
		{
			$files[] = $v;
		}

	}
}

if ($_POST['nature']=='restore')
{

	$new_table_prefix = $_POST['table_prefix'];
	
	//get all of the tables belonging to this blog and  and truncate them
	$tables = array();
	$result = mysql_query('SHOW TABLES');
	while($row = mysql_fetch_row($result))
	{
		if (strstr($row[0],$table_prefix))
		{
			$tables[] = $row[0];
			//$result = mysql_query('TRUNCATE '.$table);
		}
	}

	$backup_file = $_POST['backup_file'];
	$archive = new PclZip("./../../my-backups/$backup_file");
	$list = $archive->extract(PCLZIP_OPT_BY_NAME, "backup.xml",
                              PCLZIP_OPT_EXTRACT_AS_STRING);
	if ($list == 0) {
      echo "ERROR : ".$archive->errorInfo(true);
      exit;
    }
	$xml_string =  $list[0]['content'];	

	$blocks = explode('<block>',$xml_string);
	//print_r($blocks);exit;
	$original_site_url = $blocks[0];
	$original_site_path = $blocks[1];
	$original_upload_path = $blocks[2];
	$htaccess = $blocks[3];
	$sql = $blocks[4];
	$old_table_prefix = $blocks[5];
	
	//echo $original_site_url;exit;
	//echo $original_site_path;exit;
	//echo $htaccess;exit;
	//echo $sql; exit;
	//echo $old_table_prefix; exit;
	
	if ($new_table_prefix)
	{
		$new_table_prefix = trim($new_table_prefix);
		$sql = str_replace($old_table_prefix,$new_table_prefix,$sql);
	}
	
	//replace old site url with new site url
	$new_site_url = get_bloginfo('wpurl');
	$new_upload_path = wp_upload_dir();
	$new_upload_path = $new_upload_path['path'];
	$new_site_path = explode('wp-content', $new_upload_path);
	$new_site_path = $site_path[0];

	if ($new_site_url!=$original_site_url)
	{
		//replace site
		if (substr($new_site_url, -1, 1)!='/')
		{
			$new_site_url = $new_site_url."/";
		}
		$sql = str_replace($original_site_url,$new_site_url,$sql);
		$sql = str_replace($original_upload_path,$new_upload_path,$sql);
		$htaccess = str_replace($original_site_url,$new_site_url,$htaccess);
		
		//clean path
		if (strstr($original_site_path,'public_html'))
		{
			$original_site_path = explode('public_html', $original_site_path);
			$original_site_path = $original_site_path[1];
		}
		
		//replace_path
		$htaccess = str_replace($original_site_path, $new_site_path, $htaccess);
		
	}
	
	//extract theme & plugin files
	$list = $archive->extract(PCLZIP_OPT_BY_NAME, "wp-content/",
                             PCLZIP_OPT_PATH, "../../../");
	if ($list == 0) {
      echo "ERROR : ".$archive->errorInfo(true);
      exit;
    }

	//create new htaccess file
	if ($htaccess)
	{
		$handle = fopen('../../../.htaccess', "w");
		fwrite($handle, $htaccess);
		fclose($handle);
	}
	
	$sql = explode(";\n",$sql);
	//echo count($sql); exit;
	foreach ($sql as $key=>$val)
	{
		//echo $val; exit;
		$val = trim($val);	
		if ($val)	
		{		
			$result = mysql_query($val);
			if (!$result){ echo $val; echo mysql_error(); exit; }
		}
	}
	
	
	echo "Plugins & Themes have successfully been extracted.<br>";
	echo ".htaccess has been rebuilt and modified to reflect current blog location. If you experience 500 connection issues then this file may require manual attention.<br>";
	echo "All posts, pages, plugin settings and theme settings of backup profile have been transplanted to current blog.";
	exit;
	
}
else
{
	?>
		
	<html>
	<head>

	<script type="text/javascript" src="./../includes/jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="./../includes/jquery-ui-1.7.2.custom.css">
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/jquery-ui.min.js"></script>
	<script type="text/javascript"> 
	$(document).ready(function() 
	{
		
	});
	</script>
	</head>
	<body style="font-family:Khmer UI;">
	<form action="" id="" name="" method=POST>
	<input type=hidden name='nature' value='restore'>


	<table width="100%">
		<tr>
			<td width='50%' valign='top'>
				<center>							   
				<div style="font-size:14px;width:90%;text-align:left;margin-left:auto;margin-right:auto;font-weight:600;">Restore a Wordpress Backup Profile 

				<hr style="width:100%;color:#eeeeee;background-color:#eeeeee;">
				 
				<table  style="width:100%;margin-left:auto;margin-right:auto;border: solid 1px #eeeeee;"> 
				  <tr>
					 <td  align=left valign=top style="font-size:13px;">
						<img src="./../../nav/tip.png" style="cursor:pointer;" border=0 title="Create and organize your articles into folders for maximum flexibility. Articles & folders are to be uploaded into /my-articles/ ">
						Select Backup: <br> </td>
					 <td align=right style="font-size:13px;">
						<select  name='backup_file'>
						<?php
							if ($files)
							{
								foreach ($files as $v)
								{			   
									echo "<option value='$v'>$v</option>";				  
								}
							}
							else
							{
								echo "<option>No backups exist";
							}
						?>
						</select>
					</td>
					</tr>
				  
					<tr>
						 <td colspan=2 align=center valign=top style='font-size:13px;padding:20px;'>
							<i> All backup profiles should be placed in your /my-backups/ folder.</i>
						 </td>
					</tr>
				
					<tr>
					 <td  align=left valign=top style="font-size:13px;">
						<img src="./../../nav/tip.png" style="cursor:pointer;" border=0 title="Leave this blank to retain original table prefix. A typical table prefix is wp_, and a typical prefix for a subblog is wp_2_.">
						Define Table Prefix: <br> </td>
					 <td align=right style="font-size:13px;">
						<input name='table_prefix' size=20>
					</td>
					</tr>
				  
					<tr>
						 <td colspan=2 align=center valign=top style='font-size:13px;padding:20px;'>
							<input type=submit value='Restore/Import Profile'>
						 </td>
					</tr>
			</table>
		</td>
	</table>
	</form>
	</div>
	</body>
	</html>
	<?php
}
?>
