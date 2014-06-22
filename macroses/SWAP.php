<?
/**
* Toolkit of small functions aks "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Swap values of two vars
* @param	&$one	First var
* @param	&$two	Second var
*
* @return	void
**/
function SWAP(&$one, &$two){
$_tmp = $two;

$two = $one;
$one = $_tmp;
}
?>