<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Consts;

/**
* Constants manipulation
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created ?2008-05-29 17:45 ver 1.0 to 1.0.1
* @example Consts.example.php
**/
class Consts {
	/**
	* Return array of constants
	*
	* @param string $regexp Regexp to filter out. Default "@.*@i", what mean - no filter, return all.
	* @param ?string $category Category of constants needed. If null - return all categories.
	* @return array Associative array of matched constants with its values.
	**/
	public static function getByRegexp(string $regexp = '@.*@i', ?string $category = null): array {
		if ($category === null) {
			$constants = \get_defined_constants();
		}
		else {
			$all_constants = \get_defined_constants(true);
			$constants = $all_constants[$category] ?? [];
		}

		$filtered = @\preg_grep($regexp, \array_flip($constants));
		if ($filtered !== false) {
			return \array_flip($filtered);
		}

		return [];
	}

	/**
	* Return pair constant-name and its values
	*
	* @param string $const Constant name.
	* @return array Associative array with key of constant-name, and value its value
	**/
	public static function get(string $const): array {
		return [$const => \constant($const)];
	}

	/**
	* Locate constant-name by its value.
	*
	* @param mixed $value Needed value
	* @param string $regexp Regexp to filter out. Default "@.*@i", what mean - no filter, return all. {@see ::getByRegexp}
	* @param ?string $category Category of constants needed. If null - return all categories. {@see ::getByRegexp}
	* @return array Associative array of matched constants with its values.
	**/
	public static function getNameByValue($value, string $regexp = '@.*@i', ?string $category = null): array {
		$constants = self::getByRegexp($regexp, $category);

		return \array_filter($constants, function($item) use ($value) {
			return ($value === $item);
		});
	}
}
