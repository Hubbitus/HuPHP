<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

use Hubbitus\HuPHP\System\OutputType;
use Hubbitus\HuPHP\Debug\Dump;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\System\OS;

/**
* Debug and backtrace toolkit.
*
* Common implementation suitable for the most types. Primarily intended for logs, like:
* Single::def('HuLog')->toLog('Exception occurred: ' . $e->getMessage(), 'ERR', 'Some', new commonOutExtraData($SomeCurrentStructuredData));
* Output based on dump::* functions
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
**/
class OutExtraDataCommon implements IOutExtraData {
	protected mixed $_var = null;

	/** @var OutputType|null Current output type for formatting */
	protected ?OutputType $_curTypeOut = null;

	public function __construct($var) {
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

	public function strByOutType(OutputType $type, array|string|null $format = null): string {
		return static::strByOutTypeBase($this, $type, $format);
	}

	public static function strByOutTypeBase(/*$this*/&$obj, OutputType $type, array|string|null $format = null): string {
		$obj->_curTypeOut = $type;

		return match($type) {
			OutputType::WEB => $obj->strForWeb($format),
			OutputType::CONSOLE => $obj->strForConsole($format),
			OutputType::FILE => $obj->strForFile($format),
			OutputType::PRINT => $obj->strForPrint($format),
			default => throw new VariableRangeException($type->name . ' MUST be one of: OutputType::BROWSER, OutputType::CONSOLE, OutputType::FILE or OutputType::PRINT!'),
		};
	}

	public static function strForPrintBase(/*$this*/&$obj, array|string|null $format = null): string {
		$obj->_curTypeOut = OutputType::PRINT; //Pseudo. Will be clarified.
		return OutputType::WEB === OS::getOutType() ? $obj->strForWeb($format) : $obj->strForConsole($format);
	}
}
