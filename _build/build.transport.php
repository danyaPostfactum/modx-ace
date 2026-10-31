<?php
/**
 * Ace build script. Compatible with MODX 2 and MODX 3.
 *
 * @package ace
 * @subpackage build
 */

$tstart = microtime(true);
set_time_limit(0);

if (php_sapi_name() === 'cli' && PHP_VERSION_ID >= 80200) {
    $buildErrorReporting = error_reporting(E_ALL & ~E_DEPRECATED);
}

header('Content-Type:text/html;charset=utf-8');
require_once dirname(__FILE__) . '/build.config.php';

$root = dirname(dirname(__FILE__)) . '/';
$sources = [
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'lexicon' => $root . 'core/components/' . PKG_NAMESPACE . '/lexicon/',
    'documents' => $root . 'core/components/' . PKG_NAMESPACE . '/documents/',
    'elements' => $root . 'core/components/' . PKG_NAMESPACE . '/elements/',
    'source_assets' => $root . 'assets/components/' . PKG_NAMESPACE,
    'source_core' => $root . 'core/components/' . PKG_NAMESPACE,
];
unset($root);

/* load MODX */
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
echo defined('XPDO_CLI_MODE') && XPDO_CLI_MODE ? '' : '<pre>';
$modx->setLogTarget('ECHO');

/* MODX 2 / MODX 3 compatibility: load Transport classes */
$isModx3 = false;
$modx->loadClass('transport.modPackageBuilder', '', false, true);
if (!class_exists('modPackageBuilder', false)) {
    $modx3Transport = MODX_CORE_PATH . 'src/Revolution/Transport/';
    if (file_exists($modx3Transport . 'modPackageBuilder.php')) {
        require_once $modx3Transport . 'modPackageBuilder.php';
        class_alias('MODX\Revolution\Transport\modPackageBuilder', 'modPackageBuilder');
        $isModx3 = true;
    }
    if (!class_exists('modTransportPackage', false) && file_exists($modx3Transport . 'modTransportPackage.php')) {
        require_once $modx3Transport . 'modTransportPackage.php';
        class_alias('MODX\Revolution\Transport\modTransportPackage', 'modTransportPackage');
    }
}
if (!class_exists('xPDOTransport', false) && class_exists('xPDO\Transport\xPDOTransport', false)) {
    class_alias('xPDO\Transport\xPDOTransport', 'xPDOTransport');
}

$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAMESPACE, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAMESPACE, false, true, '{core_path}components/' . PKG_NAMESPACE . '/');

/* create the plugin object */
$plugin = $modx->newObject('modPlugin');
$plugin->set('id', 1);
$plugin->set('name', PKG_NAME);
$plugin->set('description', 'Ace code editor plugin for MODx Revolution');
$plugin->set('static', false);
$plugin->set('static_file', PKG_NAMESPACE . '/elements/plugins/' . PKG_NAMESPACE . '.plugin.php');
$plugin->set('plugincode', file_get_contents($sources['source_core'] . '/elements/plugins/' . PKG_NAMESPACE . '.plugin.php'));
$plugin->set('category', 0);

/* add plugin events */
$events = include $sources['data'] . 'transport.plugin.events.php';
if (is_array($events) && !empty($events)) {
    $plugin->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_INFO, '✓ Packaged ' . count($events) . ' plugin events');
    flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not find plugin events!');
    flush();
}
unset($events);

/* load plugin properties */
//$properties = include $sources['data'].'properties.inc.php';
//$plugin->setProperties($properties);
//$modx->log(xPDO::LOG_LEVEL_INFO,'Setting '.count($properties).' Plugin Properties.'); flush();

$attributes = [
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::PREEXISTING_MODE => xPDOTransport::REMOVE_PREEXISTING,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
        'PluginEvents' => [
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
            xPDOTransport::UNIQUE_KEY => ['pluginid', 'event'],
        ],
    ],
];
$vehicle = $builder->createVehicle($plugin, $attributes);

$modx->log(modX::LOG_LEVEL_INFO, '[Ace] Adding file resolvers...');
$vehicle->resolve('file', [
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
]);
$vehicle->resolve('file', [
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
]);
$vehicle->resolve('php', [
    'source' => $sources['data'] . 'transport.resolver.php',
    'name' => 'resolve',
    'type' => 'php',
]);
$builder->putVehicle($vehicle);

