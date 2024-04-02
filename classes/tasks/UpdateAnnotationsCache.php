<?php

namespace APP\plugins\generic\hypothesis\classes\tasks;

use PKP\scheduledTask\ScheduledTask;
use APP\core\Services;
use PKP\cache\CacheManager;
use APP\plugins\generic\hypothesis\classes\HypothesisHelper;

class UpdateAnnotationsCache extends ScheduledTask {

	public function executeActions() {
        $contextIds = Services::get('context')->getIds([
			'isEnabled' => true,
		]);

        foreach ($contextIds as $contextId) {
            $cacheManager = CacheManager::getManager();
            $cache = $cacheManager->getFileCache(
                $contextId,
                'submissions_annotations',
                [$this, 'cacheDismiss']
            );

            $cache->flush();
            $hypothesisHelper = new HypothesisHelper();
			$cache->setEntireCache($hypothesisHelper->getSubmissionsAnnotations($contextId));
        }

		return true;
	}

    function cacheDismiss() {
		return null;
	}
}
