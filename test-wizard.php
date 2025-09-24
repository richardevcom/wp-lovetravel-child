<?php

/**
 * Manual Test Script for LoveTravel Setup Wizard
 * Run this via admin to test individual components
 */

// WordPress bootstrap
require_once('../../../wp-config.php');

if (!is_admin() || !current_user_can('manage_options')) {
    wp_die('Access denied. Admin access required.');
}

// Get test action
$test_action = $_GET['test'] ?? 'menu';

?>
<!DOCTYPE html>
<html>

<head>
    <title>LoveTravel Wizard Test Suite</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 20px;
        }

        .test-section {
            border: 1px solid #ddd;
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
        }

        pre {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            overflow-x: auto;
        }

        .menu a {
            display: inline-block;
            margin: 10px;
            padding: 10px 15px;
            background: #0073aa;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }

        .status-badge {
            padding: 2px 8px;
            border-radius: 3px;
            color: white;
            font-size: 12px;
        }

        .fetching {
            background: #ffc107;
        }

        .processing {
            background: #17a2b8;
        }

        .completed {
            background: #28a745;
        }

        .failed {
            background: #dc3545;
        }

        .stopped {
            background: #6c757d;
        }
    </style>
</head>

<body>

    <h1>LoveTravel Setup Wizard Test Suite</h1>

    <?php

    switch ($test_action) {
        case 'menu':
            echo '<div class="menu">';
            echo '<a href="?test=api">Test API Connectivity</a>';
            echo '<a href="?test=progress">Check Import Progress</a>';
            echo '<a href="?test=cron">Test WordPress Cron</a>';
            echo '<a href="?test=manual_batch">Manual Batch Test</a>';
            echo '<a href="?test=reset">Reset All Progress</a>';
            echo '<a href="?test=validate">Validate Imported Data</a>';
            echo '</div>';
            break;

        case 'api':
            testApiConnectivity();
            break;

        case 'progress':
            checkImportProgress();
            break;

        case 'cron':
            testWordPressCron();
            break;

        case 'manual_batch':
            testManualBatch();
            break;

        case 'reset':
            resetAllProgress();
            break;

        case 'validate':
            validateImportedData();
            break;

        default:
            echo '<p>Unknown test action.</p>';
    }

    function testApiConnectivity()
    {
        echo '<div class="test-section">';
        echo '<h2>API Connectivity Test</h2>';

        $endpoints = array(
            'adventures' => 'https://tribetravel.eu/api/adventures/?limit=1',
            'media' => 'https://tribetravel.eu/api/media/?limit=1',
            'destinations' => 'https://tribetravel.eu/api/destinations/?limit=1'
        );

        foreach ($endpoints as $name => $url) {
            echo '<h3>' . ucfirst($name) . ' API</h3>';
            echo '<p>Testing: <code>' . $url . '</code></p>';

            $start_time = microtime(true);
            $response = wp_remote_get($url, array('timeout' => 10));
            $end_time = microtime(true);

            if (is_wp_error($response)) {
                echo '<div class="error">❌ <strong>Error:</strong> ' . $response->get_error_message() . '</div>';
            } else {
                $code = wp_remote_retrieve_response_code($response);
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);

                $response_time = round(($end_time - $start_time) * 1000, 2);

                if ($code === 200) {
                    echo '<div class="success">✅ <strong>Success:</strong> HTTP ' . $code . ' (' . $response_time . 'ms)</div>';
                    if (isset($data['docs']) && is_array($data['docs'])) {
                        echo '<p><strong>Total available:</strong> ' . (count($data['docs']) ?? 'unknown') . '</p>';
                        if (!empty($data['docs'][0])) {
                            echo '<p><strong>Sample data keys:</strong> ' . implode(', ', array_keys($data['docs'][0])) . '</p>';
                        }
                    } else {
                        echo '<div class="warning">⚠️ Response structure unexpected</div>';
                        echo '<pre>' . htmlspecialchars(substr($body, 0, 500)) . '</pre>';
                    }
                } else {
                    echo '<div class="error">❌ <strong>HTTP Error:</strong> ' . $code . '</div>';
                    echo '<pre>' . htmlspecialchars(substr($body, 0, 500)) . '</pre>';
                }
            }
        }
        echo '</div>';
    }

    function checkImportProgress()
    {
        echo '<div class="test-section">';
        echo '<h2>Import Progress Status</h2>';

        $import_status = get_option('lovetravel_import_status', array());
        $adventure_progress = get_option('lovetravel_adventure_import_progress', array());
        $media_progress = get_option('lovetravel_media_import_progress', array());
        $destinations_progress = get_option('lovetravel_destinations_import_progress', array());

        echo '<h3>Overall Import Status</h3>';
        if (empty($import_status)) {
            echo '<p>No completed imports found.</p>';
        } else {
            foreach ($import_status as $step => $completed_at) {
                echo '<p>✅ <strong>' . ucfirst($step) . ':</strong> Completed at ' . $completed_at . '</p>';
            }
        }

        $progress_data = array(
            'Adventures' => $adventure_progress,
            'Media' => $media_progress,
            'Destinations' => $destinations_progress
        );

        foreach ($progress_data as $name => $progress) {
            echo '<h3>' . $name . ' Progress</h3>';
            if (empty($progress)) {
                echo '<p>No progress data found.</p>';
                continue;
            }

            $status = $progress['status'] ?? 'unknown';
            echo '<p><span class="status-badge ' . $status . '">' . strtoupper($status) . '</span></p>';

            if (isset($progress['processed'], $progress['total_' . strtolower($name)])) {
                $total_key = 'total_' . strtolower($name);
                if ($name === 'Destinations') $total_key = 'total_destinations';
                if ($name === 'Media') $total_key = 'total_media';
                if ($name === 'Adventures') $total_key = 'total_adventures';

                $processed = $progress['processed'];
                $total = $progress[$total_key] ?? 0;
                $percentage = $total > 0 ? round(($processed / $total) * 100, 1) : 0;

                echo '<p><strong>Progress:</strong> ' . $processed . '/' . $total . ' (' . $percentage . '%)</p>';

                if ($name === 'Adventures') {
                    echo '<p><strong>Imported:</strong> ' . ($progress['imported'] ?? 0) . '</p>';
                    echo '<p><strong>Skipped:</strong> ' . ($progress['skipped'] ?? 0) . '</p>';
                }
            }

            if (!empty($progress['errors'])) {
                echo '<div class="error"><strong>Errors (' . count($progress['errors']) . '):</strong><br>';
                foreach (array_slice($progress['errors'], 0, 5) as $error) {
                    echo '• ' . htmlspecialchars($error) . '<br>';
                }
                echo '</div>';
            }

            if (!empty($progress['debug_logs'])) {
                echo '<div class="warning"><strong>Debug Logs:</strong><br>';
                foreach (array_slice($progress['debug_logs'], -3) as $log) {
                    echo '• ' . htmlspecialchars($log) . '<br>';
                }
                echo '</div>';
            }

            if (isset($progress['last_activity'])) {
                echo '<p><strong>Last Activity:</strong> ' . $progress['last_activity'] . '</p>';
            }
        }

        echo '</div>';
    }

    function testWordPressCron()
    {
        echo '<div class="test-section">';
        echo '<h2>WordPress Cron Test</h2>';

        echo '<p><strong>DISABLE_WP_CRON:</strong> ' . (defined('DISABLE_WP_CRON') ? (DISABLE_WP_CRON ? 'true' : 'false') : 'undefined') . '</p>';

        $scheduled = array(
            'Adventures' => wp_get_scheduled_event('lovetravel_process_adventure_import'),
            'Media' => wp_get_scheduled_event('lovetravel_process_media_import'),
            'Destinations' => wp_get_scheduled_event('lovetravel_process_destinations_import')
        );

        foreach ($scheduled as $name => $event) {
            echo '<h3>' . $name . ' Cron Job</h3>';
            if ($event) {
                echo '<div class="warning">⚠️ <strong>Scheduled:</strong> Next run at ' . date('Y-m-d H:i:s', $event->timestamp) . '</div>';
            } else {
                echo '<div class="success">✅ No scheduled job (normal when not importing)</div>';
            }
        }

        // Test manual cron trigger
        if (isset($_GET['trigger_cron'])) {
            echo '<h3>Manual Cron Trigger Test</h3>';
            $step = sanitize_text_field($_GET['trigger_cron']);

            switch ($step) {
                case 'adventures':
                    do_action('lovetravel_process_adventure_import');
                    echo '<div class="success">✅ Adventure processing triggered</div>';
                    break;
                case 'media':
                    do_action('lovetravel_process_media_import');
                    echo '<div class="success">✅ Media processing triggered</div>';
                    break;
                case 'destinations':
                    do_action('lovetravel_process_destinations_import');
                    echo '<div class="success">✅ Destinations processing triggered</div>';
                    break;
            }
        } else {
            echo '<p><a href="?test=cron&trigger_cron=adventures">Trigger Adventure Processing</a> | ';
            echo '<a href="?test=cron&trigger_cron=media">Trigger Media Processing</a> | ';
            echo '<a href="?test=cron&trigger_cron=destinations">Trigger Destinations Processing</a></p>';
        }

        echo '</div>';
    }

    function testManualBatch()
    {
        echo '<div class="test-section">';
        echo '<h2>Manual Batch Processing Test</h2>';

        if (!isset($_GET['run_batch'])) {
            echo '<p><a href="?test=manual_batch&run_batch=adventures">Test Adventure Batch</a> | ';
            echo '<a href="?test=manual_batch&run_batch=media">Test Media Batch</a> | ';
            echo '<a href="?test=manual_batch&run_batch=destinations">Test Destinations Batch</a></p>';
            return;
        }

        $batch_type = sanitize_text_field($_GET['run_batch']);

        // Initialize wizard class
        $wizard = new LoveTravel_Child_Setup_Wizard();

        try {
            switch ($batch_type) {
                case 'adventures':
                    echo '<h3>Testing Adventure Import</h3>';
                    $wizard->process_background_adventure_import();
                    echo '<div class="success">✅ Adventure processing completed</div>';
                    break;
                case 'media':
                    echo '<h3>Testing Media Import</h3>';
                    $wizard->process_background_media_import();
                    echo '<div class="success">✅ Media processing completed</div>';
                    break;
                case 'destinations':
                    echo '<h3>Testing Destinations Import</h3>';
                    $wizard->process_background_destinations_import();
                    echo '<div class="success">✅ Destinations processing completed</div>';
                    break;
            }
        } catch (Exception $e) {
            echo '<div class="error">❌ <strong>Error:</strong> ' . $e->getMessage() . '</div>';
        }

        echo '</div>';
    }

    function resetAllProgress()
    {
        if (!isset($_GET['confirm_reset'])) {
            echo '<div class="test-section">';
            echo '<h2>Reset All Progress</h2>';
            echo '<div class="warning">⚠️ This will clear all wizard progress and cached data.</div>';
            echo '<p><a href="?test=reset&confirm_reset=1" onclick="return confirm(\'Are you sure?\')">Confirm Reset</a></p>';
            echo '</div>';
            return;
        }

        echo '<div class="test-section">';
        echo '<h2>Resetting All Progress</h2>';

        // Clear all wizard data
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

        echo '<div class="success">✅ All wizard progress cleared successfully</div>';
        echo '<p><a href="?test=progress">Check Progress Status</a></p>';
        echo '</div>';
    }

    function validateImportedData()
    {
        echo '<div class="test-section">';
        echo '<h2>Validate Imported Data</h2>';

        // Check adventures
        $adventures = get_posts(array(
            'post_type' => 'nd_travel_cpt_1',
            'post_status' => 'publish',
            'numberposts' => -1
        ));

        echo '<h3>Adventures (' . count($adventures) . ' found)</h3>';
        if (!empty($adventures)) {
            foreach (array_slice($adventures, 0, 5) as $adventure) {
                echo '<p>• <strong>' . $adventure->post_title . '</strong> (ID: ' . $adventure->ID . ')</p>';
            }
            if (count($adventures) > 5) {
                echo '<p>... and ' . (count($adventures) - 5) . ' more</p>';
            }
        }

        // Check media
        $media = get_posts(array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'meta_query' => array(array(
                'key' => 'payload_media_id',
                'compare' => 'EXISTS'
            )),
            'numberposts' => -1
        ));

        echo '<h3>Imported Media (' . count($media) . ' found)</h3>';

        // Check destinations
        $destinations = get_posts(array(
            'post_type' => 'nd_travel_cpt_2',
            'post_status' => 'publish',
            'numberposts' => -1
        ));

        echo '<h3>Destinations (' . count($destinations) . ' found)</h3>';

        echo '</div>';
    }

    ?>

    <p><a href="?test=menu">← Back to Menu</a></p>

</body>

</html>