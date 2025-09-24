<?php
/**
 * ✅ Test script for adventure media refactor
 * Tests per-adventure media import, collision handling, and UI progress
 */

// Load WordPress if not already loaded
if (!defined('ABSPATH')) {
    // Navigate up to WordPress root from themes/lovetravel-child
    $wp_root = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($wp_root . '/wp-load.php');
}

// Test comprehensive MIME type detection
function test_mime_type_detection() {
    echo "\n=== Testing MIME Type Detection ===\n";
    
    $test_files = array(
        'test.jpg' => 'image/jpeg',
        'test.png' => 'image/png',
        'test.mp4' => 'video/mp4',
        'test.pdf' => 'application/pdf',
        'test.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'test.unknown' => 'application/octet-stream'
    );
    
    foreach ($test_files as $filename => $expected) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Simulate the MIME detection logic
        $mime_types = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg', 
            'png' => 'image/png',
            'mp4' => 'video/mp4',
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        );
        
        $detected = isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';
        
        $status = ($detected === $expected) ? '✅' : '❌';
        echo "{$status} {$filename}: {$detected} (expected: {$expected})\n";
    }
}

// Test collision detection logic
function test_collision_detection() {
    echo "\n=== Testing Collision Detection ===\n";
    
    // Test adventure slug collision
    $test_slugs = array(
        'amazing-adventure',
        'mountain-trek',
        'beach-paradise'
    );
    
    foreach ($test_slugs as $slug) {
        $existing_post = get_page_by_path($slug, OBJECT, 'nd_travel_cpt_1');
        $status = $existing_post ? '⚠️ COLLISION' : '✅ AVAILABLE';
        echo "{$status} Adventure slug: {$slug}\n";
    }
    
    // Test media filename collision
    $test_filenames = array(
        'sunrise.jpg',
        'mountain-view.png',
        'adventure-map.pdf'
    );
    
    $upload_dir = wp_upload_dir();
    foreach ($test_filenames as $filename) {
        $file_path = $upload_dir['path'] . '/' . $filename;
        $status = file_exists($file_path) ? '⚠️ COLLISION' : '✅ AVAILABLE';
        echo "{$status} Media file: {$filename}\n";
    }
}

// Test progress structure
function test_progress_structure() {
    echo "\n=== Testing Progress Structure ===\n";
    
    $progress = get_option('lovetravel_adventure_import_progress', array());
    
    if (empty($progress)) {
        echo "❌ No adventure import progress found\n";
        return;
    }
    
    $required_fields = array(
        'status', 'current_batch', 'total_adventures', 'processed_adventures',
        'adventures_data', 'collision_info', 'media_import_status'
    );
    
    foreach ($required_fields as $field) {
        $status = isset($progress[$field]) ? '✅' : '❌';
        echo "{$status} Progress field: {$field}\n";
    }
    
    // Show collision info if present
    if (isset($progress['collision_info']) && !empty($progress['collision_info'])) {
        echo "\n--- Collision Info ---\n";
        foreach ($progress['collision_info'] as $adventure_id => $info) {
            echo "Adventure ID {$adventure_id}:\n";
            if (isset($info['adventure_collision'])) {
                echo "  - Adventure collision: {$info['adventure_collision']['type']}\n";
            }
            if (isset($info['media_collisions'])) {
                echo "  - Media collisions: " . count($info['media_collisions']) . "\n";
            }
        }
    }
}

// Test cleanup functionality
function test_cleanup_recent_media() {
    echo "\n=== Testing Recent Media Cleanup ===\n";
    
    // Find media files from last 2 days
    $two_days_ago = date('Y-m-d H:i:s', strtotime('-2 days'));
    
    $media_query = new WP_Query(array(
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => -1,
        'date_query' => array(
            array(
                'after' => $two_days_ago,
                'inclusive' => true,
            ),
        ),
        'meta_query' => array(
            array(
                'key' => 'payload_original_url',
                'compare' => 'EXISTS'
            )
        )
    ));
    
    echo "Found {$media_query->found_posts} recent Payload media files\n";
    
    if ($media_query->have_posts()) {
        echo "Files that would be cleaned up:\n";
        while ($media_query->have_posts()) {
            $media_query->the_post();
            $filename = basename(get_attached_file(get_the_ID()));
            echo "  - {$filename} (ID: " . get_the_ID() . ")\n";
        }
        wp_reset_postdata();
    }
}

// Test gallery meta field structure
function test_gallery_meta_structure() {
    echo "\n=== Testing Gallery Meta Structure ===\n";
    
    // Find adventure posts with gallery content
    $adventures = get_posts(array(
        'post_type' => 'nd_travel_cpt_1',
        'posts_per_page' => 5,
        'meta_query' => array(
            array(
                'key' => 'nd_travel_meta_box_tab_gallery_content',
                'compare' => 'EXISTS'
            )
        )
    ));
    
    echo "Found " . count($adventures) . " adventures with gallery content\n";
    
    foreach ($adventures as $adventure) {
        echo "\nAdventure: {$adventure->post_title} (ID: {$adventure->ID})\n";
        
        $gallery_content = get_post_meta($adventure->ID, 'nd_travel_meta_box_tab_gallery_content', true);
        $gallery_ids = get_post_meta($adventure->ID, '_adventure_gallery', true);
        
        echo "  - Gallery content length: " . strlen($gallery_content) . " chars\n";
        echo "  - Gallery IDs: " . (is_array($gallery_ids) ? count($gallery_ids) : 'not array') . "\n";
        
        if (preg_match_all('/ids="([^"]*)"/', $gallery_content, $matches)) {
            echo "  - Shortcode IDs found: " . implode(', ', $matches[1]) . "\n";
        }
    }
}

// Run all tests
echo "Adventure Media Refactor Test Suite\n";
echo "===================================\n";

test_mime_type_detection();
test_collision_detection();
test_progress_structure();
test_cleanup_recent_media();
test_gallery_meta_structure();

echo "\n=== Test Complete ===\n";
echo "Check results above for any issues that need attention.\n";