/* load system settings */
$settings = include $sources['data'] . 'transport.settings.php';
if (is_array($settings) && !empty($settings)) {
    $attributes = [
        xPDOTransport::UNIQUE_KEY => 'key',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => false,
    ];
    foreach ($settings as $setting) {
        $vehicle = $builder->createVehicle($setting, $attributes);
        $builder->putVehicle($vehicle);
    }
    $modx->log(xPDO::LOG_LEVEL_INFO, '✓ Packaged ' . count($settings) . ' system settings');
    flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR, 'Could not package System Settings.');
}
unset($settings, $setting);

$modx->log(modX::LOG_LEVEL_INFO, '[Ace] Setting package attributes (license, readme, changelog)...');
$builder->setPackageAttributes([
    'license' => file_get_contents($sources['documents'] . 'license.txt'),
    'readme' => file_get_contents($sources['documents'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['documents'] . 'changelog.txt'),
]);

/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO, '[Ace] Packing transport package...');
$builder->pack();

$signature = $builder->getSignature();
if ($isModx3 && class_exists('ZipArchive')) {
    // MODX 2 get() uses loadClass("{$vehicle_package}.{$vehicle_class}") with default vehicle_package='transport'.
    // So vehicle_class must be just the class name: xPDOObjectVehicle, xPDOFileVehicle (file under core/xpdo/transport/).
    $vehicleClassMap = [
        'transport.xPDO\Transport\xPDOObjectVehicle' => 'xPDOObjectVehicle',
        'transport.xPDO\Transport\xPDOFileVehicle' => 'xPDOFileVehicle',
        'transport.xPDO\Transport\xPDOTransportVehicle' => 'xPDOTransportVehicle',
        'xPDO\Transport\xPDOObjectVehicle' => 'xPDOObjectVehicle',
        'xPDO\Transport\xPDOFileVehicle' => 'xPDOFileVehicle',
        'xPDO\Transport\xPDOTransportVehicle' => 'xPDOTransportVehicle',
        'transport.xPDO\\\\Transport\\\\xPDOObjectVehicle' => 'xPDOObjectVehicle',
        'transport.xPDO\\\\Transport\\\\xPDOFileVehicle' => 'xPDOFileVehicle',
        'transport.xPDO\\\\Transport\\\\xPDOTransportVehicle' => 'xPDOTransportVehicle',
        'xPDO\\\\Transport\\\\xPDOObjectVehicle' => 'xPDOObjectVehicle',
        'xPDO\\\\Transport\\\\xPDOFileVehicle' => 'xPDOFileVehicle',
        'xPDO\\\\Transport\\\\xPDOTransportVehicle' => 'xPDOTransportVehicle',
        'xpdo.transport.xpdoobjectvehicle' => 'xPDOObjectVehicle',
        'xpdo.transport.xpdofilevehicle' => 'xPDOFileVehicle',
        'xpdo.transport.xpdotransportvehicle' => 'xPDOTransportVehicle',
        'transport.xpdo.transport.xpdoobjectvehicle' => 'xPDOObjectVehicle',
        'transport.xpdo.transport.xpdofilevehicle' => 'xPDOFileVehicle',
        'transport.xpdo.transport.xpdotransportvehicle' => 'xPDOTransportVehicle',
    ];
    $vehiclePackageMap = [
        'xPDO\\Transport' => 'transport',
        'xPDO\Transport' => 'transport',
        'transport.xPDO\\Transport' => 'transport',
        'transport.xPDO\Transport' => 'transport',
        'xpdo.transport' => 'transport',
    ];
    $modxClassMap = [
        'MODX\\Revolution\\modNamespace' => 'modNamespace',
        'MODX\\Revolution\\modPlugin' => 'modPlugin',
        'MODX\\Revolution\\modPluginEvent' => 'modPluginEvent',
        'MODX\\Revolution\\modSystemSetting' => 'modSystemSetting',
        'MODX\\Revolution\\modCategory' => 'modCategory',
        'MODX\\Revolution\\modSnippet' => 'modSnippet',
        'MODX\\Revolution\\modChunk' => 'modChunk',
        'MODX\\Revolution\\modTemplate' => 'modTemplate',
        'MODX\\Revolution\\modTemplateVar' => 'modTemplateVar',
        'MODX\\Revolution\\' => '',
        'MODX\\\\Revolution\\\\' => '',
    ];
    $vehicleClassMap = array_merge($vehicleClassMap, $vehiclePackageMap, $modxClassMap);
    $patchVehicleClasses = function ($value) use (&$patchVehicleClasses, $vehicleClassMap) {
        if (is_array($value)) {
            $out = [];
            foreach ($value as $k => $v) {
                $out[$patchVehicleClasses($k)] = $patchVehicleClasses($v);
            }
            return $out;
        }
        if (is_string($value)) {
            return str_replace(array_keys($vehicleClassMap), array_values($vehicleClassMap), $value);
        }
        return $value;
    };
    $zipPath = MODX_CORE_PATH . 'packages/' . $signature . '.transport.zip';
    if (file_exists($zipPath)) {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            for ($i = $zip->numFiles - 1; $i >= 0; $i--) {
                $name = $zip->getNameIndex($i);
                $content = $zip->getFromIndex($i);
                if ($content === false) {
                    continue;
                }
                $hasVehicleClass = (strpos($content, 'xPDO') !== false && strpos($content, 'Transport') !== false)
                    || strpos($content, 'ObjectVehicle') !== false
                    || strpos($content, 'FileVehicle') !== false
                    || strpos($content, 'MODX\\') !== false
                    || strpos($content, 'MODX\\\\') !== false;
                if (!$hasVehicleClass) {
                    continue;
                }
                $newContent = null;
                if (preg_match('/^[aOs]:\d+:/', $content)) {
                    $data = @unserialize($content);
                    if ($data !== false || $content === 'b:0;') {
                        $newContent = serialize($patchVehicleClasses($data));
                    }
                }
                if ($newContent === null) {
                    $newContent = str_replace(
                        array_keys($vehicleClassMap),
                        array_values($vehicleClassMap),
                        $content
                    );
                }
                if ($newContent !== $content) {
                    $zip->deleteIndex($i);
                    $zip->addFromString($name, $newContent);
                }
            }
            $zip->close();
            $modx->log(modX::LOG_LEVEL_INFO, '[Ace] Patched transport for MODX 2 compatibility.');
        }
    }
}

