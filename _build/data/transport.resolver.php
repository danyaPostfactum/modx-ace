<?php
/**
 * Ace Source Editor for MODx Revolution
 *
 * @author Danil Kostin <danya@postfactum@gmail.com>
 *
 * @package ace
 */

/**
 * Resolver: set default editor, add missing event, cleanup on upgrade.
 * Outputs a readable install log.
 *
 * @package ace
 * @subpackage build
 */

$success = true;
$log = function ($message) use ($object) {
    $object->xpdo->log(xPDO::LOG_LEVEL_INFO, $message);
};

if ($pluginid = $object->get('id')) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $log('[Ace] Running installation resolver...');

            $setting = $object->xpdo->getObject('modSystemSetting', ['key' => 'which_element_editor']);
            if ($setting) {
                $setting->set('value', 'Ace');
                $setting->save();
                $log('✓ Set default element editor to Ace (which_element_editor)');
            }
            unset($setting);

            $event = $object->xpdo->getObject('modEvent', ['name' => 'OnFileEditFormPrerender']);
            if (!$event) {
                $newEvent = $object->xpdo->newObject('modEvent');
                $newEvent->fromArray([
                    'name' => 'OnFileEditFormPrerender',
                    'service' => 1,
                    'groupname' => 'System',
                ], '', true, true);
                $newEvent->save();
                $log('✓ Added missing system event: OnFileEditFormPrerender');
            }
            $log('[Ace] Default editor and events configured.');
            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $success = true;
            break;
    }

    if ($options[xPDOTransport::PACKAGE_ACTION] === xPDOTransport::ACTION_UPGRADE) {
        $plugin = $object->xpdo->getObject('modPlugin', ['name' => 'Ace']);
        if ($plugin) {
            $log('[Ace] Cleaning up obsolete data...');
            $plugin->setProperties([]);
            $plugin->save();
            $log('✓ Cleared obsolete plugin properties');

            $rrmdir = function ($dir) use (&$rrmdir) {
                if (!is_dir($dir)) {
                    return;
                }
                $items = scandir($dir);
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') {
                        continue;
                    }
                    $path = $dir . '/' . $item;
                    if (is_dir($path)) {
                        $rrmdir($path);
                    } else {
                        @unlink($path);
                    }
                }
                @rmdir($dir);
            };

            $oldAssets = [
                MODX_MANAGER_PATH . 'assets/components/ace/',
                MODX_MANAGER_PATH . 'components/ace/',
            ];
            foreach ($oldAssets as $path) {
                if (is_dir($path)) {
                    @$rrmdir($path);
                    $log('✓ Removed old assets directory: ' . $path);
                    break;
                }
            }
            $log('[Ace] Cleanup completed.');
        }
    }
}

$log('✅ [Ace] Resolver completed.');
return $success;
