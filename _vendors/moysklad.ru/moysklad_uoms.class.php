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
* Moysklad element uoms.
**/
class moysklad_uoms extends moysklad_element_base{
	function getName(){
		return 'uoms';
	}#m getName
}#c moysklad_uoms

/**
* Moysklad element script.
**/
class moysklad_uom extends moysklad_element_base{
	/*
	<uom name="шт" updatedBy="admin@hubbitus" updated="2010-02-26T23:06:04.651+03:00" changeMode="NONE" readMode="PARENT">
		<id>tVD4k03EhnKBPbeXSY9RW2</id>
		<version>0</version>
		<company>hubbitus</company>
		<code>796</code>
		<description>Демонстрационные данные - начало работы с МоимСкладом</description>
	</uom>
	*/
	// Each attributes shoud be listed here
	protected $attributes_defaults = array(
		'name'		=> 'шт'
		,'updatedBy'	=> '' // "admin@hubbitus"
		,'updated'		=> null // "2010-02-26T23:06:04.958+03:00" <- date('c') by default
		,'changeMode'	=> 'NONE'
		,'readMode'	=> 'PARENT'
	);
	protected $elements_defaults = array(
		'id'			=> null
		,'version'	=> 0
		,'company'	=> null
		,'code'		=> null
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
	}#__c

	function getName() {
		return 'uom';
	}#m getName
}#c moysklad_uom
?>