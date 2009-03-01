<?
/**
* Toolkit of small functions as "macroses".
*
* @package Macroses
* @version 1.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-01-30 15:10 ver 1.0
*	- Initial version
*
*	* 2009-03-01 13:59 ver 1.0 to 1.1
*	- Add second, "safe" variant of macros IS_SET_VAR. It is based on self
*		{@see is_set} instead of standard isset. See overall description why in it.
*	- Old ISSET_VAR can't be switched on IS_SET, because use 1 incoming parameter.
*		Also, it must be used fo just variables (not check indexes in array or strings)
**/

/**
* Return value of SCALAR variable if it defined without notices and error-handling.
* For safely check indexes (in string and arrays use {@see IS_SET_VAR})
*
* In most cases check like "if ($variable) $str = $variable . 'some'" is laconic form of more strict like "if (isset($variable) and $variable) $str = $variable . 'some'".
* So, if $variable was not defined yet we got notice. Well, when we do not need it, we can suppress it like "if (@$variable)"
* all seems good on first glance but we only supress error message, NOT error handling if it occures!
* So, if error handler was be set before (like set_error_handler("func_error_handler");) this error handler got control and stack will be broken!
*
* With that function we may safely use simple: $str = ISSET_VAR($variable) . 'some'...
*
* For Chec
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

function &IS_SET_VAR($what, &$where){
include_once('macroses/IS_SET.php');//MUST be explicit. It used in autoload.php, so, autoloading is not present yet!
	if (is_set($what, $where)) return $where[$what];
	else{
	$t = null; //To do not fire error "Only variables can be passed by reference in ..."
	return $t;
	}
}
?>