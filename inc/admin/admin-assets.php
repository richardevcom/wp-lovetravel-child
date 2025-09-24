<?php
/**
 * ✅ Admin Assets Manager
 * Centralized CSS and JS asset registration and enqueuing for admin pages
 * 
 * @package LoveTravel_Child
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class LoveTravel_Admin_Assets
{
    /**
     * ✅ Constructor - register hooks
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'register_admin_assets'), 5);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_context_specific_assets'), 10);
    }

    /**
     * ✅ Register all admin assets globally (but don't enqueue them yet)
     */
    public function register_admin_assets()
    {
        // ✅ Verified: Register shared admin tools CSS
        if (!wp_style_is('lovetravel-admin-tools', 'registered')) {
            wp_register_style(
                'lovetravel-admin-tools',
                LOVETRAVEL_CHILD_URI . '/assets/css/admin-tools.css',
                array(),
                LOVETRAVEL_CHILD_VERSION
            );
        }

        // ✅ Verified: Register setup wizard CSS  
        if (!wp_style_is('lovetravel-wizard', 'registered')) {
            wp_register_style(
                'lovetravel-wizard',
                LOVETRAVEL_CHILD_URI . '/assets/css/wizard.css',
                array('lovetravel-admin-tools'), // Depends on admin-tools
                LOVETRAVEL_CHILD_VERSION
            );
        }

        // ✅ Verified: Register admin JavaScript files
        if (!wp_script_is('lovetravel-wizard', 'registered')) {
            wp_register_script(
                'lovetravel-wizard',
                LOVETRAVEL_CHILD_URI . '/assets/js/wizard.js',
                array('jquery'),
                LOVETRAVEL_CHILD_VERSION,
                true
            );
        }

        if (!wp_script_is('lovetravel-adventures-import', 'registered')) {
            wp_register_script(
                'lovetravel-adventures-import',
                LOVETRAVEL_CHILD_URI . '/assets/js/admin-adventures-import.js',
                array('jquery'),
                LOVETRAVEL_CHILD_VERSION,
                true
            );
        }

        if (!wp_script_is('lovetravel-payload-import', 'registered')) {
            wp_register_script(
                'lovetravel-payload-import',
                LOVETRAVEL_CHILD_URI . '/assets/js/admin-payload-import.js',
                array('jquery'),
                LOVETRAVEL_CHILD_VERSION,
                true
            );
        }

        if (!wp_script_is('lovetravel-mailchimp-export', 'registered')) {
            wp_register_script(
                'lovetravel-mailchimp-export',
                LOVETRAVEL_CHILD_URI . '/assets/js/admin-mailchimp-export.js',
                array('jquery'),
                LOVETRAVEL_CHILD_VERSION,
                true
            );
        }
    }

    /**
     * ✅ Enqueue assets based on current admin page context
     */
    public function enqueue_context_specific_assets($hook_suffix)
    {
        // ✅ Setup Wizard page
        if ($hook_suffix === 'love-travel-theme_page_lovetravel-setup-wizard') {
            wp_enqueue_style('lovetravel-admin-tools');
            wp_enqueue_style('lovetravel-wizard');
            wp_enqueue_script('lovetravel-wizard');
            
            // Localize wizard script
            wp_localize_script('lovetravel-wizard', 'loveTravelWizard', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('lovetravel_wizard_nonce'),
                'strings' => array(
                    'importing' => __('Importing...', 'lovetravel-child'),
                    'complete'  => __('Import Complete!', 'lovetravel-child'),
                    'error'     => __('Import Error', 'lovetravel-child'),
                )
            ));
        }

        // ✅ Adventures Import page  
        if ($hook_suffix === 'nd_travel_cpt_1_page_payload-adventures-import') {
            wp_enqueue_style('lovetravel-admin-tools');
            wp_enqueue_script('lovetravel-adventures-import');
            
            // Localize adventures import script
            wp_localize_script('lovetravel-adventures-import', 'adventuresImport', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('lovetravel_adventures_import')
            ));
        }

        // ✅ Media Import page (under Media menu)
        if ($hook_suffix === 'media_page_payload-media-import') {
            wp_enqueue_style('lovetravel-admin-tools');
            wp_enqueue_script('lovetravel-payload-import');
            
            // Localize payload import script
            wp_localize_script('lovetravel-payload-import', 'payloadImport', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('payload_import_nonce')
            ));
        }

        // ✅ Mailchimp Export page (under Tools menu)
        if ($hook_suffix === 'tools_page_payload-subscribers-export') {
            wp_enqueue_style('lovetravel-admin-tools');
            wp_enqueue_script('lovetravel-mailchimp-export');
            
            // Localize Mailchimp script
            wp_localize_script('lovetravel-mailchimp-export', 'mc4wpExportConfig', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('mc4wp_export_nonce')
            ));
        }

        // ✅ All LoveTravel admin pages get admin-tools.css
        if (strpos($hook_suffix, 'lovetravel-') !== false || strpos($hook_suffix, 'love-travel-theme') !== false) {
            wp_enqueue_style('lovetravel-admin-tools');
        }
    }

    /**
     * ✅ Helper to check if we're on a LoveTravel admin page
     */
    public function is_lovetravel_admin_page($hook_suffix = null)
    {
        if (!$hook_suffix) {
            $hook_suffix = get_current_screen()->id ?? '';
        }

        $lovetravel_pages = array(
            'love-travel-theme_page_lovetravel-setup-wizard',
            'love-travel-theme_page_lovetravel-adventures-import', 
            'love-travel-theme_page_lovetravel-media-import',
            'love-travel-theme_page_lovetravel-mailchimp-export',
        );

        return in_array($hook_suffix, $lovetravel_pages) || 
               strpos($hook_suffix, 'lovetravel-') !== false ||
               strpos($hook_suffix, 'love-travel-theme') !== false;
    }
}

// ✅ Initialize admin assets manager
new LoveTravel_Admin_Assets();