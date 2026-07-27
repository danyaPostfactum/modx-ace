<?php
/**
 * Build config for Ace. Compatible with MODX 2 and MODX 3.
 * MODX root is resolved by: MODX_BASE_PATH env, or core/config/config.inc.php, or core/model/modx/modx.class.php.
 */

define('PKG_NAME', 'Ace');
define('PKG_NAMESPACE', 'ace');
define('PKG_VERSION', '1.9.11');
define('PKG_RELEASE', 'pl');
define('PKG_AUTO_INSTALL', true);

if (!defined('MODX_BASE_PATH')) {
    if (!empty($_SERVER['MODX_BASE_PATH'])) {
        define('MODX_BASE_PATH', rtrim($_SERVER['MODX_BASE_PATH'], '/') . '/');
    } elseif (getenv('MODX_BASE_PATH')) {
        define('MODX_BASE_PATH', rtrim(getenv('MODX_BASE_PATH'), '/') . '/');
    } else {
        $buildDir = dirname(__FILE__);
        $basePath = null;
        // Search by core/config/config.inc.php (e.g. component in Extras/)
        $path = $buildDir;
        while ($path !== '/' && strlen($path) > 1) {
            if (file_exists($path . '/core/config/config.inc.php')) {
                $basePath = $path . '/';
                break;
            }
            $path = dirname($path);
        }
        // Fallback: search by core/model/modx/modx.class.php
        if (!$basePath) {
            $modxClass = 'core/model/modx/modx.class.php';
            foreach ([3, 4, 5, 6] as $levels) {
                $path = $buildDir;
                for ($i = 0; $i < $levels; $i++) {
                    $path = dirname($path);
                }
                $path .= '/';
                if (file_exists($path . $modxClass)) {
                    $basePath = $path;
                    break;
                }
            }
        }
        define('MODX_BASE_PATH', $basePath ?: (dirname(dirname(dirname(dirname(__FILE__)))) . '/'));
    }
}

define('MODX_CORE_PATH', MODX_BASE_PATH . 'core/');
define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');

define('MODX_BASE_URL', '/');
define('MODX_CORE_URL', MODX_BASE_URL . 'core/');
define('MODX_MANAGER_URL', MODX_BASE_URL . 'manager/');
define('MODX_CONNECTORS_URL', MODX_BASE_URL . 'connectors/');
define('MODX_ASSETS_URL', MODX_BASE_URL . 'assets/');
