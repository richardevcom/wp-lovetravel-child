<?php
/**
 * Internationalization
 *
 * Loads and defines the internationalization files for translation.
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
 * Internationalization Class
 *
 * Defines theme text domain and loads translation files.
 */
class LoveTravelChildI18n {

	/**
	 * Load the theme text domain for translation.
	 *
	 * @since 2.0.0
	 */
	public function loadThemeTextdomain() {
		load_child_theme_textdomain(
			'lovetravel-child',
			get_stylesheet_directory() . '/languages'
		);
	}
}
