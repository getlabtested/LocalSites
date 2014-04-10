<?php
/**
 * @package WordPress
 * @subpackage PinpointMD_Domains_Theme
 */
 
/*
Template Name: Order Page
*/

get_header(); ?>

	<div id="col-wide">

		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
			</div>
		</div>
		<?php endwhile; endif; ?>
		<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
		
	</div>

<?php get_footer(); ?>
