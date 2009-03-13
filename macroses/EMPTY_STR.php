<?
/**
* Toolkit of small functions as "macroses".
*
* @package Macroses
* @version 2.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-13 12:18 ver 1.0 to 2.0
*	- Add PhpDoc to NON_EMPTY_STR macros
*	- Make check on "NON EMPTY" in EMPTY_STR macros more complexity then
*		just "if (@$str)" was in version 1.0. (see in doc below more detailse).
*	- Macros NON_EMPTY_STR now use NON_EMPTY one, for the more complexity provided checks (see before)
*	- Add example and test file and @example tag into both functions.
**/

/**
* Return first NON-empty string if present. Silent return empty string "" otherwise.
*
* WARNING! This macros operate by *strings*. In particular case it means are:
*	1) What null/false and even *TRUE* values threated as EMPTY *STRINGS* and default
*		value will be returned!
*	2) Opposite it, integer 0 failse this check end go to default value, what it also
*		is not what was prefered. We handle "0" correctly too as "NON EMPTY STRING"
*	3) Macros do not intended to use with arrays, but PHP has internal support conversion its
*		to 'Array' string. It is usefull. BUT, nevertheless unfortunately empty
*		array() converted into empty string! To cast into single form, all arrays
*		converted into string like "Array(N)" where N is count of elements.
*
* @example EMPTY_STR.example.php
*
* @params	variable amount of arguments.
* @return	string
**/
function EMPTY_STR(){
$numargs = func_num_args();
$i = 0;
$str = null;
	do{
	$str = func_get_arg($i++);
	}
	while (
		!(//Most comples check. See explanation in PhpDoc
			(//It must be first check, because non-empty array simple check evaluated into true.
				is_array($str) //Explicit check, even it is EMPTY array
				and
				($str = 'Array(' . count($str) . ')')	# Assign in condition
			)
			or
			(
				true === $str	# False and null values self converted to empty string and do not require futher checks
				and
					(
					# Assign in condition and explicitly return true, because '' is false as empty string
					$str = ''
					or
					true
					)
			)
			or
			0 === $str		# Integer 0 is string "0" but evaluated in empty by previous check
			or
			$str	//Last generick check after all special cases!
		)
		and
		$i < $numargs //In do-wile it must be last
	);
return (string)$str;
}#f EMPTY_STR

/**
* If provided argument $str is not empty *string* then return "$prefix.$str.$suffix" otherwise $defValue
*
* WARNING! this macros operate by *STRINGS*, so, it is handle several values such as 0, true, Array() by special way.
* To determine of string "empting" it is fully relyed on {@see EMPTY_STR()}. Please se it for more details.
*
* @example EMPTY_STR.example.php
*
* @param	string $str
* @param	string $prefix
* @param	string $suffix
* @param	string $defValue
* @return	string
**/
function NON_EMPTY_STR(&$str, $prefix='', $suffix='', $defValue=''){
// strlen because '0'? treated as false anddefault value returned
return ( strlen(($str = EMPTY_STR($str))) > 0 ? $prefix.$str.$suffix : $defValue);
}#f NON_EMPTY_STR
?>