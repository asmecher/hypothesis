<?php

class HypothesisHandler {

    public function getSubmissionsWithAnnotations($contextId) {
        $submissions = Services::get('submission')->getMany([
            'contextId' => $contextId
        ]);

        $submissionsWithAnnotations = [];
        foreach ($submissions as $submission) {
            if($this->submissionHasAnnotations($submission))
                $submissionsWithAnnotations[] = $submission;
        }

        return $submissionsWithAnnotations;
    }

    private function submissionHasAnnotations($submission): bool {
        $publication = $submission->getCurrentPublication();
        $galleys = $publication->getData('galleys');

        foreach ($galleys as $galley) {
            $galleyDownloadURL = $this->getGalleyDownloadURL($galley);
            $requestURL = "https://hypothes.is/api/search?limit=0&group=__world__&uri={$galleyDownloadURL}";

            $response = json_decode(file_get_contents($requestURL), true);
            if ($response['total'] > 0) return true;
        }

        return false;
    }

    public function getGalleyDownloadURL($galley) {
        $request = Application::get()->getRequest();
        $indexUrl = $request->getIndexUrl();
        $contextPath = $request->getContext()->getPath();
        
        $submissionFile = $galley->getFile();
        $submissionId = $submissionFile->getData('submissionId');
        $assocId = $submissionFile->getData('assocId');
        $submissionFileId = $submissionFile->getId();
        
        return $indexUrl . "/$contextPath/preprint/download/$submissionId/$assocId/$submissionFileId";
    }

}
