<?php
/**
* RegExp manupulation.
*
* @package RegExp
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2014, Pahan-Hubbitus (Pavel Alexeev)
* @created 21.06.2014 14:56:24
**/

/**
* After PHP 5.2.0 get error defining abstract static methods: Strict standards: Static function RegExp_base_base::quote() should not be abstract :
* http://www.php.net//manual/ru/migration52.incompatible.php , So introduce interface as workaround ( as per http://stackoverflow.com/questions/13494807/strict-standards-static-function-modeltablestruct-should-not-be-abstract-in )
**/
interface IRegExp{
	/**
	* Quote given string or each (recursive) string in array.
	*
	* @param	string|array	$toQuote
	* @param	string='/'	$delimiter. Chars to addition escape. Usaly (and default) char start and end of regexp.
	* @return	string|array	Same type as given.
	**/
	public static function quote($toQuote, $delimeter = '/');
}