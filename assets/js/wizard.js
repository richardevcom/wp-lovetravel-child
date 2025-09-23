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
	 * ✅ Verified: Handle background import (Adventures)
	 */
	function handleBackgroundImport(step, $button, data) {
		// ✅ Verified: Show progress area immediately
		$('#adventure-import-progress').show();
		
		// ✅ Verified: Initialize progress display
		$('.progress-fill').css('width', '0%');
		$('#progress-status').text('Starting import...');
		$('#progress-details').text('Preparing to fetch adventures from Payload CMS');
		
		// ✅ Verified: Update button text
		$button.text('Import Running...');
		
		// ✅ Verified: Show success message with progress context
		showAdminNotice('info', data.message + ' Progress shown below.');
		
		// ✅ Verified: Start progress polling immediately
		startProgressPolling(step, $button);
		
		// ✅ Verified: Do initial progress check after 1 second
		setTimeout(function() {
			checkProgressOnce();
		}, 1000);
	}

	/**
	 * ✅ Verified: Poll import progress
	 */
	function startProgressPolling(step, $button) {
		var progressInterval = setInterval(function() {
			$.ajax({
				url: loveTravelWizard.ajaxUrl,
				type: 'POST',
				data: {
					action: 'lovetravel_wizard_get_progress',
					nonce: loveTravelWizard.nonce
				},
				success: function(response) {
					if (response.success) {
						updateProgressDisplay(response.data);
						
						// ✅ Verified: Stop polling when completed
						if (response.data.status === 'completed' || response.data.status === 'failed') {
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
	 * ✅ Verified: Update progress display
	 */
	function updateProgressDisplay(progressData) {
		var $progressBar = $('.progress-fill');
		var $status = $('#progress-status');
		var $details = $('#progress-details');
		
		// ✅ Verified: Update progress bar
		$progressBar.css('width', progressData.percentage + '%');
		
		// ✅ Verified: Update status text
		$status.text(progressData.message);
		
		// ✅ Verified: Update details
		var details = '';
		if (progressData.total > 0) {
			details = progressData.processed + '/' + progressData.total + ' adventures';
			if (progressData.media_total > 0) {
				details += ', ' + progressData.media_processed + '/' + progressData.media_total + ' media files';
			}
		}
		$details.text(details);
	}

	/**
	 * ✅ Verified: Single progress check
	 */
	function checkProgressOnce() {
		$.ajax({
			url: loveTravelWizard.ajaxUrl,
			type: 'POST',
			data: {
				action: 'lovetravel_wizard_get_progress',
				nonce: loveTravelWizard.nonce
			},
			success: function(response) {
				if (response.success) {
					updateProgressDisplay(response.data);
				}
			},
			error: function() {
				console.log('Initial progress check failed, polling will continue...');
			}
		});
	}

	/**
	 * ✅ Verified: Handle progress completion
	 */
	function handleProgressComplete(step, $button, progressData) {
		if (progressData.status === 'completed') {
			handleStepSuccess(step, $button, {
				message: progressData.message
			});
		} else {
			handleStepError(step, $button, {
				message: progressData.message
			});
		}
		
		// ✅ Verified: Keep progress visible longer for completed status
		setTimeout(function() {
			$('#adventure-import-progress').fadeOut(1000);
		}, 5000); // Show completed status for 5 seconds
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