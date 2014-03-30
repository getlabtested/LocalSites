<?php
/*
Plugin Name: BlogSense Connect
Version: v0.8
Description: Adds link to BlogSense Admin as well as ties BlogSense's internal cron system into Wordpress's pseudocronsystem to avoid setting up an external cronjobs. For multi-site installs this plugin will need to be active for every sub-blog. BlogSense must be installed and activated for this plugin to have any affect.
Author: Hudson Atwell
*/
//creadits also go out to automatic @ http://automattic.com/ for his unfilter wordpress MU code which affects the youtube module in multipress blogs.

add_action('admin_menu', 'wp_blogsense_menu');
add_filter('cron_schedules', 'add_per_min');

define('wp_blogsense_BUTTON_TEXT', 'BlogSense');
define('wp_blogsense_ICON_URL', '../wp-content/plugins/blogsense-connect/icon.png');

function wp_blogsense_menu() {

	add_menu_page('', wp_blogsense_BUTTON_TEXT, 5, __FILE__, '', wp_blogsense_ICON_URL, 1);
	global $menu;
	global $wpdb;

	$escape = $wpdb->real_escape;
	$wpdb->real_escape = true;
	$row = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'blogsense WHERE option_name="blogsense_blogsense_url"');
	$menu[1][2] = $row->option_value;
	$wpdb->real_escape = $escape;
}

function add_per_min($schedules) {
 $schedules['perminute'] = array('interval' => 60, 'display' => 'Every Minute');
 return $schedules;
}

function call_cron_url() {
	global $wpdb;

	$escape = $wpdb->real_escape;
	$wpdb->real_escape = true;
	$row = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'blogsense WHERE option_name="blogsense_cron_script_heartbeat"');
	$wpdb->real_escape = $escape;

	$parsed = parse_url($row->value);
	$url = $row->option_value;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, false);	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_NOBODY, false);
	$data = curl_exec($ch);

	//mail('hudson.atwell@gmail.com','bobo', $url."<br>".$data);
}

function bs_delete_bookmark($postid)
{
	global $table_prefix;
	//echo 1;exit;
	$query = "DELETE FROM {$table_prefix}posts_to_bookmark WHERE post_id = '{$postid}'";
	$result = mysql_query($query);
}

function bs_update_bookmark($postid)
{
	global $table_prefix;
	//echo 1;exit;
	$query = "UPDATE {$table_prefix}posts_to_bookmark SET status='0' WHERE post_id = '{$postid}' AND status='3'";
	$result = mysql_query($query);
}

add_action('trash_post', 'bs_delete_bookmark');
add_action('pending_to_publsih', 'bs_update_bookmark');
add_action('draft_to_publsih', 'bs_update_bookmark');

if (!wp_next_scheduled('wp_blogsense_heartbeat')) {
	wp_schedule_event(time(), 'perminute', 'wp_blogsense_heartbeat' );
}

add_action('wp_blogsense_heartbeat', 'call_cron_url');

function disable_kses_content() {
remove_filter('content_save_pre', 'wp_filter_post_kses');
}
add_action('init','disable_kses_content',20);


