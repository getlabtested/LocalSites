<?php
/**
 * @package WordPress
 * @subpackage PinPointMD_Domains_Theme
 */

get_header(); ?>

	<div id="col-left">

	<?php if (have_posts()) : ?>

		<h1>Search Results</h1>

		<div class="navigation">
			<div class="alignleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="alignright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

		<?php while (have_posts()) : the_post(); ?>

			<div <?php post_class() ?>>
				<h1 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
				<p><small><?php the_time('l, F jS, Y') ?></small></p>

				<p class="postmetadata"><?php the_tags('Tags: ', ', ', '<br />'); ?> Posted in <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
			</div>

		<?php endwhile; ?>

		<div class="navigation">
			<div class="fleft"><?php next_posts_link('&laquo; Older Entries') ?></div>
			<div class="fright"><?php previous_posts_link('Newer Entries &raquo;') ?></div>
		</div>

	<?php else : ?>

		<h1 class="acenter">No posts found. Try a different search?</h1>
		<?php get_search_form(); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