$tend = explode(' ', microtime());
$tend = $tend[1] + $tend[0];
$totalTime = sprintf('%2.4f s', $tend - $tstart);

if (defined('PKG_AUTO_INSTALL') && PKG_AUTO_INSTALL) {
    $sig = explode('-', $signature);
    $versionSignature = explode('.', $sig[1]);

    $transportClass = $isModx3 ? 'MODX\Revolution\Transport\modTransportPackage' : 'transport.modTransportPackage';
    $package = $modx->getObject($transportClass, ['signature' => $signature]);
    if (!$package) {
        $package = $modx->newObject($transportClass);
        $package->set('signature', $signature);
        $package->fromArray([
            'created' => date('Y-m-d H:i:s'),
            'updated' => null,
            'state' => 1,
            'workspace' => 1,
            'provider' => 0,
            'source' => $signature . '.transport.zip',
            'package_name' => PKG_NAME,
            'version_major' => $versionSignature[0],
            'version_minor' => !empty($versionSignature[1]) ? $versionSignature[1] : 0,
            'version_patch' => !empty($versionSignature[2]) ? $versionSignature[2] : 0,
        ]);
        if (!empty($sig[2])) {
            $r = preg_split('/([0-9]+)/', $sig[2], -1, PREG_SPLIT_DELIM_CAPTURE);
            if (is_array($r) && !empty($r)) {
                $package->set('release', $r[0]);
                $package->set('release_index', (isset($r[1]) ? $r[1] : '0'));
            } else {
                $package->set('release', $sig[2]);
            }
        }
        $package->save();
    }

    if ($package->install()) {
        $modx->runProcessor($isModx3 ? 'System/ClearCache' : 'system/clearcache');
        $modx->log(modX::LOG_LEVEL_INFO, '✅ [Ace] Package ' . $signature . ' installed successfully.');
    }
}
if (!empty($_GET['download'])) {
    echo '<script>document.location.href = "/core/packages/' . $signature . '.transport.zip' . '";</script>';
}

$modx->log(modX::LOG_LEVEL_INFO, "\n[Ace] Execution time: {$totalTime}s\n");
if (!defined('XPDO_CLI_MODE') || !XPDO_CLI_MODE) {
    echo '</pre>';
}

if (php_sapi_name() === 'cli' && isset($buildErrorReporting)) {
    error_reporting($buildErrorReporting);
}