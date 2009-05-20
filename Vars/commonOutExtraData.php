<?
/**
* Debug and backtrace toolkit.
*
* @package Debug
* @subpackage HuLOG
* @version 2.0.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
**/

/*-inc
include_once('Vars/outExtraData.interface.php');
include_once('Debug/debug.php');
*/
/**
* @uses dump
* @uses outExtraData.interface
**/

/**
* Common realisation suitable for the most types. Primarly intended to logs, like:
* Single::def('HuLog')->toLog('Exception occured: ' . $e->getMessage(), 'ERR', 'Some', new commonOutExtraData($SomeCurrentSctructuredData));
* Output based on dump::* functions
**/
class commonOutExtraData implements outExtraData{
protected $_var = null;
	public function __construct($var){
	$this->_var =& $var;
	}

	public function strToConsole($format = null){
	return dump::c($this->_var, null, true);
	}#m strToConsole

	public function strToFile($format = null){
	return dump::log($this->_var, false, true);
	}#m strToFile

	public function strToWeb($format = null){
	return dump::w($this->_var, false, true);
	}#m strToWeb

	public function strToPrint($format = null){
	return __outExtraData__common_implementation::strToPrint($this, $format);
	}#m strToPrint

	public function strByOutType($type, $format = null){
	return __outExtraData__common_implementation::strByOutType($this, $type, $format);
	}#m strByOutType
}#c commonOutExtraData
?>