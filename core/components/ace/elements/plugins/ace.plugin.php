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

if ($modx->event->name == 'OnSiteSettingsRender') {
    $modx->controller->addHtml('<script>'."
        Ext.onReady(function() {
            MODx.combo.AceTheme = function(config) {
                config = config || {};
                Ext.applyIf(config,{
                    store: ['ambiance','chaos','chrome','clouds','clouds_midnight','cobalt','crimson_editor','dawn','dreamweaver','eclipse','github','idle_fingers','kr','merbivore','merbivore_soft','mono_industrial','monokai','pastel_on_dark','solarized_dark','solarized_light','textmate','tomorrow','tomorrow_night','tomorrow_night_blue','tomorrow_night_bright','tomorrow_night_eighties','twilight','vibrant_ink','xcode']
                    ,name: 'value'
                    ,hiddenName: 'value'
                });
                MODx.combo.AceTheme.superclass.constructor.call(this,config);
            };
            Ext.extend(MODx.combo.AceTheme,MODx.combo.ComboBox);
            Ext.reg('combo-ace-theme',MODx.combo.AceTheme);
        });
    ".'</script>');
    return;
}

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
    return;
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
            $richText = $modx->controller->resourceArray['richtext'];
            $classKey = $modx->controller->resourceArray['class_key'];

            if ($richText || in_array($classKey, array('modStaticResource','modSymLink','modWebLink','modXMLRPCResource'))) {
                return;
            }
        }

        $field = 'ta';
        $mimeType = $modx->getObject('modContentType', $modx->controller->resourceArray['content_type'])->get('mime_type');
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
                enableKeyEvents: true,
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
            textEditor.on('keydown', function(e){textArea.fireEvent('keydown', e);});
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
