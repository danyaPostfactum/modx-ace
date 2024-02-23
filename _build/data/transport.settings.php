<?php
$settings = array();

$settings['theme']= $modx->newObject('modSystemSetting');
$settings['theme']->fromArray(array(
        'key' => 'ace.theme',
        'xtype' => 'textfield',
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

$settings['snippets']= $modx->newObject('modSystemSetting');
$settings['snippets']->fromArray(array(
        'key' => 'ace.snippets',
        'xtype' => 'textarea',
        'value' => '',
        'namespace' => 'ace',
        'area' => 'general'
    ),'',true,true);

$settings['height']= $modx->newObject('modSystemSetting');
$settings['height']->fromArray(array(
        'key' => 'ace.height',
        'xtype' => 'textfield',
        'value' => '',
        'namespace' => 'ace',
        'area' => 'general'
    ),'',true,true);

$settings['grow']= $modx->newObject('modSystemSetting');
$settings['grow']->fromArray(array(
        'key' => 'ace.grow',
        'xtype' => 'textfield',
        'value' => '',
        'namespace' => 'ace',
        'area' => 'general'
    ),'',true,true);

$settings['html_elements_mime']= $modx->newObject('modSystemSetting');
$settings['html_elements_mime']->fromArray(array(
        'key' => 'ace.html_elements_mime',
        'xtype' => 'textfield',
        'value' => '',
        'namespace' => 'ace',
        'area' => 'general'
    ),'',true,true);

return $settings;