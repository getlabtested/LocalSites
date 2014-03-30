<?php

add_action( 'admin_init', 'pinpointmd_theme_options_init' );
add_action( 'admin_menu', 'pinpointmd_theme_options_add_page' );

/**
 * Init plugin options to white list our options
*/
function pinpointmd_theme_options_init(){
	register_setting( 'pinpointmd_options', 'pinpointmd_theme_options', 'pinpointmd_theme_options_validate' );
}

/**
 * Load up the menu page
*/
function pinpointmd_theme_options_add_page() {
	add_theme_page( 'PinpointMD Theme Options', 'PinpointMD Theme Options', 'edit_theme_options', 'pinpointmd_theme_options', 'pinpointmd_theme_options_do_page' );
}

/**
 * Create the options page
*/
function pinpointmd_theme_options_do_page() {
	global $greenleaf_radio_options;

	if ( ! isset( $_REQUEST['settings-updated'] ) )
		$_REQUEST['settings-updated'] = false;
	?>
	<div class="wrap">
		<?php screen_icon(); echo "<h2>PinpointMD Theme Options</h2>"; ?>

		<?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
			<div class="updated fade"><p><strong>Options saved.</strong></p></div>
		<?php endif; ?>

		<form method="post" action="options.php" style="width: 65%;">
			<?php settings_fields( 'pinpointmd_options' ); ?>
			<?php $options = get_option( 'pinpointmd_theme_options' ); ?>

			<table class="form-table">
				<tr valign="top">
					<td colspan="2">
						<h2>Settings</h2>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Phone #:</th>
					<td>
						<input id="pinpointmd_theme_options[pinpointmd_phone]" class="regular-text" type="text" name="pinpointmd_theme_options[pinpointmd_phone]" value="<?php esc_attr_e(stripslashes($options['pinpointmd_phone'])); ?>" />
					</td>
				</tr>
				<tr valign="top"><th scope="row">Individual Test Price:</th>
					<td>
						<input id="pinpointmd_theme_options[pinpointmd_test_price]" class="regular-text" type="text" name="pinpointmd_theme_options[pinpointmd_test_price]" value="<?php esc_attr_e(stripslashes($options['pinpointmd_test_price'])); ?>" />
					</td>
				</tr>
				<tr valign="top"><th scope="row">Ulitmate Package Price:</th>
					<td>
						<input id="pinpointmd_theme_options[pinpointmd_ultimate_price]" class="regular-text" type="text" name="pinpointmd_theme_options[pinpointmd_ultimate_price]" value="<?php esc_attr_e(stripslashes($options['pinpointmd_ultimate_price'])); ?>" />
					</td>
				</tr>
				<tr valign="top"><th scope="row">At-Home Package Price:</th>
					<td>
						<input id="pinpointmd_theme_options[pinpointmd_at_home_price]" class="regular-text" type="text" name="pinpointmd_theme_options[pinpointmd_at_home_price]" value="<?php esc_attr_e(stripslashes($options['pinpointmd_at_home_price'])); ?>" />
					</td>
				</tr>
			</table>

			<table class="form-table">
				<tr valign="top">
					<td colspan="2"><br />
						<h2>Testing Center Locations</h2>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Latitude:</th>
					<td>
						<input id="pinpointmd_theme_options[pinpointmd_lat]" class="regular-text" type="text" name="pinpointmd_theme_options[pinpointmd_lat]" value="<?php esc_attr_e(stripslashes($options['pinpointmd_lat'])); ?>" /><br />
					</td>
				</tr>
				<tr valign="top"><th scope="row">Longitude:</th>
					<td>
						<input id="pinpointmd_theme_options[pinpointmd_lng]" class="regular-text" type="text" name="pinpointmd_theme_options[pinpointmd_lng]" value="<?php esc_attr_e(stripslashes($options['pinpointmd_lng'])); ?>" /><br />
					</td>
				</tr>
				<tr valign="top"><th scope="row">Radius (in kilometers):</th>
					<td>
						<input id="pinpointmd_theme_options[pinpointmd_rad]" class="regular-text" type="text" name="pinpointmd_theme_options[pinpointmd_rad]" value="<?php esc_attr_e(stripslashes($options['pinpointmd_rad'])); ?>" /><br />
					</td>
				</tr>
			</table>
			
			<table class="form-table">
				<tr valign="top">
					<td colspan="2"><br />
						<h2>Google Analytics</h2>
						<p>Google Analytics code will NOT be included if Google Analytics ID field left empty.</p>
					</td>
				</tr>
				<tr valign="top"><th scope="row">Google Analytics ID:</th>
					<td>
						<input id="pinpointmd_theme_options[pinpointmd_ga_code]" class="regular-text" type="text" name="pinpointmd_theme_options[pinpointmd_ga_code]" value="<?php esc_attr_e(stripslashes($options['pinpointmd_ga_code'])); ?>" /><br />
						<label class="description" for="pinpointmd_theme_options[ga_code]">Copy and paste your Google Analytics account ID (UA-XXXXXXXX-X) here.</label>
					</td>
				</tr>		
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="Save Options" />
			</p>
		</form>
		
	</div>
	
<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
*/
function pinpointmd_theme_options_validate( $input ) {
	// Say our textarea option must be safe text with the allowed tags for posts
	$input['pinpointmd_state'] = wp_filter_post_kses( $input['pinpointmd_state'] );
	$input['pinpointmd_phone'] = wp_filter_post_kses( $input['pinpointmd_phone'] );
	$input['pinpointmd_test_price'] = wp_filter_post_kses( $input['pinpointmd_test_price'] );
	$input['pinpointmd_ga_code'] = wp_filter_post_kses( $input['pinpointmd_ga_code'] );

	return $input;
}

?>