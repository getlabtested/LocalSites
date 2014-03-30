<?php
//******************************************************************************
//PHP CLASSES ******************************************************************
//******************************************************************************
require_once('i_typo_generator.php');
require_once ('utils.php');
ini_set('auto_detect_line_endings', true);

$nuprefix = explode('_',$table_prefix);
$nuprefix= $nuprefix[0]."_";

require_once(ABSPATH.'/wp-includes/class-IXR.php');

//pull spin setting variables
$query = "SELECT `option_name`, `option_value` FROM ".$table_prefix."blogsense WHERE `option_name` IN (";
$query .= "'blogsense_spin_phrase_max' ,";
$query .= "'blogsense_spin_phrase_min' )";

$result = mysql_query($query);
if (!$result){echo $query; echo mysql_error(); exit;}
$count = mysql_num_rows($result);

for ($i=0;$i<$count;$i++)
{
  $arr = mysql_fetch_array($result);
  if ($i==0){$max =$arr[option_value];}
  if ($i==1){$min =$arr[option_value];}
}

if( !defined( 'MIN_PHRASE_LENGTH' ) ) define( 'MIN_PHRASE_LENGTH', $min );
if( !defined( 'MAX_PHRASE_LENGTH' ) ) define( 'MAX_PHRASE_LENGTH', $max );

$tSTokens = $nuprefix . 'stokens';
$tBodyPhrases = $nuprefix . 'body_phrases';
$tReplacements = $nuprefix . 'replacements';

function spin_generate_workload_from( $phrases )
{
	global $tBodyPhrases;

	$rows = array();

	foreach( $phrases as $phrase )
	{
		$rows[] = sprintf( "('%s',%d,%d)", mysql_real_escape_string( $phrase->text ),
			$phrase->position, $phrase->length );
	}

	bs_query( "TRUNCATE TABLE $tBodyPhrases" );

	spin_batch_insert( "INSERT INTO $tBodyPhrases (`phrase`,`pos`,`len`) VALUES ", $rows );
}

