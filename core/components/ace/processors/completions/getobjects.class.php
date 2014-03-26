<?php
/**
 * @package modx
 * @subpackage processors.element
 */
class modCompletionsGetObjects extends modProcessor {
    public function checkPermissions() {
        return true;
    }
    public function getLanguageTopics() {
        return array();
    }

    public function initialize() {
        return true;
    }

    public function process() {
        $objects = $this->modx->getCollection($this->getProperty('classKey'));
        $list = array();
        if (count($objects)) {
            if ($this->getProperty('classKey') == 'modSystemSetting') {
                $this->modx->lexicon->load('setting');
                foreach ($objects as $object) {
                    $array = $this->prepareSetting($object);
                    $list[$object->get('key')] = $array['name_trans'] . '. ' . $array['description_trans'];
                }
            } else {
                foreach ($objects as $object) {
                    $list[$object->get('name')] = $object->get('description');
                }
            }
        }
        return $this->toJSON($list);
    }

    function prepareSetting(xPDOObject $object) {
        $settingArray = $object->toArray();
        $k = 'setting_'.$settingArray['key'];

        /* if 3rd party setting, load proper text, fallback to english */
        $this->modx->lexicon->load('en:'.$object->get('namespace').':default');
        $this->modx->lexicon->load($object->get('namespace').':default');

        /* get translated area text */
        if ($this->modx->lexicon->exists('area_'.$object->get('area'))) {
            $settingArray['area_text'] = $this->modx->lexicon('area_'.$object->get('area'));
        } else {
            $settingArray['area_text'] = $settingArray['area'];
        }

        /* get translated name and description text */
        if (empty($settingArray['description_trans'])) {
            if ($this->modx->lexicon->exists($k.'_desc')) {
                $settingArray['description_trans'] = $this->modx->lexicon($k.'_desc');
                $settingArray['description'] = $k.'_desc';
            } else {
                $settingArray['description_trans'] = !empty($settingArray['description']) ? $settingArray['description'] : '';
            }
        } else {
            $settingArray['description'] = $settingArray['description_trans'];
        }
        if (empty($settingArray['name_trans'])) {
            if ($this->modx->lexicon->exists($k)) {
                $settingArray['name_trans'] = $this->modx->lexicon($k);
                $settingArray['name'] = $k;
            } else {
                $settingArray['name_trans'] = $settingArray['key'];
            }
        } else {
            $settingArray['name'] = $settingArray['name_trans'];
        }
        return $settingArray;
    }

}
return 'modCompletionsGetObjects';