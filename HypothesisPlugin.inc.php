<?php

/**
 * @file HypothesisPlugin.inc.php
 *
 * Copyright (c) 2013-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class HypothesisPlugin
 * @brief Hypothesis annotation/discussion integration
 */


import('lib.pkp.classes.plugins.GenericPlugin');
import('plugins.generic.hypothesis.classes.HypothesisClient');

class HypothesisPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId = null) {
		if (parent::register($category, $path, $mainContextId)) {
			HookRegistry::register('ArticleHandler::download',array(&$this, 'callback'));
			HookRegistry::register('TemplateManager::display', array(&$this, 'callbackTemplateDisplay'));
			HookRegistry::register('Hypothesis::annotationNumber', array(&$this, 'addAnnotationNumberViewer'));
			
			$request = PKPApplication::get()->getRequest();
			$templateMgr = TemplateManager::getManager($request);
			$templateMgr->addStyleSheet(
				'Hypothesis',
				$request->getBaseUrl() . '/' . $this->getPluginPath() . '/styles/annotationViewer.css',
				['contexts' => ['frontend']]
			);

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
		$plugin = 'plugins-generic-pdfJsViewer';
		$submissionGalleyTpl = 'submissionGalley.tpl';
		$issueGalleyTpl = 'issueGalley.tpl';

		// template path contains the plugin path, and ends with the tpl file
		if ( (strpos($template, $plugin) !== false) && (  (strpos($template, ':'.$submissionGalleyTpl,  -1 - strlen($submissionGalleyTpl)) !== false)  ||  (strpos($template, ':'.$issueGalleyTpl,  -1 - strlen($issueGalleyTpl)) !== false))) {
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

	public function addAnnotationNumberViewer($hookName, $args) {
		$templateMgr = $args[1];
		$output =& $args[2];
		$galley = $args[0]['galley'];
		
		$hypothesisClient = new HypothesisClient();
		$annotationsNumber = $hypothesisClient->getGalleyAnnotationsNumber($galley);

		if($annotationsNumber > 0) {
			$templateMgr->assign('annotationsNumber', $annotationsNumber);
			$output .= $templateMgr->fetch($this->getTemplateResource('annotationViewer.tpl'));
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

	function getStyleSheet() {
		return $this->getPluginPath() . '/styles/annotationViewer.css';
	}
}

