<?php

/**
 * LoveTravel Child Theme functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package LoveTravel_Child
 * @subpackage LoveTravel_Child
 * @since LoveTravel Child 1.0
 * @version 1.0.0
 * @author richardevcom
 */

// Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Child theme version and constants
 * 
 * @since LoveTravel Child 1.0
 */
define('LOVETRAVEL_CHILD_VERSION', '1.0.0');
define('LOVETRAVEL_CHILD_DIR', get_stylesheet_directory());
define('LOVETRAVEL_CHILD_URI', get_stylesheet_directory_uri());

// Core theme setup and functionality
if (! function_exists('lovetravel_child_theme_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * @since LoveTravel Child 1.0
     *
     * @return void
     */
    function lovetravel_child_theme_setup()
    {
        // Load theme setup module
        require_once LOVETRAVEL_CHILD_DIR . '/inc/setup/theme-setup.php';
    }
endif;
add_action('after_setup_theme', 'lovetravel_child_theme_setup');

// Load utility functions and customizations
if (! function_exists('lovetravel_child_load_utilities')) :
    /**
     * Load utility functions and customizations.
     *
     * @since LoveTravel Child 1.0
     *
     * @return void
     */
    function lovetravel_child_load_utilities()
    {
        // Load common utility functions
        require_once LOVETRAVEL_CHILD_DIR . '/inc/utilities/common-functions.php';

        // Load CPT overrides and customizations
        require_once LOVETRAVEL_CHILD_DIR . '/inc/utilities/cpt-overrides.php';
    }
endif;
add_action('init', 'lovetravel_child_load_utilities', 5); // Priority 5 to run before plugin CPT registration

// Load admin functionality
if (! function_exists('lovetravel_child_load_admin')) :
    /**
     * Load admin functionality and utilities.
     *
     * @since LoveTravel Child 1.0
     *
     * @return void
     */
    function lovetravel_child_load_admin()
    {
        if (is_admin()) {
            // Admin utilities and helpers
            require_once LOVETRAVEL_CHILD_DIR . '/inc/admin/admin-utilities.php';
        }
    }
endif;
add_action('init', 'lovetravel_child_load_admin');

// Load integrations (admin-only to prevent frontend conflicts)  
if (! function_exists('lovetravel_child_load_integrations')) :
    /**
     * Load third-party integrations and tools.
     * ✅ Verified: Only loads in admin context to prevent UI conflicts
     *
     * @since LoveTravel Child 1.0
     *
     * @return void
     */
    function lovetravel_child_load_integrations()
    {
        // Only load admin integrations in admin context
        if (! is_admin()) {
            return;
        }

        // Debug logging
        error_log('LoveTravel Child: Loading integrations in admin context');

        // Elementor templates integration (admin only)
        require_once LOVETRAVEL_CHILD_DIR . '/inc/integrations/elementor-templates.php';
        error_log('LoveTravel Child: Loaded elementor-templates.php');

        // Payload CMS integrations (admin only)  
        require_once LOVETRAVEL_CHILD_DIR . '/inc/integrations/payload-media-import.php';
        error_log('LoveTravel Child: Loaded payload-media-import.php');
        require_once LOVETRAVEL_CHILD_DIR . '/inc/integrations/mailchimp-subscriber-export.php';
        error_log('LoveTravel Child: Loaded mailchimp-subscriber-export.php');
        require_once LOVETRAVEL_CHILD_DIR . '/inc/integrations/payload-adventures-import.php';
        error_log('LoveTravel Child: Loaded payload-adventures-import.php');

        // ✅ FIXED: Admin page template should NOT be loaded here - it's included by admin_page() callback
        // The template file contains HTML and should only run when the admin page is rendered
    }
endif;
add_action('init', 'lovetravel_child_load_integrations'); // ✅ Fixed: Changed back to 'init' for proper admin_menu timing

// Load customizer modifications (if file exists)
if (! function_exists('lovetravel_child_load_customizer')) :
    /**
     * Load customizer modifications.
     *
     * @since LoveTravel Child 1.0
     *
     * @return void
     */
    function lovetravel_child_load_customizer()
    {
        $customizer_fonts_file = LOVETRAVEL_CHILD_DIR . '/inc/customizer/fonts.php';
        if (file_exists($customizer_fonts_file)) {
            require_once $customizer_fonts_file;
        }
    }
endif;
add_action('customize_register', 'lovetravel_child_load_customizer');

/**
 * Custom functions for the child theme
 * 
 * Add your custom functionality below this line.
 * Keep functions organized and well-documented.
 * 
 * @since LoveTravel Child 1.0
 */

// Add your custom functions here
