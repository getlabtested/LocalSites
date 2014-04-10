<?php
include_once('./../../../wp-config.php');
$mode = $_GET['mode'];

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

function get_string_between($string, $start, $end)
{
   $string = " ".$string;
     $ini = strpos($string,$start);
     if ($ini == 0) return "";
     $ini += strlen($start);   
     $len = strpos($string,$end,$ini) - $ini;
     return substr($string,$ini,$len);
}
function files_in_directory($start_dir)
{
     //returns array of files in directory
     $files = array();
     $dir = opendir($start_dir);
     while(($myfile = readdir($dir)) !== false)
     {
         if($myfile != '.' && $myfile != '..' && !is_file($myfile) && $myfile != 'resource.frk' && !eregi('^Icon',$myfile) )
         {
             $files[] = $myfile;
         }
     }
     closedir($dir);
     return $files;
}


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
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_USERAGENT, $agents[rand(0,(count($agents)-1))]);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}



if ($mode=='loc')
{
   $loc1 = $_GET['loc'];
   $loc2 = str_replace('./../','./../../', $loc1);
   $files = files_in_directory($loc2);
   $current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
   
   if (substr($loc1,0,2)=='./')
   {	 
	 $count = substr_count($loc2, '../');
	 $current_url = str_replace('http://', '' ,$current_url);
	 $chopped = explode('/',$current_url);
	 $diff = count($chopped)-$count;
	 $diff=$diff-1;;
	 $rebuilt = "";
	 foreach ($chopped as $k=>$v)
	 {
	   if ($k<$diff)
	   {
	    $rebuilt .="$v/";
	   }
	 }
	 $full_url = "http://$rebuilt";
	 $loc3 = substr($loc2,2);
	 $loc3 = str_replace('../', '', $loc3);
   }
   
   if (substr($loc3,0,-1)!='/')
   {
       $loc3 = "$loc3/";
   }
    echo '<br><br><br><br><center><img src="./../nav/refresh.png" style="cursor:pointer;" id=id_image_button_display_local></center>';
   echo '<br>';
   echo '<br><div id="grid_slider">
						<div class="ui-slider-handle"></div>
		</div><br><br>';
					
   echo "<div id=id_photos>";	
   foreach ($files as $k=>$v)
   {
        $j = $k+1;
        //if ($j%8==0){echo "<br>";}
		echo "<img src='$full_url$loc3$v' class='grid_pic' id=id_grid_pic_$k style='max-width:100;max-height:100;cursor:pointer;' title='Click to Toggle Select.' border=3 />&nbsp;";

	}
	echo '</div>';

}

if ($mode=='flickr')
{
   $keywords = $_GET['keywords'];
   $max_results = $_GET['max_results'];
   $keywords = str_replace(' ','+',$keywords);
   $url = "http://www.degraeve.com/flickr-rss/rss.php?tags=$keywords&tagmode=all&sort=interestingness-desc&num=$max_results";
   //echo $url; exit;
   $string = quick_curl($url);
   //echo $string;exit;
   $remove = get_string_between($string,"<?xml","<item>");
   $string = str_replace($remove,'',$string);
   $count = substr_count($string, '<guid>');
   //echo $string;exit;
   //echo $count;exit;
   for($i=0;$i<$count;$i++)
   {
      $links[] = get_string_between($string,"<guid>","</guid>");
	  $string = str_replace_once("<guid>","", $string); 
   }
   
   echo '<br><br><center><img src="./../nav/refresh.png" style="cursor:pointer;" id=id_image_button_display_flickr></center>';
   echo '<br><div id="grid_slider">
						<div class="ui-slider-handle"></div>
		</div><br><br>';
   echo "<div id=id_photos>";
   if ($links)
   {
	   foreach ($links as $k=>$v)
	   {
			$j = $k+1;
			//if ($j%8==0){echo "<br>";}
			echo "<img src='$v' class='grid_pic' id=id_grid_pic_$k style='max-width:100;max-height:100;cursor:pointer;' title='Click to Toggle Select.' border=3 />&nbsp;";

		}
		
	}
	else
	{
	    echo "<center><i>sorry no results.</i></center>";
	}
	echo '</div>';

}

