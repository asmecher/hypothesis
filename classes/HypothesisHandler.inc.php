<?php

class HypothesisHandler {

    public function getSubmissionsWithAnnotations($contextId) {
        $submissions = Services::get('submission')->getMany([
            'contextId' => $contextId
        ]);

        $groupsRequests = $this->getSubmissionsGroupsRequests($submissions, $contextId);
        $submissionsWithAnnotations = [];
        foreach ($groupsRequests as $groupRequest) {
            $groupResponse = $this->getRequestAnnotations($groupRequest);
            if (!is_null($groupResponse) && $groupResponse['total'] > 0) {
                $submissionsWithAnnotations = array_merge(
                    $submissionsWithAnnotations,
                    $this->getWhichSubmissionsHaveAnnotations($groupResponse)
                );
            }
        }

        return $submissionsWithAnnotations;
    }

    private function getSubmissionsGroupsRequests($submissions, $contextId) {
        $requests = [];
        $requestPrefix = $currentRequest = "https://hypothes.is/api/search?limit=200&group=__world__";
        $maxRequestLength = 4094;

        foreach ($submissions as $submission) {
            $submissionRequestParams = $this->getSubmissionRequestParams($submission, $contextId);

            if(!is_null($submissionRequestParams)) {
                if(strlen($currentRequest.$submissionRequestParams) < $maxRequestLength) {
                    $currentRequest .= $submissionRequestParams;
                }
                else {
                    $requests[] = $currentRequest;
                    $currentRequest = $requestPrefix . $submissionRequestParams; 
                }
            }
        }

        return $requests;
    }

    private function getSubmissionRequestParams($submission, $contextId) {
        $submissionRequestParams = "";
        $publication = $submission->getCurrentPublication();
        
        if(is_null($publication))
            return null;
        
        $galleys = $publication->getData('galleys');
        foreach ($galleys as $galley) {
            $galleyDownloadURL = $this->getGalleyDownloadURL($galley, $contextId);
            if(!is_null($galleyDownloadURL))
                $submissionRequestParams .= "&uri={$galleyDownloadURL}";
        }

        return $submissionRequestParams;
    }
    
    private function getRequestAnnotations($requestURL) {
        $ch = curl_init($requestURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        if(!$output || substr($output, 1, 8) != '"total":') return null;

        return json_decode($output, true);
    }

    private function getWhichSubmissionsHaveAnnotations($groupResponse) {
        $submissionsWithAnnotations = [];

        foreach ($groupResponse['rows'] as $annotation) {
            $urlBySlash = explode("/", $annotation['links']['incontext']);
            $submissionId = (int) $urlBySlash[count($urlBySlash) - 3];
            $submissionsWithAnnotations[$submissionId] = $submissionId;
        }

        return $submissionsWithAnnotations;
    }

    public function getGalleyDownloadURL($galley, $contextId) {
        $request = Application::get()->getRequest();
        $indexUrl = $request->getIndexUrl();
        $context = Services::get('context')->get($contextId);
        $contextPath = $context->getPath();
        
        $submissionFile = $galley->getFile();
        if(is_null($submissionFile))
            return null;

        $submissionId = $submissionFile->getData('submissionId');
        $assocId = $submissionFile->getData('assocId');
        $submissionFileId = $submissionFile->getId();
        
        return $indexUrl . "/$contextPath/preprint/download/$submissionId/$assocId/$submissionFileId";
    }

}
