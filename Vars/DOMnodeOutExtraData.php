<?
/**
* Debug and backtrace toolkit.
*
* Class to provide easy wrapper aroun HuFormat for anywhere usage.
*
* @package Debug
* @subpackage HuLOG
* @version 1.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-05-20 19:10
**/

/**
* Class to provide easy wrapper to dump DOMElement (and DOMnode possibly), which default dump seems like:
* object(DOMElement)#97 (0) {
* }
* This wrapper put it into DOMDocument, and autput it as formated XML. For output standard family of dump::* methods used.
*
* @uses commonOutExtraData
* @uses dump
**/
class DOMnodeOutExtraData extends commonOutExtraData{
	protected /* DOMDocument */ $_var;

	/**
	* Constructor.
	*
	* @param	Object(DOMNode)	$var Var to output with provided format.
	* @param	string='utf-8'	$format	Format how output $vavr. Must contain 3 elements:
	*	'FORMAT_CONSOLE', 'FORMAT_WEB', 'FORMAT_FILE' each represent according
	*	format (See class {@see HuFormat} for more details).
	**/
	function  __construct(DOMNode $var, $encoding = 'utf-8'){
		$this->_var = new DOMDocument('1.0', $encoding); // DOMDocument NEEDED ot import into it nodes, it also NEEDED to export result asXML...
		$this->_var->appendChild($this->_var->importNode($var, true));
		$this->_var->preserveWhiteSpace = false;
		$this->_var->formatOutput = true;
	}#__c

	public function strToConsole($format = null){
		return dump::c(trim($this->_var->saveXML()), null, true);
	}#m strToConsole

	public function strToFile($format = null){
		return dump::log(trim($this->_var->saveXML()), false, true);
	}#m strToFile

	public function strToWeb($format = null){
		return dump::w(trim($this->_var->saveXML()), false, true);
	}#m strToWeb
}#c huFormatOutExtraData
?>