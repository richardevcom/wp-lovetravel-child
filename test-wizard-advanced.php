<?php
/**
 * LoveTravel Setup Wizard Test Suite
 * Validates the new single-adventure import with collision handling
 */

class LoveTravel_Wizard_Tests {
    
    /**
     * Test pre-import cleanup functionality
     */
    public static function test_cleanup_recent_media() {
        echo "=== Testing Pre-Import Cleanup ===\n";
        
        // Create test media file from 1 day ago
        $test_attachment = wp_insert_attachment(array(
            'post_title' => 'Test Media File',
            'post_content' => '',
            'post_status' => 'inherit',
            'post_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'meta_input' => array('payload_media_id' => 'test123')
        ));
        
        echo "Created test attachment: {$test_attachment}\n";
        
        // Initialize wizard and test cleanup
        $wizard = new LoveTravel_Child_Setup_Wizard();
        $reflection = new ReflectionClass($wizard);
        $cleanup_method = $reflection->getMethod('cleanup_recent_media_files');
        $cleanup_method->setAccessible(true);
        
        $deleted_count = $cleanup_method->invoke($wizard);
        echo "Deleted {$deleted_count} recent media files\n";
        
        // Verify test file was deleted
        $attachment_exists = get_post($test_attachment);
        echo $attachment_exists ? "❌ Test file still exists\n" : "✅ Test file deleted successfully\n";
        
        return !$attachment_exists;
    }
    
    /**
     * Test adventure collision detection
     */
    public static function test_adventure_collision_detection() {
        echo "\n=== Testing Adventure Collision Detection ===\n";
        
        // Create existing adventure
        $existing_adventure = wp_insert_post(array(
            'post_title' => 'Test Adventure',
            'post_name' => 'test-adventure',
            'post_type' => 'nd_travel_cpt_1',
            'post_status' => 'publish'
        ));
        
        echo "Created existing adventure: {$existing_adventure}\n";
        
        // Test collision detection
        $wizard = new LoveTravel_Child_Setup_Wizard();
        $reflection = new ReflectionClass($wizard);
        $collision_method = $reflection->getMethod('check_adventure_collision');
        $collision_method->setAccessible(true);
        
        $test_data = array('title' => 'Test Adventure', 'slug' => 'test-adventure');
        $collision = $collision_method->invoke($wizard, $test_data);
        
        if ($collision && $collision['existing_id'] === $existing_adventure) {
            echo "✅ Collision detected correctly\n";
            $result = true;
        } else {
            echo "❌ Collision detection failed\n";
            $result = false;
        }
        
        // Cleanup
        wp_delete_post($existing_adventure, true);
        
        return $result;
    }
    
    /**
     * Test media filename preservation
     */
    public static function test_media_filename_preservation() {
        echo "\n=== Testing Media Filename Preservation ===\n";
        
        $wizard = new LoveTravel_Child_Setup_Wizard();
        $reflection = new ReflectionClass($wizard);
        $import_method = $reflection->getMethod('import_single_media_with_preserved_name');
        $import_method->setAccessible(true);
        
        // Create a test image URL (using a placeholder service)
        $test_url = 'https://via.placeholder.com/150/0000FF/808080?text=Test';
        $test_filename = 'test-image.png';
        $test_post_id = wp_insert_post(array(
            'post_title' => 'Test Adventure',
            'post_type' => 'nd_travel_cpt_1',
            'post_status' => 'publish'
        ));
        
        $media_info = array(
            'type' => 'featured',
            'alt' => 'Test Image'
        );
        
        $result = $import_method->invoke($wizard, $test_url, $test_filename, $test_post_id, $media_info);
        
        if ($result['success']) {
            $attachment_file = get_attached_file($result['attachment_id']);
            $actual_filename = basename($attachment_file);
            
            if ($actual_filename === $test_filename) {
                echo "✅ Filename preserved correctly: {$actual_filename}\n";
                $test_result = true;
            } else {
                echo "❌ Filename not preserved. Expected: {$test_filename}, Got: {$actual_filename}\n";
                $test_result = false;
            }
            
            // Cleanup
            wp_delete_attachment($result['attachment_id'], true);
        } else {
            echo "❌ Media import failed: " . $result['message'] . "\n";
            $test_result = false;
        }
        
        wp_delete_post($test_post_id, true);
        return $test_result;
    }
    
    /**
     * Test AJAX response structure
     */
    public static function test_ajax_response_structure() {
        echo "\n=== Testing AJAX Response Structure ===\n";
        
        // Mock progress data
        $mock_progress = array(
            'status' => 'processing',
            'processed' => 5,
            'total_adventures' => 10,
            'errors' => array(),
            'debug_logs' => array('Test log'),
            'last_activity' => current_time('mysql'),
            'started_at' => current_time('mysql'),
            'retry_count' => 0,
            'deleted_recent' => 3,
            'collisions' => array(
                array('type' => 'adventure', 'title' => 'Test Adventure')
            )
        );
        
        // Set up mock progress
        update_option('lovetravel_adventure_import_progress', $mock_progress);
        
        // Test AJAX handler (simulate request)
        $_POST['nonce'] = wp_create_nonce('lovetravel_wizard_nonce');
        $_POST['step'] = 'adventures';
        
        $wizard = new LoveTravel_Child_Setup_Wizard();
        
        // Capture output
        ob_start();
        $wizard->ajax_get_import_progress();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        
        $required_fields = array('step', 'status', 'processed', 'total', 'deleted_recent', 'collisions');
        $missing_fields = array();
        
        foreach ($required_fields as $field) {
            if (!isset($response['data'][$field])) {
                $missing_fields[] = $field;
            }
        }
        
        if (empty($missing_fields)) {
            echo "✅ All required fields present in AJAX response\n";
            $result = true;
        } else {
            echo "❌ Missing fields: " . implode(', ', $missing_fields) . "\n";
            $result = false;
        }
        
        // Cleanup
        delete_option('lovetravel_adventure_import_progress');
        
        return $result;
    }
    
    /**
     * Run all tests
     */
    public static function run_all_tests() {
        echo "LoveTravel Setup Wizard Test Suite\n";
        echo "===================================\n";
        
        $tests = array(
            'test_cleanup_recent_media',
            'test_adventure_collision_detection', 
            'test_media_filename_preservation',
            'test_ajax_response_structure'
        );
        
        $passed = 0;
        $total = count($tests);
        
        foreach ($tests as $test) {
            if (self::$test()) {
                $passed++;
            }
        }
        
        echo "\n=== TEST RESULTS ===\n";
        echo "Passed: {$passed}/{$total}\n";
        
        if ($passed === $total) {
            echo "✅ All tests passed!\n";
        } else {
            echo "❌ Some tests failed. Please review the implementation.\n";
        }
        
        return $passed === $total;
    }
}

// CLI test runner
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('lovetravel test-wizard', 'LoveTravel_Wizard_Tests::run_all_tests');
}

// Web interface test runner
if (isset($_GET['run_tests']) && current_user_can('manage_options')) {
    LoveTravel_Wizard_Tests::run_all_tests();
}
?>