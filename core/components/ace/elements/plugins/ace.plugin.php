<?php
/**
 * Ace Source Editor Plugin
 *
 * Events: OnManagerPageBeforeRender, OnRichTextEditorRegister, OnSnipFormPrerender,
 * OnTempFormPrerender, OnChunkFormPrerender, OnPluginFormPrerender,
 * OnFileCreateFormPrerender, OnFileEditFormPrerender, OnDocFormPrerender
 *
 * @author Danil Kostin <danya.postfactum(at)gmail.com>
 *
 * @package ace
 *
 * @var array $scriptProperties
 * @var Ace $ace
 */
if ($modx->event->name == 'OnRichTextEditorRegister') {
    $modx->event->output('Ace');
    return;
}

if ($modx->getOption('which_element_editor', null, 'Ace') !== 'Ace') {
    return;
}

$corePath = $modx->getOption('ace.core_path', null, $modx->getOption('core_path').'components/ace/');
$ace = $modx->getService('ace', 'Ace', $corePath.'model/ace/');
$ace->initialize();

$extensionMap = array(
    'tpl'   => 'text/x-smarty',
    'htm'   => 'text/html',
    'html'  => 'text/html',
    'css'   => 'text/css',
    'scss'  => 'text/x-scss',
    'less'  => 'text/x-less',
    'svg'   => 'image/svg+xml',
    'xml'   => 'application/xml',
    'xsl'   => 'application/xml',
    'js'    => 'application/javascript',
    'json'  => 'application/json',
    'php'   => 'application/x-php',
    'sql'   => 'text/x-sql',
    'md'    => 'text/x-markdown',
    'txt'   => 'text/plain',
    'twig'  => 'text/x-twig'
);

// Define default mime for html elements(templates, chunks and html resources)
$html_elements_mime=$modx->getOption('ace.html_elements_mime',null,false);
if(!$html_elements_mime){
    // this may deprecated in future because components may set ace.html_elements_mime option now
    switch (true) {
        case $modx->getOption('twiggy_class'):
            $html_elements_mime = 'text/x-twig';
            break;
        case $modx->getOption('pdotools_fenom_parser'):
            $html_elements_mime = 'text/x-smarty';
            break;
        default:
            $html_elements_mime = 'text/html';
    }
}

// Defines wether we should highlight modx tags
$modxTags = false;
switch ($modx->event->name) {
    case 'OnSnipFormPrerender':
        $field = 'modx-snippet-snippet';
        $mimeType = 'application/x-php';
        break;
    case 'OnTempFormPrerender':
        $field = 'modx-template-content';
        $modxTags = true;
        $mimeType = $html_elements_mime;
        break;
    case 'OnChunkFormPrerender':
        $field = 'modx-chunk-snippet';
        if ($modx->controller->chunk && $modx->controller->chunk->isStatic()) {
            $extension = pathinfo($modx->controller->chunk->name, PATHINFO_EXTENSION);
            if(!$extension||!isset($extensionMap[$extension])){
                $extension = pathinfo($modx->controller->chunk->getSourceFile(), PATHINFO_EXTENSION);
            }
            $mimeType = isset($extensionMap[$extension]) ? $extensionMap[$extension] : 'text/plain';
        } else {
            $mimeType = $html_elements_mime;
        }
        $modxTags = true;
        break;
    case 'OnPluginFormPrerender':
        $field = 'modx-plugin-plugincode';
        $mimeType = 'application/x-php';
        break;
    case 'OnFileCreateFormPrerender':
        $field = 'modx-file-content';
        $mimeType = 'text/plain';
        break;
    case 'OnFileEditFormPrerender':
        $field = 'modx-file-content';
        $extension = pathinfo($scriptProperties['file'], PATHINFO_EXTENSION);
        $mimeType = isset($extensionMap[$extension])
            ? $extensionMap[$extension]
            : ('@FILE:'.pathinfo($scriptProperties['file'], PATHINFO_BASENAME));
        $modxTags = $extension == 'tpl';
        break;
    case 'OnDocFormPrerender':
        if (!$modx->controller->resourceArray) {
            return;
        }
        $field = 'ta';
        $mimeType = $modx->getObject('modContentType', $modx->controller->resourceArray['content_type'])->get('mime_type');

        if($mimeType == 'text/html')$mimeType = $html_elements_mime;

        if ($modx->getOption('use_editor')){
            $richText = $modx->controller->resourceArray['richtext'];
            $classKey = $modx->controller->resourceArray['class_key'];
            if ($richText || in_array($classKey, array('modStaticResource','modSymLink','modWebLink','modXMLRPCResource'))) {
                $field = false;
            }
        }
        $modxTags = true;
        break;
    case 'OnTVInputRenderList':
        $modx->event->output($corePath . 'elements/tv/input/');
        break;
    default:
        return;
}

$modxTags = (int) $modxTags;
$script = '';
if ($field) {
    $script .= "MODx.ux.Ace.replaceComponent('$field', '$mimeType', $modxTags);";
}

if ($modx->event->name == 'OnDocFormPrerender' && !$modx->getOption('use_editor')) {
    $script .= "MODx.ux.Ace.replaceTextAreas(Ext.query('.modx-richtext'));";
}

if ($script) {
    $modx->controller->addHtml('<script>Ext.onReady(function() {' . $script . '});</script>');
}
