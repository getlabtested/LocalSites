<?php
/**
 * @package WordPress
 * @subpackage PinpointMD_Domains_Theme
 */

/*
Template Name: Process Page
*/

get_header(); ?>
<?php
	//get theme options
	$options = get_option('pinpointmd_theme_options');
?>

	<div id="col-left">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

			</div>
		</div>
		<?php endwhile; endif; ?>
		<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
		
	</div>	
	
	<div id="col-right">
		<ul>
			<li id="sidebar-questions">
				<strong><?php if ($options['pinpointmd_phone'] != "") echo stripslashes($options['pinpointmd_phone']); ?></strong>
				<div>Questions? Call and speak to a STD Counselor in <?php bloginfo('name'); ?>!</div>
			</li>
		</ul>
	</div>

<?php get_footer(); ?>
