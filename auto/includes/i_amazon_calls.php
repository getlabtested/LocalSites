<?php

$AWSAccessKeyId = trim($_GET['aws_access_key']);
$SecretAccessKey = trim($_GET['secret_access_key']);
$SecretAccessKey = str_replace(' ','+', $SecretAccessKey);
//$AWSAccessKeyId ="7AKIAJPM37ILEI7FX3RJA";
//$SecretAccessKey ="anp3FxC9JT4UwGm584WuMjPvLZ+EOM8E6Au/3Yz07";

$ItemId = $_GET['asin'];
$locale = $_GET['locale']; // ASIN
$pre = 'webservices';
$Timestamp = gmdate("Y-m-d\TH:i:s\Z");
$Timestamp = str_replace(":", "%3A", $Timestamp);
$ResponseGroup = "ItemAttributes,Images,EditorialReview";
$ResponseGroup = str_replace(",", "%2C", $ResponseGroup);

$String = "AWSAccessKeyId=$AWSAccessKeyId&ItemId=$ItemId&Operation=ItemLookup&ResponseGroup=$ResponseGroup&Service=AWSECommerceService&Timestamp=$Timestamp&Version=2009-01-06";


$Prepend = "GET\n";
$Prepend .= "$pre.amazon.$locale\n";
$Prepend .= "/onca/xml\n";
$PrependString = $Prepend . $String;

$Signature = base64_encode(hash_hmac("sha256", $PrependString, $SecretAccessKey, True));  
$Signature = str_replace("+", "%2B", $Signature);
$Signature = str_replace("=", "%3D", $Signature);

$BaseUrl = "http://$pre.amazon.$locale/onca/xml?";
$SignedRequest = "{$BaseUrl}{$String}&Signature={$Signature}";


function quick_curl($link)
{
	$agents[] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; Media Center PC 5.0)";
	$agents[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
	$agents[] = "Opera/9.63 (Windows NT 6.0; U; ru) Presto/2.1.1";
	$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5?";
	$agents[] = "Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.18) Gecko/20081203 Firefox/2.0.0.18";
	$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16";
	$agents[] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1";
	 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_HEADER, false);
	//echo ini_get('open_basedir');exit;
	//print_r(ini_get('safe_mode'));exit;
	if (!ini_get('open_basedir') && !ini_get('safe_mode'))
	{
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	}
   // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_USERAGENT, $agents[rand(0,(count($agents)-1))]);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

header('Content-type: text/xml');
header('Pragma: public');        
header('Cache-control: private');
header('Expires: -1');

$XML  = quick_curl($SignedRequest);

echo $XML;
//echo $SignedRequest;

?>