<?php
/**
 * Helper Functions
 *
 * Shared utility functions used across admin and public contexts.
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
 * Get theme version.
 *
 * @since  2.0.0
 * @return string Theme version.
 */
function lovetravelChildGetVersion() {
	return defined( 'LOVETRAVEL_CHILD_VERSION' ) ? LOVETRAVEL_CHILD_VERSION : '2.0.0';
}

/**
 * Check if in admin context.
 *
 * @since  2.0.0
 * @return bool True if admin.
 */
function lovetravelChildIsAdmin() {
	return is_admin() && ! wp_doing_ajax();
}

/**
 * Check if in public context.
 *
 * @since  2.0.0
 * @return bool True if public.
 */
function lovetravelChildIsPublic() {
	return ! is_admin() || wp_doing_ajax();
}
