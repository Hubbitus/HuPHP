<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;
use Hubbitus\HuPHP\System\OutputType;

use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;

interface IOutExtraData {
	/**
	* String to print into file. Primary for logs string representation
	*
	* @param array<mixed>|string|null	$format Any useful helper information to format
	* @return string
	**/
	public function strForFile(array|string|null $format = null): string;

	/**
	* Return string to print into user browser.
	*
	* @param array<mixed>|string|null	$format Any useful helper information to format
	* @return string
	**/
	public function strForWeb(array|string|null $format = null): string;

	/**
	* String to print on console.
	*
	* @param array<mixed>|string|null	$format Any useful helper information to format
	* @return string
	**/
	public function strForConsole(array|string|null $format = null): string;

	/**
	* String to print. automatically detect (by {@link OS::getOutType()}) Web or Console and
	*	invoke appropriate ::strToWeb() or ::strToConsole()
	*
	* @param array<mixed>|string|null $format	If @format not-empty use it for formatting result. "Format of $format"
	*	see in {@link settings::getString()}. Put in ::strToWeb() or ::strToConsole()
	* @return string
	**/
	public function strForPrint(array|string|null $format = null): string;

	/**
	* Convert to string by provided type.
	*
	* @param OutputType $type	Output type enum
	* @param array<mixed>|string|null	$format Any useful helper information to format
	* @return string
	* @throws VariableRangeException
	**/
	public function strByOutType(OutputType $type, array|string|null $format = null): string;
}
