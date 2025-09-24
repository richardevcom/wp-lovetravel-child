<?php
/**
 * Quick Status Check for Adventure Import
 */
require_once('../../../wp-load.php');

$progress = get_option('lovetravel_adventure_import_progress', array());

echo "=== CURRENT ADVENTURE IMPORT STATUS ===\n";
echo "Status: " . ($progress['status'] ?? 'none') . "\n";
echo "Current Batch: " . ($progress['current_batch'] ?? 0) . "\n";
echo "Processed: " . ($progress['processed'] ?? 0) . "\n";
echo "Total: " . ($progress['total_adventures'] ?? 0) . "\n";
echo "Imported: " . ($progress['imported'] ?? 0) . "\n";
echo "Skipped: " . ($progress['skipped'] ?? 0) . "\n";
echo "Errors: " . count($progress['errors'] ?? []) . "\n";
echo "Last Activity: " . ($progress['last_activity'] ?? 'none') . "\n";

if (!empty($progress['debug_logs'])) {
    echo "\n=== RECENT DEBUG LOGS ===\n";
    foreach (array_slice($progress['debug_logs'], -5) as $log) {
        echo "- " . $log . "\n";
    }
}

if (!empty($progress['errors'])) {
    echo "\n=== RECENT ERRORS ===\n";
    foreach (array_slice($progress['errors'], -3) as $error) {
        echo "- " . $error . "\n";
    }
}

// Test if we can trigger processing manually
if (isset($_GET['trigger'])) {
    echo "\n=== TRIGGERING MANUAL PROCESSING ===\n";
    try {
        $wizard = new LoveTravel_Child_Setup_Wizard();
        $wizard->process_background_adventure_import();
        echo "✅ Processing triggered successfully\n";
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

echo "\n=== ACTIONS ===\n";
echo "Trigger processing: Add ?trigger=1 to URL\n";
?>