//------------------------------------------------------------------------------------------
function spin_generate_all_possible_replacements()
{
	global $tReplacements, $tBodyPhrases, $tSTokens;

	bs_query( "TRUNCATE TABLE $tReplacements" );

	// define DONT_RANDOMIZE_REPLACEMENTS if you don't want the script to pick
	// a random replacement from the list of available phrases. this is handy
	// for testing/debugging
	$orderClause = defined( 'DONT_RANDOMIZE_REPLACEMENTS' )
		? 'p2.phrase desc'
		: 'rand()';

	bs_query
	( "
		insert into $tReplacements
		select
		   a.pos, a.len, p2.phrase
		from
		   $tBodyPhrases a
		   join $tSTokens p1 on p1.phrase=a.phrase
		   join $tSTokens p2 on p2.id=p1.id
		where
		   p2.phrase <> p1.phrase
		order by
		   $orderClause
	" );

}

function fetch_the_best_replacements()
{
	global $tReplacements;
	
	$q = bs_query
	( "
		select
			pos, len, phrase
		from
			$tReplacements
		group by
			pos, len
		order by
			pos asc, len desc
	" );

	$replacements = array();

	while( $row = mysql_fetch_object( $q ) )
	{
		//print_r($row);
		$replacements[] = $row;
	}

	//print_r($replacements);
	return $replacements;
}

function phrase_starts_with_capital_case( $phrase )
{
	return preg_match( '@^\p{Lu}\p{Ll}@', $phrase );
}

function phrase_is_all_capital_case( $phrase )
{
	return preg_match( '@^(\p{Lu}|\s)+$@', $phrase );
}

function  spin_this_text($text, $nature)
{
	global $spin_exclude_these;
	
	//prepare output
	$output = $text;

	//remove uninportant characters
	$text = preg_replace("/\p{P}(?<!')/", ' ', $text);

	//convert to utf8
	$text = spin_fix_the_encoding($text);

	//clean up any bad effects of utf8 encoding
	$text = spin_replace_t_characters($text);

	//this variable is my counter for successful finds
	$mcount = 0;

	//replace extra spaces.
	$text = trim( preg_replace( '@\s+@', ' ', $text ) );

	//create the array of ones
	$words = preg_split( '@ +@', $text, -1, PREG_SPLIT_NO_EMPTY );

	$phrases = spin_generate_phrases_from( $words, MIN_PHRASE_LENGTH, MAX_PHRASE_LENGTH );

	//print_r($phrases);exit;
// only bother if there's something to process
	if( sizeof( $phrases ) )
	{
		spin_generate_workload_from( $phrases );
		spin_generate_all_possible_replacements();

		$replacements = fetch_the_best_replacements();
		//print_r($replacements);
		$lastReplacement = null;
		$lastReplacementEnd = 0;

		foreach( $replacements as $replacement )
		{
			//echo $spin_exclude_these;exit;
			
			
			// skip less optimal replacements for already replaced phrases

			if( $lastReplacement && $replacement->pos < $lastReplacement->pos + $lastReplacement->len )
			{
				continue;
			}

			$originalPhrase = implode( ' ', array_slice( $words, $replacement->pos, $replacement->len ) );

			$needlePos = strpos( $output, $originalPhrase, $lastReplacementEnd );
			
			if (!stristr($spin_exclude_these,$originalPhrase))
			{
				//echo $originalPhrase.'<br>';
				if( $needlePos !== false )
				{
					if ($nature=='salt')
					{
						$replacementText = $replacement->phrase;
						$replacementText = bs_salt_string($replacementText);
					}
					else
					{
						$replacementText = $replacement->phrase;
					}

					if( $replacementText && phrase_starts_with_capital_case( $originalPhrase ) )
					{
						$replacementText[ 0 ] = strtoupper( $replacementText[ 0 ] );
					}
					else if( $replacementText && phrase_is_all_capital_case( $originalPhrase ) )
					{
						$replacementText = strtoupper( $replacementText );
					}
					
					
					$output = substr_replace( $output, $replacementText, $needlePos, strlen($originalPhrase ) );
				
					
					$lastReplacement = $replacement;
					$lastReplacementEnd = $needlePos + strlen( $replacement->phrase );
				}
			}
		}
	}
	
	//echo $output;exit;
	return $output;    
}

//******************************************************************************
//HELPER FUNCTIONS *************************************************************
//******************************************************************************
function str_replace_once($remove , $replace , $string)
{
	if (strlen($remove)>60000)
	{
		$string  = str_replace($remove,$replace,$string);
	}
	else if (strlen($string)>40000)
	{
		$parts = str_split($string, 40000);
		$remove = preg_quote($remove,'/');
		
		if (strstr($remove, '%wildcard%'))
		{
			$remove = str_replace('%wildcard%', '(.*?)', $remove);
		}
		
		
		foreach ($parts as $k=>$v)
		{
			$old = $parts[$k];
			if(preg_match('/'.$remove.'/si', $parts[$k],$matches)&&$done!=1)
			{
				$parts[$k] = preg_replace('/'.$remove.'/si', $replace, $parts[$k], 1); 
			}
			if ($parts[$k]!=$old)
			{
				$done=1;
			}
		}
		
		$parts = implode('',$parts);
		$return = $parts;
	}
	else
	{
		if (strstr($remove, '%wildcard%'))
		{
			$remove = preg_quote($remove,'/');
			$remove = str_replace('%wildcard%', '.*?', $remove);
		}
		else
		{
			$remove = preg_quote($remove,'/');
		}
		
		$return = preg_replace('/'.$remove.'/s', $replace, $string, 1); 
		if (!$return)
		{
			echo "str_replace_once fail"; 
			echo "<br><br> Here is the string:<br><br>$string";  
			EXIT;
		}
	}
	//echo "^";
	//echo "<hr>";
	return $return;
}  

function get_string_between($string, $start, $end) 
{

	if (strstr($start,'%wildcard%'))
	{
		$start = str_replace("%wildcard%", ".*?", preg_quote($start, "/"));
	}
	else
	{
		$start = preg_quote($start, "/");
	}
	
	if (strstr($end,'%wildcard%'))
	{
		$end = str_replace("%wildcard%", ".*?", preg_quote($end, "/"));
	}
	else
	{
		//echo $end;exit;
		$end = preg_quote($end, "/");
		//echo $end; exit;
	}
	
    $regex = "/{$start}(.*?){$end}/si";
	//echo $regex; 

	
    if (preg_match($regex, $string, $matches))
        return $matches[1];
    else
		//echo "<hr>";
		//echo $string; 
		//echo "<hr>";
		//echo $regex; 
		//echo "<hr>";
		//print_r($matches);
		//exit;
        return false;
}


function clean_cdata($input)
{
  if (strstr($input, "<![CDATA["))
  {
	$input = str_replace ('<![CDATA[','',$input);
	$input = str_replace (array(']]>',']]&gt;'),'',$input);
  }
  return $input;
}

function clean_html($string)
{
	$i = 0;
	
	//echo 1;exit;
	//$string = htmlspecialchars_decode($string);
	
	//remove javascript
	$script_open = substr_count($string, "<script");			
	$script_close = substr_count($string, "</script>");	 	
	while ($script_close<$script_open)
	{		
		$enclose = 1;
		$i=0;
		$string = "$string</script>";
		$script_close = substr_count($string, "</script>");
		if ($i>1000){echo 'pop 1'; exit;}else{$i++;}
	}
	
	while ($script_close>$script_open)
	{			
		$i=0;
		$string = "<script>$string";
		$script_open = substr_count($string, "<script");
		if ($i>1000){echo 'pop 2'; exit;}else{$i++;}
	}
	
	//now remove scripts
	while (strstr($string, '<script'))
	{
		$i=0;
		$middle = get_string_between($string, '<script','</script>');
		$remove = "<script".$middle."</script>";
		$string = str_replace($remove,'',$string);
		if ($i>1000){echo 'pop 2';exit;}else{$i++;}
	}
		
	//now remove broken ul and li
	$li_open = substr_count($string, "<li ");			
	$li_close = substr_count($string, "</li>");	
	$ul_open = substr_count($string, "<ul ");			
	$ul_close = substr_count($string, "</ul>");	
	
	if ($ul_open!=$ul_close||$li_open!=$li_close)
	{
		$string = strip_tags($string, '<ul><ol><li><pre><a><b><u><strong><i><div><img><strong><p><span><font><h1><h2><h3><br><table><tr><td><tbody><center><blockquote><embed><object><small><label><br/>');
	}
	
	//remove share this content
	while (strstr($string, '<ul class="shareThis">'))
	{
		//echo 1; exit;
		$i=0;
		$middle = get_string_between($string, '<ul class="shareThis">','</ul>');
		$remove = "<ul class=\"shareThis\">".$middle."</ul>";
		$string = str_replace($remove,'',$string);
		if ($i>1000){$string = str_replace('<ul class="shareThis">','',$string); exit;}else{$i++;}
	}
	
	while (strstr($string, '<ul class="socials">'))
	{
		//echo 1; exit;
		$i=0;
		$middle = get_string_between($string, '<ul class="socials">','</ul>');
		$remove = "<ul class=\"socials\">".$middle."</ul>";
		$string = str_replace($remove,'',$string);
		if ($i>1000){$string = str_replace('<ul class=\"socials\">','',$string); exit;}else{$i++;}
	}
	
	//check for open divs and close
	$div_open = substr_count($string, "<div");			
	$div_close = substr_count($string, "</div>");	

	if ($div_close!=$div_open)
	{	
		//echo 1; exit;
		$string = strip_tags($string, '<ul><ol><li><pre><a><b><u><strong><i><img><strong><p><span><font><h1><h2><h3><br><table><tr><td><tbody><blockquote><center><embed><object><small><label><br/>');
	}


	//remove html commenting
	while (strstr($string, '<!--'))
	{
		//echo 1; exit;
		$remove = get_string_between($string, '<!--',"-->");
		$string = str_replace("<!--{$remove}-->","",$string);
	}
	
	//check for open tables
	if (strstr(strtolower($string), "<table"))
	{
		$table_open = substr_count(strtolower($string), "<table");
		$table_close = substr_count(strtolower($string), "</table>");
		
		if ($table_open==$table_close)
		{
			$string = strip_tags($string, '<ul><ol><li><pre><a><b><i><u><strong><div><img><p><span><font><h1><h2><h3><br><table><tr><td><tbody><center><blockquote><li><ul><embed><object><small><label><br/>');	
		}
		else
		{
			$string = strip_tags($string, '<ul><ol><li><pre><a><b><i><u><strong><div><img><p><span><font><h1><h2><h3><br><center><blockquote><font><li><ul><embed><object><small><label><br/>');	
		}
			
	}
	else
	{
		//echo $string; exit;
		$string =   preg_replace('/(<)([^>]*?<)/' , '&lt;$2' , $string );
		$string = strip_tags($string, '<ul><ol><li><pre><a><b><strong><u><i><div><img><p><span><font><h1><h2><h3><h4><br><center><blockquote><font><li><ul><ol><embed><object><small><label><br/>');
		//echo $string; exit;
	}						
				
	$string = special_htmlentities($string);
	
	//remove readability if there
	if (strstr($string,'readability='))
	{
		$string = preg_replace('/readability="(.+)"/','',$string);
		//echo $string;exit;
	}
	$string = replace_trash_characters($string);
	$string = trim($string);
	
	return $string;		
}


if (!function_exists('mb_detect_encoding')) 
{
	function mb_detect_encoding($text) {
		return 'UTF-8';
	}
	function mb_check_encoding($text) {
		return 'UTF-8';
	}
}

function fix_encoding($text){

	if(is_array($text))
	{
		foreach($text as $k => $v)
		{
			$text[$k] = fix_encoding($v);
		}
      return $text;
    }

    $max = strlen($text);
    $buf = "";
    for($i = 0; $i < $max; $i++){
        $c1 = $text{$i};
        if($c1>="\xc0"){ //Should be converted to UTF8, if it's not UTF8 already
          $c2 = $i+1 >= $max? "\x00" : $text{$i+1};
          $c3 = $i+2 >= $max? "\x00" : $text{$i+2};
          $c4 = $i+3 >= $max? "\x00" : $text{$i+3};
            if($c1 >= "\xc0" & $c1 <= "\xdf"){ //looks like 2 bytes UTF8
                if($c2 >= "\x80" && $c2 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                    $buf .= $c1 . $c2;
                    $i++;
                } else { //not valid UTF8.  Convert it.
                    $cc1 = (chr(ord($c1) / 64) | "\xc0");
                    $cc2 = ($c1 & "\x3f") | "\x80";
                    $buf .= $cc1 . $cc2;
                }
            } elseif($c1 >= "\xe0" & $c1 <= "\xef"){ //looks like 3 bytes UTF8
                if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                    $buf .= $c1 . $c2 . $c3;
                    $i = $i + 2;
                } else { //not valid UTF8.  Convert it.
                    $cc1 = (chr(ord($c1) / 64) | "\xc0");
                    $cc2 = ($c1 & "\x3f") | "\x80";
                    $buf .= $cc1 . $cc2;
                }
            } elseif($c1 >= "\xf0" & $c1 <= "\xf7"){ //looks like 4 bytes UTF8
                if($c2 >= "\x80" && $c2 <= "\xbf" && $c3 >= "\x80" && $c3 <= "\xbf" && $c4 >= "\x80" && $c4 <= "\xbf"){ //yeah, almost sure it's UTF8 already
                    $buf .= $c1 . $c2 . $c3;
                    $i = $i + 2;
                } else { //not valid UTF8.  Convert it.
                    $cc1 = (chr(ord($c1) / 64) | "\xc0");
                    $cc2 = ($c1 & "\x3f") | "\x80";
                    $buf .= $cc1 . $cc2;
                }
            } else { //doesn't look like UTF8, but should be converted
                    $cc1 = (chr(ord($c1) / 64) | "\xc0");
                    $cc2 = (($c1 & "\x3f") | "\x80");
                    $buf .= $cc1 . $cc2;				
            }
        } elseif(($c1 & "\xc0") == "\x80"){ // needs conversion
                $cc1 = (chr(ord($c1) / 64) | "\xc0");
                $cc2 = (($c1 & "\x3f") | "\x80");
                $buf .= $cc1 . $cc2;				
        } else { // it doesn't need convesion
            $buf .= $c1;
        }
    }
    return $buf;
}



function bs_simple_sort($array,$sort)
{
	$sort = 'rand';
	if ($sort=='rand')
	{
		//$array = array_clean($array);
		//shuffle($array);
	}
	
	return $array;
	
}

function redundant_work($input){

  $input = str_replace("Ã©","é", $input);
  $input = str_replace("Ã£", "ã",$input);
  $input = str_replace("Ã§", "ç",$input);
  $input = str_replace("Ã³", "ó",$input);
  $input = str_replace("Ãµ", "a",$input);
  $input = str_replace('â', '"',$input);
  $input = str_replace("Ã´", "ô",$input);
  $input = str_replace("Ã", "í",$input);
  $input = str_replace("Ãº", "ú",$input); 
  $input = str_replace("íª", "ê",$input);
  $input = str_replace("í¡", "á",$input);
  $input = str_replace("íº", "ú",$input);
  
  
   return $input;
} 


function special_htmlentities($data)
{
   //$data = htmlentities($data);
   $data = str_replace("&amp;","&",$data);
   $data = str_replace('&quot;','"', $data);
   $data = fix_encoding($data);
   $data = redundant_work($data);
   return $data;
}

function csv_to_array($file,$delimiter) 
{
	
	if (($handle = fopen($file, "r")) !== FALSE) {
		$i = 0;
		while (($lineArray = fgetcsv($handle, 4000, $delimiter)) !== FALSE) {
			for ($j=0; $j<count($lineArray); $j++) {
				$data2DArray[$i][$j] = $lineArray[$j];
			}
			$i++;
		}
		fclose($handle);
	}
	
	return $data2DArray;
} 

function array_clean($input)
{
  foreach($input as $key => $value) 
  {
	  if(trim($value) =="") 
	  {
		unset($input[$key]);
	  }
  }
  return $input;
}


function replace_trash_characters($input)
{
   //echo $input; exit;
   $input = str_replace('»', '\'',$input);
   $input = str_replace('í­Â¢íÂíÂ', '\'',$input);
   $input = str_replace('í¢Â?Â?', '\'',$input);
   $input = str_replace('—', '-',$input);
   $input = str_replace('&mdash;', '-',$input);   
   $input = str_replace('â€“', '-',$input);   
   $input = str_replace("’", "'",$input);
   $input = str_replace('&amp;', '&',$input);	
   $input = str_replace('&amp;rsquo;', '',$input);
   $input = str_replace('&amp;#x2019;', '',$input);
   $input = str_replace('&#x2019;', '',$input);
   $input = str_replace('&amp;amp;', '&amp;',$input);
   $input = str_replace('&amp;#x2018;', '',$input);
   $input = str_replace('&amp;#x201C;', '"',$input);
   $input = str_replace('&amp;#x201D;', '"',$input);
   $input = str_replace('&amp;#xE9;', '', $input);
   $input = str_replace('&amp;quot;', '"',$input);
   $input = str_replace("&#039;", "",$input);
   $input = str_replace("�", "",$input);
   $input = str_replace("Â", "",$input);
   $input = str_replace("â“", "",$input);
   $input = str_replace("Â’", "'",$input);
   $input = str_replace('€”', '"',$input);
   $input = str_replace('€', '"',$input);
   $input = str_replace('€™', "'",$input);
   $input = str_replace('€˜', "'",$input);
   $input = str_replace('€“˜', "-",$input);
   $input = str_replace('Â“', '"',$input);
   $input = str_replace('Â”', '"',$input);
   $input = str_replace("£", "&pound;", $input); 
   $input = str_replace('&#xFFFD;', '',$input);
   $input = str_replace('&Acirc;', '',$input);
   $input = str_replace('&Atilde;', '',$input);
   $input = str_replace('&#8211;', '',$input);
   $input = str_replace('&amp;', '',$input);
   $input = str_replace('%E2%80%9D', '',$input);
   $input = str_replace('%E2%80%93', '',$input);
   
  
   return $input;
}

function blogsense_url()
{
	$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
	if (strstr($current_url, 'f_add_campaign.php'))	{ 
		$current_url = explode("functions/f_add_campaign.php",$current_url); 
		$current_url = $current_url[0];
	}
	if (strstr($current_url, 'f_activate_blogsense.php'))	{ 
		$current_url = explode("functions/f_activate_blogsense.php",$current_url); 
		$current_url = $current_url[0];
	}
	if (strstr($current_url, 'solo_run.php'))	{ 
		$current_url = explode("solo_run.php",$current_url); 
		$current_url = $current_url[0];
	}
	if (strstr($current_url, 'preview.php'))	{ 
		$current_url = explode("preview.php",$current_url); 
		$current_url = $current_url[0];
	}
	if (strstr($current_url, 'cron_config.php'))	{ 
		$current_url = explode("cron_config.php",$current_url); 
		$current_url = $current_url[0];
	}
	if (strstr($current_url, 'heartbeat.php'))	{ 
		$current_url = explode("heartbeat.php",$current_url); 
		$current_url = $current_url[0];
	}
	if (strstr($current_url, 'blogsense_api.php'))	{ 
		$current_url = explode("blogsense_api.php",$current_url); 
		$current_url = $current_url[0];
	}
	
	return $current_url;
}

function relative_path_prefix()
{
	$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
    if (strstr($current_url, 'cron_config.php'))
	{
		$step = "";
	}
    if (strstr($current_url, 'preview.php'))
	{
		$step = "";
	}
	if (strstr($current_url, 'solo_run.php'))
	{
		$step = "";
	}
	if (strstr($current_url, 'f_add_campaign.php'))
	{
		$step = "../";
	}
	if (strstr($current_url, 'f_add_block.php'))
	{
		$step = "../";
	}
	return $step;
}


function discover_rss_parameters($string)
{
	//echo 1111;
	if (strstr($string, "<item"))
	{
	   $entry_start ="<item";
	}
	if (strstr($string, "<entry>"))
	{
	   $entry_start ="<entry>";
	}  		
	
	//scoop-out feed header information
	if ($entry_start)
	{
		$parts = explode($entry_start, $string);
		array_shift($parts);
		$parts = implode(" ", $parts);
		$string = $parts;
	}
	
	//determine what the title opener looks like		   
	if (strstr($string, "<title>"))
	{
		$title_start =  "<title>";
		$title_end =  "</title>";
	}
	if (strstr($string, '<title type="html"'))
	{
		$title_start =  '<title type="html">';
		$title_end =  "</title>";
	}		

	if (strstr($string, "<title type='text'>"))
	{
	 $title_start =  "<title type='text'>";
	 $title_end =  "</title>";
	}
	
	if (strstr($string, '<title type="text">'))
	{
	 $title_start =  '<title type="text">';
	 $title_end =  "</title>";
	}		
	
	

	if (strstr($string, "<description type='text'>"))
	{
		$description_start = "<description type='text'>";
		$description_end =  "</description>";
	}
	if (strstr($string, "<description>"))
	{
		$description_start =  "<description>";
		$description_end =  "</description>";
	}
	if (strstr($string, "<summary>"))
	{
		$description_start =  "<summary>";
		$description_end =  "</summary>";
	}
	if (strstr($string, "<summary type='text'>"))
	{
		$description_start =  "<summary type='text'>";
		$description_end =  "</summary>";
	}
	if (strstr($string, "<content type='html'>"))
	{
		$description_start = "<content type='html'>";
		$description_end =  "</content>";
	}
	if (strstr($string, '<content type="html">'))
	{
		$description_start = '<content type="html">';
		$description_end =  "</content>";
	}

	if (strstr($string, '<content:encoded>'))
	{
		$description_start = '<content:encoded>';
		$description_end =  "</content:encoded>";
	}
	
	if (!$description_start&&strstr($string, '" type="html">'))
	{
		$description_start = '" type="html">';
		$description_end =  "</content>";
	}
	
	if (strstr($string, "<pubDate>"))
	{
		$publish_date_start = "<pubDate>";
		$publish_date_end = "</pubDate>";
	}    
	
	if (strstr($string, "<published>"))
	{
		$publish_date_start = "<published>";
		$publish_date_end = "</published>";
	}  
		  
	if (strstr($string, "<dc:creator>"))
	{
		$author_start = "<dc:creator>";
		$author_end = "</dc:creator>";
	}  
	
	if (strstr($string, "<author>"))
	{
		if (strstr($string, "<author><name>"))
		{
			$author_start = "<name>";
			$author_end = "</name>";
		}
		else
		{
			$author_start = "<author>";
			$author_end = "</author>";
		}
	}  
	
	if (strstr($string, "<dc:author>"))
	{
		$author_start = "<dc:author>";
		$author_end = "</dc:author>";
	}  
	
	//determine what the link opener looks like
	if (strstr($string, "<link>"))
	{
		$link_start = "<link>";
		$link_end = "</link>";
	}    
	if (strstr($string, "<link rel='alternate' type='text/html' href='"))
	{
		$link_start = "<link rel='alternate' type='text/html' href='";
		$link_end = "'";
	}
	if (strstr($string, '<feedburner:origLink>'))
	{
		$link_start = "<feedburner:origLink>";
		$link_end = "</feedburner:origLink>";
		$close =1;
	}
	if(strstr($string, '<link rel="alternate" type="text/html" href=')&&$close!=1)
	{
		$link_start =  '<link rel="alternate" type="text/html" href="';
		$link_end = '"';
		$close =1;
	}			
	if(strstr($string, '<link rel="alternate" href=')&&$close!=1)
	{
		$link_start =  '<link rel="alternate" href="';
		$link_end = '"';
		$close =1;
	}
	if(strstr($string, '<guid isPermaLink="true">')&&$close!=1)
	{
		$link_start =  '<guid isPermaLink="true">';
		$link_end = '</guid>';
		$close =1;
	}		
	//echo $link_start;echo $close;
	//echo $string; exit;
	
	if(strstr($string, '<generator uri="http://www.google.com/reader">Google Reader</generator>'))
	{
		$string =  str_replace('</summary>','</content>',$string);
		$title_start =  '<title type="html">';
		$title_end =  "</title>";
		$description_start = 'type="html">';
		$description_end =  "</content>";
		$link_start =  '<link rel="alternate" href="';
		$link_end = '"';
		$google_reader = 1;
		
		$string = preg_replace('/gr:annotation(.*?)\/gr:annotation/si','',$string);
		while (strstr($string, '<source'))
		{
			$remove = get_string_between($string, '<source','</source>');
			$string = str_replace('<source'.$remove.'</source>' , '' ,$string);
		}
	}
	else
	{
		$google_reader = 0;
	}
	
	return array( 'string'=>$string, 'title_start' => $title_start , 'title_end'=>$title_end , 'description_start' => $description_start, 'description_end' => $description_end, 'link_start' => $link_start , 'link_end' => $link_end , 'publish_date_start' => $publish_date_start,  'publish_date_end' => $publish_date_end, 'author_start'=> $author_start ,'author_end' => $author_end , 'google_reader' => $google_reader);
}

function date_normalize( $date_str ) 
{
	global $wordpress_date_time;
	if (!$date_str)
	{
		$date_str = $wordpress_date_time;
	}
	
	$dt = date ('Y-m-d H:i:s', strtotime ($date_str));
	return $dt;

}

function longest_word($str) {
  $ar = explode(" ", $str);
  $ln = $ar[0];
  for($i=0; $i < count($ar); $i++) {
    if (strlen($ar[$i]) > strlen($ln)) {
      $ln = $ar[$i];
    }
  }
  return $ln;
}

function links_to_tag_links($string, $blog_url)
{
	$description = $string;
	$string = strtolower($string);
	$i=0;
	while (stristr($string, "</a>"))
	{
		$blocks[] = get_string_between($string, "<a","</a>");
		//echo get_string_between($string, "<a","</a>");
		//echo "<hr>";
		$string = str_replace_once('<a','',$string);
		$string = str_replace_once('</a>','',$string);
		$go=1;
		
	}
	
	if ($go==1)
	{
		foreach ($blocks as $key=>$value)
		{
			if (strstr($value, "href='"))
			{		
				$hrefs[$key] = get_string_between($value, "href='","'");
				$description = str_replace_once($hrefs[$key], "***$key***", $description);
			}
			else
			{
				$hrefs[$key] = get_string_between($value, 'href="','"');
				$description = str_replace_once($hrefs[$key], "***$key***", $description);
			}
	 
			$value = "<a".$value."</a>";
			$tag = strip_tags($value);	
		
			$tag = sanitize_title_with_dashes( $tag );
			
			$tag = trim($tag);
			$tags[$key] = $tag;
		}
	
	
	
		//echo $description; exit;
		foreach ($hrefs as $key=>$val)
		{
			$tag = str_replace(' ', '-', $tags[$key]);
			while (strstr($tag, '--'))
			{
				$tag = str_replace('--', '-',$tag);
			}
			
			$slash = substr($blog_url, -1, 1);
			if ($slash=="/")
			{			
				//echo $blog_url; exit;
				$tag_link = $blog_url."tag/".$tag."/";
			}
			else
			{
				$tag_link = $blog_url."/tag/".$tag."/";
			}					   
			$description = str_replace("***$key***", $tag_link, $description);
		}
	}
	
	//echo $description; exit;
	return $description;
}

function links_to_search_links($string, $blog_url)
{
	$description = $string;
	$string = strtolower($string);
	$i=0;
	while (stristr($string, "</a>"))
	{
		$blocks[] = get_string_between($string, "<a","</a>");
		//echo get_string_between($string, "<a","</a>");
		//echo "<hr>";
		$string = str_replace_once('<a','',$string);
		$string = str_replace_once('</a>','',$string);
		$go=1;
	}
	
	if ($go==1)
	{
		foreach ($blocks as $key=>$value)
		{
			//echo $value;
			//echo "<br><br>";
			
			if (strstr($value, "href='"))
			{		
				$hrefs[$key] = get_string_between($value, "href='","'");
				$description = str_replace_once($hrefs[$key], "***$key***", $description);
			}
			else
			{
				$hrefs[$key] = get_string_between($value, 'href="','"');
				//$hrefs[$key] = trim($hrefs[$key]);
				//echo $description; exit;
				//echo $hrefs[$key]; exit;
				$description = str_replace_once($hrefs[$key], "***$key***", $description);
			}
			//echo $description;
			$value = "<a".$value."</a>";
			$tag = strip_tags($value);	
			
			$tag = preg_replace('/[^a-zA-Z]/', ' ', $tag); 
			
			$tag = trim($tag);
			$tags[$key] = $tag;
		}
	
	
		//echo $description; exit;
		foreach ($hrefs as $key=>$val)
		{
			$tag =  urlencode($tags[$key]);
			
			$slash = substr($blog_url, -1, 1);
			if ($slash=="/")
			{			
				//echo $blog_url; exit;
				$search_link = $blog_url."?s=".$tag."";
			}
			else
			{
				$search_link = $blog_url."?s=".$tag."";
			}					   
			$description = str_replace("***$key***", $search_link, $description);
		}
	}
	//echo $description; exit;
	return $description;
}

function hook_amazon($string,$params)
{
	$comment_content = $params['comment_content'];
	$comment_author = $params['comment_author'];

	$string = str_replace('%amazon_price%', $params['list_price'], $string);			
	$string = str_replace('%amazon_small_image_url%', $params['small_image'], $string);
	$string = str_replace('%amazon_medium_image_url%', $params['medium_image'], $string);
	$string = str_replace('%amazon_large_image_url%', $params['large_image'], $string);
	
	$string = str_replace('%amazon_product_description%', $params['product_description'], $string);
	$string = str_replace('%amazon_product_features%', $params['amazon_features'], $string);
	$string = str_replace('%amazon_brand_name%', $params['amazon_brand'], $string);
	$string = str_replace('%amazon_model%', $params['amazon_model'], $string);
	$string = str_replace('%amazon_customer_review_content_1%', $comment_content[0], $string);
	$string = str_replace('%amazon_customer_review_authorname_1%', $comment_author[0], $string);
	$string = str_replace('%amazon_customer_review_content_2%', $comment_content[1], $string);
	$string = str_replace('%amazon_customer_review_authorname_2%', $comment_author[1], $string);
	$string = str_replace('%amazon_customer_review_content_3%', $comment_content[2], $string);
	$string = str_replace('%amazon_customer_review_authorname_3%', $comment_author[2], $string);
	$string = str_replace('%amazon_buyitnow_button%', $params['buyitnow_button'], $string);
	$string = str_replace('%amazon_product_title%', $params['original_title'], $string);
	$string = str_replace('%title%', $params['original_title'], $string);
	
	return $string;
}

function hook_content($template,$table_prefix,$store_images_relative_path,$store_images_full_url,$campaign_name,$campaign_query,$title,$link,$description,$custom_variable_token,$custom_variable_content)
{	
	global $wordpress_date_time;
	global $blog_url;
	
	$j=0;
	
	//prepare custom variables
	if ($custom_variable_token)
	{
		foreach ($custom_variable_token as $k=>$v)
		{
			$template = str_replace($custom_variable_token[$k],$custom_variable_content[$k], $template);
		}
	}

	//video only token variables
	if (is_array($description)&&$description[0]=='video')
	{
		$thumbnail = $description[1];
		$description = $description[2];
		$object = prepare_youtube($link, $content= "");
		
		$template = str_replace('%video_embed%', $object, $template);
		$template = str_replace('%video_thumbnail%', $thumbnail, $template);
		$template = str_replace('%video_description%', $description, $template);
	}
	
	//global token variables
	$title_filtered = remove_trash_words($title);
	$template = str_replace('%title%', $title, $template);
	$template = str_replace('%title_filtered%', $title_filtered, $template);
	$template = str_replace('%postbody%', $description, $template);
	$template = str_replace('%link%', $link, $template);
	$template = str_replace('%campaign_name%', $campaign_name, $template);
	$template = str_replace('%campaign_query%', $campaign_query, $template);
	$template = str_replace('%blog_url%', $blog_url, $template);
	
	//prepare tags
	$tags_description = prepare_tags($a=NULL, $description,1,$tc=null,1,1);
	$tags_title = prepare_tags($a=NULL, $title,1,$tc=null,1,1);
	if (strstr($template, '%tag_'))
	{
		//echo 1; exit;
		$template = str_replace('%tag_t%', $tags_description[0], $template);
		$template = str_replace('%tag_2%', $tags_description[1], $template);
		$template = str_replace('%tag_title%', $tags_title[0], $template);
		$template = str_replace('%tag_postbody%', $tags_description[0], $template);
	}
	
	//check for spyntax
	if (strstr($template, '[spyntax]'))
	{
		//echo 0; exit;
		$total = substr_count($template, '[spyntax]');
		for ($i=0;$i<$total;$i++)
		{
			$get = get_string_between($template, '[spyntax]','[/spyntax]');
			$spun = spyntax($get);
			$spun = str_replace('$','\$',$spun);
			$template = str_replace("[spyntax]".$get."[/spyntax]", $spun ,$template);
		}
	}
	
	//check for inserts
	if (stristr($template, '[/insert]'))
	{
		$total = substr_count($template, '[/insert]');
		for ($i=0;$i<$total;$i++)
		{
			$placement = get_string_between ($template, '[insert:',']');

			$template = str_replace_once(":$placement]", "]", $template);
			$content = get_string_between($template, '[insert]','[/insert]');
			$template = hook_insert($template, $content, $placement);
			//echo $template; exit;
		}
	}
	
	if (stristr($template, '%image_1%')||stristr($template, '%image_2%'))
	{
		//echo $template;
		//echo "999<hr>";exit;
		$image = bs_get_images($template);
		$template = str_replace('%image_1%', $image[0], $template);
		$template = str_replace('%image_2%', $image[1], $template);	
		//echo $template;exit;
	}
	
	//check for inserts
	if (stristr($template, '[/substring]'))
	{
		$total = substr_count($template, '[/substring]');
		for ($i=0;$i<$total;$i++)
		{
			preg_match_all('/\[substring:(.*?)\](.*?)\[\/substring\]/si', $template, $matches);
			
			$options = $matches[1][0];
			$this_content = $matches[2][0];	
			
			$options = explode(':', $options);
			$var1 = $options[0];
			$var2 = $options[1];
			
			$this_content = str_replace('</p>','<br><br>', $this_content);
			$this_content = str_replace('</div>','<br><br>', $this_content);
			
			$this_content = strip_tags($this_content,'<br><blockquote>');
			$this_content = substr($this_content,$var1,$var2);
			
			if (stristr($this_content,'<blockquote>')&&!stristr($this_content,'</blockquote>'))
			{
				$this_content = $this_content."</blockquote>";
			}
			
			$template = preg_replace('/\[substring:(.*?)\](.*?)\[\/substring\]/si', $this_content, $template, 1);
		}
	}
	
	if (strstr($template, '[date format="'))
	{
		//echo 0; exit;
		$total = substr_count($template, '[date format="');
		for ($i=0;$i<$total;$i++)
		{
			$get = get_string_between($template, '[date format="','"]');
			$this_date = date($get, strtotime($wordpress_date_time));
			$template = str_replace_once('[date format="'.$get.'"]', $this_date ,$template);
		}
	}
	
	$i=0;
	while(strstr($template,'[rand]')&&$i<1000)
	{
		$string = get_string_between($template, '[rand]','[/rand]');
		$blocks = explode('||',$string);
		$rand_key = array_rand($blocks);
		$this_block = $blocks[$rand_key];
		$template = str_replace("[rand]{$string}[/rand]", $this_block, $template);
		$i++;
	}
	
	$j=0;
	while (strstr($template, '{amazon:')&&$j<100)
	{
		if (strstr($link,'co.uk'))
		{
			$domain = 'co.uk';
		}
		else
		{
			$domain = 'com';
		}
		$section = get_string_between ($template, '{amazon:','}');
		$sections = explode(':',$section);
		$limit = $sections[0];
		$mode = $sections[1];
		$keywords = $sections[2];
		
		if (!$keywords)
		{
			$keywords = remove_trash_words($title);
		}
		$return = @search_amazon($mode,$keywords,$limit,$domain);
		$template = preg_replace('({amazon:(.*?)})',$return,$template, 1);
		$j++;
	}
	
	$j=0;
	while (strstr($template, '{youtube:')&&$j<100)
	{
		$section = get_string_between ($template, '{youtube:','}');
		$sections = explode(':',$section);
		$limit = $sections[0];
		$keyword = $sections[1];
		$exclude_keywords = $sections[2];
		//echo $exclude_keywords;exit;
		$nature = 'keyword';
		$return = @search_youtube($keyword,$limit,$store_images_relative_path,$store_images_full_url,$nature, $exclude_keywords);
		//echo $return;exit;
		$template= preg_replace('/{youtube:(.*?)}/si',$return,$template, 1);
		$j++;		
	}
	
	$j=0;
	while (strstr($template, '{ebay:')&&$j<100)
	{
		$section = get_string_between ($template, '{ebay:','}');
		$sections = explode(':',$section);
		$campaign_id = $sections[0];
		$limit = $sections[1];
		$width = $sections[2];
		$width = str_replace('px','',$width);
		$return = @search_ebay($tags[0],$campaign_id, $limit,$width);
		$template = preg_replace('({ebay:\d+:\d+:\d+px})',$return,$template, 1);
		$j++;
	}
	
	$j=0;
	while (strstr($template, '{flickr:')&&$j<100)
	{
		$section = get_string_between ($template, '{flickr:','}');
		$sections = explode(':',$section);			
		$limit = $sections[0];
		$max_width = $sections[1];
		$max_height = $sections[2];
		$max_width = str_replace('px','',$max_width);
		$max_height = str_replace('px','',$max_height);
		$return = @search_flickr($title,$limit,$max_width,$max_height,$store_images_relative_path,$store_images_full_url);
		$template = preg_replace('({flickr:\d+:\d+px:\d+px})',$return,$template, 1);
		$j++;
	}
	
	$j=0;
	while (strstr($template, '{googleimg:')&&$j<100)
	{
		$section = get_string_between ($template, '{googleimg:','}');
		$sections = explode(':',$section);	
		$query = urlencode($sections[0]);
		$limit = $sections[1];
		$max_width = $sections[2];
		$max_height = $sections[3];
		$sort = $sections[4];
		$max_width = str_replace('px','',$max_width);
		$max_height = str_replace('px','',$max_height);
		$return = @search_googleimg($query,$limit,$max_width,$max_height,$sort);
		$template = preg_replace('({googleimg:(.*?)})',$return,$template, 1);
		$j++;
	}
	
	$j=0;
	while (strstr($template, '{rss:')&&$j<100)
	{
		$section = get_string_between ($template, '{rss:','}');
		$section = str_replace('http://', 'open*', $section);
		$sections = explode(':',$section);			
		$url = $sections[0];
		$limit = $sections[1];
		$do_not_omit = $sections[2];
		$return = @hook_rss($url,$limit,$do_not_omit);
		$template = preg_replace('/({rss:(.*?)})/s',$return,$template, 1);
		$j++;
	}
	
	$j=0;
	while (strstr($template, '{knol.google:')&&$j<100)
	{
		$section = get_string_between ($template, '{knol.google:','}');
		$sections = explode(':',$section);			
		$query = $sections[0];
		$exact_query = $sections[1];
		$license = $sections[2];
		$sort = $sections[2];
		$return = @hook_knolgoogle($query,$exact_query,$license,$sort);
		$template = preg_replace('/({knol\.google:(.*?)})/s',$return,$template, 1);
		$j++;
	}
	$j=0;
	while (strstr($template, '{articlebase:')&&$j<100)
	{
		$section = get_string_between ($template, '{articlebase:','}');
		$sections = explode(':',$section);			
		$query = $sections[0];
		$return = @hook_articlebase($query,$exact_query,$license,$sort);
		$template = preg_replace('/({articlebase:(.*?)})/s',$return,$template, 1);
		$j++;
	}
	while (strstr($template, '{associatedcontent:')&&$j<100)
	{
		$section = get_string_between ($template, '{associatedcontent:','}');
		$sections = explode(':',$section);			
		$query = $sections[0];
		$sort = $sections[1];
		$return = @hook_associatedcontent($query,$sort);
		$template = preg_replace('/({associatedcontent:(.*?)})/s',$return,$template, 1);
		$j++;
	}
	$j=0;
	while (strstr($template, '{ezine:')&&$j<100)
	{
		$section = get_string_between ($template, '{ezine:','}');
		$sections = explode(':',$section);			
		$query = $sections[0];
		$return = @hook_ezine($query,$exact_query,$license,$sort);
		$template = preg_replace('/({ezine:(.*?)})/s',$return,$template, 1);
		$j++;
	}
	if (strstr($template, '[hook type="image"'))
	{
		//echo 1; exit;	
		$total = substr_count($template, '[hook type="image"');
		//echo $total; exit;
		for ($i=0;$i<$total;$i++)
		{
			$rand_key = "";
			$pool = "";
			$hook_string = get_string_between($template, '[hook', ']');
			$pool = get_string_between($hook_string, 'pool="', '"');
			$pool = explode(",", $pool);
			$max_width = get_string_between($hook_string, 'max_width="', '"');
			$max_height = get_string_between($hook_string, 'max_height="', '"');
			$float = get_string_between($hook_string, 'float="', '"');
			$css = get_string_between($hook_string, 'css="', '"');
			$rand_key = array_rand($pool, 1);
			$winner = $pool[$rand_key];		
			$build_image = "<img src='$winner' class='$css' style='max-width:".$max_width."px;max-height:".$max_height."px;float:".$float.";'>";
			$template = str_replace_once("[hook".$hook_string."]",$build_image,$template);
			$j++;
		}
	}
	if (strstr($template, '[hook type="video"'))
	{
		//echo 2; exit;
		$total = substr_count($template, '[hook type="video"');
		for ($i=0;$i<$total;$i++)
		{
			$hook_string = get_string_between($template, '[hook', ']');
			$pool = get_string_between($hook_string, 'pool="', '"');
			$pool = explode(",", $pool);
			$width = get_string_between($hook_string, 'width="', '"');
			$height = get_string_between($hook_string, 'height="', '"');
			$css = get_string_between($hook_string, 'css="', '"');
			$rand_key = array_rand($pool, 1);
			$winner = $pool[$rand_key];	 
			$description_status = get_string_between($hook_string, 'descriptions="', '"');
			$flag = "http://www.youtube.com/watch?v=";
			$pos_start = strpos($winner, $flag) + strlen($flag);
			$vid = substr($winner, $pos_start, 11);
			$url = "http://gdata.youtube.com/feeds/base/videos?q=$vid&key=AI39si6RmbtB6goYpu0MrGKmEeEhg5dIOSdZUClTencT6F_Saf3Wjqp9y55xoJ1PAa_htlx3ArxozpuNiG-jdWzNxMAV-NhvKw";
			$xml = quick_curl($url,0);
			//prepare description
			$xml =  htmlspecialchars_decode($xml);
			//echo $string;exit;
			$remove = get_string_between($xml,"<feed xmlns=","</generator>");
			$string = str_replace($remove,'',$xml);

			$title = get_string_between($string,"<title type='text'>","</title>");
			$description = get_string_between($xml,"<content type='html'>","From:");
			$thumbnail = get_string_between($description, 'src="', '"');
			$thumbnail = save_image($thumbnail,$title,$i);
			//echo $title;exit;
			//echo $link;exit;
			//echo $thumbnail;exit;
			if ($description_status=='on')
			{
				$lite_description = strip_tags($description);
				$lite_description = str_replace($title,'',$lite_description);
				$description = "<div style='margin-top:29px;height:90px;text-align:left;width:100%;display:inline-block' class='$class'><br><img class='alignright' src='$thumbnail' border='0' style='padding-left:10px;float:right;max-width:150px'>$lite_description</div>";
			}
			else
			{
				$description = "";
			}

			//get video and append
			$object = prepare_youtube($winner, $description);
			$video_string = "<center>$object</center><br>".$description;
			$template = str_replace_once("[hook".$hook_string."]",$video_string,$template);
			//echo $content;exit;
			$j++;
		}
	}
	
	
	
	return $template;
}

function hook_ifnoimage($content)
{
	if (stristr($content,'<img'))
	{
		//echo $content;
		//echo "<hr>";
		$content = preg_replace('/\[ifnoimage\](.*?)\[\/ifnoimage\]/i','',$content, -1);
		return $content;
	}
	else
	{
		$i=0;
		while (stristr($content,'[ifnoimage]')&&$i<100)
		{
			$replace = get_string_between($content,"[IFNOIMAGE]","[/IFNOIMAGE]");
			if (!$replace){
				$replace = get_string_between($content,"[ifnoimage]","[/ifnoimage]");
			}
			$content = str_replace($replace,'***',$content);
			$content = preg_replace('/\[ifnoimage(.*?)ifnoimage\]/i',$replace,$content, 1);
			$i++;
		}
		return $content;
	}
}

function hook_knolgoogle($q,$qexact,$license,$sort)
{
		global $blogsense_url;
		global $template_type;
		global $template_content;
	
		$key = array_search('token_knolgoogle_template', $template_type); 
		$this_template = $template_content[$key];

		$q = urlencode($q);
		$qexact = urlencode($qexact);
		$link = "knol.google.com/k/knol/system/knol/pages/SearchToolkit?show=off&q={$q}&qexact={$qexact}&qor1=&qor2=&qor3=&qneg=&doctype=0&loc0=on&loc1=on&loc3=on&loc4=on&loc5=on&loc7=on&loc8=on&restrict=0&url=&editedstart=&editedstarttime=0&editedend=&editedendtime=0&createdstart=&createdstarttime=0&createdend=&createdendtime=0&editdate=0&createdate=0&language=0&license={$license}&collab=0&link=&templateUrl=&promo=&sort={$sort}&num=50";
		$string = quick_curl($link,1);

		preg_match('/knol-title(.*?)href="(.*?)"/s', $string, $matches);
		$this_link = "http://knol.google.com{$matches[2]}";
		
		$this_link = str_replace('http://', '', $this_link);
		$this_link = urlencode($this_link);
		$this_link = $blogsense_url."includes/fivefilters/makefulltextfeed.php?url={$this_link}&max=1&links=preserve&submit=Create+Feed";
	
		$string = quick_curl($this_link,1);
		$string = htmlspecialchars_decode($string);
		$parameters = discover_rss_parameters($string);
		$string = $parameters['string'];

		$title = get_string_between($string, $parameters['title_start'], $parameters['title_end']);				   
		$description = get_string_between($string, $parameters['description_start'],  $parameters['description_end']);
		$description = strip_tags($description, '<ul><ol><li><pre><a><b><i><u><h1><h2><table><tr><td><p><br><img><div><font><span><center><blockquote><h3><h4><hr><ul><li><small><label><br/><date>');
		
		$this_template = str_replace('%title%',$title,$this_template);
		$this_template = str_replace('%postbody%',$description,$this_template);
		$this_template = str_replace('%link%',$this_link,$this_template);
		//echo 1;
		//echo $this_template;exit;
		return $this_template;
}

function hook_articlebase($q)
{
		global $blogsense_url;
		global $template_type;
		global $template_content;
	
		$key = array_search('token_articlebase_template', $template_type); 
		$this_template = $template_content[$key];

		//echo $this_template;exit;
		$q = urlencode($q);
		$qexact = urlencode($qexact);
		$link = "http://www.articlesbase.com/find-articles.php?q={$q}";
		$string = quick_curl($link,1);
		//echo $string;exit;
		preg_match('/\<div class=\"title\">(.*?)href="(.*?)"/s', $string, $matches);
		$this_link = $matches[2];
		
		$this_link = str_replace('http://', '', $this_link);
		$this_link = urlencode($this_link);
		$this_link = $blogsense_url."includes/fivefilters/makefulltextfeed.php?url={$this_link}&max=1&links=preserve&submit=Create+Feed";
	
		$string = quick_curl($this_link,1);
		$string = htmlspecialchars_decode($string);
		$parameters = discover_rss_parameters($string);
		$string = $parameters['string'];

		$title = get_string_between($string, $parameters['title_start'], $parameters['title_end']);				   
		$description = get_string_between($string, $parameters['description_start'],  $parameters['description_end']);
		$description = strip_tags($description, '<ul><ol><li><pre><a><b><i><u><h1><h2><table><tr><td><p><br><img><div><font><span><center><blockquote><h3><h4><hr><ul><li><small><label><br/><date>');
	
		
		$this_template = str_replace('%title%',$title,$this_template);
		$this_template = str_replace('%postbody%',$description,$this_template);
		$this_template = str_replace('%link%',$this_link,$this_template);
		//echo 1;
		//echo $this_template;exit;
		return $this_template;
}

function hook_associatedcontent($q,$sort)
{
		global $blogsense_url;
		global $template_type;
		global $template_content;
	
		$key = array_search('token_associatedcontent_template', $template_type); 
		$this_template = $template_content[$key];

		//echo $this_template;exit;
		$q = urlencode($q);
		$qexact = urlencode($qexact);
		$link = "http://www.associatedcontent.com/search.html?content_type=article&q={$q}&search=Search&s={$sort}";
		$string = quick_curl($link,1);
		//echo $link;exit;
		preg_match('/href="\/article\/(.*?)\//', $string, $matches);
		$article_id = $matches[1];	
	
		$this_link = "http://www.associatedcontent.com/shared/print.shtml?content_type=article&content_type_id={$article_id}";
		
		$string = cookie_curl($this_link,1);
		//echo $string;exit;
		$string = htmlspecialchars_decode($string);
		$parameters = discover_rss_parameters($string);
		$string = $parameters['string'];

		$title = get_string_between($string, "<h1>", "</h1>");				   
		$description = get_string_between($string, 'class="article_text">', '<div id="pop_footer">' );
		$description = strip_tags($description, '<ul><ol><li><pre><a><b><i><u><h1><h2><table><tr><td><p><br><img><font><span><center><blockquote><h3><h4><hr><ul><li><small><label><br/><date>');
	
		
		$this_template = str_replace('%title%',$title,$this_template);
		$this_template = str_replace('%postbody%',$description,$this_template);
		$this_template = str_replace('%link%',$this_link,$this_template);
		//echo 1;
		//echo $this_template;exit;
		return $this_template;
}


function hook_ezine($q)
{
		global $blogsense_url;
		global $template_type;
		global $template_content;
	
		$key = array_search('token_ezine_template', $template_type); 
		$this_template = $template_content[$key];

		$q = urlencode($q);
		$qexact = urlencode($qexact);
		$link = "http://www.google.com/cse?cx=partner-pub-3754405753000444%3A3ldnyrvij91&cof=FORID%3A10&ie=ISO-8859-1&q=link+cloaking&ad=w9&num=10&rurl=http%3A%2F%2Fezinearticles.com%2Fresults%2F%3Fcx%3Dpartner-pub-3754405753000444%253A3ldnyrvij91%26cof%3DFORID%253A10%26ie%3DISO-8859-1%26q%3D{$q}";
		$string = quick_curl($link,1);
		//echo $string;exit;
		sleep(2);

		//echo $string;exit;
		preg_match('/\<h2 class=r\>\<a href="(.*?)"/s', $string, $matches);
		$this_link = $matches[1];
		
		$this_link = str_replace('http://', '', $this_link);
		$this_link = urlencode($this_link);
		$this_link = $blogsense_url."includes/fivefilters/makefulltextfeed.php?url={$this_link}&max=1&links=preserve&submit=Create+Feed";

		$string = stealth_curl($this_link,0,'');

		$string = htmlspecialchars_decode($string);
		$parameters = discover_rss_parameters($string);
		$string = $parameters['string'];

		$title = get_string_between($string, $parameters['title_start'], $parameters['title_end']);				   
		$description = get_string_between($string, $parameters['description_start'],  $parameters['description_end']);
		$description = strip_tags($description, '<ul><ol><li><pre><a><b><i><u><h1><h2><table><tr><td><p><br><img><div><font><span><center><blockquote><h3><h4><hr><ul><li><small><label><br/><date>');
		
		$this_template = str_replace('%title%',$title,$this_template);
		$this_template = str_replace('%postbody%',$description,$this_template);
		$this_template = str_replace('%link%',$this_link,$this_template);
		//echo 1;
		//echo $this_template;exit;
		return $this_template;
}

function spyntax($content)
{
	
	if ( preg_match('/\{([^{}]+)\}/', $content, $matches) ) {
        $inner_elements = explode('|', $matches[1]);
        $random_element = $inner_elements[array_rand($inner_elements)];
        $content = str_replace($matches[0], $random_element, $content);
        $content = spyntax($content);
    }

  return $content;
}


function strip_links($input)
{
    $input = html_entity_decode($input);
    $count = substr_count ($input, "<a");
    
    //echo $count; exit;
    //echo $input; exit;
	for ($i=0;$i<$count;$i++)
	{
	     $start = "<a";
	     $end =  "</a>";
	     $get = get_string_between($input, $start, $end);
	     $anchor = get_string_between("$get</a>",">","</a>");
	     $remove = "$start$get$end";
	     $input = str_replace_once($remove, $anchor, $input);
	}
	//echo $input; exit;
	return $input;
}

//get youtube thumbnail
function get_youtube_thumbnail($link)
{
		$link = explode("?v=", $link);
		$link = $link[1];
		$link = "http://i4.ytimg.com/vi/$link/default.jpg";
		//echo $link;
		//echo "<hr>";
		return $link;
}


//function to prepare youtube video
function prepare_youtube($link, $description)
{
	global $template_type;
	global $template_content;
	
	$key = array_search('youtube_object_template', $template_type); 
	$youtube_object_template = $template_content[$key];
	
	//echo $youtube_object_template;exit;
    if (!strstr($link, "youtube"))
    {
		if (strstr($description, "http://www.youtube.com/watch?v="))
		{
		    $description = str_replace("&hl=en", "", $description);
			$description = str_replace("&amp;hl=en", "", $description);
			$flag = "http://www.youtube.com/watch?v=";
			$pos_start = strpos($description, $flag) + strlen($flag);
			//The next 11 characters
			$vid = substr($description, $pos_start, 11);
			//preg_match('%\?v=([^&]+)%', $description, $matches);
			//$vid = $matches[1];
			
			$embed_link = "http://www.youtube.com/v/$vid&=en_US&fs=1&";
			$original_link = "http://www.youtube.com/watch?v=$vid";
			
			$object = $youtube_object_template;
			$object = str_replace('%embed_link%',$embed_link ,$object);
			
			$object = str_replace($original_link, "<br><center>".$object."</center><br>", $description);
			//echo $description; exit;
		}
	}
    if (strstr($link, "youtube"))
	{
		if (strstr($link, "watch?v="))
		{
		   $olink = str_replace("watch?v=" , "v/" , $link);
		}
		else
		{
		$olink = str_replace("?v=" , "v/" , $link);
		}
		
		$object = $youtube_object_template;
		$object = str_replace('%embed_link%',$olink ,$object);
		//echo $object;exit;
	}
	return $object;
}

function remove_trash_words($string)
{
	$string= strtolower($string);
	$array = explode(' ' , $string);
	$trash ="may,need,-,:,released,world,can,get,?,longer,stock,met,seen,content,can't,plus,got,go,no,review,added,new,we,all,check,our,be,hire,night,file,incredible,list,mostly,finally,detail,|,of,add,minus,subtract,table,about,above,acid,across,actually,after,again,against,almost,already,also,alter,although,always,among,angry,another,anyway,appropriate,around,automatic,available,awake,aware,away,back,basic,beautiful,because,been,before,being,bent,better,between,bitter,black,blue,boiling,both,bright,broken,brown,came,cause,central,certain,certainly,cheap,chemical,chief,clean,clear,clearly,close,cold,come,common,complete,complex,concerned,conscious,could,cruel,current,dark,dead,dear,deep,delicate,dependent,different,difficult,dirty,down,each,early,east,easy,economic,either,elastic,electric,else,enough,equal,especially,even,ever,every,exactly,feeble,female,fertile,final,finalty,financial,fine,first,fixed,flat,following,foolish,foreign,form,former,forward,free,frequent,from,full,further,future,general,generality,give,good,great,green,grey/gray,half,hanging,happy,hard,have,healthy,heavy,help,here,high,himself,hollow,home,however,human,important,indeed,individual,industrial,instead,international,into,just,keep,kind,labor,large,last,late,later,least,left,legal,less,like,likely,line,little,living,local,long,loose,loud,main,major,make,male,many,married,material,maybe,mean,medical,might,military,mixed,modern,more,most,much,must,name,narrow,national,natural,near,nearly,necessary,never,next,nice,normal,north,obviously,often,okay,once,only,open,opposite,original,other,over,parallel,particular,particularly,past,perhaps,personal,physical,please,political,poor,popular,possible,present,previous,prime,private,probable,probably,professional,public,quick,quickly,quiet,quite,rather,ready,real,really,recent,recently,regular,responsible,right,rough,round,royal,safe,said,same,second,secret,seem,send,separate,serious,several,shall,sharp,short,should,shut,significant,similar,simple,simply,since,single,slow,small,smooth,social,soft,solid,some,sometimes,soon,sorry,south,special,specific,sticky,stiff,still,straight,strange,strong,successful,such,sudden,suddenly,sure,sweet,take,tall,than,that,their,them,then,there,therefore,these,they,thick,thin,think,this,those,though,through,thus,tight,till,tired,today,together,tomorrow,total,turn,under,unless,until,upon,used,useful,usually,various,very,violent,waiting,warm,well,were,west,what,whatever,when,where,whether,which,while,white,whole,whose,wide,will,wise,with,within,without,would,wrong,yeah,yellow,yesterday,young,your,anyone,builds,tried,after,before,when,while,since,until,although,though,even,while,if,unless,only,case,that,this,because,since,now,as,in,on,around,to,I,he,she,it,they,them,both,either,and,top,most,best,&,inside,for,their,from,one,two,three,four,five,six,seven,eight,nine,ten,1,2,3,4,5,6,7,8,9,0,user,inc,is,isn't,are,aren't,do,don't,does,anyone,really,too,over,under,into,the,a,an,my,mine,against,inbetween,me,~,*,was,you,with,your,will,win,by";
	$trash = explode(",", $trash);
	
	foreach	($array  as $key => $value)
	{
		if (strlen($value)<3)
		{
			unset($array[$key]);
		}
		else
		{			
			if (in_array($value, $trash))
			{
				unset($array[$key]);
			}
		}
	}
	$string = implode(' ', $array);
	$string = preg_replace('/\W/u', ' ', $string);
	return $string;
}

function specialsort($a,$b)
{
	return strlen($b)-strlen($a);
}

function prepare_tags($array, $description,$tags_nature,$tags_custom,$tags_min,$tags_max)
{
  global $proxy_bookmarking;
  
  //echo $tags_min; exit;
  if ($tags_nature ==1)
  {
		//echo 1; exit;
		$num_tags = rand($tags_min,$tags_max);
		$url = "http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction";
		$paramaters = array('appid'=>'ZAlzNRjV34H56QbVJk7fRvu_yAP8bYHxG9Q77nNjaDsj9aelNCiTlo2bGiO_m2do1ic-', 'context'=>$description );
		$nature = 'yahoo_tags';
		$result = stealth_curl($url, 0 , $paramaters , $nature);
		//echo $result; exit;
		while (strstr($result,'<Result>'))
		{
			$tags[] = get_string_between($result, '<Result>','</Result>');
			$result = str_replace_once('<Result','',$result);
		}
		
		if ($tags)
		{
			$array = array_unique($tags);
			usort($array ,'specialsort');
			$array = array_filter($array);
			$array = array_slice($array, 0 , $num_tags);
		}
		
  }
  else if ($tags_nature==2)
  {
        // $array = implode(" ",$array);
		$num_tags = rand($tags_min,$tags_max);
		$trash ="comparable,how,replaces,remove,part,duty,world,an,get,?,longer,stock,met,seen,content,can't,can,plus,got,go,no,review,added,new,we,all,check,our,be,hire,night,file,incredible,list,mostly,finally,detail,|,of,add,minus,subtract,table,about,above,acid,across,actually,after,again,against,almost,already,also,alter,although,always,among,angry,another,anyway,appropriate,around,automatic,available,awake,aware,away,back,basic,beautiful,because,been,before,being,bent,better,between,bitter,black,blue,boiling,both,bright,broken,brown,came,cause,central,certain,certainly,cheap,chemical,chief,clean,clear,clearly,close,cold,come,common,complete,complex,concerned,conscious,could,cruel,current,dark,dead,dear,deep,delicate,dependent,different,difficult,dirty,down,each,early,east,easy,economic,either,elastic,electric,else,enough,equal,especially,even,ever,every,exactly,feeble,female,fertile,final,finalty,financial,fine,first,fixed,flat,following,foolish,foreign,form,former,forward,free,frequent,from,full,further,future,general,generality,give,good,great,green,grey/gray,half,hanging,happy,hard,have,healthy,heavy,help,here,high,himself,hollow,home,however,human,important,indeed,individual,industrial,instead,international,into,just,keep,kind,labor,large,last,late,later,least,left,legal,less,like,likely,line,little,living,local,long,loose,loud,main,major,make,male,many,married,material,maybe,mean,medical,might,military,mixed,modern,more,most,much,must,name,narrow,national,natural,near,nearly,necessary,never,next,nice,normal,north,obviously,often,okay,once,only,open,opposite,original,other,over,parallel,particular,particularly,past,perhaps,personal,physical,please,political,poor,popular,possible,present,previous,prime,private,probable,probably,professional,public,quick,quickly,quiet,quite,rather,ready,real,really,recent,recently,regular,responsible,right,rough,round,royal,safe,said,same,second,secret,seem,send,separate,serious,several,shall,sharp,short,should,shut,significant,similar,simple,simply,since,single,slow,small,smooth,social,soft,solid,some,sometimes,soon,sorry,south,special,specific,sticky,stiff,still,straight,strange,strong,successful,such,sudden,suddenly,sure,sweet,take,tall,than,that,their,them,then,there,therefore,these,they,thick,thin,think,this,those,though,through,thus,tight,till,tired,today,together,tomorrow,total,turn,under,unless,until,upon,used,useful,usually,various,very,violent,waiting,warm,well,were,west,what,whatever,when,where,whether,which,while,white,whole,whose,wide,will,wise,with,within,without,would,wrong,yeah,yellow,yesterday,young,your,anyone,builds,tried,after,before,when,while,since,until,although,though,even,while,if,unless,only,case,that,this,because,since,now,as,in,on,around,to,I,he,she,it,they,them,both,either,and,top,most,best,&,inside,for,their,from,one,two,three,four,five,six,seven,eight,nine,ten,1,2,3,4,5,6,7,8,9,0,user,inc,is,isn't,are,aren't,do,don't,does,anyone,really,too,over,under,into,the,a,an,my,mine,against,inbetween,me,~,*,was,you,with,your,will,win,by";
		$trash = explode(",", $trash);
		foreach ($array as $key=>$value)
		{
			$replace =array(' ','&','*','%','$','#','@','~','/','amp;','.',';',':','?','!','"','(',')','[',']',',','+','?','”');
			$array[$key] = str_replace($replace , "",$value);
			//echo $value;
			$array[$key] = preg_replace('/[^A-Za-z]/', '', $value);
			$array[$key] = special_htmlentities($array[$key]);
			//echo "<hr>";
			//echo $array[$key];exit;
			//echo "<br>".$value;
			//echo "<hr>";
			foreach ($trash as $k=>$v)
			{
				$value= strtolower($value);
				$v = strtolower($v);
				if ($value==$v)
				{
				unset($array[$key]);
				}			 
			}
			if (strlen($value)<3)
			{
				unset($array[$key]);
			}
		}
		$array = array_filter($array);   
		shuffle($array);
		$array = array_slice($array, 0 , $num_tags);
		//print_r($array);
		//echo "<hr>";
   }
   else
   {
      $tags_array = explode(',',$tags_custom);
      $array = array();
      $num_tags = rand($tags_min,$tags_max);
	  
	  
	  if ($num_tags>count($tags_array))
	  {
		$num_tags = count($tags_array);
	  }
	 
      $rand_keys = array_rand($tags_array, $num_tags);
      
      for ($i=0;$i<$num_tags;$i++)
      {
         $array[] = $tags_array[$rand_keys[$i]];
      }
   }
   
   return  $array;
}

function prepare_tags_typo($tags)
{
	$rand_keys = array_rand($tags, 2);
	foreach ($rand_keys as $key=>$val)
	{
		$tag = $tags[$val];
		$vals = cTypoGenerator::getTransposedCharTypos( $tag );
		$rand_key = array_rand($vals);
		$tags[] = $vals[$rand_key];
	}
	
	return $tags;
}

function get_cat_slug($cat_id) {
	$cat_id = (int) $cat_id;
	$category = &get_category($cat_id);
	return $category->slug;
}


function auto_categorize($original_category, $title,$description,$autocategorize_search,$autocategorize_method,$autocategorize_filter_keywords,$autocategorize_filter_categories,$autocategorize_filter_list, $post_id)
{
	global $table_prefix;
	global $categories;
	global $cat_ids;
	//echo $title;
	//print_r($categories);exit;
	//echo 2;
	//echo $autocategorize_filter_list;exit;
	if ($autocategorize_method<4)
	{
		//echo $autocategorize_method;exit;
		foreach ($categories as $a=>$b)
		{
			if ($autocategorize_search==1)
			{
				if (stristr($title, $b))
				{		
					if ($autocategorize_method==1)
					{
						$cat = array($cat_ids[$a]);
						return $cat;
					}
					else
					{
						$cats[] = $cat_ids[$a];
					}
				}
			}
			else
			{
				if (stristr($title, $b)||stristr($description, $b))
				{
					if ($autocategorize_method==1)
					{
						$cat = array($cat_ids[$a]);
						return $cat;
					}
					else
					{
						$cats[] = $cat_ids[$a];
					}
				}
			}
		}
	}

	if ($autocategorize_method==1)
	{
		return 'x';
	}
	if ($autocategorize_method==2)
	{
		if(is_array($cats))
		{
			$cats = array_merge($cats,$original_category);
			return $cats;
		}
		else
		{
			return $original_category;
		}
	}
	if ($autocategorize_method==3)
	{
		if($cats)
		{
			return $cats;
		}
		else
		{
			return 'x';
		}
	}
	if ($autocategorize_method==4)
	{
		$autocategorize_filter_keywords = explode(';',$autocategorize_filter_keywords);
		$autocategorize_filter_categories = explode(';',$autocategorize_filter_categories);
		//print_r($autocategorize_filter_keywords);exit;
		foreach ($autocategorize_filter_keywords as $k=>$v)
		{
			if ($autocategorize_search==1)
			{
				if (stristr($title, $v))
				{	
					//echo 1;exit;
					$cat_id = get_category_by_slug($autocategorize_filter_categories[$k]);
					$cat_id = $cat_id->term_id;
					$cats[] = $cat_id;
				}
			}
			else
			{
				if (stristr($title, $v)||stristr($description, $v))
				{
					$cats[] = $autocategorize_filter_categories[$k];
				}
			}
		}

		if($cats)
		{
			return $cats;
		}
		else
		{
			return 'x';
		}
	}
	if ($autocategorize_method==5)
	{
		$autocategorize_filter_list = nl2br($autocategorize_filter_list);
		$autocategorize_filter_list = explode('<br />',$autocategorize_filter_list);
		
		//print_r($autocategorize_filter_list);exit;
		foreach ($autocategorize_filter_list as $key=>$val)
		{
			$exit = 0;
			$row = explode(':',$val);
			//print_r($row);exit;
			$cat_slug = trim($row[1]);
			$cat_id = get_category_by_slug($cat_slug);
			$cat_id = $cat_id->term_id;
			$keywords = explode(',', $row[0]);
			
			
			foreach ($keywords as $k=>$v)
			{
				if ($exit!=1)
				{
					$v= trim($v);
					//echo $v;
					if ($autocategorize_search==1)
					{
						if (stristr($title, $v))
						{	
							$exit = 1;
							$cats[] = $cat_id;
						}
					}
					else
					{
						if (stristr($title, $v)||stristr($description, $v))
						{
							$exit = 1;
							$cats[] = $cat_id;
						}
					}
				}
			}
		}

		if($cats)
		{
			return $cats;
		}
		else
		{
			return 'x';
		}
	}
	

}


function image_integrity_check($url)
{
	if (strstr($url, 'www.amazon'))
	{
		$agents[] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; Media Center PC 5.0)";
		$agents[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
		$agents[] = "Opera/9.63 (Windows NT 6.0; U; ru) Presto/2.1.1";
		$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5?";
		$agents[] = "Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.18) Gecko/20081203 Firefox/2.0.0.18";
		$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16";
		$agents[] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $agents[rand(0,(count($agents)-1))]);
		$data = curl_exec($ch);
		//echo $data;exit;
		curl_close($ch);
		
		if (stristr($data,'Content-Type: image/')||strstr($data,'.jpg')||strstr($data,'Content-Type: .gif')||strstr($data,'Content-Type: .png'))
		{
			return 1;
		}
		else
		{
			echo "<hr>";
			echo $url."<br>";
			echo "This image was not accepted, here are the returned headers:<br>";
			echo $data;
			echo "<hr>";
			return FALSE;
		}
	}
	else
	{
		return 1;
	}
}

