/* Mailchimp Export Admin JS (externalized) */
jQuery(function ($) {
	'use strict';
	const cfg = window.mc4wp_export_ajax || {};
	let exportInProgress = false;

	function updateProgress(percent, text, details) {
		$('#progress-fill').css('width', percent + '%').text(percent + '%');
		$('#progress-text').text(text);
		$('#progress-details').text(details || '');
	}

	function showResult(type, html) {
		const cls = type === 'success' ? 'notice notice-success' : 'notice notice-error';
		$('#export-notices')
			.removeClass('success error notice notice-success notice-error')
			.addClass(cls)
			.html('<p>' + html + '</p>')
			.show();
	}

	function logLine(msg) {
		const time = new Date().toLocaleTimeString();
		$('#export-log').append('<div>[' + time + '] ' + msg + '</div>');
		const el = document.getElementById('export-log');
		if (el) el.scrollTop = el.scrollHeight;
	}

	function loadStatistics() {
		$('#total-subscribers, #active-subscribers, #recent-subscribers').text('...');
		return $.post(cfg.ajax_url, { action: 'get_subscriber_stats', nonce: cfg.nonce })
			.done(function (res) {
				if (res.success) {
					$('#total-subscribers').text(res.data.total_count.toLocaleString());
					$('#active-subscribers').text(res.data.active_count.toLocaleString());
					$('#recent-subscribers').text(res.data.recent_count.toLocaleString());
				} else {
					showResult('error', 'Failed to load statistics: ' + (res.data ? res.data.message : 'Unknown error'));
				}
			})
			.fail(function () {
				showResult('error', 'Failed to load statistics: Network error');
				$('#total-subscribers, #active-subscribers, #recent-subscribers').text('Error');
			});
	}

	function exportSubscribers() {
		$('#export-progress').show();
		$('#export-notices').hide().removeClass('notice notice-success notice-error');
		$('#download-export').hide().attr('href', '#');
		$('#download-description').hide();
		logLine('Starting export...');
		const data = {
			action: 'export_mailchimp_subscribers',
			nonce: cfg.nonce,
			format: $('#export-format').val(),
			include_unsubscribed: $('#include-unsubscribed').is(':checked') ? '1' : '0',
			use_date_range: $('#use-date-range').is(':checked') ? '1' : '0',
			start_date: $('#start-date').val(),
			end_date: $('#end-date').val()
		};
		updateProgress(10, 'Connecting to Payload CMS...', 'Initializing export process');
		logLine('Submitting export request to server...');
		$.post(cfg.ajax_url, data)
			.done(function (res) {
				if (res.success) {
					updateProgress(100, 'Export completed successfully!', 'Exported ' + res.data.exported_count + ' subscribers');
					logLine('Export complete: ' + res.data.exported_count + ' subscribers.');
					$('#download-export').attr('href', res.data.download_url).show();
					$('#download-description').show();
					showResult('success', 'Export complete: ' + res.data.exported_count + ' subscribers.');
				} else {
					updateProgress(0, 'Export failed', '');
					const msg = 'Export failed: ' + (res.data ? res.data.message : 'Unknown error');
					logLine(msg);
					showResult('error', msg);
				}
			})
			.fail(function (xhr, status) {
				updateProgress(0, 'Export failed', '');
				const msg = 'Export failed due to network error: ' + status;
				logLine(msg);
				showResult('error', msg);
			})
			.always(function () {
				exportInProgress = false;
				$('#export-subscribers').prop('disabled', false).text('Export Subscribers');
			});
	}

	function bindEvents() {
		$('#use-date-range').on('change', function () { $('#date-range-options').toggle(this.checked); });
		$('#export-subscribers').on('click', function () {
			if (exportInProgress) { alert('Export already in progress!'); return; }
			exportInProgress = true;
			$(this).prop('disabled', true).text('Exporting...');
			exportSubscribers();
		});
	}

	if (!cfg.ajax_url) {
		console.error('mc4wp_export_ajax not defined');
		showResult('error', 'JavaScript configuration error. Please refresh the page.');
		return;
	}

	// Load fresh statistics on page load; no manual refresh button needed
	loadStatistics();
	bindEvents();
});
