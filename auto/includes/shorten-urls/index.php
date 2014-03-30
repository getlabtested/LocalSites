<?php
include_once('./../../../wp-config.php');

session_start();

include("./../../functions/f_login.php");
if(checkSession() == false)
blogsense_redirect("./../../login.php");

include_once("../helper_functions.php");
include_once('../prepare_variables.php');

set_time_limit(500);


function shorten_shorten_url($link, $nature)
{
	global $bookmarking_bitly_apikey;
	global $bookmarking_bitly_username;
	global $proxy_campaigns;
	
	//echo $bookmarking_bitly_apikey;exit;
	if ($nature=='bit.ly')
	{
		if ($bookmarking_bitly_apikey)
		{
			$result = quick_curl("http://api.bit.ly/shorten?version=2.0.1&longUrl=$link&login=$bookmarking_bitly_username&apiKey=$bookmarking_bitly_apikey",$proxy_campaigns);
			$short_url = get_string_between($result, 'shortCNAMEUrl": "', '"');
			//echo $short_url;exit;
		}
	}
	
	if ($nature=='goog.gl')
	{
		$data['longUrl'] = $link;
		$data = json_encode($data);
		$result = stealth_curl("https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyDBaB4T9WN6auQrAcG1GyOYaaAOl9Kf2K0",$proxy_campaigns,$data,'goog.gl');
		$result = json_decode($result,true);
		$short_url = $result['id'];
		//echo $short_url;
	}
	
	if ($nature=='tiny.url')
	{
		$result = quick_curl("http://tinyurl.com/api-create.php?url=$link",0);
		$short_url = $result;
	}

	if (!strstr($short_url,'http'))
	{
		$short_url = $link;
	}
	
	return $short_url;
}



//$url = "http://www.google.com";
//echo urlcover($url); exit;

if ($_POST['url_list'])
{
	$url_list = $_POST['url_list'];
	$nature = $_POST['nature'];
	$url_list = explode("\n",$url_list);
	$nature_array = array('goog.gl','bit.ly');
	
	echo "<pre>";
	foreach ($url_list as $key=>$val)
	{
		if (strstr($val,';'))
		{
			$delemit = ";";
			$this_array = explode(';',$val);
			$val = $this_array[0];
			$end = $this_array[1];
		}
		if (strstr($val,'{'))
		{
			$delemit = "{";
			$this_array = explode('{',$val);
			$val = $this_array[0];
			$end = $this_array[1];
		}
		if ($nature=='mix')
		{
			$rand_key = array_rand($nature_array);
			$this_nature = $nature_array[$rand_key];
			
			$val =  shorten_shorten_url($val,$this_nature);
			if ($end)
			{
				$val = $val.$delemit.$end;
			}
			echo  $val;
			echo "\n";
			unset($this_nature);

		}
		else
		{
			$val =  shorten_shorten_url($val,$nature);
			if ($end)
			{
				$val = $val.$delemit.$end;
			}
			echo  $val;
			echo "\n";
		}
	}
	echo "</pre>";
	
}
else
{
	?>
	<form action='' method=POST>
		
		URLs to Shortnen:<br>
		<textarea name='url_list'style='width:400px;height:500px' wrap='off'></textarea><br>
		<select name='nature'>
			<option value='goog.gl'>Goog.gl</option>
			<option value='bit.ly'>Bit.ly (Set credentials in Bookmarking Section)</option>
			<option value='tiny.url'>Tiny.url </option>
			<option value='mix'>Mix</option>
			
		</select><br>
		<input type='submit' value='Generate Shortened URLS'>
		
		
	</form>
	<br><i>please allow 5 minutes for list generation for large lists<br>
	This script accepts the Scrapebox format: http://www.url.com/ {keyword|keyword}<br>
	This script also accepts the following format: http://www.url.com/;keyword,keyword
	</i>
	<?php
}
?>