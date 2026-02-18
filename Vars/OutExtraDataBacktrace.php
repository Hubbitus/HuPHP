<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

use Hubbitus\HuPHP\System\OS;
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
	public function strToConsole($format = null){
		return $this->_var->printout(true, null, OS::OUT_TYPE_CONSOLE);
	}
	public function strToFile($format = null){
		return $this->_var->printout(true, null, OS::OUT_TYPE_FILE);
	}
	public function strToWeb($format = null){
		return $this->_var->printout(true, null, OS::OUT_TYPE_BROWSER);
	}
	
	public function __toString(): string {
		return $this->strToConsole();
	}
}
