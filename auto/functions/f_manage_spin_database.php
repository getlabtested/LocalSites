<?php
session_start();
include_once('./../../wp-config.php');
include_once('./../includes/prepare_variables.php');

function clean_cdata($input)
{
  if (strstr($input, "<![CDATA["))
  {
	$input = str_replace ('<![CDATA[','',$input);
	$input = str_replace (array(']]>',']]&gt;'),'',$input);
  }
  return $input;
}

$nuprefix = explode('_',$table_prefix);
$nuprefix= $nuprefix[0]."_";
$tSTokens = $nuprefix . 'stokens';

if ($_POST['nature']=='import_database')
{
	if ($_FILES["file"])
	{
		//echo 1; exit;
		if ($_FILES["file"]["error"] > 0)
		{
			echo "Error: " . $_FILES["file"]["error"] . "<br />";
		}
		else
		{
			$query = "TRUNCATE $tSTokens";
			$result = mysql_query($query);
			if (!$result){ echo $query; echo mysql_error(); exit; }
			
			$string = file_get_contents($_FILES['file']['tmp_name']);
			//echo $string;exit;
			$set = explode('<set>',$string);
			array_shift($set);
			//print_r($campaigns);
			$i = 1; 
			foreach ($set as $key=>$val)
			{
				$val = str_replace('</set>','',$val);
				$val = clean_cdata($val);
				$val = explode('|',$val);
				
				foreach ($val as $k=>$v)
				{
				//print_r($this_array);exit;
					$query = "INSERT INTO $tSTokens (`id`,`phrase`) VALUES ('$i',$v')";
					$result = mysql_query($query);
					if (!$result){ echo $query; echo mysql_error(); exit; }
					
				}
				$i++;
			}
		}
		echo "<center><font color='green'>$i Sets Imported!</font>";
		exit;
	}
	else
	{
		echo "Failed for some reason.";
	}
}
if ($_GET['nature']=='export_database')
{
	ini_set('memory_limit', '128M');
	//include_once('xml_parser.php');
	include_once('./../../wp-admin/includes/class-pclzip.php');
	
	$these_ids = $_POST['campaign_id'];
	$count = count($campaign_id);
	
	//print_r($these_ids);exit;
	$this_xml ="<?xml version='1.0' ?>\n<rss version='2.0'>\n<channel>\n";
	$this_xml .="<title>BlogSense Spin Database Backup File</title>\n";
	
	$query = "SELECT * FROM $tSTokens";
	$result = mysql_query($query);
	if (!$result){ echo $query; echo mysql_error(); exit; }
	
	while ($arr = mysql_fetch_array($result))
	{
		$id = $arr['id'];
		$phrase = $arr['phrase'];
		
		$phrases[$id][] =  $phrase;
	}
	
	$keys = array_keys($phrases);

	foreach ($keys as $key=>$val)
	{
		
		if ($phrases[$key])
		{
			$set = implode('|',$phrases[$key]);
			$this_xml .="<set>\n";
			$this_xml .="<![CDATA[ {$set} ]]>\n";
			$this_xml .="</set>\n";
		}
		else
		{
			//echo $key;
			//print_r($phrases[$key]);
		}
	}
	
	$xml_backup = "./../my-backups/bs-spindatabase-{$table_prefix}backup-".date('m-d-Y')."-".time().".xml";
		
	//echo $backup;exit;
	$handle = fopen($xml_backup,'w+');
	fwrite($handle,$this_xml);
	fclose($handle);
	
	$this_site = sanitize_title_with_dashes($site_url);
	//now compile final backup zip
	$filename = "bs-spindatabase-{$table_prefix}backup-".date('m-d-Y')."-".time().".xml";
	
	  
	//print_r($v_list);exit;
	//unlink($xml_backup);
	//create headers and download file
	header('Content-type: application/xml');
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	readfile($xml_backup);
	
	exit;
}	
?>
<html>
<head>
<script type="text/javascript" src="./../includes/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="./../includes/jquery-ui-1.7.2.custom.css">
<script type='text/javascript'>
$(document).ready(function() 
{
	$(".class_add_phrase").live("click" ,function(){
       var result_id = this.id.replace('id_add_phrase_','');
	   
	   html = "<div style='display:inline;' >"
				+" &nbsp;|&nbsp; "
				+"<img onClick='$(this).parent().remove();' src='./../nav/remove.png' style='cursor:pointer;'>"
				+"<input size=15 name='phrase["+result_id+"][]' value=''></span>"
				+"</div>";
	   
	   $('.class_div_last_phrase_'+result_id+'').before(html);
	 });
	 
	$("#id_button_add_new_phrases").live("click" ,function()
	{
		$(".class_div_add_new_phrases").css("display","block");
	});
	
	$("#id_button_export_database").live("click" ,function()
	{
		$(".class_div_import_database").html("loading...");
		$(".class_div_import_database").css("display","block");
		window.location = "<?php echo $current_url; ?>?nature=export_database";

	});
	
	$("#id_button_import_database").live("click" ,function()
	{
		$(".class_div_import_database").css("display","block");
	});
	 
	 $("#id_add_new_new_phrase").live("click" ,function()
	 {	 
   
	   html = "<div style='display:inline;' >"
				+" &nbsp;|&nbsp; "
				+"<img onClick='$(this).parent().remove();' src='./../nav/remove.png' style='cursor:pointer;'>"
				+"<input size=15 name='new_phrase[]' value=''></span>"
				+"</div>";
	   
	   $('.class_div_last_new_phrase').before(html);
	 });
	 
	
});
</script>
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
<?php
if ($_POST['nature']=='save')
{
	$phrases = $_POST['phrase'];
	//print_r($phrases);exit;
	$ids = array_keys($phrases);
	if ($ids)
	{
		foreach ($ids as $key=>$val)
		{
			//echo "deleted $val <br>";
			$query  = "DELETE FROM $tSTokens WHERE id = '$val'";
			$result = mysql_query($query);
			if (!$result){echo $query; echo mysql_error(); exit; }
		}
		
		foreach ($phrases as $key=>$val)
		{
		
			IF ($val)
			{
				foreach ($val as $k=>$v)
				{
					$query  = "INSERT INTO $tSTokens (`id`,`phrase`) VALUES ('$key','$v')";
					$result = mysql_query($query);
					if (!$result){echo $query; echo mysql_error(); exit; }
					//echo $query;
					//echo "<hr>";
				}
			}
		}
		
		echo "<center><font color='green'>Updated!</font>";
		exit;
	}
}
else if ($_POST['nature']=='add_phrase')
{
	$phrases = $_POST['new_phrase'];
	$query = "SELECT id FROM $tSTokens ORDER BY id DESC LIMIT 1";
	$result = mysql_query($query);
	$arr = mysql_fetch_array($result);
	$lid = $arr['id'];
	$lid++;
	foreach ($phrases as $key=>$val)
	{
	
		IF ($val)
		{
			$query  = "INSERT INTO $tSTokens (`id`,`phrase`) VALUES ('$lid','$val')";
			$result = mysql_query($query);
			if (!$result){echo $query; echo mysql_error(); exit; }		
		}
		
	}
	echo "<center><font color='green'>Added!</font>";
	exit;
}
else
{
	?>
	<form action='' method='GET'>
		<div style='text-align:left;width:100%;font-size:12px;padding-top:3px;padding-bottom:3px'>
		<i>Use the search box below to search out phrases for editing</i><br><br>
		Search Phrase: &nbsp;&nbsp; <input size=30 name='s'> &nbsp; <input type='submit' value='Search'> 
		or <input type='button' value='Add New Phrases' id='id_button_add_new_phrases'>
		or <input type='button' value='Export Spin Database' id='id_button_export_database'>
		or <input type='button' value='Import Spin Database' id='id_button_import_database'>
		<br>
		<hr>
		</div>
	</form>
	<div class='class_div_add_new_phrases' style='display:none;'>
		<form action='' method='POST'>
		<input type='hidden' name='nature' value='add_phrase'>
		<h4>Add New Phrases to Database <img src='./../nav/add.png' style='cursor:pointer;' id='id_add_new_new_phrase'> </h4>
			{
				<div style='display:inline;' id=''>
				<img onClick='$(this).parent().remove();' src='./../nav/remove.png' id='id_add_$val_$j' style='cursor:pointer;' >
				<input size=15 name='new_phrase[]' value=''></span>
				</div>
				<div class='class_div_last_new_phrase' style='display:inline;'></div>
								
			}
			<input type='submit' value='Add Phrase Set'>
		</form>
	</div>
	<div class='class_div_import_database' style='display:none;'>
		<form action='' method='POST' enctype="multipart/form-data">
		<input type='hidden' name='nature' value='import_database'>
		<h4>Import BlogSense Spin Database  </h4>
			<table width='400' align=middle>
				<tr>
					<td valign=top align='right' >
							
					</td>
				</tr>
				<tr>
					<td valign=top align='right' >
						<input type="file" name="file" id="file" />		
						<input type='submit'  value='Import XML Backup'>
					</td>
				</tr>
			</table>
		
		</form>
	</div>
	<?php
	
}

