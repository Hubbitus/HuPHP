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
* @uses VariableRequiredException
* @uses backtrace
**/

namespace Hubbitus\HuPHP\Macroses;

use \Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use \Hubbitus\HuPHP\Debug\Backtrace;

/**
* Throws {@see VariableRequiredException) if !$var ({@link http://ru2.php.net/manual/ru/types.comparisons.php}).
* In constructor of VariableRequiredException passed object(backtrace).
* Otherwise return ref to var (&ref).
* This is useful in direct operations like assignment, or other. F.e:
*	$this->settings = REQUIRED_VAR($settings);
*
* @param	&mixed	$var	Variable to test.
* @param	string	$varname	If present, initialize them arg of Tokenizer, else real parse.
* @return &mixed
* @throws VariableRequiredException
**/
function &REQUIRED_VAR($var, $varname = null){
	if (!$var){
		throw new VariableRequiredException (
			new Backtrace(),
			$varname,
			'Variable required'
		);
	}
	else return $var;
}
