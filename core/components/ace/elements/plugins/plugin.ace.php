<?php
/**
 * Ace Source Editor Plugin
 *
 * Events: OnRichTextEditorRegister, OnRichTextEditorInit, OnSnipFormPrerender,
 * OnTempFormPrerender, OnChunkFormPrerender, OnPluginFormPrerender,
 * OnFileCreateFormPrerender, OnFileEditFormPrerender, OnDocFormRender
 *
 * @author Danil Kostin <danya@postfactum@gmail.com>
 *
 * @package ace
 */

if ($modx->event->name == 'OnRichTextEditorRegister') {
	$modx->event->output('Ace');
	return;
}

$useEditor = $modx->getOption('use_editor',null,false);

if (!$useEditor) {
	return;
}

$whichEditor = $modx->getOption('which_editor',null,'');
$whichElementEditor = $modx->getOption('which_element_editor',null,'');

switch ($modx->event->name)
{
	case 'OnSnipFormPrerender':
	case 'OnTempFormPrerender':
	case 'OnChunkFormPrerender':
	case 'OnPluginFormPrerender':
	case 'OnFileCreateFormPrerender':
	case 'OnFileEditFormPrerender':
		if ($whichElementEditor != 'Ace') {
			return;
		}
		break;
	case 'OnDocFormRender':
		if (($whichElementEditor != 'Ace') || ($scriptProperties['resource']->get('richtext') == 1) || ($scriptProperties['resource']->get('class_key') != 'modDocument')){
			return;
		}
		break;
	case 'OnRichTextEditorInit':
		if (($whichEditor != 'Ace') || (!$scriptProperties['resource'])) {
			return;
		}
		break;
	default:
		return;
		break;
}

$ace = $modx->getService('ace','Ace',$modx->getOption('ace.core_path',null,$modx->getOption('core_path').'components/ace/').'model/ace/');

if (!($ace instanceof Ace)) {
	return;
}

$load = true;

switch ($modx->event->name) {
    case 'OnSnipFormPrerender':
		$field = 'modx-snippet-snippet';
		$panel = 'modx-panel-snippet';
		$mode = 'php';
		break;
	case 'OnTempFormPrerender':
		$field = 'modx-template-content';
		$panel = 'modx-panel-template';
	    $mode = 'html';
	    break;
	case 'OnChunkFormPrerender':
		$field = 'modx-chunk-snippet';
		$panel = 'modx-panel-chunk';
	    $mode = 'html';
	    break;
	case 'OnPluginFormPrerender':
		$field = 'modx-plugin-plugincode';
		$panel = 'modx-panel-plugin';
	    $mode = 'php';
	    break;
	case 'OnFileCreateFormPrerender':
		$field = 'modx-file-content';
		$panel = 'modx-panel-file-create';
	    $mode = 'text';
	    break;
	case 'OnFileEditFormPrerender':
		$field = 'modx-file-content';
		$panel = 'modx-panel-file-edit';
		switch (pathinfo($scriptProperties['file'], PATHINFO_EXTENSION))
		{
			case 'tpl':
			case 'html':
				$mode = 'html';
				break;
			case 'css':
				$mode = 'css';
				break;
			case 'svg':
    			$mode = 'svg';
				break;
			case 'xml':
				$mode = 'xml';
				break;
			case 'js':
				$mode = 'javascript';
				break;
    		case 'json':
				$mode = 'json';
				break;
			case 'php':
				$mode = 'php';
				break;
			case 'sql':
				$mode = 'sql';
				break;
			case 'txt':
			default:
				$mode = 'text';
		}
		break;
	case 'OnDocFormRender':
	case 'OnRichTextEditorInit':
		$field = 'ta';
		$panel = 'modx-panel-resource';
		switch ($scriptProperties['resource']->get('contentType')){
		    case 'text/html':
				$mode = 'html';
				break;
		    case 'text/css':
				$mode = 'css';
				break;
    	    case 'image/svg+xml':
				$mode = 'svg';
				break;
		    case 'text/xml':
		    case 'application/xml':
		    case 'application/xhtml+xml':
		    case 'application/rss+xml':
				$mode = 'xml';
				break;
		    case 'text/javascript':
		    case 'application/javascript':
				$mode = 'javascript';
				break;
		    case 'application/json':
				$mode = 'json';
				break;
			case 'text/plain':
			default:
				$mode = 'text';
		}
		break;
	default:
		$load = false;
}

if (!$load) {
    return;
}

$lang = $modx->lexicon->fetch('ui_ace');

$modx->regClientStartupScript($ace->config['assetsUrl'].'ace.js');
$modx->regClientStartupScript($ace->config['assetsUrl'].'theme/'.$modx->getOption('ace.theme', null, 'textmate').'.js');
$modx->regClientStartupScript($ace->config['assetsUrl'].'mode/'.$mode.'.js');
$modx->regClientStartupScript($ace->config['assetsUrl'].'modx.codearea.js');

$modx->regClientStartupScript('<script type="text/javascript">'."
    // <![CDATA[
    Ext.onReady(function() {
        var TextArea = Ext.getCmp('{$field}');
		var CodeArea = MODx.load({
			xtype: 'modx-codearea',
			anchor: TextArea.anchor,
			width: 'auto',
			height: TextArea.height,
			name: TextArea.name,
			value: TextArea.getValue(),
			mode: '{$mode}'
		});
		
        TextArea.el.dom.name = '';
        TextArea.el.setStyle('display', 'none');
        CodeArea.render(TextArea.el.dom.parentNode);
		CodeArea.on('change', function(e){TextArea.fireEvent('keydown', e);});
	});
	Ext.apply(MODx.lang, {$modx->toJSON($lang)});
    // ]]>
".'</script>', true);

$modx->regClientCSS('<style type="text/css">'."
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