/**
 * Pure Ace helpers shared by manager JS and Node unit tests.
 * Browser: MODx.ux.AceUtils (survives Ext.extend) and MODx.ux.Ace.Utils when Ace exists
 * Node: module.exports
 */
(function (root, factory) {
    var utils = factory();
    if (typeof module === 'object' && module.exports) {
        module.exports = utils;
    }
    if (typeof Ext !== 'undefined') {
        Ext.namespace('MODx.ux');
        MODx.ux.AceUtils = utils;
        if (MODx.ux.Ace) {
            MODx.ux.Ace.Utils = utils;
        }
    } else if (root) {
        root.AceUtils = utils;
    }
}(typeof globalThis !== 'undefined' ? globalThis : this, function () {
    'use strict';

    var COLOR_LITERAL_REGEX = /#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})\b|rgba?\(\s*(?:25[0-5]|2[0-4]\d|1?\d?\d)\s*,\s*(?:25[0-5]|2[0-4]\d|1?\d?\d)\s*,\s*(?:25[0-5]|2[0-4]\d|1?\d?\d)(?:\s*,\s*(?:0|1|0?\.\d+))?\s*\)|hsla?\(\s*-?\d+(?:\.\d+)?\s*,\s*\d+(?:\.\d+)?%\s*,\s*\d+(?:\.\d+)?%(?:\s*,\s*(?:0|1|0?\.\d+))?\s*\)/g;

    function parseBoolSetting(value, defaultValue) {
        if (value === undefined || value === null || value === '') {
            return defaultValue === undefined ? false : !!defaultValue;
        }
        return value === true || value === '1' || value === 1;
    }

    function draftStorageKey(action, name, id) {
        var a = action ? String(action) : 'manager';
        var n = name ? String(name) : 'field';
        var i = (id === undefined || id === null || id === '') ? 'new' : String(id);
        return 'ace:draft:' + encodeURIComponent(a) + ':' + encodeURIComponent(n) + ':' + encodeURIComponent(i);
    }

    function shouldOfferDraftRestore(draftValue, initialValue) {
        return typeof draftValue === 'string'
            && draftValue !== ''
            && draftValue !== initialValue;
    }

    function normalizeCssColor(value) {
        if (!value || typeof value !== 'string') {
            return null;
        }
        if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/.test(value)) {
            return value;
        }
        if (/^rgba?\(/i.test(value) || /^hsla?\(/i.test(value)) {
            COLOR_LITERAL_REGEX.lastIndex = 0;
            var match = COLOR_LITERAL_REGEX.exec(value);
            if (match && match[0] === value) {
                return value;
            }
            return null;
        }
        return null;
    }

    function mimeSupportsColorPreview(mimeType) {
        if (!mimeType) {
            return false;
        }
        return /css|scss|less|html|smarty|twig|svg/i.test(mimeType);
    }

    function shouldWaitForAsyncBuffer(fieldId) {
        return fieldId === 'modx-rdata-buffer';
    }

    var PHP_EDGE_TRIM = /[\u0000\u0009\u000A\u000B\u000D\u0020]/;
    var PHP_TAIL_TRIM = /[\u0000\u000A\u000B\u000D\u0020]/;
    var PHP_OPEN = '<' + '?';
    var PHP_CLOSE = '?' + '>';

    function trimChars(value, pattern) {
        var start = 0;
        var end = value.length;
        while (start < end && pattern.test(value.charAt(start))) {
            start++;
        }
        while (end > start && pattern.test(value.charAt(end - 1))) {
            end--;
        }
        return value.slice(start, end);
    }

    /**
     * modScript::set() stores snippet/plugin code without a PHP wrapper:
     * trim, drop a leading open tag, drop a trailing close tag, trim again.
     * The second trim does not remove tabs.
     */
    function stripModxScriptWrapper(value) {
        if (typeof value !== 'string') {
            return '';
        }
        var v = trimChars(value, PHP_EDGE_TRIM);
        if (v.slice(0, PHP_OPEN.length) === PHP_OPEN) {
            v = v.slice(PHP_OPEN.length);
            if (v.slice(0, 3) === 'php') {
                v = v.slice(3);
            }
        }
        if (v.slice(-PHP_CLOSE.length) === PHP_CLOSE) {
            v = v.slice(0, -PHP_CLOSE.length);
        }
        return trimChars(v, PHP_TAIL_TRIM);
    }

    function isPhpSourceContext(mimeType) {
        return mimeType === 'application/x-php' || mimeType === 'text/x-php';
    }

    /**
     * True when the saved value differs only by the PHP wrapper MODX removes.
     * Limited to PHP mime types: snippets and plugins. HTML chunks can be
     * switched to PHP mode and still store the wrapper, so mode alone is not
     * enough.
     */
    function shouldKeepEditorAfterModxPhpStrip(current, incoming, mimeType) {
        if (typeof current !== 'string' || typeof incoming !== 'string') {
            return false;
        }
        if (!isPhpSourceContext(mimeType)) {
            return false;
        }
        return stripModxScriptWrapper(current) === incoming;
    }

    /**
     * Fullscreen insets so the editor sits beside the manager nav and below
     * the Save/Close bar. A narrow tall header is the side nav, even if it
     * is a few pixels off the viewport edge. A wide short header is a top bar.
     * Buttons count only when they sit on that top edge.
     */
    function fullscreenInsets(header, buttons) {
        var top = 0;
        var left = 0;
        if (header && header.width > 0 && header.height > 0) {
            var sideNav = header.height > header.width && header.right > header.left && header.right <= 240;
            if (sideNav) {
                left = header.right;
            } else if (header.top <= 1) {
                top = header.bottom;
            }
        }
        if (buttons && buttons.width > 0 && buttons.height > 0 && buttons.bottom > top && buttons.top <= top + 1) {
            top = buttons.bottom;
        }
        return {
            top: Math.ceil(top),
            left: Math.ceil(left)
        };
    }

    return {
        COLOR_LITERAL_REGEX: COLOR_LITERAL_REGEX,
        parseBoolSetting: parseBoolSetting,
        draftStorageKey: draftStorageKey,
        shouldOfferDraftRestore: shouldOfferDraftRestore,
        normalizeCssColor: normalizeCssColor,
        mimeSupportsColorPreview: mimeSupportsColorPreview,
        shouldWaitForAsyncBuffer: shouldWaitForAsyncBuffer,
        stripModxScriptWrapper: stripModxScriptWrapper,
        shouldKeepEditorAfterModxPhpStrip: shouldKeepEditorAfterModxPhpStrip,
        fullscreenInsets: fullscreenInsets
    };
}));
