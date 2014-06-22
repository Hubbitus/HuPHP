<?
/**
* moysklad.ru XML class implementation.
* http://www.moysklad.ru/home/42-connectors/183-xml/
* http://www.moysklad.ru/schema/exchange-1.1.0.xsd
*
* @package moysklad
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2010, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @example moysklad.example.php
* @created 2010-04-01 01:15
**/

/**
* Base class for most (all?) elements in moysklad.
**/
abstract class moysklad_element_base extends DOMElement{
	/**
	* We need it, because in current DOM implementation nor DOMelement can append childs
	* ( http://www.sitepoint.com/forums/showthread.php?t=199386 ),
	* nor even DOMDocumentFragment until both had not produced from DOMDocument (->createElement or ->createDocumentFragment)
	*
	* @var Object(DOMDocument)
	**/
	protected $domf_;

	/**
	* Main method for overload. Must return element name
	**/
	abstract function getName();

	public function __construct($value = '', $namespaceURI = ''){
		parent::__construct($this->getName(), $value, $namespaceURI);

		$this->domf_ = new DOMDocument;
		$this->domf_->appendChild($this);
		$this->domf_->registerNodeClass('DOMElement', __CLASS__);
		if (is_null($value)) $this->set_xsi_nil();
		// http://bugs.php.net/bug.php?id=51462
//		$GLOBALS['PHP_HACK_DOM_ELEMENT'][spl_object_hash($rootElement)] = $rootElement; //Note, reference is not enough :(
//		$GLOBALS['PHP_HACK_DOM_ELEMENT'][spl_object_hash($this)] = $this; //Note, reference is not enough :(
	}#__c

	/**
	* http://bugs.php.net/bug.php?id=51462
	**/
	public function __destruct() {
//	unset($GLOBALS['PHP_HACK_DOM_ELEMENT'][spl_object_hash($this)]);
	}#__destructor

	/**
	* Turn element into someting like: <workflow xsi:nil="true" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"/>
	*
	* @return	&$this
	**/
	public function &set_xsi_nil(){
		$this->domf_->documentElement->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
		$this->domf_->documentElement->setAttribute('xsi:nil', 'true');
		return $this;
	}#m set_xsi_nil

	/**
	* @var	DOMElement $dom
	**/
	function __get($name){
		if('dom' == $name) return $this->domf_->documentElement;
		else throw new moysklad_exception_absentElement("$name is not present");
	}#m getDOMElement

	/**
	* Get string representation of XML document.
	*
	* @param	array	$opts Array of options, which must be applyed to DOMDocument
	*					object first. As array of Key=>value. Nothing checked.
	* @return	string
	**/
	public function saveXML(array $opts = array( 'formatOutput' => true, 'encoding' => 'utf-8', 'preserveWhiteSpace' => true )){
		$dom = new DOMDocument('1.0'); // DOMDocument NEEDED ot import into it nodes, it also NEEDED to export result asXML...
		$dom->appendChild($dom->importNode($this->dome_, true));
		foreach ($opts as $opt => $val){
			$dom->{$opt} = $val;
		}
		return $dom->saveXML();
	}#m saveXML
}#c moysklad_element_base
?>