
function insertAfter(newNode, referenceNode) {
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

function createAnnotationViewerNode(galleyUrl, message) {
    var viewerLi = document.createElement('li');
    viewerLi.classList.add('annotation_viewer');
    var viewer = document.createElement('a');
    viewer.href = galleyUrl;
    viewer.textContent = message;

    viewerLi.appendChild(viewer);
    return viewerLi;
}

async function addAnnotationViewers() {
    let galleyLinks = document.getElementsByClassName('obj_galley_link');

    for (let galleyLink of galleyLinks) {
        let galleyUrl = galleyLink.href;
        let galleyDownloadUrl = await $.get(
            app.hypothesisHandlerUrl + 'get-galley-download-url',
            {
                galleyUrl: galleyUrl
            }
        );
        let viewerNode = createAnnotationViewerNode(JSON.parse(galleyDownloadUrl), '1 annotation');
        insertAfter(viewerNode, galleyLink.parentNode);
    }
}


$(document).ready(addAnnotationViewers);