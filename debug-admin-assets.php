<?php
/**
 * ⚠️ Debug: Admin Asset Loading Test
 * Access via: /wp-content/themes/lovetravel-child/debug-admin-assets.php
 */

// Load WordPress
require_once dirname(dirname(dirname(__DIR__))) . '/wp-load.php';

if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}

echo '<h1>Admin Asset Loading Debug</h1>';

// ✅ Check if files exist
echo '<h2>1. Asset File Existence</h2>';
$assets = array(
    'admin-tools.css' => LOVETRAVEL_CHILD_DIR . '/assets/css/admin-tools.css',
    'wizard.css' => LOVETRAVEL_CHILD_DIR . '/assets/css/wizard.css', 
    'wizard.js' => LOVETRAVEL_CHILD_DIR . '/assets/js/wizard.js',
    'admin-adventures-import.js' => LOVETRAVEL_CHILD_DIR . '/assets/js/admin-adventures-import.js',
    'admin-payload-import.js' => LOVETRAVEL_CHILD_DIR . '/assets/js/admin-payload-import.js',
    'admin-mailchimp-export.js' => LOVETRAVEL_CHILD_DIR . '/assets/js/admin-mailchimp-export.js',
);

foreach ($assets as $name => $path) {
    $exists = file_exists($path);
    $url = str_replace(LOVETRAVEL_CHILD_DIR, LOVETRAVEL_CHILD_URI, $path);
    echo '<p><strong>' . esc_html($name) . ':</strong> ' . 
         ($exists ? '✅ EXISTS' : '❌ NOT FOUND') . 
         ' <code>' . esc_html($path) . '</code></p>';
    if ($exists) {
        echo '<p style="margin-left: 20px; color: #666;">URL: <a href="' . esc_url($url) . '" target="_blank">' . esc_html($url) . '</a></p>';
    }
}

// ✅ Check asset registration 
echo '<h2>2. Asset Registration Status</h2>';
$registered_styles = array('lovetravel-admin-tools', 'lovetravel-wizard');
$registered_scripts = array('lovetravel-wizard', 'lovetravel-adventures-import', 'lovetravel-payload-import', 'lovetravel-mailchimp-export');

foreach ($registered_styles as $handle) {
    $registered = wp_style_is($handle, 'registered');
    $enqueued = wp_style_is($handle, 'enqueued');
    echo '<p><strong>Style ' . esc_html($handle) . ':</strong> ' . 
         ($registered ? '✅ Registered' : '❌ Not Registered') . ' | ' .
         ($enqueued ? '✅ Enqueued' : '❌ Not Enqueued') . '</p>';
}

foreach ($registered_scripts as $handle) {
    $registered = wp_script_is($handle, 'registered');
    $enqueued = wp_script_is($handle, 'enqueued');
    echo '<p><strong>Script ' . esc_html($handle) . ':</strong> ' . 
         ($registered ? '✅ Registered' : '❌ Not Registered') . ' | ' .
         ($enqueued ? '✅ Enqueued' : '❌ Not Enqueued') . '</p>';
}

// ✅ Check current page context
echo '<h2>3. Current Page Context</h2>';
$current_screen = get_current_screen();
echo '<p><strong>Screen ID:</strong> ' . esc_html($current_screen->id ?? 'unknown') . '</p>';
echo '<p><strong>Hook Suffix:</strong> ' . esc_html($_GET['hook'] ?? 'none') . '</p>';

// ✅ Check if admin assets class is loaded
echo '<h2>4. Admin Assets Class</h2>';
if (class_exists('LoveTravel_Admin_Assets')) {
    echo '<p>✅ LoveTravel_Admin_Assets class is loaded</p>';
} else {
    echo '<p>❌ LoveTravel_Admin_Assets class is NOT loaded</p>';
}

// ✅ Test admin-tools.css loading manually
echo '<h2>5. Manual Asset Test</h2>';
if (file_exists(LOVETRAVEL_CHILD_DIR . '/assets/css/admin-tools.css')) {
    echo '<p>✅ admin-tools.css exists - loading inline for preview:</p>';
    echo '<style>';
    echo file_get_contents(LOVETRAVEL_CHILD_DIR . '/assets/css/admin-tools.css');
    echo '</style>';
    
    echo '<div class="wizard-step-status wizard-step-completed" style="margin: 10px 0;">';
    echo '<span class="dashicons dashicons-yes-alt"></span>';
    echo '<span>Test: Admin Tools CSS Loaded</span>';
    echo '</div>';
    
    echo '<div class="wizard-step-status wizard-step-pending" style="margin: 10px 0;">';
    echo '<span class="dashicons dashicons-clock"></span>';
    echo '<span>Test: Pending Status</span>';
    echo '</div>';
} else {
    echo '<p>❌ admin-tools.css not found</p>';
}

echo '<hr>';
echo '<p><a href="' . admin_url('admin.php?page=lovetravel-setup-wizard') . '">→ Go to Setup Wizard</a></p>';
echo '<p><a href="' . admin_url() . '">← Back to Admin</a></p>';
?>