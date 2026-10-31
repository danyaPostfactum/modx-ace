<?php

/**
 * Pure helpers for Ace plugin logic (unit-tested).
 *
 * @package ace
 */

if (!function_exists('aceGetEffectiveUseEditor')) {
    /**
     * @param object $modx
     * @return bool
     */
    function aceGetEffectiveUseEditor($modx) {
        if ($modx->controller && !empty($modx->controller->context)) {
            return (bool) $modx->controller->context->getOption('use_editor', $modx->getOption('use_editor'));
        }
        return (bool) $modx->getOption('use_editor');
    }
}

if (!function_exists('aceGetEffectiveWhichEditor')) {
    /**
     * @param object $modx
     * @return string
     */
    function aceGetEffectiveWhichEditor($modx) {
        if ($modx->controller && !empty($modx->controller->context)) {
            return trim((string) $modx->controller->context->getOption('which_editor', $modx->getOption('which_editor', '')));
        }
        return trim((string) $modx->getOption('which_editor', ''));
    }
}

if (!function_exists('aceIsNonAceRichTextEditor')) {
    /**
     * @param object $modx
     * @return bool
     */
    function aceIsNonAceRichTextEditor($modx) {
        $whichEditor = aceGetEffectiveWhichEditor($modx);
        return $whichEditor !== '' && strcasecmp($whichEditor, 'Ace') !== 0;
    }
}

if (!function_exists('aceMimeSupportsModxTags')) {
    /**
     * @param string $mimeType
     * @return bool
     */
    function aceMimeSupportsModxTags($mimeType) {
        if ($mimeType === '' || strpos($mimeType, '@FILE:') === 0) {
            return false;
        }
        static $supported = array(
            'text/html' => true,
            'text/x-smarty' => true,
            'text/x-twig' => true,
        );
        return !empty($supported[$mimeType]);
    }
}

if (!function_exists('aceIsResourceDataPage')) {
    /**
     * @param object $modx
     * @return bool
     */
    function aceIsResourceDataPage($modx) {
        if (!$modx->controller) {
            return false;
        }
        if (!empty($modx->controller->config['controller'])) {
            $controller = strtolower(str_replace('\\', '/', $modx->controller->config['controller']));
            if ($controller === 'resource/data') {
                return true;
            }
        }
        $action = $modx->getOption('action', $_REQUEST, '');
        if ($action === '' && !empty($_REQUEST['a'])) {
            $action = (string) $_REQUEST['a'];
        }
        return strtolower(str_replace('\\', '/', $action)) === 'resource/data';
    }
}
