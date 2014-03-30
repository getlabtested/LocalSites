<?php
/**
 * @package WordPress
 * @subpackage PinpointMD_Domains_Theme
 */
 
/*
Template Name: Home Page
*/

get_header(); ?>

	<div id="banner">
		Confidential, same-day STD testing!
		<a href="<?php echo get_option('home'); ?>/order/" title="Get STD Tested">Get STD Tested</a>
	</div>

	<div id="col-left">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			</div>
		</div>
		<?php endwhile; endif; ?>
		<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
	
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
