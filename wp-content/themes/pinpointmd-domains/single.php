<?php
/**
 * @package WordPress
 * @subpackage PinpointMD_Domains_Theme
 */

if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
	wpcf7_enqueue_scripts();
	wpcf7_enqueue_styles();
}

get_header();
?>

	<div id="col-left">

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
			<h1><?php the_title(); ?></h1>
			<p><small class="post-date">Posted by <?php the_author() ?> on <?php the_time('F jS, Y') ?></small></p>

			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

				<p class="postmetadata">
					<?php the_tags( '<small>This post was tagged as: ', ', ', '</small>'); ?>
				</p>
			</div>
		</div>

	<?php //comments_template(); ?>

	<?php endwhile; else: ?>

		<p>Sorry, no posts matched your criteria.</p>

<?php endif; ?>

	</div>

<?php get_sidebar('blog'); ?>

<?php get_footer(); ?>
