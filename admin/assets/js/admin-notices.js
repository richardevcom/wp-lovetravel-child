/**
 * Admin Notices Handler
 *
 * Handles dismissal of admin notices via AJAX.
 *
 * @package LoveTravelChild
 * @since   2.0.0
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		// Handle notice dismissal
		$(document).on('click', '.notice.is-dismissible[data-notice-id] .notice-dismiss', function(e) {
			const $notice = $(this).closest('.notice');
			const noticeId = $notice.data('notice-id');

			if (!noticeId) {
				return;
			}

			// Send AJAX request to persist dismissal
			$.ajax({
				url: lovetravelChildNotices.ajaxurl,
				type: 'POST',
				data: {
					action: 'lovetravel_child_dismiss_notice',
					nonce: lovetravelChildNotices.nonce,
					notice_id: noticeId
				},
				success: function(response) {
					if (!response.success) {
						console.error('Failed to dismiss notice:', response.data);
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX error dismissing notice:', error);
				}
			});
		});
	});

})(jQuery);
