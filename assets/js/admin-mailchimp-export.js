(function($){
    'use strict';
    const cfg = window.mc4wp_export_ajax || {};
    let exportInProgress = false;

    function qs(sel){ return $(sel); }

    function updateProgress(percent, text, details){
        $('#progress-fill').css('width', percent + '%').text(percent + '%');
        $('#progress-text').text(text);
        $('#progress-details').text(details||'');
    }

    function showResult(type, html){
        $('#export-results')
            .removeClass('success error')
            .addClass(type)
            .html(html)
            .show();
    }

    function loadStatistics(){
        $('#total-subscribers, #active-subscribers, #recent-subscribers').text('...');
        return $.post(cfg.ajax_url, { action:'get_subscriber_stats', nonce: cfg.nonce })
            .done(res => {
                if(res.success){
                    $('#total-subscribers').text(res.data.total_count.toLocaleString());
                    $('#active-subscribers').text(res.data.active_count.toLocaleString());
                    $('#recent-subscribers').text(res.data.recent_count.toLocaleString());
                } else {
                    showResult('error','Failed to load statistics: ' + (res.data?res.data.message:'Unknown error'));
                }
            })
            .fail(()=>{
                showResult('error','Failed to load statistics: Network error');
                $('#total-subscribers, #active-subscribers, #recent-subscribers').text('Error');
            });
    }

    function exportSubscribers(){
        $('#export-progress').show();
        $('#export-results').hide().removeClass('success error');
        const data = {
            action: 'export_mailchimp_subscribers',
            nonce: cfg.nonce,
            format: $('#export-format').val(),
            include_unsubscribed: $('#include-unsubscribed').is(':checked') ? '1':'0',
            use_date_range: $('#use-date-range').is(':checked') ? '1':'0',
            start_date: $('#start-date').val(),
            end_date: $('#end-date').val()
        };
        updateProgress(10,'Connecting to Payload CMS...','Initializing export process');
        $.post(cfg.ajax_url, data)
            .done(res => {
                if(res.success){
                    updateProgress(100,'Export completed successfully!','Exported ' + res.data.exported_count + ' subscribers');
                    showResult('success', '<strong>Export Complete!</strong><br>' +
                        'Successfully exported ' + res.data.exported_count + ' subscribers.<br>' +
                        '<a href="' + res.data.download_url + '" class="mc4wp-download-link" target="_blank">📥 Download Export File (' + res.data.filename + ')</a>'
                    );
                } else {
                    updateProgress(0,'Export failed','');
                    showResult('error','Export failed: ' + (res.data?res.data.message:'Unknown error'));
                }
            })
            .fail((xhr,status)=>{
                updateProgress(0,'Export failed','');
                showResult('error','Export failed due to network error: ' + status);
            })
            .always(()=>{
                exportInProgress = false;
                $('#export-subscribers').prop('disabled', false).text('Export Subscribers');
            });
    }

    function bindEvents(){
        $('#refresh-stats').on('click', function(){
            const btn = $(this); btn.prop('disabled', true).text('Loading...');
            loadStatistics().always(()=> btn.prop('disabled', false).text('Refresh Statistics'));
        });
        $('#use-date-range').on('change', function(){ $('#date-range-options').toggle(this.checked); });
        $('#export-subscribers').on('click', function(){
            if(exportInProgress){ alert('Export already in progress!'); return; }
            exportInProgress = true;
            $(this).prop('disabled', true).text('Exporting...');
            exportSubscribers();
        });
    }

    $(document).ready(function(){
        if(!cfg.ajax_url){
            console.error('mc4wp_export_ajax not defined');
            showResult('error','JavaScript configuration error. Please refresh the page.');
            return;
        }
        loadStatistics();
        bindEvents();
    });
})(jQuery);
