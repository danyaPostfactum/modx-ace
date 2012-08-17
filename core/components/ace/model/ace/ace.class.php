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

    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('ace.core_path',$config,$this->modx->getOption('core_path').'components/ace/');
        $assetsUrl = $this->modx->getOption('ace.assets_url',$config,$this->modx->getOption('assets_url').'components/ace/');

        $this->config = array_merge(array(
            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl.'js/',
        ),$config);

        $this->modx->addPackage('ace',$this->config['modelPath']);
        $this->modx->lexicon->load('ace:default');
    }
}