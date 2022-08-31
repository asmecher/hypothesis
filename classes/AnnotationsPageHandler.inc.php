<?php

import('classes.handler.Handler');
import('plugins.generic.hypothesis.classes.HypothesisHandler');

class AnnotationsPageHandler extends Handler {
    
    function index($args, $request) {
        $plugin = PluginRegistry::getPlugin('generic', 'hypothesisplugin');

        $contextId = $request->getContext()->getId();
        $submissionsWithAnnotations = $this->getSubmissionsWithAnnotations($contextId);

        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('submissionsWithAnnotations', $submissionsWithAnnotations);
        return $templateMgr->display($plugin->getTemplateResource('submissionsWithAnnotations.tpl'));
    }

    private function getSubmissionsWithAnnotations($contextId) {
        $cacheManager = CacheManager::getManager();
		$cache = $cacheManager->getFileCache(
			$contextId,
			'submissions_with_annotations',
			[$this, 'cacheDismiss']
		);

        $submissionsWithAnnotations = $cache->getContents();
		$currentCacheTime = time() - $cache->getCacheTime();
        
		if (is_null($submissionsWithAnnotations)){
			$cache->flush();
            $hypothesisHandler = new HypothesisHandler();
			$cache->setEntireCache($hypothesisHandler->getSubmissionsWithAnnotations($contextId));
            $submissionsWithAnnotations = $cache->getContents();
		}

        foreach ($submissionsWithAnnotations as $index => $submissionId) {
            $submissionsWithAnnotations[$index] = Services::get('submission')->get($submissionId);
        }

        return $submissionsWithAnnotations;
    }

    function cacheDismiss() {
		return null;
	}

}
