<?
/**
* Toolkit of small functions as "macroses".
* DEBUG version
* @package Macroses
* @version 1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	2008-05-29 19:55 version 1.2 from 1.0
*	- Rewritten with VariableRequiredException. Now provide only backtrace, not Tokenizer! It takes
*	less overhead and debag/nodebug handled in one place (in exception class)
**/

include_once('Exceptions/variables.php');

/**
* Thows {@see VariableRequiredException) if is_null($var)
* In constructor of VariableIsNullException passed object(backtrace).
* Otherwise return ref to var (&ref).
* This is usefull in direct operations like assigment, or other. F.e:
*	$this->settings = REQUIRED_VAR($settings);
*
* @param	&mixed	$var	Variable to test.
* @param	string	$varname	If present, initialise them arg of Tokenizer, else real parse.
* @return &mixed
* @Throw(VariableIsNullException)
**/
function &REQUIRED_NOT_NULL(&$var, $varname = null){
	if (is_null($var)){
		throw new VariableIsNullException(
			new backtrace(),
			$varname,
			'Variable required'
		);
	}
	else return $var;
}
?>