<?php
/**
 * Common utility functions for LoveTravel Child Theme
 *
 * @package LoveTravel_Child
 * @subpackage Utilities
 * @since LoveTravel Child 1.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get theme file path with fallback
 *
 * @since LoveTravel Child 1.0
 *
 * @param string $file File path relative to theme directory
 * @return string Full file path
 */
function lovetravel_child_get_file_path( $file ) {
	return LOVETRAVEL_CHILD_DIR . '/' . ltrim( $file, '/' );
}

/**
 * Get theme file URI with fallback
 *
 * @since LoveTravel Child 1.0
 *
 * @param string $file File path relative to theme directory
 * @return string Full file URI
 */
function lovetravel_child_get_file_uri( $file ) {
	return LOVETRAVEL_CHILD_URI . '/' . ltrim( $file, '/' );
}

/**
 * Check if we're in admin area
 *
 * @since LoveTravel Child 1.0
 *
 * @return bool True if in admin area
 */
function lovetravel_child_is_admin() {
	return is_admin() && ! wp_doing_ajax();
}

/**
 * Load text domain for translations
 * ✅ FIXED: Textdomain loading handled in theme-setup.php to prevent duplicate loading
 *
 * @since LoveTravel Child 1.0
 *
 * @return void
 */
// Textdomain loading moved to theme-setup.php to prevent conflicts