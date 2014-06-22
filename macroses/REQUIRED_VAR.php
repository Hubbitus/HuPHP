<?
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
* @uses VariableRequiredException
* @uses backtrace
**/

/**
* Thows {@see VariableRequiredException) if !$var ({@link http://ru2.php.net/manual/ru/types.comparisons.php}).
* In constructor of VariableRequiredException passed object(backtrace).
* Otherwise return ref to var (&ref).
* This is usefull in direct operations like assigment, or other. F.e:
*	$this->settings = REQUIRED_VAR($settings);
*
* @param	&mixed	$var	Variable to test.
* @param	string	$varname	If present, initialise them arg of Tokenizer, else real parse.
* @return &mixed
* @Throws(VariableRequiredException)
**/
function &REQUIRED_VAR(&$var, $varname = null){
	if (!$var){
		throw new VariableRequiredException(
			new backtrace(),
			$varname,
			'Variable required'
		);
	}
	else return $var;
}
?>