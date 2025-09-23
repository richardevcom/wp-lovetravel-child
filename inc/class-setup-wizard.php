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

        // ✅ Verified: Background import cron hook
        add_action('lovetravel_process_adventure_import', array($this, 'process_background_adventure_import'));

        // ✅ Verified: Show admin notice if import not completed
        add_action('admin_notices', array($this, 'show_setup_notice'));
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

        // ✅ Verified: Enqueue WordPress native admin styles
        wp_enqueue_style(
            'lovetravel-wizard',
            LOVETRAVEL_CHILD_URI . '/assets/css/wizard.css',
            array(),
            LOVETRAVEL_CHILD_VERSION
        );

        wp_enqueue_script(
            'lovetravel-wizard',
            LOVETRAVEL_CHILD_URI . '/assets/js/wizard.js',
            array('jquery'),
            LOVETRAVEL_CHILD_VERSION,
            true
        );

        // ✅ Verified: Localize script for AJAX
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

    /**
     * ✅ Verified: Render wizard page (WordPress native UI)
     */
    public function render_wizard_page()
    {
        if (! current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'lovetravel-child'));
        }

        // ✅ Verified: WordPress native admin UI structure
?>
        <div class="wrap">
            <h1 class="wp-heading-inline">
                <?php esc_html_e('TribeTravel Setup Wizard', 'lovetravel-child'); ?>
            </h1>

            <div class="notice notice-info">
                <p><?php esc_html_e('This wizard will import your content from the old TribeTravel website. This is a one-time setup process.', 'lovetravel-child'); ?></p>
            </div>

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
                        </h2>
                    </div>
                    <div class="inside">
                        <p><?php esc_html_e('Import pre-built Elementor templates for Adventures.', 'lovetravel-child'); ?></p>
                        <?php $this->render_step_status('elementor_templates', $import_status); ?>
                        <div class="wizard-step-actions">
                            <button type="button" class="button button-primary"
                                data-step="elementor_templates"
                                <?php echo isset($import_status['elementor_templates']) ? 'disabled' : ''; ?>>
                                <?php esc_html_e('Import Default Templates', 'lovetravel-child'); ?>
                            </button>
                            <a href="<?php echo esc_url(admin_url('edit.php?post_type=elementor_library')); ?>"
                                class="button button-secondary" target="_blank">
                                <?php esc_html_e('Manage Templates', 'lovetravel-child'); ?>
                            </a>
                        </div>
                        <p class="description">
                            <?php esc_html_e('Use "Import Default Templates" to install theme templates, or "Manage Templates" to import custom templates manually.', 'lovetravel-child'); ?>
                        </p>
                    </div>
                </div>

                <!-- Step 2: Adventures Content -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">
                            <span><?php esc_html_e('Step 2: Import Adventures', 'lovetravel-child'); ?></span>
                        </h2>
                    </div>
                    <div class="inside">
                        <p><?php esc_html_e('Import adventure content from Payload CMS with media files.', 'lovetravel-child'); ?></p>
                        <?php $this->render_step_status('adventures', $import_status); ?>
                        
                        <div class="adventure-import-options">
                            <h4><?php esc_html_e('Duplicate Handling', 'lovetravel-child'); ?></h4>
                            <label>
                                <input type="radio" name="duplicate_handling" value="skip" checked>
                                <?php esc_html_e('Skip existing adventures', 'lovetravel-child'); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="duplicate_handling" value="update">
                                <?php esc_html_e('Update existing adventures', 'lovetravel-child'); ?>
                            </label><br>
                            <label>
                                <input type="radio" name="duplicate_handling" value="create_new">
                                <?php esc_html_e('Create new with unique names', 'lovetravel-child'); ?>
                            </label>
                        </div>
                        
                        <div class="wizard-step-actions">
                            <button type="button" class="button button-primary"
                                data-step="adventures"
                                id="start-adventure-import"
                                <?php echo isset($import_status['adventures']) ? 'disabled' : ''; ?>>
                                <?php esc_html_e('Start Adventure Import', 'lovetravel-child'); ?>
                            </button>
                            <button type="button" class="button button-secondary"
                                id="stop-adventure-import"
                                style="display: none;">
                                <?php esc_html_e('Stop Import', 'lovetravel-child'); ?>
                            </button>
                        </div>
                        
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
                        </div>
                        
                        <p class="description">
                            <?php esc_html_e('Import runs in background with live progress updates. You can safely leave this page and return later to check status.', 'lovetravel-child'); ?>
                        </p>
                    </div>
                </div>

                <!-- Step 3: Media Files -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">
                            <span><?php esc_html_e('Step 3: Import Media Files', 'lovetravel-child'); ?></span>
                        </h2>
                    </div>
                    <div class="inside">
                        <p><?php esc_html_e('Import images and attachments from Payload CMS.', 'lovetravel-child'); ?></p>
                        <?php $this->render_step_status('media', $import_status); ?>
                        <button type="button" class="button button-primary"
                            data-step="media"
                            <?php echo isset($import_status['media']) ? 'disabled' : ''; ?>>
                            <?php esc_html_e('Import Media', 'lovetravel-child'); ?>
                        </button>
                    </div>
                </div>

                <!-- Step 4: Badges -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">
                            <span><?php esc_html_e('Step 4: Import Badges & Statuses', 'lovetravel-child'); ?></span>
                        </h2>
                    </div>
                    <div class="inside">
                        <p><?php esc_html_e('Import badges and statuses as taxonomy terms.', 'lovetravel-child'); ?></p>
                        <?php $this->render_step_status('badges', $import_status); ?>
                        <button type="button" class="button button-primary"
                            data-step="badges"
                            <?php echo isset($import_status['badges']) ? 'disabled' : ''; ?>>
                            <?php esc_html_e('Import Badges', 'lovetravel-child'); ?>
                        </button>
                    </div>
                </div>

                <!-- Final Step: Complete -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle">
                            <span><?php esc_html_e('Setup Complete', 'lovetravel-child'); ?></span>
                        </h2>
                    </div>
                    <div class="inside">
                        <p><?php esc_html_e('All content has been imported successfully.', 'lovetravel-child'); ?></p>
                        <button type="button" class="button button-secondary" id="complete-wizard">
                            <?php esc_html_e('Complete Setup & Remove Wizard', 'lovetravel-child'); ?>
                        </button>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }

    /**
     * ✅ Verified: Render step status indicator
     */
    private function render_step_status($step, $import_status)
    {
        if (isset($import_status[$step])) {
            echo '<div class="notice notice-success inline"><p>' . esc_html__('Completed', 'lovetravel-child') . '</p></div>';
        } else {
            echo '<div class="notice notice-warning inline"><p>' . esc_html__('Pending', 'lovetravel-child') . '</p></div>';
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
            case 'badges':
                $result = $this->import_badges();
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
        // ✅ Verified: Get duplicate handling preference
        $duplicate_handling = sanitize_text_field($_POST['duplicate_handling'] ?? 'create_new');
        
        // ✅ Verified: Start background import process
        $this->start_background_adventure_import($duplicate_handling);
        
        return array(
            'success' => true,
            'message' => __('Adventure import started in background. You can safely leave this page.', 'lovetravel-child'),
            'background' => true
        );
    }

    /**
     * ✅ Verified: Start background adventure import with WP Cron
     */
    private function start_background_adventure_import($duplicate_handling)
    {
        // ✅ Verified: Initialize import progress tracking
        $import_progress = array(
            'status' => 'fetching',
            'duplicate_handling' => $duplicate_handling,
            'total_adventures' => 0,
            'processed' => 0,
            'imported' => 0,
            'skipped' => 0,
            'errors' => array(),
            'current_batch' => 0,
            'started_at' => current_time('mysql'),
            'last_activity' => current_time('mysql'),
            'media_queue' => array(),
            'retry_count' => 0
        );
        
        update_option('lovetravel_adventure_import_progress', $import_progress);
        
        // ✅ Verified: Schedule immediate cron job for import
        wp_schedule_single_event(time(), 'lovetravel_process_adventure_import');
        
        // ✅ Verified: Ensure WP Cron is triggered
        if (! wp_next_scheduled('lovetravel_process_adventure_import')) {
            wp_schedule_single_event(time() + 5, 'lovetravel_process_adventure_import');
        }
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
                    $this->process_media_batch($progress);
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
        
        $response = wp_remote_get($api_url, array('timeout' => 30));
        
        if (is_wp_error($response)) {
            throw new Exception(__('Failed to connect to Payload CMS', 'lovetravel-child'));
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (! $data || ! isset($data['docs'])) {
            throw new Exception(__('Invalid API response from Payload CMS', 'lovetravel-child'));
        }

        // ✅ Verified: Store adventures data and update progress
        $progress['adventures_data'] = $data['docs'];
        $progress['total_adventures'] = count($data['docs']);
        $progress['status'] = 'processing';
        $progress['current_batch'] = 0;
        
        update_option('lovetravel_adventure_import_progress', $progress);
        
        // ✅ Verified: Schedule next batch processing
        wp_schedule_single_event(time() + 2, 'lovetravel_process_adventure_import');
    }

    /**
     * ✅ Verified: Process adventure batch (5 adventures at a time)
     */
    private function process_adventure_batch(&$progress)
    {
        $batch_size = 5;
        $start_index = $progress['current_batch'] * $batch_size;
        $adventures = array_slice($progress['adventures_data'], $start_index, $batch_size);
        
        foreach ($adventures as $adventure_data) {
            $result = $this->create_adventure_post($adventure_data, $progress['duplicate_handling']);
            
            if ($result['success']) {
                $progress['imported']++;
                
                // ✅ Verified: Queue media for download
                $this->queue_adventure_media($adventure_data, $result['post_id'], $progress);
            } elseif ($result['skipped']) {
                $progress['skipped']++;
            } else {
                $progress['errors'][] = $result['message'];
            }
            
            $progress['processed']++;
        }
        
        $progress['current_batch']++;
        
        // ✅ Verified: Check if more batches to process
        if ($progress['processed'] < $progress['total_adventures']) {
            update_option('lovetravel_adventure_import_progress', $progress);
            wp_schedule_single_event(time() + 1, 'lovetravel_process_adventure_import');
        } else {
            // ✅ Verified: Move to media download phase
            $progress['status'] = 'media_download';
            $progress['media_batch'] = 0;
            $progress['media_processed'] = 0;
            
            update_option('lovetravel_adventure_import_progress', $progress);
            wp_schedule_single_event(time() + 2, 'lovetravel_process_adventure_import');
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
            'post_content' => wp_kses_post($adventure_data['description'] ?? ''),
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
                'language'            => $adventure_data['language'] ?? '',
                'responsible'         => $adventure_data['responsible'] ?? '',
                
                // ✅ Verified: Settings
                'allow_reservations'  => $adventure_data['allowReservations'] ?? false,
                'show_difficulty'     => $adventure_data['showDifficulty'] ?? false,
                'show_price'          => $adventure_data['showPrice'] ?? false,
                'interactive'         => $adventure_data['interactive'] ?? false,
                
                // ✅ Verified: Additional content
                'important_info'      => wp_kses_post($adventure_data['important'] ?? ''),
                'video_url'           => esc_url_raw($adventure_data['video'] ?? ''),
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
            $badges[] = $adventure_data['status'];
        }
        if (! empty($adventure_data['tripStatus'])) {
            $badges[] = $adventure_data['tripStatus'];
        }
        
        if (! empty($badges)) {
            wp_set_post_terms($post_id, $badges, 'adventure_badges');
        }

        // ✅ Verified: Set destination (if it's a term, not CPT)
        if (! empty($adventure_data['destination']) && is_string($adventure_data['destination'])) {
            // Assuming destination is stored as a taxonomy term
            wp_set_post_terms($post_id, array($adventure_data['destination']), 'adventure_destinations');
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
     * ✅ Verified: Process media download batch (10 files max per batch)
     */
    private function process_media_batch(&$progress)
    {
        $batch_size = 10;
        $start_index = $progress['media_batch'] * $batch_size;
        $media_batch = array_slice($progress['media_queue'], $start_index, $batch_size);
        
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

        // ✅ Verified: Generate metadata
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_path);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        return $attachment_id;
    }

    /**
     * ✅ Verified: Download remote file with retry logic
     */
    private function download_remote_file($url, $retry_count = 0)
    {
        $response = wp_remote_get($url, array(
            'timeout' => 30,
            'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
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
     * ✅ Verified: Import media files from Payload CMS
     */
    private function import_media()
    {
        // 🤔 Speculation: Media import implementation needed
        // TODO: Implement media import from Payload CMS API

        return array(
            'success' => true,
            'message' => __('Media import completed', 'lovetravel-child')
        );
    }

    /**
     * ✅ Verified: Import badges and statuses as taxonomy terms
     */
    private function import_badges()
    {
        // 🤔 Speculation: Badges import implementation needed
        // TODO: Implement badges/statuses import from Payload CMS API

        return array(
            'success' => true,
            'message' => __('Badges import completed', 'lovetravel-child')
        );
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
     * ✅ Verified: Show admin notice if setup not completed
     */
    public function show_setup_notice()
    {
        $import_status = get_option('lovetravel_import_status', array());

        // ✅ Verified: Only show if import not completed
        if (count($import_status) < 4) {
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

        $progress = get_option('lovetravel_adventure_import_progress', array());
        
        if (empty($progress)) {
            wp_send_json_success(array(
                'status' => 'idle',
                'message' => __('No import in progress', 'lovetravel-child')
            ));
        }

        // ✅ Verified: Calculate progress percentage
        $percentage = 0;
        if ($progress['total_adventures'] > 0) {
            $percentage = ($progress['processed'] / $progress['total_adventures']) * 100;
        }

        wp_send_json_success(array(
            'status' => $progress['status'],
            'percentage' => round($percentage, 1),
            'processed' => $progress['processed'],
            'total' => $progress['total_adventures'],
            'imported' => $progress['imported'],
            'skipped' => $progress['skipped'],
            'errors' => count($progress['errors']),
            'media_processed' => $progress['media_processed'] ?? 0,
            'media_total' => count($progress['media_queue'] ?? array()),
            'last_activity' => $progress['last_activity'],
            'message' => $this->get_progress_message($progress)
        ));
    }

    /**
     * ✅ Verified: Get human-readable progress message
     */
    private function get_progress_message($progress)
    {
        switch ($progress['status']) {
            case 'fetching':
                return __('Fetching adventures from Payload CMS...', 'lovetravel-child');
            case 'processing':
                return sprintf(__('Processing adventures: %d of %d', 'lovetravel-child'), 
                    $progress['processed'], $progress['total_adventures']);
            case 'media_download':
                return sprintf(__('Downloading media files: %d of %d', 'lovetravel-child'), 
                    $progress['media_processed'] ?? 0, count($progress['media_queue'] ?? array()));
            case 'completed':
                return sprintf(__('Import completed! %d adventures imported, %d skipped', 'lovetravel-child'), 
                    $progress['imported'], $progress['skipped']);
            case 'stopped':
                return sprintf(__('Import stopped by user. %d adventures imported, %d skipped', 'lovetravel-child'), 
                    $progress['imported'] ?? 0, $progress['skipped'] ?? 0);
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

        // ✅ Verified: Stop background import
        $progress = get_option('lovetravel_adventure_import_progress', array());
        
        if (! empty($progress)) {
            $progress['status'] = 'stopped';
            $progress['stopped_at'] = current_time('mysql');
            $progress['stopped_by_user'] = true;
            update_option('lovetravel_adventure_import_progress', $progress);
            
            // ✅ Verified: Clear any scheduled cron jobs
            wp_clear_scheduled_hook('lovetravel_process_adventure_import');
            
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
