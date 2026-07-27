# Ace

Интеграция [Ace](https://ace.c9.io/) (редактор кода) в **MODX Revolution** (2.x и 3.x): подсветка синтаксиса, автодополнение, темы, TV-поле и плагин для менеджера.

**Версия пакета:** 1.9.11 (см. `_build/build.config.php` и `core/components/ace/documents/changelog.txt`)

## Возможности

- Редактор кода в ресурсах, чанках, сниппетах, шаблонах и связанных формах менеджера
- Автодополнение (поля ресурсов, фильтры, свойства, объекты и т.д.)
- Множество тем оформления и режимов (в т.ч. PHP, HTML, CSS, JavaScript, JSON, YAML и др.)
- Сочетания клавиш: **Ctrl+Alt+H** — список горячих клавиш; **Ctrl+Shift+B** — форматирование кода; **Alt+Z** — перенос по словам

## Требования

- Установленный **MODX Revolution** 2 или 3

## Установка

Обычно через **Установщик пакетов** MODX: загрузка транспортного пакета `.transport.zip` или установка из репозитория extras.

Исходники этого репозитория — для разработки и сборки; на продакшене используйте готовый пакет или сборку из `_build/`.

## Сборка транспортного пакета

1. Положите каталог компонента в дерево установки MODX (или укажите корень через переменную окружения).
2. Запустите `_build/build.transport.php` из корня компонента (CLI с заданным `MODX_BASE_PATH` или из контекста установки MODX).

Скрипт совместим с **MODX 2 и MODX 3**. Для установки собранного пакета на MODX 2 при сборке на MODX 3 в скрипте предусмотрено приведение манифеста к именам классов транспорта MODX 2 (подробнее — в `core/components/ace/documents/readme.txt`).

Поиск корня MODX: `MODX_BASE_PATH` → `core/config/config.inc.php` вверх по каталогам → `core/model/modx/modx.class.php`.

## Структура репозитория

| Путь | Назначение |
|------|------------|
| `core/components/ace/` | PHP-модель, плагин, процессоры автодополнения, лексиконы, TV input |
| `assets/components/ace/` | JS (Ace, `modx.texteditor.js`), Emmet, `completions.php` |
| `_build/` | Конфиг и скрипт сборки транспорта, резолвер установки |

## Документация и история изменений

- Подробности для пользователей/сборки: `core/components/ace/documents/readme.txt`
- Changelog: `core/components/ace/documents/changelog.txt`
- Лицензия: `core/components/ace/documents/license.txt` (GNU GPLv2 или новее)

## Ссылки

- Репозиторий: [github.com/modx-pro/modx-ace](https://github.com/modx-pro/modx-ace)