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
 */

if ($modx->event->name == 'OnRichTextEditorRegister') {
    $modx->event->output('Ace');
    return;
}

if ($modx->getOption('which_element_editor',null,'Ace') !== 'Ace') {
    return;
}

$ace = $modx->getService('ace','Ace',$modx->getOption('ace.core_path',null,$modx->getOption('core_path').'components/ace/').'model/ace/');

$ace->initialize();

if ($modx->event->name == 'OnManagerPageBeforeRender') {
    $modx->controller->addHtml('<style>'."
        .x-form-textarea{
        border-radius:2px;
        position: relative;
        background-color: #fbfbfb;
        background-image: none;
        border: 1px solid;
        border-color: #CCCCCC;
        }
        .x-form-focus {
        border-color: #658F1A;
        background-color: #FFFFFF;
        }
    ".'</style>');
}

switch ($modx->event->name) {
    case 'OnSnipFormPrerender':
        $field = 'modx-snippet-snippet';
        $mimeType = 'application/x-php';
        break;
    case 'OnTempFormPrerender':
        $field = 'modx-template-content';
        $mimeType = 'text/html';
        break;
    case 'OnChunkFormPrerender':
        $field = 'modx-chunk-snippet';
        $mimeType = 'text/html';
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
        switch (pathinfo($scriptProperties['file'], PATHINFO_EXTENSION))
        {
            case 'tpl':
            case 'html':
                $mimeType = 'text/html';
                break;
            case 'css':
                $mimeType = 'text/css';
                break;
            case 'scss':
                $mimeType = 'text/x-scss';
                break;
            case 'less':
                $mimeType = 'text/x-less';
                break;
            case 'svg':
                $mimeType = 'image/svg+xml';
                break;
            case 'xml':
                $mimeType = 'application/xml';
                break;
            case 'js':
                $mimeType = 'application/javascript';
                break;
            case 'json':
                $mimeType = 'application/json';
                break;
            case 'php':
                $mimeType = 'application/x-php';
                break;
            case 'sql':
                $mimeType = 'text/x-sql';
                break;
            case 'txt':
            default:
                $mimeType = 'text/plain';
        }
        break;
    case 'OnDocFormPrerender':
        if ($modx->getOption('use_editor')){
            if (isset($scriptProperties['resource'])) {
                    $richText = $scriptProperties['resource']->get('richtext');
                    $classKey = $scriptProperties['resource']->get('class_key');
            } else {
                    $richText = $modx->getOption('richtext_default');
                    $classKey = $modx->getOption('class_key', $_REQUEST, 'modDocument');
            }
            if ($richText || $classKey !== 'modDocument') {
                return;
            }
        }
        if (isset($scriptProperties['resource'])) {
            $contentType = $modx->getObject('modContentType', $scriptProperties['resource']->get('content_type'));
        } else {
            $contentType = $modx->getObject('modContentType', $modx->getOption('default_content_type'));
        }
        $field = 'ta';
        $mimeType = $contentType ? $contentType->get('mime_type') : 'text/plain';
        break;
    default:
        return;
}

$modx->controller->addHtml('<script>'."
    Ext.onReady(function() {
        setTimeout(function(){
            var textArea = Ext.getCmp('$field');
            var textEditor = MODx.load({
                xtype: 'modx-texteditor',
                anchor: textArea.anchor,
                width: 'auto',
                height: textArea.height,
                name: textArea.name,
                value: textArea.getValue(),
                mimeType: '$mimeType'
            });

            textArea.el.dom.removeAttribute('name');
            textArea.el.setStyle('display', 'none');
            textEditor.render(textArea.el.dom.parentNode);
            textEditor.on('keydown', function(t, e){textArea.fireEvent('keydown', t, e);});
            textEditor.on('onCtrlEnter', function(t){textArea.fireEvent('onCtrlEnter', t);});
            MODx.load({
                xtype: 'modx-treedrop',
                target: textEditor,
                targetEl: textEditor.el,
                onInsert: (function(s){
                    this.insertAtCursor(s);
                    this.focus();
                    return true;
                }).bind(textEditor),
                iframe: true
            });
        });
    });
".'</script>');

return;