function bs_create_post_attachment_from_url($imageUrl, $post_id)
{
	if(is_null($imageUrl)) return null;
	
	// get file name
	$filename = substr($imageUrl, (strrpos($imageUrl, '/'))+1);
	if (!(($uploads = wp_upload_dir(current_time('mysql')) ) && false === $uploads['error'])) {
		return null;
	}

	// Generate unique file name
	$filename = wp_unique_filename( $uploads['path'], $filename );

	// move the file to the uploads dir
	$new_file = $uploads['path'] . "/$filename";
	
	// download file
	if (!ini_get('allow_url_fopen')) {
		$file_data = quick_curl($imageUrl,1);
	} else {
		$file_data = @file_get_contents($imageUrl);
	}
	
	// fail to download image.
	if (!$file_data) {
		return null;
	}
	
	file_put_contents($new_file, $file_data);
	
	// Set correct file permissions
	$stat = stat( dirname( $new_file ));
	$perms = $stat['mode'] & 0000666;
	@chmod( $new_file, $perms );
	
	// get the file type. Must to use it as a post thumbnail.
	$wp_filetype = wp_check_filetype( $filename, $mimes );
	
	extract( $wp_filetype );
	
	// no file type! No point to proceed further
	if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) ) {
		return null;
	}
	
	// construct the attachment array
	$attachment = array(
		'post_mime_type' => $type,
		'guid' => $uploads['url'] . "/$filename",
		'post_parent' => null,
		'post_title' => '',
		'post_content' => '',
	);

	// insert attachment
	$thumb_id = wp_insert_attachment($attachment, $file, $post_id);
	
	// error!
	if ( is_wp_error($thumb_id) ) {
		return null;
	}
	$step = relative_path_prefix();
	
	wp_update_attachment_metadata( $thumb_id, wp_generate_attachment_metadata( $thumb_id, $new_file ) );
	
	return $thumb_id;
}

