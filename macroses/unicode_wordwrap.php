<?
/**
* Toolkit of small functions as "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-10-22 19:03 ver 1.0
*	- Initial version
**/

/**
* wordwrap standard function wrap by amount *bytes*, not *chars*!
* Idea got from http://ru.php.net/manual/en/function.wordwrap.php#92577 but its implementation
* don't work. Reimplemented.
*
* @param	string	$str	String to wrap
* @param	integer=75	$len	Length to wit in.
* @param	string=\n	$break	String to place on end
* @param	boolean=false	$cut	Cut words or not (Default false, to wrap by word boundary).
* @return	string
**/
function unicode_wordwrap($str, $len = 75, $break = "\n", $cut = false){
	if($cut) $reg = $reg = "#((.{$len}))#u";
	else $reg = "#((.{0,$len})[^\pL])#u";
return preg_replace($reg, "\\2$break", $str);
}#f unicode_wordwrap
?>