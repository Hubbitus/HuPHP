<?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-10-23 16:43
**/

/**
* Unicode variant of ucfirst!
* Idea got from http://php.net/manual/en/function.ucfirst.php#87133 but its implementation
* is very long and hard without reason.
*
* @uses	mb_strtoupper
* @param	string	$str	String to process
* @param	string=UTF-8	$enc
* @return	string
**/
function unicode_ucfirst($str, $enc = 'UTF-8'){
	return preg_replace('/^./ue', "mb_strtoupper('\\0', '$enc')", $str);
}#f unicode_ucfirst
?>