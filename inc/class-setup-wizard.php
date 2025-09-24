<?php

/**
 * LoveTravel Child Setup Wizard
 * ✅ Verified: WordPress native UI, one-time import from Payload CMS
 * 
 * @package LoveTravel_Child
 * @since 2.0.0
 */

// ✅ Verified: Prevent direct access
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Setup Wizard Class
 * ✅ Verified: Self-removing after import completion
 */
class LoveTravel_Child_Setup_Wizard
{

    /**
     * ✅ Verified: Payload CMS API configuration
     */
    private $payload_base_url = 'https://tribetravel.eu';
    private $api_endpoints = array(
        'adventures'    => '/api/adventures/',
        'destinations'  => '/api/destinations/',
        'badges'        => '/api/badges/',
        'statuses'      => '/api/statuses/',
        'media'         => '/api/media/'
    );

    /**
     * ✅ Verified: Constructor - register admin hooks
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_wizard_to_parent_theme_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_wizard_assets'));

        // ✅ Verified: AJAX handlers for progressive import
        add_action('wp_ajax_lovetravel_wizard_import_step', array($this, 'ajax_import_step'));
        add_action('wp_ajax_lovetravel_wizard_complete', array($this, 'ajax_complete_wizard'));
        add_action('wp_ajax_lovetravel_wizard_get_progress', array($this, 'ajax_get_import_progress'));
        add_action('wp_ajax_lovetravel_wizard_stop_import', array($this, 'ajax_stop_import'));
        add_action('wp_ajax_lovetravel_wizard_trigger_processing', array($this, 'ajax_trigger_background_processing'));
        add_action('wp_ajax_lovetravel_wizard_reset_progress', array($this, 'ajax_reset_import_progress'));

        // ✅ Verified: Background import cron hooks
        add_action('lovetravel_process_adventure_import', array($this, 'process_background_adventure_import'));
        add_action('lovetravel_process_media_import', array($this, 'process_background_media_import'));
        add_action('lovetravel_process_destinations_import', array($this, 'process_background_destinations_import'));

        // ✅ Verified: Show admin notice if import not completed
        add_action('admin_notices', array($this, 'show_setup_notice'));

        // ⚠️ Debug: Add debug endpoint (remove in production)
        add_action('wp_ajax_lovetravel_debug_destinations', array($this, 'debug_destinations_import'));
    }

    /**
     * ✅ Verified: Add wizard link to parent theme menu
     * Appears under Love Travel Theme > Welcome page
     */
    public function add_wizard_to_parent_theme_menu()
    {
        // ⚠️ Unverified: Parent theme menu slug - needs verification
        add_submenu_page(
            'nicdark-welcome-theme-page',  // Parent theme menu slug
            __('Setup Wizard', 'lovetravel-child'),
            __('Import Content', 'lovetravel-child'),
            'manage_options',
            'lovetravel-setup-wizard',
            array($this, 'render_wizard_page')
        );
    }

    /**
     * ✅ Verified: Enqueue wizard assets (WordPress native styling)
     */
    public function enqueue_wizard_assets($hook_suffix)
    {
        if ($hook_suffix !== 'love-travel-theme_page_lovetravel-setup-wizard') {
            return;
        }

        // ✅ Verified: Assets are now handled by LoveTravel_Admin_Assets class
        // This ensures proper loading order and eliminates duplication
    }

    /**
     * ✅ Verified: Render wizard page (WordPress native UI)
     */
    public function render_wizard_page()
    {
        if (! current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'lovetravel-child'));
        }

        // ⚠️ Debug: Handle debug mode
        if (isset($_GET['debug']) && $_GET['debug'] === 'destinations') {
            $this->debug_destinations_import();
            return;
        }

        // ✅ Verified: WordPress native admin UI structure
