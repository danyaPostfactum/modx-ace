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
        $assetsUrl = $this->modx->getOption('ace.assets_url',$config,$this->modx->getOption('assets_url').'components/ace/');

        $this->config = array_merge(array(
            'corePath' => $corePath,
            'assetsUrl' => $assetsUrl,
        ),$config);

        $this->modx->addPackage('ace', $this->config['corePath'].'model/');
        $this->modx->lexicon->load('ace:default');
    }

    public function initialize() {
        if (!$this->assetsLoaded) {
            $this->modx->controller->addLexiconTopic('ace:default');
            $this->modx->controller->addJavascript($this->config['assetsUrl'].'ace/ace.min.js');
            $this->modx->controller->addJavascript($this->config['assetsUrl'].'ace/ext-language_tools.js');
            $this->modx->controller->addJavascript($this->config['assetsUrl'].'ace/ext-keybinding_menu.js');
            $this->modx->controller->addJavascript($this->config['assetsUrl'].'ace/ext-emmet.js');
            $this->modx->controller->addJavascript($this->config['assetsUrl'].'modx.texteditor.js');
        }
        $this->assetsLoaded = true;
    }
}