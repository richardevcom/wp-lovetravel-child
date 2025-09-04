<?php

/**
 * Payload CMS v2 Media Import Tool - Fixed Version 4.0
 *
 * Clean, modular WordPress media import tool for Payload CMS v2 API
 * Features: Proper state management, file-based logging, background processing
 * 
 * @package LoveTravel_Child
 * @version 4.0.0-Fixed
 * @author richardevcom
 * @since 4.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Import Logger - File-based logging
 */
class PayloadImportLogger
{
    private $log_file;
    private $session_id;

    public function __construct()
    {
        $upload_dir = wp_upload_dir();
        $this->log_file = $upload_dir['basedir'] . '/import.log';
        $this->session_id = 'PL_' . date('Ymd_His') . '_' . substr(md5(microtime()), 0, 8);
    }

    public function clear_log()
    {
        if (file_exists($this->log_file)) {
            file_put_contents($this->log_file, '');
        }
    }

    public function log($level, $category, $message, $data = [])
    {
        $timestamp = date('Y-m-d H:i:s');
        $memory = round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB';

        $log_entry = sprintf(
            "[%s] [%s] [%s] [%s] %s %s\n",
            $timestamp,
            $this->session_id,
            $level,
            $category,
            $message,
            !empty($data) ? json_encode($data) : ''
        );

        // Write to import log file
        file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX);

        // Only log warnings and errors to debug.log
        if (in_array($level, ['WARNING', 'ERROR', 'CRITICAL'])) {
            error_log("[PayloadImporter] {$level}: {$message}");
        }
    }

    public function get_recent_logs($lines = 50)
    {
        if (!file_exists($this->log_file)) {
            return [];
        }

        $log_content = file_get_contents($this->log_file);
        $log_lines = explode("\n", trim($log_content));

        return array_slice($log_lines, -$lines);
    }
}

/**
 * State Manager - Single source of truth for job state
 */
class PayloadStateManager
{
    private $logger;
    private $state_option = 'payload_import_job_state';

    public function __construct(PayloadImportLogger $logger)
    {
        $this->logger = $logger;
    }

    public function get_state()
    {
        $state = get_option($this->state_option, null);
        return $state ? $state : $this->get_default_state();
    }

    public function set_state($state)
    {
        $state['updated_at'] = time();
        update_option($this->state_option, $state);
    }

    public function clear_state()
    {
        delete_option($this->state_option);
        delete_transient('payload_import_stop_requested');
        delete_option('payload_import_last_history');

        // Clear scheduled events
        wp_clear_scheduled_hook('payload_import_background_process');

        $this->logger->log('INFO', 'STATE', 'All state cleared and cron events cancelled');
    }

    public function is_running()
    {
        $state = $this->get_state();
        return $state['status'] === 'running';
    }

    public function request_stop()
    {
        $state = $this->get_state();
        if ($state['status'] === 'running') {
            $state['status'] = 'stopping';
            $this->set_state($state);

            // Set stop flag for background processes
            set_transient('payload_import_stop_requested', true, HOUR_IN_SECONDS);

            // Clear scheduled events
            wp_clear_scheduled_hook('payload_import_background_process');

            $this->logger->log('INFO', 'STATE', 'Stop requested - status changed to stopping');
        }
    }

    public function is_stop_requested()
    {
        return get_transient('payload_import_stop_requested') !== false;
    }

    public function finalize_stop()
    {
        $state = $this->get_state();
        if ($state['status'] === 'stopping') {
            $state['status'] = 'stopped';
            $state['stopped_at'] = time();
            $this->set_state($state);

            delete_transient('payload_import_stop_requested');
            $this->logger->log('INFO', 'STATE', 'Import finalized as stopped');
        }
    }

    private function get_default_state()
    {
        return [
            'job_id' => null,
            'status' => 'idle',
            'started_at' => null,
            'updated_at' => time(),
            'total' => 0,
            'imported' => 0,
            'skipped' => 0,
            'failed' => 0,
            'current_index' => 0,
            'current_file' => null,
            'last_error' => null,
            'options' => []
        ];
    }
}

/**
 * API Client - Handles Payload CMS communication
 */
