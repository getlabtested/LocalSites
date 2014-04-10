<?php

$pdf = $_POST['pdf'];
//$pdf = "http://www.pubwire.com/DownloadDocs/AFABLES.PDF";
$google_cache_url = "http://74.125.47.132/search?q=cache:";
$prepared_url = "$google_cache_url$pdf";

//get pdf filename and remove extension
$base = basename($pdf);
$parts = explode(".", $base);
$pdf_filename = $parts[0];

//get html version of pdf into string
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$prepared_url");
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
$string = curl_exec($ch);
curl_close($ch);


if (strstr($string, "did not match any documents."))
{
echo "this file not work with blogsense.";
exit;
}
//echo $string; exit;
//helper functions///////////////////
function get_string_between($string, $start, $end)
{
     $string = " ".$string;
     $ini = strpos($string,$start);
     if ($ini == 0) return "";
     $ini += strlen($start);   
     $len = strpos($string,$end,$ini) - $ini;
     return substr($string,$ini,$len);
}
function url_exists($url) {
    // Version 4.x supported
    $handle   = curl_init($url);
    if (false === $handle)
    {
        return false;
    }
    curl_setopt($handle, CURLOPT_HEADER, false);
    curl_setopt($handle, CURLOPT_FAILONERROR, true);  // this works
    curl_setopt($handle, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // request as if Firefox   
    curl_setopt($handle, CURLOPT_NOBODY, true);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, false);
    $connectable = curl_exec($handle);
    curl_close($handle);  
    return $connectable;
}
function str_replace_once($remove , $replace , $string)
{
	// Looks for the first occurence of $needle in $haystack
	// and replaces it with $replace.
	$pos = strpos($string, $remove);
	if ($pos === false) 
	{
	// Nothing found
	return $haystack;
	}
	return substr_replace($string, $replace, $pos, strlen($remove));
}  
////////////////////////////////////
    

if (!url_exists($pdf)&&$_POST['m']!=3)
{
 echo "Invalid PDF Url."; exit;
}
else
{
	 if ($_POST['m']==1)
	 {
			echo $string;
	 }
	 if ($_POST['m']==2)
	 {
		$page_count = substr_count($string, "<a name=");
		//echo $page_count; exit;
		$pdf = urlencode($pdf);
		header("Location: pdf_import.php?s=2&p=$page_count&pdf=$pdf");
		exit;
	 }
	 if($_POST['m']==3)
	 {
		//get titles
		$post_titles = $_POST['post_titles'];		
		$post_page_begin = $_POST['post_page_begin'];
		$post_page_end = $_POST['post_page_end'];
		
		//chop up source code by page
		$page_count = substr_count($string, "<a name=");
		
		for ($i=0; $i<$page_count;$i++)
		{
		 $j = $i+1;
		 $k = $j+1;
		 $start = "<a name=$j>";
		 if (strstr($string, "<a name=$k>"))
		 {
			$end = "<a name=$k>";
		 }
		 else
		 {
			$end = "</body>";
		 }
		 $page_content[$i] = get_string_between($string, $start, $end);
		 //if ($i==1) {echo $page_content[1]; exit;	}
		 
		 $remove = "$start$page_content[$i]";
		 $string = str_replace_once($remove, "", $string);
		 //if ($i==1) {echo $page_content[1]; exit;	}
		 $page_content[$i] = str_replace_once("Page $j", "", $page_content[$i]); 
		 //if ($i==1){ echo $page_content[1]; exit;		}
		 $page_content[$i]  = strip_tags($page_content[$i] , '<b><h1><h2><h3><p><br><h3><li><ul><span><img><font><i>');
		 $page_content[$i] = str_replace("\n", " ", $page_content[$i]); 
		 //if ($i==1){ echo $page_content[1]; exit;	}
		}
	    
		if ($_POST['import'])
		{
			foreach ($post_titles as $key=>$value)
			{
				$title = $post_titles[$key];
				$content = "";
				$post_page_begin = $post_page_begin[$key] -1;
				$post_page_end = $post_page_end[$key] -1;
				for ($i=$post_page_begin;$i<=$post_page_end;$i++)
				{
					$content .= $page_content[$i]."<br><br>";
				}		
			  
				//create folder in my-articles if does not exits
				$path = "./my-articles/$pdf_filename";
				if (!file_exists($path)) 
				{
					mkdir($path,0755);
				}	

				//final preparations on document writing
				$final_content = "<title>$post_titles[$key]</title>\n\n";
				$final_content .= "$content";
				
				$open = fopen($path."/$key.txt" , "w");
				fwrite($open, $final_content);
				fclose($open);     
				
			}
			header("Location: pdf_import.php?s=3");
			exit;
		}
		
		if ($_POST['preview'])
		{
		    $get_titles = implode(",", $post_titles);
		    $get_post_start = implode(",", $post_page_begin);
		    $get_post_end = implode(",", $post_page_end);
			
		    echo "<a href=\"pdf_import.php?s=2&p=$page_count&titles=$get_titles&starts=$get_post_start&ends=$get_post_end&pdf=$pdf\">Go Back</a>";
		    echo "<center><div width=100% style='text-align:left;'>";
			foreach ($post_titles as $key=>$value)
			{
				$title = $post_titles[$key];
				$content = "";
				$post_page_begin = $post_page_begin[$key] -1;
				$post_page_end = $post_page_end[$key] -1;
				//echo $page_content[0]; exit;
				for ($i=$post_page_begin;$i<=$post_page_end;$i++)
				{
					$content .= $page_content[$i]."<br><br>";
				}	
				
				echo "<h3>$title</h3><br><br>$content <br><hr><br><br>";
			}
			echo "</center></div>";
		}
	}

//foreach ($page_content as $k=>$v)
//	{
//	 echo "$page $k";
//	 echo $v;
//	}	

}