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
*
* @changelog
*	* 2010-04-01 01:15 ver 1.0
*	- Initial version.
**/

/**
* Moysklad element goods.
*
* @author Pavel Alexeev aka Pahan-Hubbitus
* @created 2010-04-01 01:15 ver 1.0
* @copyright 2010 Pavael Alexeev Aka Pahan-Hubbitus
**/
class moysklad_goods extends moysklad_element_base{
	function getName(){
	return 'goods';
	}#m getName
}#c moysklad_goods

/**
 * Moysklad element good.
 *
 * @author Pavel Alexeev aka Pahan-Hubbitus
 * @created 2010-04-01 16:47 ver 1.0
 * @copyright 2010 Pavael Alexeev aka Pahan-Hubbitus
 **/
class moysklad_good extends moysklad_element_base{
// Each attributes shoud be listed here
protected $attributes_defaults = array(
	'minimumBalance'	=> 0		#"20.0"
	,'buyPrice'		=> 0		# "0.0"
	,'isSerialTrackable'=> 'false'	# "false"
	,'salePrice'		=> null	# "1250.0"
	,'uomId'			=> null	# "tVD4k03EhnKBPbeXSY9RW2"
	,'vat'			=> 18	# "18"
	,'parentId'		=> null	# "c4rm8jR6izyZyVouh5yMw2" <- CategoryId!
	,'name'			=> null	# "Мыло душистое"
	,'updatedBy'		=> null	# "admin@hubbitus"
	,'updated'		=> null	# "2010-02-26T23:06:04.958+03:00" <- date('c') by default
	,'changeMode'		=> 'NONE'
	,'readMode'		=> 'ALL'
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
	}#m  __construct

	function getName() {
	return 'good';
	}#m getName
}#c moysklad_good