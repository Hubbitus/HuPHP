<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\RegExp;

/**
* RegExp manipulation.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 21.06.2014 14:56:24
**/
interface IRegExp {
	/**
	* Quote given string or each (recursive) string in array.
	*
	* @param string|array $toQuote
	* @param string $delimiter Chars to addition escape. Usually (and default) char start and end of regexp.
	* @return string|array Same type as given.
	**/
	public static function quote($toQuote, $delimiter = '/');
}
