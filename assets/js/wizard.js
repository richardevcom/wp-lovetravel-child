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
	}

	/**
	 * ✅ Verified: Import single step via AJAX
	 */
	function importStep(step, $button) {
		// ✅ Verified: Update button state
		$button.prop('disabled', true)
			   .addClass('importing')
			   .text(loveTravelWizard.strings.importing);

		var requestData = {
			action: 'lovetravel_wizard_import_step',
			step: step,
			nonce: loveTravelWizard.nonce
		};

		// ✅ Verified: Add duplicate handling for adventures
		if (step === 'adventures') {
			var duplicateHandling = $('input[name="duplicate_handling"]:checked').val();
			requestData.duplicate_handling = duplicateHandling;
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
						if (response.data.status === 'processing' || response.data.status === 'media_download') {
							if (response.data.processed === lastProcessed) {
								stallCount++;
								console.log('Progress stalled, count:', stallCount);
								
								// Trigger background processing if stalled
								if (stallCount >= maxStallCount) {
									console.log('Triggering background processing for', step);
									triggerBackgroundProcessing(step);
									stallCount = 0; // Reset counter
								}
							} else {
								lastProcessed = response.data.processed;
								stallCount = 0; // Reset counter on progress
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
		
		var $progressBar, $status, $details;
		
		if (step === 'media') {
			$progressBar = $('#media-import-progress .progress-fill');
			$status = $('#media-progress-status');
			$details = $('#media-progress-details');
		} else if (step === 'destinations') {
			$progressBar = $('#destinations-import-progress .progress-fill');
			$status = $('#destinations-progress-status');
			$details = $('#destinations-progress-details');
		} else {
			$progressBar = $('.progress-fill');
			$status = $('#progress-status');
			$details = $('#progress-details');
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
				details = progressData.processed + '/' + progressData.total + ' adventures';
				if (progressData.media_total > 0) {
					details += ', ' + progressData.media_processed + '/' + progressData.media_total + ' media files';
				}
			}
		}
		$details.text(details);
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
		
		if (step === 'media') {
			$stopButton = $('#stop-media-import');
			$startButton = $('[data-step="media"]');
			$progressContainer = $('#media-import-progress');
			$statusElement = $('#media-progress-status');
			$progressBar = $('#media-import-progress .progress-fill');
		} else if (step === 'destinations') {
			$stopButton = $('#stop-destinations-import');
			$startButton = $('[data-step="destinations"]');
			$progressContainer = $('#destinations-import-progress');
			$statusElement = $('#destinations-progress-status');
			$progressBar = $('#destinations-import-progress .progress-fill');
		} else {
			$stopButton = $('#stop-adventure-import');
			$startButton = $('[data-step="adventures"]');
			$progressContainer = $('#adventure-import-progress');
			$statusElement = $('#progress-status');
			$progressBar = $('.progress-fill');
		}
		
		// ✅ Verified: Confirm with user
		if (!confirm('Are you sure you want to stop the import? Progress will be lost.')) {
			return;
		}
		
		$stopButton.prop('disabled', true).text('Stopping...');
		
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
					// ✅ Verified: Reset UI state
					$startButton.prop('disabled', false).removeClass('importing').text('Start Import');
					$stopButton.hide().prop('disabled', false).text('Stop Import');
					
					// ✅ Verified: Update progress display
					$statusElement.text('Import stopped by user');
					$progressBar.css('width', '0%');
					
					showAdminNotice('warning', response.data.message);
					
					// ✅ Verified: Hide progress after delay
					setTimeout(function() {
						$progressContainer.fadeOut();
					}, 3000);
				} else {
					$stopButton.prop('disabled', false).text('Stop Import');
					showAdminNotice('error', response.data.message);
				}
			},
			error: function() {
				$stopButton.prop('disabled', false).text('Stop Import');
				showAdminNotice('error', 'Failed to stop import');
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
		// ✅ Verified: Update UI
		$button.removeClass('importing')
			   .addClass('button-secondary')
			   .text(loveTravelWizard.strings.complete);
		
		// ✅ Verified: Update status notice
		var $postbox = $button.closest('.postbox');
		var $notice = $postbox.find('.notice');
		
		$notice.removeClass('notice-warning')
			   .addClass('notice-success')
			   .find('p')
			   .text(data.message || loveTravelWizard.strings.complete);
		
		// ✅ Verified: Show completion message
		showAdminNotice('success', data.message);
		
		// ✅ Verified: Check if all steps completed
		checkAllStepsCompleted();
	}

	/**
	 * ✅ Verified: Handle step error
	 */
	function handleStepError(step, $button, data) {
		// ✅ Verified: Reset button state
		$button.prop('disabled', false)
			   .removeClass('importing')
			   .text($button.data('original-text') || 'Retry Import');
		
		// ✅ Verified: Show error message
		showAdminNotice('error', data.message || loveTravelWizard.strings.error);
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

})(jQuery);