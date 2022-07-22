<link rel="stylesheet" type="text/css" href="/plugins/generic/hypothesis/styles/annotationViewer.css">

<script>
    function insertAfter(newNode, referenceNode) {ldelim}
        referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
    {rdelim}

    function createAnnotationViewerLi(annotationNumber) {
        const viewerLi = document.createElement('li');
        const viewerSpan = document.createElement('span');

        viewerLi.classList.add('annotation_viewer_li');
        viewerSpan.classList.add('annotation_viewer_span');
        if(annotationNumber > 1)
            viewerSpan.textContent = annotationNumber + ' {translate key="plugins.generic.hypothesis.annotations"}';
        else
            viewerSpan.textContent = annotationNumber + ' {translate key="plugins.generic.hypothesis.annotation"}';

        viewerLi.appendChild(viewerSpan);
        return viewerLi;
    }

    var galleyLinks = document.getElementsByClassName('obj_galley_link');
    var annotationNumbers = JSON.parse("{json_encode($annotationNumbers)}");

    for (let i = 0; i < galleyLinks.length; i++) {ldelim}
        if(annotationNumbers[i] > 0 || annotationNumbers[i] == null){ldelim}
            const galleyLink = galleyLinks[i];
            const annotationViewerLi = createAnnotationViewerLi(annotationNumbers[i]);

            insertAfter(annotationViewerLi, galleyLink.parentNode);
        {rdelim}    
    {rdelim}

</script>