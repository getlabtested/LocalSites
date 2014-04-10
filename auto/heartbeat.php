<?php
include_once('../wp-config.php');
session_start();
require_once ('../wp-blog-header.php');
require_once ('../wp-includes/registration.php');

//check for multisite
$bid = $_GET['blog_id'];
$_COOKIE['bs_blog_id'] = $bid;
$debug = $_GET['debug'];
if (function_exists('switch_to_blog')) switch_to_blog($bid);
include_once('includes/helper_functions.php');
$timezone_format = _x('Y-m-d G:i:s', 'timezone date format');
$wordpress_date_time =  date_i18n($timezone_format);

//fix broken
if ($debug==1)
{
	$query = "UPDATE ".$table_prefix."blogsense SET `option_value`='0' WHERE option_name='blogsense_cron_primer'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); }
		
	$query = "UPDATE ".$table_prefix."blogsense SET `option_value`='0' WHERE option_name='blogsense_cron_running'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); }
}

//get current primer and safety variables
$query = "SELECT option_value FROM ".$table_prefix."blogsense WHERE `option_name` IN (";
$query .= "'blogsense_blog_url' ,";
$query .= "'blogsense_cron_days' ,";
$query .= "'blogsense_cron_email' ,";
$query .= "'blogsense_cron_hours' ,";
$query .= "'blogsense_cron_minutes' ,";
$query .= "'blogsense_cron_months' ,";
$query .= "'blogsense_cron_primer' ,";
$query .= "'blogsense_cron_running' ,";
$query .= "'blogsense_cron_running_safety_count' , ";
$query .= "'blogsense_cron_timeout' , ";
$query .= "'blogsense_cron_weekdays'  ) ORDER BY option_name ASC";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error(); }

while ($arr = mysql_fetch_array($result))
{
	$array[] = $arr[0];
}
//print_r($array);exit;
$blog_url = $array[0];
$cronjob_days = $array[1];
$cronjob_email = $array[2];
$cronjob_hours = $array[3];
$cronjob_minutes = $array[4];
$cronjob_months = $array[5];
$primer = $array[6];
$running = $array[7];
$running_safety_count = $array[8];
$running_timeout = $array[9];
$cronjob_weekdays = $array[10];
//echo $primer;exit;
//echo $running;exit;

set_time_limit($cronjob_timeout);

if ($running_safety_count>3)
{
	//echo 1; exit;
	$query = "UPDATE ".$table_prefix."blogsense SET `option_value`='0' WHERE option_name='blogsense_cron_primer'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); }
		
	$query = "UPDATE ".$table_prefix."blogsense SET `option_value`='0' WHERE option_name='blogsense_cron_running'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); }
	
	$query = "UPDATE ".$table_prefix."blogsense SET `option_value`='0' WHERE option_name='blogsense_cron_running_safety_count'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); }
}

