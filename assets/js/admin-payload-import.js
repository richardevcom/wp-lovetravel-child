/* Payload Media Import Admin JS (extracted from template) */
jQuery(function ($) {
  let statusCheckInterval = null;
  let currentStatus = payloadImport.state || 'idle';

  const selectors = {
    form: '#import-form',
    stop: '#stop-import',
    reset: '#reset-import',
    refreshLog: '#refresh-log',
    log: '#import-log',
    status: '#import-status',
    jobId: '#job-id',
    progress: '#import-progress',
    currentFile: '#current-file',
    progressFill: '#progress-fill',
    payloadCount: '#payload-count',
    wpCount: '#wp-count',
    importedCount: '#imported-count',
    remainingCount: '#remaining-count'
  };

  function updateLog(message) {
    const timestamp = new Date().toLocaleTimeString();
    const logEntry = '[' + timestamp + '] ' + message;
    const $log = $(selectors.log);
    $log.append('<div>' + $('<div>').text(logEntry).html() + '</div>');
    $log.scrollTop($log[0].scrollHeight);
  }

  function ajax(action, data = {}) {
    return $.post(payloadImport.ajax_url, Object.assign({
      action: action,
      nonce: payloadImport.nonce
    }, data));
  }

  function startImport() {
    const data = {
      batch_size: $('#batch-size').val(),
      skip_existing: $('#skip-existing').is(':checked') ? '1' : '0',
      generate_thumbnails: $('#generate-thumbnails').is(':checked') ? '1' : '0'
    };
    updateLog('🚀 Starting import...');
    ajax('payload_import_start', data)
      .done(function (response) {
        if (response.success) {
          updateLog('✅ Import started — Job ID: ' + response.data.job_id);
          currentStatus = 'running';
          setTimeout(function () { location.reload(); }, 800);
        } else {
          updateLog('❌ Failed to start: ' + (response.data.message || 'Unknown error'));
        }
      })
      .fail(function () { updateLog('❌ AJAX start failed'); });
  }

  function stopImport() {
    ajax('payload_import_stop')
      .done(function (response) {
        if (response.success) {
          updateLog('🛑 Stop requested — will halt after current batch');
          $(selectors.status).text('Stopping').attr('class', 'status-stopping');
          $(selectors.stop).prop('disabled', true).text('Stopping...');
        } else {
          updateLog('❌ Failed to stop: ' + (response.data.message || 'Unknown error'));
        }
      })
      .fail(function () { updateLog('❌ Stop request failed'); });
  }

  function resetImport() {
    if (!confirm('Reset import state? This clears all progress.')) return;
    ajax('payload_import_reset')
      .done(function (response) {
        if (response.success) {
          updateLog('🧹 Import state reset');
          setTimeout(function () { location.reload(); }, 800);
        } else {
          updateLog('❌ Failed to reset: ' + (response.data.message || 'Unknown error'));
        }
      })
      .fail(function () { updateLog('❌ Reset request failed'); });
  }

  function refreshLog() {
    ajax('payload_import_status')
      .done(function (response) {
        if (response.success && response.data.logs) {
          updateLogFromResponse(response.data.logs);
        }
      });
  }

  function refreshStats() {
    ajax('payload_import_stats')
      .done(function (response) {
        if (response.success && response.data.stats) {
          const stats = response.data.stats;
          $(selectors.payloadCount).text(stats.payload_count || 0);
          $(selectors.wpCount).text(stats.wp_count || 0);
          $(selectors.importedCount).text(stats.imported_count || 0);
          $(selectors.remainingCount).text(stats.remaining_count || 0);
        }
      });
  }

  function updateImportStatus(state) {
    if (!state) return;
    $(selectors.status).text(state.status.charAt(0).toUpperCase() + state.status.slice(1))
      .attr('class', 'status-' + state.status);
    $(selectors.jobId).text(state.job_id || 'N/A');
    $(selectors.progress).text((state.current_index || 0) + '/' + (state.total || 0));
    $(selectors.currentFile).text(state.current_file || '-');
    if (state.total > 0) {
      const percent = ((state.current_index || 0) / state.total) * 100;
      $(selectors.progressFill).css('width', percent + '%');
    }
    if (state.status !== currentStatus) {
      currentStatus = state.status;
      if (['stopped', 'completed', 'error'].includes(state.status)) {
        clearInterval(statusCheckInterval);
        statusCheckInterval = null;
        setTimeout(function () { location.reload(); }, 2200);
      }
    }
  }

  function updateLogFromResponse(logs) {
    if (!logs || !logs.length) return;
    const $log = $(selectors.log).empty();
    logs.forEach(function (line) {
      if (line.trim()) $log.append('<div>' + $('<div>').text(line).html() + '</div>');
    });
    $log.scrollTop($log[0].scrollHeight);
  }

  function startStatusCheck() {
    if (statusCheckInterval) clearInterval(statusCheckInterval);
    statusCheckInterval = setInterval(function () {
      ajax('payload_import_status')
        .done(function (response) {
          if (response.success) {
            updateImportStatus(response.data.state);
            updateLogFromResponse(response.data.logs);
            refreshStats();
          }
        });
    }, 3000);
  }

  // Event bindings
  $(selectors.form).on('submit', function (e) { e.preventDefault(); startImport(); });
  $(selectors.stop).on('click', stopImport);
  $(selectors.reset).on('click', resetImport);
  $(selectors.refreshLog).on('click', refreshLog);

  // Start polling if active
  if (['running', 'stopping'].includes(currentStatus)) {
    startStatusCheck();
    refreshLog();
  }

  // Accessible legend
  if (!document.querySelector('#import-log-legend')) {
    $(selectors.log).before('<p id="import-log-legend" class="description">Legend: 🚀 start · ✅ imported · ♻️ skipped · ⚠️ warning · ❌ error · 🧹 reset · 🛑 stop</p>');
  }
});
