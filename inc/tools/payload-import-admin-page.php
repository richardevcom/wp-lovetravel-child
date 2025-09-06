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
                            <div id="progress-fill" class="payload-progress-fill" style="width: <?php echo esc_attr($progress); ?>%;"></div>
                        </div>
                        <div class="import-actions" style="margin-top:10px;">
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
                        <div id="import-log" aria-live="polite">
                            <div>Ready to start import...</div>
                        </div>
                        <p class="import-log-controls" style="margin-top:10px;">
                            <button type="button" id="refresh-log" class="button button-small">Refresh Log</button>
                        </p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<!-- Inline JS & CSS moved to external assets: admin-payload-import.js & admin-tools.css -->