class PayloadApiClient
{
    private $base_url;
    private $logger;
    private $cached_count = null;
    private $cache_time = null;

    public function __construct($base_url, PayloadImportLogger $logger)
    {
        $this->base_url = rtrim($base_url, '/');
        $this->logger = $logger;
    }

    public function get_media_count($force_refresh = false)
    {
        // Use cache if available and less than 30 minutes old
        if (!$force_refresh && $this->cached_count && $this->cache_time && (time() - $this->cache_time) < 1800) {
            return $this->cached_count;
        }

        $url = $this->base_url . '/api/media/count?locale=undefined&draft=false&depth=1&sort=createdAt';

        $response = wp_remote_get($url, ['timeout' => 30]);

        if (is_wp_error($response)) {
            $this->logger->log('ERROR', 'API', 'Failed to fetch media count', ['error' => $response->get_error_message()]);
            return $this->cached_count ?: 0;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->log('ERROR', 'API', 'Invalid JSON response from media count API');
            return $this->cached_count ?: 0;
        }

        $count = isset($data['totalDocs']) ? (int) $data['totalDocs'] : 0;

        // Cache the result
        $this->cached_count = $count;
        $this->cache_time = time();

        return $count;
    }

    public function get_media_batch($page = 1, $limit = 100)
    {
        $url = $this->base_url . '/api/media';
        $url = add_query_arg([
            'locale' => 'undefined',
            'draft' => 'false',
            'depth' => '1',
            'sort' => 'createdAt',
            'page' => $page,
            'limit' => $limit
        ], $url);

        $response = wp_remote_get($url, ['timeout' => 60]);

        if (is_wp_error($response)) {
            throw new Exception('Failed to fetch media batch: ' . $response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from media API');
        }

        return [
            'docs' => $data['docs'] ?? [],
            'hasNextPage' => $data['hasNextPage'] ?? false,
            'totalDocs' => $data['totalDocs'] ?? 0
        ];
    }
}

/**
 * File Processor - Handles individual media file imports
 */
class PayloadFileProcessor
{
    private $base_url;
    private $logger;

    public function __construct($base_url, PayloadImportLogger $logger)
    {
        $this->base_url = rtrim($base_url, '/');
        $this->logger = $logger;
    }

    public function process_file($media_data, $options = [])
    {
        $file_id = $media_data['id'] ?? 'unknown';
        $filename = $media_data['filename'] ?? 'unknown';

        // Check if already exists
        if (!empty($options['skip_existing']) && $this->file_exists($media_data)) {
            return ['status' => 'skipped', 'reason' => 'already_exists'];
        }

        // Get file URL
        $file_url = $this->get_file_url($media_data);
        if (!$file_url) {
            $this->logger->log('WARNING', 'FILE', 'File URL not found', ['id' => $file_id]);
            return ['status' => 'error', 'error' => 'no_url'];
        }

        // Import file
        return $this->import_file($file_url, $media_data, $options);
    }

    private function import_file($file_url, $media_data, $options)
    {
        // Include WordPress functions
        if (!function_exists('download_url')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        if (!function_exists('media_handle_sideload')) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
        }
        if (!function_exists('wp_generate_attachment_metadata')) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }

        // Download file
        $temp_file = download_url($file_url);
        if (is_wp_error($temp_file)) {
            $error_message = $temp_file->get_error_message();
            $this->logger->log('WARNING', 'FILE', 'Download failed', [
                'url' => $file_url,
                'error' => $error_message
            ]);
            return ['status' => 'error', 'error' => $error_message];
        }

        // Process file
        $file_array = [
            'name' => $media_data['filename'] ?? 'imported_file',
            'type' => $media_data['mimeType'] ?? 'application/octet-stream',
            'tmp_name' => $temp_file,
            'error' => 0,
            'size' => filesize($temp_file)
        ];

        $attachment_id = media_handle_sideload($file_array, 0);

        // Cleanup
        if (file_exists($temp_file)) {
            unlink($temp_file);
        }

        if (is_wp_error($attachment_id)) {
            $this->logger->log('WARNING', 'FILE', 'Import failed', [
                'error' => $attachment_id->get_error_message()
            ]);
            return ['status' => 'error', 'error' => $attachment_id->get_error_message()];
        }

