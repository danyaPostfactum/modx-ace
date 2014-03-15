Ext.ux.Ace = Ext.extend(Ext.form.TextField,  {

    growMin : 60,

    growMax: 1000,

    mode : 'text',

    theme : 'textmate',

    showInvisibles : false,

    selectionStyle : 'line',

    scrollSpeed : 3,

    showFoldWidgets : true,

    useSoftTabs : true,

    tabSize : 4,

    useWrapMode : false,

    fontSize : '13px',

    value : '',

    style: 'position:relative;padding:0;line-height:1.3',

    initEvents : function(){
        Ext.ux.Ace.superclass.initEvents.call(this);
        this.editor.on('focus', this.onFocus.bind(this));
        this.editor.on('blur', this.onBlur.bind(this));
    },

    initComponent : function(){
        this.valueHolder = document.createElement('input');
        this.valueHolder.type = 'hidden';
        this.valueHolder.name = this.name;
        this.valueHolder.value = this.value;
    },

    onRender : function(ct, position){
        if(!this.el){
            this.defaultAutoCreate = {
                tag: "div",
                cls: "x-form-textarea",
                style:"width:100px;height:60px;position:relative"
            };
        }
        Ext.ux.Ace.superclass.onRender.call(this, ct, position);

        var useragent = ace.require('ace/lib/useragent');

        if(this.grow){
            this.el.setHeight(this.growMin);
        }

        this.editor = ace.edit(this.el.dom);

        this.el.appendChild(this.valueHolder);
        this.el.dom.removeAttribute('name');

        this.el.focus = this.focus.bind(this);

        this.editor.getSession().setValue(this.valueHolder.value);

        this.editor.setShowPrintMargin(false);
        this.editor.getSession().setTabSize(this.tabSize);

        this.editor.setAutoScrollEditorIntoView(true);

        if (!useragent.isMac)
            this.editor.setDragDelay(0);

        this.editor.setFontSize(this.fontSize);
        this.editor.setFadeFoldWidgets(false);

        this.setShowInvisibles(this.showInvisibles);
        this.setSelectionStyle(this.selectionStyle);
        this.setScrollSpeed(this.scrollSpeed);
        this.setShowFoldWidgets(this.showFoldWidgets);
        this.setUseSoftTabs(this.useSoftTabs);
        this.setUseWrapMode(this.useWrapMode);

        ace.require("ace/ext/language_tools");
        this.editor.setOptions({
            enableBasicAutocompletion: true
        });

        this.setTheme(this.theme);
        this.setMode(this.mode);

        this.editor.getSession().on('change', (function(){
            setTimeout(function(){
                this.valueHolder.value = this.editor.getSession().getValue();
            }.bind(this), 10);
        }).bind(this));
        // TODO: attach autoSize to according event (?)
        this.autoSize();
    },

    onDestroy : function(){
        this.editor.destroy();
        Ext.ux.Ace.superclass.onDestroy.call(this);
    },

    validate : function(){
        return true;
    },

    getErrors : function(value){
        return null;
    },

    onResize : function(){
        this.editor.resize(true);
    },

    doAutoSize : function(e){
        return !e.isNavKeyPress() || e.getKey() == e.ENTER;
    },

    autoSize: function(){
        if(!this.grow){
            return;
        }
        var linesCount = this.editor.getSession().getDocument().getLength();
        var lineHeight = this.editor.renderer.lineHeight;
        var scrollBar =  this.editor.renderer.scrollBar.getWidth();
        var bordersWidth = this.el.getBorderWidth('tb');

        var h = Math.min(this.growMax, Math.max(linesCount * lineHeight + scrollBar + bordersWidth, this.growMin));
        if(h != this.lastHeight){
            this.lastHeight = h;
            this.el.setHeight(h);
            this.editor.resize();
            this.fireEvent("autosize", this, h);
        }
    },

    setSize : function(width, height){
        Ext.ux.Ace.superclass.setSize.apply(this, arguments);
        this.editor.resize(true);
    },

    getValue : function (){
        return this.valueHolder.value;
    },

    setValue : function (value){
        if (this.editor) {
            this.editor.getSession().setValue(value);
        } else {
            this.valueHolder.value = value;
        }
        this.value = value;
    },

    setMode : function (mode){
        this.editor.getSession().setMode( 'ace/mode/' + mode );
    },

    setTheme : function(theme){
        this.editor.setTheme('ace/theme/' + theme);
    },

    setFontSize : function(fontSize){
        this.editor.setFontSize(fontSize);
    },

    setShowInvisibles : function(showInvisibles){
        this.editor.setShowInvisibles(showInvisibles);
    },

    setSelectionStyle : function(selectionStyle){
        this.editor.setSelectionStyle(selectionStyle);
    },

    setScrollSpeed : function(scrollSpeed){
        this.editor.setScrollSpeed(scrollSpeed);
    },

    setShowFoldWidgets : function(showFoldWidgets){
        this.editor.setShowFoldWidgets(showFoldWidgets);
    },

    setUseSoftTabs : function(useSoftTabs){
        this.editor.getSession().setUseSoftTabs(useSoftTabs);
    },

    setUseWrapMode : function(useWrapMode){
        this.editor.getSession().setUseWrapMode(useWrapMode);
    },

    insertAtCursor : function (value){
        return this.editor.insert(value);
    },

    focus: function (){
        this.editor.focus();
    },

    blur: function (){
        this.editor.blur();
    }
});

