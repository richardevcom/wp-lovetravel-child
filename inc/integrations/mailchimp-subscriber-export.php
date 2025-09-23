<?php

/**
 * Mailchimp Subscriber Export Tool
 *
 * This file handles exporting newsletter subscribers from Payload CMS API 
 * and preparing them for Mailchimp import.
 * 
 * @package LoveTravel_Child
 * @version 1.0.0
 * @author richardevcom
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Mailchimp Subscriber Export Class
 * 
 * Handles the export of newsletter subscribers from Payload CMS for Mailchimp import
 */
class LoveTravel_Mailchimp_Subscriber_Export
{
    /**
     * Payload CMS base URL
     * 
     * @var string
     */
    private $payload_base_url = 'https://tribetravel.eu';

    /**
     * API endpoint for mailing collection
     * 
     * @var string
     */
    private $mailing_endpoint = '/api/mailing';

    /**
     * Default query parameters for Payload v2 API
     * 
     * @var array
     */
    private $default_params = [
        'locale' => 'undefined',
        'draft' => 'false',
        'depth' => '1'
    ];

    /**
     * Maximum items per API request
     * 
     * @var int
     */
    private $max_limit = 100;

    /**
     * Constructor
     * 
     * @since 1.0.0
     */
    public function __construct()
    {
        // Add admin menu hook for Mailchimp plugin
        add_action('admin_menu', [$this, 'add_admin_menu'], 20);

        // Add AJAX handlers
        add_action('wp_ajax_export_mailchimp_subscribers', [$this, 'ajax_export_subscribers']);
        add_action('wp_ajax_download_mailchimp_export', [$this, 'ajax_download_export']);
        add_action('wp_ajax_get_subscriber_stats', [$this, 'ajax_get_subscriber_stats']);

        // Add admin scripts
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    /**
     * Add admin menu for subscriber export
     * 
     * @since 1.0.0
     */
    public function add_admin_menu()
    {
        // Only add if Mailchimp for WP plugin is active
        if (!function_exists('mc4wp')) {
            return;
        }

        add_submenu_page(
            'mailchimp-for-wp',
            'Payload Export',
            'Payload Export',
            'manage_options',
            'mc4wp-export-subscribers',
            [$this, 'admin_page']
        );
    }

    /**
     * Enqueue admin scripts and styles
     * 
     * @param string $hook The current admin page hook
     * @since 1.0.0
     */
    public function enqueue_admin_scripts($hook)
    {
        // Only load on our specific page
        if (!isset($_GET['page']) || $_GET['page'] !== 'mc4wp-export-subscribers') {
            return;
        }

        // Enqueue shared admin styles
        if (file_exists(LOVETRAVEL_CHILD_DIR . '/assets/css/admin-tools.css')) {
            wp_enqueue_style(
                'lovetravel-admin-tools',
                LOVETRAVEL_CHILD_URI . '/assets/css/admin-tools.css',
                [],
                LOVETRAVEL_CHILD_VERSION
            );
        }

        // Enqueue externalized JS and localize config
        if (file_exists(LOVETRAVEL_CHILD_DIR . '/assets/js/admin-mailchimp-export.js')) {
            wp_enqueue_script(
                'lovetravel-mailchimp-export-admin',
                LOVETRAVEL_CHILD_URI . '/assets/js/admin-mailchimp-export.js',
                ['jquery'],
                LOVETRAVEL_CHILD_VERSION,
                true
            );
            wp_localize_script(
                'lovetravel-mailchimp-export-admin',
                'mc4wp_export_ajax',
                [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('mc4wp_export_nonce')
                ]
            );
        }
    }

    /**
     * Admin page for subscriber export
     * 
     * @since 1.0.0
     */
    public function admin_page()
    {
?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Payload Subscribers Export</h1>
            <hr class="wp-header-end">
            <div id="export-notices"></div>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">

                    <!-- Sidebar: Statistics -->
                    <div id="postbox-container-1" class="postbox-container">
                        <div class="postbox">
                            <h2 class="hndle">Subscriber Statistics</h2>
                            <div class="inside">
                                <table class="widefat">
                                    <tr>
                                        <td><strong>Total Subscribers:</strong></td>
                                        <td id="total-subscribers">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Active Subscribers:</strong></td>
                                        <td id="active-subscribers">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Last 30 Days:</strong></td>
                                        <td id="recent-subscribers">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div id="postbox-container-2" class="postbox-container">
                        <div class="postbox">
                            <h2 class="hndle">Export Options</h2>
                            <div class="inside">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row">Export Format</th>
                                        <td>
                                            <select id="export-format" class="regular-text">
                                                <option value="csv">CSV (Recommended for Mailchimp)</option>
                                                <option value="json">JSON</option>
                                            </select>
                                            <p class="description">CSV format is recommended for Mailchimp import</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Include Unsubscribed</th>
                                        <td>
                                            <label>
                                                <input type="checkbox" id="include-unsubscribed" value="1">
                                                Include unsubscribed users in export
                                            </label>
                                            <p class="description">Useful for data backup, but not recommended for Mailchimp import</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Date Range</th>
                                        <td>
                                            <label>
                                                <input type="checkbox" id="use-date-range" value="1">
                                                Export only subscribers from specific date range
                                            </label>
                                            <div id="date-range-options" style="display: none; margin-top: 10px;">
                                                <input type="date" id="start-date" class="regular-text"> to
                                                <input type="date" id="end-date" class="regular-text">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <p class="submit">
                                    <button type="button" id="export-subscribers" class="button button-primary">Export Subscribers</button>
                                </p>
                            </div>
                        </div>

                        <div class="postbox" id="export-progress">
                            <h2 class="hndle">Export Progress</h2>
                            <div class="inside">
                                <div class="mc4wp-progress-bar">
                                    <div class="mc4wp-progress-fill" id="progress-fill"></div>
                                </div>
                                <div id="progress-text">Starting export...</div>
                                <div id="progress-details"></div>
                                <p class="submit">
                                    <a href="#" id="download-export" class="button button-secondary" target="_blank" style="display:none;">Download CSV</a>
                                </p>
                                <p class="description" id="download-description" style="display:none;">This will download the exported CSV file compatible with Mailchimp import.</p>
                            </div>
                        </div>

                        <div class="postbox">
                            <h2 class="hndle">Export Log</h2>
                            <div class="inside">
                                <div id="export-log" class="import-log-box" aria-live="polite"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- JS moved to assets/js/admin-mailchimp-export.js; styles moved to assets/css/admin-tools.css -->
<?php
    }

    /**
     * AJAX handler for getting subscriber statistics
     * 
     * @since 1.0.0
     */
    public function ajax_get_subscriber_stats()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'mc4wp_export_nonce')) {
            wp_die('Security check failed');
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
            return;
        }

        // Get subscriber statistics
        $stats = $this->get_subscriber_statistics();

        if (is_wp_error($stats)) {
            wp_send_json_error(['message' => $stats->get_error_message()]);
        } else {
            wp_send_json_success($stats);
        }
    }

    /**
     * AJAX handler for exporting subscribers
     * 
     * @since 1.0.0
     */
    public function ajax_export_subscribers()
    {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'mc4wp_export_nonce')) {
            wp_die('Security check failed');
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Insufficient permissions']);
            return;
        }

        $format = sanitize_text_field($_POST['format'] ?? 'csv');
        $include_unsubscribed = isset($_POST['include_unsubscribed']) && $_POST['include_unsubscribed'] == '1';
        $use_date_range = isset($_POST['use_date_range']) && $_POST['use_date_range'] == '1';
        $start_date = sanitize_text_field($_POST['start_date'] ?? '');
        $end_date = sanitize_text_field($_POST['end_date'] ?? '');

        // Get all subscribers
        $subscribers = $this->get_all_subscribers($include_unsubscribed, $use_date_range, $start_date, $end_date);

        if (is_wp_error($subscribers)) {
            wp_send_json_error(['message' => $subscribers->get_error_message()]);
            return;
        }

        // Generate export file
        $export_result = $this->generate_export_file($subscribers, $format);

        if (is_wp_error($export_result)) {
            wp_send_json_error(['message' => $export_result->get_error_message()]);
        } else {
            wp_send_json_success([
                'exported_count' => count($subscribers),
                'download_url' => $export_result['download_url'],
                'filename' => $export_result['filename']
            ]);
        }
    }

    /**
     * Get subscriber statistics from Payload CMS
     * 
     * @return array|WP_Error Statistics or error
     * @since 1.0.0
     */
    private function get_subscriber_statistics()
    {
        // Get total count
        $total_url = $this->payload_base_url . $this->mailing_endpoint . '/count';
        $total_url = add_query_arg($this->default_params, $total_url);

        $response = wp_remote_get($total_url, ['timeout' => 30]);
        if (is_wp_error($response)) {
            return new WP_Error('api_error', 'Failed to connect to Payload CMS');
        }

        $total_data = json_decode(wp_remote_retrieve_body($response), true);
        $total_count = $total_data['totalDocs'] ?? 0;

        // Get active subscribers count
        $active_url = $this->payload_base_url . $this->mailing_endpoint . '/count';
        $active_params = array_merge($this->default_params, ['where[subscribed][equals]' => 'true']);
        $active_url = add_query_arg($active_params, $active_url);

        $response = wp_remote_get($active_url, ['timeout' => 30]);
        $active_data = json_decode(wp_remote_retrieve_body($response), true);
        $active_count = $active_data['totalDocs'] ?? 0;

        // Get recent subscribers (last 30 days)
        $thirty_days_ago = date('Y-m-d\TH:i:s.000\Z', strtotime('-30 days'));
        $recent_url = $this->payload_base_url . $this->mailing_endpoint . '/count';
        $recent_params = array_merge($this->default_params, [
            'where[createdAt][greater_than_equal]' => $thirty_days_ago,
            'where[subscribed][equals]' => 'true'
        ]);
        $recent_url = add_query_arg($recent_params, $recent_url);

        $response = wp_remote_get($recent_url, ['timeout' => 30]);
        $recent_data = json_decode(wp_remote_retrieve_body($response), true);
        $recent_count = $recent_data['totalDocs'] ?? 0;

        return [
            'total_count' => $total_count,
            'active_count' => $active_count,
            'recent_count' => $recent_count
        ];
    }

    /**
     * Get all subscribers from Payload CMS
     * 
     * @param bool $include_unsubscribed Include unsubscribed users
     * @param bool $use_date_range Use date range filter
     * @param string $start_date Start date
     * @param string $end_date End date
     * @return array|WP_Error Array of subscribers or error
     * @since 1.0.0
     */
    private function get_all_subscribers($include_unsubscribed = false, $use_date_range = false, $start_date = '', $end_date = '')
    {
        $all_subscribers = [];
        $page = 1;
        $has_next_page = true;

        while ($has_next_page) {
            $url = $this->payload_base_url . $this->mailing_endpoint;
            $params = array_merge($this->default_params, [
                'limit' => $this->max_limit,
                'page' => $page
            ]);

            // Add subscription filter
            if (!$include_unsubscribed) {
                $params['where[subscribed][equals]'] = 'true';
            }

            // Add date range filter
            if ($use_date_range && !empty($start_date)) {
                $params['where[createdAt][greater_than_equal]'] = $start_date . 'T00:00:00.000Z';
            }
            if ($use_date_range && !empty($end_date)) {
                $params['where[createdAt][less_than_equal]'] = $end_date . 'T23:59:59.999Z';
            }

            $url = add_query_arg($params, $url);

            $response = wp_remote_get($url, ['timeout' => 30]);

            if (is_wp_error($response)) {
                return new WP_Error('api_error', 'Failed to fetch subscribers: ' . $response->get_error_message());
            }

            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code !== 200) {
                return new WP_Error('api_error', 'API returned error: ' . $response_code);
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return new WP_Error('json_error', 'Invalid JSON response');
            }

            if (isset($data['docs']) && is_array($data['docs'])) {
                $all_subscribers = array_merge($all_subscribers, $data['docs']);
            }

            // Check if there's a next page
            $has_next_page = isset($data['hasNextPage']) && $data['hasNextPage'];
            $page++;

            // Safety check to prevent infinite loops
            if ($page > 100) {
                break;
            }
        }

        return $all_subscribers;
    }

    /**
     * Generate export file
     * 
     * @param array $subscribers Array of subscriber data
     * @param string $format Export format (csv or json)
     * @return array|WP_Error Export result or error
     * @since 1.0.0
     */
    private function generate_export_file($subscribers, $format = 'csv')
    {
        // Create uploads directory if it doesn't exist
        $upload_dir = wp_upload_dir();
        $export_dir = $upload_dir['basedir'] . '/mailchimp-exports';

        if (!file_exists($export_dir)) {
            wp_mkdir_p($export_dir);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $filename = "subscribers_export_{$timestamp}.{$format}";
        $filepath = $export_dir . '/' . $filename;

        if ($format === 'csv') {
            $result = $this->generate_csv_file($subscribers, $filepath);
        } else {
            $result = $this->generate_json_file($subscribers, $filepath);
        }

        if (is_wp_error($result)) {
            return $result;
        }

        // Generate download URL
        $download_url = add_query_arg([
            'action' => 'download_mailchimp_export',
            'file' => basename($filepath),
            'nonce' => wp_create_nonce('mc4wp_download_nonce')
        ], admin_url() . 'admin-ajax.php');

        return [
            'filepath' => $filepath,
            'filename' => $filename,
            'download_url' => $download_url
        ];
    }

    /**
     * Generate CSV file for Mailchimp import
     * 
     * @param array $subscribers Array of subscriber data
     * @param string $filepath File path to save
     * @return true|WP_Error Success or error
     * @since 1.0.0
     */
    private function generate_csv_file($subscribers, $filepath)
    {
        $file = fopen($filepath, 'w');
        if (!$file) {
            return new WP_Error('file_error', 'Could not create export file');
        }

        // Write CSV header (Mailchimp format)
        fputcsv($file, [
            'Email Address',
            'Status',
            'Date Subscribed',
            'Date Updated',
            'Source'
        ]);

        // Write subscriber data
        foreach ($subscribers as $subscriber) {
            $status = ($subscriber['subscribed'] ?? false) ? 'subscribed' : 'unsubscribed';
            $date_subscribed = isset($subscriber['createdAt']) ?
                date('Y-m-d H:i:s', strtotime($subscriber['createdAt'])) : '';
            $date_updated = isset($subscriber['updatedAt']) ?
                date('Y-m-d H:i:s', strtotime($subscriber['updatedAt'])) : '';

            fputcsv($file, [
                $subscriber['email'] ?? '',
                $status,
                $date_subscribed,
                $date_updated,
                'Payload CMS'
            ]);
        }

        fclose($file);
        return true;
    }

    /**
     * Generate JSON file
     * 
     * @param array $subscribers Array of subscriber data
     * @param string $filepath File path to save
     * @return true|WP_Error Success or error
     * @since 1.0.0
     */
    private function generate_json_file($subscribers, $filepath)
    {
        $json_data = json_encode($subscribers, JSON_PRETTY_PRINT);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', 'Failed to encode JSON');
        }

        $result = file_put_contents($filepath, $json_data);
        if ($result === false) {
            return new WP_Error('file_error', 'Could not write export file');
        }

        return true;
    }

    /**
     * AJAX handler for downloading export files
     * 
     * @since 1.0.0
     */
    public function ajax_download_export()
    {
        // Verify nonce
        if (!wp_verify_nonce($_GET['nonce'] ?? '', 'mc4wp_download_nonce')) {
            wp_die('Security check failed');
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }

        $filename = sanitize_file_name($_GET['file'] ?? '');
        if (empty($filename)) {
            wp_die('Invalid file');
        }

        $upload_dir = wp_upload_dir();
        $filepath = $upload_dir['basedir'] . '/mailchimp-exports/' . $filename;

        if (!file_exists($filepath)) {
            wp_die('File not found');
        }

        // Set headers for download
        $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
        $content_type = $file_extension === 'csv' ? 'text/csv' : 'application/json';

        header('Content-Type: ' . $content_type);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        // Output file
        readfile($filepath);
        exit;
    }
}

// Initialize the exporter only if Mailchimp for WP is active
// ✅ Verified: Class will be instantiated via functions.php hook system
if (function_exists('mc4wp') || class_exists('MC4WP_Container')) {
    // Class instantiation handled in functions.php via WordPress hooks
    add_action('admin_init', function() {
        if (is_admin()) {
            new LoveTravel_Mailchimp_Subscriber_Export();
        }
    });
}
