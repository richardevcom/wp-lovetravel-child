<?php
/**
 * ✅ Clean up test media files from 21.09.2025 to 24.09.2025
 * DANGER: This will permanently delete media files!
 */

// Load WordPress if not already loaded
if (!defined('ABSPATH')) {
    // Navigate up to WordPress root from themes/lovetravel-child
    $wp_root = dirname(dirname(dirname(dirname(__FILE__))));
    require_once($wp_root . '/wp-load.php');
}

echo "Media Cleanup Script - DANGER ZONE\n";
echo "==================================\n";
echo "This will delete ALL media files added between 21.09.2025 and 24.09.2025\n\n";

// Define date range
$start_date = '2025-09-21 00:00:00';
$end_date = '2025-09-24 23:59:59';

echo "Date range: {$start_date} to {$end_date}\n";

// First, let's see what we're dealing with
$media_query = new WP_Query(array(
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => -1,
    'date_query' => array(
        array(
            'after' => $start_date,
            'before' => $end_date,
            'inclusive' => true,
        ),
    ),
    'fields' => 'ids'
));

$media_ids = $media_query->posts;
$total_count = count($media_ids);

echo "Found {$total_count} media files to delete\n";

if ($total_count === 0) {
    echo "✅ No media files found in the specified date range.\n";
    exit;
}

// Show a sample of what will be deleted
echo "\nSample of files to be deleted:\n";
$sample_size = min(10, $total_count);
for ($i = 0; $i < $sample_size; $i++) {
    $attachment = get_post($media_ids[$i]);
    $file_path = get_attached_file($media_ids[$i]);
    $filename = basename($file_path);
    echo "  - {$filename} (ID: {$media_ids[$i]}, Date: {$attachment->post_date})\n";
}

if ($total_count > $sample_size) {
    echo "  ... and " . ($total_count - $sample_size) . " more files\n";
}

echo "\n⚠️  WARNING: This action cannot be undone!\n";
echo "Press ENTER to continue or Ctrl+C to abort...\n";

// Wait for user input (only works in CLI)
if (php_sapi_name() === 'cli') {
    $handle = fopen("php://stdin", "r");
    $confirmation = fgets($handle);
    fclose($handle);
} else {
    echo "Running in web mode - proceeding automatically...\n";
}

echo "\n🗑️  Starting deletion process...\n";

$deleted_count = 0;
$error_count = 0;
$batch_size = 50;

// Process in batches
for ($i = 0; $i < $total_count; $i += $batch_size) {
    $batch = array_slice($media_ids, $i, $batch_size);
    
    foreach ($batch as $media_id) {
        $attachment = get_post($media_id);
        $filename = basename(get_attached_file($media_id));
        
        // Delete the attachment (this also deletes the physical file)
        $result = wp_delete_attachment($media_id, true);
        
        if ($result) {
            $deleted_count++;
            echo "✅ Deleted: {$filename} (ID: {$media_id})\n";
        } else {
            $error_count++;
            echo "❌ Failed to delete: {$filename} (ID: {$media_id})\n";
        }
    }
    
    // Progress update
    $progress = min($i + $batch_size, $total_count);
    $percentage = round(($progress / $total_count) * 100, 1);
    echo "\nProgress: {$progress}/{$total_count} ({$percentage}%)\n";
    
    // Small delay to prevent overwhelming the system
    usleep(100000); // 0.1 seconds
}

echo "\n=== Cleanup Complete ===\n";
echo "✅ Successfully deleted: {$deleted_count} files\n";
echo "❌ Failed to delete: {$error_count} files\n";
echo "📊 Total processed: " . ($deleted_count + $error_count) . " files\n";

// Clean up WordPress caches
wp_cache_flush();
echo "✅ WordPress caches flushed\n";

// Final verification
$verification_query = new WP_Query(array(
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => -1,
    'date_query' => array(
        array(
            'after' => $start_date,
            'before' => $end_date,
            'inclusive' => true,
        ),
    ),
    'fields' => 'ids'
));

$remaining_count = $verification_query->found_posts;
echo "🔍 Verification: {$remaining_count} files remaining in date range\n";

if ($remaining_count === 0) {
    echo "🎉 All media files successfully cleaned up!\n";
} else {
    echo "⚠️  Some files may still remain - check manually if needed\n";
}

echo "\nCleanup operation completed.\n";