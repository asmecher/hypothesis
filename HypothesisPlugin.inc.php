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

class HypothesisPlugin extends GenericPlugin {
	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId = null) {
		if (parent::register($category, $path, $mainContextId)) {
			HookRegistry::register('ArticleHandler::download', array(&$this, 'callback'));
			HookRegistry::register('TemplateManager::display', array(&$this, 'callbackTemplateDisplay'));
			HookRegistry::register('TemplateManager::display', [$this, 'addAnnotationNumberViewers']);
			HookRegistry::register('LoadHandler', array($this, 'addAnnotationsHandler'));
			HookRegistry::register('LoadComponentHandler', array($this, 'setupHypothesisHandler'));
			HookRegistry::register('AcronPlugin::parseCronTab', [$this, 'addTasksToCrontab']);

			$this->addHandlerURLToJavaScript();

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
			$templateMgr->registerFilter("output", array($this, 'addHypothesisConfig'));
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
	 * Adds Hypothesis tab configuration so sidebar opens automatically when PDF has annotations
	 * @param $output string
	 * @param $templateMgr TemplateManager
	 * @return $string
	 */
	public function addHypothesisConfig($output, $templateMgr) {
		if (preg_match('/<div[^>]+id="pdfCanvasContainer/', $output, $matches, PREG_OFFSET_CAPTURE)) {
            $posMatch = $matches[0][1];
			$config = $templateMgr->fetch($this->getTemplateResource('hypothesisConfig.tpl'));

            $output = substr_replace($output, $config, $posMatch, 0);
            $templateMgr->unregisterFilter('output', array($this, 'addHypothesisConfig'));
        }
		return $output;
	}

	public function addAnnotationNumberViewers($hookName, $args) {
		$templateMgr = $args[0];
		$template = $args[1];
		$pagesToInsert = [
			'frontend/pages/indexServer.tpl',
			'frontend/pages/preprint.tpl',
			'frontend/pages/preprints.tpl',
			'frontend/pages/sections.tpl',
			'frontend/pages/indexJournal.tpl',
			'frontend/pages/article.tpl',
			'frontend/pages/issue.tpl'
		];

		if (in_array($template, $pagesToInsert)) {
            $request = Application::get()->getRequest();

            $jsUrl = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/js/addAnnotationViewers.js';
            $styleUrl = $request->getBaseUrl() . '/' . $this->getPluginPath() . '/styles/annotationViewer.css';

            $templateMgr->addJavascript('AddAnnotationViewers', $jsUrl, ['contexts' => 'frontend']);
            $templateMgr->addStyleSheet('AnnotationViewerStyleSheet', $styleUrl, ['contexts' => 'frontend']);
        }

		return false;
	}

	public function addAnnotationsHandler($hookName, $args) {
		$page = $args[0];
		if ($page == 'annotations') {
			$this->import('pages.annotations.AnnotationsHandler');
			define('HANDLER_CLASS', 'AnnotationsHandler');
			return true;
		}
		return false;
	}

	public function setupHypothesisHandler($hookName, $args) {
		$component = &$args[0];
        if ($component == 'plugins.generic.hypothesis.controllers.HypothesisHandler') {
            return true;
        }
        return false;
	}

	public function addTasksToCrontab($hookName, $args) {
		$taskFilesPath = &$args[0];
        $taskFilesPath[] = $this->getPluginPath() . DIRECTORY_SEPARATOR . 'scheduledTasks.xml';
        return false;
	}

	public function addHandlerURLToJavaScript()
    {
        $request = Application::get()->getRequest();
        $templateMgr = TemplateManager::getManager($request);
        $handlerUrl = $request->getDispatcher()->url($request, ROUTE_COMPONENT, null, 'plugins.generic.hypothesis.controllers.HypothesisHandler');
        $data = ['hypothesisHandlerUrl' => $handlerUrl];

        $templateMgr->addJavaScript('HypothesisHandler', 'app = ' . json_encode($data) . ';', ['contexts' => 'frontend', 'inline' => true]);
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