        // Store metadata
        if (isset($media_data['id'])) {
            update_post_meta($attachment_id, 'payload_cms_id', $media_data['id']);
        }

        // Generate thumbnails
        if (!empty($options['generate_thumbnails'])) {
            wp_generate_attachment_metadata($attachment_id, get_attached_file($attachment_id));
        }

        return ['status' => 'imported', 'attachment_id' => $attachment_id];
    }

    private function file_exists($media_data)
    {
        global $wpdb;

        $payload_id = $media_data['id'] ?? null;
        if (!$payload_id) {
            return false;
        }

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'payload_cms_id' AND meta_value = %s LIMIT 1",
            $payload_id
        ));

        return !empty($existing);
    }

    private function get_file_url($media_data)
    {
        if (isset($media_data['url']) && !empty($media_data['url'])) {
            $url = $media_data['url'];
            if (strpos($url, 'http') !== 0) {
                $url = $this->base_url . $url;
            }
            return $this->encode_url_safely($url);
        }

        if (isset($media_data['filename']) && !empty($media_data['filename'])) {
            return $this->encode_url_safely($this->base_url . '/media/' . $media_data['filename']);
        }

        return false;
    }

    private function encode_url_safely($url)
    {
        $parsed = parse_url($url);
        if (!$parsed) return $url;

        $encoded = '';
        if (isset($parsed['scheme'])) $encoded .= $parsed['scheme'] . '://';
        if (isset($parsed['host'])) $encoded .= $parsed['host'];
        if (isset($parsed['port'])) $encoded .= ':' . $parsed['port'];
        if (isset($parsed['path'])) {
            $path_parts = explode('/', $parsed['path']);
            $encoded .= '/' . implode('/', array_map('rawurlencode', array_filter($path_parts)));
        }
        if (isset($parsed['query'])) $encoded .= '?' . $parsed['query'];
        if (isset($parsed['fragment'])) $encoded .= '#' . $parsed['fragment'];

        return $encoded;
    }
}

/**
 * Background Processor - Handles WP Cron background processing
 */
class PayloadBackgroundProcessor
{
    private $logger;
    private $state_manager;
    private $api_client;
    private $file_processor;
    private $hook_name = 'payload_import_background_process';

    public function __construct(PayloadImportLogger $logger, PayloadStateManager $state_manager, PayloadApiClient $api_client, PayloadFileProcessor $file_processor)
    {
        $this->logger = $logger;
        $this->state_manager = $state_manager;
        $this->api_client = $api_client;
        $this->file_processor = $file_processor;

        add_action($this->hook_name, [$this, 'process_background_batch']);
    }

    public function schedule_next_batch()
    {
        // Remove any existing scheduled event
        wp_clear_scheduled_hook($this->hook_name);

        // Schedule next batch in 3 seconds
        wp_schedule_single_event(time() + 3, $this->hook_name);

        $this->logger->log('INFO', 'CRON', 'Next batch scheduled');
    }