if ($mode=='picasa')
{
   $keywords = $_GET['keywords'];
   $max_results = $_GET['max_results'];
   $keywords = str_replace(' ','+',$keywords);
   $url = "http://picasaweb.google.com/data/feed/base/all?alt=rss&kind=photo&max-results=$max_results&access=public&filter=1&q=$keywords&hl=en_US";
   //echo $url; exit;
   $string = quick_curl($url);
   //echo $string;exit;
   $remove = get_string_between($string,"<?xml","<item>");
   $string = str_replace($remove,'',$string);
   $count = substr_count($string, "<enclosure type='image/jpeg' url='");
   //echo $string;exit;
   //echo $count;exit;
   for($i=0;$i<$count;$i++)
   {
      $links[] = get_string_between($string,"<enclosure type='image/jpeg' url='","'");
	  $string = str_replace_once("<enclosure type='image/jpeg' url='","", $string); 
   }
   
   echo '<br><br><center><img src="./../nav/refresh.png" style="cursor:pointer;" id=id_image_button_display_picasa></center>';
   echo '<br><div id="grid_slider">
						<div class="ui-slider-handle"></div>
		</div><br><br>';
   echo "<div id=id_photos>";
    if ($links)
   {
	   foreach ($links as $k=>$v)
	   {
			$j = $k+1;
			//if ($j%8==0){echo "<br>";}
			echo "<img src='$v' class='grid_pic' id=id_grid_pic_$k style='max-width:100;max-height:100;cursor:pointer;' title='Click to Toggle Select.' border=3 />&nbsp;";

		}
	}
	else
	{
	    echo "<center><i>sorry no results.</i></center>";
	}
	echo '</div>';

}

if ($mode=='youtube')
{
   $keywords = $_GET['keywords'];
   $max_results = $_GET['max_results'];
   $keywords = str_replace(' ','+',$keywords);
   $url = "http://gdata.youtube.com/feeds/base/videos?max-results=$max_results&q=$keywords&key=AI39si6RmbtB6goYpu0MrGKmEeEhg5dIOSdZUClTencT6F_Saf3Wjqp9y55xoJ1PAa_htlx3ArxozpuNiG-jdWzNxMAV-NhvKw";
   //echo $url; exit;
   $string = quick_curl($url);
   $string =  htmlspecialchars_decode($string);
   //echo $string;exit;
   $remove = get_string_between($string,"<feed xmlns=","</generator>");
   $string = str_replace($remove,'',$string);
   $count = substr_count($string, "<title type='text'>");
   //echo $string;exit;
   //echo $count;exit;
   for($i=0;$i<$count;$i++)
   {

	  $titles[] = get_string_between($string,"<title type='text'>","</title>");
	  $links[] = get_string_between($string,"<link rel='alternate' type='text/html' href='","'/>");
	  $descriptions[] = get_string_between($string,"<content type='html'>","From:");
	  $thumbnails[] = get_string_between($descriptions[$i], 'src="', '"');
	  $string = str_replace_once("<title type='text'>","", $string); 
	  $string = str_replace_once("<link rel='alternate' type='text/html'","", $string); 
      $string = str_replace_once("<content type='html'>","", $string); 
	  
	  //echo $titles[$i];exit;
	  //echo $links[$i];exit;
	  //echo $thumbnails[$i];exit;

   }
   
   echo '<br><br><center><img src="./../nav/refresh.png" style="cursor:pointer;" id=id_video_button_display></center>';
   echo '<br><br>';
   echo "<div id=id_photos>";
    if ($links)
   {
	   foreach ($links as $k=>$v)
	   {
			$j = $k+1;
			//if ($j%8==0){echo "<br>";}
			echo "<div style='font-size:10px;width:300px;'><img src='$thumbnails[$k]' class='grid_pic' alt='$links[$k]' id=id_grid_pic_$k style='max-width:100;max-height:100;cursor:pointer;border-color:#004fa1' title='Click to Toggle Select.' border=3 /><br><center><br><a href='$links[$k]' target=_blank title='preview video in new window'>$titles[$k]</a></center><br><hr></div><br>";

		}
	}
	else
	{
	    echo "<center><i>sorry no results.</i></center>";
	}
	echo '</div>';

}