?>



<body >

<?php

	$search = $_GET['s'];
	if ($_GET['s'])
	{
		echo "<form action='' method='POST'>";
		echo "<input type='hidden' name='nature' value='save'>";
		
		$query  = "SELECT id, phrase FROM $tSTokens WHERE phrase LIKE '%$search%'";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error();}
		$count = mysql_num_rows($result);
		
		while ($arr = mysql_fetch_array($result))
		{
			$id[] = $arr['id'];
		}
		
		

		$i = 1;
		
		if ($id)
		{
			echo "<h3>Manage Database Results <input type='submit' value='Save All Changes'> </h3>";
			
			
			echo "<font style='text-decoration:italics; color:#aaaaaa; font-size:11px'>";
			foreach ($id as $key=>$val)
			{
			
				echo "<h4>Result Set $i <img src='./../nav/add.png' style='cursor:pointer;' class='class_add_phrase' id='id_add_phrase_$val'> 
					 </h4>";
				
				$query  = "SELECT phrase FROM $tSTokens WHERE id = '$val'";
				$result = mysql_query($query);
				echo "{ &nbsp;&nbsp;";
				$j = 0;
				while ($arr = mysql_fetch_array($result))
				{
					$phrase = $arr['phrase'];
					if ($phrase)
					{
						echo "<div style='display:inline;' id='id_div_$val_$j'>";
						if ($j!=0){ echo ' &nbsp;|&nbsp; '; }
						echo "<img onClick='$(this).parent().remove();' src='./../nav/remove.png' id='id_add_$val_$j' style='cursor:pointer;' >		";
						echo "<input size=15 name='phrase[$val][]' value='$phrase'></span>";
						echo "</div>";
						$j++;
					}
					
				}
				echo "&nbsp;&nbsp; <div class='class_div_last_phrase_$val' style='display:inline;'></div> }";
				
				echo "<hr><br>";
				$i++;
				
			}
			echo "<h3>Spin Database Results <input type='submit' value='Save All Changes'></h3>";
			echo "</form>";
		}
		else
		{
			echo "<center><i>No Results</i></center>";
			echo "</font>";
			echo "</form>";
		}
		
	}
	else
	{
		
	}
	

?>

	<center>
	




</body>
</html>