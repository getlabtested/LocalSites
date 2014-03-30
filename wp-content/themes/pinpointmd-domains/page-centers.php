<?php
/**
 * @package WordPress
 * @subpackage PinpointMD_Domains_Theme
 */

/*
Template Name: Centers Page
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
		
		<?php

			$i = 1;
			$lat = $options['pinpointmd_lat']; // latitude of centre of bounding circle in degrees
			$lon = $options['pinpointmd_lng']; // longitude of centre of bounding circle in degrees
			$rad = $options['pinpointmd_rad']; // radius of bounding circle in kilometers
			$R = 6371;  // earth's radius, km

			//first-cut bounding box (in degrees)
			$maxLat = $lat + rad2deg($rad/$R);
			$minLat = $lat - rad2deg($rad/$R);
			
			//compensate for degrees longitude getting smaller with increasing latitude
			$maxLon = $lon + rad2deg($rad/$R/cos(deg2rad($lat)));
			$minLon = $lon - rad2deg($rad/$R/cos(deg2rad($lat)));

			$results = $wpdb->get_results("SELECT * FROM ppmd_centers WHERE lat>$minLat AND Lat<$maxLat AND lng>$minLon AND lng<$maxLon");

			foreach ($results as $center):

		?>

				<div class="center-locations">
					<div class="center-locations-number"><?php echo $i; ?>.</div>
					<div itemscope itemtype="http://schema.org/MedicalClinic">
						<div class="center-locations-address">										
							<strong><span itemprop="name"><?php echo $center->name; ?></span></strong><br />
							<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
								<b><span itemprop="streetAddress"><?php echo $center->address1; ?></span></b><br />
								<span itemprop="addressLocality"><?php echo $center->city; ?></span>, <span itemprop="addressRegion"><?php echo $center->state; ?></span> <span itemprop="postalCode"><?php echo $center->zip; ?></span>
							</div>
						</div>
						<div class="center-locations-cta">
							<a href="<?php echo get_option('home'); ?>/order/" class="center-locations-cta-button">Order STD Tests</a>
							or call us at <strong><span itemprop="telephone"><?php if ($options['pinpointmd_phone'] != "") echo stripslashes($options['pinpointmd_phone']); ?></span></strong>
							<div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
								<meta itemprop="latitude" content="<?php echo $center->lat; ?>" />
								<meta itemprop="longitude" content="<?php echo $center->lng; ?>" />
							</div>
						</div>
					</div>
					<div class="cboth"></div>
				</div>

		<?php
		
				$gmap_locations .= "['".$center->name."', ".$center->lat.", ".$center->lng.", ".$i."],\n";
			
				$i++;
				
			endforeach;
		
		?>
		
		<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
		
	</div>

	<div id="col-right">
		<ul>
			<li id="sidebar-questions">
				<strong><?php if ($options['pinpointmd_phone'] != "") echo stripslashes($options['pinpointmd_phone']); ?></strong>
				<div>Questions? Call and speak to a STD Counselor in <?php bloginfo('name'); ?>!</div>
			</li>
			<li>
			
				<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
				<script type="text/javascript">
				
					<?php
						echo "var centers = [\n";
						echo $gmap_locations;
						echo "];\n";					
					?>

					function initialize() {
						var center = centers[0];
						var myOptions = {
							mapTypeControl: false,
							//zoom: 11,
							zoom: 10,
							center: new google.maps.LatLng(center[1], center[2]),
							mapTypeId: google.maps.MapTypeId.ROADMAP
						}
						var map = new google.maps.Map(document.getElementById("center-locations-gmap"), myOptions);
						setMarkers(map, centers);
					}

					function setMarkers(map, centers) {
						for (var i = 0; i < centers.length; i++) {
							var center = centers[i];
							var myLatLng = new google.maps.LatLng(center[1], center[2]);
							var marker = new google.maps.Marker({
								position: myLatLng,
								map: map,
								title: center[0],
								zIndex: center[3]
							});
						}
					}

					window.onload = initialize;
				  
				</script>
				<div id="center-locations-gmap"></div>	
			
			</li>
		</ul>
	</div>

<?php get_footer(); ?>
