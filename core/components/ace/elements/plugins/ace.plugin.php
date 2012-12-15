<?php
/**
 * Ace Source Editor Plugin
 *
 * Events: OnManagerPageBeforeRender, OnRichTextEditorRegister, OnSnipFormPrerender,
 * OnTempFormPrerender, OnChunkFormPrerender, OnPluginFormPrerender,
 * OnFileCreateFormPrerender, OnFileEditFormPrerender, OnDocFormRender
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
    case 'OnDocFormRender':
        if ($modx->getOption('use_editor',null,true)) {
            switch ($scriptProperties['mode'])
            {
                case modSystemEvent::MODE_NEW:
                    if ($modx->getOption('richtext_default', null)) return;
                    break;
                case modSystemEvent::MODE_UPD:
                    if ($scriptProperties['resource']->get('richtext')) return;
                    break;
            }
        }
        if ($scriptProperties['resource']->get('class_key') !== 'modDocument') return;
        $field = 'ta';
        $mimeType = $scriptProperties['resource']->get('contentType');
        break;
    default:
        return;
}

$modx->regClientStartupScript('<script>'."
    Ext.onReady(function() {
        var TextArea = Ext.getCmp('$field');
        var TextEditor = MODx.load({
            xtype: 'modx-texteditor',
            anchor: TextArea.anchor,
            width: 'auto',
            height: TextArea.height,
            name: TextArea.name,
            value: TextArea.getValue(),
            mimeType: '$mimeType'
        });

        TextArea.el.dom.name = '';
        TextArea.el.setStyle('display', 'none');
        TextEditor.render(TextArea.el.dom.parentNode);
        TextEditor.on('keydown', function(e){TextArea.fireEvent('keydown', e);});
        MODx.load({
            xtype: 'modx-treedrop',
            target: TextEditor,
            targetEl: TextEditor.el,
            onInsert: (function(s){
                this.insertAtCursor(s);
                this.focus();
                return true;
            }).bind(TextEditor),
            iframe: true
        });
    });
".'</script>', true);

return;