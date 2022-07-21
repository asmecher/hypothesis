<?php

class HypothesisClient {

    private function getGalleyDownloadURL($galley) {
        $request = Application::get()->getRequest();
        $indexUrl = $request->getIndexUrl();
        $contextPath = $request->getContext()->getPath();
        
        $submissionFile = $galley->getFile();
        $submissionId = $submissionFile->getData('submissionId');
        $assocId = $submissionFile->getData('assocId');
        $submissionFileId = $submissionFile->getId();
        
        return $indexUrl . "/$contextPath/preprint/download/$submissionId/$assocId/$submissionFileId";
    }

    private function checkRequestStatus($responseHeaders) {
        $partitionedStatus = explode(" ", $responseHeaders[0]);
        
        $responseStatusCode = $partitionedStatus[1];
        $httpOKCode = "200";

        return $responseStatusCode == $httpOKCode;
    }

    public function getGalleyAnnotationsNumber($galley) {
        $galleyFileDownloadURL = $this->getGalleyDownloadURL($galley);
        $requestURL = "https://hypothes.is/api/search?limit=0&group=__world__&uri={$galleyFileDownloadURL}";

        $requestSuccedeed = $this->checkRequestStatus(get_headers($requestURL));
        if(!$requestSuccedeed)
            return null;
        
        $response = json_decode(file_get_contents($requestURL), true);
        $annotationsNumber = $response['total'];

        return $annotationsNumber;
    }

}