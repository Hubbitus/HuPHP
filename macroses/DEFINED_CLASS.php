<?
/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Return name of first defined class from names comes as arguments. Nothing return otherwise (yes, warning will).
*
* Common usage: $classname = DEFINED_CLASS('some_child', 'base'); $obj = new $classname();
*
* @params	variable amount of arguments - strings of class name to try.
* @return	string|null
**/
function DEFINED_CLASS(){
$numargs = func_num_args();
$i=0;
	while (
		$i < $numargs
		 and
		!( $res = class_exists($classname = func_get_arg($i++)) )
	){/*Nothing do, just skip it */}

	if ($res) return $classname;
}
?>