Ext.namespace('MODx.ux');

MODx.ux.Ace.isColorPreviewEnabled = function() {
    var utils = MODx.ux.Ace.Utils || MODx.ux.AceUtils;
    return utils.parseBoolSetting(MODx.config['ace.color_preview'], false);
};

MODx.ux.Ace.supportsColorPreview = function(mimeType) {
    var utils = MODx.ux.Ace.Utils || MODx.ux.AceUtils;
    return utils.mimeSupportsColorPreview(mimeType);
};

/**
 * Per-editor color preview instances (not a shared singleton).
 * Avoids marker/state collisions when multiple Ace fields are on one page.
 */
MODx.ux.Ace.ColorPreview = {
    _instances: {},
    _seq: 0,
    MAX_SCAN_LINES: 2000,

    init: function(editor, mimeType) {
        if (!MODx.ux.Ace.isColorPreviewEnabled() || !MODx.ux.Ace.supportsColorPreview(mimeType)) {
            return null;
        }
        if (!editor) {
            return null;
        }

        var id = editor.id || ('ace-color-' + (++this._seq));
        if (!editor.id) {
            editor.id = id;
        }
        this.destroy(editor);

        var instance = {
            editor: editor,
            markerIds: [],
            markerSeq: 0,
            styleEl: null
        };

        instance.styleEl = document.createElement('style');
        instance.styleEl.id = 'ace-modx-color-preview-' + id;
        document.head.appendChild(instance.styleEl);

        var refresh = this.debounce(function() {
            MODx.ux.Ace.ColorPreview.refresh(instance);
        }, 150);

        instance._onChange = refresh;
        editor.getSession().on('change', refresh);
        editor.on('changeMode', refresh);
        editor.on('changeSession', refresh);

        this._instances[id] = instance;
        this.refresh(instance);
        return instance;
    },

    destroy: function(editor) {
        if (!editor) {
            return;
        }
        var id = editor.id;
        var instance = id ? this._instances[id] : null;
        if (!instance) {
            return;
        }
        var session = instance.editor.getSession();
        instance.markerIds.forEach(function(markerId) {
            session.removeMarker(markerId);
        });
        if (instance.styleEl && instance.styleEl.parentNode) {
            instance.styleEl.parentNode.removeChild(instance.styleEl);
        }
        if (instance._onChange) {
            try {
                session.removeListener('change', instance._onChange);
            } catch (e) {}
            try {
                instance.editor.removeListener('changeMode', instance._onChange);
                instance.editor.removeListener('changeSession', instance._onChange);
            } catch (e2) {}
        }
        delete this._instances[id];
    },

    refresh: function(instance) {
        var editor = instance.editor;
        if (!editor) {
            return;
        }

        var session = editor.getSession();
        var Range = ace.require('ace/range').Range;
        var utils = MODx.ux.Ace.Utils || MODx.ux.AceUtils;
        var regex = new RegExp(utils.COLOR_LITERAL_REGEX.source, 'g');

        instance.markerIds.forEach(function(markerId) {
            session.removeMarker(markerId);
        });
        instance.markerIds = [];

        var lines = session.doc.getAllLines();
        var end = Math.min(lines.length, this.MAX_SCAN_LINES);
        var rules = [];

        for (var row = 0; row < end; row++) {
            var line = lines[row];
            var match;
            regex.lastIndex = 0;
            while ((match = regex.exec(line)) !== null) {
                var color = utils.normalizeCssColor(match[0]);
                if (!color) {
                    continue;
                }
                var className = 'ace-modx-color-marker-' + (instance.markerSeq++);
                rules.push('.' + className + '{background-color:' + color + ' !important;opacity:0.35;border-radius:2px;}');
                instance.markerIds.push(session.addMarker(
                    new Range(row, match.index, row, match.index + match[0].length),
                    className,
                    'text',
                    false
                ));
            }
        }

        if (instance.styleEl) {
            instance.styleEl.textContent = rules.join('\n');
        }
    },

    normalizeColor: function(value) {
        var utils = MODx.ux.Ace.Utils || MODx.ux.AceUtils;
        return utils.normalizeCssColor(value);
    },

    debounce: function(fn, ms) {
        var timer;
        return function() {
            var args = arguments;
            var ctx = this;
            clearTimeout(timer);
            timer = setTimeout(function() {
                fn.apply(ctx, args);
            }, ms);
        };
    }
};
