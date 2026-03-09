<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Debug;

/**
* Debug and backtrace toolkit.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 2008-06-26 03:58
**/
class DumpUtils {
	/**
	* Transform string, which is text-representation of requested var into more well formatted form.
	* print_r variant
	* @param string $dump String returned by print_r
	* @return string Transformed, well-formatted string
	**/
	public static function transformCorrect_print_r(string $dump): string {
		return \trim(
			\preg_replace(
				[
					'/Array\n\s*\(/',
					'/Object\n\s*\(/',
					'/\["(.+)"\]=>/',
					'/Array\(0\){\s+}/',
				],
				[
					'Array(',
					'Object(',
					'[\1]=>',
					'Array(0){}',
				],
				$dump
			)
		);
	}

	/**
	* Transform string, which is text-representation of requested var into more well formatted form.
	* var_dump variant
	* @param string $dump String returned by var_dump
	* @return string Transformed, well-formatted string
	**/
	public static function transformCorrect_var_dump(string $dump): string {
	return
		trim(/* For var_dump variant */
			preg_replace(
				array(
					'/array(\(\d+\))\s+({)/i',
					'/object\([^)]+\)\s+\(/',
					'/\["?(.+?)"?\]=>/',
				),
				array(
					'Array\1\2',
					'Object(',
					'[\1] => ',
				),
				$dump
			)
		);
	}
}
