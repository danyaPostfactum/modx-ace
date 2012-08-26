Ext.form.CodeArea = Ext.extend(Ext.form.TextField,  {
    
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
    
    style: 'padding:0',

    initEvents : function(){
        Ext.form.CodeArea.superclass.initEvents.call(this);
        this.editor.on('focus', this.onFocus.bind(this));
        this.editor.on('blur', this.onBlur.bind(this));
    },
    
    initComponent : function(){
        this.fieldEl = document.createElement('input');
        this.fieldEl.type = 'hidden';
        this.fieldEl.name = this.name;
        this.fieldEl.value = this.value;
    },

    onRender : function(ct, position){
        if(!this.el){
            this.defaultAutoCreate = {
                tag: "div",
                cls: "x-form-textarea",
                style:"width:100px;height:60px;position:relative",
                autocomplete: "off"
            };
        }
        Ext.form.CodeArea.superclass.onRender.call(this, ct, position);
        if(this.grow){
            this.el.setHeight(this.growMin);
        }

        this.editor = ace.edit(this.el.dom);

        this.el.appendChild(this.fieldEl);
        this.el.setStyle('line-height', '1.3');
        this.el.dom.removeAttribute('name');
        
        this.el.focus = this.focus.bind(this);
        
        this.editor.getSession().setValue(this.fieldEl.value);
               
        this.editor.setShowPrintMargin(false);
        this.editor.getSession().setTabSize(this.tabSize);
        this.editor.setDragDelay(0);
        this.editor.setFontSize(this.fontSize);
        this.editor.setFadeFoldWidgets(false);
        
        this.setShowInvisibles(this.showInvisibles);
        this.setSelectionStyle(this.selectionStyle);
        this.setScrollSpeed(this.scrollSpeed);
        this.setShowFoldWidgets(this.showFoldWidgets);
        this.setUseSoftTabs(this.useSoftTabs);
        this.setUseWrapMode(this.useWrapMode);
        
        this.setTheme(this.theme);
        this.setMode(this.mode);

        this.editor.getSession().on('change', (function(){
            var self = this;
            setTimeout(function(){
                self.fieldEl.value = self.editor.getSession().getValue();
            }, 10);
            this.fireEvent('change');
            this.fireEvent('keydown');
        }).bind(this));
        // TODO: attach autoSize to according event (?)
        this.autoSize();
    },

    onDestroy : function(){
        this.editor.destroy();
        Ext.form.CodeArea.superclass.onDestroy.call(this);
    },
    
    validate : function(){
        return true;
    },

    getErrors : function(value){
        return null;
    },
    
    onResize : function(){
        this.editor.resize();
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

    getValue : function (){
        return this.fieldEl.value;
    },

    setValue : function (value){
        if (this.editor) {
            this.editor.getSession().setValue(value);
        } else {
            this.fieldEl.value = value;
        }
        this.value = value;
    },

    setMode : function (mode){
        this.editor.getSession().setMode( 'ace/mode/' + mode );
    },
    
    setTheme : function(theme){
        this.editor.setTheme('ace/theme/' + theme);
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

    insert : function (value){
        return this.editor.insert(value);
    },
    
    focus: function (){
        this.editor.focus();
    },

    blur: function (){
        this.editor.blur();
    }
});
Ext.reg('codearea', Ext.form.CodeArea);

MODx.form.CodeArea = Ext.extend(Ext.form.CodeArea, {

    mimeType : 'text/plain',

    theme : MODx.config['ace.theme'] || 'textmate',

    fontSize : MODx.config['ace.font_size'] || '13px',

    useWrapMode : MODx.config['ace.word_wrap'] == true,

    onRender : function (ct, position) {

        MODx.form.CodeArea.superclass.onRender.call(this, ct, position);
        
        MODx.load({
            xtype: 'modx-treedrop',
            target: this,
            targetEl: this.el,
            onInsert: (function(s){
                this.insert(s);
                this.focus();
                return true;
            }).bind(this),
            iframe: true
        });

        this.setMimeType(this.mimeType);

        this.editor.commands.addCommand({
            name: "find",
            bindKey: {win: "Ctrl-F", mac: "Command-F"},
            exec: (function(){this.showFindReplaceWindow(0);}).bind(this),
            readOnly: true
        });
        
        this.editor.commands.addCommand({
            name: "replace",
            bindKey: {win: "Ctrl-R|Ctrl-Shift-R", mac: "Command-Option-F|Command-Shift-Option-F"},
            exec: (function(){this.showFindReplaceWindow(1);}).bind(this),
            readOnly: true
        });
        
        this.editor.commands.addCommand({
            name: "gotoline",
            bindKey: {win: "Ctrl-L", mac: "Command-Option-L"},
            exec: this.showGotoLineWindow.bind(this),
            readOnly: true
        });

        this.windows = [];
    },

    onDestroy : function(){
        MODx.form.CodeArea.superclass.onDestroy.call(this);
    },

    setMimeType : function (mimeType){
       var mode;

       switch (mimeType)
       {
            case 'text/x-php':
            case 'application/x-php':
                mode = 'php';
                break;
            case 'text/x-sql':
                mode = 'sql';
                break;
            case 'text/x-scss':
                mode = 'scss';
                break;
            case 'text/x-less':
                mode = 'less';
                break;
            case 'text/xml':
            case 'application/xml':
                mode = 'xml';
                break;
            case 'image/svg+xml':
                mode = 'svg';
                break;
            case 'text/html':
            case 'application/xhtml+xml':
                mode = 'html';
                break;
            case 'text/javascript':
            case 'application/javascript':
                mode = 'javascript';
                break;
            case 'application/json':
                mode = 'json';
                break;
            case 'text/css':
                mode = 'css';
                break;
            case 'text/plain':
            default:
               mode = 'text';
       }
       this.setMode( mode );
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
                ,hideBorders: false
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
            }],
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
            }).bind(this),
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
            }],
        });

        return window;
    }
});

Ext.reg('modx-codearea',MODx.form.CodeArea);