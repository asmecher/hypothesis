<li id="annotation_viewer-{$galley->getId()}">
    {include file="frontend/objects/galley_link.tpl" parent=$preprint publication=$publication galley=$galley}
</li>

<script>
    $(function(){ldelim}
        $.get(
            'https://hypothes.is/api/search?limit=0&group=__world__&uri={$galleyDownloadURL}',
            function(response) {ldelim}
                if(response['total'] > 0) {ldelim}
                    const viewer = document.getElementById('annotation_viewer-{$galley->getId()}');
                    const galleyLink = viewer.getElementsByTagName('a')[0];
                    if(response['total'] == 1)
                        galleyLink.textContent = response['total'] + ' {translate key="plugins.generic.hypothesis.annotation"}';
                    else
                        galleyLink.textContent = response['total'] + ' {translate key="plugins.generic.hypothesis.annotations"}';
                    galleyLink.href = galleyLink.href + '?hasAnnotations=true';
                    viewer.style.visibility = 'visible';
                {rdelim}
            {rdelim}
        );
    {rdelim});
</script>