<?php

class HypothesisClient {

    public function getGalleyAnnotationsNumber($galley) {
        $request = Application::get()->getRequest();
        $indexUrl = $request->getIndexUrl();
        $contextPath = $request->getContext()->getPath();
        
        $submissionFile = $galley->getFile();
        $submissionId = $submissionFile->getData('submissionId');
        $assocId = $submissionFile->getData('assocId');
        $submissionFileId = $submissionFile->getId();
        
        $galleyLinkUrl = $indexUrl . "/$contextPath/preprint/download/$submissionId/$assocId/$submissionFileId";
        return 10;
    }

}