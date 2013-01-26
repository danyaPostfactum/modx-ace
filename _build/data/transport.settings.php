<?php
$settings = array();

$settings['theme']= $modx->newObject('modSystemSetting');
$settings['theme']->fromArray(array(
        'key' => 'ace.theme',
        'xtype' => 'combo-ace-theme',
        'value' => 'chrome',
        'namespace' => 'ace',
        'area' => 'general'
    ),'',true,true);

$settings['font_size']= $modx->newObject('modSystemSetting');
$settings['font_size']->fromArray(array(
        'key' => 'ace.font_size',
        'xtype' => 'textfield',
        'value' => '13px',
        'namespace' => 'ace',
        'area' => 'general'
    ),'',true,true);

$settings['word_wrap']= $modx->newObject('modSystemSetting');
$settings['word_wrap']->fromArray(array(
        'key' => 'ace.word_wrap',
        'xtype' => 'combo-boolean',
        'value' => '',
        'namespace' => 'ace',
		'area' => 'general'
    ),'',true,true);

$settings['soft_tabs']= $modx->newObject('modSystemSetting');
$settings['soft_tabs']->fromArray(array(
        'key' => 'ace.soft_tabs',
        'xtype' => 'combo-boolean',
        'value' => '1',
        'namespace' => 'ace',
        'area' => 'general'
    ),'',true,true);

$settings['tab_size']= $modx->newObject('modSystemSetting');
$settings['tab_size']->fromArray(array(
        'key' => 'ace.tab_size',
        'xtype' => 'textfield',
        'value' => '4',
        'namespace' => 'ace',
        'area' => 'general'
    ),'',true,true);

$settings['fold_widgets']= $modx->newObject('modSystemSetting');
$settings['fold_widgets']->fromArray(array(
        'key' => 'ace.fold_widgets',
        'xtype' => 'combo-boolean',
        'value' => '1',
        'namespace' => 'ace',
        'area' => 'general'
    ),'',true,true);

$settings['show_invisibles']= $modx->newObject('modSystemSetting');
$settings['show_invisibles']->fromArray(array(
        'key' => 'ace.show_invisibles',
        'xtype' => 'combo-boolean',
        'value' => '0',
        'namespace' => 'ace',
        'area' => 'general'
    ),'',true,true);


return $settings;