Ext.reg('ace', Ext.ux.Ace);

Ext.namespace('MODx.ux');

MODx.ux.Ace = Ext.extend(Ext.ux.Ace, {

    mimeType : 'text/plain',

    theme : MODx.config['ace.theme'] || 'textmate',

    fontSize : MODx.config['ace.font_size'] || '13px',

    useWrapMode : MODx.config['ace.word_wrap'] == true,

    useSoftTabs : MODx.config['ace.soft_tabs'] == true,

    tabSize : MODx.config['ace.tab_size'] * 1 || 4,

    showFoldWidgets : MODx.config['ace.fold_widgets'] == true,

    showInvisibles : MODx.config['ace.show_invisibles'] == true,

    modxTags : false,

    initComponent : function() {
        MODx.ux.Ace.superclass.initComponent.call(this);
        var Config = ace.require("ace/config");
        var acePath = MODx.config['manager_url'] + 'assets/components/ace/ace';
        Config.set('modePath', acePath);
        Config.set('themePath', acePath);
        Config.set('workerPath', acePath);
    },

    onRender : function (ct, position) {
        var TokenIterator = ace.require("ace/token_iterator").TokenIterator;

        MODx.ux.Ace.superclass.onRender.call(this, ct, position);

        var expander = document.createElement('div');
        expander.style.width = expander.style.height = '16px';
        expander.style.top = expander.style.right = '3px';
        expander.style.position = 'absolute';
        expander.style.zIndex = 10;
        expander.style.backgroundImage = 'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAABgFBMVEVmrBxkqxpjqhlkqxprryFnrB1kqxtkqxp6uzJiqRhZow9kqxpmrBxlrBtnrBxjqhlmrBxjqhptsSNkqhplqxt4tyxkqhlmrB1mrBx5uzJmrBxorR9orR5nrB1kqhlorR14uS5mrRxhqBhmrR1aoxBlqxt3titusiRlqxpiqRd4ty1rrh9hqBdjqxp3tSpaow9mrR15ujBorh5lrBxapRFmrBxkqhpjqhp5uzBhqRdnrB1rsCJlrBt6vDNorh93tipmqxxhqRhapBFbpRJnrR1mrB14uC1usiVnrB1apRFhqReq1VmLxUGv5Gyt3GOs22Cm0FKn2FyUzUyRykev4WmUz1CUy0mw5G6RzEmr11xusSOSy0iOxkKPx0Or2V6CwTuo3GGUz0+Sy0mu5W6Ry0mq1lptsCORy0iNx0OAvDOo2V5/vDOCwDmPxkOOy0mj0lWl1lqu4Gap1Viv4mms2l+s3WSPx0SKwDtusiWCwTqQxUGSyUaRykiAvDSp2l+Qx0QdWpRIAAAAS3RSTlMAAAAa4gAAlfAZMRLhuQCV1ZXwGgDwlQCy8MzY2swS2PDqEdUisvDwABn64hEA8CLh+tq5MeoAAPAZ4eKy+tr6uREiMdXq8PDqIhHgP7bQAAAA4ElEQVR42mOw5uBw52cAA351CwUWBttk30p9iIBKTmGcFgNHeEKwlBIfAwOrjmxNQaQoQ0V8TVa9PDMDg7B0RF1MdhSDGJdLfWqVqS6Ta5BfvYAzO1Arp0xVOS8bo3FGtAwnxDBuHhsRBgYNVR5uBihgZAOTTDA+AxMjiDSDCYhzW0kAtTBKGMiJgwUs7cJ8eBkZHfISjTSBXDEu5RT/akVzJo/Q4mgBE3aGisr0OqjDkupyM9MYJEMCY6UcQU73ki3LL1JjMCwtqfWEmO5U6x1gz8Ci4CYkCBEQFBLV0wYAXu4m8P20SwoAAAAASUVORK5CYII=)';
        expander.style.backgroundSize = '100%';
        expander.onmousedown = function(event) {
            event.preventDefault();
        };
        expander.onclick = function(event) {
            this.fullScreen();
            event.preventDefault();
        }.bind(this);
        this.editor.renderer.scroller.appendChild(expander);

        this.setMimeType(this.mimeType);

        if (!MODx.ux.Ace.initialized) {
            new MODx.ux.Ace.CodeCompleter();
            var snippetManager = ace.require("ace/snippets").snippetManager;
            var snippets = MODx.config['ace.snippets'] || '';
            snippetManager.register(snippetManager.parseSnippetFile(snippets), "_");

            var HashHandler = ace.require("ace/keyboard/hash_handler").HashHandler;
            var commands = new HashHandler();
            commands.addCommand({
                name: "insertsnippet",
                bindKey: {win: "Tab", mac: "Tab"},
                exec: function(editor) {
                    return snippetManager.expandWithTab(editor);
                }
            });
            // to overwrite emmet
            var onChangeMode = function(e, target) {
                var editor = target;
                editor.keyBinding.addKeyboardHandler(commands);
            };
            onChangeMode({}, this.editor);

            var Emmet = ace.require("ace/ext/emmet");
            var net = ace.require('ace/lib/net');
            net.loadScript(MODx.config['manager_url'] + 'assets/components/ace/emmet/emmet.js', function() {
                Emmet.setCore(window.emmet);
                this.editor.setOption("enableEmmet", true);
                this.editor.on("changeMode", onChangeMode);
                onChangeMode({}, this.editor);
            }.bind(this));

            ace.require('ace/ext/keybinding_menu').init(this.editor);

            MODx.ux.Ace.initialized = true;
        }

        this.editor.commands.addCommand({
            name: "showKeyboardShortcuts",
            bindKey: {win: "Ctrl-Alt-H", mac: "Command-Alt-H"},
            exec: function(editor) {
                editor.showKeyboardShortcuts();
            }
        });

        this.editor.commands.addCommand({
            name: "find",
            bindKey: {win: "Ctrl-F", mac: "Command-F"},
            exec: this.showFindReplaceWindow.bind(this, 0),
            readOnly: true
        });

        this.editor.commands.addCommand({
            name: "replace",
            bindKey: {win: "Ctrl-R|Ctrl-Shift-R", mac: "Command-Option-F|Command-Shift-Option-F"},
            exec: this.showFindReplaceWindow.bind(this, 1),
            readOnly: true
        });

        this.editor.commands.addCommand({
            name: "gotoline",
            bindKey: {win: "Ctrl-L", mac: "Command-Option-L"},
            exec: this.showGotoLineWindow.bind(this),
            readOnly: true
        });

        this.editor.commands.addCommand({
            name: "fullscreen",
            bindKey: {win: "Ctrl-F11", mac: "Command-F12"},
            exec: this.fullScreen.bind(this),
            readOnly: true
        });

        this.windows = [];
    },

    fullScreen : function() {
        if (this.isFullscreen){
            this.isFullscreen = false;
            this.el.setStyle({
                position: 'relative',
                zIndex: 0,
                height: this.savedState.style.height,
                width: this.savedState.style.width,
                borderWidth: this.savedState.style.borderWidth
            });
            this.grow = this.savedState.grow;
        } else {
            this.isFullscreen = true;
            this.savedState = {
                style: {
                    width: this.el.dom.style.width,
                    height: this.el.dom.style.height,
                    borderWidth: this.el.dom.style.borderWidth,
                },
                grow: this.grow
            };
            this.el.setStyle({
                position: 'fixed',
                zIndex: 100,
                top: 0,
                left: 0,
                bottom: 0,
                right: 0,
                height: 'auto',
                width: 'auto',
                borderWidth: 0
            });
            this.grow = false;
        }
        this.onResize();
    },

    setMimeType : function (mimeType){
        var typeMap = {
             'text/x-php'            : 'php'
            ,'application/x-php'     : 'php'
            ,'text/x-sql'            : 'sql'
            ,'text/x-scss'           : 'scss'
            ,'text/x-less'           : 'less'
            ,'text/xml'              : 'xml'
            ,'application/xml'       : 'xml'
            ,'image/svg+xml'         : 'svg'
            ,'text/html'             : 'html'
            ,'application/xhtml+xml' : 'html'
            ,'text/javascript'       : 'javascript'
            ,'application/javascript': 'javascript'
            ,'application/json'      : 'json'
            ,'text/css'              : 'css'
            ,'text/plain'            : 'text'
        };

        this.setMode( typeMap[mimeType] || 'text' );
    },

    showFindReplaceWindow: function (tab) {
        var window, field, tabs;
        if (!this.windows.findRelpace) {
            this.windows.findRelpace = this.createFindReplaceWindow();
        }
        window = this.windows.findRelpace;
        field = window.fp.getForm().findField('needle');
        tabs = window.findByType('modx-tabs')[0];

        window.show();

        if (this.editor.getCopyText()) {
            field.setValue(this.editor.getCopyText());
        }
        field.focus(false,50);

        tabs.setActiveTab(tab);
    },

    showGotoLineWindow : function(){
        var window;
        if (!this.windows.gotoLine){
            this.windows.gotoLine = this.createGotoLineWindow();
        }
        window = this.windows.gotoLine;
        window.show();
    },

    doFind : function(){
        var window, options, needle;

        window = this.windows.findRelpace;

        needle = window.getFieldValue('needle');
        options = window.getOptions();
        
        this.editor.find(needle, options);
    },

    doReplaceAll : function(){
        var window, options, needle, replacement, result;

        window = this.windows.findRelpace;

        needle = window.getFieldValue('needle');
        replacement = window.getFieldValue('replacement');
        options = window.getOptions();

        options.needle = needle;

        result = this.editor.replaceAll(replacement, options);
        Ext.Msg.alert(_('ui_ace.replace_all'), _('ui_ace.message_replaced', {count: result}));
    },
    
    doReplace : function(){
        var window, options, needle, replacement;

        window = this.windows.findRelpace;

        needle = window.getFieldValue('needle');
        replacement = window.getFieldValue('replacement');
        options = window.getOptions();

        options.needle = needle;

        this.editor.replace(replacement, options);
    },
    
    doGotoLine : function(){
        var window, line;

        window = this.windows.gotoLine;

        line = window.fp.getForm().getFieldValues('line')['line'];
        if (!isNaN(line)){
            this.editor.gotoLine(line);
            window.hide();
        }
    },

    createFindReplaceWindow : function (){
        var window = MODx.load({
            xtype: 'modx-window',
            title: _('ui_ace.find')
            ,resizable: false
            ,maximizable: false
            ,allowDrop: false
            ,width: 300
            ,buttons: [{
                text: _('ui_ace.find')
                ,hidden: true
                ,name: 'find-button'
                ,scope: this
                ,handler: this.doFind
            },{
                text: _('ui_ace.replace_all')
                ,hidden: true
                ,name: 'replaceall-button'
                ,scope: this
                ,handler: this.doReplaceAll
            },{
                text: _('ui_ace.replace')
                ,hidden: true
                ,name: 'replace-button'
                ,scope: this
                ,handler: this.doReplace
            },{
                text: _('ui_ace.close')
                ,scope: this
                ,handler: function() { window.hide(); }
            }]
            ,keys: [{
                key: Ext.EventObject.ENTER
                ,fn: function(){
                    var tabs = window.findByType('modx-tabs')[0];

                    if (tabs.getActiveTab().name == 'find-tab') {
                        window.fbar.items.filter('name', 'find-button').first().handler.call(this);
                    }
                    if (tabs.getActiveTab().name == 'replace-tab') {
                        window.fbar.items.filter('name', 'replace-button').first().handler.call(this);
                    }
                }
                ,scope: this
            }]
            ,action: 'find/replace'
            ,listeners: {
                'hide': {fn: this.focus, scope: this}
            }
            ,fields: [{
                xtype: 'modx-tabs'
                ,forceLayout: true
                ,deferredRender: false
                ,collapsible: false
                ,border: false
                ,items: [{
                    title: _('ui_ace.find')
                    ,cls: 'modx-find-tab'
                    ,layout: 'form'
                    ,name: 'find-tab'
                    ,forceLayout: true
                    ,deferredRender: false
                    ,labelWidth: 200
                    ,border: true
                },{
                    title: _('ui_ace.replace')
                    ,cls: 'modx-replace-tab'
                    ,layout: 'form'
                    ,name: 'replace-tab'
                    ,forceLayout: true
                    ,deferredRender: false
                    ,labelWidth: 200
                    ,border: true
                }]
            },{
                xtype: 'textfield'
                ,fieldLabel:  _('ui_ace.find')
                ,name: 'needle'
                ,selectOnFocus: true
                ,anchor: '100%'
                ,value:  ''
            },{
                xtype: 'textfield'
                ,fieldLabel: _('ui_ace.replace_with')
                ,name: 'replacement'
                ,selectOnFocus: true
                ,anchor: '100%'
                ,value:  ''
            },{
                xtype: 'xcheckbox'
                ,boxLabel: _('ui_ace.whole_word')
                ,name: 'wholeword'
                ,inputValue: 1
                    ,layout: 'form'
                    ,forceLayout: true
                    ,deferredRender: false
                ,checked: false
            },{
                xtype: 'xcheckbox'
                ,boxLabel: _('ui_ace.case_sensitive')
                ,name: 'casesensitive'
                ,inputValue: 1
                ,checked: false
            },{
                xtype: 'xcheckbox'
                ,boxLabel: _('ui_ace.search_wrap')
                ,name: 'wrap'
                ,inputValue: 1
                ,checked: true
            }]
        });

        window.getOptions = function () {
            var options;

            options = {};
            options.wholeWord = this.getFieldValue('wholeword');
            options.caseSensitive = this.getFieldValue('casesensitive');
            options.wrap = this.getFieldValue('wrap');

            return options;
        };

        window.getFieldValue = function (field) {
            var form, value;

            form = this.fp.getForm();
            value = form.getFieldValues(field)[field];

            return value;
        };

        var tabs = window.findByType('modx-tabs')[0];
        tabs.on({
            tabchange: (function(tabs, item){
                var form, buttons;

                form = window.fp.getForm();
                buttons = window.fbar.items;

                switch (item.name)
                {
                    case 'find-tab':
                        window.setTitle( _('ui_ace.find') );
                        form.findField('replacement').hide();
                        form.findField('needle').focus(false, 0);

                        buttons.filter('name', 'find-button').first().show();
                        buttons.filter('name', 'replaceall-button').first().hide();
                        buttons.filter('name', 'replace-button').first().hide();
                        break;
                    case 'replace-tab':
                        window.setTitle( _('ui_ace.replace') );
                        form.findField('replacement').show();
                        form.findField('needle').focus(false, 0);

                        buttons.filter('name', 'find-button').first().hide();
                        buttons.filter('name', 'replaceall-button').first().show();
                        buttons.filter('name', 'replace-button').first().show();
                        break;
                }
            }).bind(this)
        });

        return window;
    },

    createGotoLineWindow: function () {
        var window = MODx.load({
            xtype: 'modx-window',
            title: _('ui_ace.goto_line')
            ,resizable: false
            ,maximizable: false
            ,allowDrop: false
            ,width: 300
            ,buttons: [{
                text: _('ui_ace.go')
                ,scope: this
                ,handler: this.doGotoLine
            },{
                text: _('ui_ace.close')
                ,scope: this
                ,handler: function() { window.hide(); }
            }]
            ,keys: [{
                key: Ext.EventObject.ENTER
                ,fn: this.doGotoLine
                ,scope: this
            }]
            ,action: 'gotoline'
            ,listeners: {
                'hide': {fn: this.focus, scope: this}
            }
            ,fields: [{
                xtype: 'textfield'
                ,validator: function (value) {
                    return !isNaN(value);
                }
                ,fieldLabel: _('ui_ace.goto_line')
                ,name: 'line'
                ,anchor: '100%'
                ,value:  ''
            }]
        });

        return window;
    },

    setMode : function (mode){
        var editor = this.editor;
        if (!this.modxTags)
            return editor.session.setMode('ace/mode/' + mode);

        var config = ace.require('ace/config');
        config.loadModule(["mode", 'ace/mode/' + mode], function(m) {
            var Mode = m.Mode;

            function ModxMixedMode() {
                Mode.call(this);
                var HighlightRules = this.HighlightRules;


                function ModxMixedHighlightRules() {
                    HighlightRules.call(this);

                    this.$rules['modxtag-comment'] = [
                        {
                            token : "comment.modx",
                            regex : "[^\\[\\]]+",
                            merge : true
                        },{
                            token : "comment.modx",
                            regex : "\\[\\[\\-.*?\\]\\]"
                        },{
                            token : "comment.modx",
                            regex : "\\s+",
                            merge : true
                        },
                        {
                            token : "paren.rparen.comment.modx",
                            regex : "\\]\\]",
                            next: "pop"
                        }
                    ];
                    this.$rules['modxtag-start'] = [
                        {
                            token : ["cache-flag.variable.modx", "tag-token.variable.modx", "tag-name.variable.modx"],
                            regex : "(!)?([%|*|~|\\+|\\$]|(?:\\+\\+)|(?:\\*#))?([-_a-zA-Z0-9\\.]+)",
                            push : [
                                {include: "modxtag-filter"},
                                {
                                    token: "tag-delimiter.keyword.operator.modx",
                                    regex: "\\?",
                                    push: [
                                        {token : "text.modx", regex : "\\s+"},
                                        {include: 'modxtag-property-string'},
                                        {token: "", regex: "$"},
                                        {token: '', regex: '', next: 'pop'}
                                    ]
                                },
                                {token : "text.modx", regex : "\\s+"},
                                {token: "", regex: "$"},
                                {token: '', regex: '', next: 'pop'}
                            ]
                        },
                        {
                            token : "support.constant.paren.lparen.modx", // opening tag
                            regex : "\\[\\[",
                            push : 'modxtag-start'
                        },
                        {
                            token : "text",
                            regex : "\\s+"
                        },
                        {
                            token : "support.constant.paren.rparen.tag-brackets.modx",
                            regex : "\\]\\]",
                            next: "pop"
                        },
                        {defaultToken: 'text.modx'}
                    ];
                    this.$rules['modxtag-propertyset'] = [
                        {
                            token : ['keyword.operator.modx', "support.class.modx"],
                            regex : "(@)([-_a-zA-Z0-9\\.]+|\\[\\[.*?\\]\\])",
                            next : 'modxtag-filter'
                        },
                        {
                            token : "text",
                            regex : "\\s+"
                        },
                        {token: "", regex: "$"},
                        {
                            token: "empty",
                            regex: "",
                            next: "modxtag-filter"
                        }
                    ];
                    this.$rules['modxtag-filter'] = [
                        {
                            token : 'filter-delimiter.keyword.operator.modx',
                            regex : ":",
                            push : [
                                {
                                    token: "filter-name.support.function.modx",
                                    regex: "[-_a-zA-Z0-9]+|\\[\\[.*?\\]\\]",
                                    push: "modxtag-filter-eq"
                                },
                                {
                                    token: "empty",
                                    regex: "",
                                    next: "pop"
                                }
                            ]
                        },
                        {
                            token : "text",
                            regex : "\\s+"
                        }
                    ];
                    this.$rules['modxtag-filter-eq'] = [
                        {
                            token : ["keyword.operator.modx"],
                            regex : "="
                            },{
                            token : 'string',
                            regex : '`',
                            push: "modxtag-filter-value"
                        },
                        {
                            token : "text",
                            regex : "\\s+"
                        },
                        {
                            token: "empty",
                            regex: "",
                            next: "pop"
                        }
                    ];
                    this.$rules["modxtag-property-string"] = [
                        {
                            token : "entity.other.attribute-name.modx",
                            regex: "&"
                        },
                        {
                            token: "entity.other.attribute-name.modx",
                            regex: "[-_a-zA-Z0-9]+"
                        },
                        {
                            token : "string.modx",
                            regex : '`',
                            push : "modxtag-attribute-value"
                            }, {
                            token : "keyword.operator.modx",
                            regex : "="
                            }, {
                            token : "entity.other.attribute-name.modx",
                            regex : "[-_a-zA-Z0-9]+"
                        },
                        {
                            token : "comment.modx",
                            regex : "\\[\\[\\-.*?\\]\\]"
                        },
                        {
                            token : "property-string.text.modx",
                            regex : "\\s+"
                        }
                    ];
                    this.$rules["modxtag-attribute-value"] = [
                        {
                            token : "string.modx",
                            regex : "[^`\\[]+",
                            merge : true
                            },{
                            token : "string.modx",
                            regex : "[^`]+",
                            merge : true
                            },/* {
                            token : "string",
                            regex : "\\\\$",
                            next  : "modxtag-attribute-value",
                            merge : true
                            },*/ {
                            token : "string.modx",
                            regex : "`",
                            next  : "pop",
                            merge : true
                        }
                    ];
                    this.$rules["modxtag-filter-value"] = [
                        {
                            token : "string.modx",
                            regex : "[^`\\[]+",
                            merge : true
                            },{
                            token : "string.modx",
                            regex : "\\[\\[.*?\\]\\]",
                            merge : true
                            }, {
                            token : "string.modx",
                            regex : "\\\\$",
                            next  : "pop",
                            merge : true
                            }, {
                            token : "string.modx",
                            regex : "`",
                            next  : "pop",
                            merge : true
                        }
                    ];

                    // add twig start tags to the HTML start tags
                    for (var rule in this.$rules) {
                        this.$rules[rule].unshift({
                            token : "paren.lparen.comment.modx", // opening tag
                            regex : "\\[\\[\\-",
                            push : 'modxtag-comment',
                            merge: true
                        }, {
                            token : "support.constant.paren.lparen.tag-brackets.modx", // opening tag
                            regex : "\\[\\[",
                            push : 'modxtag-start',
                            merge : false
                        });
                    }

                    this.normalizeRules();
                }

                ModxMixedHighlightRules.prototype = HighlightRules.prototype;

                this.HighlightRules = ModxMixedHighlightRules;

                this.$behaviour = Object.create(this.$behaviour || new Behaviour());

                this.$behaviour.add("brackets", "insertion", function (state, action, editor, session, text) {
                    if (text == '[') {
                        var selection = editor.getSelectionRange();
                        var selected = session.doc.getTextRange(selection);
                        if (selected !== "") {
                            return {
                                text: '[' + selected + ']',
                                selection: false
                            };
                        } else {
                            return {
                                text: '[]',
                                selection: [1, 1]
                            };
                        }
                    } else if (text == ']') {
                        var cursor = editor.getCursorPosition();
                        var line = session.doc.getLine(cursor.row);
                        var rightChar = line.substring(cursor.column, cursor.column + 1);
                        if (rightChar == ']') {
                            var matching = session.$findOpeningBracket(']', {column: cursor.column + 1, row: cursor.row});
                            if (matching !== null) {
                                return {
                                    text: '',
                                    selection: [1, 1]
                                };
                            }
                        }
                    }
                });
                this.$behaviour.add("brackets", "deletion", function (state, action, editor, session, range) {
                    var selected = session.doc.getTextRange(range);
                    if (!range.isMultiLine() && selected == '[') {
                        var line = session.doc.getLine(range.start.row);
                        var rightChar = line.substring(range.start.column + 1, range.start.column + 2);
                        if (rightChar == ']') {
                            range.end.column++;
                            return range;
                        }
                    }
                });
                this.$behaviour.add("string_apostrophes", "insertion", function (state, action, editor, session, text) {
                    if (text == '`') {
                        var quote = "`";
                        var selection = editor.getSelectionRange();
                        var selected = session.doc.getTextRange(selection);
                        if (selected !== "") {
                            return {
                                text: quote + selected + quote,
                                selection: false
                            };
                        } else {
                            var cursor = editor.getCursorPosition();
                            var line = session.doc.getLine(cursor.row);
                            var leftChar = line.substring(cursor.column-1, cursor.column);

                            // Find what token we're inside.
                            var tokens = session.getTokens(selection.start.row);
                            var col = 0, token;
                            var quotepos = -1; // Track whether we're inside an open quote.

                            for (var x = 0; x < tokens.length; x++) {
                                token = tokens[x];
                                if (token.type == "string.modx") {
                                    quotepos = -1;
                                } else if (quotepos < 0) {
                                    quotepos = token.value.indexOf(quote);
                                }
                                if ((token.value.length + col) > selection.start.column) {
                                    break;
                                }
                                col += tokens[x].value.length;
                            }

                            // Try and be smart about when we auto insert.
                            if (!token || (quotepos < 0 && token.type !== "comment" && (token.type !== "string.modx" || ((selection.start.column !== token.value.length+col-1) && token.value.lastIndexOf(quote) === token.value.length-1)))) {
                                return {
                                    text: quote + quote,
                                    selection: [1,1]
                                };
                            } else if (token && token.type === "string.modx") {
                                // Ignore input and move right one if we're typing over the closing quote.
                                var rightChar = line.substring(cursor.column, cursor.column + 1);
                                if (rightChar == quote) {
                                    return {
                                        text: '',
                                        selection: [1, 1]
                                    };
                                }
                            }
                        }
                    }
                });
                this.$behaviour.add("string_apostrophes", "deletion", function (state, action, editor, session, range) {
                    var selected = session.doc.getTextRange(range);
                    if (!range.isMultiLine() && (selected == '`')) {
                        var line = session.doc.getLine(range.start.row);
                        var rightChar = line.substring(range.start.column + 1, range.start.column + 2);
                        if (rightChar == '`') {
                            range.end.column++;
                            return range;
                        }
                    }
                });
            }
            ModxMixedMode.prototype = Object.create(Mode.prototype, {
                constructor: {value: ModxMixedMode}
            });
            editor.session.setMode(new ModxMixedMode());
        }.bind(this));

    }
});

