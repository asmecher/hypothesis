<?php

/**
 * @defgroup plugins_generic_hypothesis
 */
 
/**
 * @file plugins/generic/hypothesis/index.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_hypothesis
 * @brief Wrapper for Hypothes.is plugin.
 *
 */

require_once('HypothesisPlugin.inc.php');

return new HypothesisPlugin();

