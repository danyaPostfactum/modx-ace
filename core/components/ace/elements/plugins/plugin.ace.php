<?php
/**
 * Ace Source Editor Plugin
 *
 * Events: OnManagerPageBeforeRender
 *
 * @author Danil Kostin <danya@postfactum@gmail.com>
 *
 * @package ace
 */

if ($modx->event->name == 'OnRichTextEditorRegister') {
	$modx->event->output('Ace');
	return;
}

if ($modx->getOption('which_element_editor',null,'') != 'Ace') {
	return;
}

$ace = $modx->getService('ace','Ace',$modx->getOption('ace.core_path',null,$modx->getOption('core_path').'components/ace/').'model/ace/');

$ace->initialize();