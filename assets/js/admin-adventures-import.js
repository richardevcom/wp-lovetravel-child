/* Adventures Import Admin JS */
jQuery(function ($) {
  const S = {
    form: '#adventures-import-form',
    limit: '#limit',
    overwrite: '#overwrite-existing',
    dryRun: '#dry-run',
    start: '#start-import',
    stop: '#stop-import',
    notices: '#adventures-notices',
    totalDocs: '#total-docs',
    totalPages: '#total-pages',
    currentPage: '#current-page',
    pagesDone: '#pages-done',
    pagesTotal: '#pages-total',
    imported: '#count-imported',
    updated: '#count-updated',
    skipped: '#count-skipped',
    failed: '#count-failed',
    progressFill: '#progress-fill',
    log: '#import-log'
  };

  let running = false;
  let page = 1;
  let totals = { imported: 0, updated: 0, skipped: 0, failed: 0 };

  function ajax(action, data = {}) {
    return $.post(adventuresImport.ajax_url, Object.assign({
      action,
      nonce: adventuresImport.nonce
    }, data));
  }

  function notice(html, type='info') {
    $(S.notices).html('<div class="notice notice-' + type + '"><p>' + html + '</p></div>');
  }

  function log(line) {
    const t = new Date().toLocaleTimeString();
    const $log = $(S.log);
    $log.append('<div>[' + t + '] ' + $('<div>').text(line).html() + '</div>');
    $log.scrollTop($log[0].scrollHeight);
  }

  function setProgress(done, total) {
    const pct = total > 0 ? Math.min(100, Math.round((done / total) * 100)) : 0;
    $(S.progressFill).css('width', pct + '%');
  }

  function fetchStats() {
    const limit = parseInt($(S.limit).val(), 10) || adventuresImport.defaults.limit;
    return ajax('lovetravel_adventures_get_stats', { page: 1, limit })
      .done(function (res) {
        if (res.success) {
          const s = res.data.stats;
          $(S.totalDocs).text(s.totalDocs);
          $(S.totalPages).text(s.totalPages);
          $(S.currentPage).text(s.page);
          $(S.pagesTotal).text(s.totalPages);
        } else {
          notice('Could not load stats', 'warning');
        }
      })
      .fail(function () { notice('Stats request failed', 'error'); });
  }

  function importNextPage() {
    if (!running) return;
    const limit = parseInt($(S.limit).val(), 10) || adventuresImport.defaults.limit;
    const overwrite = $(S.overwrite).is(':checked') ? '1' : '0';
    const dry_run = $(S.dryRun).is(':checked') ? '1' : '0';

    ajax('lovetravel_adventures_import_page', { page, limit, overwrite, dry_run })
      .done(function (res) {
        if (res.success) {
          const d = res.data;
          totals.imported += d.imported;
          totals.updated += d.updated;
          totals.skipped += d.skipped;
          totals.failed += d.failed;
          $(S.imported).text(totals.imported);
          $(S.updated).text(totals.updated);
          $(S.skipped).text(totals.skipped);
          $(S.failed).text(totals.failed);
          (d.messages || []).forEach(m => log(m));

          const pagesDone = Math.min(parseInt($(S.pagesDone).text(), 10) + 1, parseInt($(S.pagesTotal).text(), 10));
          $(S.pagesDone).text(pagesDone);
          setProgress(pagesDone, parseInt($(S.pagesTotal).text(), 10));

          if (d.hasNextPage && d.nextPage) {
            page = d.nextPage;
            $(S.currentPage).text(page);
            setTimeout(importNextPage, 400);
          } else {
            running = false;
            $(S.start).prop('disabled', false);
            $(S.stop).prop('disabled', true);
            notice('Import completed', 'success');
          }
        } else {
          running = false;
          $(S.start).prop('disabled', false);
          $(S.stop).prop('disabled', true);
          notice('Import failed: ' + (res.data && res.data.message ? res.data.message : 'Unknown error'), 'error');
        }
      })
      .fail(function () {
        running = false;
        $(S.start).prop('disabled', false);
        $(S.stop).prop('disabled', true);
        notice('Network error', 'error');
      });
  }

  function startImport(e) {
    e.preventDefault();
    if (running) return;
    running = true;
    page = 1;
    totals = { imported: 0, updated: 0, skipped: 0, failed: 0 };
    $(S.imported).text(0);
    $(S.updated).text(0);
    $(S.skipped).text(0);
    $(S.failed).text(0);
    $(S.pagesDone).text(0);
    setProgress(0, parseInt($(S.pagesTotal).text(), 10));
    $(S.start).prop('disabled', true);
    $(S.stop).prop('disabled', false);
    $(S.log).empty();
    log('🚀 Starting import...');
    importNextPage();
  }

  function stopImport() {
    running = false;
    $(S.start).prop('disabled', false);
    $(S.stop).prop('disabled', true);
    notice('Stopping after current request...', 'info');
  }

  // Bindings
  $(S.form).on('submit', startImport);
  $(S.stop).on('click', stopImport);

  // Init
  fetchStats();
});
