<?php

namespace APP\plugins\generic\hypothesis\pages\annotations;

use APP\handler\Handler;
use PKP\plugins\PluginRegistry;
use APP\template\TemplateManager;
use APP\core\Application;
use PKP\config\Config;
use APP\facades\Repo;
use PKP\cache\CacheManager;
use PKP\security\authorization\ContextRequiredPolicy;
use APP\plugins\generic\hypothesis\classes\HypothesisHelper;
use APP\plugins\generic\hypothesis\classes\HypothesisDAO;

class AnnotationsHandler extends Handler {

    private const ORDER_BY_DATE_PUBLISHED = 'datePublished';
    private const ORDER_BY_LAST_ANNOTATION = 'lastAnnotation';

    public function authorize($request, &$args, $roleAssignments)
    {
        $this->addPolicy(new ContextRequiredPolicy($request));
        
        if (Application::getName() == 'ojs2') {
            $this->addPolicy(new \APP\security\authorization\OjsJournalMustPublishPolicy($request));
        } else {
            $this->addPolicy(new \APP\security\authorization\OpsServerMustPublishPolicy($request));
        }

        return parent::authorize($request, $args, $roleAssignments);
    }

    public function index($args, $request) {
        $plugin = PluginRegistry::getPlugin('generic', 'hypothesisplugin');
        $context = $request->getContext();

        $paginationParams = $this->getPaginationParams($args, $request, $context);
        $pubIdPlugins = PluginRegistry::loadCategory('pubIds', true, $context->getId());

        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign($paginationParams);
        $templateMgr->assign('pubIdPlugins', $pubIdPlugins);
        $templateMgr->assign('context', $context);
        $templateMgr->assign('application', Application::getName());

        $jsUrl = $request->getBaseUrl() . '/' . $plugin->getPluginPath() . '/js/load.js';
		$templateMgr->addJavascript('AnnotationsPage', $jsUrl, ['contexts' => 'frontend']);

        return $templateMgr->display($plugin->getTemplateResource('annotationsPage.tpl'));
    }

    private function getPaginationParams($args, $request, $context): array {
        $page = isset($args[0]) ? (int) $args[0] : 1;
        $itemsPerPage = $context->getData('itemsPerPage') ? $context->getData('itemsPerPage') : (int) Config::getVar('interface', 'items_per_page');
		$offset = $page > 1 ? ($page - 1) * $itemsPerPage : 0;

        $orderBy = ($request->getUserVar('orderBy') ?? self::ORDER_BY_LAST_ANNOTATION);
        $submissionsAnnotations = $this->getSubmissionsAnnotations($context->getId(), $orderBy);
        $pageAnnotations = array_slice($submissionsAnnotations, $offset, $itemsPerPage);

        $total = count($submissionsAnnotations);
        $showingStart = $offset + 1;
		$showingEnd = min($offset + $itemsPerPage, $offset + count($pageAnnotations));
		$nextPage = $total > $showingEnd ? $page + 1 : null;
		$prevPage = $showingStart > 1 ? $page - 1 : null;

        foreach ($pageAnnotations as $submissionAnnotation) {
            $submissionAnnotation->submission = Repo::submission()->get($submissionAnnotation->submissionId);
        }

        return [
            'submissionsAnnotations' => $pageAnnotations,
            'orderBy' => $orderBy,
            'showingStart' => $showingStart,
            'showingEnd' => $showingEnd,
            'total' => $total,
            'nextPage' => $nextPage,
            'prevPage' => $prevPage,
            'authorUserGroups' => Repo::userGroup()->getCollector()
                ->filterByRoleIds([\PKP\security\Role::ROLE_ID_AUTHOR])
                ->filterByContextIds([$context->getId()])
                ->getMany()->remember(),
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
            $hypothesisHelper = new HypothesisHelper();
			$cache->setEntireCache($hypothesisHelper->getSubmissionsAnnotations($contextId));
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