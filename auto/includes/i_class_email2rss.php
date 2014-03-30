<? 

if ($_GET['mail_server'])
{


	require("./pop3/pop3.class.inc");

	// Constructor
	// optional
	$apop_detect = TRUE;    // default = FALSE
	$log = TRUE;            // default = FALSE
	$log_file = "pop3.class.log"; // must be set when $log = TRUE !!!
	$qmailer = FALSE;

	$username = $_GET['username'];
	$password = $_GET['password'];
	$server = $_GET['mail_server'];
	$port = $_GET['port'];
	

	// Your own free Vars
	// Save to MySQL ??
	$savetomysql = FALSE;
	$savetofile = FALSE;
	$delete = FALSE;


	$pop3 = new POP3($log,$log_file,$apop_detect);

	if($pop3->connect($server)){
		if($pop3->login($username,$password)){
			if(!$msg_list = $pop3->get_office_status()){
				echo $pop3->error;
				return;
			}
		}else{
			echo $pop3->error;
			return;
		}
	}else{
		echo $pop3->error;
		return;
	}



	$noob = TRUE;

	for($i=1;$i<=$msg_list["count_mails"];$i++)
	{
		if(!$header = $pop3->get_top($i)){
			echo $pop3->error;
		}
		// Get Message ID and set $unique_id for save2file()
			$g = 0;
			while(!ereg("</HEADER>",$header[$g])){
				if(eregi("Message-ID",$header[$g])){
					$unique_id = md5($header[$g]);
				}
				$g++;
			}
		unset($g);
		
		$query = 'SELECT `unique_id` FROM `'.$msg_table.'` WHERE 1 AND `unique_id` = \''.$unique_id.'\' LIMIT 0, 1';
		//$result = mysql_query($query,$db["link"]) or die(mysql_error());
		
		//mysql_free_result($result);
		//unset($rows);
		
		
		
		






		if($get_msg){
			if(!$message = $pop3->get_mail($i, $qmailer)){
				echo $pop3->error;
				$savetofile = FALSE;
				$savetomysql = FALSE;
				$delete = FALSE;
			}
		}


		
		// Save to File !!!
		if($savetofile){



			$filename = ".//mails//".$unique_id.".txt";

			if(!is_file($filename)){
			if(!$filesize = $pop3->save2file($message,$filename)){
				echo $pop3->error;
				return;
			}else{
				echo "File saved to ".$filename." (".$filesize." Bytes written) !! \r\n <br>";
			}
			}else{
				echo "File <b>(".$filename.")</b> already exists. !! \r\n <br>";
			}
		}
		
		// Save to MySQL
		if($savetomysql){

			if($count_bytes = $pop3->save2mysql($message,$db["link"],$db["dir_table"],$db["msg_table"])){
				echo "File save to MySQL complete. (".$count_bytes." Bytes written) !! \r\n <br>";
			}else{
				echo $pop3->error;
				return;
			}
		}
		
		// Send Noob command !!
		if($noop){
			if(!$pop3->noop()){
				echo $pop3->error;
				$noob = FALSE;
			}
		}
		
		// Delete MSG
		if($delete){
			if($pop3->delete_mail($i)){
				echo "Nachricht als gelöscht markiert !!! \r\n <br>";
			}else{
				echo $pop3->error;
			}
		}


		
	}
	if($msg_list["count_mails"] == "0"){
		echo "Could not detect any messages.";
	}

	//echo count($msg_list);
	$i = 1;
	foreach ($msg_list as $k=>$v)
	{
		$mid = $i;
		$uid = $pop3->uidl($mid);
		$uid = $uid[$i];
		if ($uid)
		{
			$message = $pop3->get_mail($mid);
			$message =  implode('',$message);
			
			$message = str_replace(array("\n","\r"),"=^^",$message);
			$message = str_replace('==^^','',$message);
			$message = str_replace('=^^','',$message);
			//echo $message;exit;
			$message = str_replace('3D','',$message);
			$message = str_replace('=E2=80=93',':',$message);
			$message = str_replace('=E2=80=99',"'",$message);
			
			$subject = explode('Subject:', $message);
			$subject = $subject[1];
			$subject = explode('From:', $subject);
			$subject = $subject[0];
			$subject = trim($subject);
			
			//echo $subject;exit;
			if (strstr($message,'<MESSAGE>'))
			{
				$message = explode('<MESSAGE>',$message);
				$message = $message[1];
			}
			if (strstr($message, '<html>'))
			{
				$message = explode('<html>',$message);
				$message = $message[1];
				$message = explode('</html>',$message);
				$message = $message[0];
				//echo $message;exit;
			}
			if (strstr($message, '<body>'))
			{
				$message = explode('<body>',$message);
				$message = $message[1];
				$message = explode('</body',$message);
				$message = $message[0];
				//echo $message;exit;
			}

			$message = trim($message);
			$subject = trim($subject);
			
			$unique_ids[] = $uid;
			$title[] = $subject;
			$description[] = $message;	
		}
		$i++;
	}

	$pop3->close();


	//krsort($description);
	//krsort($title);
	
	echo "<?xml version='1.0' ?>\n<rss version='2.0'>\n<channel>\n";
	echo "<title>Latest Items for $username</title>\n";
	echo "<link>mailto:$username</link>\n";
	echo "<description>BlogSense Email Campaign Feed for $username</description>\n";
	//echo "<item></item>";
	foreach ($title as $key=>$value)
	{
		echo "<item>\n";
		echo "<![CDATA[ {$this_json} ]]>";
		echo "</item>\n";
	}
	echo "\n";
}
else
{
	?>
		<form action='' method='GET'>
		<table width="335" style="margin-left: auto; margin-right: auto; padding: 5px; border: 3px solid rgb(238, 238, 238);"> 
			  <tbody>
				<tr>
					 <td align="center" colspan=2 style="font-size: 13px;">
							<i>This RSS generator may not work with gmail or other emails outside of your local server due to your host's firewall settings.</i>
					 </td>
				</tr>
				<tr>
					 <td align="left" style="font-size: 13px;">
						POP Username: 
					 </td>
					 <td align="right" style="font-size: 13px;">
						<input name='username' size=20>
					 </td>
				</tr>
				<tr>
					 <td align="left" style="font-size: 13px;">
						POP Password: 
					 </td>
					 <td align="right" style="font-size: 13px;">
						<input name='password' size=20>
					 </td>
				</tr>
				<tr>
					 <td align="left" style="font-size: 13px;">
						POP Mailserver: 
					 </td>
					 <td align="right" style="font-size: 13px;">
						<input name='mail_server' size=20 >
					 </td>
				</tr>
				<tr>
					 <td align="left" style="font-size: 13px;">
						POP PORT: 
					 </td>
					 <td align="right" style="font-size: 13px;">
						<input name='port' size=20 value=110>
					 </td>
				</tr>
				<tr>
					 <td align="center" colspan=2 style="font-size: 13px;">
							<input type=submit value='generate feed'>
					 </td>
				</tr>
			</tbody>
		</table>
		</form>
	
	<?php
	
}
?>
