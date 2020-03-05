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
			HookRegistry::register('ArticleHandler::download',array(&$this, 'callback'));
			HookRegistry::register('TemplateManager::display', array(&$this, 'callbackTemplateDisplay'));
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
		$galley =& $args[1];
		if (!$galley || $galley->getFileType() != 'text/html') return false;

		ob_start(function($buffer) {
			return str_replace('<head>', '<head><script async defer src="//hypothes.is/embed.js"></script>', $buffer);
		});

		return false;
	}

	/**
	 * Hook callback function for TemplateManager::display
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function callbackTemplateDisplay($hookName, $args) {
		if ($hookName != 'TemplateManager::display') return false;
		$templateMgr = $args[0];
		$template = $args[1];
		if ($template == 'plugins-plugins-generic-pdfJsViewer-generic-pdfJsViewer:articleGalley.tpl') {
			$templateMgr->registerFilter("output", array($this, 'changePdfjsPath'));
		}
		return false;
	}

	/**
	 * Output filter to create a new element in a registration form
	 * @param $output string
	 * @param $templateMgr TemplateManager
	 * @return $string
	 */
	function changePdfjsPath($output, $templateMgr) {
		$newOutput = str_replace('pdfJsViewer/pdf.js/web/viewer.html?file=', 'hypothesis/pdf.js/viewer/web/viewer.html?file=', $output);
		return $newOutput;
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
