<?php
/**
 * Settings lexicon for Ace
 *
 * @package ace
 * @subpackage lexicon
 */

$_lang['area_general'] = 'Основные настройки';

$_lang['setting_ace.theme'] = 'Тема редактора';
$_lang['setting_ace.theme_desc'] = 'Доступные темы: ambiance, chaos, chrome, clouds, clouds_midnight, cobalt, crimson_editor, dawn, dreamweaver, eclipse, github, idle_fingers, katzenmilch, kr, kuroir, merbivore, merbivore_soft, mono_industrial, monokai, pastel_on_dark, solarized_dark, solarized_light, terminal, textmate, tomorrow, tomorrow_night, tomorrow_night_blue, tomorrow_night_bright, tomorrow_night_eighties, twilight, vibrant_ink, xcode.';
$_lang['setting_ace.word_wrap'] = 'Перенос строк';
$_lang['setting_ace.word_wrap_desc'] = 'Переносить длинные строки.';
$_lang['setting_ace.font_size'] = 'Размер шрифта';
$_lang['setting_ace.font_size_desc'] = 'Размер шрифта редактора.';
$_lang['setting_ace.soft_tabs'] = 'Мягкая табуляция';
$_lang['setting_ace.soft_tabs_desc'] = 'Использовать пробелы вместо символа табуляции.';
$_lang['setting_ace.tab_size'] = 'Размер табуляции';
$_lang['setting_ace.tab_size_desc'] = 'Размер табуляции, выраженный в количестве пробелов.';
$_lang['setting_ace.fold_widgets'] = 'Свертывание кода';
$_lang['setting_ace.fold_widgets_desc'] = 'Отображать на линейке элементы управления свертыванием кода.';
$_lang['setting_ace.show_invisibles'] = 'Невидимые символы';
$_lang['setting_ace.show_invisibles_desc'] = 'Отображать пробелы, табуляцию и символы конца строк.';
$_lang['setting_ace.snippets'] = 'Сниппеты';
$_lang['setting_ace.snippets_desc'] = 'Сниппеты, разворачиваемые по клавише «Tab». Пример сниппета:<br /><br /><pre>\nsnippet getr\n	[!getResources? parents=`${1}`${2}]]\n</pre></br>Знак табуляции можно вставить используя клавиши Alt + 09';
$_lang['setting_ace.height'] = 'Высота области редактирования';
$_lang['setting_ace.height_desc'] = 'Высота редактора в пикселах. Если значение не указано, редактор будет иметь высоту по умолчанию.';
$_lang['setting_ace.grow'] = 'Подогнать высоту под текст';
$_lang['setting_ace.grow_desc'] = 'Высота редактора будет подогнана под текст. Минимальная высота будет равна опции ace.height. Возможные значения: Пусто - высота не подгоняется под текст; Число больше 0 - высота подгоняется под текст но не более значения; 0 - высота подгоняется под текст, высота не ограничена.';
$_lang['setting_ace.html_elements_mime'] = 'MIME-type для html элементов';
$_lang['setting_ace.html_elements_mime_desc'] = 'Этот тип будет использован редактором для html элементов - шаблонов, чанков и ресурсов с типом html. Если не указан будет использован тип по умолчанию';