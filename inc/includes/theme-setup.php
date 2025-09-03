<?php

/**
 * Theme Setup and Core Functionality
 * 
 * This file handles the core theme setup, enqueuing of styles and scripts,
 * and other essential theme functionality.
 * 
 * @package LoveTravel_Child
 * @subpackage Includes
 * @version 1.0.0
 * @since 1.0.0
 */

// Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Set up theme defaults and register support for various WordPress features
 * 
 * @since 1.0.0
 */
function lovetravel_child_setup()
{
    // Add support for automatic feed links
    add_theme_support('automatic-feed-links');

    // Add support for post thumbnails
    add_theme_support('post-thumbnails');

    // Add support for title tag
    add_theme_support('title-tag');

    // Add support for HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
    ));

    // Load theme textdomain for translations
    load_child_theme_textdomain('lovetravel-child', LOVETRAVEL_CHILD_DIR . '/languages');
}
add_action('after_setup_theme', 'lovetravel_child_setup');

/**
 * Enqueue parent and child theme styles
 * 
 * @since 1.0.0
 */
function lovetravel_child_enqueue_styles()
{
    // Get parent theme version for cache busting
    $parent_theme = wp_get_theme(get_template());
    $parent_version = $parent_theme->get('Version');

    // Enqueue child theme stylesheet
    wp_enqueue_style(
        'lovetravel-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('lovetravel-parent-style'),
        LOVETRAVEL_CHILD_VERSION
    );

    // Enqueue custom CSS if it exists
    $custom_css_path = LOVETRAVEL_CHILD_DIR . '/assets/css/custom.css';
    if (file_exists($custom_css_path)) {
        wp_enqueue_style(
            'lovetravel-child-custom',
            LOVETRAVEL_CHILD_URI . '/assets/css/custom.css',
            array('lovetravel-child-style'),
            LOVETRAVEL_CHILD_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'lovetravel_child_enqueue_styles');

/**
 * Enqueue child theme scripts
 * 
 * @since 1.0.0
 */
function lovetravel_child_enqueue_scripts()
{
    // Enqueue custom JavaScript if it exists
    $custom_js_path = LOVETRAVEL_CHILD_DIR . '/assets/js/custom.js';
    if (file_exists($custom_js_path)) {
        wp_enqueue_script(
            'lovetravel-child-custom',
            LOVETRAVEL_CHILD_URI . '/assets/js/custom.js',
            array('jquery'),
            LOVETRAVEL_CHILD_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'lovetravel_child_enqueue_scripts');

/**
 * Add custom body classes for styling
 * 
 * @param array $classes Existing body classes
 * @return array Modified body classes
 * @since 1.0.0
 */
function lovetravel_child_body_classes($classes)
{
    // Add child theme class
    $classes[] = 'lovetravel-child';

    // Add version class
    $classes[] = 'lovetravel-child-v' . str_replace('.', '-', LOVETRAVEL_CHILD_VERSION);

    return $classes;
}
add_filter('body_class', 'lovetravel_child_body_classes');
