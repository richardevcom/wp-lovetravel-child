<?php
/**
 * Favicon Manager
 *
 * Handles favicon output with Customizer integration.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 * @since      2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Favicon Manager Class
 *
 * Outputs comprehensive favicon tags when WordPress Customizer site icon is not set.
 */
class LoveTravelChildFavicon {

	/**
	 * Output favicon links.
	 *
	 * Only outputs if Customizer site icon is not set.
	 *
	 * @since 2.0.0
	 */
	public function outputFavicon() {
		lovetravelChildOutputFavicon();
	}

	/**
	 * Output theme color meta tags.
	 *
	 * Always outputs theme branding colors.
	 *
	 * @since 2.0.0
	 */
	public function outputThemeColor() {
		lovetravelChildOutputThemeColor();
	}
}
