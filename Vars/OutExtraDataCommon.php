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

	public function strForConsole(array|string|null $format = null): string {
		return Dump::c($this->_var, null, true);
	}
	public function strForFile(array|string|null $format = null): string {
		return Dump::log($this->_var, null, true);
	}
	public function strForWeb(array|string|null $format = null): string {
		return Dump::w($this->_var, null, true);
	}
	public function strForPrint(array|string|null $format = null): string {
		return static::strForPrintBase($this, $format);
	}
	public function strByOutType(int $type, array|string|null $format = null): string {
		return static::strByOutTypeBase($this, $type, $format);
	}

	public static function strByOutTypeBase(/*$this*/&$obj, int $type, array|string|null $format = null): string {
		$obj->_curTypeOut = $type;

		switch ($type){
			case OS::OUT_TYPE_BROWSER:
			return $obj->strForWeb($format);
				break;

			case OS::OUT_TYPE_CONSOLE:
				return $obj->strForConsole($format);
				break;

			case OS::OUT_TYPE_FILE:
				return $obj->strForFile($format);
				break;

			// Addition, pseudo
			case OS::OUT_TYPE_PRINT:
				return $obj->strForPrint($format);
				break;

			default:
				throw new VariableRangeException("$type MUST be one of: OS::OUT_TYPE_BROWSER, OS::OUT_TYPE_CONSOLE, OS::OUT_TYPE_FILE or OS::OUT_TYPE_PRINT!");
		}
	}

	public static function strForPrintBase(/*$this*/&$obj, array|string|null $format = null): string {
		$obj->_curTypeOut = OS::OUT_TYPE_PRINT;//Pseudo. Will be clarified.
		if (OS::OUT_TYPE_BROWSER == OS::getOutType()) return $obj->strForWeb($format);
		else return $obj->strForConsole($format);
	}
}
