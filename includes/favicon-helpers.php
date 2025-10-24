<?php
/**
 * Favicon Helper Functions
 *
 * Manages favicon output and integration with WordPress Customizer.
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
 * Output comprehensive favicon links.
 *
 * Only outputs if WordPress Site Icon (Customizer) is NOT set.
 * When Site Icon is set, WordPress handles favicon output automatically.
 *
 * @since 2.0.0
 */
function lovetravelChildOutputFavicon() {
	// Check if WordPress Site Icon is set in Customizer
	if ( has_site_icon() ) {
		// WordPress will handle favicon output via wp_site_icon()
		return;
	}

	// Output comprehensive favicon links (default theme favicons)
	$favicon_path = get_stylesheet_directory_uri() . '/assets/favicon';
	?>
	<!-- Favicon (LoveTravel Child Theme defaults) -->
	<link rel="icon" type="image/png" href="<?php echo esc_url( $favicon_path . '/favicon-96x96.png' ); ?>" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="<?php echo esc_url( $favicon_path . '/favicon.svg' ); ?>" />
	<link rel="shortcut icon" href="<?php echo esc_url( $favicon_path . '/favicon.ico' ); ?>" />
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( $favicon_path . '/apple-touch-icon.png' ); ?>" />
	<meta name="apple-mobile-web-app-title" content="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
	<link rel="manifest" href="<?php echo esc_url( $favicon_path . '/site.webmanifest' ); ?>" />
	<?php
}

/**
 * Output theme color meta tags.
 *
 * Outputs regardless of Site Icon setting for consistent branding.
 *
 * @since 2.0.0
 */
function lovetravelChildOutputThemeColor() {
	?>
	<!-- Theme Colors -->
	<meta name="theme-color" content="#000000" />
	<meta name="msapplication-TileColor" content="#000000" />
	<?php
}