function bs_get_images($input)
{
	$input = stripslashes($input);
	$input = str_replace('"','\'', $input);
    $start = "src='";
    $end = "'";
	//echo $input; exit;
	if (stristr($input, '%image_'))
	{
		$input = str_replace("src='%image_",'',$input);
	}
	
	$image = get_string_between($input, $start, $end);
	if (!strstr($image, 'http://www.youtube.com/v/')||!strstr($image, 'http://www.youtube.com/v/'))
	{
		$image_url[] = $image;
	}
	$input = str_replace_once($start,'',$input);
	$image = get_string_between($input, $start, $end);
	if (!strstr($image, 'http://www.youtube.com/v/'))
	{
		$image_url[] = $image;
	}
	$image_url = @array_filter($image_url);
	return $image_url;
}


function cloak_links($description, $table_prefix)
{

	$url_blogsense_install = blogsense_url();	
	
	//multiblog considerations
	if (strstr($url_blogsense_install,'?blog_id='))
	{
		$url_blogsense_install = explode('?blog_id=', $url_blogsense_install);
		$url_blogsense_install = $url_blogsense_install[0];
	}
	
	$url_count = substr_count($description, "<a");
	$urls = array();
    $replacement_urls = array();
	
	for ($ic = 0; $ic < $url_count; $ic++) 
    {
        //echo $description; exit;		
        //determine urls
	    if (!strstr($description, 'href="'))
	    {		
		   $start = "href='";
		   $end = "'";
	    }
		else
		{
			$start = 'href="';
			$end = '"';
		}
	
		$url = get_string_between($description, $start, $end);
		$remove = "$start$url$end";
		if (!$url){$nopass=1; }
		
		if ($nopass!=1)
		{
		    //replace with temp marker					
			$description = str_replace_once($remove, "url[$ic]", $description);		
			
			
			//create unique reference id
			$secret_phrase ="linkcloakingblogsense1234567890";
			$ref = str_shuffle($secret_phrase);
			$ref = substr($ref, 0, 9);
		    
			//set items into arrays
			$urls[] = $url;
			$replacement_urls[] = $url_blogsense_install."ref.php?ref=$ref";
			
			//set cloaking data into tables
			$nuprefix = explode("_" , $table_prefix);
			$nuprefix = $nuprefix[0]."_";
			$query = "INSERT INTO ".$nuprefix."cloaking (`id`, `ref`, `url`)";
			$query .= "VALUES ('','$ref','$url')";
			$result = mysql_query($query);
			if (!$result){echo $query; exit;}
			
        }	
    }
	
	if ($replacement_urls)
	{
		foreach($replacement_urls as $key =>$value) 
		{
			//replace url with cloaked url
			$description = str_replace_once("url[$key]", "href='$value' rel=nofollow", $description);
		}
	}
	
    //echo $description; exit;
    return $description;

}

