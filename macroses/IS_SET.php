<?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-03-01 13:57
**/

/**
* Intended to USE ANYWERE INSTEAD OF ISSET!!!
* Return TRUE isset, correct handling strings opposite official version.
*
* See PHP bugs:
* http://bugs.php.net/bug.php?id=43889
* http://bugs.php.net/bug.php?id=38165
* http://bugs.php.net/bug.php?id=29883
* http://bugs.php.net/bug.php?id=26413
*
* Thay want think this is "future" (I think it is a BUG!) of PHP.
* When we want check presented key in array we MUST do there 3 checks if check in array due to the
* ($str = 'text'; isset($str['any index']) <- Always return true!!!)
* instead of just check: if ( isset($GLOBALS['__CONFIG'][$classname]['class_file']) ) :
*	isset($GLOBALS['__CONFIG'][$classname])
*	and is_array($GLOBALS['__CONFIG'][$classname])
*	and isset($GLOBALS['__CONFIG'][$classname]['class_file'])
* OR use this dirty hack, which shortly, and does the same:
*	in_array('class_file', (array)@$GLOBALS['__CONFIG'][$classname])
* OR can check on string...
*
* Last variant is safele for all cases as I think. So, implement it.
*
* @param string	$what Key
* @param &(string|array)	$where Where check.
* @return boolean
**/
function is_set($what, $where){// Opposite to standard isset.
	if (is_string($where) and !is_numeric($what)) return false;
	else return isset($where[$what]);
}
?>