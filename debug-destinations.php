<?php
/**
 * ⚠️ Debug: Destinations Import Debugging
 * Access via: /wp-content/themes/lovetravel-child/debug-destinations.php
 */

// Load WordPress
require_once dirname(dirname(dirname(__DIR__))) . '/wp-load.php';

if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}

echo '<h1>Destinations Import Debug</h1>';

// ✅ Check post types
echo '<h2>1. Post Type Check</h2>';
echo '<p>nd_travel_cpt_1 (Adventures): ' . (post_type_exists('nd_travel_cpt_1') ? '✅ EXISTS' : '❌ NOT FOUND') . '</p>';
echo '<p>nd_travel_cpt_2 (Destinations): ' . (post_type_exists('nd_travel_cpt_2') ? '✅ EXISTS' : '❌ NOT FOUND') . '</p>';
echo '<p>nd_travel_cpt_3 (Locations): ' . (post_type_exists('nd_travel_cpt_3') ? '✅ EXISTS' : '❌ NOT FOUND') . '</p>';

// ✅ Check API
echo '<h2>2. API Test</h2>';
$api_url = 'https://tribetravel.eu/api/destinations/?limit=3';
echo '<p><strong>API URL:</strong> ' . esc_html($api_url) . '</p>';

$response = wp_remote_get($api_url, array('timeout' => 10));
if (is_wp_error($response)) {
    echo '<p style="color: red;">❌ <strong>API Error:</strong> ' . esc_html($response->get_error_message()) . '</p>';
} else {
    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    echo '<p><strong>Response Code:</strong> ' . esc_html($code) . '</p>';
    
    if ($code === 200) {
        $data = json_decode($body, true);
        if ($data && isset($data['docs'])) {
            echo '<p style="color: green;">✅ <strong>API Success:</strong> Found ' . count($data['docs']) . ' destinations</p>';
            if (!empty($data['docs'])) {
                echo '<h3>First destination data:</h3>';
                echo '<pre style="background: #f0f0f0; padding: 10px; overflow: auto; max-height: 300px;">';
                echo esc_html(print_r($data['docs'][0], true));
                echo '</pre>';
            }
        } else {
            echo '<p style="color: red;">❌ Invalid API response structure</p>';
            echo '<p>Response (first 500 chars): ' . esc_html(substr($body, 0, 500)) . '</p>';
        }
    } else {
        echo '<p style="color: red;">❌ API returned error code: ' . esc_html($code) . '</p>';
        echo '<p>Response: ' . esc_html(substr($body, 0, 500)) . '</p>';
    }
}

// ✅ Check current progress
echo '<h2>3. Current Import Progress</h2>';
$progress = get_option('lovetravel_destinations_import_progress', array());
if (empty($progress)) {
    echo '<p>ℹ️ No progress data found - import not started</p>';
} else {
    echo '<pre style="background: #f0f0f0; padding: 10px; overflow: auto; max-height: 400px;">';
    echo esc_html(print_r($progress, true));
    echo '</pre>';
}

// ✅ Check import status
echo '<h2>4. Import Status</h2>';
$import_status = get_option('lovetravel_import_status', array());
if (empty($import_status)) {
    echo '<p>ℹ️ No import status found</p>';
} else {
    echo '<pre style="background: #f0f0f0; padding: 10px;">';
    echo esc_html(print_r($import_status, true));
    echo '</pre>';
}

// ✅ Check if we can create a test destination
echo '<h2>5. Test Post Creation</h2>';
if (post_type_exists('nd_travel_cpt_2')) {
    echo '<p>✅ Destination post type exists - attempting test creation...</p>';
    
    $test_post_data = array(
        'post_title'   => 'Debug Test Destination',
        'post_name'    => 'debug-test-destination',
        'post_type'    => 'nd_travel_cpt_2',
        'post_status'  => 'draft', // Use draft to avoid cluttering
        'meta_input'   => array(
            'debug_test' => true,
        )
    );
    
    $post_id = wp_insert_post($test_post_data);
    if (is_wp_error($post_id)) {
        echo '<p style="color: red;">❌ Failed to create test post: ' . esc_html($post_id->get_error_message()) . '</p>';
    } else {
        echo '<p style="color: green;">✅ Successfully created test post with ID: ' . esc_html($post_id) . '</p>';
        
        // Clean up test post
        wp_delete_post($post_id, true);
        echo '<p>🧹 Test post cleaned up</p>';
    }
} else {
    echo '<p style="color: red;">❌ Cannot test - nd_travel_cpt_2 post type does not exist</p>';
}

// ✅ Check WP Cron
echo '<h2>6. WP Cron Status</h2>';
if (wp_next_scheduled('lovetravel_process_destinations_import')) {
    echo '<p>⏰ Destinations import cron job is scheduled</p>';
} else {
    echo '<p>ℹ️ No destinations import cron job scheduled</p>';
}

echo '<hr>';
echo '<p><a href="' . admin_url('admin.php?page=lovetravel-setup-wizard') . '">← Back to Setup Wizard</a></p>';
?>