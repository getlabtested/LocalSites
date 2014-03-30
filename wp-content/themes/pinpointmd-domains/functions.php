<?php
/**
 * @package WordPress
 * @subpackage PinpointMD_Domains_Theme
 */

// enable Theme Options page
require_once ( get_stylesheet_directory() . '/theme-options.php' );


// enable feed links
add_theme_support('automatic-feed-links');


// enable dynamic sidebar
if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name' => 'Main Sidebar',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2>',
	));
	register_sidebar(array(
		'name' => 'Blog Sidebar',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2>',
	));
}


// enable shortcodes
function ppmdPhoneShortcode() {
	$options = get_option('pinpointmd_theme_options');
	$output = stripslashes($options['pinpointmd_phone']);
	return $output;
}
add_shortcode('ppmd-phone', 'ppmdPhoneShortcode');

function ppmdTestPriceShortcode() {
	$options = get_option('pinpointmd_theme_options');
	$output = stripslashes($options['pinpointmd_test_price']);
	return $output;
}
add_shortcode('ppmd-test-price', 'ppmdTestPriceShortcode');

function ppmdUltimatePriceShortcode() {
	$options = get_option('pinpointmd_theme_options');
	$output = stripslashes($options['pinpointmd_ultimate_price']);
	return $output;
}
add_shortcode('ppmd-ultimate-price', 'ppmdUltimatePriceShortcode');

function ppmdAtHomePriceShortcode() {
	$options = get_option('pinpointmd_theme_options');
	$output = stripslashes($options['pinpointmd_at_home_price']);
	return $output;
}
add_shortcode('ppmd-at-home-price', 'ppmdAtHomePriceShortcode');

function ppmdNameURL() {
	$output = strtoupper(str_replace("http://www.", "", get_bloginfo('home')));
	return $output;
}
add_shortcode('ppmd-name-url', 'ppmdNameURL');


// remove WordPress version from generator tag
function generic_generator_tag($generator) {
       return str_replace(' '.get_bloginfo('version'), '', $generator);
}
add_filter('the_generator', 'generic_generator_tag');


// page pagination
function PinpointMD_Domains_pagenavi($before = '', $after = '', $prelabel = '', $nxtlabel = '', $pages_to_show = 5, $always_show = false) {
	global $wpdb, $wp_query;
	$request = $wp_query->request;
	$posts_per_page = intval(get_query_var('posts_per_page'));
	$paged = intval(get_query_var('paged'));
	
	if(empty($prelabel)) {
		$prelabel  = '<strong>&laquo;</strong>';
	}
	if(empty($nxtlabel)) {
		$nxtlabel = '<strong>&raquo;</strong>';
	}
	$half_pages_to_show = round($pages_to_show/2);
	if (!is_single()) {
		if(!is_category()) {
			preg_match('#FROM\s(.*)\sORDER BY#siU', $request, $matches);		
		} else {
			preg_match('#FROM\s(.*)\sGROUP BY#siU', $request, $matches);		
		}
		$fromwhere = $matches[1];
		$numposts = $wp_query->found_posts;
		$max_page = $wp_query->max_num_pages;
		
		if(empty($paged) || $paged == 0) {
			$paged = 1;
		}
		if($max_page > 1 || $always_show) {
			echo "$before <div class='nav'>";
			if ($paged >= ($pages_to_show-1)) {
				echo '<a href="'.get_pagenum_link().'">&laquo; First</a>';
			}
			previous_posts_link($prelabel);
			for($i = $paged - $half_pages_to_show; $i  <= $paged + $half_pages_to_show; $i++) {
				if ($i >= 1 && $i <= $max_page) {
					if($i == $paged) {
						echo "<strong class='on'>$i</strong>";
					} else {
						echo ' <a href="'.get_pagenum_link($i).'">'.$i.'</a> ';
					}
				}
			}
			next_posts_link($nxtlabel, $max_page);
			if (($paged+$half_pages_to_show) < ($max_page)) {
				echo '<a href="'.get_pagenum_link($max_page).'">Last &raquo;</a>';
			}
			echo "</div> $after";
		}
	}
}


?>
