<?php
/**
 * Payload Media Import Admin Page Template - v4.0 Fixed
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Payload Media Import v4.0 - Fixed</h1>
    <hr class="wp-header-end">
    
    <div id="import-notices"></div>
    
    <div class="notice notice-info">
        <p><strong>Fixed Version:</strong> Proper state management, reliable Start/Stop functionality, and file-based logging.</p>
    </div>
    
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            
            <!-- Statistics Panel -->
            <div id="postbox-container-1" class="postbox-container">
                <div class="postbox">
                    <h2 class="hndle">Media Statistics</h2>
                    <div class="inside">
                        <table class="widefat">
                            <tr>
                                <td><strong>Payload CMS Files:</strong></td>
                                <td id="payload-count"><?php echo esc_html($stats['payload_count']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>WordPress Media:</strong></td>
                                <td id="wp-count"><?php echo esc_html($stats['wp_count']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Already Imported:</strong></td>
                                <td id="imported-count"><?php echo esc_html($stats['imported_count']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Remaining:</strong></td>
                                <td id="remaining-count"><?php echo esc_html($stats['remaining_count']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Import Status Panel -->
                <?php if ($state['status'] !== 'idle'): ?>
                <div class="postbox">
                    <h2 class="hndle">Import Status</h2>
                    <div class="inside">
                        <p><strong>Status:</strong> <span id="import-status" class="status-<?php echo esc_attr($state['status']); ?>"><?php echo esc_html(ucfirst($state['status'])); ?></span></p>
                        <p><strong>Job ID:</strong> <span id="job-id"><?php echo esc_html($state['job_id'] ?? 'N/A'); ?></span></p>
                        <p><strong>Progress:</strong> <span id="import-progress"><?php echo esc_html($state['current_index']); ?>/<?php echo esc_html($state['total']); ?></span></p>
                        <p><strong>Current File:</strong> <span id="current-file"><?php echo esc_html($state['current_file'] ?? '-'); ?></span></p>
                        <div class="progress-bar">
                            <?php $progress = $state['total'] > 0 ? ($state['current_index'] / $state['total']) * 100 : 0; ?>
                            <div id="progress-fill" style="width: <?php echo esc_attr($progress); ?>%; background: #0073aa; height: 20px; transition: width 0.3s;"></div>
                        </div>
                        <div style="margin-top: 10px;">
                            <?php if ($state['status'] === 'running'): ?>
                                <button type="button" id="stop-import" class="button button-secondary">Stop Import</button>
                            <?php endif; ?>
                            <button type="button" id="reset-import" class="button">Reset State</button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Main Content -->
            <div id="postbox-container-2" class="postbox-container">
                
                <!-- Import Controls -->
                <?php if ($state['status'] === 'idle'): ?>
                <div class="postbox">
                    <h2 class="hndle">Start Import</h2>
                    <div class="inside">
                        <form id="import-form">
                            <table class="form-table">
                                <tr>
                                    <th><label for="batch-size">Batch Size:</label></th>
                                    <td>
                                        <select id="batch-size" name="batch_size">
                                            <option value="10">10 files</option>
                                            <option value="25" selected>25 files</option>
                                            <option value="50">50 files</option>
                                            <option value="100">100 files</option>
                                        </select>
                                        <p class="description">Number of files to process per batch.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="skip-existing">Skip Existing:</label></th>
                                    <td>
                                        <input type="checkbox" id="skip-existing" name="skip_existing" checked>
                                        <label for="skip-existing">Skip files that are already imported</label>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="generate-thumbnails">Generate Thumbnails:</label></th>
                                    <td>
                                        <input type="checkbox" id="generate-thumbnails" name="generate_thumbnails" checked>
                                        <label for="generate-thumbnails">Generate WordPress thumbnails</label>
                                    </td>
                                </tr>
                            </table>
                            <p class="submit">
                                <button type="submit" class="button button-primary">Start Import</button>
                            </p>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Import Log -->
                <div class="postbox">
                    <h2 class="hndle">Import Log</h2>
                    <div class="inside">
                        <div id="import-log" style="height: 300px; overflow-y: auto; background: #f9f9f9; padding: 10px; border: 1px solid #ddd; font-family: monospace; font-size: 12px;">
                            <div>Ready to start import...</div>
                        </div>
                        <p style="margin-top: 10px;">
                            <button type="button" id="refresh-log" class="button button-small">Refresh Log</button>
                        </p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    let statusCheckInterval = null;
    let currentStatus = '<?php echo esc_js($state['status']); ?>';
    
    // Form submission
    $('#import-form').on('submit', function(e) {
        e.preventDefault();
        startImport();
    });
    
    // Stop import
    $('#stop-import').on('click', function() {
        stopImport();
    });
    
    // Reset import
    $('#reset-import').on('click', function() {
        resetImport();
    });
    
    // Refresh log
    $('#refresh-log').on('click', function() {
        refreshLog();
    });
    
    function startImport() {
        const formData = {
            action: 'payload_import_start',
            nonce: payloadImport.nonce,
            batch_size: $('#batch-size').val(),
            skip_existing: $('#skip-existing').is(':checked') ? '1' : '0',
            generate_thumbnails: $('#generate-thumbnails').is(':checked') ? '1' : '0'
        };
        
        updateLog('🚀 Starting import...');
        
        $.post(payloadImport.ajax_url, formData)
            .done(function(response) {
                if (response.success) {
                    updateLog('✅ Import started successfully - Job ID: ' + response.data.job_id);
                    currentStatus = 'running';
                    
                    // Reload page to show import status
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    updateLog('❌ Failed to start import: ' + (response.data.message || 'Unknown error'));
                }
            })
            .fail(function() {
                updateLog('❌ AJAX request failed');
            });
    }
    
    function stopImport() {
        $.post(payloadImport.ajax_url, {
            action: 'payload_import_stop',
            nonce: payloadImport.nonce
        })
        .done(function(response) {
            if (response.success) {
                updateLog('🛑 Stop requested - import will stop after current batch');
                $('#import-status').text('Stopping').removeClass().addClass('status-stopping');
                $('#stop-import').prop('disabled', true).text('Stopping...');
            } else {
                updateLog('❌ Failed to stop import: ' + (response.data.message || 'Unknown error'));
            }
        })
        .fail(function() {
            updateLog('❌ Stop request failed');
        });
    }
    
    function resetImport() {
        if (!confirm('Are you sure you want to reset the import state? This will clear all progress.')) {
            return;
        }
        
        $.post(payloadImport.ajax_url, {
            action: 'payload_import_reset',
            nonce: payloadImport.nonce
        })
        .done(function(response) {
            if (response.success) {
                updateLog('� Import state reset');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                updateLog('❌ Failed to reset import: ' + (response.data.message || 'Unknown error'));
            }
        })
        .fail(function() {
            updateLog('❌ Reset request failed');
        });
    }
    
    function startStatusCheck() {
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
        }
        
        statusCheckInterval = setInterval(function() {
            $.post(payloadImport.ajax_url, {
                action: 'payload_import_status',
                nonce: payloadImport.nonce
            })
            .done(function(response) {
                if (response.success) {
                    updateImportStatus(response.data.state);
                    updateLogFromResponse(response.data.logs);
                    refreshStats();
                }
            })
            .catch(function() {
                // Silently fail status checks to avoid spam
            });
        }, 3000); // Check every 3 seconds
    }
    
    function updateImportStatus(state) {
        if (!state) return;
        
        $('#import-status').text(state.status.charAt(0).toUpperCase() + state.status.slice(1))
            .removeClass().addClass('status-' + state.status);
        $('#job-id').text(state.job_id || 'N/A');
        $('#import-progress').text((state.current_index || 0) + '/' + (state.total || 0));
        $('#current-file').text(state.current_file || '-');
        
        // Update progress bar
        if (state.total > 0) {
            const percent = ((state.current_index || 0) / state.total) * 100;
            $('#progress-fill').css('width', percent + '%');
        }
        
        // Update status class and button states
        if (state.status !== currentStatus) {
            currentStatus = state.status;
            
            if (state.status === 'stopped' || state.status === 'completed' || state.status === 'error') {
                clearInterval(statusCheckInterval);
                statusCheckInterval = null;
                
                // Reload page after completion
                setTimeout(function() {
                    location.reload();
                }, 3000);
            }
        }
    }
    
    function updateLogFromResponse(logs) {
        if (!logs || logs.length === 0) return;
        
        const logContainer = $('#import-log');
        
        // Clear and add new logs
        logContainer.empty();
        logs.forEach(function(logLine) {
            if (logLine.trim()) {
                logContainer.append('<div>' + $('<div>').text(logLine).html() + '</div>');
            }
        });
        
        // Scroll to bottom
        logContainer.scrollTop(logContainer[0].scrollHeight);
    }
    
    function refreshLog() {
        $.post(payloadImport.ajax_url, {
            action: 'payload_import_status',
            nonce: payloadImport.nonce
        })
        .done(function(response) {
            if (response.success && response.data.logs) {
                updateLogFromResponse(response.data.logs);
            }
        });
    }
    
    function refreshStats() {
        $.post(payloadImport.ajax_url, {
            action: 'payload_import_stats',
            nonce: payloadImport.nonce
        })
        .done(function(response) {
            if (response.success && response.data.stats) {
                const stats = response.data.stats;
                $('#payload-count').text(stats.payload_count || 0);
                $('#wp-count').text(stats.wp_count || 0);
                $('#imported-count').text(stats.imported_count || 0);
                $('#remaining-count').text(stats.remaining_count || 0);
            }
        });
    }
    
    function updateLog(message) {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = '[' + timestamp + '] ' + message;
        const logContainer = $('#import-log');
        
        logContainer.append('<div>' + $('<div>').text(logEntry).html() + '</div>');
        logContainer.scrollTop(logContainer[0].scrollHeight);
    }
    
    // Start status checking if import is active
    if (currentStatus === 'running' || currentStatus === 'stopping') {
        startStatusCheck();
        refreshLog(); // Load current logs
    }
});
</script>

<style>
.progress-bar {
    background: #f0f0f0;
    border: 1px solid #ccc;
    border-radius: 3px;
    overflow: hidden;
    margin: 10px 0;
}

#import-log div {
    margin-bottom: 2px;
    line-height: 1.4;
}

#import-log div:nth-child(even) {
    background: rgba(0,0,0,0.02);
}

.postbox .inside table.widefat td {
    padding: 8px 10px;
}

.status-idle { color: #666; }
.status-running { color: #0073aa; font-weight: bold; }
.status-stopping { color: #ffb900; font-weight: bold; }
.status-stopped { color: #d63638; }
.status-completed { color: #00a32a; font-weight: bold; }
.status-error { color: #d63638; font-weight: bold; }

button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>