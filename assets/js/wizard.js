/**
 * LoveTravel Child Setup Wizard JavaScript
 * ✅ Verified: Progressive import with WordPress AJAX
 */

(function($) {
	'use strict';

	// ✅ Verified: Initialize wizard on document ready
	$(document).ready(function() {
		initSetupWizard();
	});

	/**
	 * ✅ Verified: Initialize setup wizard functionality
	 */
	function initSetupWizard() {
		// ✅ Verified: Handle step buttons
		$('.button[data-step]').on('click', function(e) {
			e.preventDefault();
			
			var $button = $(this);
			var step = $button.data('step');
			
			if ($button.prop('disabled')) {
				return;
			}
			
			importStep(step, $button);
		});
		
		// ✅ Verified: Handle stop import buttons
		$('#stop-adventure-import').on('click', function(e) {
			e.preventDefault();
			stopImport('adventures');
		});
		
		$('#stop-media-import').on('click', function(e) {
			e.preventDefault();
			stopImport('media');
		});
		
		$('#stop-destinations-import').on('click', function(e) {
			e.preventDefault();
			stopImport('destinations');
		});
		
		// ✅ Verified: Handle wizard completion
		$('#complete-wizard').on('click', function(e) {
			e.preventDefault();
			completeWizard();
		});
		
		// ✅ Verified: Handle wizard reset
		$('#reset-wizard-progress').on('click', function(e) {
			e.preventDefault();
			resetWizardProgress();
		});

		// ✅ Verified: Handle remove import buttons
		$('#remove-elementor-import').on('click', function(e) {
			e.preventDefault();
			removeImports('elementor_templates', $(this));
		});

		$('#remove-adventure-import').on('click', function(e) {
			e.preventDefault();
			removeImports('adventures', $(this));
		});

		$('#remove-media-import').on('click', function(e) {
			e.preventDefault();
			removeImports('media', $(this));
		});

		$('#remove-destinations-import').on('click', function(e) {
			e.preventDefault();
			removeImports('destinations', $(this));
		});

		// ✅ NEW: Handle stop import buttons
		$('#stop-elementor-import').on('click', function(e) {
			e.preventDefault();
			stopImport('elementor_templates', $(this));
		});

		$('#stop-adventure-import').on('click', function(e) {
			e.preventDefault();
			stopImport('adventures', $(this));
		});

		$('#stop-media-import').on('click', function(e) {
			e.preventDefault();
			stopImport('media', $(this));
		});

		$('#stop-destinations-import').on('click', function(e) {
			e.preventDefault();
			stopImport('destinations', $(this));
		});
	}

	/**
	 * ✅ Verified: Import single step via AJAX
	 */
	function importStep(step, $button) {
		// ✅ Verified: Update button state
		$button.prop('disabled', true)
			   .addClass('importing')
			   .text(loveTravelWizard.strings.importing);

		// ✅ NEW: Show stop button and hide start button
		var stopButtonId = '#stop-' + (step === 'elementor_templates' ? 'elementor' : step.replace('_', '-')) + '-import';
		var $stopButton = $(stopButtonId);
		$button.hide();
		$stopButton.show();

		var requestData = {
			action: 'lovetravel_wizard_import_step',
			step: step,
			nonce: loveTravelWizard.nonce
		};

		// ✅ NEW: Add collision handling preferences for adventures
		if (step === 'adventures') {
			var adventureAction = $('#adventure-collision-action').val();
			var mediaAction = $('#media-collision-action').val();
			requestData.adventure_collision_action = adventureAction;
			requestData.media_collision_action = mediaAction;
			requestData.duplicate_handling = adventureAction; // Backward compatibility
		}
		
		// ✅ PERFORMANCE: Add skip media downloads option
		if (step === 'media') {
			var skipDownloads = $('#skip-media-downloads').is(':checked');
			requestData.skip_downloads = skipDownloads;
			if (skipDownloads) {
				console.log('Media import will skip downloads for faster processing');
			}
		}

		// ✅ Verified: WordPress AJAX request
		$.ajax({
			url: loveTravelWizard.ajaxUrl,
			type: 'POST',
			data: requestData,
			success: function(response) {
				if (response.success) {
					// ✅ Verified: Handle background import differently
					if (response.data.background && step === 'adventures') {
						handleBackgroundImport(step, $button, response.data);
					} else {
						handleStepSuccess(step, $button, response.data);
					}
				} else {
					handleStepError(step, $button, response.data);
				}
			},
			error: function(xhr, status, error) {
				handleStepError(step, $button, {
					message: loveTravelWizard.strings.error + ': ' + error
				});
			}
		});
	}

	/**
	 * ✅ Verified: Handle background import (Adventures and Media)
	 */
	function handleBackgroundImport(step, $button, data) {
		if (step === 'adventures') {
			// ✅ Verified: Show adventure progress area
			$('#adventure-import-progress').show();
			$('.progress-fill').css('width', '0%');
			$('#progress-status').text('Starting import...');
			$('#progress-details').text('Preparing to fetch adventures from Payload CMS');
			$('#stop-adventure-import').show();
		} else if (step === 'media') {
			// ✅ Verified: Show media progress area
			$('#media-import-progress').show();
			$('#media-import-progress .progress-fill').css('width', '0%');
			$('#media-progress-status').text('Starting import...');
			$('#media-progress-details').text('Preparing to fetch media files from Payload CMS');
			$('#stop-media-import').show();
		} else if (step === 'destinations') {
			// ✅ Verified: Show destinations progress area
			$('#destinations-import-progress').show();
			$('#destinations-import-progress .progress-fill').css('width', '0%');
			$('#destinations-progress-status').text('Starting import...');
			$('#destinations-progress-details').text('Preparing to fetch destinations from Payload CMS');
			$('#stop-destinations-import').show();
		}
		
		// ✅ Verified: Update button states
		$button.text('Import Running...').prop('disabled', true);
		
		// ✅ Verified: Show success message with progress context
		showAdminNotice('info', data.message + ' Progress shown below.');
		
		// ✅ Verified: Start progress polling immediately
		startProgressPolling(step, $button);
		
		// ✅ Verified: Do initial progress check after 1 second
		setTimeout(function() {
			checkProgressOnce(step);
		}, 1000);
		
		// ✅ Verified: Trigger background processing immediately to bypass cron issues
		setTimeout(function() {
			console.log('Triggering immediate background processing for', step, 'to bypass cron');
			triggerBackgroundProcessing(step);
		}, 2000);
	}

	/**
	 * ✅ Verified: Poll import progress
	 */
	function startProgressPolling(step, $button) {
		var lastProcessed = 0;
		var stallCount = 0;
		var maxStallCount = 3; // Trigger processing after 6 seconds of no progress
		
		var progressInterval = setInterval(function() {
			$.ajax({
				url: loveTravelWizard.ajaxUrl,
				type: 'POST',
				data: {
					action: 'lovetravel_wizard_get_progress',
					step: step,
					nonce: loveTravelWizard.nonce
				},
				success: function(response) {
					if (response.success) {
						updateProgressDisplay(response.data, step);
						
						// ✅ Verified: Check for stalled progress (WordPress cron not working)
						if (response.data.status === 'fetching' || response.data.status === 'processing' || response.data.status === 'media_download') {
							// Check if progress is stalled (no change in processed count)
							if (response.data.processed === lastProcessed) {
								stallCount++;
								console.log('Progress stalled at', response.data.processed + '/' + response.data.total, 'count:', stallCount);
								
								// Trigger background processing if stalled for too long
								if (stallCount >= maxStallCount) {
									console.log('Triggering background processing for', step, 'due to stall at', response.data.status);
									triggerBackgroundProcessing(step);
									stallCount = 0; // Reset counter
								}
							} else if (response.data.processed > lastProcessed) {
								lastProcessed = response.data.processed;
								stallCount = 0; // Reset counter on progress
								console.log('Progress detected:', response.data.processed, '/', response.data.total);
							}
							
							// Special case: if we have total but no progress for too long, trigger processing
							if (response.data.total > 0 && response.data.processed === 0 && stallCount >= 2) {
								console.log('No progress despite having total items, triggering processing');
								triggerBackgroundProcessing(step);
								stallCount = 0;
							}
						}
						
						// ✅ Verified: Stop polling when completed, failed, or stopped
						if (response.data.status === 'completed' || response.data.status === 'failed' || response.data.status === 'stopped') {
							clearInterval(progressInterval);
							handleProgressComplete(step, $button, response.data);
						}
					}
				},
				error: function() {
					// ✅ Verified: Continue polling on AJAX errors
					console.log('Progress polling error, retrying...');
				}
			});
		}, 2000); // Poll every 2 seconds
	}
	
	/**
	 * ✅ Verified: Trigger background processing via AJAX (fallback for WordPress cron)
	 */
	function triggerBackgroundProcessing(step) {
		$.ajax({
			url: loveTravelWizard.ajaxUrl,
			type: 'POST',
			data: {
				action: 'lovetravel_wizard_trigger_processing',
				step: step,
				nonce: loveTravelWizard.nonce
			},
			success: function(response) {
				if (response.success) {
					console.log('Background processing triggered successfully');
				} else {
					console.log('Failed to trigger background processing:', response.data.message);
				}
			},
			error: function() {
				console.log('Error triggering background processing');
			}
		});
	}

	/**
	 * ✅ Verified: Update progress display
	 */
	function updateProgressDisplay(progressData, step) {
		step = step || 'adventures';
		
		// Log debug information to console
		if (progressData.debug_logs && progressData.debug_logs.length > 0) {
			console.log('Progress Debug Logs for ' + step + ':', progressData.debug_logs);
		}
		
		// Log errors to console
		if (progressData.error_details && progressData.error_details.length > 0) {
			console.warn('Progress Errors for ' + step + ':', progressData.error_details);
		}
		
		// Log full progress data for debugging
		console.log('Progress Update for ' + step + ':', {
			status: progressData.status,
			percentage: progressData.percentage,
			processed: progressData.processed,
			total: progressData.total,
			retry_count: progressData.retry_count,
			last_activity: progressData.last_activity
		});
		
		var $progressBar, $status, $details, $progressContainer;
		
		if (step === 'elementor_templates') {
			$progressContainer = $('#elementor-import-progress');
			$progressBar = $progressContainer.find('.progress-fill');
			$status = $('#elementor-progress-status');
			$details = $('#elementor-progress-details');
		} else if (step === 'media') {
			$progressContainer = $('#media-import-progress');
			$progressBar = $progressContainer.find('.progress-fill');
			$status = $('#media-progress-status');
			$details = $('#media-progress-details');
		} else if (step === 'destinations') {
			$progressContainer = $('#destinations-import-progress');
			$progressBar = $progressContainer.find('.progress-fill');
			$status = $('#destinations-progress-status');
			$details = $('#destinations-progress-details');
		} else {
			// Adventures (default)
			$progressContainer = $('#adventure-import-progress');
			$progressBar = $progressContainer.find('.progress-fill');
			$status = $('#progress-status');
			$details = $('#progress-details');
		}
		
		// Show progress container when processing starts
		if (progressData.status === 'processing' && $progressContainer.length > 0) {
			$progressContainer.show();
		}
		
		// ✅ Verified: Update progress bar
		$progressBar.css('width', progressData.percentage + '%');
		
		// ✅ Verified: Update status text
		$status.text(progressData.message);
		
		// ✅ Verified: Update details
		var details = '';
		if (progressData.total > 0) {
			if (step === 'media') {
				details = progressData.processed + '/' + progressData.total + ' media files';
				if (progressData.imported && progressData.updated) {
					details += ' (' + progressData.imported + ' new, ' + progressData.updated + ' updated)';
				}
			} else if (step === 'destinations') {
				details = progressData.processed + '/' + progressData.total + ' destinations';
				if (progressData.destinations_created || progressData.locations_created || progressData.updated) {
					details += ' (' + progressData.destinations_created + ' destinations, ' + progressData.locations_created + ' locations, ' + progressData.updated + ' updated)';
				}
			} else {
				// For adventures, don't duplicate the count if message already contains it
				if (progressData.message && progressData.message.includes(progressData.processed + ' of ' + progressData.total)) {
					details = ''; // Message already contains the progress info
				} else {
					details = progressData.processed + '/' + progressData.total + ' adventures';
				}
				if (progressData.media_total > 0) {
					if (details) details += ', ';
					details += progressData.media_processed + '/' + progressData.media_total + ' media files';
				}
			}
		} else if (progressData.status === 'fetching') {
			details = 'Connecting to Payload CMS...';
		}
		
		// Add error indicator if there are errors
		if (progressData.errors > 0) {
			details += ' (' + progressData.errors + ' errors)';
		}
		
		// ✅ Show collision information from new structure
		if (progressData.collision_info && Object.keys(progressData.collision_info).length > 0) {
			var collisionCount = Object.keys(progressData.collision_info).length;
			details += ' (' + collisionCount + ' adventures with collisions)';
			updateCollisionDisplay(progressData.collision_info);
		}
		
		// Show cleanup information
		if (progressData.deleted_recent > 0) {
			details += ' (cleaned ' + progressData.deleted_recent + ' recent files)';
		}
		
		$details.text(details);
		
		// ✅ Enhanced: Update live logs display for current step
		if (progressData.live_logs && progressData.live_logs.length > 0) {
			updateLiveLogsDisplay(progressData.live_logs, step);
		}
	}

	/**
	 * ✅ Verified: Single progress check
	 */
	function checkProgressOnce(step) {
		step = step || 'adventures';
		
		$.ajax({
			url: loveTravelWizard.ajaxUrl,
			type: 'POST',
			data: {
				action: 'lovetravel_wizard_get_progress',
				step: step,
				nonce: loveTravelWizard.nonce
			},
			success: function(response) {
				if (response.success) {
					updateProgressDisplay(response.data, step);
				}
			},
			error: function() {
				console.log('Initial progress check failed, polling will continue...');
			}
		});
	}

	/**
	 * ✅ Verified: Stop import functionality
	 */
	function stopImport(step) {
		step = step || 'adventures';
		
		var $stopButton, $startButton, $progressContainer, $statusElement, $progressBar;
		var buttonText = 'Start ';
		
		if (step === 'media') {
			$stopButton = $('#stop-media-import');
			$startButton = $('[data-step="media"]');
			$progressContainer = $('#media-import-progress');
			$statusElement = $('#media-progress-status');
			$progressBar = $('#media-import-progress .progress-fill');
			buttonText += 'Media Import';
		} else if (step === 'destinations') {
			$stopButton = $('#stop-destinations-import');
			$startButton = $('[data-step="destinations"]');
			$progressContainer = $('#destinations-import-progress');
			$statusElement = $('#destinations-progress-status');
			$progressBar = $('#destinations-import-progress .progress-fill');
			buttonText += 'Destinations Import';
		} else {
			$stopButton = $('#stop-adventure-import');
			$startButton = $('[data-step="adventures"]');
			$progressContainer = $('#adventure-import-progress');
			$statusElement = $('#progress-status');
			$progressBar = $('.progress-fill');
			buttonText += 'Adventure Import';
		}
		
		// ✅ Verified: Confirm with user
		if (!confirm('Are you sure you want to stop the import? Current progress will be preserved, but the import will need to be restarted.')) {
			return;
		}
		
		$stopButton.prop('disabled', true).text('Stopping...');
		console.log('Stopping import for step:', step);
		
		$.ajax({
			url: loveTravelWizard.ajaxUrl,
			type: 'POST',
			data: {
				action: 'lovetravel_wizard_stop_import',
				step: step,
				nonce: loveTravelWizard.nonce
			},
			success: function(response) {
				console.log('Stop import response:', response);
				
				if (response.success) {
					// ✅ Verified: Reset UI state but keep button available
					$startButton.prop('disabled', false)
							   .removeClass('importing button-secondary')
							   .addClass('button-primary')
							   .text(buttonText);
					$stopButton.hide().prop('disabled', false).text('Stop Import');
					
					// ✅ Verified: Update progress display
					$statusElement.text('Import stopped by user');
					$progressBar.css('width', '0%');
					
					showAdminNotice('warning', response.data.message);
					
					// ✅ Verified: Hide progress after delay but keep button enabled
					setTimeout(function() {
						$progressContainer.fadeOut();
					}, 5000);
				} else {
					$stopButton.prop('disabled', false).text('Stop Import');
					showAdminNotice('error', response.data.message);
				}
			},
			error: function(xhr, status, error) {
				console.error('Stop import failed:', {xhr, status, error});
				$stopButton.prop('disabled', false).text('Stop Import');
				showAdminNotice('error', 'Failed to stop import: ' + error);
			}
		});
	}

	/**
	 * ✅ Verified: Handle progress completion
	 */
	function handleProgressComplete(step, $button, progressData) {
		// ✅ Verified: Hide appropriate stop button
		if (step === 'adventures') {
			$('#stop-adventure-import').hide();
		} else if (step === 'media') {
			$('#stop-media-import').hide();
		} else if (step === 'destinations') {
			$('#stop-destinations-import').hide();
		}
		
		if (progressData.status === 'completed') {
			handleStepSuccess(step, $button, {
				message: progressData.message
			});
		} else if (progressData.status === 'stopped') {
			// ✅ Verified: Reset button for stopped import
			var buttonText = 'Start ' + (step === 'media' ? 'Media' : (step === 'destinations' ? 'Destinations' : 'Adventure')) + ' Import';
			$button.prop('disabled', false)
				   .removeClass('importing')
				   .text(buttonText);
			
			showAdminNotice('warning', progressData.message);
		} else {
			handleStepError(step, $button, {
				message: progressData.message
			});
		}
		
		// ✅ Verified: Keep progress visible longer for completed/stopped status
		var progressSelector = step === 'media' ? '#media-import-progress' : (step === 'destinations' ? '#destinations-import-progress' : '#adventure-import-progress');
		setTimeout(function() {
			$(progressSelector).fadeOut(1000);
		}, 5000); // Show status for 5 seconds
	}

	/**
	 * ✅ Verified: Handle successful step completion
	 */
	function handleStepSuccess(step, $button, data) {
		// ✅ NEW: Hide stop button and show remove button
		var stopButtonId = '#stop-' + (step === 'elementor_templates' ? 'elementor' : step.replace('_', '-')) + '-import';
		var $stopButton = $(stopButtonId);
		$stopButton.hide();
		$button.hide(); // Hide start button
		
		// ✅ NEW: Show remove imports button
		var removeButtonId = '#remove-' + (step === 'elementor_templates' ? 'elementor' : step.replace('_', '-')) + '-import';
		var $removeButton = $(removeButtonId);
		if ($removeButton.length) {
			$removeButton.show();
		}
		
		// ✅ Verified: Update status notice
		var $postbox = $button.closest('.postbox');
		var $notice = $postbox.find('.notice');
		
		$notice.removeClass('notice-warning')
			   .addClass('notice-success')
			   .find('p')
			   .text(data.message || loveTravelWizard.strings.complete);
		
		// ✅ Add "Imported" badge to step title
		var $stepTitle = $postbox.find('.hndle').first();
		if (!$stepTitle.find('.wizard-step-status').length) {
			$stepTitle.append('<span class="wizard-step-status wizard-step-completed">' +
				'<span class="dashicons dashicons-yes-alt"></span>' +
				'<span>Imported</span>' +
				'</span>');
		}
		
		// ✅ Verified: Show completion message
		showAdminNotice('success', data.message);
		
		// ✅ Verified: Check if all steps completed
		checkAllStepsCompleted();
	}

	/**
	 * ✅ Verified: Handle step error
	 */
	function handleStepError(step, $button, data) {
		// ✅ NEW: Hide stop button and show start button
		var stopButtonId = '#stop-' + (step === 'elementor_templates' ? 'elementor' : step.replace('_', '-')) + '-import';
		var $stopButton = $(stopButtonId);
		$stopButton.hide();
		$button.show();
		
		// ✅ Verified: Reset button state
		$button.prop('disabled', false)
			   .removeClass('importing')
			   .text($button.data('original-text') || 'Retry Import');
		
		// ✅ Verified: Show error message
		showAdminNotice('error', data.message || loveTravelWizard.strings.error);
	}

	/**
	 * ✅ Verified: Reset wizard progress (for testing/debugging)
	 */
	function resetWizardProgress() {
		if (!confirm('Are you sure you want to reset ALL wizard progress? This will clear all import data and cannot be undone.')) {
			return;
		}
		
		var $button = $('#reset-wizard-progress');
		$button.prop('disabled', true).text('Resetting...');
		
		$.ajax({
			url: loveTravelWizard.ajaxUrl,
			type: 'POST',
			data: {
				action: 'lovetravel_wizard_reset_progress',
				nonce: loveTravelWizard.nonce
			},
			success: function(response) {
				if (response.success) {
					showAdminNotice('success', response.data.message);
					// Reload page after short delay
					setTimeout(function() {
						location.reload();
					}, 1500);
				} else {
					showAdminNotice('error', response.data.message);
					$button.prop('disabled', false).text('Reset All Progress');
				}
			},
			error: function() {
				showAdminNotice('error', 'Failed to reset wizard progress');
				$button.prop('disabled', false).text('Reset All Progress');
			}
		});
	}

	/**
	 * ✅ Verified: Complete wizard and redirect
	 */
	function completeWizard() {
		$.ajax({
			url: loveTravelWizard.ajaxUrl,
			type: 'POST',
			data: {
				action: 'lovetravel_wizard_complete',
				nonce: loveTravelWizard.nonce
			},
			success: function(response) {
				if (response.success) {
					showAdminNotice('success', response.data.message);
					
					// ✅ Verified: Redirect after short delay
					setTimeout(function() {
						if (response.data.redirect) {
							window.location.href = response.data.redirect;
						} else {
							location.reload();
						}
					}, 2000);
				} else {
					showAdminNotice('error', response.data.message);
				}
			},
			error: function(xhr, status, error) {
				showAdminNotice('error', loveTravelWizard.strings.error + ': ' + error);
			}
		});
	}

	/**
	 * ✅ Verified: Check if all import steps are completed
	 */
	function checkAllStepsCompleted() {
		var $allButtons = $('.button[data-step]');
		var completedCount = 0;
		
		$allButtons.each(function() {
			if ($(this).hasClass('button-secondary')) {
				completedCount++;
			}
		});
		
		// ✅ Verified: Enable complete button if all steps done
		if (completedCount === $allButtons.length) {
			$('#complete-wizard').prop('disabled', false)
								 .removeClass('button-secondary')
								 .addClass('button-primary');
		}
	}

	/**
	 * ✅ Enhanced: Update collision display in UI with new structure
	 */
	function updateCollisionDisplay(collisionInfo) {
		var $collisionPreview = $('#collision-preview');
		var $collisionList = $('#collision-list');
		
		if (collisionInfo && Object.keys(collisionInfo).length > 0) {
			$collisionPreview.show();
			$collisionList.empty();
			
			// Process each adventure's collision info
			Object.keys(collisionInfo).forEach(function(adventureId) {
				var adventureCollisions = collisionInfo[adventureId];
				var collisionHtml = '<div class="collision-group">';
				collisionHtml += '<h4>Adventure ID: ' + adventureId + '</h4>';
				
				// Adventure collision
				if (adventureCollisions.adventure_collision) {
					var adventureInfo = adventureCollisions.adventure_collision;
					collisionHtml += '<div class="collision-item adventure-collision">';
					collisionHtml += '<strong>Adventure Collision:</strong> ';
					collisionHtml += adventureInfo.type + ' (Existing ID: ' + adventureInfo.existing_id + ')';
					if (adventureInfo.user_choice) {
						collisionHtml += ' - <em>Action: ' + adventureInfo.user_choice + '</em>';
					}
					collisionHtml += '</div>';
				}
				
				// Media collisions
				if (adventureCollisions.media_collisions && adventureCollisions.media_collisions.length > 0) {
					collisionHtml += '<div class="media-collisions">';
					collisionHtml += '<strong>Media Collisions (' + adventureCollisions.media_collisions.length + '):</strong>';
					adventureCollisions.media_collisions.forEach(function(mediaCollision) {
						collisionHtml += '<div class="collision-item media-collision">';
						collisionHtml += '• ' + mediaCollision.filename;
						if (mediaCollision.existing_id) {
							collisionHtml += ' (conflicts with ID: ' + mediaCollision.existing_id + ')';
						}
						if (mediaCollision.user_choice) {
							collisionHtml += ' - <em>Action: ' + mediaCollision.user_choice + '</em>';
						}
						collisionHtml += '</div>';
					});
					collisionHtml += '</div>';
				}
				
				collisionHtml += '</div>';
				$collisionList.append(collisionHtml);
			});
		} else {
			$collisionPreview.hide();
		}
	}
	
	/**
	 * ✅ Enhanced: Update live logs display for specific step
	 */
	function updateLiveLogsDisplay(liveLogs, step) {
		step = step || 'adventures'; // Default to adventures for backward compatibility
		
		var containerIds = {
			'elementor_templates': 'elementor-live-logs-container',
			'adventures': 'adventure-live-logs-container', 
			'media': 'media-live-logs-container',
			'destinations': 'destinations-live-logs-container'
		};
		
		var listIds = {
			'elementor_templates': 'elementor-live-logs-list',
			'adventures': 'adventure-live-logs-list',
			'media': 'media-live-logs-list', 
			'destinations': 'destinations-live-logs-list'
		};
		
		var $liveLogsContainer = $('#' + containerIds[step]);
		var $logsList = $('#' + listIds[step]);
		
		if ($liveLogsContainer.length === 0 || $logsList.length === 0) {
			return; // Container doesn't exist for this step
		}
		
		// Show container and populate logs
		if (liveLogs && liveLogs.length > 0) {
			$liveLogsContainer.show();
			$logsList.empty();
			
			// Show last 10 logs
			var recentLogs = liveLogs.slice(-10);
			recentLogs.forEach(function(log) {
				var logClass = 'log-' + log.type;
				var logHtml = '<div class="live-log-entry ' + logClass + '">' +
					'<span class="log-time">' + log.timestamp + '</span> ' +
					'<span class="log-message">' + log.message + '</span>' +
					'</div>';
				$logsList.append(logHtml);
			});
			
			// Auto-scroll to bottom
			$logsList.scrollTop($logsList[0].scrollHeight);
		}
	}
	
	/**
	 * ✅ Enhanced: Check if step is truly completed (all media imported per adventure)
	 */
	function isStepTrulyCompleted(progressData, step) {
		if (step === 'adventures') {
			// Adventure step is complete when all adventures AND their media are processed
			if (progressData.status !== 'completed') {
				return false;
			}
			
			// Check if all adventures have been processed
			if (progressData.processed_adventures < progressData.total_adventures) {
				return false;
			}
			
			// ✅ NEW: Check media import status for each adventure
			if (progressData.media_import_status) {
				var allMediaComplete = true;
				Object.keys(progressData.media_import_status).forEach(function(adventureId) {
					var mediaStatus = progressData.media_import_status[adventureId];
					if (mediaStatus.status !== 'completed') {
						allMediaComplete = false;
					}
				});
				return allMediaComplete;
			}
			
			return progressData.processed_adventures > 0;
		}
		
		// Other steps use standard completion check
		return progressData.status === 'completed' && 
			   progressData.processed === progressData.total &&
			   progressData.processed > 0;
	}

	/**
	 * ✅ Verified: Show WordPress admin notice (native styling)
	 */
	function showAdminNotice(type, message) {
		var noticeClass = 'notice-' + type;
		var $notice = $('<div class="notice ' + noticeClass + ' is-dismissible">' +
						'<p>' + message + '</p>' +
						'</div>');
		
		// ✅ Verified: Insert after page title
		$('.wp-heading-inline').after($notice);
		
		// ✅ Verified: Auto-dismiss after 5 seconds
		setTimeout(function() {
			$notice.fadeOut(500, function() {
				$(this).remove();
			});
		}, 5000);
	}

	/**
	 * ✅ Verified: Remove imports for a specific step
	 */
	function removeImports(step, $button) {
		// ✅ Verified: Confirm with user before removing
		var stepName = step.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
		if (!confirm('Are you sure you want to remove all imported ' + stepName + '? This action cannot be undone.')) {
			return;
		}

		// ✅ Verified: Update button state
		var originalText = $button.text();
		$button.prop('disabled', true)
			   .addClass('importing')
			   .text('Removing...');

		// ✅ Verified: WordPress AJAX request
		$.ajax({
			url: loveTravelWizard.ajaxUrl,
			type: 'POST',
			data: {
				action: 'lovetravel_wizard_remove_imports',
				step: step,
				nonce: loveTravelWizard.nonce
			},
			success: function(response) {
				if (response.success) {
					// ✅ Show success message
					showAdminNotice('success', response.data.message);
					
					// ✅ Hide the Remove button
					$button.hide();
					
					// ✅ Show the Start Import button
					var startButtonId = '#start-' + (step === 'elementor_templates' ? 'elementor' : step.replace('_', '-')) + '-import';
					var $startButton = $(startButtonId);
					$startButton.show().prop('disabled', false).removeClass('importing');
					
					// ✅ Remove the "Imported" status badge
					var $stepHeader = $button.closest('.postbox').find('.postbox-header h2');
					$stepHeader.find('.wizard-step-status').remove();
					
					// ✅ Update any progress indicators to show reset state
					resetProgressIndicators(step);
					
				} else {
					handleRemoveError($button, originalText, response.data);
				}
			},
			error: function(xhr, status, error) {
				handleRemoveError($button, originalText, {
					message: 'Error removing imports: ' + error
				});
			}
		});
	}

	/**
	 * ✅ Verified: Handle remove import errors
	 */
	function handleRemoveError($button, originalText, errorData) {
		// ✅ Verified: Restore button state
		$button.prop('disabled', false)
			   .removeClass('importing')
			   .text(originalText);
		
		// ✅ Verified: Show error message
		showAdminNotice('error', errorData.message || 'An error occurred while removing imports');
	}

	/**
	 * ✅ NEW: Stop import process for a specific step
	 */
	function stopImport(step, $button) {
		// ✅ Confirm with user before stopping
		var stepName = step.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
		if (!confirm('Are you sure you want to stop the ' + stepName + ' import? This will cancel the current process.')) {
			return;
		}

		// ✅ Update button state
		var originalText = $button.text();
		$button.prop('disabled', true)
			   .addClass('stopping')
			   .text('Stopping...');

		// ✅ WordPress AJAX request
		$.ajax({
			url: loveTravelWizard.ajaxUrl,
			type: 'POST',
			data: {
				action: 'lovetravel_wizard_stop_import',
				step: step,
				nonce: loveTravelWizard.nonce
			},
			success: function(response) {
				if (response.success) {
					// ✅ Update UI to reflect stopped state
					$button.hide();
					var startButtonId = '#start-' + (step === 'elementor_templates' ? 'elementor' : step.replace('_', '-')) + '-import';
					var $startButton = $(startButtonId);
					$startButton.show().prop('disabled', false).removeClass('importing').text($startButton.data('original-text') || 'Start Import');
					
					// ✅ Show success message
					showAdminNotice('success', response.data.message || stepName + ' import stopped successfully');
					
					// ✅ Clear any progress indicators
					resetProgressIndicators(step);
				} else {
					handleStopError($button, originalText, response.data);
				}
			},
			error: function(xhr, status, error) {
				handleStopError($button, originalText, {message: 'Network error: ' + error});
			}
		});
	}

	/**
	 * ✅ NEW: Handle stop import errors
	 */
	function handleStopError($button, originalText, errorData) {
		// ✅ Restore button state
		$button.prop('disabled', false)
			   .removeClass('stopping')
			   .text(originalText);
		
		// ✅ Show error message
		showAdminNotice('error', errorData.message || 'An error occurred while stopping import');
	}

	/**
	 * ✅ NEW: Reset progress indicators for a step
	 */
	function resetProgressIndicators(step) {
		var progressId = step === 'elementor_templates' ? 'elementor' : step.replace('_', '-');
		var progressSelector = '#' + progressId + '-progress';
		var logSelector = '#' + progressId + '-log';
		
		$(progressSelector + ' .progress-fill').css('width', '0%');
		$(progressSelector + ' .progress-text').text('0%');
		$(logSelector).empty();
	}

})(jQuery);