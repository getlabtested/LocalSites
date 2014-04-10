<?php
/**
 * @package WordPress
 * @subpackage PinpointMD_Domains_Theme
 */

if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
	wpcf7_enqueue_scripts();
	wpcf7_enqueue_styles();
}

get_header(); ?>

	<div id="col-left">

	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<h1><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
				<p><small class="post-date">Posted by <?php the_author() ?> on <?php the_time('F jS, Y') ?></small></p>
				<div class="entry">
					<?php the_excerpt('Read the rest of this entry &raquo;'); ?>
					<p><a href="<?php the_permalink() ?>" class="b-read-more" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">Read more</a></p>
				</div>
			</div>

		<?php endwhile; ?>

		<div class="pagination">
			<?php PinpointMD_Domains_pagenavi(); ?>
		</div>

	<?php else : ?>

		<h1 class="acenter">Not Found</h1>
		<p class="acenter">Sorry, but you are looking for something that isn't here.</p>
		<?php get_search_form(); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar('blog'); ?>

<?php get_footer(); ?>
