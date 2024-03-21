
function insertAfter(newNode, referenceNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

function createAnnotationViewerNode(galleyId) {
    var viewerLi = document.createElement('li');
    viewerLi.classList.add('annotation_viewer');
    viewerLi.classList.add('hidden_viewer');
    var viewer = document.createElement('a');
    viewer.id = 'annotation_viewer_link-' + galleyId;

    viewerLi.appendChild(viewer);
    return viewerLi;
}

async function addAnnotationViewers() {
    let galleyLinks = document.getElementsByClassName('obj_galley_link');

    for (let galleyLink of galleyLinks) {
        let galleyUrl = galleyLink.href;
        let explodedUrl = galleyUrl.split('/');
        let galleyId = explodedUrl[explodedUrl.length - 1];
        let viewerNode = createAnnotationViewerNode(galleyId);
        insertAfter(viewerNode, galleyLink.parentNode);

        let viewerData = await $.get(
            app.hypothesisHandlerUrl + 'get-annotation-viewer-data',
            {
                galleyUrl: galleyUrl
            }
        );
        viewerData = JSON.parse(viewerData);

        if (viewerData !== null) {
            $.get(
                'https://hypothes.is/api/search?limit=0&group=__world__&uri=' + viewerData['downloadUrl'],
                function(response) {
                    if (response['total'] > 0) {
                        const viewerButton = document.getElementById('annotation_viewer_link-' + galleyId);
                        const viewerLi = viewerButton.parentNode;
                        const suffix = (response['total'] == 1) ? viewerData['annotationMsg'] : viewerData['annotationsMsg'];
                        viewerButton.textContent = response['total'] + ' ' + suffix;
                        
                        const galleyLink = viewerLi.previousElementSibling.getElementsByTagName('a')[0];
                        galleyLink.href = galleyLink.href + '?hasAnnotations=true';
                        viewerButton.href = galleyLink.href;
                        viewerLi.classList.remove('hidden_viewer');
                    }
                }
            );
        }

    }
}


$(document).ready(addAnnotationViewers);