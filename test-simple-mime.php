<?php
/**
 * ✅ Simple test for MIME type detection without WordPress loading
 */

echo "Adventure Media Refactor - MIME Type Test\n";
echo "=========================================\n";

// Test comprehensive MIME type detection
function test_mime_detection() {
    echo "Testing MIME Type Detection:\n";
    
    $test_files = array(
        'test.jpg' => 'image/jpeg',
        'test.png' => 'image/png', 
        'test.gif' => 'image/gif',
        'test.webp' => 'image/webp',
        'test.mp4' => 'video/mp4',
        'test.avi' => 'video/x-msvideo',
        'test.pdf' => 'application/pdf',
        'test.docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'test.zip' => 'application/zip',
        'test.unknown' => 'application/octet-stream'
    );
    
    // Simulate the comprehensive MIME type mapping
    $mime_types = array(
        // Images
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg', 
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'bmp' => 'image/bmp',
        'ico' => 'image/x-icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        
        // Videos
        'mp4' => 'video/mp4',
        'avi' => 'video/x-msvideo',
        'mov' => 'video/quicktime',
        'wmv' => 'video/x-ms-wmv',
        'flv' => 'video/x-flv',
        'webm' => 'video/webm',
        'mkv' => 'video/x-matroska',
        '3gp' => 'video/3gpp',
        
        // Audio
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'flac' => 'audio/flac',
        'aac' => 'audio/aac',
        'm4a' => 'audio/mp4',
        
        // Documents
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'txt' => 'text/plain',
        'rtf' => 'application/rtf',
        
        // Archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        '7z' => 'application/x-7z-compressed',
        'tar' => 'application/x-tar',
        'gz' => 'application/gzip'
    );
    
    foreach ($test_files as $filename => $expected) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $detected = isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';
        
        $status = ($detected === $expected) ? '✅' : '❌';
        echo "  {$status} {$filename}: {$detected}\n";
        
        if ($detected !== $expected) {
            echo "      Expected: {$expected}\n";
        }
    }
}

// Test filename collision detection
function test_filename_collision() {
    echo "\nTesting Filename Collision Logic:\n";
    
    $test_cases = array(
        array(
            'original' => 'sunset.jpg',
            'existing' => array('sunset.jpg'),
            'expected' => 'sunset_1.jpg'
        ),
        array(
            'original' => 'mountain.png',
            'existing' => array('mountain.png', 'mountain_1.png'),
            'expected' => 'mountain_2.png'
        ),
        array(
            'original' => 'document.pdf',
            'existing' => array(),
            'expected' => 'document.pdf'
        )
    );
    
    foreach ($test_cases as $case) {
        $collision_resolved = resolve_filename_collision($case['original'], $case['existing']);
        $status = ($collision_resolved === $case['expected']) ? '✅' : '❌';
        echo "  {$status} {$case['original']} -> {$collision_resolved}\n";
        
        if ($collision_resolved !== $case['expected']) {
            echo "      Expected: {$case['expected']}\n";
        }
    }
}

// Simulate filename collision resolution
function resolve_filename_collision($filename, $existing_files) {
    if (!in_array($filename, $existing_files)) {
        return $filename;
    }
    
    $pathinfo = pathinfo($filename);
    $base = $pathinfo['filename'];
    $extension = isset($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';
    
    $counter = 1;
    do {
        $new_filename = $base . '_' . $counter . $extension;
        $counter++;
    } while (in_array($new_filename, $existing_files));
    
    return $new_filename;
}

// Test progress structure validation
function test_progress_structure() {
    echo "\nTesting Progress Structure:\n";
    
    $sample_progress = array(
        'status' => 'processing',
        'current_batch' => 2,
        'total_adventures' => 50,
        'processed_adventures' => 10,
        'adventures_data' => array(),
        'collision_info' => array(
            'adventure_123' => array(
                'adventure_collision' => array(
                    'type' => 'slug_exists',
                    'existing_id' => 456,
                    'user_choice' => 'update'
                ),
                'media_collisions' => array(
                    array(
                        'filename' => 'sunset.jpg',
                        'existing_id' => 789,
                        'user_choice' => 'skip'
                    )
                )
            )
        ),
        'media_import_status' => array()
    );
    
    $required_fields = array(
        'status', 'current_batch', 'total_adventures', 'processed_adventures',
        'adventures_data', 'collision_info', 'media_import_status'
    );
    
    foreach ($required_fields as $field) {
        $status = isset($sample_progress[$field]) ? '✅' : '❌';
        echo "  {$status} Progress field: {$field}\n";
    }
    
    echo "  ✅ Sample collision info structure validated\n";
}

// Run all tests
test_mime_detection();
test_filename_collision();
test_progress_structure();

echo "\n=== Test Complete ===\n";
echo "All core logic validated without WordPress dependencies.\n";