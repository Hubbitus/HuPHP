
В данном проекте начата миграция древнего фреймворка PHP, написанного для PHP 4 и 5 на новую версию PHP 8.5.

Продолжаем миграцию по шагам:

1. Самый минимум - нужно добиться чтобы заработал скрипт ./tools/regenerate.all
2. Добавить сборку пакета composer. Подготовить фреймворк для публикации.
3. Обновить README.md файл.

Можно и нужно рефакторить код, опираясь на следующие принципы и требования:
1. Совместимость с PHP версий 8.0 и далее. Все "костыли" совместимости с версией ниже 7 можно выкидывать, соответствующим образом зарефакторив вызовы.
2. Имена классов, функций и файлов должны следовать [PSR-4](https://www.php-fig.org/psr/psr-4). Всё не совпадающее переименовываем и приводим к этому:
    - Все классы и файлы должны использовать неймспейсы. Верхнего уровня неймспейс фреймворка: "namespace Hubbitus\HuPHP". Для остальных файлов исходя из положения директорий
    - В файлах должны быть `use` инструкции, чтобы использовать классы просто по имени, без указания полного пакета в месте использования.
    - Полагаемся на автолоад и импорт классов по неймспейсаv. Ручные `include`/`require` вызовы должны быть везде удалены.
        - Если где-то прямо требуется их оставить, обязательно должен быть комментарий почему нельзя без этого!
    - All call to global functions must be denoted by "\", like `\is_array()`.
3. Почти у каждого метода в конце есть комментарий: "#m methodName" bkb "//c methodName", а у классов "#c className" или "//c className", а у конструкторов ещё иногда встречаются "#__c" - убери их, пожалуйста.
4. Old PHP style code (`array()` instead of `[]`) should be modernized.
5. In comments and documentation found "typos" cases where no doubt in meaning like "formating" instead of "formatting" must be fixed automatically!
6. Old fashion `call_user_func`/`call_user_func_array`/`create_function` should be refactored to use `Closure`, and `arrow functions` (`fn() => ...`) where possible.


# Code style
1. All open braces "{" must be on the same line of entity or item their encloses (class or method name, condition and so on...), separated with single space from it.
2. Between methods should be 1 single empty line
4. In all PHP files must present strict declaration clause: "declare(strict_types=1);".
5. All PHP files must use full open tag "<?php" and should not use close tag "?>"
6. PHP files must have last empty single line
7. All variables, method input parameters and output results must have type specification!
8. All conditions if/else must have body with braces `{}`

# Автотесты
Продолжаем писать и включать тесты.
Конечно продолжаем включать отключенные тесты.

Для файлов, которые требуют изменений и рефакторинга, не стесняемся, делаем это активно:
1. Для методов где не указано, выводим и добавляем:
   - модификаторы доступа (public, private, protected)
   - argument types (prefer more concrete from context, not always set `mixed`)
   - типы возвращаемых значений
2. Также для констант - указываем модификаторы, типы
3. Если видно что код был написан для совместимости с PHP версии ниже 7 - смело удаляем! Разумеется, сохраняя логику.
4. All call to global functions must be denoted by "\", like `\is_array()`.
5. CHECK and update all PHPDoc for th modified (refactored method), update it accordingly. Especially tags `@param`, `@return`, `@throws`
6. Follow other instructions and code-style from @AGENTS.md too!