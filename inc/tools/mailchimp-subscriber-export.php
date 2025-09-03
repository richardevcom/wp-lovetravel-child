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

        wp_enqueue_script('jquery');

        // Add inline script with AJAX configuration
        add_action('admin_footer', function () {
?>
            <script type="text/javascript">
                window.mc4wp_export_ajax = {
                    ajax_url: '<?php echo admin_url() . 'admin-ajax.php'; ?>',
                    nonce: '<?php echo wp_create_nonce('mc4wp_export_nonce'); ?>'
                };
                console.log('MC4WP Export AJAX configured:', window.mc4wp_export_ajax);
            </script>
        <?php
        });

        // Add styling to match payload import tool
        wp_add_inline_style('wp-admin', '
            .mc4wp-export-card { 
                background: #fff; 
                border: 1px solid #c3c4c7; 
                border-radius: 4px; 
                padding: 20px; 
                margin: 20px 0; 
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .mc4wp-progress-bar { 
                background: #f1f1f1; 
                border-radius: 4px; 
                height: 20px; 
                width: 100%; 
                margin: 10px 0; 
                overflow: hidden;
            }
            .mc4wp-progress-fill {
                background: linear-gradient(90deg, #0073aa 0%, #005a87 100%);
                height: 100%;
                border-radius: 4px;
                transition: width 0.3s ease;
                width: 0%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 12px;
                font-weight: 600;
            }
            .mc4wp-export-results {
                margin-top: 20px;
                padding: 15px;
                border-radius: 4px;
                display: none;
            }
            .mc4wp-export-results.success {
                background: #d1e7dd;
                border: 1px solid #badbcc;
                color: #0f5132;
            }
            .mc4wp-export-results.error {
                background: #f8d7da;
                border: 1px solid #f5c2c7;
                color: #842029;
            }
            .mc4wp-stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin: 20px 0;
            }
            .mc4wp-stat-box {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                border: 1px solid #dee2e6;
                transition: transform 0.2s ease;
            }
            .mc4wp-stat-box:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            .mc4wp-stat-number {
                font-size: 32px;
                font-weight: 700;
                color: #0073aa;
                margin-bottom: 8px;
                display: block;
            }
            .mc4wp-stat-label {
                font-size: 14px;
                color: #666;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .mc4wp-download-link {
                display: inline-block;
                background: linear-gradient(90deg, #0073aa 0%, #005a87 100%);
                color: white !important;
                padding: 12px 24px;
                text-decoration: none;
                border-radius: 6px;
                margin: 15px 0;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .mc4wp-download-link:hover {
                background: linear-gradient(90deg, #005a87 0%, #004a73 100%);
                color: white !important;
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            }
            .mc4wp-export-card h2 {
                margin-top: 0;
                color: #23282d;
                border-bottom: 2px solid #0073aa;
                padding-bottom: 10px;
                margin-bottom: 20px;
            }
            #export-progress {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                padding: 20px;
                margin: 20px 0;
            }
            #progress-text {
                font-weight: 600;
                color: #495057;
                margin: 10px 0 5px 0;
            }
            #progress-details {
                color: #6c757d;
                font-size: 14px;
            }
        ');
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
            <h1>Export Payload Subscribers for Mailchimp</h1>

            <div class="notice notice-info">
                <p><strong>Subscriber Export Tool:</strong> Export newsletter subscribers from Payload CMS and prepare them for Mailchimp import.</p>
                <p><strong>Source:</strong> <?php echo esc_html($this->payload_base_url . $this->mailing_endpoint); ?></p>
                <p><strong>Format:</strong> CSV file compatible with Mailchimp import</p>
            </div>

            <!-- Statistics Section -->
            <div class="mc4wp-export-card">
                <h2>Subscriber Statistics</h2>
                <button type="button" id="refresh-stats" class="button">Refresh Statistics</button>

                <div class="mc4wp-stats">
                    <div class="mc4wp-stat-box">
                        <div class="mc4wp-stat-number" id="total-subscribers">-</div>
                        <div class="mc4wp-stat-label">Total Subscribers</div>
                    </div>
                    <div class="mc4wp-stat-box">
                        <div class="mc4wp-stat-number" id="active-subscribers">-</div>
                        <div class="mc4wp-stat-label">Active Subscribers</div>
                    </div>
                    <div class="mc4wp-stat-box">
                        <div class="mc4wp-stat-number" id="recent-subscribers">-</div>
                        <div class="mc4wp-stat-label">Last 30 Days</div>
                    </div>
                </div>
            </div>

            <!-- Export Options -->
            <div class="mc4wp-export-card">
                <h2>Export Options</h2>

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

            <!-- Progress Section -->
            <div id="export-progress" style="display: none;">
                <h3>Export Progress</h3>
                <div class="mc4wp-progress-bar">
                    <div class="mc4wp-progress-fill" id="progress-fill"></div>
                </div>
                <div id="progress-text">Starting export...</div>
                <div id="progress-details"></div>
            </div>

            <!-- Results Section -->
            <div id="export-results" class="mc4wp-export-results"></div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function($) {
                let exportInProgress = false;

                // Check if our AJAX object is available
                if (typeof mc4wp_export_ajax === 'undefined') {
                    console.error('mc4wp_export_ajax is not defined');
                    showResult('error', 'JavaScript configuration error. Please refresh the page.');
                    return;
                }

                // Load initial statistics
                loadStatistics();

                // Refresh statistics
                $('#refresh-stats').on('click', function() {
                    $(this).prop('disabled', true).text('Loading...');
                    loadStatistics().always(function() {
                        $('#refresh-stats').prop('disabled', false).text('Refresh Statistics');
                    });
                });

                // Toggle date range options
                $('#use-date-range').on('change', function() {
                    $('#date-range-options').toggle(this.checked);
                });

                // Export subscribers
                $('#export-subscribers').on('click', function() {
                    if (exportInProgress) {
                        alert('Export already in progress!');
                        return;
                    }

                    exportInProgress = true;
                    $(this).prop('disabled', true).text('Exporting...');

                    exportSubscribers();
                });

                function loadStatistics() {
                    $('#total-subscribers, #active-subscribers, #recent-subscribers').text('...');

                    return $.post(mc4wp_export_ajax.ajax_url, {
                        action: 'get_subscriber_stats',
                        nonce: mc4wp_export_ajax.nonce
                    }).done(function(response) {
                        if (response.success) {
                            $('#total-subscribers').text(response.data.total_count.toLocaleString());
                            $('#active-subscribers').text(response.data.active_count.toLocaleString());
                            $('#recent-subscribers').text(response.data.recent_count.toLocaleString());
                            console.log('Statistics loaded successfully');
                        } else {
                            showResult('error', 'Failed to load statistics: ' + (response.data ? response.data.message : 'Unknown error'));
                        }
                    }).fail(function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        showResult('error', 'Failed to load statistics: Network error');
                        $('#total-subscribers, #active-subscribers, #recent-subscribers').text('Error');
                    });
                }

                function exportSubscribers() {
                    $('#export-progress').show();
                    $('#export-results').hide().removeClass('success error');

                    var exportData = {
                        action: 'export_mailchimp_subscribers',
                        nonce: mc4wp_export_ajax.nonce,
                        format: $('#export-format').val(),
                        include_unsubscribed: $('#include-unsubscribed').is(':checked') ? '1' : '0',
                        use_date_range: $('#use-date-range').is(':checked') ? '1' : '0',
                        start_date: $('#start-date').val(),
                        end_date: $('#end-date').val()
                    };

                    updateProgress(10, 'Connecting to Payload CMS...', 'Initializing export process');

                    $.post(mc4wp_export_ajax.ajax_url, exportData)
                        .done(function(response) {
                            if (response.success) {
                                updateProgress(100, 'Export completed successfully!',
                                    'Exported ' + response.data.exported_count + ' subscribers');
                                showResult('success',
                                    '<strong>Export Complete!</strong><br>' +
                                    'Successfully exported ' + response.data.exported_count + ' subscribers.<br>' +
                                    '<a href="' + response.data.download_url + '" class="mc4wp-download-link" target="_blank">' +
                                    '📥 Download Export File (' + response.data.filename + ')</a>'
                                );
                            } else {
                                updateProgress(0, 'Export failed', '');
                                showResult('error', 'Export failed: ' + (response.data ? response.data.message : 'Unknown error'));
                            }
                        })
                        .fail(function(xhr, status, error) {
                            console.error('Export AJAX Error:', status, error, xhr.responseText);
                            updateProgress(0, 'Export failed', '');
                            showResult('error', 'Export failed due to network error: ' + status);
                        })
                        .always(function() {
                            exportInProgress = false;
                            $('#export-subscribers').prop('disabled', false).text('Export Subscribers');
                        });
                }

                function updateProgress(percent, text, details) {
                    $('#progress-fill').css('width', percent + '%').text(percent + '%');
                    $('#progress-text').text(text);
                    $('#progress-details').text(details);
                }

                function showResult(type, message) {
                    $('#export-results')
                        .removeClass('success error')
                        .addClass(type)
                        .html(message)
                        .show();
                }
            });
        </script>
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
if (function_exists('mc4wp') || class_exists('MC4WP_Container')) {
    new LoveTravel_Mailchimp_Subscriber_Export();
}