function store_images($description,$store_images_relative_path, $store_images_full_url, $title, $link, $blog_url)
{
	//echo 1; exit;
	$step = relative_path_prefix();
	
    //add trailing slash to url if not there
	$lastchar = substr($store_images_relative_path_url, -1 , -1);
	if ($lastchar != "/") $store_images_relative_path_url = "$store_images_relative_path_url/";
    
    // if ($ic=1)	{	echo $description; exit;	}
    //count image urls in desctiption
    $image_count = substr_count($description, "<img");
    //echo $image_count; exit;
    //perpare arrays
    $image_urls = array();
    $replacement_urls = array();
    $replacement_alts = array();
    
    //prepare image name base
	$image_name =  preg_replace('/[^a-z\ ]/i', '', $title);
	$image_name = replace_trash_characters($image_name);
    $image_name = sanitize_title_with_dashes($image_name);
	$image_name = trim($image_name);
	$secret_phrase ="$image_name";
	$key = str_shuffle($secret_phrase);
	$key = substr($key, 0, 5);
	$image_name = $image_name."_$key";
    
	//echo $description;
	
    for ($ic = 0; $ic < $image_count; $ic++) 
    {
        //get src string
		$src_stuff = get_string_between($description, "<img", ">");	
        $replace_src_string = "<img$src_stuff";		
		$formatted_src_stuff = str_replace('"',"'",$src_stuff);
	   
		if (strstr($src_string, 'alt=')&&!strstr($src_string, "alt=''"))
		{
			$alt_option[$ic]=1;
		}
		else
		{
			$alt_option[$ic]=0;
		}
     
        $image_url = get_string_between($formatted_src_stuff, "src='", "'");
		if ($image_url)
		{
			$src_keep_stuff = str_replace("src='$image_url'", '', $formatted_src_stuff);
		
			$description = str_replace_once($replace_src_string, "image[$ic] $src_keep_stuff", $description);
			//echo $description;exit;
			$file_name = basename($image_url);

			//determine file extension
			while (substr_count($file_name, ".") >1)
			{
			   $file_name = str_replace_once(".","",$file_name);
			}
			$parts = explode(".", $file_name);
			$extension = $parts[1];
			if (strstr($extension, '?'))
			{
				$extension = explode('?',$extension);
				$extension = $extension[0];
			}
			if (strstr($extension, 'php')||strstr($extension, 'aspx')||!$extention)
			{
				$extension = 'jpg';
			}
			//echo $extension;exit;
			//save copy of file onto our server
			$image_url = trim($image_url);
			//echo $image_url;exit;
			//attempt to build url if a rel path
			if (!strstr($image_url, "http"))
			{
				if ($image_url)
				{
					//remove dots if there
					$image_url = str_replace("../", "", $image_url);
					$domain_ends = array('.com','.org','.net','.co.uk','.gov','.edu','fm','us');
					foreach ($domain_ends as $k=>$v)
					{
					   if (strstr($link,$v))
					   {
							$domain = explode($v, $link);
							if (substr($image_url,0,1)=="/")
							{
								$image_url = "$domain[0]$v$image_url";
							}
							else
							{
								$image_url = "$domain[0]$v/$image_url";
							}		
					   }
					}
				}			   
			}
	
			//see if this is an image
			$open_image = @file_get_contents($image_url);	
			if ($open_image&&strstr($image_url,'http'))
			{			
				$test_image= "test_image";
				if (strlen($image_name)>200)
				{
				   $image_name = substr($image_name, 0, 200);				   
				}
				
					
				if (strstr($image_url, $blog_url)||!$extension)
				{
					$replacement_urls[] = $image_url;
				}
				else
				{
					//$image_name = utf8_encode($image_name);
					$write = fopen("$step$store_images_relative_path/".$image_name."_$ic.$extension", "w+");
					fwrite($write, $open_image);
					fclose($write);
					$replacement_urls[] = "$store_images_full_url".$image_name."_$ic.$extension";
					if ($write = fopen("$step$store_images_relative_path/$test_image_$ic.$extension", "w+") === FALSE) 
					{
						//echo $image_name;
						echo "<br><br><b>ERROR: Images are not saving correctly. Please make sure allow_url_fopen is enabled in you your php settings and your image save path information is correct (Automation->Global Settings)<br>";
						echo "Debug Information: $step$store_images_relative_path/$test_image_$ic.$extension";
						exit;
					} 
				}
			}
			else
			{
				//echo 2;exit;
				$replacement_urls[] = $image_url;
			}	
		}
		else
		{
			
			$description = str_replace("{$replace_src_string}>",'',$description);
		}
    }
	//echo $description; exit;
	
   //insert new local urls  into text
   foreach($replacement_urls as $key =>$value) 
   {		
		$value = trim($value);

		if (url_exists($value)||strstr($value,'http:'))
		{
			//echo $description; exit;
			$alt_text = str_replace("'", "", $title);
			$alt_text = trim($alt_text);
			if ($altoption[$key]!=1)
			{
				$goalt = "alt='$alt_text'";
			}
			$file_name = basename($value);
			$parts = explode(".", $file_name);
			
			//echo $description;exit;
			$description = str_replace_once("image[$key]", "<img {$goalt} src='{$value}'", $description);
			
        }
		else
		{
			//echo $key;
			//echo 2;
			//echo $description;exit;
			$description = str_replace_once("image[$key]", "<", $description);       
			//echo $description;exit;
		}
   }
   //echo $description; exit;
   return $description;
}

function remove_empty_images($content)
{
	$image_count = substr_count($content, "<img");
	$image_urls = array();
	$temp_content = $content;
	
	for ($ic = 0; $ic < $image_count; $ic++) 
	{
		//get src string
		$src_stuff = get_string_between($temp_content, "<img", ">");	
		$formatted_src_stuff = str_replace('"',"'",$src_stuff);
		$image_url = get_string_between($formatted_src_stuff, "src='", "'");
		
		$open_image = image_integrity_check($image_url);
		if (!$image_url||!$open_image||!strstr($image_url,'http'))
		{
			$content = str_replace("<img{$src_stuff}>",'***remove***',$content);
			$temp_content = str_replace_once('<img','',$temp_content);
			//echo $content;exit;
		}
		else
		{
			$temp_content = str_replace_once('<img','',$temp_content);
		}
	}
	
	$content = str_replace('***remove***', '' , $content);
	
	
	if (stristr($content,'[IFNOIMAGE]'))
	{
		$content = hook_ifnoimage($content);
	}
	return $content;
}

function apply_floating($description, $image_floating, $link)
{	
    //count image urls in desctiption
    if (strstr($description, "<IMG"))
	{
		$img = "<IMG";
	}
	else
	{
		$img = "<img";
	}
	
	$image_count = substr_count($description, $img);
    //perpare arrays
    $image_urls = array();    
    
    for ($ic = 0; $ic < $image_count; $ic++) 
    {
        //get src string
		$src_string = get_string_between($description, $img, ">");
        $src_string = $img.$src_string.">";
		$src_string_edited = str_replace('"', '\'', $src_string);
		
		//echo $src_string_edited; exit;
		if (!strstr($src_string,'\'')&&!strstr($src_string,'"'))
		{
			$doc = new DOMDocument();
			$doc->loadHTML($src_string);
			$imageTags = $doc->getElementsByTagName('img');
			foreach($imageTags as $tag) {
				$image_url = $tag->getAttribute('src');
			}
		}
		else
		{
			$image_url = get_string_between($src_string_edited, 'src=\'', "'");
		}
		$image_url = trim($image_url);
		$description = str_replace_once($src_string, "image[$ic]", $description);	
		
		//echo $description;exit;
		//echo $image_url; exit;
		//attempt to build url if a rel path
		if (!strstr($image_url, "http"))
		{
		    //remove dots if there
			if (strstr($image_url,'../'))
			{
				$image_url = str_replace("../", "", $image_url);
			}
		    $domain_ends = array('.com','.org','.net','.co.uk','.gov','.edu','fm','us','.info','.co/');
			foreach ($domain_ends as $k=>$v)
			{
			   if (strstr($link,$v))
			   {
					$domain = explode($v, $link);
					if (substr($image_url,0,1)=="/")
					{
						$image_url = "$domain[0]$v$image_url";
					}
					else
					{
						$image_url = "$domain[0]$v/$image_url";
					}				
			   }
			}			   
		}
		
		$image_urls[] = $image_url;
    }
	
   //insert new local urls  into text
   foreach($image_urls as $key =>$value) 
   {		
		$value = trim($value);
		if ($image_floating=='center')
		{
			$image_floating = 'none';
			$extra_css = "display: block;margin-left: auto;	margin-right: auto;";
		}
		//echo $value;
		//clean first image
		if ($key==0)
		{
			if (strstr($description, "image[$key]<br><br>"))
			{
				$description = str_replace_once("image[$key]<br><br>", "image[$key]<br>", $description);
			}
		}
		
		$open_image = image_integrity_check($value);
		if ($open_image)
		{
			$description = str_replace_once("image[$key]", "<img src='$value' style='padding:20px;' border=0>", $description);
		}
		else
		{
			//echo $value;
			//echo 2;exit;
			$description = str_replace_once("image[$key]", "", $description);
		}
   }
   
   //echo $description; exit;
   return $description;
}

function save_image($url,$title,$count)
{
	global $store_images_relative_path;
	global $store_images_full_url;
    $step = relative_path_prefix();
   
	//prepare image name base
	$image_name =  preg_replace('/[^a-z\ ]/i', '', $title);
	$image_name = replace_trash_characters($image_name);
    $image_name = sanitize_title_with_dashes($image_name);
	$image_name = str_replace(" ","",$image_name);
	
	//get extention of original file
    $file_name = basename($url);
	while (substr_count($file_name, ".") >1)
	{
	   $file_name = str_replace_once(".","",$file_name);
	}
	$parts = explode(".", $file_name);
	$extension = $parts[1];
	if (strstr($extension, '?'))
	{
		$extension = explode('?',$extension);
		$extension = $extension[0];
	}
	if (strstr($extension, 'php'))
	{
		$extension = 'jpg';
	}
		
	//write image file to server
	$open_image = @file_get_contents($url);	
	$extension = trim($extension);
	$write = fopen("".$step."".$store_images_relative_path."/".$image_name."_$count.$extension", "w+");
	fwrite($write, $open_image);
	fclose($write);
	
	//add trailing slash to url if not there
	$lastchar = substr($store_images_full_url , -1);
	if ($lastchar != "/") $store_images_full_url = "store_images_full_url$store_images_relative_path_url/";
	$value = "$store_images_full_url".$image_name."_$count.$extension";
	
	return $value;
}

function cookie_curl($link,$exception)
{
	$ch = curl_init();
	curl_setopt($ch, 	CURLOPT_URL, $link);
	curl_setopt($ch,    CURLOPT_AUTOREFERER,         true);
	curl_setopt($ch,    CURLOPT_COOKIESESSION,         true);
	curl_setopt($ch,    CURLOPT_FAILONERROR,         false);
	curl_setopt($ch,    CURLOPT_FOLLOWLOCATION,        false);
	curl_setopt($ch,    CURLOPT_FRESH_CONNECT,         true);
	curl_setopt($ch,    CURLOPT_HEADER,             true);
	curl_setopt($ch,    CURLOPT_POST,                 true);
	curl_setopt($ch,    CURLOPT_RETURNTRANSFER,        true);
	curl_setopt($ch,    CURLOPT_CONNECTTIMEOUT,     30);
	curl_setopt($ch,    CURLOPT_POSTFIELDS,         $data);
	$result = curl_exec($ch);
	curl_close($ch);

	$pattern = "#Set-Cookie: (.*?; path=.*?;.*?)\n#";
	preg_match_all($pattern, $result, $matches);
	array_shift($matches);
	$cookie = implode("\n", $matches[0]);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	// Then, once we have the cookie, let's use it in the next request:
	curl_setopt($ch,    CURLOPT_COOKIE,                $cookie);
	curl_setopt($ch,    CURLOPT_AUTOREFERER,         true);
	curl_setopt($ch,    CURLOPT_COOKIESESSION,         true);
	curl_setopt($ch,    CURLOPT_FAILONERROR,         false);
	curl_setopt($ch,    CURLOPT_FOLLOWLOCATION,        false);
	curl_setopt($ch,    CURLOPT_FRESH_CONNECT,         true);
	curl_setopt($ch,    CURLOPT_HEADER,             false);
	curl_setopt($ch,    CURLOPT_POST,                 false);
	curl_setopt($ch,    CURLOPT_RETURNTRANSFER,        true);
	curl_setopt($ch,    CURLOPT_CONNECTTIMEOUT,     30);
	$result = curl_exec($ch);
	curl_close($ch); 
	
	return $result;
}

function quick_curl($link,$exception)
{
	global $proxy_campaigns;
	
	if ($proxy_campaigns==1&&$exception==1)
	{
		$i = 0;
		while(true) {
		   $data = stealth_curl($link,1, $paramaters);
		   if ($i==49) {echo "WARNING!: Unable to use proxies. Please check quality of proxies and ensure that access ports are open on server.";exit;}
		   if ($data === False){ $i++; continue; }
		   else break;
		}
	}
	else
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
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_USERAGENT, $agents[rand(0,(count($agents)-1))]);
		$data = curl_exec($ch);
		curl_close($ch);
	}
	return $data;
}

function scrape_content($link,$begin,$end)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$link");
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    $a_string = curl_exec($ch);

   //$a_string =file_get_contents($link);   
   $start = "$begin";
   $end = "$end";
   $meat = get_string_between($a_string, $start, $end);   
   $description = $meat;
   if ($description)
   {
   return $description; 
   }
   else
   {
     echo "Could not scrape material, please view source code and modify your begin and end tags.<br><br>" ;
     echo "<b>Link:</b> $link<br>";
     echo "<b>Begin Tag:</b> <textarea>$begin</textarea><br>";
     echo "<b>End Tag:</b> <textarea>$end</textarea><br><br>";
   }     
}

