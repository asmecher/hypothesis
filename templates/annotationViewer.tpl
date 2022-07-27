<li class="annotation_viewer">
    <span id="annotation_viewer_span-{$galley->getId()}"></span>
</li>

<script>
    $(function(){ldelim}
        $.get(
            'https://hypothes.is/api/search?limit=0&group=__world__&uri={$galleyDownloadURL}',
            function(response) {ldelim}
                if(response['total'] > 0) {ldelim}
                    const viewerSpan = document.getElementById('annotation_viewer_span-{$galley->getId()}');
                    if(response['total'] == 1)
                        viewerSpan.textContent = response['total'] + ' {translate key="plugins.generic.hypothesis.annotation"}';
                    else
                        viewerSpan.textContent = response['total'] + ' {translate key="plugins.generic.hypothesis.annotations"}';
                    viewerSpan.parentNode.style.visibility = 'visible';
                {rdelim}
            {rdelim}
        );
    {rdelim});
</script>