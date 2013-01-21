ace.define("ace/theme/textmate",["require","exports","module","ace/lib/dom"],function(e,t,n){t.isDark=!1,t.cssClass="ace-tm",t.cssText='.ace-tm .ace_gutter {\nbackground: #f0f0f0;\ncolor: #333;\n}\n.ace-tm .ace_print-margin {\nwidth: 1px;\nbackground: #e8e8e8;\n}\n.ace-tm .ace_fold {\nbackground-color: #6B72E6;\n}\n.ace-tm .ace_scroller {\nbackground-color: #FFFFFF;\n}\n.ace-tm .ace_cursor {\nborder-left: 2px solid black;\n}\n.ace-tm .ace_overwrite-cursors .ace_cursor {\nborder-left: 0px;\nborder-bottom: 1px solid black;\n}\n.ace-tm .ace_invisible {\ncolor: rgb(191, 191, 191);\n}\n.ace-tm .ace_storage,\n.ace-tm .ace_keyword {\ncolor: blue;\n}\n.ace-tm .ace_constant {\ncolor: rgb(197, 6, 11);\n}\n.ace-tm .ace_constant.ace_buildin {\ncolor: rgb(88, 72, 246);\n}\n.ace-tm .ace_constant.ace_language {\ncolor: rgb(88, 92, 246);\n}\n.ace-tm .ace_constant.ace_library {\ncolor: rgb(6, 150, 14);\n}\n.ace-tm .ace_invalid {\nbackground-color: rgba(255, 0, 0, 0.1);\ncolor: red;\n}\n.ace-tm .ace_support.ace_function {\ncolor: rgb(60, 76, 114);\n}\n.ace-tm .ace_support.ace_constant {\ncolor: rgb(6, 150, 14);\n}\n.ace-tm .ace_support.ace_type,\n.ace-tm .ace_support.ace_class {\ncolor: rgb(109, 121, 222);\n}\n.ace-tm .ace_keyword.ace_operator {\ncolor: rgb(104, 118, 135);\n}\n.ace-tm .ace_string {\ncolor: rgb(3, 106, 7);\n}\n.ace-tm .ace_comment {\ncolor: rgb(76, 136, 107);\n}\n.ace-tm .ace_comment.ace_doc {\ncolor: rgb(0, 102, 255);\n}\n.ace-tm .ace_comment.ace_doc.ace_tag {\ncolor: rgb(128, 159, 191);\n}\n.ace-tm .ace_constant.ace_numeric {\ncolor: rgb(0, 0, 205);\n}\n.ace-tm .ace_variable {\ncolor: rgb(49, 132, 149);\n}\n.ace-tm .ace_xml-pe {\ncolor: rgb(104, 104, 91);\n}\n.ace-tm .ace_entity.ace_name.ace_function {\ncolor: #0000A2;\n}\n.ace-tm .ace_markup.ace_heading {\ncolor: rgb(12, 7, 255);\n}\n.ace-tm .ace_markup.ace_list {\ncolor:rgb(185, 6, 144);\n}\n.ace-tm .ace_meta.ace_tag {\ncolor:rgb(0, 22, 142);\n}\n.ace-tm .ace_string.ace_regex {\ncolor: rgb(255, 0, 0)\n}\n.ace-tm .ace_marker-layer .ace_selection {\nbackground: rgb(181, 213, 255);\n}\n.ace-tm.ace_multiselect .ace_selection.ace_start {\nbox-shadow: 0 0 3px 0px white;\nborder-radius: 2px;\n}\n.ace-tm .ace_marker-layer .ace_step {\nbackground: rgb(252, 255, 0);\n}\n.ace-tm .ace_marker-layer .ace_stack {\nbackground: rgb(164, 229, 101);\n}\n.ace-tm .ace_marker-layer .ace_bracket {\nmargin: -1px 0 0 -1px;\nborder: 1px solid rgb(192, 192, 192);\n}\n.ace-tm .ace_marker-layer .ace_active-line {\nbackground: rgba(0, 0, 0, 0.07);\n}\n.ace-tm .ace_gutter-active-line {\nbackground-color : #dcdcdc;\n}\n.ace-tm .ace_marker-layer .ace_selected-word {\nbackground: rgb(250, 250, 255);\nborder: 1px solid rgb(200, 200, 250);\n}\n.ace-tm .ace_indent-guide {\nbackground: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAACCAYAAACZgbYnAAAAE0lEQVQImWP4////f4bLly//BwAmVgd1/w11/gAAAABJRU5ErkJggg==") right repeat-y;\n}\n';var r=e("../lib/dom");r.importCssString(t.cssText,t.cssClass)})