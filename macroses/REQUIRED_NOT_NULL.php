<?php
declare(strict_types=1);

/**
* Toolkit of small functions aka "macroses".
* DEBUG version
*
* @package Macroses
* @version 1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-29 19:55 version 1.2 from 1.0
*
* @uses VariableIsNullException
**/

/**
* Thows {@see VariableIsNullException) if is_null($var)
* In constructor of VariableIsNullException passed object(backtrace).
* Otherwise return ref to var (&ref).
* This is useful in direct operations like assignment, or other. F.e:
*	$this->settings = REQUIRED_VAR($settings);
*
* @param	&mixed	$var	Variable to test.
* @param	string	$varname	If present, initialize them arg of Tokenizer, else real parse.
* @return &mixed
* @Throws(VariableIsNullException)
**/
function &REQUIRED_NOT_NULL(&$var, $varname = null){
	if (is_null($var)){
		throw new VariableIsNullException(
			new Backtrace(),
			$varname,
			'Variable required'
		);
	}
	else return $var;
}
