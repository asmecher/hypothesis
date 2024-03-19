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


namespace APP\plugins\generic\hypothesis;

use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

class HypothesisPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId = null) {
		if (parent::register($category, $path, $mainContextId)) {
			Hook::add('ArticleHandler::download',array(&$this, 'callback'));
			Hook::add('TemplateManager::display', array(&$this, 'callbackTemplateDisplay'));
			Hook::add('LoadHandler', [$this, 'addAnnotationsHandler']);
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
		$submissiontpl = 'submissionGalley.tpl';
		$issuetpl = 'issueGalley.tpl';
		
		// template path contains the plugin path, and ends with the tpl file
		if ( (strpos($template, $plugin) !== false) && (  (strpos($template, ':'.$submissiontpl,  -1 - strlen($submissiontpl)) !== false)  ||  (strpos($template, ':'.$issuetpl,  -1 - strlen($issuetpl)) !== false))) {
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

	public function addAnnotationsHandler($hookName, $args)
    {
        $page = $args[0];
        if ($page == 'annotations') {
            define('HANDLER_CLASS', 'APP\plugins\generic\hypothesis\pages\annotations\AnnotationsHandler');
            return true;
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

