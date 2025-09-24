<?php
/**
 * ✅ Simple media cleanup script for Docker WordPress
 */

// Load WordPress 
require_once('/var/www/html/wp-load.php');

echo "Media Cleanup for Test Files (Sept 21-24)\n";
echo "==========================================\n";

$start_date = '2025-09-21 00:00:00';
$end_date = '2025-09-24 23:59:59';

// Get media files in date range
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
    echo "✅ No media files found in specified date range.\n";
    exit;
}

// Show sample
echo "\nSample files to delete:\n";
for ($i = 0; $i < min(5, $total_count); $i++) {
    $filename = basename(get_attached_file($media_ids[$i]));
    echo "  - {$filename} (ID: {$media_ids[$i]})\n";
}

echo "\n🗑️  Deleting {$total_count} files...\n";

$deleted = 0;
$errors = 0;

foreach ($media_ids as $media_id) {
    $filename = basename(get_attached_file($media_id));
    
    if (wp_delete_attachment($media_id, true)) {
        $deleted++;
        if ($deleted % 100 === 0) {
            echo "Deleted {$deleted}/{$total_count} files...\n";
        }
    } else {
        $errors++;
        echo "❌ Failed: {$filename}\n";
    }
}

echo "\n=== Cleanup Complete ===\n";
echo "✅ Deleted: {$deleted} files\n";
echo "❌ Errors: {$errors} files\n";

wp_cache_flush();
echo "✅ Cache flushed\n";