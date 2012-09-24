<?php
/**
 * The base class for Ace.
 *
 * @package ace
 */
class Ace {
    /**
     * @var modX A reference to the modX object.
     */
    public $modx = null;
    /**
     * @var array An array of configuration options
     */
    public $config = array();
    /**
     * @var bool A flag to prevent double script registering
     */
    public $assetsLoaded = false;

    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('ace.core_path',$config,$this->modx->getOption('core_path').'components/ace/');
        $managerUrl = $this->modx->getOption('ace.manager_url',$config,$this->modx->getOption('manager_url').'components/ace/');

        $this->config = array_merge(array(
            'corePath' => $corePath,
            'managerUrl' => $managerUrl,
        ),$config);

        $this->modx->addPackage('ace', $this->config['corePath'].'model/');
        $this->modx->lexicon->load('ace:default');
    }

    public function initialize() {
        if (!$this->assetsLoaded) {

            $lang = $this->modx->toJSON($this->modx->lexicon->fetch('ui_ace'));
            $lang = $lang ? $lang : '{}';

            $this->modx->controller->addHtml('<script>Ext.apply(MODx.lang, '.$lang.');</script>');
            $this->modx->controller->addJavascript($this->config['managerUrl'].'assets/ace/ace.js');
            $this->modx->controller->addJavascript($this->config['managerUrl'].'assets/modx.codearea.js');
        }
        $this->assetsLoaded = true;
    }
}