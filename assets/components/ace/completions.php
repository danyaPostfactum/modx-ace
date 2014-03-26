<?php
/**
 * Ace Connector
 *
 * @package ace
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$aceCorePath = $modx->getOption('ace.core_path', null, $modx->getOption('core_path').'components/ace/');
$modx->request->handleRequest(array(
    'processors_path' => $aceCorePath . 'processors/',
    'location' => 'completions',
));