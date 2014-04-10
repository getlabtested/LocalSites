<?php
/**
 * @package WordPress
 * @subpackage PinpointMD_Domains_Theme
 */
?>

<?php
	//get theme options
	$options = get_option('pinpointmd_theme_options');
?>

	<div id="footer">
		<p class="acenter">
			<?php if ($options['pinpointmd_phone'] != "") echo "<strong>Call ".stripslashes($options['pinpointmd_phone'])."</strong><br />"; ?>
			<a href="<?php echo get_option('home'); ?>/sitemap/">Sitemap</a> | <a href="<?php echo get_option('home'); ?>/terms-of-service/">Terms of Service</a> | <a href="<?php echo get_option('home'); ?>/privacy-policy/">Privacy Policy</a><br />
			Copyright &copy; <?php echo date('Y'); ?> <a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?> STD Testing</a>. All rights reserved.
		</p>
	</div>
	
	<div class="cboth"></div>
	
</div>

<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->

<?php wp_footer(); ?>


<?php if (!is_user_logged_in() && $options['pinpointmd_ga_code'] != "") { ?>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '<?php echo stripslashes($options['pinpointmd_ga_code']); ?>']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
<?php } ?>

<!-- Google Code for GST Visitors 1 Remarketing List -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1047469408;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "666666";
var google_conversion_label = "w3GLCNrD9AEQ4Lq88wM";
var google_conversion_value = 0;
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1047469408/?label=w3GLCNrD9AEQ4Lq88wM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<!-- EOF Google Code for GST Visitors 1 Remarketing List -->

<!-- AdRoll Remarketing Code -->
<script type="text/javascript">
adroll_adv_id = "OBD5PEJZNRCB3PQUJMOZ65";
adroll_pix_id = "V6VV7AXHKZHE3JSOL2EKB5";
(function () {
var oldonload = window.onload;
window.onload = function(){
   __adroll_loaded=true;
   var scr = document.createElement("script");
   var host = (("https:" == document.location.protocol) ? "https://s.adroll.com" : "http://a.adroll.com");
   scr.setAttribute('async', 'true');
   scr.type = "text/javascript";
   scr.src = host + "/j/roundtrip.js";
   ((document.getElementsByTagName('head') || [null])[0] ||
    document.getElementsByTagName('script')[0].parentNode).appendChild(scr);
   if(oldonload){oldonload()}};
}());
</script>
<!-- EOF AdRoll Remarketing Code -->

<?php
	$trackingCode = get_post_meta($post->ID, 'TrackingCode', true); 
	if (!is_user_logged_in() && $trackingCode != "") echo $trackingCode;
?>


</body>
</html>
