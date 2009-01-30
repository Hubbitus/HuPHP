<?
/**
* Toolkit of small functions as "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

/**
* Return first NON-empty var if present. Nothing return otherwise (yes, warning will).
* @params	variable amount of arguments.
* @return mixed
**/
function EMPTY_VAR(){
$numargs = func_num_args();
$i=0;
	while (
		$i < $numargs
		 and
		!($res = func_get_arg($i++))
	){/*Nothing do, just skip it */}

	if ($res) return $res;
}
?>