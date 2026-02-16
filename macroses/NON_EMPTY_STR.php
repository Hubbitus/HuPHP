<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Macroses;

/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 2.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2009-03-13 12:18 ver 1.0 to 2.0
**/

use function Hubbitus\HuPHP\Macroses\EMPTY_STR;

/**
* If provided argument $str is not empty *string* then return "$prefix.$str.$suffix" otherwise $defValue
*
* WARNING! this macros operate by *STRINGS*, so, it is handle several values such as 0, true, Array() by special way.
* To determine of string "emptying" it is fully relied on {@see EMPTY_STR()}. Please se it for more details.
*
* @example EMPTY_STR.example.php
*
* @param	string $str
* @param	string $prefix
* @param	string $suffix
* @param	string $defValue
* @return	string
**/
function NON_EMPTY_STR($str, $prefix='', $suffix='', $defValue=''){
	return ( \strlen(($str = EMPTY_STR($str))) > 0 ? $prefix.$str.$suffix : $defValue);
}
