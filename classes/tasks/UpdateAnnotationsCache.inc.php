<?php

import('lib.pkp.classes.scheduledTask.ScheduledTask');
import('plugins.generic.hypothesis.classes.HypothesisHelper');

class UpdateAnnotationsCache extends ScheduledTask
{
	public function executeActions()
	{
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

	public function cacheDismiss()
	{
		return null;
	}
}
