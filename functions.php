<?php

/**
 * LoveTravel Child Theme Functions
 * 
 * This file handles the main functionality for the LoveTravel child theme.
 * It includes various components and ensures proper inheritance from the parent theme.
 * 
 * @package LoveTravel_Child
 * @version 1.0.0
 * @author richardevcom
 * @link https://github.com/richardevcom
 */

// Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Child theme version
 */
define('LOVETRAVEL_CHILD_VERSION', '1.0.0');

/**
 * Theme directory path
 */
define('LOVETRAVEL_CHILD_DIR', get_stylesheet_directory());

/**
 * Theme directory URI
 */
define('LOVETRAVEL_CHILD_URI', get_stylesheet_directory_uri());

/**
 * Include core theme setup and functionality
 */
require_once LOVETRAVEL_CHILD_DIR . '/inc/includes/theme-setup.php';

/**
 * Include hooks and filters for customizations
 */
require_once LOVETRAVEL_CHILD_DIR . '/inc/hooks/cpt-overrides.php';

/**
 * Include admin utilities and helper functions
 */
require_once LOVETRAVEL_CHILD_DIR . '/inc/includes/admin-utilities.php';

/**
 * Include Elementor templates helper (import/export utilities)
 */
require_once LOVETRAVEL_CHILD_DIR . '/inc/includes/elementor-templates.php';

/**
 * Include Payload CMS media import functionality
 */
require_once LOVETRAVEL_CHILD_DIR . '/inc/tools/payload-media-import.php';

/**
 * Include Mailchimp subscriber export functionality
 */
require_once LOVETRAVEL_CHILD_DIR . '/inc/tools/mailchimp-subscriber-export.php';

/**
 * Include Adventures import functionality (Payload CMS -> CPT `nd_travel_cpt_1`)
 */
require_once LOVETRAVEL_CHILD_DIR . '/inc/tools/payload-adventures-import.php';

/**
 * Include customizer modifications (if file exists)
 */
$customizer_fonts_file = LOVETRAVEL_CHILD_DIR . '/inc/customizer/fonts.php';
if (file_exists($customizer_fonts_file)) {
    require_once $customizer_fonts_file;
}

/**
 * Custom functions for the child theme
 * 
 * Add your custom functionality below this line.
 * Keep functions organized and well-documented.
 */

/**
 * Example custom function
 * 
 * @since 1.0.0
 */
function lovetravel_child_example_function()
{
    // Add your custom code here
}

// Add your custom functions here
