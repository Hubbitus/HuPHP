<?
/**
* Debug and backtrace toolkit.
* Class to provide convenient backtrace logging.
*
* @package Debug
* @subpackage Bactrace
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-03-07 18:35
*
* @uses commonOutExtraData
**/

class backtrace_out extends commonOutExtraData{
	public function strToConsole($format = nul){
	return $this->_var->printout(true, null, OS::OUT_TYPE_CONSOLE);
	}#m strToConsole

	public function strToFile($format = null){
	return $this->_var->printout(true, null, OS::OUT_TYPE_FILE);
	}#m strToFile

	public function strToWeb($format = null){
	return $this->_var->printout(true, null, OS::OUT_TYPE_BROWSER);
	}#m strToWeb
}#c
?>