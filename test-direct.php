<?php
/**
 * Direct Test Script for Adventure Import
 * Access this via: /wp-content/themes/lovetravel-child/test-direct.php
 */

// WordPress bootstrap
require_once('../../../wp-load.php');

// Ensure we're in admin context
if (!current_user_can('manage_options')) {
    echo '<h1>Access Denied</h1><p>Admin access required.</p>';
    exit;
}

echo '<h1>Direct Adventure Import Test</h1>';

// Initialize the wizard
$wizard = new LoveTravel_Child_Setup_Wizard();

echo '<h2>Current Progress</h2>';
$current_progress = get_option('lovetravel_adventure_import_progress', array());
echo '<pre>';
var_dump($current_progress);
echo '</pre>';

echo '<h2>Testing Direct API Call</h2>';
$api_url = 'https://tribetravel.eu/api/adventures/?limit=5';
$response = wp_remote_get($api_url, array('timeout' => 10));

if (is_wp_error($response)) {
    echo '<p style="color: red;">❌ API Error: ' . $response->get_error_message() . '</p>';
} else {
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    echo '<p style="color: green;">✅ API Success: ' . count($data['docs']) . ' adventures found</p>';
}

echo '<h2>Manual Processing Test</h2>';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    switch ($action) {
        case 'start':
            echo '<p>Starting adventure import...</p>';
            
            // Initialize progress manually
            $import_progress = array(
                'status' => 'fetching',
                'duplicate_handling' => 'update',
                'total_adventures' => 0,
                'processed' => 0,
                'imported' => 0,
                'skipped' => 0,
                'errors' => array(),
                'debug_logs' => array('Manual start initiated'),
                'current_batch' => 0,
                'started_at' => current_time('mysql'),
                'last_activity' => current_time('mysql'),
                'media_queue' => array(),
                'retry_count' => 0
            );
            
            update_option('lovetravel_adventure_import_progress', $import_progress);
            echo '<p>✅ Progress initialized</p>';
            
            // Call the processing directly
            try {
                $wizard->process_background_adventure_import();
                echo '<p>✅ Background processing completed</p>';
            } catch (Exception $e) {
                echo '<p style="color: red;">❌ Processing error: ' . $e->getMessage() . '</p>';
            }
            
            break;
            
        case 'process':
            echo '<p>Processing current batch...</p>';
            try {
                $wizard->process_background_adventure_import();
                echo '<p>✅ Background processing completed</p>';
            } catch (Exception $e) {
                echo '<p style="color: red;">❌ Processing error: ' . $e->getMessage() . '</p>';
            }
            break;
            
        case 'reset':
            echo '<p>Resetting progress...</p>';
            delete_option('lovetravel_adventure_import_progress');
            delete_option('lovetravel_import_status');
            wp_clear_scheduled_hook('lovetravel_process_adventure_import');
            echo '<p>✅ Progress reset</p>';
            break;
    }
    
    echo '<h2>Updated Progress</h2>';
    $updated_progress = get_option('lovetravel_adventure_import_progress', array());
    echo '<pre>';
    var_dump($updated_progress);
    echo '</pre>';
}

echo '<h2>Actions</h2>';
echo '<p><a href="?action=start">Start Import</a> | ';
echo '<a href="?action=process">Process Batch</a> | ';
echo '<a href="?action=reset">Reset Progress</a></p>';

echo '<h2>Debug Logs</h2>';
$debug_log_path = WP_CONTENT_DIR . '/debug.log';
if (file_exists($debug_log_path)) {
    $recent_logs = array_slice(file($debug_log_path), -20);
    echo '<pre style="background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;">';
    foreach ($recent_logs as $log_line) {
        if (strpos($log_line, 'LoveTravel') !== false) {
            echo htmlspecialchars($log_line);
        }
    }
    echo '</pre>';
} else {
    echo '<p>No debug log found at ' . $debug_log_path . '</p>';
}

?>