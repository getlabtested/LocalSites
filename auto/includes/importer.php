<?php
ini_set("memory_limit","80M");
//echo $table_prefix;
//echo 7; exit;
//include_once('../wp-config.php');
$nuprefix = explode('_',$table_prefix);
$nuprefix= $nuprefix[0]."_";
require_once 'utils.php';

$maxPhraseLength = 64;

$tSTokens = $nuprefix . 'stokens';
$tBodyPhrases = $nuprefix . 'body_phrases';
$tReplacements = $nuprefix . 'replacements';

$blogsense_url = spin_this_url();
//echo $blogsense_url; exit;

bs_query( "drop table if exists `$tBodyPhrases`" );

bs_query( "create table `$tBodyPhrases`
(
    `phrase` varchar($maxPhraseLength) not null,
    `pos` int unsigned not null,
    `len` int unsigned not null
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;" );

bs_query( "drop table if exists `$tReplacements`" );

bs_query( "create table `$tReplacements`
(
    `pos` int unsigned not null,
    `len` int unsigned not null,
    `phrase` varchar($maxPhraseLength) not null
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;" );

bs_query( "DROP TABLE IF EXISTS `$tSTokens`" );

bs_query( "CREATE TABLE `$tSTokens` (
  `id` int unsigned NOT NULL,
  `phrase` varchar($maxPhraseLength) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE utf8_general_ci" );

// fetch known phrases from the file
if (!function_exists('im_quick_curl'))
{
	function im_quick_curl($link)
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
}

$rows = array_map( "trim", file( $blogsense_url."/includes/importer.dat" ) );


// process them in batches so that mysql doesn't choke on a huge query
spin_batch_insert( "INSERT INTO `$tSTokens` (`id`, `phrase`) VALUES ", $rows );

bs_query( "create index `id` on `$tSTokens` (`id`)" );
bs_query( "create index `phrase` on `$tSTokens` (`phrase`(32))" );

?>
