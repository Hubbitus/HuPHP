<?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Assign value of variable if value not (bool)false.
* @param	&mixed	$var
* @param	&mixed	$value
* @return void
**/
function ASSIGN_IF(&$var, &$value){
	if ($value) $var = $value;
}#f ASSIGN_IF
?>