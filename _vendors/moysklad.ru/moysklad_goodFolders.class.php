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

include_once ('macroses/EMPTY_callback.php');

/**
* Moysklad element goodFolders.
**/
class moysklad_goodFolders extends moysklad_element_base{
	function getName(){
		return 'goodFolders';
	}#m getName
}#c moysklad_goodFolders

/**
* Moysklad element script.
**/
class moysklad_goodFolder extends moysklad_element_base {
	// Each attributes shoud be listed here
	protected $attributes_defaults = array(
		'productCode'	=> ''
		,'vat'		=> '18'
		,'name'		=> ''
		,'updatedBy'	=> 'admin@hubbitus'
		,'updated'	=> null //If null propogated by date('c') in constructor
		,'changeMode'	=> 'NONE'
		,'readMode'	=> 'ALL'
	);
	protected $elements_defaults = array(
		'id'			=> null
		,'version'	=> 0
		,'company'	=> null
		,'description'	=> null
	);

	/**
	*
	* @param array $value Mixed array ofdescedance elements and attributes:
	* @TODO add data type checks and requirements
	**/
	function __construct(array $value){
		parent::__construct('');

		// PHP does not allow dinamic (non constants) initializations
		if(is_null($this->attributes_defaults['updated'])) $this->attributes_defaults['updated'] = date('c');

		foreach($this->attributes_defaults as $attr => $val){
			// isset id not function but language construction and can't be invoked directly
			$this->dom->setAttribute($attr, EMPTY_callback(create_function('$v', 'return isset($v);'), @$value[$attr], $val));
		}
		foreach ($this->elements_defaults as $elem => $val){
			$this->dom->appendChild(new DOMElement($elem, EMPTY_callback(create_function('$v', 'return isset($v);'), @$value[$elem], $val)));
		}
	}#m  __construct

	function getName(){
		return 'goodFolder';
	}#m getName
}#c moysklad_goodFolder
?>