if ($running!=1)
{

	//mail('atwell.publishing@gmail.com','hello test','gello goeooo');
	//class to check if script is already running
	class pid {

		protected $filename;
		public $already_running = false;
	   
		function __construct($directory) {
		   
			$this->filename = $directory . '/' .  basename($_SERVER['PHP_SELF']) . '.pid';
		   
			if(is_writable($this->filename) || is_writable($directory)) {
			   
				if(file_exists($this->filename)) {
					$pid = (int)trim(file_get_contents($this->filename));
					if(posix_kill($pid, 0)) {
						$this->already_running = true;
					}
				}
			   
			}
			else {
				//die("Cannot write to pid file '$this->filename'. Program execution halted.\n");
			}
		   
			if(!$this->already_running) {
				$pid = getmypid();
				file_put_contents($this->filename, $pid);
			}
		   
		}

		public function __destruct() {

			if(!$this->already_running && file_exists($this->filename) && is_writeable($this->filename)) {
				unlink($this->filename);
			}
	   
		}
	   
	}


	$heartbeat = 'on';

	//echo $cron_months;exit;
	//echo $cron_weekdats;exit;
	//echo $cron_days;exit;
	//echo $cron_hours;exit;

	$t_month = date(n);
	$t_weekday = date(w);
	$t_day = date(j);
	$t_hour = date(G);
	$t_minute = date(i);

	$hours_other = array();
	$hours_four = array();
	$hours_six = array();
	$hours_eight = array();

	$min_other = array();
	$min_five = array();
	$min_ten = array();
	$min_fifteen = array();

	if ($cronjob_hours=='*/2')
	{
		$hours_other = array('1','3','5','7','9','11','13','15','17','19','21','23');
	}
	if ($cronjob_hours=='*/4')
	{
		$hours_four = array('0','4','8','12','16','20');
	}
	if ($cronjob_hours=='*/6')
	{
		$hours_six = array('0','6','12','18');
	}
	if ($cronjob_hours=='*/8')
	{
		$hours_eight = array('0','8','16');
	}


	if ($cronjob_minutes=='*/2')
	{
		$min_other = array('1','3','5','7','9','11','13','15','17','19','21','23','25','27','29','31','33','35','37','39','41','43','45','47','49','51','53','55','57','59');
	}
	if ($cronjob_minutes=='*/5')
	{
		$min_five = array('0','5','10','15','20','25','30','35','40','45','50','55');
	}
	if ($cronjob_minutes=='*/10')
	{
		$min_ten = array('0','10','20','30','40','50');
	}
	if ($cronjob_minutes=='*/15')
	{
		$min_fifteen = array('0','15','30','45');
	}




	$gateway_1 = 0;
	$gateway_2 = 0;
	$gateway_3 = 0;
	$gateway_4 = 0;
	$gateway_5 = 0;

	//begin to construct query
	if ($cronjob_months=="*"||$cronjob_months==$t_month)
	{
		$gateway_1= 1;
	}

	if ($cronjob_weekdays=="*"||$cronjob_weekdays==$t_weekday)
	{
		$gateway_2 = 1;
	}

	if ($cronjob_days=="*"||$cronjob_days==$t_day)
	{
		$gateway_3 = 1;
	}

	if ($cronjob_hours=="*"||$cronjob_hours==$t_hour||array_search($t_hour, $hours_other)||array_search($t_hour, $hours_four)||array_search($t_hour, $hours_six)||array_search($t_hour, $hours_eight))
	{
		$gateway_4 = 1;
	}

	if ($cronjob_minutes=="*"||$cronjob_minutes==$t_minute||array_search($t_minute, $min_five)||array_search($t_minute, $min_ten)||array_search($t_minute, $min_fifteen))
	{
		$gateway_5 = 1;
	}

	//determine if there has been a missed cronjob
	if ($gateway_1==1&&$gateway_2==1&&$gateway_3==1&&$gateway_4==1&&$gateway_5==0)
	{
		if (is_numeric($cronjob_minutes))
		{
			if ($t_minute<$cronjob_minutes)
			{
				$query = "UPDATE ".$table_prefix."blogsense SET `option_value`='1' WHERE option_name='blogsense_cron_primer'";
				$result = mysql_query($query);
				if (!$result){echo $query; echo mysql_error(); }
			}
			if ($t_minute>$cronjob_minutes&&$primer==1)
			{
				$late_fire=1;
			}	
		}
	}

	if ($gateway_1==1&&$gateway_2==1&&$gateway_3==1&&$gateway_4==0)
	{
		if ($cronjob_hours=='*/4')
		{
			$l_hour = $t_hour - 1 ; 
			
			if (in_array($l_hour, $hours_four))
			{
				if ($primer==1)
				{
					$late_fire=1;
				}	
			}
		}
		if ($cronjob_hours=='*/6')
		{
			$l_hour = $t_hour - 1 ; 
			
			if (in_array($l_hour, $hours_six))
			{
				if ($primer==1)
				{
					$late_fire=1;
				}	
			}
		}
		if ($cronjob_hours=='*/8')
		{
			$l_hour = $t_hour - 1 ; 
			
			if (in_array($l_hour, $hours_eight))
			{
				if ($primer==1)
				{
					$late_fire=1;
				}	
			}
		}
		if (is_numeric($cronjob_hours))
		{
			if ($t_hour>$cronjob_hours&&$primer==1)
			{
				$late_fire=1;
			}	
		}
	}
	//run query and collect results

	if ($gateway_1==1&&$gateway_2==1&&$gateway_3==1&&$gateway_4==1&&$gateway_5==1||$debug==1||$late_fire==1)
	{
		//set database to running status
		$query = "UPDATE ".$table_prefix."blogsense SET `option_value`='1' WHERE option_name='blogsense_cron_running'";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error(); }
		
	
		//execute cronjob
		$pid = new pid('/tmp');
		if($pid->already_running) {
			echo "Already running.\n";
			exit;
		}
		else {
			echo "Running...\n";
		}

		$blogsense_url = blogsense_url();
		$cron_config = $blogsense_url."cron_config.php?blog_id=$bid";
		//echo $cron_config;exit;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $cron_config);
		if (!ini_get('open_basedir') && !ini_get('safe_mode'))
		{
				curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		}	
		curl_setopt($ch, CURLOPT_HEADER, false);	
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('heartbeat' => 1));	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $cronjob_timeout);
		$curl_result = curl_exec($ch);
		//print_r(curl_getinfo($ch));
		curl_close ($ch);
		
		$query = "UPDATE ".$table_prefix."blogsense SET `option_value`='0' WHERE option_name='blogsense_cron_primer'";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error(); }
		
		$query = "UPDATE ".$table_prefix."blogsense SET `option_value`='0' WHERE option_name='blogsense_cron_running'";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error(); }
		
		$running_saftey_count++;
		$query = "UPDATE ".$table_prefix."blogsense SET `option_value`='0' WHERE option_name='blogsense_cron_running'";
		$result = mysql_query($query);
		if (!$result){echo $query; echo mysql_error(); }
	
		
		$headers = "MIME-Version: 1.0\n" ;
		$headers .= "Content-Type: text/html; charset=\"iso-8859-1\"\n"; 
		if ($cronjob_email)
		{
			mail($cronjob_email,"Cronjob Report - BlogSense - $blog_url", $curl_result, $headers);
		}
	}
	else
	{
		echo "<u>Gateways:</u><br>";
		echo $gateway_1." - $cronjob_months - $t_month<br>";
		echo $gateway_2." - $cronjob_weekdays - $t_weekday<br>";
		echo $gateway_3." - $cronjob_days - $t_day<br>";
		echo $gateway_4." - $cronjob_hours - $t_hour<br>";
		echo $gateway_5." - $cronjob_minutes - $t_minute<br>";
	}

	//post bookmarks.
	include_once('functions/f_run_bookmarks.php');
	
}//if running is not on
else
{
	$running_safety_count++;
	$query = "UPDATE ".$table_prefix."blogsense SET `option_value`='$running_safety_count' WHERE option_name='blogsense_cron_running_safety_count'";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); }
}

?>
<html>
<body>
</body>
</html>