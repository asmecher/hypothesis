<?php

define('ORDER_BY_DATE_PUBLISHED', 'datePublished');
define('ORDER_BY_LAST_ANNOTATION', 'lastAnnotation');

import('classes.handler.Handler');
import('plugins.generic.hypothesis.classes.HypothesisHandler');
import('plugins.generic.hypothesis.classes.HypothesisDAO');

class AnnotationsPageHandler extends Handler {
    
    function index($args, $request) {
        $plugin = PluginRegistry::getPlugin('generic', 'hypothesisplugin');
        $context = $request->getContext();
        
        $paginationParams = $this->getPaginationParams($args, $request, $context);
        $pubIdPlugins = PluginRegistry::loadCategory('pubIds', true, $context->getId());

        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign($paginationParams);
        $templateMgr->assign('pubIdPlugins', $pubIdPlugins);
        $templateMgr->assign('journal', $context);

        $jsUrl = $request->getBaseUrl() . '/' . $plugin->getPluginPath() . '/js/load.js';
		$templateMgr->addJavascript('AnnotationsPage', $jsUrl, ['contexts' => 'frontend']);

        return $templateMgr->display($plugin->getTemplateResource('annotationsPage.tpl'));
    }

    private function getPaginationParams($args, $request, $context): array {
        $page = isset($args[0]) ? (int) $args[0] : 1;
        $itemsPerPage = $context->getData('itemsPerPage') ? $context->getData('itemsPerPage') : Config::getVar('interface', 'items_per_page');
		$offset = $page > 1 ? ($page - 1) * $itemsPerPage : 0;

        $orderBy = ($request->getUserVar('orderBy') ?? ORDER_BY_LAST_ANNOTATION);
        $submissionsAnnotations = $this->getSubmissionsAnnotations($context->getId(), $orderBy);
        $pageAnnotations = array_slice($submissionsAnnotations, $offset, $itemsPerPage);

        $total = count($submissionsAnnotations);
        $showingStart = $offset + 1;
		$showingEnd = min($offset + $itemsPerPage, $offset + count($pageAnnotations));
		$nextPage = $total > $showingEnd ? $page + 1 : null;
		$prevPage = $showingStart > 1 ? $page - 1 : null;

        foreach ($pageAnnotations as $submissionAnnotation) {
            $submissionId = $submissionAnnotation->submissionId;
            $submissionAnnotation->submission = Services::get('submission')->get($submissionId);
        }

        return [
            'submissionsAnnotations' => $pageAnnotations,
            'orderBy' => $orderBy,
            'showingStart' => $showingStart,
            'showingEnd' => $showingEnd,
            'total' => $total,
            'nextPage' => $nextPage,
            'prevPage' => $prevPage
        ];
    }

    private function getSubmissionsAnnotations($contextId, $orderBy) {
        $cacheManager = CacheManager::getManager();
		$cache = $cacheManager->getFileCache(
			$contextId,
			'submissions_annotations',
			[$this, 'cacheDismiss']
		);

        $submissionsAnnotations = $cache->getContents();
        
		if (is_null($submissionsAnnotations)){
			$cache->flush();
            $hypothesisHandler = new HypothesisHandler();
			$cache->setEntireCache($hypothesisHandler->getSubmissionsAnnotations($contextId));
            $submissionsAnnotations = $cache->getContents();
		}

        $orderingFunction = $orderBy.'Ordering';
        usort($submissionsAnnotations, [$this, $orderingFunction]);

        return $submissionsAnnotations;
    }

    public function lastAnnotationOrdering($submissionAnnotationsA, $submissionAnnotationsB) {
        $lastAnnotationA = $submissionAnnotationsA->annotations[0];
        $lastAnnotationB = $submissionAnnotationsB->annotations[0];

        if($lastAnnotationA->dateCreated == $lastAnnotationB->dateCreated) return 0;

        return ($lastAnnotationA->dateCreated < $lastAnnotationB->dateCreated) ? 1 : -1;
    }

    public function datePublishedOrdering($submissionAnnotationsA, $submissionAnnotationsB) {
        $hypothesisDao = new HypothesisDAO();
        $datePublishedA = $hypothesisDao->getDatePublished($submissionAnnotationsA->submissionId);
        $datePublishedB = $hypothesisDao->getDatePublished($submissionAnnotationsB->submissionId);

        if($datePublishedA == $datePublishedB) return 0;

        return ($datePublishedA < $datePublishedB) ? 1 : -1;
    }

    function cacheDismiss() {
		return null;
	}

}
