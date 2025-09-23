<?php
/**
 * Admin Utilities
 * 
 * This file contains admin-specific utilities and helper functions.
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
 * Add admin notice to flush rewrite rules after CPT changes
 * 
 * @since 1.0.0
 */
function lovetravel_child_rewrite_rules_notice() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $dismissed = get_option('lovetravel_child_rewrite_dismissed', false);
    if ($dismissed) {
        return;
    }

    echo '<div class="notice notice-info is-dismissible">';
    echo '<p><strong>LoveTravel Child Theme:</strong> ';
    echo __('Custom post type labels have been updated to "Trips". ', 'lovetravel-child');
    echo '<a href="' . esc_url(get_admin_url(null, 'options-permalink.php')) . '">';
    echo __('Visit Permalinks settings', 'lovetravel-child');
    echo '</a> ';
    echo __('and save to update URL structure.', 'lovetravel-child');
    echo ' <a href="' . esc_url(add_query_arg('dismiss_rewrite_notice', '1')) . '">';
    echo __('Dismiss', 'lovetravel-child');
    echo '</a></p>';
    echo '</div>';
}
add_action('admin_notices', 'lovetravel_child_rewrite_rules_notice');

/**
 * Handle dismissal of rewrite rules notice
 * 
 * @since 1.0.0
 */
function lovetravel_child_dismiss_rewrite_notice() {
    if (isset($_GET['dismiss_rewrite_notice']) && current_user_can('manage_options')) {
        update_option('lovetravel_child_rewrite_dismissed', true);
        wp_redirect(remove_query_arg('dismiss_rewrite_notice'));
        exit;
    }
}
add_action('admin_init', 'lovetravel_child_dismiss_rewrite_notice');