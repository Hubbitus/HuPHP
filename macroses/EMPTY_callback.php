<?php
declare(strict_types=1);

/**
* Toolkit of small functions aka "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @since 2010-04-03 00:31
**/

/**
* Return first argument after @call for what true === call($arg). Nothing return otherwise (yes, warning will).
* It is logic continue of set macroses like EMPTY_INT, EMPTY_STR, EMPTY_VAR...
*
* @param	callback	$call
* @param 	mixed params	variable amount of arguments to check.
* @return mixed
**/
function EMPTY_callback($call, ...$params){
$numargs = func_num_args();
$i = 1; //0 is callback
	while (
		$i < $numargs
		 and
		!($res = call_user_func($call, $arg = func_get_arg($i++)))
	){/*Nothing do, just skip it */}

	if ($res) return $arg;
}
