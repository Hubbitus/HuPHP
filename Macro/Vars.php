<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Macro;

use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableIsNullException;
use Hubbitus\HuPHP\Debug\Backtrace;

/**
* Variable utility macros as static methods.
*
* @package Hubbitus\HuPHP\Macro
**/
class Vars {
	/**
	* Returns first non-empty variable from provided arguments.
	*
	* @param mixed ...$vars Variables to check
	* @return mixed First non-empty variable or null if all empty
	**/
	public static function firstMeaning(mixed ...$vars): mixed {
		foreach ($vars as $var) {
			if ($var) {
				return $var;
			}
		}
		return null;
	}

	/**
	* Returns first non-empty string from provided arguments.
	*
	* Handles special cases:
	* - null/false/true treated as empty strings
	* - integer 0 is treated as string "0" (non-empty)
	* - arrays converted to string like "Array(N)" where N is count
	*
	* @param mixed ...$strings Strings to check
	* @return string First non-empty string or empty string if all empty
	**/
	public static function firstMeaningString(mixed ...$strings): string {
		foreach ($strings as $str) {
			// Handle arrays specially
			if (\is_array($str)) {
				return 'Array(' . \count($str) . ')';
			}

			// Handle boolean true
			if (true === $str) {
				return '';
			}

			// Handle integer 0
			if (0 === $str) {
				return '0';
			}

			// Standard string check
			if ($str) {
				return (string) $str;
			}
		}
		return '';
	}

	/**
	* Returns string surrounded with prefix and suffix if non-empty, otherwise default value.
	*
	* @param string|null $str String to check
	* @param string|null $prefix Prefix to add
	* @param string|null $suffix Suffix to add
	* @param string|null $defValue Default value if string is empty
	* @return string Formatted string or default value
	**/
	public static function surround(?string $str, ?string $prefix = '', ?string $suffix = '', ?string $defValue = ''): string {
		$str ??= '';
		$result = self::firstMeaningString($str);
		$prefix ??= '';
		$suffix ??= '';
		$defValue ??= '';
		return (\strlen($result) > 0) ? $prefix . $str . $suffix : $defValue;
	}

	/**
	* Returns variable if non-empty, throws exception otherwise.
	*
	* @param mixed $var Variable to test
	* @param string|null $varname Variable name for exception message
	* @return mixed Variable value
	* @throws VariableRequiredException If variable is empty
	**/
	public static function requiredNotEmpty(mixed $var, ?string $varname = null): mixed {
		if (!$var) {
			throw new VariableRequiredException(
				new Backtrace(),
				$varname,
				'Variable required'
			);
		}
		return $var;
	}

	/**
	* Returns variable if not null, throws exception otherwise.
	*
	* @param mixed $var Variable to test
	* @param string|null $varname Variable name for exception message
	* @return mixed Variable value
	* @throws VariableIsNullException If variable is null
	**/
	public static function requiredNotNull(mixed $var, ?string $varname = null): mixed {
		if (null === $var) {
			throw new VariableIsNullException(
				new Backtrace(),
				$varname,
				'Variable required'
			);
		}
		return $var;
	}

	/**
	* Swaps values of two variables.
	*
	* @param mixed $a First variable
	* @param mixed $b Second variable
	* @return void
	**/
	public static function swap(mixed &$a, mixed &$b): void {
		$tmp = $b;
		$b = $a;
		$a = $tmp;
	}

	/**
	* Check if key exists in array or string.
	*
	* Handles string keys correctly, unlike standard isset().
	*
	* @param string|int $what Key to check
	* @param array|string $where Array or string to check in
	* @return bool True if key exists
	**/
	public static function isset(string|int $what, array|string $where): bool {
		if (\is_string($where) && !\is_numeric($what)) {
			return false;
		}
		return isset($where[$what]);
	}
}
