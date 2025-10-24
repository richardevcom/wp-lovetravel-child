/**
 * Admin Scripts
 *
 * Admin-specific JavaScript for LoveTravel Child Theme.
 * Loaded at priority 20 to override parent theme and plugins.
 *
 * @package LoveTravelChild
 * @since   2.0.0
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		/**
		 * Handle manual template import
		 */
		$('#lovetravel-child-import-templates').on('click', function(e) {
			e.preventDefault();

			const $button = $(this);
			const $spinner = $button.next('.spinner');
			const $messageContainer = $('#lovetravel-child-import-message');

			// Disable button and show spinner
			$button.prop('disabled', true);
			$spinner.addClass('is-active');
			$messageContainer.empty();

			// Send AJAX request
			$.ajax({
				url: lovetravelChildAdmin.ajaxurl,
				type: 'POST',
				data: {
					action: 'lovetravel_child_import_templates',
					nonce: lovetravelChildAdmin.nonce
				},
				success: function(response) {
					if (response.success) {
						$messageContainer.html(
							'<div class="notice notice-success inline" style="margin-top: 15px;"><p>' + 
							response.data.message + 
							'</p></div>'
						);

						// Reload page after 2 seconds to refresh template status
						setTimeout(function() {
							window.location.reload();
						}, 2000);
					} else {
						$messageContainer.html(
							'<div class="notice notice-error inline" style="margin-top: 15px;"><p>' + 
							response.data.message + 
							'</p></div>'
						);
					}
				},
				error: function(xhr, status, error) {
					$messageContainer.html(
						'<div class="notice notice-error inline" style="margin-top: 15px;"><p>' + 
						'AJAX error: ' + error + 
						'</p></div>'
					);
				},
				complete: function() {
					// Re-enable button and hide spinner
					$button.prop('disabled', false);
					$spinner.removeClass('is-active');
				}
			});
		});
	});

})(jQuery);
