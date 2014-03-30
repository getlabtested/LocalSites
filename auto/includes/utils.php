<?php

/*
********************************************************************************************
*
*   Utility functions.
*
********************************************************************************************
*/

function spin_this_url()
{
	$current_url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."";
	if (strstr($current_url, 'update_sql.php'))	{ 
		$current_url = explode("update_sql.php",$current_url); 
		$current_url = $current_url[0];
	}
	if (strstr($current_url, 'f_activate_blogsense.php'))	{ 
		$current_url = explode("functions/f_activate_blogsense.php",$current_url); 
		$current_url = $current_url[0];
	}	
	if (strstr($current_url, 'index.php'))	{ 
		$current_url = explode("index.php",$current_url); 
		$current_url = $current_url[0];
	}	
	if (strstr($current_url, 'update.php'))	{ 
		$current_url = explode("update.php",$current_url); 
		$current_url = $current_url[0];
	}	
	//echo $current_url; exit;
	return $current_url;
}
function spin_replace_t_characters($input)
{
   //echo $input; exit;
   //$input = str_replace('–', '',$input);
   $input = str_replace('—', '-',$input);
   $input = str_replace('&mdash;', '-',$input);   
   $input = str_replace("’", "'",$input);
   $input = str_replace('"', '"',$input);
   $input = str_replace('"', '" ',$input);
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
   $input = str_replace("?", "",$input);
  
   return $input;
}

//------------------------------------------------------------------------------------------
function spin_fix_the_encoding($in_str)
{
  $cur_encoding = mb_detect_encoding($in_str) ;
  if($cur_encoding == "UTF-8" && mb_check_encoding($in_str,"UTF-8"))
    return $in_str;
  else
    return utf8_encode($in_str);
}

//------------------------------------------------------------------------------------------
function spin_generate_phrases_from( $words, $minPhraseLength = 2, $maxPhraseLength = 5 )
{
    $result = array();


    static $ignoredPhrases = array
    (
        1 => array("of","a","for","from","does","the","too","to","a","an","my","mine","me",
                "was","you","with","by","time","in","and"),
        2 => array("in a","time to")
    );

    for( $i = 0; $i < sizeof( $words ); ++$i )
    {
        $currentPhraseLength = min( $maxPhraseLength, sizeof( $words ) - $i );

        while( $currentPhraseLength >= $minPhraseLength )
        {
            $phrase = new StdClass;

            $phrase->text = implode( ' ', array_slice( $words, $i, $currentPhraseLength ) );
            $phrase->position = $i;
            $phrase->length = $currentPhraseLength;

            // only add the phrase if it's not in the list of ignored phrases

            if( !isset( $ignoredPhrases[ $phrase->length ] ) || !in_array( strtolower( $phrase->text ),
                $ignoredPhrases[ $phrase->length ] ) )
            {    
                $result[] = $phrase;
            }

            --$currentPhraseLength;
        }
    }

    return $result;
}

//------------------------------------------------------------------------------------------
function bs_query( $query )
{
    $result = mysql_query( $query );

    if( $result )
	{	
		return $result;
	}
	else
	{
		echo $query;
		echo "<br><br>";
		echo mysql_error();
	}
}

//------------------------------------------------------------------------------------------
function spin_batch_insert( $query, $rows )
{
    while( sizeof( $rows ) )
    {
        $maxRowsInBatch = 5000;

        $batch = array_slice( $rows, 0, $maxRowsInBatch );
        $rows = array_slice( $rows, $maxRowsInBatch );

        bs_query( $query . implode ( ',', $batch ) );
    }
}


?>
