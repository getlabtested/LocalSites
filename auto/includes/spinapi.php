<?php
include_once('../../wp-config.php');
require_once 'utils.php';
$nuprefix = explode('_',$table_prefix);
$nuprefix= $nuprefix[0]."_";

/*
********************************************************************************************
*
*   Setup.
*
********************************************************************************************
*/

set_time_limit(500);

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


/*
********************************************************************************************
*
*   Helpers.
*
********************************************************************************************
*/

function generate_workload_from( $phrases )
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
function generate_all_possible_replacements()
{
    global $tReplacements, $tBodyPhrases, $tSTokens;

    bs_query( "TRUNCATE TABLE $tReplacements" );

	// define DONT_RANDOMIZE_REPLACEMENTS if you don't want the script to pick
    // a random replacement from the list of available phrases. this is handy
    // for testing/debugging
    $orderClause = defined( 'DONT_RANDOMIZE_REPLACEMENTS' )
        ? 'p2.phrase desc'
        : 'rand()';

    query
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

//------------------------------------------------------------------------------------------
function fetch_the_best_replacements()
{
    global $tReplacements;

    $q = query
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
        $replacements[] = $row;
    }

    return $replacements;
}

//------------------------------------------------------------------------------------------
function phrase_starts_with_capital_case( $phrase )
{
    return preg_match( '@^\p{Lu}\p{Ll}@', $phrase );
}

//------------------------------------------------------------------------------------------
function phrase_is_all_capital_case( $phrase )
{
    return preg_match( '@^(\p{Lu}|\s)+$@', $phrase );
}

/*
********************************************************************************************
*
*   Main.
*
********************************************************************************************
*/

//pull content in
$text= $_POST['content'];

//prepare content
//$text = strip_tags($text, '<a><b><h1><h2><table><tr><td><p><br><img><div><font><span>');

// XXX: by romank, disabled this code because it would convert plain text to html by adding <br/>'s
// and the latter would affect replacements.
/*
if (!preg_match("/([\<])([^\>]{1,})*([\>])/i", $text)) {

    $text = nl2br($text);
} 
*/

//prepare output
$output = $text;

//remove uninportant characters
$text = preg_replace("/\p{P}(?<!')/", ' ', $text);

//convert to utf8
$text = spin_fix_the_encoding($text);

//clean up any bad effects of utf8 encoding
$text = replace_t_characters($text);

//this variable is my counter for successful finds
$mcount = 0;

//replace extra spaces.
$text = trim( preg_replace( '@\s+@', ' ', $text ) );

//create the array of ones
$words = preg_split( '@ +@', $text, -1, PREG_SPLIT_NO_EMPTY );

$phrases = spin_generate_phrases_from( $words, MIN_PHRASE_LENGTH, MAX_PHRASE_LENGTH );

// only bother if there's something to process
if( sizeof( $phrases ) )
{
    generate_workload_from( $phrases );
    generate_all_possible_replacements();

    $replacements = fetch_the_best_replacements();
    
    $lastReplacement = null;
    $lastReplacementEnd = 0;

    foreach( $replacements as $replacement )
    {
        // skip less optimal replacements for already replaced phrases

        if( $lastReplacement && $replacement->pos < $lastReplacement->pos + $lastReplacement->len )
        {
            continue;
        }

        $originalPhrase = implode( ' ', array_slice( $words, $replacement->pos, $replacement->len ) );

        $needlePos = strpos( $output, $originalPhrase, $lastReplacementEnd );

        if( $needlePos !== false )
        {
			$replacementText = $replacement->phrase;

            if( $replacementText && phrase_starts_with_capital_case( $originalPhrase ) )
            {
                $replacementText[ 0 ] = strtoupper( $replacementText[ 0 ] );
            }
            else if( $replacementText && phrase_is_all_capital_case( $originalPhrase ) )
            {
                $replacementText = strtoupper( $replacementText );
            }
            $output = substr_replace( $output, $replacementText, $needlePos, strlen(
                $originalPhrase ) );

            // prevent overwriting already replaced results with replacements that follow

            $lastReplacement = $replacement;
            $lastReplacementEnd = $needlePos + strlen( $replacement->phrase );
        }
    }
}

echo $output;    

?>