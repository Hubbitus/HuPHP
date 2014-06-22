<?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-30 17:21
**/

/**
* This is generic type, mandatory only "name" elememnt. Additionally Yandex support name it as "name".
* In this type attribute "type" in element <offer> must be ommited.
* What is more intresting, it is present in DTD, but description do not describes it separately and
* present only devil mesh of it with YML_offer_vendormodel.
* Some explanation got from Yandex-support stuff - Andrey Tikhonov.
**/
class YML_offer_attributes_generic extends YML_offer_attributes{
	//Defaults
	protected $__SETS = array(
		'type' => null
		,'available'	=> true
	);
}#c YML_offer_attributes_generic

class YML_offer_generic extends YML_offer{
	// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
	public $properties_addon = array(
		// name, vendor?,vendorCode?
		'name'
		,'vendor'		//?
		,'vendorCode'	//?
	);

	public function __construct(array $array, YML_offer_attributes_generic $props, DOMNode $currencies, DOMNode $categories = null){
		$this->nesting();

		parent::__construct($array, $props, $currencies, $categories);
	}#__c
}#c YML_offer_generic
?>