<?php

/**
 * @file plugins/generic/hypothesis/HypothesisPlugin.inc.php
 *
 * Copyright (c) 2013-2015 Simon Fraser University Library
 * Copyright (c) 2003-2015 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class HypothesisPlugin
 * @ingroup plugins_generic_hypothesis
 *
 * @brief Hypothesis annotation/discussion integration
 */


import('lib.pkp.classes.plugins.GenericPlugin');

class HypothesisPlugin extends GenericPlugin {
	/**
	 * Register the plugin, if enabled; note that this plugin
	 * runs under both Journal and Site contexts.
	 * @param $category string
	 * @param $path string
	 * @return boolean
	 */
	function register($category, $path) {
		if (parent::register($category, $path)) {
			HookRegistry::register('TemplateManager::display',array(&$this, 'callback'));
			return true;
		}
		return false;
	}

	/**
	 * Hook callback function for TemplateManager::display
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function callback($hookName, $args) {
		// Only pages requests interest us here
		$request =& Registry::get('request');
		if (!is_a($request->getRouter(), 'PKPPageRouter')) return null;

		$page = Request::getRequestedPage();
		$op = Request::getRequestedOp();

		switch ("$page/$op") {
			case 'article/view':
				$templateManager =& $args[0];
				$additionalHeadData = $templateManager->get_template_vars('additionalHeadData');
				$templateManager->assign(
					'additionalHeadData',
					$templateManager->get_template_vars('additionalHeadData') .
					'<script async defer src="//hypothes.is/embed.js"></script>'
				);
				break;
		}
		return false;
	}

	/**
	 * Get the display name of this plugin
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.generic.hypothesis.name');
	}

	/**
	 * Get the description of this plugin
	 * @return string
	 */
	function getDescription() {
		return __('plugins.generic.hypothesis.description');
	}
}

?>
