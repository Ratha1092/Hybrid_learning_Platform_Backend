<script>
    if (!window.__biCsvDownloadListenerAdded) {
        window.__biCsvDownloadListenerAdded = true;
        window.addEventListener('download-csv', function(e) {
            const a = document.createElement('a');
            a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(e.detail.content);
            a.download = e.detail.filename;
            a.click();
        });
    }
</script>
