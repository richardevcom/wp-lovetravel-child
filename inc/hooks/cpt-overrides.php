<?php

/**
 * Custom Post Type Overrides
 * 
 * This file contains hooks and filters to override plugin CPT labels and slugs
 * without modifying the original plugin files.
 * 
 * @package LoveTravel_Child
 * @subpackage Hooks
 * @version 1.0.0
 * @since 1.0.0
 */

// Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Override ND Travel CPT 1 (Packages) to use "Adventures" labels and slug
 * 
 * This filter runs just before the post type is registered, allowing us to
 * modify the registration arguments efficiently.
 * 
 * @param array $args Array of arguments for registering a post type
 * @param string $post_type Post type key
 * @return array Modified arguments
 * @since 1.0.0
 */
function lovetravel_child_override_packages_cpt($args, $post_type)
{
    // Only modify our target CPT
    if ($post_type !== 'nd_travel_cpt_1') {
        return $args;
    }

    // Override labels to use "Adventures"
    $args['labels'] = array(
        'name'                  => __('Adventures', 'lovetravel-child'),
        'singular_name'         => __('Adventure', 'lovetravel-child'),
        'menu_name'             => __('Adventures', 'lovetravel-child'),
        'name_admin_bar'        => __('Adventure', 'lovetravel-child'),
        'archives'              => __('Adventure Archives', 'lovetravel-child'),
        'attributes'            => __('Adventure Attributes', 'lovetravel-child'),
        'parent_item_colon'     => __('Parent Adventure:', 'lovetravel-child'),
        'all_items'             => __('All Adventures', 'lovetravel-child'),
        'add_new_item'          => __('Add New Adventure', 'lovetravel-child'),
        'add_new'               => __('Add New', 'lovetravel-child'),
        'new_item'              => __('New Adventure', 'lovetravel-child'),
        'edit_item'             => __('Edit Adventure', 'lovetravel-child'),
        'update_item'           => __('Update Adventure', 'lovetravel-child'),
        'view_item'             => __('View Adventure', 'lovetravel-child'),
        'view_items'            => __('View Adventures', 'lovetravel-child'),
        'search_items'          => __('Search Adventures', 'lovetravel-child'),
        'not_found'             => __('Not found', 'lovetravel-child'),
        'not_found_in_trash'    => __('Not found in Trash', 'lovetravel-child'),
        'featured_image'        => __('Featured Image', 'lovetravel-child'),
        'set_featured_image'    => __('Set featured image', 'lovetravel-child'),
        'remove_featured_image' => __('Remove featured image', 'lovetravel-child'),
        'use_featured_image'    => __('Use as featured image', 'lovetravel-child'),
        'insert_into_item'      => __('Insert into adventure', 'lovetravel-child'),
        'uploaded_to_this_item' => __('Uploaded to this adventure', 'lovetravel-child'),
        'items_list'            => __('Adventures list', 'lovetravel-child'),
        'items_list_navigation' => __('Adventures list navigation', 'lovetravel-child'),
        'filter_items_list'     => __('Filter adventures list', 'lovetravel-child'),
    );

    // Override the rewrite slug
    $args['rewrite'] = array(
        'slug' => 'adventures',
        'with_front' => false,
    );

    return $args;
}
add_filter('register_post_type_args', 'lovetravel_child_override_packages_cpt', 20, 2);

/**
 * Override taxonomy labels to match the new "Adventures" terminology
 * 
 * @param array $args Array of arguments for registering a taxonomy
 * @param string $taxonomy Taxonomy key
 * @return array Modified arguments
 * @since 1.0.0
 */
function lovetravel_child_override_packages_taxonomies($args, $taxonomy)
{
    // Override taxonomy labels for package-related taxonomies
    switch ($taxonomy) {
        case 'nd_travel_cpt_1_tax_1': // Durations
            $args['labels'] = array(
                'name'              => __('Adventure Durations', 'lovetravel-child'),
                'singular_name'     => __('Adventure Duration', 'lovetravel-child'),
                'menu_name'         => __('Durations', 'lovetravel-child'),
            );
            $args['rewrite'] = array('slug' => 'durations');
            break;

        case 'nd_travel_cpt_1_tax_2': // Difficulty
            $args['labels'] = array(
                'name'              => __('Adventure Difficulty', 'lovetravel-child'),
                'singular_name'     => __('Adventure Difficulty', 'lovetravel-child'),
                'menu_name'         => __('Difficulty', 'lovetravel-child'),
            );
            $args['rewrite'] = array('slug' => 'difficulty');
            break;

        case 'nd_travel_cpt_1_tax_3': // Min Age
            $args['labels'] = array(
                'name'              => __('Adventure Month', 'lovetravel-child'),
                'singular_name'     => __('Adventure Month', 'lovetravel-child'),
                'menu_name'         => __('Month', 'lovetravel-child'),
            );
            $args['rewrite'] = array('slug' => 'month');
            break;
    }

    return $args;
}
add_filter('register_taxonomy_args', 'lovetravel_child_override_packages_taxonomies', 20, 2);

/**
 * Force flush rewrite rules when theme is activated to update permalinks
 * 
 * @since 1.0.0
 */
function lovetravel_child_flush_rewrite_rules()
{
    // Use WordPress function to flush rewrite rules
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}
add_action('after_switch_theme', 'lovetravel_child_flush_rewrite_rules');
