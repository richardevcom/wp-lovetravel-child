(function($){
    'use strict';

    const payloadImportConfig = window.payloadImport || {};
    let currentStatus = payloadImportConfig.currentStatus || 'idle';
    let statusCheckInterval = null;

    function qs(id){ return $(id); }
    function logContainer(){ return $('#import-log'); }

    function updateLog(message){
        const timestamp = new Date().toLocaleTimeString();
        const entry = '[' + timestamp + '] ' + message;
        const c = logContainer();
        c.append('<div>' + $('<div>').text(entry).html() + '</div>');
        c.scrollTop(c[0].scrollHeight);
    }

    function updateLogFromResponse(logs){
        if(!logs || !logs.length) return;
        const c = logContainer();
        c.empty();
        logs.forEach(line => { if(line.trim()){ c.append('<div>' + $('<div>').text(line).html() + '</div>'); }});
        c.scrollTop(c[0].scrollHeight);
    }

    function refreshStats(){
        $.post(payloadImportConfig.ajax_url, {
            action: 'payload_import_stats',
            nonce: payloadImportConfig.nonce
        }).done(res => {
            if(res.success && res.data.stats){
                const s = res.data.stats;
                $('#payload-count').text(s.payload_count || 0);
                $('#wp-count').text(s.wp_count || 0);
                $('#imported-count').text(s.imported_count || 0);
                $('#remaining-count').text(s.remaining_count || 0);
            }
        });
    }

    function updateImportStatus(state){
        if(!state) return;
        $('#import-status').text(state.status.charAt(0).toUpperCase() + state.status.slice(1))
            .removeClass().addClass('status-' + state.status);
        $('#job-id').text(state.job_id || 'N/A');
        $('#import-progress').text((state.current_index || 0) + '/' + (state.total || 0));
        $('#current-file').text(state.current_file || '-');
        if(state.total > 0){
            const pct = ((state.current_index || 0) / state.total) * 100;
            $('#progress-fill').css('width', pct + '%');
        }
        if(state.status !== currentStatus){
            currentStatus = state.status;
            if(['stopped','completed','error'].includes(state.status)){
                clearInterval(statusCheckInterval); statusCheckInterval = null;
                setTimeout(()=>{ window.location.reload(); }, 3000);
            }
        }
    }

    function startStatusCheck(){
        if(statusCheckInterval) clearInterval(statusCheckInterval);
        statusCheckInterval = setInterval(()=>{
            $.post(payloadImportConfig.ajax_url, { action:'payload_import_status', nonce: payloadImportConfig.nonce })
                .done(res => { if(res.success){ updateImportStatus(res.data.state); updateLogFromResponse(res.data.logs); refreshStats(); }});
        }, 3000);
    }

    function startImport(){
        const data = {
            action: 'payload_import_start',
            nonce: payloadImportConfig.nonce,
            batch_size: $('#batch-size').val(),
            skip_existing: $('#skip-existing').is(':checked') ? '1':'0',
            generate_thumbnails: $('#generate-thumbnails').is(':checked') ? '1':'0'
        };
        updateLog('🚀 Starting import...');
        $.post(payloadImportConfig.ajax_url, data)
            .done(res => {
                if(res.success){
                    updateLog('✅ Import started successfully - Job ID: ' + res.data.job_id);
                    currentStatus = 'running';
                    setTimeout(()=> window.location.reload(), 800);
                } else {
                    updateLog('❌ Failed to start import: ' + (res.data.message || 'Unknown error'));
                }
            })
            .fail(()=> updateLog('❌ AJAX request failed'));
    }

    function stopImport(){
        $.post(payloadImportConfig.ajax_url, { action:'payload_import_stop', nonce: payloadImportConfig.nonce })
            .done(res => { if(res.success){ updateLog('🛑 Stop requested - import will stop after current batch'); $('#import-status').text('Stopping').removeClass().addClass('status-stopping'); $('#stop-import').prop('disabled', true).text('Stopping...'); } else { updateLog('❌ Failed to stop import: ' + (res.data.message||'Unknown error')); } })
            .fail(()=> updateLog('❌ Stop request failed'));
    }

    function resetImport(){
        if(!confirm('Are you sure you want to reset the import state? This will clear all progress.')) return;
        $.post(payloadImportConfig.ajax_url, { action:'payload_import_reset', nonce: payloadImportConfig.nonce })
            .done(res => { if(res.success){ updateLog('♻️ Import state reset'); setTimeout(()=> window.location.reload(), 800); } else { updateLog('❌ Failed to reset import: ' + (res.data.message||'Unknown error')); } })
            .fail(()=> updateLog('❌ Reset request failed'));
    }

    function refreshLog(){
        $.post(payloadImportConfig.ajax_url, { action:'payload_import_status', nonce: payloadImportConfig.nonce })
            .done(res => { if(res.success && res.data.logs){ updateLogFromResponse(res.data.logs); }});
    }

    function bindEvents(){
        $('#import-form').on('submit', e => { e.preventDefault(); startImport(); });
        $('#stop-import').on('click', stopImport);
        $('#reset-import').on('click', resetImport);
        $('#refresh-log').on('click', refreshLog);
    }

    $(document).ready(function(){
        bindEvents();
        if(['running','stopping'].includes(currentStatus)){
            startStatusCheck();
            refreshLog();
        }
    });
})(jQuery);
