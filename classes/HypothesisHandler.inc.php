<?php

class HypothesisHandler {

    public function getSubmissionsWithAnnotations($contextId) {
        $submissions = Services::get('submission')->getMany([
            'contextId' => $contextId
        ]);

        $submissions = iterator_to_array($submissions);
        $groupSize = 50;
        $submissionGroups = array_chunk($submissions, $groupSize);
        $submissionsWithAnnotations = [];
        
        foreach ($submissionGroups as $submissionGroup) {
            $groupResponse = $this->getSubmissionGroupAnnotations($submissionGroup);
            if (!is_null($groupResponse) && $groupResponse['total'] > 0) {
                $submissionsWithAnnotations[] = array_merge(
                    $submissionsWithAnnotations,
                    $this->getWhichSubmissionsHaveAnnotations($groupResponse)
                );
            }
        }

        return $submissionsWithAnnotations;
    }

    private function getSubmissionGroupAnnotations($submissionGroup) {
        $requestURL = "https://hypothes.is/api/search?limit=200&group=__world__";
        foreach ($submissionGroup as $submission) {
            $publication = $submission->getCurrentPublication();
            $galleys = $publication->getData('galleys');

            foreach ($galleys as $galley) {
                $galleyDownloadURL = $this->getGalleyDownloadURL($galley);
                if(!is_null($galleyDownloadURL))
                    $requestURL .= "&uri={$galleyDownloadURL}";
            }
        }

        return json_decode(file_get_contents($requestURL), true);
    }

    private function getWhichSubmissionsHaveAnnotations($groupResponse) {
        $submissions = [];

        foreach ($groupResponse['rows'] as $annotation) {
            $submissionId = (int) array_slice(explode("/", $annotation['uri']), -3, 1);
            $submissions[$submissionId] = $submissionId;
        }

        return $submissions;
    }

    public function getGalleyDownloadURL($galley) {
        $request = Application::get()->getRequest();
        $indexUrl = $request->getIndexUrl();
        $contextPath = $request->getContext()->getPath();
        
        $submissionFile = $galley->getFile();
        if(is_null($submissionFile))
            return null;

        $submissionId = $submissionFile->getData('submissionId');
        $assocId = $submissionFile->getData('assocId');
        $submissionFileId = $submissionFile->getId();
        
        return $indexUrl . "/$contextPath/preprint/download/$submissionId/$assocId/$submissionFileId";
    }

}
