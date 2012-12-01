<?php
$events = array();

$events[1] = $modx->newObject('modPluginEvent');
$events[1]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnChunkFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events[2] = $modx->newObject('modPluginEvent');
$events[2]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnPluginFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events[3] = $modx->newObject('modPluginEvent');
$events[3]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnSnipFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events[4] = $modx->newObject('modPluginEvent');
$events[4]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnTempFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events[5] = $modx->newObject('modPluginEvent');
$events[5]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnFileEditFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events[6] = $modx->newObject('modPluginEvent');
$events[6]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnFileCreateFormPrerender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events[7] = $modx->newObject('modPluginEvent');
$events[7]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnDocFormRender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events[8] = $modx->newObject('modPluginEvent');
$events[8]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnRichTextEditorRegister',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events[9] = $modx->newObject('modPluginEvent');
$events[9]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnManagerPageBeforeRender',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

$events[10] = $modx->newObject('modPluginEvent');
$events[10]->fromArray(array(
    'pluginid' => 1,
    'event' => 'OnTVInputRenderList',
    'priority' => 0,
    'propertyset' => 0
),'',true,true);

return $events;