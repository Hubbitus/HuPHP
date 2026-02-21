<?php
declare(strict_types=1);

/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 2.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-13 12:18 ver 1.0 to 2.0
**/

namespace Hubbitus\HuPHP\Macroses;

/**
* Return first NON-empty string if present. Silent return empty string "" otherwise.
*
* WARNING! This macros operate by *strings*. In particular case it means are:
*	1) What null/false and even *TRUE* values treated as EMPTY *STRINGS* and default
*		value will be returned!
*	2) Opposite it, integer 0 fails this check end go to default value, what it also
*		is not what was preferred. We handle "0" correctly too as "NON EMPTY STRING"
*	3) Macros do not intended to use with arrays, but PHP has internal support conversion its
*		to 'Array' string. It is useful. BUT, nevertheless unfortunately empty
*		array() converted into empty string! To cast into single form, all arrays
*		converted into string like "Array(N)" where N is count of elements.
*
* @example EMPTY_STR.example.php
*
* @param array variable amount of arguments.
* @return string
**/
function EMPTY_STR(mixed ...$params){
	$numArgs = func_num_args();
	$i = 0;
	$str = null;

	do{
		$str = func_get_arg($i++);
	}
	while (
		!(//Most complex check. See explanation in PhpDoc
			(//It must be first check, because non-empty array simple check evaluated into true.
				\is_array($str) //Explicit check, even it is EMPTY array
				and
				($str = 'Array(' . count($str) . ')')	// Assign in condition
			)
			or
			(
				true === $str	// False and null values self converted to empty string and do not require futher checks
				and
					(
					// Assign in condition and explicitly return true, because '' is false as empty string
					$str = ''
					or
					true
					)
			)
			or
			0 === $str	// Integer 0 is string "0" but evaluated in empty by previous check
			or
			$str	// Last generic check after all special cases!
		)
		and
		$i < $numArgs //In do-wile it must be last
	);
	return (string)$str;
}
