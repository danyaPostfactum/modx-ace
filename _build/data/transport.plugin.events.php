<?php
$events = array();

$events['OnChunkFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnChunkFormPrerender']->fromArray(array(
    'event' => 'OnChunkFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events['OnPluginFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnPluginFormPrerender']->fromArray(array(
    'event' => 'OnPluginFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events['OnSnipFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnSnipFormPrerender']->fromArray(array(
    'event' => 'OnSnipFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events['OnTempFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnTempFormPrerender']->fromArray(array(
    'event' => 'OnTempFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events['OnFileEditFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnFileEditFormPrerender']->fromArray(array(
    'event' => 'OnFileEditFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events['OnFileCreateFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnFileCreateFormPrerender']->fromArray(array(
    'event' => 'OnFileCreateFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events['OnDocFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnDocFormPrerender']->fromArray(array(
    'event' => 'OnDocFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events['OnDocFormRender'] = $modx->newObject('modPluginEvent');
$events['OnDocFormRender']->fromArray(array(
    'event' => 'OnDocFormRender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events['OnRichTextEditorRegister'] = $modx->newObject('modPluginEvent');
$events['OnRichTextEditorRegister']->fromArray(array(
    'event' => 'OnRichTextEditorRegister',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events['OnManagerPageBeforeRender'] = $modx->newObject('modPluginEvent');
$events['OnManagerPageBeforeRender']->fromArray(array(
    'event' => 'OnManagerPageBeforeRender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

return $events;