<?php

import('classes.handler.Handler');
import('plugins.generic.hypothesis.classes.HypothesisHandler');

class AnnotationsPageHandler extends Handler {
    
    function index($args, $request) {
        $plugin = PluginRegistry::getPlugin('generic', 'hypothesisplugin');

        $contextId = $request->getContext()->getId();
        $hypothesisHandler = new HypothesisHandler();
        $submissionsWithAnnotations = $hypothesisHandler->getSubmissionsWithAnnotations($contextId);

        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('submissionsWithAnnotations', $submissionsWithAnnotations);
        return $templateMgr->display($plugin->getTemplateResource('submissionsWithAnnotations.tpl'));
    }

}