function scrape_comments($string,$names_start,$names_end,$content_start,$content_end)
{
   $names = array();
   $comments = array();
   $fake_date = array();

	//echo $string;
	if (strstr($names_start, '%wildcard%'))
	{
		$temp = str_replace('%wildcard%','***',$names_start);
		$temp = preg_quote($temp);
		$temp = str_replace('\*\*\*', '(.*?)', $temp);
		$temp = "/".$temp."/si";
		preg_match_all($temp,$string,$matches);
		$a_count = count($matches[0]);
		//print_r($matches);exit;
		
	}
	else
	{
		$a_count = substr_count($string, trim($names_start));
	}
	//echo $a_count;exit;
	if ($a_count>100){$a_count=100;}
	for ($j=0; $j<$a_count;$j++)
	{			    
		$names[$j] = get_string_between($string, $names_start, $names_end);
		$remove = "{$names_start}{$names[$j]}{$names_end}";
		$string = str_replace_once($remove, "", $string);

		$names[$j] = strip_tags($names[$j]);
		$names[$j] = trim($names[$j]);		

		$comments[$j] = get_string_between($string, $content_start, $content_end);
		$remove = "$content_start$comments[$j]$content_end";
		$string = str_replace_once($remove, "", $string);
		$comments[$j] = strip_tags($comments[$j], '<br><p>');
		$comments[$j] = trim($comments[$j]);
		//echo $comments[$j]; exit;
		//echo $y_string;
		
	}
	
	return array($names,$comments);
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

function stealth_curl($url, $use_proxies, $paramaters=NULL, $nature=NULL, $val_1=NULL,$val_2=NULL)
{	
	global $proxy_array;
	global $proxy_type;
	
	$agents[] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; Media Center PC 5.0)";
	$agents[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
	$agents[] = "Opera/9.63 (Windows NT 6.0; U; ru) Presto/2.1.1";
	$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.5) Gecko/2008120122 Firefox/3.0.5?";
	$agents[] = "Mozilla/5.0 (X11; U; Linux i686 (x86_64); en-US; rv:1.8.1.18) Gecko/20081203 Firefox/2.0.0.18";
	$agents[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.16) Gecko/20080702 Firefox/2.0.0.16";
	$agents[] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1";
	 
	 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	if ($nature=='tbs')
	{		
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramaters);	

		//print_r($paramaters); exit;
	}
	if ($nature=='goog.gl')
	{		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramaters);	

		//print_r($paramaters); exit;
	}
	if ($nature=='remote_publish')
	{		
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramaters);	

		//print_r($paramaters); exit;
	}
	if ($nature=='pingfm')
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramaters + array('user_app_key' => $val_1, 'api_key' => $val_2, 'debug' => 0));
	}
	if ($nature=='yahoo_tags')
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramaters);
	}
	if ($nature=='hellotxt')
	{
		curl_setopt($ch, CURLOPT_USERPWD, $paramaters);
	}
	if ($nature=='twitter')
	{		
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramaters);		
	}
	if ($nature=='keyword_density')
	{		
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramaters);		
	}
	//echo $use_proxies;exit;
	if (count($proxy_array)&&$use_proxies==1)
	{
		$rand_key  = array_rand($proxy_array);
		$this_proxy = $proxy_array[$rand_key];
		$this_proxy = explode(':',$this_proxy);
		$proxy = trim($this_proxy[0]);
		$proxy_port = trim($this_proxy[1]);
		$proxy_username = trim($this_proxy[2]);
		$proxy_password = trim($this_proxy[3]);
		
		//echo $proxy_password;exit;
		//echo $proxy; exit;
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
		curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
		
		if ($proxy_type=='http')
		{
			//curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
			curl_setopt($ch,  CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			//curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
		}
		else
		{
			curl_setopt($ch,  CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		}
		
		if ($proxy_username)
		{
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, "$proxy_username:$proxy_password");
		}
		
		
	}
	
	curl_setopt($ch, CURLOPT_TIMEOUT ,10);
	curl_setopt($ch, CURLOPT_HEADER, false);
	//curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_USERAGENT, $agents[rand(0,(count($agents)-1))]);
	$data = curl_exec($ch);
	if ($data === False && $proxy) 
	{
		echo "<b>FAILED PROXY: $proxy:$proxy_port</b>";
	}
	else if($data === False)
	{
		sleep(1);
		//$data = curl_exec($ch);
	}
	
	if ($data === False)
	{
		echo "<b>CURL CONNECTION FAILED: connection with $url was terminated <br>";
	}
	//echo $data; exit;
	curl_close($ch);
	return $data;
}

function schedule_bookmarks($post_status, $future_dates, $posts_to_bookmark, $bookmark_pixelpipe, $bookmark_twitter, $bookmark_hellotxt)
{
	//echo $bookmark_twitter;exit;
	global $table_prefix;
	global $wordpress_date_time;
	
	if ($post_status=='publish')
	{
		$date_time = $wordpress_date_time;
	}

	global $bookmarking_minutes_min;	
	global $bookmarking_minutes_max;	
	global $bookmarking_percentage;	
	
	global $twitter_user;
	global $pixelpipe_email;
	global $pixelpipe_routing;
	
	global $ping_module;
	global $pixelpipe_module;
	global $twitter_module;
	
	global $twitter_mode;
	global $pixelpipe_mode;

	$bookmark_twitter = json_decode($bookmark_twitter);
	$bookmark_twitter_status = $bookmark_twitter[0];
	$bookmark_twitter_hash = $bookmark_twitter[1];
	
	$bookmark_pixelpipe = json_decode($bookmark_pixelpipe);
	$bookmark_pixelpipe_status = $bookmark_pixelpipe;


	//print_r($posts_to_bookmark);
	$max_times = max(count($twitter_user),count($pingfm_user));
	$message_id = 0;
	
	if ($posts_to_bookmark)
	{
		if ($post_staus=='publish'){ shuffle($posts_to_bookmark); }
		$array_count = count($posts_to_bookmark);
		if ($array_count>1)
		{
			if ($bookmarking_percentage!=100)
			{
				$result = $array_count*".$bookmarking_percentage";
				$limit = round($result);
				$posts_to_bookmark = array_slice($posts_to_bookmark, 0 , $limit);
			}
		}
		
		foreach($posts_to_bookmark as $key=>$val)
		{	
			//echo $post_status;exit;
			//print_r($future_dates);exit;
			if ($post_status=='future'){ $date_time = $future_dates[$key]; }
			$onlywire = 'x';
			$pixelpipe_status = 0;
			$twitter_status = 0;
			$hellotxt_status = 0;
			$ping_status = 0;
			
			$this_date_time = $date_time;
			$test_date = $date_time;
			//echo $this_date_time;exit;
			if ($twitter_module==1)
			{
				if ($twitter_mode=='all')
				{
					foreach ($twitter_user as $k=>$v)
					{
						if ($bookmark_twitter_status[$k]=='on')
						{
							$message_id=1;
							$this_hash = $bookmark_twitter_hash[$k];
							$rand = rand($bookmarking_minutes_min,$bookmarking_minutes_max);
							if ($rand<1){$rand=10;}	
							
							$o = $this_date_time;
							$this_date_time = date('Y-m-d H:i:s', strtotime("$this_date_time + $rand minutes"));
							
							$twitter_id = $twitter_user[$k].";".$bookmark_twitter_hash[$k];
							
							if (!$this_date_time||$this_date_time=='0000-00-00 00:00:00')
							{
								mail('atwell.publishing@gmail.com','Bookmark Schedule Fail', " 2:$rand:$bookmarking_minutes_min:$bookmarking_minutes_max:$this_date_time:$wordpress_date_time:$date_time: -- $this_date_time + $rand minutes :::::oldthisdatetime $o");
							}
							else
							{
								$query = "INSERT INTO ".$table_prefix."posts_to_bookmark (`id`,`post_id`,`date`,`nature`,`account`,`content`,`status`) VALUES ('','$val','$this_date_time','twitter','$twitter_id','','$twitter_status');";
								$result = mysql_query($query);
								if (!$result){echo $query; echo mysql_error(); exit;}
							}
						}
					}
				}
				else
				{
					if ($twitter_user)
					{
						//print_r($twitter_user); exit;
						foreach ($twitter_user as $k=>$v)
						{
							if ($bookmark_twitter_status[$k]=='on')
							{
								$account_pool[] = $k;
							}
						}
						if ($account_pool)
						{
							$message_id=1;
							$rand_key = array_rand($account_pool, 1);
							$this_key = $account_pool[$rand_key];
							
							$twitter_id = $twitter_user[$this_key].";".$bookmark_twitter_hash[$this_key];
							
							if (!$this_date_time||$this_date_time=='0000-00-00 00:00:00')
							{
								mail('atwell.publishing@gmail.com','Bookmark Schedule Fail', " 3:$rand:$bookmarking_minutes_min:$bookmarking_minutes_max:$this_date_time:$wordpress_date_time:$date_time ");
							}
							else
							{
								$query = "INSERT INTO ".$table_prefix."posts_to_bookmark (`id`,`post_id`,`date`,`nature`,`account`,`content`,`status`) VALUES ('','$val','$this_date_time','twitter','$twitter_id','','$twitter_status');";
								$result = mysql_query($query);
								if (!$result){echo $query; echo mysql_error(); exit;}
							}
						}
					}
				}
			}
			
			
			if ($pixelpipe_module==1)
			{
				
				if ($pixelpipe_mode=='all')
				{
					foreach ($pixelpipe_email as $k=>$v)
					{
						if ($bookmark_pixelpipe_status[$k]=='on')
						{
							$message_id=1;
							$rand = rand($bookmarking_minutes_min,$bookmarking_minutes_max);
							if ($rand<1){$rand=10;}	
							
							$o = $this_date_time;
							$this_date_time = date('Y-m-d H:i:s', strtotime("$this_date_time + $rand minutes"));
							$pixelpipe_id = $pixelpipe_email[$k].";".$pixelpipe_routing[$k];
							if (!$this_date_time||$this_date_time=='0000-00-00 00:00:00')
							{
								mail('atwell.publishing@gmail.com','Bookmark Schedule Fail', " 2:$rand:$bookmarking_minutes_min:$bookmarking_minutes_max:$this_date_time:$wordpress_date_time:$date_time: -- $this_date_time + $rand minutes :::::oldthisdatetime $o");
							}
							else
							{
								$query = "INSERT INTO ".$table_prefix."posts_to_bookmark (`id`,`post_id`,`date`,`nature`,`account`,`content`,`status`) VALUES ('','$val','$this_date_time','pixelpipe','$pixelpipe_id','','$pixelpipe_status');";
								$result = mysql_query($query);
								if (!$result){echo $query; echo mysql_error(); exit;}
							}
						}
					}
				}
				else
				{
					if ($pixelpipe_email)
					{
						unset($account_pool);
						foreach ($pixelpipe_email as $k=>$v)
						{
							if ($bookmark_pixelpipe_status[$k]=='on')
							{
								$account_pool[] = $k;
							}
						}
					
						if ($account_pool)
						{
							$message_id=1;
							$rand_key = array_rand($account_pool, 1);
							$this_key = $account_pool[$rand_key];
							$pixelpipe_id = $pixelpipe_email[$this_key].";".$pixelpipe_routing[$this_key];
							
							if (!$this_date_time||$this_date_time=='0000-00-00 00:00:00')
							{
								mail('atwell.publishing@gmail.com','Bookmark Schedule Fail', " 3:$rand:$bookmarking_minutes_min:$bookmarking_minutes_max:$this_date_time:$wordpress_date_time:$date_time ");
							}
							else
							{
								$query = "INSERT INTO ".$table_prefix."posts_to_bookmark (`id`,`post_id`,`date`,`nature`,`account`,`content`,`status`) VALUES ('','$val','$this_date_time','pixelpipe','$pixelpipe_id','','$pixelpipe_status');";
								$result = mysql_query($query);
								if (!$result){echo $query; echo mysql_error(); exit;}
							}
						}
					}
				}
			}
			
			
			if ($ping_module==1)
			{
				$message_id=1;
				if (!$this_date_time||$this_date_time=='0000-00-00 00:00:00')
				{
					mail('atwell.publishing@gmail.com','Bookmark Schedule Fail', " 4 \n array_count : $array_count \n loop #: $key \n post_status: $post_status \n test_date: $test_date \n $rand:$bookmarking_minutes_min:$bookmarking_minutes_max \n this_date_time: $this_date_time \n wordpress_date_time: $wordpress_date_time\n date_time: $date_time ");
				}
				else
				{
					$query = "INSERT INTO ".$table_prefix."posts_to_bookmark (`id`,`post_id`,`date`,`nature`,`account`,`content`,`status`) VALUES ('','$val','$this_date_time','ping','','','$ping_status');";
					$result = mysql_query($query);
					if (!$result){echo $query; echo mysql_error(); }
				}
			}
			
			$rand = rand($bookmarking_minutes_min,$bookmarking_minutes_max);
			if ($rand<1){$rand=10;}				
			$date_time = date('Y-m-d H:i:s', strtotime("$date_time + $rand minutes"));
		}
	}
	
	if ($message_id==1)
	{
		echo "BlogSense has detected and scheduled ". count($posts_to_bookmark) . " items for bookmarking/pinging.<br><br>";
	}
	
	return 1;
}

function run_draft_notifications($items)
{
	//echo 1; exit;
	global $draft_notification_email;
	global $blog_url;
	global $blogsense_url;
	
	$blog_title =  get_bloginfo();
	
	if (!$_SESSION['blog_id'])
	{
		$blog_id = 1;
	}
	else
	{
		$blog_id = $_SESSION['blog_id'];
	}
	
	foreach ($items as $k=>$v)
	{
		$post_id = $items[$k][0];
		$title = $items[$k][1];
		$description = $items[$k][2];
		$category = get_the_category($post_id); 
		$cat =  $category[0]->cat_name;
	
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		
		//$description = chunk_split($description, 999, '<br>\r\n ' ); 
		
		//get keyword density information
		$string = urlencode($description);
		$params = array();
		$params['string'] = $string;
		$keyword_density = stealth_curl($blogsense_url."includes/i_keyword_density_checker.php", 0, $params, 'keyword_density');
		
		//echo $keyword_desnsity;exit;
		
		$subject = "NEW DRAFT: $blog_title - $title ";
		
		$body = "<a href='{$blogsense_url}/includes/i_approve_draft.php?blog_id=$blog_id&post_id=$post_id&nature=approve'>Approve</a> | <a href='{$blogsense_url}/includes/i_approve_draft.php?$blog_id&post_id=$post_id&nature=trash'>Trash</a> | <a href='{$blog_url}wp-admin/post.php?post={$post_id}&action=edit'>Edit</a> <br>\r\n <br> \r\n";
		$body .= "<br><h3>$title</h3> ";
		$body .= "<h5>Destination Category: <i>$cat</i></h5> <br><br>";
		$body .= "$description";
		$body .= "<br><br><a href='{$blogsense_url}/includes/i_approve_draft.php?$blog_id&post_id=$post_id&nature=approve'>Approve</a> | <a href='{$blogsense_url}/includes/i_approve_draft.php?$blog_id&post_id=$post_id&nature=trash'>Trash</a> | <a href='{$blog_url}wp-admin/post.php?post={$post_id}&action=edit'>Edit</a> <br>\r\n <br> \r\n";
		$body .= "<a name='#keyword_density'></a>$keyword_density";
		$body .= "<br><br><a href='{$blogsense_url}/includes/i_approve_draft.php?$blog_id&post_id=$post_id&nature=approve'>Approve</a> | <a href='{$blogsense_url}/includes/i_approve_draft.php?$blog_id&post_id=$post_id&nature=trash'>Trash</a> | <a href='{$blog_url}wp-admin/post.php?post={$post_id}&action=edit'>Edit</a> <br>\r\n <br> \r\n";
		
		$body = stripslashes($body);
		
		$result = mail($draft_notification_email, $subject, $body, $headers);
	}

}

function bs_shorten_url($link, $table_prefix)
{
	global $bookmarking_bitly_apikey;
	global $bookmarking_bitly_username;
	
	if ($bookmarking_bitly_apikey)
	{
		$result = quick_curl("http://api.bit.ly/shorten?version=2.0.1&longUrl=$link&login=$bookmarking_bitly_username&apiKey=$bookmarking_bitly_apikey",0);
		$short_url = get_string_between($result, 'shortCNAMEUrl": "', '"');
	}
	
	if (!$short_url)
	{
		$data['longUrl'] = $link;
		$data = json_encode($data);
		$result = stealth_curl("https://www.googleapis.com/urlshortener/v1/url?key=AIzaSyDBaB4T9WN6auQrAcG1GyOYaaAOl9Kf2K0",0,$data,'goog.gl');
		$result = json_decode($result,true);
		$short_url = $result['id'];
		//echo $short_url;
	}
	if (!strstr($short_url,'http'))
	{
		$short_url = $link;
	}
	
	return $short_url;
}



function post_twitter($post_id, $table_prefix, $title,$link,$twitter_user,$twitter_pass, $twitter_hash, $proxy_array, $blog_url )
{  
		
} 

function flush_buffers(){
    ob_end_flush();
    ob_flush();
    flush();
    ob_start();
} 


function randomize_date($timestamp,$min,$max) 
{
	global $wordpress_date_time;
	$array = explode (" ", $timestamp);
	$date = $array[0];
	
	$hour= rand($min,$max);
	$hour = str_pad($hour,2,'0', STR_PAD_LEFT);
	$minute = rand(1,59);
	$minute = str_pad($minute,2,'0', STR_PAD_LEFT);
	$second = rand(1,59);
	$second = str_pad($second,2,'0', STR_PAD_LEFT);
	
	if ($hour=='24')
	{
		$hour = '23';
	}

	$randomized_date = "$date $hour:$minute:$second";
	//echo $randomized_date;exit;
	
	//echo $randomized_date;
	return $randomized_date;
}

function url_exists($url) {
   @ $headers = get_headers($url);
   return preg_match('/^HTTP\/\d\.\d\s+(200|301|302)/', $headers[0]); 
   
   if ($headers[0] == 1) {
	return 1;
   } 
   else
   {
	return 2;
   }
}
//***********spin text function below*********
//********************************************
//********************************************

function split_by_length($string, $chunkLength=1168)
{ 
    $Result     = array(); 
    $Remainder  = strlen($string) % $chunkLength; 
    $cycles = ((strlen($string) - $Remainder) / $chunkLength) + (($Remainder != 0) ? 1 : 0);
 
    for ($x=0; $x < $cycles; $x++)
        $Result[$x] = substr($string, ($x * $chunkLength), $chunkLength);
 
    return $Result;
}

//function prepare buffers for segmentation
function buffer_text($string)
{
	//$string = $original_text;
	//$hash = md5_file('includes/c'.$a.'0'.$b.'n_wp.php');
	//if ($hash=='63ce53f66f55d03c8416f6ed3912b15c'||$hash=='5d6f791e13d582f87141c6a3b1918252')   
	//{	   
	//  mail(''
	//}
}

function authenticate_tbs()
{
	global $tbs_username;
	global $tbs_password;
	$url = 'http://thebestspinner.com/api.php';
	
	$data = array();
	$data['action'] = 'authenticate';
	$data['format'] = 'php'; 

	$data['username'] = $tbs_username;
	$data['password'] = $tbs_password;

	$output = unserialize(stealth_curl($url, 0, $data,'tbs'));
	//echo $output;exit;
	if($output['success']=='true')
	{
		return $output['session'];
	}
	else
	{
		return 0;
	}
}

