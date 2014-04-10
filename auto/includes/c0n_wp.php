<?php
include_once('../wp-config.php');
$query = "SELECT `option_name`, `option_value` FROM ".$table_prefix."blogsense WHERE `option_name` IN (";
$query .= "'blogsense_activation_key' ,";
$query .= "'blogsense_activation_email' )";
$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error();exit;}
$count = mysql_num_rows($result);

for ($i=0;$i<$count;$i++)
{
  $arr = mysql_fetch_array($result);
  if ($i==1){$key =$arr[option_value];}
  if ($i==0){$email =$arr[option_value];} 
}

//In case of a major piracy outbreak (widespread piracy outside of TV) the developer holds right to booby trap all tampered installs to the loss of the unauthorized users. 
//For this reason please place this disclaimer whereever blogsense is shared illegally and ask them not to share in high-traffic-public communities or they may ruin it for everyone and injure their business model. 
//BlogSense users running untampered versions of BlogSense experience 0 risk of this possibility. 
$wordpress_url = get_bloginfo('url');
$ch = curl_init();
$query = "http://www.hatnohat.com/api/blogsense/validate.php?key=$key&email=$email";
curl_setopt($ch, CURLOPT_URL, $query);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array('url' => $wordpress_url));	
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
curl_close ($ch);		
	
if ($output!=1)
{
    echo $output;
	exit;
}
else
{
	//echo $query;exit;
}	
?>