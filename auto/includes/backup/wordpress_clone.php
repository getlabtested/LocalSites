<?php
include_once('./../../../wp-config.php');
session_start();
include_once('./../../includes/build_version.php');
include("./../../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../../login.php");

ini_set('memory_limit', '1024M');


//include_once('../includes/prepare_variables.php');
include_once('./../../../wp-admin/includes/class-pclzip.php');

/* backup the db OR just a table */
function backup_tables($tables = '*')
{
	global $table_prefix;

	
	$tables = array();
	$result = mysql_query('SHOW TABLES');
	while($row = mysql_fetch_row($result))
	{
		if (strcspn($table_prefix,'0123456789')==strlen($table_prefix))
		{
			if (strcspn($row[0],'0123456789')==strlen($row[0]))
			{
				//echo $row[0]; exit;
				$tables[] = $row[0];
			}
		}
		else
		{
			if (strstr($row[0],$table_prefix))
			{
				//echo 2; exit;
				$tables[] = $row[0];
			}
		}
	}
	
	//cycle through
	foreach($tables as $table)
	{
		
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE IF EXISTS '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
	
	
	//get htacess data
	$targetFile = "./../../../.htaccess"; 	
	$handle1 = @fopen($targetFile, 'r');  	
	if ($handle1)
	{
		$htaccess = fread($handle1,filesize($targetFile)); 
		fclose($handle1);
	}
	
	//get site url	
	$site_url = get_bloginfo('wpurl');
	$upload_path = wp_upload_dir();
	$upload_path = $upload_path['path'];
	$site_path = explode('wp-content', $site_path);
	$site_path = $site_path[0];
	//echo $site_path;exit;
	
	//build final backup file
	$xml  = "$site_url";
	$xml .= "<block>";
	$xml .= "$site_path";
	$xml .= "<block>";
	$xml .= "$upload_path";
	$xml .= "<block>";
	$xml .= "$htaccess";
	$xml .= "<block>";
	$xml .= "$return";
	$xml .= "<block>";
	$xml .= "$table_prefix";


	//echo $xml; exit;
	
	$xml_extension = '.xml';
	$xml_backup = '../../my-backups/backup'.$xml_extension;
	
	//echo $backup;exit;
	$handle = fopen($xml_backup,'w+');
	fwrite($handle,$xml);
	fclose($handle);
	
	$this_site = sanitize_title_with_dashes($site_url);
	//now compile final backup zip
	$filename = "bs-wordpress-$this_site-comprehensive-{$table_prefix}backup-".date('m-d-Y')."-".time()."";
	$wp_plugins = "../../../wp-content/plugins/";
	$wp_themes = "../../../wp-content/themes/";
	$final_backup_zip = "../../my-backups/$filename.zip";
	$zip = new PclZip($final_backup_zip);
	$v_list = $zip->create("{$xml_backup}", PCLZIP_OPT_REMOVE_PATH , '../../my-backups/');
	$v_list = $zip->add("{$wp_plugins},{$wp_themes}", PCLZIP_OPT_REMOVE_PATH, "../../../");
	  
	//print_r($v_list);exit;
	unlink($xml_backup);
	//create headers and download file
	header('Content-type: application/zip');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	readfile($final_backup_zip);
}

$return = backup_tables();

?>