<?
/**
* Toolkit of small functions as "macroses".
*
* @package Macroses
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @changelog
*	* 2009-01-30 15:10 ver 1.0
**/

/**
* Return value of variable if it defined without notices and error-handling.
*
* In most cases check like "if ($variable)" is laconic form of more strict like "if (isset($variable) and $variable)".
* So, if $variable was not defined yet we got notice. Well, when we do not need it, we can suppress it like "if (@$variable)"
* all seems good on first glance but we only supress error message, NOT error processing if it occures!
* So, if error handler was be set before (like set_error_handler("func_error_handler");) this error handler got control and stack will be broken!
*
* @param &mixed	$var variable amount of arguments.
* @return &mixed
**/
function &ISSET_VAR(&$var){
	if (isset($var)) return $var;
	else{
	$t = null; //To do not fire error "Only variables can be passed by reference in ..."
	return $t;
	}
}
?>