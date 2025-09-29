<?php
/**
 * Import Tool State Testing
 * Temporary test file - DELETE before production
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Only accessible to administrators
if (!current_user_can('manage_options')) {
    wp_die('Access denied');
}

// Header
?>
<!DOCTYPE html>
<html>
<head>
    <title>Import Tool State Test</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, sans-serif; margin: 40px; }
        .test-section { border: 1px solid #ccc; padding: 20px; margin: 20px 0; }
        .status { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .warning { background: #fff3cd; color: #856404; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        button { padding: 10px 20px; margin: 5px; cursor: pointer; }
        .disabled { opacity: 0.6; cursor: not-allowed; }
        pre { background: #f8f9fa; padding: 15px; overflow: auto; font-size: 12px; }
    </style>
</head>
<body>
    <h1>Import Tool State Testing</h1>
    <p><strong>URL:</strong> <code><?php echo esc_url(home_url('/wp-content/themes/lovetravel-child/test-import-state.php')); ?></code></p>
    
    <?php
    // Test 1: Check import status options
    echo '<div class="test-section">';
    echo '<h2>Test 1: Import Status Check</h2>';
    
    $import_status = get_option('lovetravel_import_status', array());
    $adventure_progress = get_option('lovetravel_adventure_import_progress', array());
    $media_progress = get_option('lovetravel_media_import_progress', array());
    $destinations_progress = get_option('lovetravel_destinations_import_progress', array());
    
    echo '<h3>Import Status Options</h3>';
    if (empty($import_status)) {
        echo '<div class="status info">No imports completed yet</div>';
    } else {
        echo '<div class="status success">Import Status: ' . count($import_status) . ' sections completed</div>';
        echo '<pre>' . htmlspecialchars(json_encode($import_status, JSON_PRETTY_PRINT)) . '</pre>';
    }
    
    // Test current progress states
    echo '<h3>Current Progress States</h3>';
    $progress_states = [
        'Adventures' => $adventure_progress,
        'Media' => $media_progress,
        'Destinations' => $destinations_progress
    ];
    
    foreach ($progress_states as $name => $progress) {
        if (empty($progress)) {
            echo "<div class='status info'>{$name}: No active import</div>";
        } else {
            $status = $progress['status'] ?? 'unknown';
            $processed = $progress['processed'] ?? 0;
            $total = isset($progress['total_adventures']) ? $progress['total_adventures'] : 
                    (isset($progress['total_media']) ? $progress['total_media'] : 
                    (isset($progress['total_destinations']) ? $progress['total_destinations'] : 0));
            
            echo "<div class='status warning'>{$name}: {$status} ({$processed}/{$total})</div>";
        }
    }
    echo '</div>';
    
    // Test 2: Simulate Button States
    echo '<div class="test-section">';
    echo '<h2>Test 2: Button State Simulation</h2>';
    echo '<p>This simulates how buttons should behave in different states:</p>';
    
    // Simulate wizard button states
    $sections = ['elementor_templates', 'adventures', 'media', 'destinations'];
    
    foreach ($sections as $section) {
        $is_imported = isset($import_status[$section]);
        $section_name = ucfirst(str_replace('_', ' ', $section));
        
        echo "<div style='border: 1px solid #ddd; padding: 15px; margin: 10px 0;'>";
        echo "<h4>{$section_name}</h4>";
        
        if (!$is_imported) {
            echo "<button id='import-{$section}' onclick='simulateImport(\"{$section}\")'>Import {$section_name}</button>";
            echo "<button id='stop-{$section}' style='display:none;' class='disabled'>Stop Import</button>";
            echo "<div class='status info'>Ready to import</div>";
        } else {
            echo "<button id='remove-{$section}' onclick='simulateRemove(\"{$section}\")' style='background: #dc3545; color: white;'>Remove Imports</button>";
            echo "<div class='status success'>✅ Imported on {$import_status[$section]}</div>";
        }
        echo "</div>";
    }
    echo '</div>';
    
    // Test 3: Notice Display Test
    echo '<div class="test-section">';
    echo '<h2>Test 3: Notice Behavior Test</h2>';
    echo '<button onclick="showTestNotice(\'success\', \'This is a success notice - should NOT auto-dismiss\')">Show Success Notice</button>';
    echo '<button onclick="showTestNotice(\'error\', \'This is an error notice - should NOT auto-dismiss\')">Show Error Notice</button>';
    echo '<button onclick="showTestNotice(\'warning\', \'This is a warning notice - should NOT auto-dismiss\')">Show Warning Notice</button>';
    echo '<div id="notice-container"></div>';
    echo '</div>';
    
    // Test 4: API Endpoint Test
    echo '<div class="test-section">';
    echo '<h2>Test 4: API Connectivity</h2>';
    echo '<button onclick="testAPIEndpoint(\'adventures\')">Test Adventures API</button>';
    echo '<button onclick="testAPIEndpoint(\'media\')">Test Media API</button>';
    echo '<button onclick="testAPIEndpoint(\'destinations\')">Test Destinations API</button>';
    echo '<div id="api-results"></div>';
    echo '</div>';
    ?>

    <script>
        function simulateImport(section) {
            const importBtn = document.getElementById('import-' + section);
            const stopBtn = document.getElementById('stop-' + section);
            
            // Simulate button state change
            importBtn.style.display = 'none';
            stopBtn.style.display = 'inline-block';
            stopBtn.textContent = 'Importing...';
            stopBtn.classList.add('disabled');
            
            // Show notice
            showTestNotice('info', 'Starting ' + section + ' import...');
            
            // Simulate completion after 3 seconds
            setTimeout(() => {
                importBtn.textContent = 'Remove Imports';
                importBtn.id = 'remove-' + section;
                importBtn.style.display = 'inline-block';
                importBtn.style.background = '#dc3545';
                importBtn.style.color = 'white';
                importBtn.onclick = () => simulateRemove(section);
                
                stopBtn.style.display = 'none';
                
                showTestNotice('success', section + ' import completed successfully!');
            }, 3000);
        }
        
        function simulateRemove(section) {
            const removeBtn = document.getElementById('remove-' + section);
            removeBtn.textContent = 'Removing...';
            removeBtn.classList.add('disabled');
            
            showTestNotice('info', 'Removing ' + section + ' imports...');
            
            // Simulate removal after 2 seconds
            setTimeout(() => {
                removeBtn.textContent = 'Import ' + section.replace('_', ' ');
                removeBtn.id = 'import-' + section;
                removeBtn.style.background = '';
                removeBtn.style.color = '';
                removeBtn.classList.remove('disabled');
                removeBtn.onclick = () => simulateImport(section);
                
                showTestNotice('success', section + ' imports removed successfully!');
            }, 2000);
        }
        
        function showTestNotice(type, message) {
            const container = document.getElementById('notice-container');
            const notice = document.createElement('div');
            notice.className = 'status ' + type;
            notice.innerHTML = message + ' <button onclick="this.parentElement.remove()" style="float: right; background: none; border: none; font-size: 16px; cursor: pointer;">&times;</button>';
            container.appendChild(notice);
            
            // DO NOT AUTO-DISMISS - This is what we want to test
            console.log('Notice shown:', type, message, '- NO auto-dismiss');
        }
        
        function testAPIEndpoint(type) {
            const container = document.getElementById('api-results');
            const endpoints = {
                'adventures': 'https://tribetravel.eu/api/adventures/?limit=1',
                'media': 'https://tribetravel.eu/api/media/?limit=1', 
                'destinations': 'https://tribetravel.eu/api/destinations/?limit=1'
            };
            
            const url = endpoints[type];
            showTestNotice('info', 'Testing ' + type + ' API: ' + url);
            
            fetch(url)
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('HTTP ' + response.status);
                })
                .then(data => {
                    showTestNotice('success', type + ' API: ✅ ' + data.totalDocs + ' items available');
                })
                .catch(error => {
                    showTestNotice('error', type + ' API: ❌ ' + error.message);
                });
        }
        
        // Log current setup wizard JavaScript if loaded
        if (typeof loveTravelWizard !== 'undefined') {
            console.log('Setup Wizard JS loaded:', loveTravelWizard);
        } else {
            console.log('Setup Wizard JS not loaded on this page');
        }
    </script>

    <div style="margin-top: 40px; padding: 20px; background: #fff3cd; border: 1px solid #ffeaa7;">
        <h3>⚠️ Important: Delete This File</h3>
        <p>This is a temporary test file. <strong>DELETE</strong> <code>test-import-state.php</code> before going to production!</p>
        <p><strong>File location:</strong> <code>/wp-content/themes/lovetravel-child/test-import-state.php</code></p>
    </div>

</body>
</html>