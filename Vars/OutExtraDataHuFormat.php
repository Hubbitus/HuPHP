<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

use Hubbitus\HuPHP\Debug\HuFormat;
use Hubbitus\HuPHP\System\OutputType;
use Hubbitus\HuPHP\Macro\Vars;

/**
* Class to provide easy wrapper around HuFormat for anywhere usage.
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created 2009-03-16 19:06
**/
class OutExtraDataHuFormat extends OutExtraDataCommon {
	protected array $format;	//Array of format
	protected HuFormat $_format;

	/**
	* Constructor.
	*
	* @param mixed $var Var to output with provided format.
	* @param array $format Format how output $var. Must contain 3 elements:
	*   `OutputType::CONSOLE`, `OutputType::WEB`, `OutputType::FILE` keys with according format strings
	**/
	public function __construct($var, array $format){
		$this->format = $format;
		$this->_format = new HuFormat(null, $var);
		parent::__construct($var);
	}

	/**
	*@inheritdoc
	**/
	public function strForConsole(array|string|null $format = null): string {
		return $this->_format->setFormat(Vars::firstMeaning($format, $this->format[OutputType::CONSOLE->name]))->getString();
	}

	/**
	*@inheritdoc
	**/
	public function strForFile(array|string|null $format = null): string {
		return $this->_format->setFormat(Vars::firstMeaning($format, $this->format[OutputType::FILE->name]))->getString();
	}

	/**
	*@inheritdoc
	**/
	public function strForWeb(array|string|null $format = null): string {
		return $this->_format->setFormat(Vars::firstMeaning($format, $this->format[OutputType::WEB->name]))->getString();
	}
}
