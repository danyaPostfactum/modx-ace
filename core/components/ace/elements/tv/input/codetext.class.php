<?php
if(!class_exists('CodeTextInputRender')) {
    class CodeTextInputRender extends modTemplateVarInputRender {
        public function getTemplate() {
            return $this->modx->getOption('ace.core_path',null,$this->modx->getOption('core_path').'components/ace/').'elements/tv/input/tpl/codetext.tpl';
        }
        public function process($value,array $params = array()) {
 
        }
    }
}
return 'CodeTextInputRender';