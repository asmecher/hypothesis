<?php

use APP\handler\Handler;
use APP\facades\Repo;

class HypothesisHandler extends Handler {
    public function getGalleyDownloadUrl($args, $request) {
        $galleyUrl = $args['galleyUrl'];
        $explodedUrl = explode('/', $galleyUrl);
        $galleyId = (int) end($explodedUrl);
        $galley = Repo::galley()->get($galleyId);
        $fileId = $galley->getFile()->getId();
        
        $galleyDownloadUrl = str_replace('view', 'download', $galleyUrl);
        $galleyDownloadUrl .= "/$fileId";
        
        return json_encode($galleyDownloadUrl);
    }
}