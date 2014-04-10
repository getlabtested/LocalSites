<?php
/**
 * @package WordPress
 * @subpackage PinpointMD_Domains_Theme
 */

get_header();
?>

	<div id="col-left">

		<h1>Error 404 - Page Not Found</h1>
		<p>Apologies, but the page you requested could not be found. Perhaps sitemap will help.</p>
		
		<?php echo ddsg_create_sitemap(); ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>