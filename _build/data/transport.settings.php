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


return $settings;