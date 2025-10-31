<?php
/**
 * LoveTravel Child Theme
 *
 * Clean OOP bootstrap for child theme functionality.
 * Architecture based on WordPress Plugin Boilerplate pattern.
 *
 * @package LoveTravelChild
 * @version 2.2.0
 * @author  richardevcom
 * @link    https://github.com/richardevcom
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme constants
 */
define( 'LOVETRAVEL_CHILD_VERSION', '2.8.0' );
define( 'LOVETRAVEL_CHILD_PATH', get_stylesheet_directory() );
define( 'LOVETRAVEL_CHILD_URI', get_stylesheet_directory_uri() );

/**
 * Load core theme class
 */
require_once LOVETRAVEL_CHILD_PATH . '/includes/class-lovetravel-child.php';

/**
 * Initialize and run the theme
 *
 * @since 2.0.0
 */
function lovetravelChildRun() {
	$theme = new LoveTravelChild();
	$theme->run();
}
lovetravelChildRun();
