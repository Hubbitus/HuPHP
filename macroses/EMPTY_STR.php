<?
/**
* Toolkit of small functions as "macroses".
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Return first NON-empty string if present. Silent return empty str "" otherwise.
* @params	variable amount of arguments.
* @return string
**/
function EMPTY_STR(){
$numargs = func_num_args();
$i=0;
	while (
		$i < $numargs
		 and
		!(string)($res = func_get_arg($i++))
	){/*Nothing doing, just skip it */}
return (string)$res;
}

#Если НЕпустой первый аргумент, то вернуть его c префиксом и суффиксом. Если пустой - дефолтное значение
function NON_EMPTY_STR (&$str, $prefix='', $suffix='', $defValue=''){
return ( @$str ? (string)$prefix.$str.$suffix : $defValue);
}
?>