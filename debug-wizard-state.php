<?php
/**
 * Debug script to analyze wizard state and progress tracking
 * Run this via browser to see current import status
 */

// WordPress bootstrap
require_once('../../../wp-config.php');

echo "<h1>LoveTravel Wizard Debug Report</h1>";
echo "<pre>";

echo "=== WIZARD IMPORT STATUS ===\n";
$import_status = get_option('lovetravel_import_status', array());
var_dump($import_status);

echo "\n=== ADVENTURE IMPORT PROGRESS ===\n";
$adventure_progress = get_option('lovetravel_adventure_import_progress', array());
var_dump($adventure_progress);

echo "\n=== MEDIA IMPORT PROGRESS ===\n";
$media_progress = get_option('lovetravel_media_import_progress', array());
var_dump($media_progress);

echo "\n=== DESTINATIONS IMPORT PROGRESS ===\n";
$destinations_progress = get_option('lovetravel_destinations_import_progress', array());
var_dump($destinations_progress);

echo "\n=== SCHEDULED CRON JOBS ===\n";
$cron_jobs = wp_get_scheduled_event('lovetravel_process_adventure_import');
echo "Adventure Import Cron: ";
var_dump($cron_jobs);

$media_cron = wp_get_scheduled_event('lovetravel_process_media_import');
echo "Media Import Cron: ";
var_dump($media_cron);

$destinations_cron = wp_get_scheduled_event('lovetravel_process_destinations_import');
echo "Destinations Import Cron: ";
var_dump($destinations_cron);

echo "\n=== API CONNECTIVITY TEST ===\n";
$api_url = 'https://tribetravel.eu/api/adventures/?limit=1';
echo "Testing: " . $api_url . "\n";

$response = wp_remote_get($api_url, array('timeout' => 10));
echo "Response Code: " . wp_remote_retrieve_response_code($response) . "\n";

if (is_wp_error($response)) {
    echo "Error: " . $response->get_error_message() . "\n";
} else {
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    echo "API Response Sample:\n";
    if (isset($data['docs']) && !empty($data['docs'])) {
        echo "Total docs available: " . (isset($data['totalDocs']) ? $data['totalDocs'] : 'unknown') . "\n";
        echo "First adventure title: " . ($data['docs'][0]['title'] ?? 'no title') . "\n";
    } else {
        echo "No docs found or invalid response\n";
        echo substr($body, 0, 500) . "\n";
    }
}

echo "\n=== WORDPRESS CRON STATUS ===\n";
echo "DISABLE_WP_CRON: " . (defined('DISABLE_WP_CRON') ? (DISABLE_WP_CRON ? 'true' : 'false') : 'undefined') . "\n";
echo "Current time: " . current_time('mysql') . "\n";
echo "GMT time: " . current_time('mysql', true) . "\n";

echo "</pre>";
?>