    public function process_background_batch()
    {
        $state = $this->state_manager->get_state();

        if ($state['status'] !== 'running') {
            $this->logger->log('INFO', 'CRON', 'No running import found, stopping background processing');
            return;
        }

        if ($this->state_manager->is_stop_requested()) {
            $this->state_manager->finalize_stop();
            return;
        }

        $batch_size = $state['options']['batch_size'] ?? 25;
        $processed = 0;

        try {
            // Calculate which page to fetch from API
            $current_global_index = $state['current_index'];
            $page = floor($current_global_index / 100) + 1;
            $offset_in_page = $current_global_index % 100;

            // Get batch from API
            $batch_data = $this->api_client->get_media_batch($page, 100);
            $media_files = array_slice($batch_data['docs'], $offset_in_page, $batch_size);

            // Process files in batch
            foreach ($media_files as $media_item) {
                if ($this->state_manager->is_stop_requested()) {
                    $this->state_manager->finalize_stop();
                    return;
                }

                $state['current_file'] = $media_item['filename'] ?? 'unknown';
                $state['current_index']++;

                // Process file
                $result = $this->file_processor->process_file($media_item, $state['options']);

                // Update counters
                if ($result['status'] === 'imported') {
                    $state['imported']++;
                } elseif ($result['status'] === 'skipped') {
                    $state['skipped']++;
                } else {
                    $state['failed']++;
                    $state['last_error'] = $result['error'] ?? 'Unknown error';
                }

                $processed++;
                $this->state_manager->set_state($state);

                // Log progress
                if ($processed % 5 === 0) {
                    $this->logger->log('INFO', 'BATCH', 'Progress update', [
                        'processed' => $state['current_index'],
                        'total' => $state['total'],
                        'imported' => $state['imported'],
                        'skipped' => $state['skipped'],
                        'failed' => $state['failed']
                    ]);
                }
            }

            // Check if import is complete
            if ($state['current_index'] >= $state['total']) {
                $state['status'] = 'completed';
                $state['completed_at'] = time();
                $this->state_manager->set_state($state);

                $this->logger->log('INFO', 'IMPORT', 'Import completed', [
                    'total' => $state['total'],
                    'imported' => $state['imported'],
                    'skipped' => $state['skipped'],
                    'failed' => $state['failed'],
                    'duration' => $state['completed_at'] - $state['started_at']
                ]);
            } else {
                // Schedule next batch
                $this->schedule_next_batch();
            }
        } catch (Exception $e) {
            $this->logger->log('ERROR', 'CRON', 'Background processing failed', ['error' => $e->getMessage()]);

            $state['status'] = 'error';
            $state['last_error'] = $e->getMessage();
            $this->state_manager->set_state($state);
        }
    }
}

/**
 * Main Import Controller - Fixed Version
 */
class PayloadMediaImporter
{
    private static $instance = null;
    private $logger;
    private $state_manager;
    private $api_client;
    private $file_processor;
    private $background_processor;
    private $base_url;

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        // Prevent multiple instances
        if (self::$instance !== null) {
            return self::$instance;
        }

        self::$instance = $this;

        $this->base_url = 'https://tribetravel.eu';

        // Initialize components
        $this->logger = new PayloadImportLogger();
        $this->state_manager = new PayloadStateManager($this->logger);
        $this->api_client = new PayloadApiClient($this->base_url, $this->logger);
        $this->file_processor = new PayloadFileProcessor($this->base_url, $this->logger);
        $this->background_processor = new PayloadBackgroundProcessor(
            $this->logger,
            $this->state_manager,
            $this->api_client,
            $this->file_processor
        );