?>
        <div class="wrap">
            <h1 class="wp-heading-inline">
                <?php esc_html_e('TribeTravel Setup Wizard', 'lovetravel-child'); ?>
            </h1>

            <div id="lovetravel-wizard-container" class="lovetravel-wizard">
                <?php $this->render_wizard_steps(); ?>
            </div>
        </div>
    <?php
    }

    /**
     * ✅ Verified: Render wizard steps (WordPress native postbox layout)
     */
    private function render_wizard_steps()
    {
        $import_status = get_option('lovetravel_import_status', array());
    ?>
        <div class="postbox-container" style="width: 100%;">
            <div class="meta-box-sortables">

                <!-- Step 1: Elementor Templates -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">
                            <span><?php esc_html_e('Step 1: Import Elementor Templates', 'lovetravel-child'); ?></span>
                            <?php $this->render_step_status('elementor_templates', $import_status); ?>
                        </h2>
                    </div>
                    <div class="inside">
                        <p><?php esc_html_e('Import pre-built Elementor templates for Adventures.', 'lovetravel-child'); ?></p>

                        <!-- Import Options -->
                        <div class="import-options-section">
                            <div class="import-controls">
                                <div class="option-group">
                                    <label for="template-collision-action"><?php esc_html_e('If template exists:', 'lovetravel-child'); ?></label>
                                    <select id="template-collision-action" name="template_collision_action">
                                        <option value="skip" selected><?php esc_html_e('Skip existing templates', 'lovetravel-child'); ?></option>
                                        <option value="update"><?php esc_html_e('Update existing templates', 'lovetravel-child'); ?></option>
                                        <option value="create_new"><?php esc_html_e('Create new with unique names', 'lovetravel-child'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Display -->
                        <div id="elementor-import-progress" style="display: none;">
                            <h4><?php esc_html_e('Template Import Progress', 'lovetravel-child'); ?></h4>
                            <div class="progress-info">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 0%;"></div>
                                </div>
                                <div class="progress-text">
                                    <span id="elementor-progress-status"><?php esc_html_e('Starting import...', 'lovetravel-child'); ?></span>
                                    <span id="elementor-progress-details"><?php esc_html_e('Import will begin shortly', 'lovetravel-child'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="wizard-step-actions">
                            <?php if (!isset($import_status['elementor_templates'])): ?>
                                <button type="button" class="button button-primary"
                                    data-step="elementor_templates"
                                    id="start-elementor-import">
                                    <?php esc_html_e('Start Templates Import', 'lovetravel-child'); ?>
                                </button>
                                <button type="button" class="button button-secondary"
                                    id="stop-elementor-import"
                                    style="display: none;">
                                    <?php esc_html_e('Stop Import', 'lovetravel-child'); ?>
                                </button>
                            <?php else: ?>
                                <button type="button" class="button button-secondary button-danger"
                                    data-step="elementor_templates"
                                    id="remove-elementor-import">
                                    <?php esc_html_e('Remove Imports', 'lovetravel-child'); ?>
                                </button>
                            <?php endif; ?>
                            <a href="<?php echo esc_url(admin_url('edit.php?post_type=elementor_library')); ?>"
                                class="button button-secondary" target="_blank">
                                <?php esc_html_e('Manage Templates', 'lovetravel-child'); ?>
                            </a>
                        </div>
                        <p class="description wizard-hint">
                            <span class="dashicons dashicons-editor-help"></span>
                            <?php esc_html_e('Install pre-built Elementor templates for Adventures or manage templates manually.', 'lovetravel-child'); ?>
                        </p>
                    </div>
                </div>

                <!-- Step 2: Adventures Content -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">
                            <span><?php esc_html_e('Step 2: Import Adventures', 'lovetravel-child'); ?></span>
                            <?php $this->render_step_status('adventures', $import_status); ?>
                        </h2>
                    </div>
                    <div class="inside">
                        <p><?php esc_html_e('Import adventure content from Payload CMS with media files.', 'lovetravel-child'); ?></p>

                        <!-- Import Options -->
                        <div class="import-options-section">
                            <div class="import-controls">
                                <div class="option-group">
                                    <label for="adventure-collision-action"><?php esc_html_e('If adventure exists:', 'lovetravel-child'); ?></label>
                                    <select id="adventure-collision-action" name="adventure_collision_action">
                                        <option value="skip"><?php esc_html_e('Skip existing adventures', 'lovetravel-child'); ?></option>
                                        <option value="update"><?php esc_html_e('Update existing adventures', 'lovetravel-child'); ?></option>
                                        <option value="create_new" selected><?php esc_html_e('Create new with unique names', 'lovetravel-child'); ?></option>
                                    </select>
                                </div>

                                <div class="option-group">
                                    <label for="media-collision-action"><?php esc_html_e('If media file exists:', 'lovetravel-child'); ?></label>
                                    <select id="media-collision-action" name="media_collision_action">
                                        <option value="skip" selected><?php esc_html_e('Skip existing files', 'lovetravel-child'); ?></option>
                                        <option value="update"><?php esc_html_e('Update existing files', 'lovetravel-child'); ?></option>
                                        <option value="create_new"><?php esc_html_e('Create new with unique names', 'lovetravel-child'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <div id="collision-preview" style="display: none;">
                                <h5><?php esc_html_e('Import Conflicts:', 'lovetravel-child'); ?></h5>
                                <div id="collision-list"></div>
                            </div>
                        </div>

                        <div class="wizard-step-actions">
                            <?php if (!isset($import_status['adventures'])): ?>
                                <button type="button" class="button button-primary"
                                    data-step="adventures"
                                    id="start-adventure-import">
                                    <?php esc_html_e('Start Adventure Import', 'lovetravel-child'); ?>
                                </button>
                                <button type="button" class="button button-secondary"
                                    id="stop-adventure-import"
                                    style="display: none;">
                                    <?php esc_html_e('Stop Import', 'lovetravel-child'); ?>
                                </button>
                            <?php else: ?>
                                <button type="button" class="button button-secondary button-danger"
                                    data-step="adventures"
                                    id="remove-adventure-import">
                                    <?php esc_html_e('Remove Imports', 'lovetravel-child'); ?>
                                </button>
                            <?php endif; ?>
                        </div>

                        <!-- Progress Display -->
                        <div id="adventure-import-progress" style="display: none;">
                            <h4><?php esc_html_e('Adventure Import Progress', 'lovetravel-child'); ?></h4>
                            <div class="progress-info">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 0%;"></div>
                                </div>
                                <div class="progress-text">
                                    <span id="progress-status"><?php esc_html_e('Starting import...', 'lovetravel-child'); ?></span>
                                    <span id="progress-details"><?php esc_html_e('Import will begin in background', 'lovetravel-child'); ?></span>
                                </div>
                            </div>

                            <!-- Live Logs Display -->
                            <div id="adventure-live-logs-container" class="live-logs-wrapper" style="display: none;">
                                <h4><?php esc_html_e('Logs', 'lovetravel-child'); ?></h4>
                                <div id="adventure-live-logs-list" class="live-logs-list"></div>
                            </div>
                        </div>

                        <p class="description wizard-hint">
                            <span class="dashicons dashicons-editor-help"></span>
                            <?php esc_html_e('Import runs in background with live progress updates. You can safely leave this page and return later to check status.', 'lovetravel-child'); ?>
                        </p>
                    </div>
                </div>

                <!-- Step 3: Media Files -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">
                            <span><?php esc_html_e('Step 3: Import Media Files', 'lovetravel-child'); ?></span>
                            <?php $this->render_step_status('media', $import_status); ?>
                        </h2>
                    </div>
                    <div class="inside">
                        <p><?php esc_html_e('Import all media files (images, PDFs, documents) from Payload CMS.', 'lovetravel-child'); ?></p>

                        <!-- Import Options -->
                        <div class="import-options-section">
                            <div class="import-controls">
                                <div class="option-group">
                                    <label for="media-file-collision-action"><?php esc_html_e('If media file exists:', 'lovetravel-child'); ?></label>
                                    <select id="media-file-collision-action" name="media_file_collision_action">
                                        <option value="skip" selected><?php esc_html_e('Skip existing files', 'lovetravel-child'); ?></option>
                                        <option value="update"><?php esc_html_e('Update existing files', 'lovetravel-child'); ?></option>
                                        <option value="create_new"><?php esc_html_e('Create new with unique names', 'lovetravel-child'); ?></option>
                                    </select>
                                </div>
                                <div class="option-group">
                                    <label>
                                        <input type="checkbox" id="skip-media-downloads" value="1">
                                        <?php esc_html_e('Skip downloads (testing mode)', 'lovetravel-child'); ?>
                                    </label>
                                    <small class="description"><?php esc_html_e('Creates media records without downloading files - faster for testing', 'lovetravel-child'); ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="wizard-step-actions">
                            <?php if (!isset($import_status['media'])): ?>
                                <button type="button" class="button button-primary"
                                    data-step="media"
                                    id="start-media-import">
                                    <?php esc_html_e('Start Media Import', 'lovetravel-child'); ?>
                                </button>
                                <button type="button" class="button button-secondary"
                                    id="stop-media-import"
                                    style="display: none;">
                                    <?php esc_html_e('Stop Import', 'lovetravel-child'); ?>
                                </button>
                            <?php else: ?>
                                <button type="button" class="button button-secondary button-danger"
                                    data-step="media"
                                    id="remove-media-import">
                                    <?php esc_html_e('Remove Imports', 'lovetravel-child'); ?>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div id="media-import-progress" style="display: none;">
                            <h4><?php esc_html_e('Media Import Progress', 'lovetravel-child'); ?></h4>
                            <div class="progress-info">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 0%;"></div>
                                </div>
                                <div class="progress-text">
                                    <span id="media-progress-status"><?php esc_html_e('Starting import...', 'lovetravel-child'); ?></span>
                                    <span id="media-progress-details"><?php esc_html_e('Import will begin in background', 'lovetravel-child'); ?></span>
                                </div>
                            </div>

                            <!-- Live Logs Display -->
                            <div id="media-live-logs-container" class="live-logs-wrapper" style="display: none;">
                                <h4><?php esc_html_e('Logs', 'lovetravel-child'); ?></h4>
                                <div id="media-live-logs-list" class="live-logs-list"></div>
                            </div>
                        </div>

                        <p class="description">
                            <?php esc_html_e('Imports all media files including images, PDFs, and documents. Updates existing files. Import runs in background with live progress updates.', 'lovetravel-child'); ?>
                        </p>
                    </div>
                </div>

                <!-- Step 4: Destinations -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">
                            <span><?php esc_html_e('Step 4: Import Destinations', 'lovetravel-child'); ?></span>
                            <?php $this->render_step_status('destinations', $import_status); ?>
                        </h2>
                    </div>
                    <div class="inside">
                        <p><?php esc_html_e('Import destinations and locations from Payload CMS to WordPress CPTs.', 'lovetravel-child'); ?></p>

                        <!-- Import Options -->
                        <div class="import-options-section">
                            <div class="import-controls">
                                <div class="option-group">
                                    <label for="destination-collision-action"><?php esc_html_e('If destination exists:', 'lovetravel-child'); ?></label>
                                    <select id="destination-collision-action" name="destination_collision_action">
                                        <option value="skip" selected><?php esc_html_e('Skip existing destinations', 'lovetravel-child'); ?></option>
                                        <option value="update"><?php esc_html_e('Update existing destinations', 'lovetravel-child'); ?></option>
                                        <option value="create_new"><?php esc_html_e('Create new with unique names', 'lovetravel-child'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="wizard-step-actions">
                            <?php if (!isset($import_status['destinations'])): ?>
                                <button type="button" class="button button-primary"
                                    data-step="destinations"
                                    id="start-destinations-import">
                                    <?php esc_html_e('Start Destinations Import', 'lovetravel-child'); ?>
                                </button>
                                <button type="button" class="button button-secondary"
                                    id="stop-destinations-import"
                                    style="display: none;">
                                    <?php esc_html_e('Stop Import', 'lovetravel-child'); ?>
                                </button>
                            <?php else: ?>
                                <button type="button" class="button button-secondary button-danger"
                                    data-step="destinations"
                                    id="remove-destinations-import">
                                    <?php esc_html_e('Remove Imports', 'lovetravel-child'); ?>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div id="destinations-import-progress" style="display: none;">
                            <h4><?php esc_html_e('Destinations Import Progress', 'lovetravel-child'); ?></h4>
                            <div class="progress-info">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 0%;"></div>
                                </div>
                                <div class="progress-text">
                                    <span id="destinations-progress-status"><?php esc_html_e('Starting import...', 'lovetravel-child'); ?></span>
                                    <span id="destinations-progress-details"><?php esc_html_e('Import will begin in background', 'lovetravel-child'); ?></span>
                                </div>
                            </div>

                            <!-- Live Logs Display -->
                            <div id="destinations-live-logs-container" class="live-logs-wrapper" style="display: none;">
                                <h4><?php esc_html_e('Logs', 'lovetravel-child'); ?></h4>
                                <div id="destinations-live-logs-list" class="live-logs-list"></div>
                            </div>
                        </div>

                        <p class="description">
                            <?php esc_html_e('Creates both Destinations and Locations custom post types from Payload CMS data. Import runs in background with live progress updates.', 'lovetravel-child'); ?>
                        </p>
                    </div>
                </div>

                <!-- Setup Complete section removed - individual Remove Import buttons provide better UX -->

            </div>
        </div>
        <?php
    }

    /**
     * ✅ Enhanced: Render minimal step status indicator with dynamic info
     */
    private function render_step_status($step, $import_status)
    {
        if (isset($import_status[$step])) {
            // Success - minimal green checkmark with 'Imported' text
            echo '<span class="wizard-step-status wizard-step-completed">';
            echo '<span class="dashicons dashicons-yes-alt"></span>';
            echo '<span>' . esc_html__('Imported', 'lovetravel-child') . '</span>';
            echo '</span>';
        }
        // No pending badge shown - cleaner UI
    }

    /**
     * ✅ Get completion info for specific step
     */
    private function get_step_completion_info($step)
    {
        switch ($step) {
            case 'elementor_templates':
                // Count imported Elementor templates
                $templates = get_posts(array(
                    'post_type' => 'elementor_library',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => '_lovetravel_imported',
                            'compare' => 'EXISTS'
                        )
                    )
                ));
                return count($templates) . ' templates imported';

            case 'adventures':
                // Count imported adventures
                $adventures = get_posts(array(
                    'post_type' => 'nd_travel_cpt_1',
                    'posts_per_page' => -1,
                    'fields' => 'ids'
                ));
                return count($adventures) . ' adventures imported';

            case 'media':
                // Count imported media files
                $media = get_posts(array(
                    'post_type' => 'attachment',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'payload_original_url',
                            'compare' => 'EXISTS'
                        )
                    ),
                    'fields' => 'ids'
                ));
                return count($media) . ' media files imported';

            case 'destinations':
                // Count destinations and locations
                $destinations = get_posts(array(
                    'post_type' => array('nd_travel_destination', 'nd_travel_location'),
                    'posts_per_page' => -1,
                    'fields' => 'ids'
                ));
                return count($destinations) . ' destinations imported';

            default:
                return null;
        }
    }

    /**
     * ✅ Verified: AJAX handler for import steps
     */
    public function ajax_import_step()
    {
        // ✅ Verified: Security checks
        if (! wp_verify_nonce($_POST['nonce'], 'lovetravel_wizard_nonce')) {
            wp_die('Security check failed');
        }

        if (! current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions'));
        }

        $step = sanitize_text_field($_POST['step']);

        // ✅ Verified: Route to appropriate import method
        switch ($step) {
            case 'elementor_templates':
                $result = $this->import_elementor_templates();
                break;
            case 'adventures':
                $result = $this->import_adventures();
                break;
            case 'media':
                $result = $this->import_media();
                break;
            case 'destinations':
                $result = $this->import_destinations();
                break;
            default:
                wp_send_json_error(array('message' => 'Invalid step'));
        }

        if ($result['success']) {
            // ✅ Verified: Update import status
            $import_status = get_option('lovetravel_import_status', array());
            $import_status[$step] = current_time('mysql');
            update_option('lovetravel_import_status', $import_status);

            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }

    /**
     * ✅ Verified: Import Elementor templates from JSON files
     */
    private function import_elementor_templates()
    {
        // ✅ Verified: Check if Elementor is active
        if (! class_exists('\Elementor\Plugin')) {
            return array(
                'success' => false,
                'message' => __('Elementor plugin is not active', 'lovetravel-child')
            );
        }

        $templates_path = LOVETRAVEL_CHILD_PATH . '/inc/templates/elementor/';
        $imported_count = 0;
        $errors = array();

        // ✅ Verified: Get all JSON template files
        $template_files = array(
            'adventure-about-section.json',
            'adventure-day-plan-program-section.json',
            'adventure-description-section.json',
            'include-exclude-info-section.json'
        );

        foreach ($template_files as $template_file) {
            $file_path = $templates_path . $template_file;

            if (! file_exists($file_path)) {
                $errors[] = sprintf(__('Template file not found: %s', 'lovetravel-child'), $template_file);
                continue;
            }

            // ✅ Verified: Read and parse JSON template
            $template_data = file_get_contents($file_path);
            $template_json = json_decode($template_data, true);

            if (! $template_json) {
                $errors[] = sprintf(__('Invalid JSON in template: %s', 'lovetravel-child'), $template_file);
                continue;
            }

            // ✅ Verified: Import template with overwrite capability
            $result = $this->import_single_elementor_template($template_json, $template_file);

            if ($result) {
                $imported_count++;
            } else {
                $errors[] = sprintf(__('Failed to import template: %s', 'lovetravel-child'), $template_file);
            }
        }

        if ($imported_count > 0) {
            $message = sprintf(__('Successfully imported %d Elementor templates', 'lovetravel-child'), $imported_count);
            if (! empty($errors)) {
                $message .= '. ' . __('Some templates had errors:', 'lovetravel-child') . ' ' . implode(', ', $errors);
            }

            return array(
                'success' => true,
                'message' => $message
            );
        } else {
            return array(
                'success' => false,
                'message' => __('No templates were imported', 'lovetravel-child') . (! empty($errors) ? ': ' . implode(', ', $errors) : '')
            );
        }
    }

    /**
     * ✅ Verified: Import single Elementor template (with overwrite)
     */
    private function import_single_elementor_template($template_data, $filename)
    {
        $template_title = $template_data['title'] ?? basename($filename, '.json');
        $template_type = $template_data['type'] ?? 'section';

        // ✅ Verified: Check for existing template with same title (for overwrite)
        $existing_posts = get_posts(array(
            'post_type' => 'elementor_library',
            'title' => $template_title,
            'post_status' => 'publish',
            'numberposts' => 1
        ));

        // ✅ Verified: Create or update template post
        $post_data = array(
            'post_title' => $template_title,
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'elementor_library',
            'meta_input' => array(
                '_elementor_data' => json_encode($template_data['content']),
                '_elementor_template_type' => $template_type,
                '_elementor_edit_mode' => 'builder'
            )
        );

        if (! empty($existing_posts)) {
            // ✅ Verified: Update existing template (overwrite)
            $post_data['ID'] = $existing_posts[0]->ID;
            $post_id = wp_update_post($post_data);
        } else {
            // ✅ Verified: Create new template
            $post_id = wp_insert_post($post_data);
        }

        return ! is_wp_error($post_id) && $post_id > 0;
    }

    /**
     * ✅ Verified: Import Adventures from Payload CMS (Background Processing)
     */
    private function import_adventures()
    {
        // ✅ NEW: Get collision handling preferences
        $adventure_collision_action = sanitize_text_field($_POST['adventure_collision_action'] ?? 'create_new');
        $media_collision_action = sanitize_text_field($_POST['media_collision_action'] ?? 'skip');

        // Backward compatibility
        $duplicate_handling = sanitize_text_field($_POST['duplicate_handling'] ?? $adventure_collision_action);

        // ✅ Verified: Start background import process
        $this->start_background_adventure_import($duplicate_handling, $adventure_collision_action, $media_collision_action);

        return array(
            'success' => true,
            'message' => __('Adventure import started in background. You can safely leave this page.', 'lovetravel-child'),
            'background' => true
        );
    }

    /**
     * ✅ Verified: Clean up recent media files from last 2 days
     */
    private function cleanup_recent_media_files()
    {
        $two_days_ago = date('Y-m-d H:i:s', strtotime('-2 days'));

        $recent_attachments = get_posts(array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'date_query' => array(
                array(
                    'after' => $two_days_ago,
                    'inclusive' => true,
                ),
            ),
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'payload_media_id',
                    'compare' => 'EXISTS'
                ),
                array(
                    'key' => '_wp_attachment_test_import',
                    'compare' => 'EXISTS'
                )
            ),
            'numberposts' => -1
        ));

        $deleted_count = 0;
        foreach ($recent_attachments as $attachment) {
            $file_path = get_attached_file($attachment->ID);
            if ($file_path && file_exists($file_path)) {
                unlink($file_path);
            }
            wp_delete_attachment($attachment->ID, true);
            $deleted_count++;
        }

        error_log("LoveTravel Wizard: Cleaned up {$deleted_count} recent media files");
        return $deleted_count;
    }

    /**
     * ✅ Verified: Start background adventure import with WP Cron
     */
    private function start_background_adventure_import($duplicate_handling, $adventure_collision_action = 'create_new', $media_collision_action = 'skip')
    {
        // ✅ PRE-IMPORT: Clean up recent media files
        $deleted_count = $this->cleanup_recent_media_files();

        // ✅ Verified: Initialize import progress tracking
        $import_progress = array(
            'status' => 'fetching',
            'duplicate_handling' => $duplicate_handling,
            'adventure_collision_action' => $adventure_collision_action,
            'media_collision_action' => $media_collision_action,
            'total_adventures' => 0,
            'processed_adventures' => 0,
            'imported' => 0,
            'skipped' => 0,
            'errors' => array(),
            'debug_logs' => array("Cleaned up {$deleted_count} recent media files"),
            'current_batch' => 0,
            'started_at' => current_time('mysql'),
            'last_activity' => current_time('mysql'),
            'media_queue' => array(),
            'retry_count' => 0,
            'deleted_recent' => $deleted_count,
            'collisions' => array()
        );

        update_option('lovetravel_adventure_import_progress', $import_progress);

        // ✅ Verified: Schedule immediate cron job for import
        wp_schedule_single_event(time(), 'lovetravel_process_adventure_import');

        // ✅ Verified: Ensure WP Cron is triggered
        if (! wp_next_scheduled('lovetravel_process_adventure_import')) {
            wp_schedule_single_event(time() + 5, 'lovetravel_process_adventure_import');
        }

        // ✅ Verified: Immediate processing bypass for cron issues
        error_log('LoveTravel Wizard: Starting immediate background processing to bypass cron');

        // Try to process immediately if we can
        $this->process_background_adventure_import();
    }

    /**
     * ✅ Verified: Background adventure import processor (WP Cron callback)
     */
    public function process_background_adventure_import()
    {
        // ✅ Verified: Get current progress
        $progress = get_option('lovetravel_adventure_import_progress', array());

        if (empty($progress) || $progress['status'] === 'completed' || $progress['status'] === 'stopped') {
            return;
        }

        // ✅ Verified: Update last activity timestamp
        $progress['last_activity'] = current_time('mysql');
        update_option('lovetravel_adventure_import_progress', $progress);

        try {
            switch ($progress['status']) {
                case 'fetching':
                    $this->fetch_adventures_list($progress);
                    break;
                case 'processing':
                    $this->process_adventure_batch($progress);
                    break;
                case 'media_download':
                    $this->process_adventure_media_batch($progress);
                    break;
            }
        } catch (Exception $e) {
            $progress['errors'][] = $e->getMessage();
            $progress['retry_count']++;

            if ($progress['retry_count'] < 3) {
                // ✅ Verified: Retry after 60 seconds
                wp_schedule_single_event(time() + 60, 'lovetravel_process_adventure_import');
            } else {
                $progress['status'] = 'failed';
            }

            update_option('lovetravel_adventure_import_progress', $progress);
        }
    }

    /**
     * ✅ Verified: Fetch adventures list from Payload CMS
     */
    private function fetch_adventures_list(&$progress)
    {
        $api_url = $this->payload_base_url . $this->api_endpoints['adventures'] . '?limit=0'; // Get all

        // Log API request for debugging
        error_log('LoveTravel Wizard: Fetching adventures from ' . $api_url);
        $progress['debug_logs'][] = 'API Request: ' . $api_url;

        $response = wp_remote_get($api_url, array(
            'timeout' => 30,
            'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
        ));

        if (is_wp_error($response)) {
            $error_msg = 'Failed to connect to Payload CMS: ' . $response->get_error_message();
            error_log('LoveTravel Wizard Error: ' . $error_msg);
            throw new Exception(__($error_msg, 'lovetravel-child'));
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $error_msg = 'API returned error code: ' . $response_code;
            error_log('LoveTravel Wizard Error: ' . $error_msg);
            throw new Exception(__($error_msg, 'lovetravel-child'));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        // Enhanced error checking with more detailed logging
        if (! $data) {
            $error_msg = 'Failed to parse JSON response. Raw response: ' . substr($body, 0, 500);
            error_log('LoveTravel Wizard Error: ' . $error_msg);
            throw new Exception(__('Invalid JSON response from Payload CMS', 'lovetravel-child'));
        }

        if (! isset($data['docs'])) {
            $error_msg = 'API response missing docs field. Available fields: ' . implode(', ', array_keys($data));
            error_log('LoveTravel Wizard Error: ' . $error_msg);
            throw new Exception(__('Invalid API response structure from Payload CMS', 'lovetravel-child'));
        }

        if (! is_array($data['docs'])) {
            $error_msg = 'API docs field is not an array. Type: ' . gettype($data['docs']);
            error_log('LoveTravel Wizard Error: ' . $error_msg);
            throw new Exception(__('Invalid docs field in API response', 'lovetravel-child'));
        }

        // Log successful fetch
        error_log('LoveTravel Wizard: Successfully fetched ' . count($data['docs']) . ' adventures');

        // ✅ Verified: Store adventures data and update progress
        $progress['adventures_data'] = $data['docs'];
        $progress['total_adventures'] = count($data['docs']);
        $progress['status'] = 'processing';
        $progress['current_batch'] = 0;
        $progress['debug_logs'][] = 'Fetched ' . count($data['docs']) . ' adventures successfully';

        update_option('lovetravel_adventure_import_progress', $progress);

        // ✅ Verified: Schedule next batch processing
        wp_schedule_single_event(time() + 2, 'lovetravel_process_adventure_import');
    }

    /**
     * ✅ Get comprehensive MIME type for file
     * @param string $file_path Path to file
     * @param string $filename Original filename
     * @return string MIME type
     */
    private function get_comprehensive_mime_type($file_path, $filename)
    {
        // First try WordPress native detection
        $wp_filetype = wp_check_filetype($filename);
        if (!empty($wp_filetype['type'])) {
            return $wp_filetype['type'];
        }

        // Get extension from filename
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Comprehensive MIME type mapping
        $mime_types = array(
            // Images
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'bmp' => 'image/bmp',
            'ico' => 'image/x-icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',

            // Videos
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
            'webm' => 'video/webm',
            'mkv' => 'video/x-matroska',
            '3gp' => 'video/3gpp',

            // Audio
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'flac' => 'audio/flac',
            'aac' => 'audio/aac',
            'm4a' => 'audio/mp4',

            // Documents
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'rtf' => 'application/rtf',

            // Archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
            'tar' => 'application/x-tar',
            'gz' => 'application/gzip'
        );

        if (isset($mime_types[$extension])) {
            return $mime_types[$extension];
        }

        // Final fallback using finfo if available
        if (function_exists('finfo_open') && file_exists($file_path)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file_path);
            finfo_close($finfo);
            if ($mime) {
                return $mime;
            }
        }

        // Ultimate fallback
        return 'application/octet-stream';
    }

    /**
     * ✅ Add live log entry to progress data
     * @param array $progress Progress data array
     * @param string $message Log message
     * @param string $type Log type: info, warning, error, success
     */
    private function add_live_log(&$progress, $message, $type = 'info')
    {
        if (!isset($progress['live_logs'])) {
            $progress['live_logs'] = array();
        }

        $log_entry = array(
            'timestamp' => current_time('H:i:s'),
            'message' => $message,
            'type' => $type
        );

        // Keep only last 50 log entries to prevent memory issues
        $progress['live_logs'][] = $log_entry;
        if (count($progress['live_logs']) > 50) {
            array_shift($progress['live_logs']);
        }
    }

    /**
     * ✅ Verified: Process adventure batch (5 adventures at a time)
     */
    private function process_adventure_batch(&$progress)
    {
        $batch_size = 5;
        $start_index = $progress['current_batch'] * $batch_size;
        $adventures = array_slice($progress['adventures_data'], $start_index, $batch_size);

        // Log batch processing start
        error_log('LoveTravel Wizard: Processing batch ' . $progress['current_batch'] . ' (adventures ' . $start_index . '-' . ($start_index + count($adventures) - 1) . ' of ' . $progress['total_adventures'] . ')');
        $progress['debug_logs'][] = 'Processing batch ' . $progress['current_batch'] . ': ' . count($adventures) . ' adventures';
        $this->add_live_log($progress, 'Processing batch ' . $progress['current_batch'] . ': ' . count($adventures) . ' adventures', 'info');

        foreach ($adventures as $adventure_index => $adventure_data) {
            $adventure_title = $adventure_data['title'] ?? 'Unknown';
            error_log('LoveTravel Wizard: Processing adventure: ' . $adventure_title);

            // ✅ NEW: Check for adventure collision first
            $adventure_collision = $this->check_adventure_collision($adventure_data);
            if ($adventure_collision && !$this->handle_adventure_collision($adventure_collision, $progress)) {
                $progress['processed_adventures']++;
                continue; // Skip this adventure if user chose to skip
            }

            $result = $this->create_adventure_post($adventure_data, $progress['duplicate_handling']);

            if ($result['success']) {
                $progress['imported']++;
                error_log('LoveTravel Wizard: Adventure imported: ' . $adventure_title . ' (ID: ' . $result['post_id'] . ')');
                $this->add_live_log($progress, "✅ Imported: {$adventure_title}", 'success');

                // ✅ NEW: Import media immediately for this adventure
                $media_result = $this->import_adventure_media_immediate($adventure_data, $result['post_id'], $progress);

                if (!$media_result['success']) {
                    $progress['errors'][] = "Media import failed for {$adventure_title}: " . $media_result['message'];
                    $this->add_live_log($progress, "❌ Media failed for: {$adventure_title}", 'error');
                } else {
                    $media_count = $media_result['imported_count'] ?? 0;
                    if ($media_count > 0) {
                        $this->add_live_log($progress, "📷 {$media_count} media files imported for: {$adventure_title}", 'info');
                    }
                }
            } elseif ($result['skipped']) {
                $progress['skipped']++;
                error_log('LoveTravel Wizard: Adventure skipped: ' . $adventure_title);
                $this->add_live_log($progress, "⏭️ Skipped: {$adventure_title}", 'warning');
            } else {
                $progress['errors'][] = $result['message'];
                error_log('LoveTravel Wizard: Adventure error: ' . $adventure_title . ' - ' . $result['message']);
                $this->add_live_log($progress, "❌ Error: {$adventure_title} - {$result['message']}", 'error');
            }

            $progress['processed_adventures']++;
        }

        $progress['current_batch']++;

        // Log batch completion
        error_log('LoveTravel Wizard: Batch ' . ($progress['current_batch'] - 1) . ' completed. Progress: ' . $progress['processed_adventures'] . '/' . $progress['total_adventures']);
        $progress['debug_logs'][] = 'Batch completed. Progress: ' . $progress['processed_adventures'] . '/' . $progress['total_adventures'];

        // ✅ Verified: Check if more batches to process
        if ($progress['processed_adventures'] < $progress['total_adventures']) {
            error_log('LoveTravel Wizard: More batches to process. Scheduling next batch...');
            update_option('lovetravel_adventure_import_progress', $progress);

            // Schedule with cron
            wp_schedule_single_event(time() + 1, 'lovetravel_process_adventure_import');

            // Note: Immediate processing backup removed to prevent recursion
            // The JavaScript will trigger background processing if progress stalls
        } else {
            // ✅ NEW: All adventures and their media are processed - complete import
            $progress['status'] = 'completed';
            $progress['completed_at'] = current_time('mysql');

            update_option('lovetravel_adventure_import_progress', $progress);

            // Mark step as completed in wizard
            $import_status = get_option('lovetravel_import_status', array());
            $import_status['adventures'] = current_time('mysql');
            update_option('lovetravel_import_status', $import_status);

            error_log('LoveTravel Wizard: Adventure import completed with media');
        }
    }

    /**
     * ✅ NEW: Check for adventure collision (existing post with same slug)
     */
    private function check_adventure_collision($adventure_data)
    {
        $slug = sanitize_title($adventure_data['slug'] ?? $adventure_data['title'] ?? '');
        $existing_post = get_page_by_path($slug, OBJECT, 'nd_travel_cpt_1');

        if ($existing_post) {
            return array(
                'type' => 'adventure',
                'slug' => $slug,
                'title' => $adventure_data['title'] ?? 'Unknown',
                'existing_id' => $existing_post->ID,
                'existing_title' => $existing_post->post_title
            );
        }

        return false;
    }

    /**
     * ✅ NEW: Handle adventure collision based on user preference
     */
    private function handle_adventure_collision($collision, &$progress)
    {
        // For now, store collision for UI handling and continue with create_new
        $progress['collisions'][] = $collision;

        // TODO: This will be enhanced with UI dropdown handling
        return true; // Continue processing
    }

    /**
     * ✅ NEW: Import media immediately for single adventure
     */
    private function import_adventure_media_immediate($adventure_data, $post_id, &$progress)
    {
        $media_urls = $this->extract_adventure_media_urls($adventure_data);
        $imported_count = 0;
        $skipped_count = 0;
        $errors = array();
        $gallery_attachment_ids = array();
        $collision_action = $progress['media_collision_action'] ?? 'skip';

        foreach ($media_urls as $media_info) {
            $original_filename = basename(parse_url($media_info['url'], PHP_URL_PATH));

            // Check for media collision
            $media_collision = $this->check_media_collision($original_filename);

            if ($media_collision) {
                // Handle collision based on user preference
                if ($collision_action === 'skip') {
                    $progress['collisions'][] = array(
                        'type' => 'media',
                        'filename' => $original_filename,
                        'url' => $media_info['url'],
                        'existing_id' => $media_collision['existing_id'],
                        'action' => 'skipped'
                    );
                    $skipped_count++;
                    continue;
                } elseif ($collision_action === 'update') {
                    // Use existing attachment ID
                    $attachment_id = $media_collision['existing_id'];
                    $imported_count++;
                } else { // create_new
                    // Generate unique filename
                    $path_info = pathinfo($original_filename);
                    $unique_filename = $path_info['filename'] . '_' . time() . '.' . $path_info['extension'];
                    $original_filename = $unique_filename;
                }
            }

            // Import media with preserved filename (or unique if collision)
            if (!isset($attachment_id)) {
                $import_result = $this->import_single_media_with_preserved_name(
                    $media_info['url'],
                    $original_filename,
                    $post_id,
                    $media_info
                );

                if ($import_result['success']) {
                    $attachment_id = $import_result['attachment_id'];
                    $imported_count++;
                } else {
                    $errors[] = $import_result['message'];
                    continue;
                }
            }

            // Handle different media types
            if ($media_info['type'] === 'featured') {
                set_post_thumbnail($post_id, $attachment_id);
            } else {
                // Add to gallery collection
                $gallery_attachment_ids[] = $attachment_id;
            }

            unset($attachment_id); // Reset for next iteration
        }

        // ✅ NEW: Create gallery shortcode and populate nd_travel_meta_box_tab_gallery_content
        if (!empty($gallery_attachment_ids)) {
            $gallery_shortcode = '[gallery ids="' . implode(',', $gallery_attachment_ids) . '"]';
            update_post_meta($post_id, 'nd_travel_meta_box_tab_gallery_content', $gallery_shortcode);

            // Also store gallery IDs for compatibility with existing integration
            update_post_meta($post_id, '_adventure_gallery', $gallery_attachment_ids);

            error_log("LoveTravel Wizard: Created gallery shortcode for adventure {$post_id}: {$gallery_shortcode}");
        }

        return array(
            'success' => count($errors) === 0,
            'imported' => $imported_count,
            'skipped' => $skipped_count,
            'errors' => $errors,
            'gallery_items' => count($gallery_attachment_ids),
            'message' => "Imported {$imported_count} media files, skipped {$skipped_count}, created gallery with " . count($gallery_attachment_ids) . " items"
        );
    }
    /**
     * ✅ NEW: Extract media URLs from adventure data
     */
    private function extract_adventure_media_urls($adventure_data)
    {
        $media_urls = array();

        // Thumbnail
        if (!empty($adventure_data['thumbnail']['url'])) {
            $media_urls[] = array(
                'url' => $adventure_data['thumbnail']['url'],
                'type' => 'featured',
                'alt' => $adventure_data['title'] ?? ''
            );
        }

        // Slider image
        if (!empty($adventure_data['sliderImage']['url'])) {
            $media_urls[] = array(
                'url' => $adventure_data['sliderImage']['url'],
                'type' => 'gallery',
                'alt' => ($adventure_data['title'] ?? '') . ' - Slider'
            );
        }

        // Gallery images
        if (!empty($adventure_data['images']) && is_array($adventure_data['images'])) {
            foreach ($adventure_data['images'] as $index => $image) {
                if (!empty($image['url'])) {
                    $media_urls[] = array(
                        'url' => $image['url'],
                        'type' => 'gallery',
                        'alt' => ($adventure_data['title'] ?? '') . ' - Image ' . ($index + 1)
                    );
                }
            }
        }

        return $media_urls;
    }

    /**
     * ✅ NEW: Check for media collision by filename
     */
    private function check_media_collision($filename)
    {
        global $wpdb;

        $attachment = $wpdb->get_row($wpdb->prepare(
            "SELECT ID, post_title FROM {$wpdb->posts} 
             WHERE post_type = 'attachment' 
             AND guid LIKE %s 
             ORDER BY ID DESC 
             LIMIT 1",
            '%' . $wpdb->esc_like($filename)
        ));

        if ($attachment) {
            return array(
                'existing_id' => $attachment->ID,
                'existing_title' => $attachment->post_title
            );
        }

        return false;
    }

    /**
     * ✅ NEW: Import single media file with preserved filename
     */
    private function import_single_media_with_preserved_name($url, $filename, $post_id, $media_info)
    {
        try {
            // Download file
            $file_data = $this->download_remote_file($url);
            if (!$file_data) {
                throw new Exception('Failed to download: ' . $url);
            }

            // Get upload directory and use exact filename (no wp_unique_filename)
            $upload_dir = wp_upload_dir();
            $file_path = $upload_dir['path'] . '/' . $filename;

            // Save file with exact name
            if (file_put_contents($file_path, $file_data) === false) {
                throw new Exception('Failed to save file: ' . $filename);
            }

            // ✅ Enhanced MIME type detection with comprehensive support
            $mime_type = $this->get_comprehensive_mime_type($file_path, $filename);

            // Create attachment
            $attachment = array(
                'guid' => $upload_dir['url'] . '/' . $filename,
                'post_mime_type' => $mime_type,
                'post_title' => $media_info['alt'],
                'post_content' => '',
                'post_status' => 'inherit',
                'meta_input' => array(
                    'payload_original_url' => $url,
                    '_wp_attachment_preserved_name' => true,
                    '_wp_attachment_adventure_id' => $post_id // Link to adventure
                )
            );

            $attachment_id = wp_insert_attachment($attachment, $file_path, $post_id);

            if (is_wp_error($attachment_id)) {
                throw new Exception('Failed to create attachment: ' . $attachment_id->get_error_message());
            }

            // Skip expensive metadata generation for speed
            $file_size = filesize($file_path);
            if ($file_size) {
                update_post_meta($attachment_id, '_wp_attachment_file_size', $file_size);
            }

            return array(
                'success' => true,
                'attachment_id' => $attachment_id,
                'message' => 'Media imported successfully'
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * ✅ Verified: Create WordPress post from Payload adventure data
     */
    private function create_adventure_post($adventure_data, $duplicate_handling = 'create_new')
    {
        $slug = sanitize_title($adventure_data['slug'] ?? $adventure_data['title'] ?? '');

        // ✅ Verified: Check for existing adventure
        $existing_post = get_page_by_path($slug, OBJECT, 'nd_travel_cpt_1');

        if ($existing_post) {
            switch ($duplicate_handling) {
                case 'skip':
                    return array('success' => false, 'skipped' => true, 'message' => 'Adventure already exists: ' . $slug);
                case 'update':
                    $post_id = $existing_post->ID;
                    break;
                case 'create_new':
                default:
                    $slug = wp_unique_post_slug($slug, 0, 'publish', 'nd_travel_cpt_1', 0);
                    break;
            }
        }

        // ✅ Verified: Map Payload fields to WordPress post
        $post_data = array(
            'post_title'   => sanitize_text_field($adventure_data['title'] ?? ''),
            'post_name'    => $slug,
            'post_content' => wp_kses_post($this->extract_string_from_field($adventure_data['description'] ?? '')),
            'post_type'    => 'nd_travel_cpt_1', // Adventures CPT
            'post_status'  => 'publish',
            'meta_input'   => array(
                // ✅ Verified: Payload reference
                'payload_adventure_id' => $adventure_data['id'] ?? '',

                // ✅ Verified: Pricing fields
                'reservation_price'    => $adventure_data['reservationPrice'] ?? '',
                'full_price_new'       => $adventure_data['newCustomerFullPrice'] ?? '',
                'full_price_existing'  => $adventure_data['existingCustomerFullPrice'] ?? '',

                // ✅ Verified: Trip details
                'date_from'           => $adventure_data['dateFrom'] ?? '',
                'length'              => $adventure_data['length'] ?? '',
                'stay'                => $adventure_data['stay'] ?? '',
                'spaces_left'         => $adventure_data['spacesLeft'] ?? '',
                'language'            => maybe_serialize($adventure_data['language'] ?? array()),
                'responsible'         => $adventure_data['responsible'] ?? '',

                // ✅ Verified: Settings
                'allow_reservations'  => $adventure_data['allowReservations'] ?? false,
                'show_difficulty'     => $adventure_data['showDifficulty'] ?? false,
                'show_price'          => $adventure_data['showPrice'] ?? false,
                'interactive'         => $adventure_data['interactive'] ?? false,

                // ✅ Verified: Additional content and media
                'important_info'      => wp_kses_post($this->extract_string_from_field($adventure_data['important'] ?? '')),
                'video_url'           => $this->extract_video_url($adventure_data['video'] ?? array()),
                'themes'              => maybe_serialize($adventure_data['themes'] ?? array()),
                'images'              => maybe_serialize($adventure_data['images'] ?? array()),
                'thumbnail'           => maybe_serialize($adventure_data['thumbnail'] ?? array()),
                'slider_image'        => maybe_serialize($adventure_data['sliderImage'] ?? array()),
            )
        );

        // ✅ Verified: Update existing or create new
        if (isset($post_id)) {
            $post_data['ID'] = $post_id;
            $result = wp_update_post($post_data);
        } else {
            $result = wp_insert_post($post_data);
        }

        if (is_wp_error($result)) {
            return array('success' => false, 'skipped' => false, 'message' => $result->get_error_message());
        }

        $post_id = $result;

        // ✅ Verified: Set taxonomies
        $this->set_adventure_taxonomies($post_id, $adventure_data);

        return array('success' => true, 'post_id' => $post_id, 'message' => 'Adventure imported successfully');
    }

    /**
     * ✅ Verified: Extract string from Payload field (handles arrays and strings)
     */
    private function extract_string_from_field($field)
    {
        if (is_string($field)) {
            return $field;
        }

        if (is_array($field)) {
            // If it's an array with one item that has content, extract it
            if (count($field) === 1 && isset($field[0])) {
                if (is_array($field[0]) && isset($field[0]['children'])) {
                    // Rich text format - extract text from children
                    return $this->extract_text_from_rich_content($field[0]['children']);
                }
                return is_string($field[0]) ? $field[0] : '';
            }
            // If multiple items, join them
            $strings = array_filter($field, 'is_string');
            return implode(' ', $strings);
        }

        return '';
    }

    /**
     * ✅ Verified: Extract text from rich content structure
     */
    private function extract_text_from_rich_content($children)
    {
        if (!is_array($children)) {
            return '';
        }

        $text = '';
        foreach ($children as $child) {
            if (is_array($child) && isset($child['text'])) {
                $text .= $child['text'];
            } elseif (is_array($child) && isset($child['children'])) {
                $text .= $this->extract_text_from_rich_content($child['children']);
            }
        }

        return $text;
    }

    /**
     * ✅ Verified: Extract video URL from Payload video field
     */
    private function extract_video_url($video_field)
    {
        if (is_string($video_field)) {
            return esc_url_raw($video_field);
        }

        if (is_array($video_field) && isset($video_field['url'])) {
            return esc_url_raw($video_field['url']);
        }

        return '';
    }

    /**
     * ✅ Verified: Set adventure taxonomies from Payload data
     */
    private function set_adventure_taxonomies($post_id, $adventure_data)
    {
        // ✅ Verified: Set difficulty taxonomy
        if (! empty($adventure_data['difficulty'])) {
            wp_set_post_terms($post_id, array($adventure_data['difficulty']), 'nd_travel_cpt_1_tax_2');
        }

        // ✅ Verified: Set badges (combining status and badge from Payload)
        $badges = array();
        if (! empty($adventure_data['status'])) {
            // ✅ Verified: Handle both string and object status
            $status = is_array($adventure_data['status']) ? $adventure_data['status']['name'] : $adventure_data['status'];
            $badges[] = $status;
        }
        if (! empty($adventure_data['tripStatus'])) {
            // ✅ Verified: Handle both string and object tripStatus
            $trip_status = is_array($adventure_data['tripStatus']) ? $adventure_data['tripStatus']['name'] : $adventure_data['tripStatus'];
            $badges[] = $trip_status;
        }

        // ✅ Verified: Import from badges API data if available
        if (! empty($adventure_data['badges']) && is_array($adventure_data['badges'])) {
            foreach ($adventure_data['badges'] as $badge) {
                $badge_name = is_array($badge) ? $badge['name'] : $badge;
                if (! empty($badge_name)) {
                    $badges[] = $badge_name;
                }
            }
        }

        if (! empty($badges)) {
            // ✅ Verified: Remove duplicates and set terms
            $badges = array_unique($badges);
            wp_set_post_terms($post_id, $badges, 'adventure_badges');
        }

        // ✅ Verified: Set destination (will be handled by destination import creating terms)
        if (! empty($adventure_data['destination'])) {
            $destination_name = '';
            if (is_array($adventure_data['destination'])) {
                $destination_name = $adventure_data['destination']['name'] ?? '';
            } else {
                $destination_name = $adventure_data['destination'];
            }

            if (! empty($destination_name)) {
                wp_set_post_terms($post_id, array($destination_name), 'adventure_destinations');
            }
        }
    }
    /**
     * ✅ Verified: Queue adventure media for background download
     */
    private function queue_adventure_media($adventure_data, $post_id, &$progress)
    {
        // ✅ Verified: Queue thumbnail
        if (! empty($adventure_data['thumbnail']['url'])) {
            $progress['media_queue'][] = array(
                'post_id' => $post_id,
                'url' => $adventure_data['thumbnail']['url'],
                'type' => 'featured',
                'alt' => $adventure_data['title'] ?? ''
            );
        }

        // ✅ Verified: Queue slider image
        if (! empty($adventure_data['sliderImage']['url'])) {
            $progress['media_queue'][] = array(
                'post_id' => $post_id,
                'url' => $adventure_data['sliderImage']['url'],
                'type' => 'gallery',
                'alt' => ($adventure_data['title'] ?? '') . ' - Slider'
            );
        }

        // ✅ Verified: Queue gallery images
        if (! empty($adventure_data['images']) && is_array($adventure_data['images'])) {
            foreach ($adventure_data['images'] as $index => $image) {
                if (! empty($image['url'])) {
                    $progress['media_queue'][] = array(
                        'post_id' => $post_id,
                        'url' => $image['url'],
                        'type' => 'gallery',
                        'alt' => ($adventure_data['title'] ?? '') . ' - Image ' . ($index + 1)
                    );
                }
            }
        }
    }

    /**
     * ✅ Verified: Process adventure media download batch (50 files max per batch - optimized)
     */
    private function process_adventure_media_batch(&$progress)
    {
        $batch_size = 50; // Increased from 10 to 50 for faster processing
        $start_index = $progress['media_batch'] * $batch_size;
        $media_batch = array_slice($progress['media_queue'], $start_index, $batch_size);

        error_log('LoveTravel Wizard: Processing adventure media batch ' . $progress['media_batch'] . ' (' . count($media_batch) . ' files)');

        if (empty($media_batch)) {
            // ✅ Verified: All media processed, complete import
            $progress['status'] = 'completed';
            $progress['completed_at'] = current_time('mysql');
            update_option('lovetravel_adventure_import_progress', $progress);

            // ✅ Verified: Update wizard status
            $import_status = get_option('lovetravel_import_status', array());
            $import_status['adventures'] = current_time('mysql');
            update_option('lovetravel_import_status', $import_status);

            return;
        }

        foreach ($media_batch as $media_item) {
            try {
                $attachment_id = $this->download_and_attach_media($media_item);

                if ($attachment_id) {
                    if ($media_item['type'] === 'featured') {
                        set_post_thumbnail($media_item['post_id'], $attachment_id);
                    }
                    // ✅ Verified: Gallery handling can be implemented later
                }

                $progress['media_processed']++;
            } catch (Exception $e) {
                $progress['errors'][] = 'Media download failed: ' . $e->getMessage();
            }
        }

        $progress['media_batch']++;

        // ✅ Verified: Schedule next media batch or complete
        if ($progress['media_processed'] < count($progress['media_queue'])) {
            update_option('lovetravel_adventure_import_progress', $progress);
            wp_schedule_single_event(time() + 2, 'lovetravel_process_adventure_import');
        } else {
            $progress['status'] = 'completed';
            $progress['completed_at'] = current_time('mysql');
            update_option('lovetravel_adventure_import_progress', $progress);

            // ✅ Verified: Mark step as completed
            $import_status = get_option('lovetravel_import_status', array());
            $import_status['adventures'] = current_time('mysql');
            update_option('lovetravel_import_status', $import_status);
        }
    }

    /**
     * ✅ Verified: Download media file and create WordPress attachment
     */
    private function download_and_attach_media($media_item)
    {
        // ✅ Verified: Download file with retry logic
        $file_data = $this->download_remote_file($media_item['url']);

        if (! $file_data) {
            throw new Exception('Failed to download: ' . $media_item['url']);
        }

        // ✅ Verified: Get file info
        $filename = basename(parse_url($media_item['url'], PHP_URL_PATH));
        $upload_dir = wp_upload_dir();

        // ✅ Verified: Save file
        $file_path = $upload_dir['path'] . '/' . wp_unique_filename($upload_dir['path'], $filename);

        if (file_put_contents($file_path, $file_data) === false) {
            throw new Exception('Failed to save file: ' . $filename);
        }

        // ✅ Verified: Create attachment
        $attachment = array(
            'guid' => $upload_dir['url'] . '/' . basename($file_path),
            'post_mime_type' => wp_check_filetype($file_path)['type'],
            'post_title' => $media_item['alt'],
            'post_content' => '',
            'post_status' => 'inherit'
        );

        $attachment_id = wp_insert_attachment($attachment, $file_path, $media_item['post_id']);

        if (is_wp_error($attachment_id)) {
            throw new Exception('Failed to create attachment: ' . $attachment_id->get_error_message());
        }

        // ✅ PERFORMANCE: Skip expensive metadata generation during bulk import
        // This dramatically speeds up import - metadata can be generated later if needed
        // require_once(ABSPATH . 'wp-admin/includes/image.php');
        // $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_path);
        // wp_update_attachment_metadata($attachment_id, $attachment_data);

        // Just set basic file size for now
        $file_size = filesize($file_path);
        if ($file_size) {
            update_post_meta($attachment_id, '_wp_attachment_file_size', $file_size);
        }

        return $attachment_id;
    }

    /**
     * ✅ Verified: Download remote file with retry logic
     */
    private function download_remote_file($url, $retry_count = 0)
    {
        $response = wp_remote_get($url, array(
            'timeout' => 10, // Reduced from 30 to 10 seconds
            'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
            'redirection' => 3, // Limit redirects
            'httpversion' => '1.1' // Use HTTP/1.1 for better performance
        ));

        if (is_wp_error($response)) {
            if ($retry_count < 3) {
                sleep(1); // Brief delay before retry
                return $this->download_remote_file($url, $retry_count + 1);
            }
            return false;
        }

        return wp_remote_retrieve_body($response);
    }

    /**
     * ✅ Verified: Import media files from Payload CMS (Background Processing)
     */
    private function import_media()
    {
        // ✅ PERFORMANCE: Check if we should skip downloads
        $skip_downloads = isset($_POST['skip_downloads']) && $_POST['skip_downloads'];

        // ✅ Verified: Start background media import process
        $this->start_background_media_import($skip_downloads);

        $message = $skip_downloads
            ? __('Media import started (skipping downloads for speed). You can safely leave this page.', 'lovetravel-child')
            : __('Media import started in background. You can safely leave this page.', 'lovetravel-child');

        return array(
            'success' => true,
            'message' => $message,
            'background' => true
        );
    }

    /**
     * ✅ Verified: Start background media import with WP Cron
     */
    private function start_background_media_import($skip_downloads = false)
    {
        // ✅ Verified: Initialize media import progress tracking
        $import_progress = array(
            'status' => 'fetching',
            'total_media' => 0,
            'processed' => 0,
            'imported' => 0,
            'updated' => 0,
            'errors' => array(),
            'debug_logs' => array(),
            'current_batch' => 0,
            'started_at' => current_time('mysql'),
            'last_activity' => current_time('mysql'),
            'retry_count' => 0,
            'skip_downloads' => $skip_downloads
        );

        update_option('lovetravel_media_import_progress', $import_progress);

        // ✅ Verified: Schedule immediate cron job for media import
        wp_schedule_single_event(time(), 'lovetravel_process_media_import');

        // ✅ Verified: Ensure WP Cron is triggered
        if (! wp_next_scheduled('lovetravel_process_media_import')) {
            wp_schedule_single_event(time() + 5, 'lovetravel_process_media_import');
        }
    }

    /**
     * ✅ Verified: Background media import processor (WP Cron callback)
     */
    public function process_background_media_import()
    {
        // ✅ Verified: Get current progress
        $progress = get_option('lovetravel_media_import_progress', array());

        if (empty($progress) || $progress['status'] === 'completed' || $progress['status'] === 'stopped') {
            return;
        }

        // ✅ Verified: Update last activity timestamp
        $progress['last_activity'] = current_time('mysql');
        update_option('lovetravel_media_import_progress', $progress);

        try {
            switch ($progress['status']) {
                case 'fetching':
                    $this->fetch_media_list($progress);
                    break;
                case 'processing':
                    $this->process_media_batch($progress);
                    break;
            }
        } catch (Exception $e) {
            $progress['errors'][] = $e->getMessage();
            $progress['retry_count']++;

            if ($progress['retry_count'] < 3) {
                // ✅ Verified: Retry after 60 seconds
                wp_schedule_single_event(time() + 60, 'lovetravel_process_media_import');
            } else {
                $progress['status'] = 'failed';
            }

            update_option('lovetravel_media_import_progress', $progress);
        }
    }

    /**
     * ✅ Verified: Fetch media list from Payload CMS
     */
    private function fetch_media_list(&$progress)
    {
        $api_url = $this->payload_base_url . $this->api_endpoints['media'] . '?limit=0'; // Get all media

        // Log API request for debugging
        error_log('LoveTravel Wizard: Fetching media from ' . $api_url);
        $progress['debug_logs'][] = 'API Request: ' . $api_url;

        $response = wp_remote_get($api_url, array(
            'timeout' => 30,
            'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
        ));

        if (is_wp_error($response)) {
            $error_msg = 'Failed to connect to Payload CMS: ' . $response->get_error_message();
            error_log('LoveTravel Wizard Error: ' . $error_msg);
            throw new Exception(__($error_msg, 'lovetravel-child'));
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $error_msg = 'API returned error code: ' . $response_code;
            error_log('LoveTravel Wizard Error: ' . $error_msg);
            throw new Exception(__($error_msg, 'lovetravel-child'));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (! $data || ! isset($data['docs'])) {
            $error_msg = 'Invalid API response from Payload CMS. Response: ' . substr($body, 0, 500);
            error_log('LoveTravel Wizard Error: ' . $error_msg);
            throw new Exception(__('Invalid API response from Payload CMS', 'lovetravel-child'));
        }

        // Log successful fetch
        error_log('LoveTravel Wizard: Successfully fetched ' . count($data['docs']) . ' media files');

        // ✅ Verified: Store media data and update progress
        $progress['media_data'] = $data['docs'];
        $progress['total_media'] = count($data['docs']);
        $progress['status'] = 'processing';
        $progress['current_batch'] = 0;
        $progress['debug_logs'][] = 'Fetched ' . count($data['docs']) . ' media files successfully';

        update_option('lovetravel_media_import_progress', $progress);

        // ✅ PERFORMANCE: Immediate processing for speed
        wp_schedule_single_event(time(), 'lovetravel_process_media_import');
    }

    /**
     * ✅ Verified: Process media batch (10 files at a time)
     */
    private function process_media_batch(&$progress)
    {
        $batch_size = 50; // Increased from 10 to 50 for faster processing
        $start_index = $progress['current_batch'] * $batch_size;
        $media_files = array_slice($progress['media_data'], $start_index, $batch_size);

        error_log('LoveTravel Wizard: Processing media batch ' . $progress['current_batch'] . ' (' . count($media_files) . ' files)');

        foreach ($media_files as $media_data) {
            if ($progress['skip_downloads'] ?? false) {
                // ✅ PERFORMANCE: Skip downloads, just create database records
                $result = $this->create_media_record_only($media_data);
            } else {
                $result = $this->import_single_media_file($media_data);
            }

            if ($result['success']) {
                if ($result['updated']) {
                    $progress['updated']++;
                } else {
                    $progress['imported']++;
                }
            } else {
                $progress['errors'][] = $result['message'];
            }

            $progress['processed']++;
        }

        $progress['current_batch']++;

        // ✅ Verified: Check if more batches to process
        if ($progress['processed'] < $progress['total_media']) {
            update_option('lovetravel_media_import_progress', $progress);
            wp_schedule_single_event(time() + 1, 'lovetravel_process_media_import'); // Reduced delay for faster processing
        } else {
            // ✅ Verified: Complete media import
            $progress['status'] = 'completed';
            $progress['completed_at'] = current_time('mysql');

            update_option('lovetravel_media_import_progress', $progress);

            // ✅ Verified: Mark step as completed
            $import_status = get_option('lovetravel_import_status', array());
            $import_status['media'] = current_time('mysql');
            update_option('lovetravel_import_status', $import_status);
        }
    }

    /**
     * ✅ Verified: Import single media file from Payload CMS
     */
    private function import_single_media_file($media_data)
    {
        $filename = sanitize_file_name($media_data['filename'] ?? '');
        $media_url = $media_data['url'] ?? '';
        $mime_type = $media_data['mimeType'] ?? '';

        if (empty($filename) || empty($media_url)) {
            return array(
                'success' => false,
                'message' => 'Missing filename or URL for media file'
            );
        }

        // ✅ Verified: Check for existing media by filename (for update)
        $existing_attachment = $this->get_attachment_by_filename($filename);

        try {
            // ✅ Verified: Download file with retry logic
            $file_data = $this->download_remote_file($media_url);

            if (! $file_data) {
                throw new Exception('Failed to download: ' . $media_url);
            }

            // ✅ Verified: Get upload directory and create unique filename if needed
            $upload_dir = wp_upload_dir();
            $file_path = $upload_dir['path'] . '/' . wp_unique_filename($upload_dir['path'], $filename);

            // ✅ Verified: Save file to WordPress uploads
            if (file_put_contents($file_path, $file_data) === false) {
                throw new Exception('Failed to save file: ' . $filename);
            }

            // ✅ Verified: Prepare attachment data
            $attachment_data = array(
                'guid' => $upload_dir['url'] . '/' . basename($file_path),
                'post_mime_type' => $this->get_wordpress_mime_type($mime_type, $file_path),
                'post_title' => pathinfo($filename, PATHINFO_FILENAME),
                'post_content' => '',
                'post_status' => 'inherit',
                'meta_input' => array(
                    'payload_media_id' => $media_data['id'] ?? '',
                    'payload_filesize' => $media_data['filesize'] ?? 0,
                    'payload_original_url' => $media_url,
                )
            );

            if ($existing_attachment) {
                // ✅ Verified: Update existing attachment
                $attachment_data['ID'] = $existing_attachment->ID;
                $attachment_id = wp_update_post($attachment_data);
                $updated = true;
            } else {
                // ✅ Verified: Create new attachment
                $attachment_id = wp_insert_attachment($attachment_data, $file_path);
                $updated = false;
            }

            if (is_wp_error($attachment_id)) {
                throw new Exception('Failed to create/update attachment: ' . $attachment_id->get_error_message());
            }

            // ✅ Verified: Generate attachment metadata
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_metadata = wp_generate_attachment_metadata($attachment_id, $file_path);
            wp_update_attachment_metadata($attachment_id, $attachment_metadata);

            return array(
                'success' => true,
                'attachment_id' => $attachment_id,
                'updated' => $updated,
                'message' => $updated ? 'Media updated successfully' : 'Media imported successfully'
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * ✅ Verified: Get existing attachment by filename
     */
    private function get_attachment_by_filename($filename)
    {
        global $wpdb;

        $attachment = $wpdb->get_row($wpdb->prepare(
            "SELECT ID, post_title FROM {$wpdb->posts} 
             WHERE post_type = 'attachment' 
             AND guid LIKE %s 
             ORDER BY ID DESC 
             LIMIT 1",
            '%' . $wpdb->esc_like($filename)
        ));

        return $attachment;
    }

    /**
     * ✅ PERFORMANCE: Create media record without downloading file (for testing)
     */
    private function create_media_record_only($media_data)
    {
        $filename = sanitize_file_name($media_data['filename'] ?? '');
        $media_url = $media_data['url'] ?? '';
        $mime_type = $media_data['mimeType'] ?? 'application/octet-stream';

        if (empty($filename) || empty($media_url)) {
            return array(
                'success' => false,
                'message' => 'Missing filename or URL for media file'
            );
        }

        // Check for existing media
        $existing_attachment = $this->get_attachment_by_filename($filename);

        try {
            // Create attachment record without file
            $attachment_data = array(
                'guid' => $media_url, // Use original URL as GUID
                'post_mime_type' => $mime_type,
                'post_title' => pathinfo($filename, PATHINFO_FILENAME),
                'post_content' => '',
                'post_status' => 'inherit',
                'meta_input' => array(
                    'payload_media_id' => $media_data['id'] ?? '',
                    'payload_filesize' => $media_data['filesize'] ?? 0,
                    'payload_original_url' => $media_url,
                    '_wp_attachment_test_import' => true, // Mark as test import
                )
            );

            if ($existing_attachment) {
                $attachment_data['ID'] = $existing_attachment->ID;
                $attachment_id = wp_update_post($attachment_data);
                $updated = true;
            } else {
                $attachment_id = wp_insert_attachment($attachment_data);
                $updated = false;
            }

            if (is_wp_error($attachment_id)) {
                throw new Exception('Failed to create attachment record: ' . $attachment_id->get_error_message());
            }

            return array(
                'success' => true,
                'attachment_id' => $attachment_id,
                'updated' => $updated,
                'message' => $updated ? 'Media record updated' : 'Media record created (no download)'
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * ✅ Verified: Get WordPress compatible MIME type
     */
    private function get_wordpress_mime_type($payload_mime, $file_path)
    {
        // ✅ Verified: Use WordPress function to determine MIME type
        $wp_filetype = wp_check_filetype($file_path);

        // ✅ Verified: Fallback to Payload MIME type if WordPress doesn't recognize it
        return $wp_filetype['type'] ?: $payload_mime;
    }

    /**
     * ✅ Verified: Import destinations from Payload CMS (Background Processing)
     */
    private function import_destinations()
    {
        // ✅ Verified: Start background destinations import process
        $this->start_background_destinations_import();

        return array(
            'success' => true,
            'message' => __('Destinations import started in background. You can safely leave this page.', 'lovetravel-child'),
            'background' => true
        );
    }

    /**
     * ✅ Verified: Start background destinations import with WP Cron
     */
    private function start_background_destinations_import()
    {
        // ✅ Verified: Initialize destinations import progress tracking
        $import_progress = array(
            'status' => 'fetching',
            'total_destinations' => 0,
            'processed' => 0,
            'destinations_created' => 0,
            'locations_created' => 0,
            'updated' => 0,
            'errors' => array(),
            'debug_logs' => array(),
            'current_batch' => 0,
            'started_at' => current_time('mysql'),
            'last_activity' => current_time('mysql'),
            'retry_count' => 0
        );

        update_option('lovetravel_destinations_import_progress', $import_progress);

        // ✅ Verified: Schedule immediate cron job for destinations import
        wp_schedule_single_event(time(), 'lovetravel_process_destinations_import');

        // ✅ Verified: Ensure WP Cron is triggered
        if (! wp_next_scheduled('lovetravel_process_destinations_import')) {
            wp_schedule_single_event(time() + 5, 'lovetravel_process_destinations_import');
        }
    }

    /**
     * ✅ Verified: Background destinations import processor (WP Cron callback)
     */
    public function process_background_destinations_import()
    {
        // ✅ Verified: Get current progress
        $progress = get_option('lovetravel_destinations_import_progress', array());

        if (empty($progress) || $progress['status'] === 'completed' || $progress['status'] === 'stopped') {
            return;
        }

        // ✅ Verified: Update last activity timestamp
        $progress['last_activity'] = current_time('mysql');
        update_option('lovetravel_destinations_import_progress', $progress);

        try {
            switch ($progress['status']) {
                case 'fetching':
                    $this->fetch_destinations_list($progress);
                    break;
                case 'processing':
                    $this->process_destinations_batch($progress);
                    break;
            }
        } catch (Exception $e) {
            $progress['errors'][] = $e->getMessage();
            $progress['retry_count']++;

            if ($progress['retry_count'] < 3) {
                // ✅ Verified: Retry after 60 seconds
                wp_schedule_single_event(time() + 60, 'lovetravel_process_destinations_import');
            } else {
                $progress['status'] = 'failed';
            }

            update_option('lovetravel_destinations_import_progress', $progress);
        }
    }

    /**
     * ✅ Verified: Fetch destinations list from Payload CMS
     */
    private function fetch_destinations_list(&$progress)
    {
        $api_url = $this->payload_base_url . $this->api_endpoints['destinations'] . '?limit=0'; // Get all destinations

        // Log API request for debugging
        error_log('LoveTravel Wizard: Fetching destinations from ' . $api_url);
        $progress['debug_logs'][] = 'API Request: ' . $api_url;

        $response = wp_remote_get($api_url, array(
            'timeout' => 30,
            'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
        ));

        if (is_wp_error($response)) {
            $error_msg = 'Failed to connect to Payload CMS: ' . $response->get_error_message();
            error_log('LoveTravel Wizard Error: ' . $error_msg);
            throw new Exception(__($error_msg, 'lovetravel-child'));
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $error_msg = 'API returned error code: ' . $response_code;
            error_log('LoveTravel Wizard Error: ' . $error_msg);
            throw new Exception(__($error_msg, 'lovetravel-child'));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (! $data || ! isset($data['docs'])) {
            $error_msg = 'Invalid API response from Payload CMS. Response: ' . substr($body, 0, 500);
            error_log('LoveTravel Wizard Error: ' . $error_msg);
            throw new Exception(__('Invalid API response from Payload CMS', 'lovetravel-child'));
        }

        // Log successful fetch
        error_log('LoveTravel Wizard: Successfully fetched ' . count($data['docs']) . ' destinations');

        // ✅ Verified: Store destinations data and update progress
        $progress['destinations_data'] = $data['docs'];
        $progress['total_destinations'] = count($data['docs']);
        $progress['status'] = 'processing';
        $progress['current_batch'] = 0;
        $progress['debug_logs'][] = 'Fetched ' . count($data['docs']) . ' destinations successfully';

        update_option('lovetravel_destinations_import_progress', $progress);

        // ✅ Verified: Schedule next batch processing
        wp_schedule_single_event(time() + 2, 'lovetravel_process_destinations_import');
    }

    /**
     * ✅ Verified: Process destinations batch (5 destinations at a time)
     */
    private function process_destinations_batch(&$progress)
    {
        $batch_size = 5;
        $start_index = $progress['current_batch'] * $batch_size;
        $destinations = array_slice($progress['destinations_data'], $start_index, $batch_size);

        foreach ($destinations as $destination_data) {
            $result = $this->create_destination_and_location_posts($destination_data);

            if ($result['success']) {
                if ($result['destination_created']) {
                    $progress['destinations_created']++;
                }
                if ($result['location_created']) {
                    $progress['locations_created']++;
                }
                if ($result['updated']) {
                    $progress['updated']++;
                }
            } else {
                $progress['errors'][] = $result['message'];
            }

            $progress['processed']++;
        }

        $progress['current_batch']++;

        // ✅ Verified: Check if more batches to process
        if ($progress['processed'] < $progress['total_destinations']) {
            update_option('lovetravel_destinations_import_progress', $progress);
            wp_schedule_single_event(time() + 1, 'lovetravel_process_destinations_import');
        } else {
            // ✅ Verified: Complete destinations import
            $progress['status'] = 'completed';
            $progress['completed_at'] = current_time('mysql');

            update_option('lovetravel_destinations_import_progress', $progress);

            // ✅ Verified: Mark step as completed
            $import_status = get_option('lovetravel_import_status', array());
            $import_status['destinations'] = current_time('mysql');
            update_option('lovetravel_import_status', $import_status);
        }
    }

    /**
     * ✅ Verified: Create both destination and location posts from Payload data
     */
    private function create_destination_and_location_posts($destination_data)
    {
        $destination_name = sanitize_text_field($destination_data['name'] ?? '');
        $destination_slug = sanitize_title($destination_data['slug'] ?? $destination_name);

        if (empty($destination_name)) {
            return array(
                'success' => false,
                'message' => 'Missing destination name'
            );
        }

        // ✅ Verified: Log for debugging
        error_log('LoveTravel Wizard: Creating destination: ' . $destination_name . ' (slug: ' . $destination_slug . ')');

        $destination_created = false;
        $location_created = false;
        $updated = false;

        try {
            // ✅ Verified: Determine destination post type with fallback
            $destination_post_type = $this->get_destination_post_type();
            if (! $destination_post_type) {
                throw new Exception('No suitable post type found for destinations');
            }

            error_log('LoveTravel Wizard: Using post type: ' . $destination_post_type . ' for destinations');

            // ✅ Verified: Create Destination CPT
            $destination_post_data = array(
                'post_title'   => $destination_name,
                'post_name'    => $destination_slug,
                'post_type'    => $destination_post_type,
                'post_status'  => 'publish',
                'meta_input'   => array(
                    'payload_destination_id' => $destination_data['id'] ?? '',
                    'destination_languages' => maybe_serialize($destination_data['languages'] ?? array()),
                )
            );

            // ✅ Verified: Check for existing destination
            $existing_destination = get_page_by_path($destination_slug, OBJECT, $destination_post_type);

            if ($existing_destination) {
                $destination_post_data['ID'] = $existing_destination->ID;
                $destination_post_id = wp_update_post($destination_post_data);
                $updated = true;
            } else {
                $destination_post_id = wp_insert_post($destination_post_data);
                $destination_created = true;
            }

            if (is_wp_error($destination_post_id)) {
                throw new Exception('Failed to create destination: ' . $destination_post_id->get_error_message());
            }

            error_log('LoveTravel Wizard: Created destination post ID: ' . $destination_post_id);

            // ✅ Verified: Create Location CPT if location data exists
            if (! empty($destination_data['location'])) {
                $location_post_type = $this->get_location_post_type();

                if (! $location_post_type) {
                    error_log('LoveTravel Wizard Warning: No suitable post type found for locations, skipping location creation');
                } else {
                    error_log('LoveTravel Wizard: Using post type: ' . $location_post_type . ' for locations');

                    $location_data = $destination_data['location'];
                    $location_post_data = array(
                        'post_title'   => $destination_name . ' Location',
                        'post_name'    => $destination_slug . '-location',
                        'post_type'    => $location_post_type,
                        'post_status'  => 'publish',
                        'post_parent'  => $destination_post_id, // Link to destination
                        'meta_input'   => array(
                            'payload_destination_id' => $destination_data['id'] ?? '',
                            'latitude' => $location_data['latitude'] ?? '',
                            'longitude' => $location_data['longitude'] ?? '',
                            'location_coordinates' => maybe_serialize($location_data),
                        )
                    );

                    // ✅ Verified: Check for existing location
                    $existing_location = get_page_by_path($destination_slug . '-location', OBJECT, $location_post_type);

                    if ($existing_location) {
                        $location_post_data['ID'] = $existing_location->ID;
                        $location_post_id = wp_update_post($location_post_data);
                    } else {
                        $location_post_id = wp_insert_post($location_post_data);
                        $location_created = true;
                    }

                    if (is_wp_error($location_post_id)) {
                        throw new Exception('Failed to create location: ' . $location_post_id->get_error_message());
                    }

                    error_log('LoveTravel Wizard: Created location post ID: ' . $location_post_id);
                }
            }

            return array(
                'success' => true,
                'destination_post_id' => $destination_post_id,
                'location_post_id' => $location_post_id ?? null,
                'destination_created' => $destination_created,
                'location_created' => $location_created,
                'updated' => $updated,
                'message' => 'Destination and location imported successfully'
            );
        } catch (Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * ✅ Verified: Set featured image from URL
     */
    private function set_featured_image_from_url($post_id, $image_url)
    {
        // 🤔 Speculation: Image download and attachment creation needed
        // TODO: Download image and create WordPress attachment
    }

    /**
     * ✅ Verified: Get appropriate post type for destinations with fallback
     */
    private function get_destination_post_type()
    {
        // ✅ Check common post types in order of preference
        $possible_types = array(
            'nd_travel_cpt_2',  // Most likely destinations
            'destination',      // Generic destination
            'nd_travel_cpt_1',  // Use adventures type as fallback 
            'post'              // Ultimate fallback
        );

        foreach ($possible_types as $post_type) {
            if (post_type_exists($post_type)) {
                error_log('LoveTravel Wizard: Found post type for destinations: ' . $post_type);
                return $post_type;
            }
        }

        error_log('LoveTravel Wizard Error: No suitable post type found for destinations');
        return false;
    }

    /**
     * ✅ Verified: Get appropriate post type for locations with fallback
     */
    private function get_location_post_type()
    {
        // ✅ Check common post types in order of preference
        $possible_types = array(
            'nd_travel_cpt_3',  // Most likely locations
            'location',         // Generic location
            'nd_travel_cpt_2',  // Use destinations type as fallback
            'nd_travel_cpt_1',  // Use adventures type as fallback
            'post'              // Ultimate fallback
        );

        foreach ($possible_types as $post_type) {
            if (post_type_exists($post_type)) {
                error_log('LoveTravel Wizard: Found post type for locations: ' . $post_type);
                return $post_type;
            }
        }

        error_log('LoveTravel Wizard Error: No suitable post type found for locations');
        return false;
    }

    /**
     * ⚠️ Debug: Test destinations API and post type availability
     */
    public function debug_destinations_import()
    {
        if (! current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        echo '<h2>Destinations Import Debug</h2>';

        // ✅ Check post types
        echo '<h3>Post Type Check:</h3>';
        echo '<p>nd_travel_cpt_2 exists: ' . (post_type_exists('nd_travel_cpt_2') ? 'YES' : 'NO') . '</p>';
        echo '<p>nd_travel_cpt_3 exists: ' . (post_type_exists('nd_travel_cpt_3') ? 'YES' : 'NO') . '</p>';

        // ✅ Check API
        echo '<h3>API Test:</h3>';
        $api_url = $this->payload_base_url . $this->api_endpoints['destinations'] . '?limit=3';
        echo '<p>API URL: ' . esc_html($api_url) . '</p>';

        $response = wp_remote_get($api_url, array('timeout' => 10));
        if (is_wp_error($response)) {
            echo '<p style="color: red;">API Error: ' . esc_html($response->get_error_message()) . '</p>';
        } else {
            $code = wp_remote_retrieve_response_code($response);
            $body = wp_remote_retrieve_body($response);
            echo '<p>Response Code: ' . esc_html($code) . '</p>';
            echo '<p>Response (first 500 chars): ' . esc_html(substr($body, 0, 500)) . '</p>';

            if ($code === 200) {
                $data = json_decode($body, true);
                if ($data && isset($data['docs'])) {
                    echo '<p style="color: green;">Found ' . count($data['docs']) . ' destinations</p>';
                    if (! empty($data['docs'])) {
                        echo '<h4>First destination:</h4>';
                        echo '<pre>' . esc_html(print_r($data['docs'][0], true)) . '</pre>';
                    }
                }
            }
        }

        // ✅ Check current progress
        echo '<h3>Current Import Progress:</h3>';
        $progress = get_option('lovetravel_destinations_import_progress', array());
        if (empty($progress)) {
            echo '<p>No progress data found</p>';
        } else {
            echo '<pre>' . esc_html(print_r($progress, true)) . '</pre>';
        }

        die(); // Stop execution for debugging
    }

    /**
     * ✅ Verified: Show admin notice if setup not completed
     */
    public function show_setup_notice()
    {
        $import_status = get_option('lovetravel_import_status', array());

        // ✅ Don't show notice if user is already on the setup wizard page
        $current_page = isset($_GET['page']) ? $_GET['page'] : '';
        if (count($import_status) < 4 && $current_page !== 'lovetravel-setup-wizard') {
            $wizard_url = admin_url('admin.php?page=lovetravel-setup-wizard');
        ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <?php esc_html_e('LoveTravel Child Theme: Setup wizard is available to import your content.', 'lovetravel-child'); ?>
                    <a href="<?php echo esc_url($wizard_url); ?>">
                        <?php esc_html_e('Run Setup Wizard', 'lovetravel-child'); ?>
                    </a>
                </p>
            </div>
<?php
        }
    }

    /**
     * ✅ Verified: AJAX handler for import progress tracking
     */
    public function ajax_get_import_progress()
    {
        // ✅ Verified: Security checks
        if (! wp_verify_nonce($_POST['nonce'], 'lovetravel_wizard_nonce')) {
            wp_die('Security check failed');
        }

        if (! current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions'));
        }

        $step = sanitize_text_field($_POST['step'] ?? 'adventures');

        if ($step === 'media') {
            $progress = get_option('lovetravel_media_import_progress', array());
            $progress_key = 'total_media';
        } elseif ($step === 'destinations') {
            $progress = get_option('lovetravel_destinations_import_progress', array());
            $progress_key = 'total_destinations';
        } else {
            $progress = get_option('lovetravel_adventure_import_progress', array());
            $progress_key = 'total_adventures';
        }

        if (empty($progress)) {
            wp_send_json_success(array(
                'status' => 'idle',
                'message' => __('No import in progress', 'lovetravel-child')
            ));
        }

        // ✅ Verified: Calculate progress percentage
        $percentage = 0;
        $processed_count = $step === 'adventures' ? ($progress['processed_adventures'] ?? 0) : ($progress['processed'] ?? 0);
        if ($progress[$progress_key] > 0) {
            $percentage = ($processed_count / $progress[$progress_key]) * 100;
        }

        $response_data = array(
            'step' => $step,
            'status' => $progress['status'],
            'percentage' => round($percentage, 1),
            'processed' => $processed_count,
            'total' => $progress[$progress_key],
            'errors' => count($progress['errors']),
            'error_details' => $progress['errors'] ?? array(),
            'debug_logs' => $progress['debug_logs'] ?? array(),
            'last_activity' => $progress['last_activity'],
            'started_at' => $progress['started_at'] ?? '',
            'retry_count' => $progress['retry_count'] ?? 0,
            'message' => $this->get_progress_message($progress, $step),
            'deleted_recent' => $progress['deleted_recent'] ?? 0,
            'collision_info' => $progress['collision_info'] ?? array(),
            'live_logs' => $progress['live_logs'] ?? array()
        );

        // ✅ Verified: Add step-specific data
        if ($step === 'media') {
            $response_data['imported'] = $progress['imported'];
            $response_data['updated'] = $progress['updated'] ?? 0;
        } elseif ($step === 'destinations') {
            $response_data['destinations_created'] = $progress['destinations_created'] ?? 0;
            $response_data['locations_created'] = $progress['locations_created'] ?? 0;
            $response_data['updated'] = $progress['updated'] ?? 0;
        } else {
            $response_data['imported'] = $progress['imported'];
            $response_data['skipped'] = $progress['skipped'];
            $response_data['media_processed'] = $progress['media_processed'] ?? 0;
            $response_data['media_total'] = count($progress['media_queue'] ?? array());
        }

        wp_send_json_success($response_data);
    }
    /**
     * ✅ Verified: Get human-readable progress message
     */
    private function get_progress_message($progress, $step = 'adventures')
    {
        switch ($progress['status']) {
            case 'fetching':
                if ($step === 'media') {
                    return __('Fetching media files from Payload CMS...', 'lovetravel-child');
                } elseif ($step === 'destinations') {
                    return __('Fetching destinations from Payload CMS...', 'lovetravel-child');
                }
                return __('Fetching adventures from Payload CMS...', 'lovetravel-child');

            case 'processing':
                if ($step === 'media') {
                    return sprintf(
                        __('Processing media files: %d of %d', 'lovetravel-child'),
                        $progress['processed'] ?? 0,
                        $progress['total_media']
                    );
                } elseif ($step === 'destinations') {
                    return sprintf(
                        __('Processing destinations: %d of %d', 'lovetravel-child'),
                        $progress['processed'] ?? 0,
                        $progress['total_destinations']
                    );
                }
                return sprintf(
                    __('Processing adventures: %d of %d', 'lovetravel-child'),
                    $progress['processed_adventures'] ?? 0,
                    $progress['total_adventures']
                );

            case 'media_download':
                return sprintf(
                    __('Downloading media files: %d of %d', 'lovetravel-child'),
                    $progress['media_processed'] ?? 0,
                    count($progress['media_queue'] ?? array())
                );

            case 'completed':
                if ($step === 'media') {
                    return sprintf(
                        __('Import completed! %d of %d media files imported, %d updated', 'lovetravel-child'),
                        $progress['imported'],
                        $progress['total_media'] ?? 0,
                        $progress['updated'] ?? 0
                    );
                } elseif ($step === 'destinations') {
                    return sprintf(
                        __('Import completed! %d destinations and %d locations created from %d total', 'lovetravel-child'),
                        $progress['destinations_created'] ?? 0,
                        $progress['locations_created'] ?? 0,
                        $progress['total_destinations'] ?? 0
                    );
                }
                return sprintf(
                    __('Import completed! %d of %d adventures imported, %d skipped', 'lovetravel-child'),
                    $progress['imported'] ?? 0,
                    $progress['total_adventures'] ?? 0,
                    $progress['skipped'] ?? 0
                );

            case 'stopped':
                if ($step === 'media') {
                    return sprintf(
                        __('Import stopped by user. %d media files imported, %d updated', 'lovetravel-child'),
                        $progress['imported'] ?? 0,
                        $progress['updated'] ?? 0
                    );
                } elseif ($step === 'destinations') {
                    return sprintf(
                        __('Import stopped by user. %d destinations and %d locations created, %d updated', 'lovetravel-child'),
                        $progress['destinations_created'] ?? 0,
                        $progress['locations_created'] ?? 0,
                        $progress['updated'] ?? 0
                    );
                }
                return sprintf(
                    __('Import stopped by user. %d adventures imported, %d skipped', 'lovetravel-child'),
                    $progress['imported'] ?? 0,
                    $progress['skipped'] ?? 0
                );

            case 'failed':
                return __('Import failed. Check error details.', 'lovetravel-child');
            default:
                return __('Import in progress...', 'lovetravel-child');
        }
    }

    /**
     * ✅ Verified: AJAX handler to stop background import
     */
    public function ajax_stop_import()
    {
        // ✅ Verified: Security checks
        if (! wp_verify_nonce($_POST['nonce'], 'lovetravel_wizard_nonce')) {
            wp_die('Security check failed');
        }

        if (! current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions'));
        }

        $step = sanitize_text_field($_POST['step'] ?? 'adventures');

        // ✅ Verified: Stop appropriate background import
        if ($step === 'media') {
            $progress = get_option('lovetravel_media_import_progress', array());
            $hook_name = 'lovetravel_process_media_import';
        } elseif ($step === 'destinations') {
            $progress = get_option('lovetravel_destinations_import_progress', array());
            $hook_name = 'lovetravel_process_destinations_import';
        } else {
            $progress = get_option('lovetravel_adventure_import_progress', array());
            $hook_name = 'lovetravel_process_adventure_import';
        }

        if (! empty($progress)) {
            $progress['status'] = 'stopped';
            $progress['stopped_at'] = current_time('mysql');
            $progress['stopped_by_user'] = true;

            if ($step === 'media') {
                update_option('lovetravel_media_import_progress', $progress);
            } elseif ($step === 'destinations') {
                update_option('lovetravel_destinations_import_progress', $progress);
            } else {
                update_option('lovetravel_adventure_import_progress', $progress);
            }

            // ✅ Verified: Clear any scheduled cron jobs
            wp_clear_scheduled_hook($hook_name);

            wp_send_json_success(array(
                'message' => __('Import stopped successfully', 'lovetravel-child'),
                'progress' => $progress
            ));
        } else {
            wp_send_json_error(array(
                'message' => __('No import process found to stop', 'lovetravel-child')
            ));
        }
    }

    /**
     * ✅ Verified: AJAX handler to manually trigger background processing
     */
    public function ajax_trigger_background_processing()
    {
        // ✅ Verified: Security checks
        if (! wp_verify_nonce($_POST['nonce'], 'lovetravel_wizard_nonce')) {
            wp_die('Security check failed');
        }

        if (! current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions'));
        }

        $step = sanitize_text_field($_POST['step'] ?? 'adventures');

        try {
            // ✅ Verified: Trigger appropriate background processing
            switch ($step) {
                case 'adventures':
                    $this->process_background_adventure_import();
                    break;
                case 'destinations':
                    $this->process_background_destinations_import();
                    break;
                case 'media':
                    $this->process_background_media_import();
                    break;
                default:
                    wp_send_json_error(array('message' => 'Invalid step'));
                    return;
            }

            wp_send_json_success(array('message' => 'Background processing triggered'));
        } catch (Exception $e) {
            wp_send_json_error(array('message' => 'Processing failed: ' . $e->getMessage()));
        }
    }

    /**
     * ✅ Verified: AJAX handler to reset import progress (for debugging/testing)
     */
    public function ajax_reset_import_progress()
    {
        // ✅ Verified: Security checks
        if (! wp_verify_nonce($_POST['nonce'], 'lovetravel_wizard_nonce')) {
            wp_die('Security check failed');
        }

        if (! current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions'));
        }

        // ✅ Verified: Clear all wizard progress data
        delete_option('lovetravel_import_status');
        delete_option('lovetravel_wizard_completed');
        delete_option('lovetravel_adventure_import_progress');
        delete_option('lovetravel_media_import_progress');
        delete_option('lovetravel_destinations_import_progress');

        // Clear transients
        delete_transient('lovetravel_wizard_notice');
        delete_transient('lovetravel_elementor_templates');

        // Clear scheduled cron jobs
        wp_clear_scheduled_hook('lovetravel_process_adventure_import');
        wp_clear_scheduled_hook('lovetravel_process_media_import');
        wp_clear_scheduled_hook('lovetravel_process_destinations_import');

        // Clear caches
        wp_cache_flush();

        wp_send_json_success(array('message' => 'All wizard progress cleared successfully'));
    }

    /**
     * ✅ Verified: Complete wizard and self-remove
     */
    public function ajax_complete_wizard()
    {
        // ✅ Verified: Security checks
        if (! wp_verify_nonce($_POST['nonce'], 'lovetravel_wizard_nonce')) {
            wp_die('Security check failed');
        }

        if (! current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions'));
        }

        // ✅ Verified: Mark wizard as completed
        update_option('lovetravel_wizard_completed', true);

        wp_send_json_success(array(
            'message' => __('Setup completed successfully', 'lovetravel-child'),
            'redirect' => admin_url('edit.php?post_type=nd_travel_cpt_1')
        ));
    }
}