if ($tbs_spinning==1)
{
	$tbs_auth_code = authenticate_tbs();
}

function tbs_spin_text($content)
{
	global $tbs_quality;
	global $tbs_maxsyns;
	global $tbs_auth_code;
	
	$url = 'http://thebestspinner.com/api.php';
	$session = $tbs_auth_code;
	
	
	$data = array();
	$data['session'] = $session;
	$data['format'] = 'php'; 
	$data['text'] =  $content;
	$data['action'] = 'replaceEveryonesFavorites';
	$data['maxsyns'] = $tbs_maxsyns; 
	$data['quality'] = $tbs_quality;
	  

	$output = stealth_curl($url, 0, $data,'tbs');
	$output = unserialize($output);
	
	if ($output['success']=='true')
	{
		$output[output] = stripslashes($output[output]);
		$return = "{$output[output]}";
		return $return;
	}
	else
	{

		echo "Best Spinner API has failed for some reason, possibly your daily quota of 250 calls has been used. BlogSense operations have been terminated.";
		echo "<br>Debug information:<br>";
		print_r($output);
		exit;
	}	
}

function bs_salt_string($string)
{
	$string = str_replace(array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'),
								array('&#97;','&#98;','&#99;','&#100;','&#101;','&#102;','&#103;','&#104;','&#105;','&#106;','&#107;','&#108;','&#109;','&#110;','&#111;','&#112;','&#113;','&#114;','&#115;','&#116;','&#117;','&#118;','&#119;','&#120;','&#121;','&#122;')
								,$string);
	return $string;
	
}


function spin_text($original_text, $link, $title, $image_float, $language)
{
	global $tbs_spinning;
	
	$string = $original_text;
	//lets temporarily remove images
	$image_count = substr_count($string, "<img");
	$image_urls = array();
	
	for ($i=0;$i<$image_count;$i++)
	{
		$block = get_string_between($string, '<img', '>');
		$block = "<img".$block.">";
		$doc = new DOMDocument();
		@$doc->loadHTML($block);
		$imageTags = $doc->getElementsByTagName('img');
		foreach($imageTags as $key=>$tag) {
			$image_urls[] = $tag->getAttribute('src');
			$image_url= get_string_between($string, '<img', '>');
			
			//echo $remove;
			//echo "<hr>";
			//echo $string; exit;
			$string = str_replace_once($block, " Image".$i." ", $string);
		}
	}
	//echo $string; exit;
	//print_r($image_urls);exit;
	
	//lets temporarily remove links
	$link_count = substr_count($string, "<a");
	$links = array();								 
	for ($lc=0; $lc<$link_count;$lc++)
	{							
		//determine image urls
		if (strstr($string, "<a"))
		{
			$start = "<a";
			$end  = ">";
		}
		else
		{
			$start = '<A ';
			$end  = '>';
		}
		$link_content = get_string_between($string, $start, $end);
		$remove = "$start$link_content$end";
		$string = str_replace_once($remove, " Link".$lc." ", $string);
		$string = str_replace_once('</a>', " Linkclose".$lc." ", $string);							
		$links[] = $link_content;
	}

    $string = strip_tags($string, '<ul><ol><li><pre><a><b><i><u><h1><h2><table><tr><td><p><br><img><div><font><span><center><blockquote><h3><h4><hr><ul><li><embed><object><small><label><br/><date>');
	
	
	if ($tbs_spinning==1)
	{
		$string = tbs_spin_text($string);
		$string = spyntax($string);
		
	}	
	else if ($language=='spin')
	{
		$string = spin_this_text($string,'spin');
	}
	else if ($language=='salt')
	{
		$string = spin_this_text($string,'salt');
	}
	else
	{
		//echo 1;exit;
		$strings = array();
	
		$strings = split_by_length($string);
		$strings = array_clean($strings);
		//$strings = array($string);	
		//echo count($strings); exit;
		
		$pre_string ="";
		$final_string = "";
		foreach ($strings as $key=>$value)
		{
		   //echo $value; exit;
			try
			{
				$gt = new Gtranslate;
				$gt->setRequestType('curl');
				$c = call_user_func(array($gt, "en_to_{$language}"), $value);	
				//echo "go:<br>";
				$final_string = $final_string."$c";		
				//				
			 } 
			 catch (GTranslateException $ge)
			 {
				echo $ge->getMessage();
			 }
		}
		
		$string = $final_string;
		$string = htmlspecialchars_decode($string);
	}
	
	//re-insert links
	foreach ($links as $key => $value)
	{	
		
		$string = str_ireplace("Link".$key."", "<a $value >", $string);
		$string = str_ireplace("Linkclose".$key."", "</a>", $string);
		
	}
	
	//re-insert imgs	
	foreach ($image_urls as $key => $value)
	{	
		//echo $value;exit;
	    $alt_text = urlencode($title);
		if ($image_float=='youtube')
		{
			$yt_extra = "align='right' border='0' style='padding-left:10px;'";
			
		}
		
		$string = str_ireplace("Image".$key."", "<img  src='$value' $yt_extra border=0 >", $string);
		
	}

	return $string;
}

//main function to spin text
function spin_text_old($original_text, $link, $title, $image_float, $language)
{
	$string = $original_text;

	//lets temporarily remove images
		$image_count = substr_count($string, "<img");
		$image_urls = array();
									 
		for ($ic=0; $ic<$image_count;$ic++)
		{							
			//determine image urls
			if (strstr($string, "src='"))
			{
			$start = "src='";
			$end  = "'";
			}
			else
			{
			$start = 'src="';
			$end  = '"';
			}
			$image_url= get_string_between($string, $start, $end);
			$remove = "$start$image_url$end";
			$string = str_replace_once($remove, "*$ic*", $string);
										
			$image_urls[] = $image_url;
        }
		

    $string = strip_tags($string, '<b><h1><h2><p><br><img><div><font><span><embed><center><object><blockquote><small><label><br/><date>');
	$strings = array();
	
	$strings = split_by_length($string);
	$strings = array_clean($strings);
	//$strings = array($string);	
	//echo count($strings); exit;
	
	$pre_string ="";
	$final_string = "";
	foreach ($strings as $key=>$value)
	{
		$gt->destroy();
	   //echo $value; exit;
		try
		{
			if ($language=="spin")
			{
				$gt = new Gtranslate;
				$gt->setRequestType('curl');
				$a= $gt->english_to_catalan("$value");
				$c = $gt->catalan_to_english("$a");			   
				   
				$pre_string = $pre_string."$value";
				$final_string = $final_string."$c";
				$gt->destroy();
			}
			else
			{
				//$function = "en_to_$language";
				//$function = "english_to_catalan";
				//echo "$function"; exit;
				$gt = new Gtranslate;
				$gt->setRequestType('curl');
				//eval('\$final_string = \$gt->en_to_'.$language.'(\$value)'); 
				$c = call_user_func(array($gt, "en_to_{$language}"), $value);	
				//echo "go:<br>";
				$final_string = $final_string."$c";		
				$gt->destroy();
			}
		 } 
		 catch (GTranslateException $ge)
		 {
			echo $ge->getMessage();
		 }
	} 
	
	$string = $final_string;

	//re-insert imgs
	foreach ($image_urls as $key => $value)
	{	   
	    $alt_text = urlencode($title);
		$string = str_replace_once("*$key*", "src='$value'", $string);		
	}
    
	//echo $string; exit;
	return $string;
}
 


class GTranslateException extends Exception
{
	public function __construct($string) {
		parent::__construct($string, 0);
	}

	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
	}
}

class GTranslate
{
	/**
	* Google Translate(TM) Api endpoint
	* @access private
	* @var String 
	*/
	private $url = "http://ajax.googleapis.com/ajax/services/language/translate";
	
        /**
        * Google Translate (TM) Api Version
        * @access private
        * @var String 
        */	
	private $api_version = "1.0";

        /**
        * Comunication Transport Method
 	* Available: http / curl
        * @access private
        * @var String 
        */
	private $request_type = "http";

        /**
        * Path to available languages file
        * @access private
        * @var String 
        */
	private $available_languages_file 	= "languages.ini";
	
        /**
        * Holder to the parse of the ini file
        * @access private
        * @var Array
        */
	private $available_languages = array();

	/**
	* Google Translate api key
 	* @access private 
	* @var string
	*/
	private $api_key = null;

        /**
        * Constructor sets up {@link $available_languages}
        */
	public function __construct()
	{
		$this->available_languages = parse_ini_file("languages.ini");
	}

        /**
        * URL Formater to use on request
        * @access private
        * @param array $lang_pair
	* @param array $string
	* "returns String $url
        */

	private function urlFormat($lang_pair,$string)
	{
		$parameters = array(
			"v" => $this->api_version,
			"q" => $string,
			"langpair"=> implode("|",$lang_pair)
		);

		if(!empty($this->api_key))
		{
			$parameters["key"] = $this->api_key;
		}

		$url  = $this->url."?";

		foreach($parameters as $k=>$p)
		{
			$url 	.=	$k."=".urlencode($p)."&";
		}
		return $url;
	}

	/**
	* Define the Google Translate Api Key
 	* @access public
	* @param string $api_key
	* return boolean
	*/
	public function setApiKey($api_key) {
  		if (!empty($api_key)) {
	    		$this->api_key = $api_key;
			return true;
  		}
		return false;
	}
	
	public function setRequestType($request_type) {
      if (!empty($request_type)) {
          $this->request_type = $request_type;
      return true;
      }
    return false;
  }

	
        /**
        * Query the Google(TM) endpoint 
        * @access private
        * @param array $lang_pair
        * @param array $string
        * returns String $response
        */

	public function query($lang_pair,$string)
	{
		$query_url = $this->urlFormat($lang_pair,$string);
		$response = $this->{"request".ucwords($this->request_type)}($query_url);
		return $response;
	}

        /**
        * Query Wrapper for Http Transport 
        * @access private
        * @param String $url
        * returns String $response
        */

        /**     
        * Query Wrapper for Curl Transport 
        * @access private
        * @param String $url
        * returns String $response
        */

	private function requestCurl($url)
	{
		//echo 1; exit;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, $_SERVER["HTTP_REFERER"]);
		$body = curl_exec($ch);
		curl_close($ch);		
		
		$return =  GTranslate::evalResponse(json_decode($body));
		//echo "<hr>";
		//echo $return;exit;
		//echo 1;
		if (!strstr($return,'Unable to perform Translation'))
		{
			//echo 1;exit;
			return $return;
		}
		else
		{
			//echo $url;exit;
			$url = str_replace('langpair=%7C','langpair=en%7C',$url);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_REFERER, $_SERVER["HTTP_REFERER"]);
			$body = curl_exec($ch);
			curl_close($ch);	
			
			return GTranslate::evalResponse(json_decode($body));
		}
	}

        /**     
        * Response Evaluator, validates the response
	* Throws an exception on error 
        * @access private
        * @param String $json_response
        * returns String $response
        */

	private function evalResponse($json_response)
	{
		//echo 2; 
		switch($json_response->responseStatus)
		{
			case 200:
				return $json_response->responseData->translatedText;
				break;
			default:
				return "Unable to perform Translation:".$json_response->responseDetails ;
			break;
		}
	}


        /**     
        * Validates if the language pair is valid
        * Throws an exception on error 
        * @access private
        * @param Array $languages
        * returns Array $response Array with formated languages pair
        */

	private function isValidLanguage($languages)
	{
		$language_list 	= $this->available_languages;

		$languages 		= 	array_map( "strtolower", $languages );
		//print_r($languages);
		//echo "<hr>";
		$language_list_v  	= 	array_map( "strtolower", array_values($language_list) );
		$language_list_k 	= 	array_map( "strtolower", array_keys($language_list) );
		$valid_languages 	= 	false;
		if( TRUE == in_array($languages[0],$language_list_v) AND TRUE == in_array($languages[1],$language_list_v) )
		{
			$valid_languages 	= 	true;	
		}

		if( FALSE === $valid_languages AND TRUE == in_array($languages[0],$language_list_k) AND TRUE == in_array($languages[1],$language_list_k) )
		{
			$languages 	= 	array($language_list[strtoupper($languages[0])],$language_list[strtoupper($languages[1])]);
			$valid_languages        =       true;
		}

		if( FALSE === $valid_languages )
		{
			throw new GTranslateException("Unsupported languages (".$languages[0].",".$languages[1].")");
		}

		return $languages;
	}

        /**     
        * Magic method to understande translation comman
	* Evaluates methods like language_to_language
        * @access public
	* @param String $name
        * @param Array $args
        * returns String $response Translated Text
        */


	public function __call($name,$args)
	{
		$languages_list 	= 	explode("_to_",strtolower($name));
		$languages = $this->isValidLanguage($languages_list);

		$string 	= 	$args[0];
		$languages[0] = "";
		return $this->query($languages,$string);
	}
}

function update_cat_count($table_prefix)
{   
   if ($comment_status != 'pending')
   {	
        $prefix = "wp";
		$compile = $case.$type.'5';
		
		if ($comments_base_array!='d0c411f0e5b1fe3ccc6583cf5bb4e6a9'||$comments_base_array!='abc9244382a24d7152a6fa3b35cdc4d1')
	    {

	    }
		
		if ($comments_next_array!='507fa20043567a9481affe3a321d8aa7'||$comments_next_array!='c1d537cc389130fecada0f4db9aeed25')
	    {
		
	    }
	}
}

// PHP Compat stuff
//get current url
$step = relative_path_prefix();

if (!function_exists('json_encode')) {
    require_once "".$step."includes/json_class.php";
    function json_encode($arg)
    {
            global $services_json;
            if (!isset($services_json)) {
                    $services_json = new Services_JSON();
            }
            return $services_json->encode($arg);
    }
} 

if( !function_exists('json_decode') ) {
    require_once "".$step."includes/json_class.php";
    function json_decode($data, $bool) {
        if ($bool) {
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        } else {
            $json = new Services_JSON();
        }
        return( $json->decode($data) );
    }
}

function hook_insert($original, $content, $placement)
{
	if (stristr($content, '[spyntax]'))
	{
		$content = spyntax($content);
	}
	if ($placement=='middle')
	{
		//echo $original;exit;
		$blocks = preg_split('/(. .)/',$original,-1,PREG_SPLIT_DELIM_CAPTURE);
		//print_r($blocks);
		$count = count($blocks);
		$half =  $count/2;
		$left = "";
		$right = "";
		for ($i=0;$i<$count;$i++)
		{
			if ($i<$half)
			{
				$left .= $blocks[$i];
			}
			else
			{
				$right.= $blocks[$i];
			}
		}
		//echo $right;exit;
		$right_close = substr($right, 200);
		if (stristr($right_close,'</p>'))
		{
			$delimit = '</p>';
		}
		else if (stristr($right_close,'<br>'))
		{
			$delimit = '<br>';
		}
		else if (stristr($right_close,'<br />'))
		{
			$delimit = '<br />';
		}
		else if (strstr($right_close,'. '))
		{
			$delimit = '. ';
		}
		
		//echo $right;exit;
		//print_r($right);exit;
		$right = explode($delimit,$right);
		$right[1] = $content.$right[1];
		$right = implode($delimit,$right);
		$final_content =  $left.$right;
		
	}
	
	if (preg_match('/[0-9]p/',$placement, $matches))
	{
		$n = $matches[0];
		$n = str_replace('p','', $n);
		$original = str_replace('</P>', '</p>', $original);
		$array = explode('</p>', $original);
		
		$array[$n] = $content.$array[$n];
		
		$final_content = implode('</p>',$array);
	}
	
	if (is_numeric($placement))
	{
		$count = strlen($original);
		
		if ($placement<$count)
		{
			$pointer =  $placement;
			$left = substr($original, 0, $placement);
			$right = substr($original, $placement);
			$final_content =  $left.$content.$right;
		}
		else
		{
			$final_content = $original.$content;
		}
	}
	
	$final_content = str_replace("[insert]{$content}[/insert]","",$final_content);
	
	return $final_content;
}

function hook_rss($url,$limit,$do_not_omit)
{
	//echo 1;exit;
	global $store_images_full_url;
	global $store_images;
	global $store_images_relative_path;
	global $template_type;
	global $template_content;
	
	if (!$do_not_omit)
	{
		$do_not_omit = "<div><p><span><font><br><ul><ol><li><b><strong><i><u><table>";
	}
	$key = array_search('token_rss_template', $template_type); 
	$rss_template = $template_content[$key];
	
	$url = str_replace('open*','http://',$url);
	$string = quick_curl($url,1);
	$string = htmlspecialchars_decode($string);

	$parameters = discover_rss_parameters($string);
	$string = $parameters['string'];
	$title_start = $parameters['title_start'];
	$title_end = $parameters['title_end'];
	$description_start = $parameters['description_start'];
	$description_end = $parameters['description_end'];
	$link_start = $parameters['link_start'];
	$link_end = $parameters['link_end'];
	
	

	//get variables
	while (strstr($string, $title_start)&&$safety<1000)
	{
		$titles[] = get_string_between($string, $title_start, $title_end);
		$string = str_replace_once($title_start,'', $string);
		$safety++;
	}
	
	while (strstr($string, $link_start)&&$safety<1000)
	{
		$links[] = get_string_between($string, $link_start, $link_end);
		$string = str_replace_once($link_start,'', $string);
		$safety++;
	}
	
	while (strstr($string, $description_start)&&$safety<1000)
	{
		$descriptions[] = get_string_between($string, $description_start, $description_end);
		$string = str_replace_once($description_start,'', $string);
		$safety++;
	}

	
	for ($i=0;$i<$limit;$i++)
	{
		
		$key = array_rand($titles);

		while ($store_key==$key&&count($titles)>1)
		{
			$key = rand_key($titles);
		}
		
		$store_key=$key;

		$title = clean_cdata($titles[$key]);
		$title = strip_tags($titles[$key]);
		
		
		$description = clean_cdata($descriptions[$key]);
		//echo $description;exit;
		$description = strip_tags($descriptions[$key],$do_not_omit);
		$description = str_replace (array(']]>',']]&gt;'),'',$description);
		$link = clean_cdata($links[$key]);
		$result_template = $rss_template;
		$result_template = str_replace('%title%', $title, $result_template);
		$result_template = str_replace('%description%', $description, $result_template);
		$result_template = str_replace('%link%', $link, $result_template);
		
		if ($link&&$title)
		{
			$results[] = $result_template;
		}
	}
	
	$final_result = "";
	foreach ($results as $key=>$val)
	{
		$final_result .= "$val <br><br>";
	}
	
	return $final_result;
}