MODx.ux.Ace.initialized = false;

MODx.ux.Ace.CodeCompleter = function() {
    var TokenIterator = ace.require("ace/token_iterator").TokenIterator;
    var langTools = ace.require("ace/ext/language_tools");

    var cache = {};

    function loadCompletions(params, callback) {
        Ext.Ajax.request({
            url: MODx.config.connectors_url + 'components/ace/completions.php',
            params: params,
            success: function(response) {
                var completions = JSON.parse(response.responseText);
                callback(completions);
            }
        });
    }

    function gatherCompletions(completionParameters, callback) {
        var wait = 0;
        var completions = [];
        completionParameters.forEach(function(parameters){
            var data = cache[parameters.cacheKey];
            if (!data) {
                wait++;
                loadCompletions(parameters.requestParams, function(data) {
                    wait--;
                    cache[parameters.cacheKey] = data;
                    completions = completions.concat(parameters.prepare(data));
                    wait || callback(null, completions);
                });
                return;
            }
            completions = completions.concat(parameters.prepare(data));
        });
        wait || callback(null, completions);
    }

    function prepareCompletions(completions, meta) {
        return Object.keys(completions).map(function(completion){
            return {
                value: meta == 'chunk' ? '$' + completion : completion,
                caption: completion,
                meta: meta,
                description: completions[completion],
                score: 1000
            };
        });
    }

    function preparePropertyCompletions(completions) {
        return Object.keys(completions).map(function(completion){
            return {
                caption: completion,
                snippet: completion + '=`$0`',
                meta: 'property',
                description: completions[completion],
                score: 1000
            };
        });
    }

    function hasType(token, type) {
        var tokenTypes = token.type.split('.');
        return type.split('.').every(function(type){
            return (tokenTypes.indexOf(type) !== -1);
        });
    }

    function isWhitespace(string) {
        for (var i = 0; i < string.length; i++) {
            var c = string[i];
            if (!(c == ' ' || c == '\n' || c == '\r')) {
                return false;
            }
        }
        return true;
    }

    function parseTag(iterator) {
        var token = iterator.getCurrentToken();

        if (!token)
            return null;
        if (token.type.substring(token.type.lastIndexOf('.') + 1) !== 'modx')
            return null;
        while(token && hasType(token, 'text.modx') && isWhitespace(token.value))
        {
            token = iterator.stepBackward();
        }

        if (!token)
            return null;

        // we are in modx tag

        var completionType = 'object';
        var objectName = '';
        var classKey = 'modSnippet';

        if (hasType(token, 'tag-name')) {// [[*tag|]]
            objectName = token.value;
            token = iterator.stepBackward();
        }

        if (hasType(token, 'tag-brackets') && token.value == '[[') {// [[|]]
            classKey = 'modSnippet';
        } else if (hasType(token, 'cache-flag')) {
            //
        } else if (hasType(token, 'tag-token') || hasType(token, 'text')) {// [[*|]]
            switch (token.value) {
                case '$':
                    classKey = 'modChunk';
                    break;
                case '*':
                    classKey = 'modTemplateVar';
                    break;
                case '++':
                    classKey = 'modSystemSetting';
                    break;
                default:
                    return null;
            }
        } else if (hasType(token, 'filter-name') || hasType(token, 'filter-delimiter')) {// [[*tag:filter|]], [[*tag:|]]
            completionType = 'filter';
        } else if (hasType(token, 'attribute-name') || hasType(token, 'tag-delimiter')) {// [[*tag?|]] , [[*tag? &prop|]]
            objectName = (function() {
                do {
                    token = iterator.stepBackward();
                } while (token && !(hasType(token, 'tag-name') || hasType(token, 'modxtag-start')));

                if (token && hasType(token, 'tag-name'))
                    return token.value;
                return null;
            })();
            if (!objectName)
                return null;
            completionType = 'property';
        } else {
            return null;
        }
        return {
            completionType: completionType,
            classKey: classKey,
            objectName: objectName,
        };
    }

    langTools.addCompleter({
        getCompletions: function(editor, session, pos, prefix, callback) {
            var iterator = new TokenIterator(session, pos.row, pos.column);

            var parsedInfo = parseTag(iterator);
            if (!parsedInfo)
                return callback(null);

            var completionType = parsedInfo.completionType;
            var classKey = parsedInfo.classKey;
            var objectName = parsedInfo.objectName;

            switch (completionType) {
                case 'propertyset':
                    break;
                case 'lexiconentry':
                    break;
                case 'property':
                    gatherCompletions([
                        {
                            cacheKey: classKey + '.' + objectName,
                            requestParams: {action: 'getProperties', classKey: classKey, key: objectName},
                            prepare: function(completions) {
                                return preparePropertyCompletions(completions, 'property');
                            }
                        }
                    ], callback);
                    break;
                case 'filter':
                    gatherCompletions([
                        {
                            cacheKey: 'filter',
                            requestParams: {action: 'getFilters'},
                            prepare: function(completions) {
                                return prepareCompletions(completions, 'filter');
                            }
                        }, {
                            cacheKey: 'modSnippet',
                            requestParams: {action: 'getObjects', classKey: 'modSnippet'},
                            prepare: function(completions) {
                                return prepareCompletions(completions, 'snippet');
                            }
                        },
                    ], callback);
                    break;
                case 'object':
                    var aliases = {
                        'modSystemSetting': 'setting',
                        'modTemplateVar': 'tv',
                        'modSnippet': 'snippet',
                        'modChunk': 'chunk'
                    };
                    alias = aliases[classKey];

                    var completionParameters = [];
                    completionParameters[0] = {
                        cacheKey: classKey,
                        requestParams: {action: 'getObjects', classKey: classKey},
                        prepare: function(completions) {
                            return prepareCompletions(completions, alias);
                        }
                    };
                    if (classKey == 'modTemplateVar') {
                        completionParameters[1] = {
                            cacheKey: 'resourcefield',
                            requestParams: {action: 'getResourceFields'},
                            prepare: function(completions) {
                                return prepareCompletions(completions, 'field');
                            }
                        };
                    }
                    gatherCompletions(completionParameters, callback);
                    break;
            }
        }
    });
}

Ext.reg('modx-texteditor',MODx.ux.Ace);