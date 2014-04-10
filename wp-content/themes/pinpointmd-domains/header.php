<?php
/**
 * @package WordPress
 * @subpackage PinpointMD_Domains_Theme
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?> STD Testing</title>

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" />
<link rel="icon" href="<?php bloginfo('template_url'); ?>/images/favicon.ico" type="image/gif" />
<meta name="robots" content="all, noodp, noydir" />

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#nav li a').prepend('<span><?php bloginfo('name'); ?><br />STD Testing<br /></span>');
	});
</script>

<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>
</head>


<?php
	//get theme options
	$options = get_option('pinpointmd_theme_options');
?>

<body <?php body_class(); ?>>


<div id="schema" itemscope itemtype="http://schema.org/MedicalOrganization">
	<span itemprop="name"><?php bloginfo('name'); ?> STD Testing</span><br />
	<span itemprop="description">Private, Same-Day STD Testing in <?php bloginfo('name'); ?></span><br />
	<span itemprop="url"><?php echo get_option('home'); ?></span><br />
	<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
		<span itemprop="addressLocality"><?php bloginfo('name'); ?></span><br />
	</div>
	<div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
		<meta itemprop="latitude" content="<?php if ($options['pinpointmd_lat'] != "") echo stripslashes($options['pinpointmd_lat']); ?>" />
		<meta itemprop="longitude" content="<?php if ($options['pinpointmd_lng'] != "") echo stripslashes($options['pinpointmd_lng']); ?>" />
	</div>
	<span itemprop="telephone"><?php if ($options['pinpointmd_phone'] != "") echo stripslashes($options['pinpointmd_phone']); ?></span>
</div>

<div id="header">
	<div id="logo">
		<a href="<?php echo get_option('home'); ?>/" title="Home | <?php bloginfo('name'); ?> STD Testing">
			<div>Private, Same-Day</div>
			<?php bloginfo('name'); ?><br />
			STD Testing
		</a>
	</div>
	<div id="social">
		<div id="social-left"></div>

		<div id="fb">
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			 var js, fjs = d.getElementsByTagName(s)[0];
			 if (d.getElementById(id)) {return;}
			 js = d.createElement(s); js.id = id;
			 js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
			 fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			<div class="fb-like" data-href="<?php echo get_option('home'); ?>" data-send="false" data-layout="button_count" data-width="45" data-show-faces="false" data-action="recommend"></div>
		</div>

		<div id="google">
			<!-- Place this tag where you want the +1 button to render -->
			<div class="g-plusone" data-href="<?php echo get_option('home'); ?>"></div>
			<!-- Place this render call where appropriate -->
			<script type="text/javascript">
			 (function() {
			   var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
			   po.src = 'https://apis.google.com/js/plusone.js';
			   var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
			 })();
			</script>
		</div>

		<div id="twitter">
			<a href="https://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="getstdtested">Tweet</a>
			<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
		</div>
		
		CALL US TODAY: <strong><?php if ($options['pinpointmd_phone'] != "") echo stripslashes($options['pinpointmd_phone']); ?></strong>
	</div>
	<div id="nav">
		<a href="<?php echo get_option('home'); ?>/order/" id="nav-order" title="Order STD Test">ORDER</a>
		<?php wp_nav_menu(); ?>
	</div>
	<div class="cboth"></div>
</div>

<div id="wrap">
	<div id="wrap-top"></div>
