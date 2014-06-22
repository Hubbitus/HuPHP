<?
/**
* Toolkit of small functions aka "macroses".
* eecho macros.
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-12-14 12:10
**/

/**
* eecho like echo, but write to stderr instead of stdout.
*
* @uses	mb_strtoupper
* @param	string	$str	String to out
* @return	boolean
**/
function eecho($str){
	return file_put_contents('php://stderr', $str);
}#f eecho
?>