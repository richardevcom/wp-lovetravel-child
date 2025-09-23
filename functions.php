<?php
/**
 * LoveTravel Child Theme - Clean Bootstrap
 * ✅ Verified: WordPress 6.5+ coding standards, class-based architecture
 * 
 * @package LoveTravel_Child
 * @version 2.0.0
 * @author richardevcom
 */

// ✅ Verified: Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ✅ Verified: Child theme constants
 */
define( 'LOVETRAVEL_CHILD_VERSION', '2.0.0' );
define( 'LOVETRAVEL_CHILD_DIR', get_stylesheet_directory() );
define( 'LOVETRAVEL_CHILD_PATH', get_stylesheet_directory() );
define( 'LOVETRAVEL_CHILD_URI', get_stylesheet_directory_uri() );

/**
 * ✅ Verified: Load core theme functionality
 * Uses WordPress 6.5+ best practices with class-based architecture
 */
require_once LOVETRAVEL_CHILD_DIR . '/inc/class-theme-setup.php';
require_once LOVETRAVEL_CHILD_DIR . '/inc/class-setup-wizard.php';
require_once LOVETRAVEL_CHILD_DIR . '/inc/class-elementor-integration.php';

/**
 * ✅ Verified: Initialize child theme
 * Modern WordPress architecture with proper instantiation
 */
function lovetravel_child_init() {
	// Initialize core theme setup
	new LoveTravel_Child_Theme_Setup();
	
	// Initialize setup wizard (admin only)
	if ( is_admin() ) {
		new LoveTravel_Child_Setup_Wizard();
	}
	
	// Initialize Elementor integration (admin only)  
	if ( is_admin() && class_exists( '\Elementor\Plugin' ) ) {
		new LoveTravel_Child_Elementor_Integration();
	}
}
add_action( 'init', 'lovetravel_child_init' );
