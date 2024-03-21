<?php

use APP\handler\Handler;
use APP\facades\Repo;

class HypothesisHandler extends Handler {
    public function getAnnotationViewerData($args, $request) {
        $galleyUrl = $args['galleyUrl'];
        $explodedUrl = explode('/', $galleyUrl);
        $galleyId = (int) end($explodedUrl);
        
        $galley = Repo::galley()->get($galleyId);
        if(!$galley) {
            return json_encode(null);
        }
        
        $fileId = $galley->getFile()->getId();
        $galleyDownloadUrl = str_replace('view', 'download', $galleyUrl);
        $galleyDownloadUrl .= "/$fileId";

        $response = file_get_contents("https://hypothes.is/api/search?limit=0&group=__world__&uri={$galleyDownloadUrl}");
        $response = json_decode($response, true);

        if ($response['total'] > 0) {
            $suffix = ($response['total'] == 1 ? 'annotation' : 'annotations');
            $message = $response['total'] . ' ' . __("plugins.generic.hypothesis.$suffix");
            return json_encode(['message' => $message]);
        }
        
        return json_encode(null);
    }
}