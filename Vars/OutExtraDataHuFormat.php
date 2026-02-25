<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

/**
* Debug and backtrace toolkit.
*
* Class to provide easy wrapper around HuFormat for anywhere usage.
*
* @package Debug
* @subpackage HuLOG
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-03-16 19:06
*
* @uses HuFormat
* @uses commonOutExtraData
**/

use Hubbitus\HuPHP\Debug\HuFormat;
use Hubbitus\HuPHP\System\OutputType;
use function Hubbitus\HuPHP\Macroses\EMPTY_VAR;

/**
* Class to provide easy wrapper around HuFormat for anywhere usage.
**/
class OutExtraDataHuFormat extends OutExtraDataCommon {
	protected array $format;	//Array of format
	protected HuFormat $_format;

	/**
	* Constructor.
	*
	* @param	mixed	$var Var to output with provided format.
	* @param	array	$format	Format how output $var. Must contain 3 elements:
	*	`OutputType::CONSOLE`, `OutputType::WEB`, `OutputType::FILE` keys with according format strings
	**/
	public function __construct($var, array $format){
		$this->format = $format;
		$this->_format = new HuFormat(null, $var);
	}

	/**
	*@inheritdoc
	**/
	public function strForConsole(array|string|null $format = null): string {
		return $this->_format->setFormat(EMPTY_VAR($format, $this->format[OutputType::CONSOLE->name]))->getString();
	}

	/**
	*@inheritdoc
	**/
	public function strForFile(array|string|null $format = null): string {
		return $this->_format->setFormat(EMPTY_VAR($format, $this->format[OutputType::FILE->name]))->getString();
	}

	/**
	*@inheritdoc
	**/
	public function strForWeb(array|string|null $format = null): string {
		return $this->_format->setFormat(EMPTY_VAR($format, $this->format[OutputType::WEB->name]))->getString();
	}
}
