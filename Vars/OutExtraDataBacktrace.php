<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;
use Hubbitus\HuPHP\System\OutputType;
use Hubbitus\HuPHP\Vars\OutExtraDataCommon;

/**
* Debug and backtrace toolkit.
* Class to provide convenient backtrace logging.
*
* @package Debug
* @subpackage Backtrace
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-03-07 18:35
*
* @uses commonOutExtraData
**/

class OutExtraDataBacktrace extends OutExtraDataCommon {
	public function strForConsole(array|string|null $format = null): string {
		return $this->_var->printFormat(null, OutputType::CONSOLE);
	}

	public function strForFile(array|string|null $format = null): string {
		return $this->_var->printFormat(null, OutputType::FILE);
	}

	public function strForWeb(array|string|null $format = null): string {
		return $this->_var->printFormat(null, OutputType::WEB);
	}

	public function __toString(): string {
		return $this->strForConsole();
	}
}
