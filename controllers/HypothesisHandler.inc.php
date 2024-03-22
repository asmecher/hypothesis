<?php

import('classes.handler.Handler');

class HypothesisHandler extends Handler {
    public function getAnnotationViewerData($args, $request) {
        $galleyUrl = $args['galleyUrl'];
        $explodedUrl = explode('/', $galleyUrl);
        $galleyId = (int) end($explodedUrl);

        $galleyDao = DAORegistry::getDAO('ArticleGalleyDAO');
        $galley = $galleyDao->getById($galleyId);
        if(!$galley) {
            return json_encode(null);
        }

        $fileId = $galley->getFile()->getId();
        $galleyDownloadUrl = str_replace('view', 'download', $galleyUrl);
        $galleyDownloadUrl .= "/$fileId";

        return json_encode([
            'downloadUrl' => $galleyDownloadUrl,
            'annotationMsg' => __('plugins.generic.hypothesis.annotation'),
            'annotationsMsg' => __('plugins.generic.hypothesis.annotations')
        ]);
    }
}