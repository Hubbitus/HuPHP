<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuLOG
* @version 2.0.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ???
*
* @uses dump
* @uses outExtraData.interface
**/

use Hubbitus\HuPHP\Debug\Dump;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\System\OS;

/**
* Common implementation suitable for the most types. Primarily intended for logs, like:
* Single::def('HuLog')->toLog('Exception occurred: ' . $e->getMessage(), 'ERR', 'Some', new commonOutExtraData($SomeCurrentStructuredData));
* Output based on dump::* functions
**/
class OutExtraDataCommon implements IOutExtraData {
protected $_var = null;

	public function __construct($var){
		$this->_var =& $var;
	}

	public function strToConsole($format = null){
		return Dump::c($this->_var, null, true);
	}
	public function strToFile($format = null){
		return Dump::log($this->_var, null, true);
	}
	public function strToWeb($format = null){
		return Dump::w($this->_var, null, true);
	}
	public function strToPrint($format = null){
		return static::strToPrintBase($this, $format);
	}
	public function strByOutType($type, $format = null){
		return static::strByOutTypeBase($this, $type, $format);
	}

	public static function strByOutTypeBase(/*$this*/&$obj, $type, $format = null){
		$obj->_curTypeOut = $type;

		switch ($type){
			case OS::OUT_TYPE_BROWSER:
			return $obj->strToWeb($format);
				break;

			case OS::OUT_TYPE_CONSOLE:
				return $obj->strToConsole($format);
				break;

			case OS::OUT_TYPE_FILE:
				return $obj->strToFile($format);
				break;

			// Addition, pseudo
			case OS::OUT_TYPE_PRINT:
				return $obj->strToPrint($format);
				break;

			default:
				throw new VariableRangeException("$type MUST be one of: OS::OUT_TYPE_BROWSER, OS::OUT_TYPE_CONSOLE, OS::OUT_TYPE_FILE or OS::OUT_TYPE_PRINT!");
		}
	}

	public static function strToPrintBase(/*$this*/&$obj, $format = null){
		$obj->_curTypeOut = OS::OUT_TYPE_PRINT;//Pseudo. Will be clarified.
		if (OS::OUT_TYPE_BROWSER == OS::getOutType()) return $obj->strToWeb($format);
		else return $obj->strToConsole($format);
	}
}
