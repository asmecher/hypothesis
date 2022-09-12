<?php

import('classes.handler.Handler');
import('plugins.generic.hypothesis.classes.HypothesisHandler');

class AnnotationsPageHandler extends Handler {
    
    function index($args, $request) {
        $plugin = PluginRegistry::getPlugin('generic', 'hypothesisplugin');
        $context = $request->getContext();
        
        $paginationParams = $this->getPaginationParams($args, $context);

        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign($paginationParams);
        return $templateMgr->display($plugin->getTemplateResource('submissionsWithAnnotations.tpl'));
    }

    private function getPaginationParams($args, $context): array {
        $page = isset($args[0]) ? (int) $args[0] : 1;
        $itemsPerPage = $context->getData('itemsPerPage') ? $context->getData('itemsPerPage') : Config::getVar('interface', 'items_per_page');
		$offset = $page > 1 ? ($page - 1) * $itemsPerPage : 0;

        $submissionsWithAnnotations = $this->getSubmissionsWithAnnotations($context->getId());
        $pageSubmissions = array_slice($submissionsWithAnnotations, $offset, $itemsPerPage);

        $total = count($submissionsWithAnnotations);
        $showingStart = $offset + 1;
		$showingEnd = min($offset + $itemsPerPage, $offset + count($pageSubmissions));
		$nextPage = $total > $showingEnd ? $page + 1 : null;
		$prevPage = $showingStart > 1 ? $page - 1 : null;

        foreach ($pageSubmissions as $index => $submissionId) {
            $pageSubmissions[$index] = Services::get('submission')->get($submissionId);
        }

        return [
            'submissionsWithAnnotations' => $pageSubmissions,
            'showingStart' => $showingStart,
            'showingEnd' => $showingEnd,
            'total' => $total,
            'nextPage' => $nextPage,
            'prevPage' => $prevPage
        ];
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

        return $submissionsWithAnnotations;
    }

    function cacheDismiss() {
		return null;
	}

}