        $this->init_hooks();
    }

    private function init_hooks()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

        // AJAX handlers
        add_action('wp_ajax_payload_import_start', [$this, 'ajax_start_import']);
        add_action('wp_ajax_payload_import_stop', [$this, 'ajax_stop_import']);
        add_action('wp_ajax_payload_import_reset', [$this, 'ajax_reset_import']);
        add_action('wp_ajax_payload_import_status', [$this, 'ajax_get_status']);
        add_action('wp_ajax_payload_import_stats', [$this, 'ajax_get_stats']);
    }

    public function add_admin_menu()
    {
        add_media_page(
            'Payload Media Import',
            'Payload Import',
            'manage_options',
            'payload-media-import',
            [$this, 'admin_page']
        );
    }

    public function enqueue_scripts($hook)
    {
        if ($hook !== 'media_page_payload-media-import') {
            return;
        }

        // Ensure jQuery for our script dependency
        wp_enqueue_script('jquery');

        // Enqueue shared admin styles (created during refactor)
        if (file_exists(LOVETRAVEL_CHILD_DIR . '/assets/css/admin-tools.css')) {
            wp_enqueue_style(
                'lovetravel-child-admin-tools',
                LOVETRAVEL_CHILD_URI . '/assets/css/admin-tools.css',
                [],
                LOVETRAVEL_CHILD_VERSION
            );
        }

        // Enqueue payload import admin script
        if (file_exists(LOVETRAVEL_CHILD_DIR . '/assets/js/admin-payload-import.js')) {
            wp_enqueue_script(
                'lovetravel-child-admin-payload-import',
                LOVETRAVEL_CHILD_URI . '/assets/js/admin-payload-import.js',
                ['jquery'],
                LOVETRAVEL_CHILD_VERSION,
                true
            );

            // Localize config object (previously bound to jquery)
            $state = $this->state_manager->get_state();
            wp_localize_script('lovetravel-child-admin-payload-import', 'payloadImport', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('payload_import_v4'),
                'currentStatus' => $state['status'] ?? 'idle'
            ]);
        }
    }

    public function admin_page()
    {
        // Get current state and stats
        $state = $this->state_manager->get_state();
        $is_running = $this->state_manager->is_running();
        $stats = $this->get_statistics();

        include __DIR__ . '/payload-import-admin-page.php';
    }

    // AJAX Handlers
    public function ajax_start_import()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'payload_import_v4')) {
            wp_die('Security check failed');
        }

        if (!current_user_can('upload_files')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
            return;
        }

        $state = $this->state_manager->get_state();

        // If already running, return current state
        if ($state['status'] === 'running') {
            wp_send_json_error(['message' => 'Import is already running']);
            return;
        }

        // Clear log file and start fresh
        $this->logger->clear_log();

        // Get total count from API
        $total_count = $this->api_client->get_media_count(true);

        if ($total_count === 0) {
            wp_send_json_error(['message' => 'No media files found to import']);
            return;
        }

        // Initialize new import job
        $job_id = 'job_' . date('Ymd_His') . '_' . substr(md5(microtime()), 0, 8);

        $new_state = [
            'job_id' => $job_id,
            'status' => 'running',
            'started_at' => time(),
            'updated_at' => time(),
            'total' => $total_count,
            'imported' => 0,
            'skipped' => 0,
            'failed' => 0,
            'current_index' => 0,
            'current_file' => null,
            'last_error' => null,
            'options' => [
                'batch_size' => intval($_POST['batch_size'] ?? 25),
                'skip_existing' => $_POST['skip_existing'] === '1',
                'generate_thumbnails' => $_POST['generate_thumbnails'] === '1'
            ]
        ];

        $this->state_manager->set_state($new_state);

        $this->logger->log('INFO', 'IMPORT', 'Import started', [
            'job_id' => $job_id,
            'total_files' => $total_count,
            'options' => $new_state['options']
        ]);

        // Start background processing
        $this->background_processor->schedule_next_batch();

        wp_send_json_success([
            'message' => 'Import started successfully',
            'job_id' => $job_id,
            'total_files' => $total_count
        ]);
    }

    public function ajax_stop_import()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'payload_import_v4')) {
            wp_die('Security check failed');
        }

        $this->state_manager->request_stop();
        $this->logger->log('INFO', 'IMPORT', 'Stop requested by user');

        wp_send_json_success(['message' => 'Stop requested']);
    }

    public function ajax_reset_import()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'payload_import_v4')) {
            wp_die('Security check failed');
        }

        $this->state_manager->clear_state();
        $this->logger->clear_log();
        $this->logger->log('INFO', 'IMPORT', 'Import state reset by user');

        wp_send_json_success(['message' => 'Import state reset']);
    }

    public function ajax_get_status()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'payload_import_v4')) {
            wp_die('Security check failed');
        }

        $state = $this->state_manager->get_state();
        $recent_logs = $this->logger->get_recent_logs(20);

        wp_send_json_success([
            'state' => $state,
            'logs' => $recent_logs
        ]);
    }

    public function ajax_get_stats()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'payload_import_v4')) {
            wp_die('Security check failed');
        }

        $stats = $this->get_statistics();
        wp_send_json_success(['stats' => $stats]);
    }

    private function get_statistics()
    {
        $state = $this->state_manager->get_state();
        $payload_count = $this->api_client->get_media_count();

        // Get WordPress media count
        $wp_count = wp_count_attachments();
        $wp_total = array_sum((array) $wp_count);

        // Get imported count
        global $wpdb;
        $imported_count = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = 'payload_cms_id'"
        );

        return [
            'payload_count' => $payload_count,
            'wp_count' => $wp_total,
            'imported_count' => $state['status'] === 'running' ? $state['imported'] : $imported_count,
            'remaining_count' => max(0, $payload_count - $imported_count),
            'current_job' => $state['status'] !== 'idle' ? $state : null
        ];
    }
}

// Initialize the importer singleton
PayloadMediaImporter::get_instance();