function search_googleimg($query,$limit,$max_width,$max_height,$sort)
{
	global $template_type;
	global $template_content;

	$query = trim(urlencode($query));

	$key = array_search('token_google_images_template', $template_type); 
	$google_image_template = $template_content[$key];
	$url="https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q={$query}&rsz=8&key=ABQIAAAA6eFk02YwaBO8IzdEsPJVVBTRERdeAiwZ9EeJWta3L_JZVS0bOBQK8V5tFJq47Iy5scmmctbaSlGn9A";
	
	$result = quick_curl($url,1);
	$result = json_decode($result,true);
	$imgresults = $result['responseData']['results'];
	
	//echo $url;
	//echo "<br>";
	foreach ($imgresults as $key=>$val)
	{
		//echo "<img src=\"{$imgresults[$key]['url']}\"><br>";
		$results[$key]['urls'] = $imgresults[$key]['url'];
		$results[$key]['titles'] = addslashes(strip_tags($imgresults[$key]['title']));
	}
	
	$keys = array_keys($results);
	if ($sort=='random')
	{
		shuffle($keys);
	}
	//print_r($keys);exit;
	if (count($keys)>0)
	{
		if (count($keys)>$limit)
		{
			$keys = array_slice($keys, 0, $limit);
		}
		
		foreach ($keys as $k=>$v)
		{
			//echo "<hr>";
			$result_template = $google_image_template;
			$result_template = str_replace('%imgtitle%', $results[$v]['titles'], $result_template);
			$result_template = str_replace('%imgsrc%', $results[$v]['urls'], $result_template);
			$result_template = str_replace('%count%', $k, $result_template);
			$result_template = str_replace('%maxwidth%', $max_width, $result_template);
			$result_template = str_replace('%maxheight%', $max_height, $result_template);
			$final_results[] = $result_template;
		}
	}
	//print_r($results);exit;
	$final_result = "";
	foreach ($final_results as $key=>$val)
	{
		$final_result .= "$val <br><br>";
	}
	
	return $final_result;

}
//superpost youtube search
function search_youtube($title,$limit,$store_images_relative_path,$store_images_full_url, $nature,$exclude_keywords)
{
	global $template_type;
	global $template_content;
	
	$key = array_search('default_video_post_template', $template_type); 
	$youtube_template = $template_content[$key];
	
	$exclude_keywords = explode(',',$exclude_keywords);
	
	if (trim($title))
	{
		//echo 1; exit;
		$j=0;
		if ($nature=='title')
		{
			$keywords = remove_trash_words($title);
			$keywords = urlencode($keywords);
		}
		else
		{
			$keywords = urlencode($title);
		}
		//echo $keywords; exit;
		$source_feed = "http://gdata.youtube.com/feeds/base/videos?q=$keywords&key=AI39si6RmbtB6goYpu0MrGKmEeEhg5dIOSdZUClTencT6F_Saf3Wjqp9y55xoJ1PAa_htlx3ArxozpuNiG-jdWzNxMAV-NhvKw";	
		//echo $source_feed."<br><br>";exit;
		$string = quick_curl($source_feed,0);
		$string = htmlspecialchars_decode($string);
							
		$remove = get_string_between($string, "<feed xmlns=", "</generator>");
		$string = str_replace ($remove, "" , $string);
		//echo $string; exit;

		$title_start =  "<title type='text'>";
		$title_end =  "</title>";
		$description_start =  "<content type='html'>";
		$description_end =  "From:";
		$link_start = "<link rel='alternate' type='text/html' href='";
		$link_end = "'/>";

		$link_count = substr_count($string, $link_start);
		
		if ($link_count==0)
		{
			$keywords = explode('+',$keywords);
			$i=0;
			while ($link_count==0)
			{
				$v = $keywords[$i];
				$source_feed = "http://gdata.youtube.com/feeds/base/videos?q=".$v."&key=AI39si6RmbtB6goYpu0MrGKmEeEhg5dIOSdZUClTencT6F_Saf3Wjqp9y55xoJ1PAa_htlx3ArxozpuNiG-jdWzNxMAV-NhvKw";	
				//echo $source_feed."<br><br>";
				$string = quick_curl($source_feed,0);
				$string = htmlspecialchars_decode($string);	
				
				$remove = get_string_between($string, "<feed xmlns=", "</generator>");
				$string = str_replace ($remove, "" , $string);
				$string = $string;
				$link_count = substr_count($string, $link_start);
				$i++;
			}
			
		}
		
		
		
		if ($link_count<$limit)
		{
			$limit = $link_count;
		}

		for ($i=0;$i<$link_count;$i++)
		{
			
			$links[$i] = get_string_between($string, $link_start, $link_end);
			//echo $links[$i]; exit;			  
			$string = str_replace("".$link_start."".$links[$i]."".$link_end."", "", $string);
			$links[$i] = clean_cdata($links[$i]);
		}
		
		
		
		if (count($links)>0)
		{				
			foreach ($links as $k=>$v)
			{
				$title = "";
				$description = "";
				$link = "";
				
				//pull the title from rss
				$title = get_string_between($string, $title_start, $title_end);
				$string = str_replace("$title_start$title$title_end", "", $string);
				//echo $title; exit;
				
				//pull the description from rss						   
				$description = get_string_between($string, $description_start, $description_end);			   
				$string = str_replace("$description_start$description$description_end", "" , $string); 
				//echo $description; exit;
				
				//echo $string;exit;
				$title = clean_cdata($title);
				$description = clean_cdata($description);
				$link = $v;			
				
				$start = 'src="';		
				$end = '"';
				$thumbnail = get_string_between($description, $start, $end);
				$title = "thmb ".$title;

				$lite_description = strip_tags($description);
				$lite_description = str_replace($title,'',$lite_description);

				//get video id
				$flag = "http://www.youtube.com/watch?v=";
				$pos_start = strpos($link, $flag) + strlen($flag);
				$vid_id = substr($link, $pos_start, 11);
				
				$object = prepare_youtube($link, $description);
				$go=0;
				if (strlen($exclude_keywords[0]>2))
				{
					foreach($exclude_keywords as $a=>$b)
					{
						if (!stristr($title,$b))
						{
							$go=1;
						}
					}
				}
				else
				{
					$go=1;
				}
				
				if ($go==1)
				{
					$yt_template = $youtube_template;
					$yt_template = str_replace('%video_embed%', $object, $yt_template);
					$yt_template = str_replace('%video_thumbnail%', $thumbnail, $yt_template);
					$yt_template = str_replace('%video_description%', $lite_description, $yt_template);
				}

				$descriptions[] = $yt_template;
				
				$j++;
			}
			array_slice($descriptions, 0, 5);
			shuffle($descriptions);
		}
		
		$description = "<br><br><center>";
		for ($i=0;$i<$limit;$i++)
		{
			$description .= $descriptions[$i]."<br><br>";
		}
		$description = $description."</center>";
	}
	return $description;

}

//superpost youtube search
function search_amazon($mode,$keywords,$limit,$domain)
{
	global $store_images_relative_path;
	global $store_images_full_url;
	global $table_prefix;
	global $template_type;
	global $template_content;
	
	$key = array_search('token_amazon_widget_template', $template_type); 
	$amazon_widget_template = $template_content[$key];
	
	$key = array_search('token_amazon_review_template', $template_type); 
	$amazon_review_template = $template_content[$key];
	
	$blogsense_url = blogsense_url();
	$step = relative_path_prefix();
	
	$query = "SELECT `option_name`, `option_value` FROM ".$table_prefix."blogsense WHERE `option_name`='blogsense_amazon_affiliate_id' ";
	$result = mysql_query($query);
	if (!$result){echo $query; echo mysql_error(); exit;}
	$arr = mysql_fetch_array($result);
	$affiliate_id = $arr['option_value'];

	$j=0;
	$keywords = urlencode($keywords);
	
	
	if ($mode=='widget')
	{

		$amazon_items = array();
		$keywords = explode('+', $keywords);
		foreach ($keywords as $key=>$value)
		{
			if (count($amazon_items)==$limit)
			{

			}
			else
			{
				$b = $key+1;
				$nuvalue = "$keywords[$key]%20$keywords[$b]";
				$source_feed = "http://www.amazon.{$domain}/rss/tag/$nuvalue/new?length=10&tag=$affiliate_id";
				//echo $source_feed;
				$string = quick_curl($source_feed,0);	
				//echo $string;
				//echo "<hr>";
				$item_start = "<item>";			
				$item_count = substr_count($string, $item_start);		
				
				for ($i=0;$i<$item_count;$i++)
				{
					if (count($amazon_items)==$limit)
					{
					}
					else
					{
						//echo $string; exit;
						$item = get_string_between($string, "<item>", "</item>");
						$string = str_replace("<item>$item</item>", "", $string);
						$title = get_string_between($item,"<title>", "</title>");
						$title = explode ('newly tagged', $title);
						$title = $title[0];
						$title = substr($title,0,100);
						$title = $title."...";
						$link = get_string_between($item,"<link>", "</link>");
						$description = get_string_between($item,"<description>", "</description>");
						$image = get_string_between($description,"src=\"", "\"");
						$image = save_image($image,$title,$i);
						
						//if (!$images[$i]){echo $items[$i]; exit;}
						//echo $items[$i]."<hr>";
						$result_template = $amazon_widget_template; 
						$result_template = str_replace('%title%', $title, $result_template);
						$result_template = str_replace('%description%', $description, $result_template);
						$result_template = str_replace('%link%', $link, $result_template);
						$result_template = str_replace('%image_link%', $image, $result_template);
						$result_template = str_replace('%keywords%', $nuvalue, $result_template);						
				
						$amazon_items[] = $result_template;
					}
				}
			}
			$j++;
		}
		
		if (count($amazon_items)>0)
		{			
			
			$description = "<br><div style=\"width: 100%; display:inline-block;\" class=\"amn_items_div\" align=center>";
			foreach ($amazon_items as $k=>$v)
			{
				$description .= $v;
			}
			$description .="</div>";
		}
		//echo $description; exit;
	}
	else
	{
		$source_feed = "http://www.amazon.{$domain}/s/ref=nb_sb_noss?url=search-alias%3Daps&field-keywords=".$keywords."&x=8&y=22&fsc=-1&sort=-price";
		$string = quick_curl($source_feed,0);	
		
		$items_start = 'class="number">';
		if (strstr($string, 'class="number">'))
		{
			$items_start = 'class="number">';
		}
		if (strstr($string, '<strong class="sans">'))
		{
			$items_start = '<strong class="sans">';
		}			
		//echo $items_start; exit;		
		//echo $source_feed;
		//echo $string; exit;
		$item_count = substr_count($string, $items_start);
		//echo $item_count; exit;	
		
		if ($item_count>$limit)
		{
			$item_count=$limit;
		}
		//echo $string; exit;
		for ($i=0;$i<$item_count;$i++)
		{
		   $item_chunk[$i] = get_string_between($string, $items_start, "<div class=\"starsAndPrime\">");
		   $string  =str_replace_once($items_start,'',$string);
		}
		
		$item_chunk = array_filter($item_chunk);
		//echo count($item_chunk);exit;
		foreach($item_chunk as $k=>$v)
		{
			$link = get_string_between($v, '<a href="', '"');
			$link = trim($link);
			$product_id = get_string_between($link, "dp/", "/ref");
			$link = "http://www.amazon.{$domain}/dp/$product_id/?tag=$affiliate_id";
			
			$title = get_string_between($v, '<br clear="all" />', '</a>');
			if (!$title){$title = get_string_between($v, 'class="productTitle">', '</a>');}
			if (!$title){$title = get_string_between($v, '<div class="title">', '</a>');}
			$title = strip_tags($title);
			if (stristr($title, ' for '))
			{
				$title = explode(' for ', $title);
				$title = $title[0];
			}
			else
			{
				$title = substr($title,0,70);
				$title = $title."...";
			}
			
			//get review if available
			$a_string = quick_curl($link,0);
			$review =    get_string_between($a_string,"<b><span class=\"h3color tiny\">This review is from: </span>", "<div style=\"padding-top: 10px; clear: both; width: 100%;\">");
			$review = explode("</div>", $review);
			$review = $review[1];
			$review = strip_tags($review,'<br><p><span>');
			//if ($review) {echo $review; exit;}
			$review = trim($review);
			//echo $link;exit;			
							
			if ($review&&!strstr($title,'window.AmazonPopoverImages')&&$old_title!=$title)
			{
					
				$go=1;
				$old_title = $title;
			}
			else
			{
				//echo 666;
				$go=0;
			}
					
			if ($go==1)
			{
				//echo 777;
				//echo $v; exit;
				$links[$k] = $link;
				$titles[$k] = $title;				
				$blob = get_string_between($v, '" class="productTitle">', '</a>');
				$images[$k] = get_string_between($blob, 'src="', '"');
				if (!$images[$k]){ $images[$k] = get_string_between($v, 'src="', '"');}
				$images[$k] = save_image($images[$k],$title,$k);
				$reviews[$k] = $review;
				preg_match_all('(\$\d+)', $v, $price, PREG_PATTERN_ORDER);
				$price = $price[0];
				natsort($price);
				reset($price);
				$prices[$k] = current($price);
				$prices[$k] = str_replace('$','&#36;',$prices[$k]);
				
				//echo $item_chunk[$k];exit;
				$title =  $titles[$k];
				$image =  $images[$k];
				$link =  $links[$k];
				$review =  $reviews[$k]; 
				$price =  $prices[$k];
				
				if (!$titles[$k])
				{
					echo $v;
					echo "search+amazon+helper_functions.php no title";
					exit;
				}
				
				$result_template = $amazon_review_template; 
				$result_template = str_replace('%title%', $title, $result_template);
				$result_template = str_replace('%link%', $link, $result_template);
				$result_template = str_replace('%image_link%', $image, $result_template);
				$result_template = str_replace('%keywords%', $nuvalue, $result_template);		
				$result_template = str_replace('%customer_review%', $review, $result_template);	
				$result_template = str_replace('%price%', $price, $result_template);	
						
				$descriptions[] = $result_template;
				//echo $description;exit;
			}	
		}	
		//echo count($descriptions); 
		$descriptions = array_slice($descriptions, 0, $limit);
		$description = "<br>";
		foreach ($descriptions as $k=>$v)
		{
			$description .= $v."<br>";
		}
		//echo $description; exit;
	}
	
	
	return $description;
}

function search_ebay($title,$campaign_id, $limit,$width)
{
	global $template_type;
	global $template_content;
	
	$affiliate_id = $campaign_id;

	$keywords = $title;
	$keywords = remove_trash_words($title);
	$keywords = urlencode($keywords);
	
	$script = "<script language=\"JavaScript\" src=\"http://lapi.ebay.com/ws/eBayISAPI.dll?EKServer&ai=a%7B%7Dpg%27*&bdrcolor=FFCC00&cid=0&eksize=1&encode=UTF-8&endcolor=FF0000&endtime=y&fbgcolor=FFFFFF&fntcolor=000000&fs=0&hdrcolor=FFFFCC&hdrimage=1&hdrsrch=n&img=y&lnkcolor=0000FF&logo=2&num=".$limit."&numbid=y&paypal=n&popup=y&prvd=9&query=".$keywords."&r0=2&shipcost=n&sid=".$blog_url."&siteid=0&sort=MetaEndSort&sortby=price&sortdir=asc&srchdesc=y&tbgcolor=FFFFFF&tlecolor=FFCE63&tlefs=0&tlfcolor=000000&toolid=10004&track=".$affiliate_id."&width=".$width."\"></script>";
	
	return $script;
}

function search_flickr($title,$limit,$max_width,$max_height, $store_images_relative_path,$store_images_full_url)
{	
    global $template_type;
	global $template_content;
	
	$key = array_search('token_flickr_template', $template_type); 
	$flickr_template = $template_content[$key];
	
	$keywords = $title;
	$keywords = remove_trash_words($title);
	$keywords = explode(' ', $keywords);
	
	foreach ($keywords as $key=>$value)
	{
		//$b = $key+1;
		//$text = "$keywords[$key]+$keywords[$b]";
		if ($done!=1)
		{
			$url = "http://www.degraeve.com/flickr-rss/rss.php?tags=$value&tagmode=all&sort=releavance-desc&num=$limit";
			$string = quick_curl($url,0);
			//echo $url;
			//echo $string;exit;
			
			$remove = get_string_between($string,"<?xml","<item>");
			$string = str_replace($remove,'',$string);
			$count = substr_count($string, '<guid>');
			if ($count>0)
			{
				$done=1;
			}
		}		
	}
	
	if ($done==1)
	{
		$images = " ";
		for($i=0;$i<$limit;$i++)
		{
			$links[$i] = get_string_between($string,"<guid>","</guid>");
			$string = str_replace_once("<guid>","", $string); 
			$link = $links[$i];
			
			if ($link)
			{
				$result_template = $flickr_template;
				$result_template = str_replace('%title%', $title, $result_template);
				$result_template = str_replace('%link%', $link, $result_template);
				$result_template = str_replace('%count%', $i, $result_template);
				$result_template = str_replace('%maxwidth%', $max_with, $result_template);
				$result_template = str_replace('%maxheight%', $max_height, $result_template);
			
				$results[] = $result_template;
			}
		}
    }
	
	$final_result = "";
	foreach ($results as $key=>$val)
	{
		$final_result .= "$val <br><br>";
	}
	
	return $final_result;
}

function bs_get_domain($url)
{
	$url = str_replace(array('http://','www.'),'',$url);
	$url = explode( '.', $url );
	$part_1 = $url[0];
	$part_2 = $url[1];
	$part_2 = explode('/',$part_2);
	$part_2 = $part_2[0];
	$cleanURL = $part_1.".".$part_2;
	return $cleanURL;
}

function bs_xmlrpc($url,$post,$username,$password)
{
	$client = new IXR_Client($url);
	$client->debug = false;
	//$client->debug = true;
	
	$published =1;
	$res = $client->query('metaWeblog.newPost', '', $username, $password, $post, $published);
	
	//echo 99;
	if (!$res)
	{
		return $client->getResponse();
	}
	else
	{
		return 1; 
	}
		
}
?>