<li class="annotation_viewer">
    <a id="annotation_viewer_link-{$galley->getId()}"></a>
</li>

<script>
    function getAnnotationMsg(response) {ldelim}
        if(response['total'] == 1)
            return response['total'] + ' {translate key="plugins.generic.hypothesis.annotation"}';
        
        return response['total'] + ' {translate key="plugins.generic.hypothesis.annotations"}';
    {rdelim}
    
    $(function(){ldelim}
        $.get(
            'https://hypothes.is/api/search?limit=0&group=__world__&uri={$galleyDownloadURL}',
            function(response) {ldelim}
                if(response['total'] > 0) {ldelim}
                    const viewerButton = document.getElementById('annotation_viewer_link-{$galley->getId()}');
                    const viewerLi = viewerButton.parentNode;
                    viewerButton.textContent = getAnnotationMsg(response);
                    
                    const galleyLink = viewerLi.previousElementSibling.getElementsByTagName('a')[0];
                    galleyLink.href = galleyLink.href + '?hasAnnotations=true';
                    viewerButton.href = galleyLink.href;
                    viewerLi.style.visibility = 'visible';
                {rdelim}
            {rdelim}
        );
    {rdelim});
</script>