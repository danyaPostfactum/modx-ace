<?php

define('PKG_NAME','Ace');
define('PKG_NAMESPACE','ace');
define('PKG_VERSION','1.6.5');
define('PKG_RELEASE','pl');
define('PKG_AUTO_INSTALL', true);

if (isset($_SERVER['MODX_BASE_PATH'])) {
	define('MODX_BASE_PATH', $_SERVER['MODX_BASE_PATH']);
}
elseif (file_exists(dirname(dirname(dirname(__FILE__))) . '/core')) {
	define('MODX_BASE_PATH', dirname(dirname(dirname(__FILE__))) . '/');
}
else {
	define('MODX_BASE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
}

define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');
define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');
 
define('MODX_BASE_URL','/');
define('MODX_CORE_URL', MODX_BASE_URL . 'core/');
define('MODX_MANAGER_URL', MODX_BASE_URL . 'manager/');
define('MODX_CONNECTORS_URL', MODX_BASE_URL . 'connectors/');
define('MODX_ASSETS_URL', MODX_BASE_URL . 'assets/');