This project has started a migration of an ancient PHP framework, originally written for PHP 4 and 5, to the PHP 8+.

# General
1. All chat conversation with user preferred in Russian
2. All comments in code and documentation may be only in English!

# Code refactoring
Code refactoring should be made, following these principles and requirements:
1. Compatibility with PHP versions 8.0 and later. All compatibility "crutches" with versions below 7 should be removed (calls must be refactored accordingly).
2. Class names, function names, and file names must follow [PSR-4](https://www.php-fig.org/psr/psr-4). Rename everything that doesn't match and bring it to this:
   - All classes and files must use namespaces. The top-level namespace of the framework: `namespace Hubbitus\HuPHP`. For other files names based on conventions by directory path
   - Files must have `use` statements to use classes by simple name, without specifying the full package at the usage point.
   - Rely on autoloading and class import by namespaces. Manual `include`/`require` calls must be removed everywhere.
     - If they need to be kept somewhere directly (only some rare "magic"), there must be a comment explaining why this is unavoidable!
   - All calls to global functions must be denoted by "\", like `\is_array()`.
3. Almost every method had have a comment at the end: `#m methodName` or `//c methodName`, and classes have `#c className` or `//c className`, and constructors sometimes also have `#__c` - please remove them.
4. Old PHP style code (e.g. `array()` instead of `[]`) should be modernized.
5. In comments and documentation found "typos" cases where there is no doubt in meaning like "formating" instead of "formatting" must be fixed automatically!
6. Old fashion `call_user_func`/`call_user_func_array`/`create_function` should be refactored to use `Closure`, and `arrow functions` (`fn() => ...`) where possible.
7. For methods where not specified, should be added:
   - access modifiers (public, private, protected)
   - argument types (prefer more concrete from context, not always set `mixed`)
   - return value types
8. Same for constants - specify modifiers, types
8. PHPDoc must be updated for the modified (refactored method) accordingly. Especially tags `@param`, `@return`, `@throws`


# Code style
1. All open braces "{" **must be on the same line** of entity or item they enclose (class or method name, condition and so on...), separated with single space from it.
2. Between methods should be 1 single empty line
3. In all PHP files must present strict declaration clause: "declare(strict_types=1);".
4. All PHP files must use full open tag "<?php" and should not use close tag "?>"
5. PHP files must have last empty single line
6. All variables, method input parameters and output results must have type specification!
7. All conditions if/else must have body with braces `{}`
8. All call to global functions must be denoted by "\", like `\is_array()`.

# Automated testing
Continue writing and enabling tests.

1. All implemented tests must be enabled and pass. If there are some failures - they must be fixed immediately.
2. In first place tests must be changed, if it fails.
3. If code needs to be fixed - check to do not introduce logic drifting.
4. Follow other instructions and code-style from @AGENTS.md for any code write or modification.
5. After complete new test class:
    * run test verification by running script: `tests/run-tests.sh`. Fix any errors and warnings if appeared.
    * suggest (but do NOT run before user acceptance) run git commit with formed message by format:
    ```
    Autotesting: add class XXX (+Y% coverage)

    Short description, statistic and coverage summary. 2-7 lines.
    ```
6. After agree from user and commit, continue tests implementation. Our global goal at least 90% code coverage by classes!
