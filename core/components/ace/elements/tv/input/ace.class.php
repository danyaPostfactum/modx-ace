<?php
/**
 * The tv render ace.
 *
 * @package ace
 */

class AceInputRender extends modTemplateVarInputRender
{
    /**
     * Return the template path to load
     *
     * @return string
     */
    public function getTemplate()
    {
    	$corePath = $this->modx->getOption('ace.core_path', null,  $this->modx->getOption('core_path').'components/ace/');        
        return $corePath . 'elements/tv/input/tpl/ace.render.tpl';
    }
}

return 'AceInputRender';
