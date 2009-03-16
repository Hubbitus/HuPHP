<?
/**
* Debug and backtrace toolkit.
*
* Class to provide easy wrapper aroun HuFormat for anywhere usage.
*
* @package Debug
* @subpackage HuLOG
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-16 19:06 ver 1.0
*	- Initial version.
**/

include_once('Debug/HuFormat.php');
include_once('Vars/commonOutExtraData.php');

/**
* Class to provide easy wrapper aroun HuFormat for anywhere usage.
**/
class huFormatOutExtraData extends commonOutExtraData{
protected $format;	//Array of format
protected /* HuFormat */ $_format;
	/**
	* Constructor.
	*
	* @param	mixed	$var Var to output with provided format.
	* @param	array	$format	Format how output $vavr. Must contain 3 elements:
	*	'FORMAT_CONSOLE', 'FORMAT_WEB', 'FORMAT_FILE' each represent according
	*	format (See class {@see HuFormat} for more details).
	**/
	function  __construct($var, array $format){
	$this->format = $format;
	$this->_format = new HuFormat(null, $var);
	}#__c

	/**
	*@inheritdoc
	**/
	public function strToConsole($format = null){
	return $this->_format->setFormat(EMPTY_VAR($format, $this->format['FORMAT_CONSOLE']))->getString();
	}#m strToConsole

	/**
	*@inheritdoc
	**/
	public function strToFile($format = null){
	return $this->_format->setFormat(EMPTY_VAR($format, $this->format['FORMAT_FILE']))->getString();
	}#m strToFile

	/**
	*@inheritdoc
	**/
	public function strToWeb($format = null){
	return $this->_format->setFormat(EMPTY_VAR($format, $this->format['FORMAT_WEB']))->getString();
	}#m strToWeb
}#c huFormatOutExtraData
?>