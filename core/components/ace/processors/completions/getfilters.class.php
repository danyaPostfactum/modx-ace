<?php
/**
 * @package modx
 * @subpackage processors.element
 */
class modCompletionsGetFilters extends modProcessor {
    public $path;
    
    public function checkPermissions() {
        return true;
    }
    public function getLanguageTopics() {
        return array('propertyset');
    }

    public function initialize() {
        $corePath = $this->modx->getOption('ace.core_path', null, $this->modx->getOption('core_path').'components/ace/');
        $this->path = $corePath . 'data/filters.json';
        return true;
    }

    public function process() {
        $data = file_get_contents($this->path);
        return $data;
    }

}
return 'modCompletionsGetFilters';