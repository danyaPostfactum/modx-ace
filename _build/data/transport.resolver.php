<?php
/**
 * Ace Source Editor for MODx Revolution
 *
 * @author Danil Kostin <danya@postfactum@gmail.com>
 *
 * @package ace
 */

/**
 * Resolver to set which_editor to Ace
 * 
 * @package ace
 * @subpackage build
 */
$success= true;
if ($pluginid= $object->get('id')) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Attempting to set which_element_editor setting to Ace.');
            // set Ace as default element editor
            $setting = $object->xpdo->getObject('modSystemSetting',array('key' => 'which_element_editor'));
            if ($setting) {
                $setting->set('value','Ace');
                $setting->save();
            }
            unset($setting);
            // add missing event in MODx < 2.2.3
			$event = $object->xpdo->getObject('modEvent', array('name' => 'OnFileEditFormPrerender'));
			if (!$event) {
				$object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Attempting to add missing OnFileEditFormPrerender event to MODx.');
				$event = $object->xpdo->newObject('modEvent');
				$event->fromArray(array (
				  'name' => 'OnFileEditFormPrerender',
				  'service' => 1,
				  'groupname' => 'System',
				), '', true, true);
				$event->save();
			}
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $success= true;
            break;
    }
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
            // remove obsolete plugin properties and files
			$plugin = $object->xpdo->getObject('modPlugin', array('name' => 'Ace'));	
            if ($plugin) {
				$object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Attempting to clear obsolete plugin properties.');
                $plugin->setProperties(array());
                $plugin->save();
                
                // Code from http://www.php.net/manual/en/function.rmdir.php#98622
                function rrmdir($dir) {
                   if (is_dir($dir)) {
                     $objects = scandir($dir);
                     foreach ($objects as $object) {
                       if ($object != "." && $object != "..") {
                         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
                       }
                     }
                     reset($objects);
                     rmdir($dir);
                   }
                 }

                $oldAssets = array(MODX_MANAGER_PATH. 'assets/components/ace/', MODX_MANAGER_PATH. 'components/ace/');
                foreach ($oldAssets as $path) {
                    if (is_dir($path)) {
                        $object->xpdo->log(xPDO::LOG_LEVEL_INFO, "Attempting to remove old assets directory ($path).");
                        @rrmdir($path);
                        break;
                    }
                }
            }
	}
}

return $success;