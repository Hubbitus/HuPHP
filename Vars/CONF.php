<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

use Hubbitus\HuPHP\Vars\Single;
use Hubbitus\HuPHP\Vars\HuArray;

/**
* CONF() function - short alias to Single::def('config')
*
* WHY FUNCTION_EXISTS GUARD IS NEEDED:
*
* PHPUnit code coverage (php-code-coverage) loads ALL .php files from <include> directories
* during coverage report generation. This happens in addition to normal bootstrap loading.
*
* Related Issues & Discussions:
* - PHPUnit #3447 (CLOSED): Bootstrap loaded twice with @runTestsInSeparateProcesses
*   https://github.com/sebastianbergmann/phpunit/issues/3447
* - PHPUnit #3100: Whitelist calling require_once for coverage
*   https://github.com/sebastianbergmann/phpunit/issues/3100
* - StackOverflow: "Cannot redeclare error when generating PHPUnit code coverage report"
*   https://stackoverflow.com/questions/2816173/cannot-redeclare-class-error-when-generating-phpunit-code-coverage-report
* - Laracasts: "PHPUnit: Cannot redeclare function"
*   https://laracasts.com/discuss/channels/testing/phpunit-cannot-redeclare-function
*
* The guard prevents "Cannot redeclare function" errors when:
* 1. bootstrap.php loads CONF.php via require_once
* 2. php-code-coverage loads CONF.php again for coverage analysis (processUncoveredFilesFromWhitelist)
*
* This is standard practice for global functions in PHPUnit projects with coverage:
* - Symfony polyfills use similar guards
* - Composer autoload files use similar guards
* - WordPress plugins use similar guards
*
* @param string|null $className Optional class name
* @param boolean     $noThrow   If true - silently not thrown any exception.
* @return Single|HuArray If className present - HuArray returned, Single(HuConfig) otherwise
**/
if (!\function_exists('Hubbitus\HuPHP\Vars\CONF')) {
	function &CONF($className = null, $noThrow = false): mixed {
		/*
		* Strange, but if we direct return:
		* if ($className) return Single::def('HuConfig')->$className;
		* All work as expected and variable returned by reference, but notice occurred:
		* PHP Notice:  Only variable references should be returned by reference in /var/www/_SHARED_/Vars/HuConfig.class.php on line 111
		* implicit call to __get solve problem. Is it bug?
		* @todo Fill bug
		**/
		/*
		* We want use HuConfig in singleton::def. It is produce cycle dependency.
		* So, rely on HuConfig do not take any settings in constructor, we may safely call Single::singleton directly
		* if ($className) return Single::def('HuConfig')->__get($className);
		* else return Single::def('HuConfig');
		**/
		if ($className) {
			return Single::singleton(HuConfig::class)->__get($className);
		}
		else {
			return Single::singleton(HuConfig::class);
		}
	}
}
