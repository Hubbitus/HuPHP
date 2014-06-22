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

class YML_offer_attributes_artisttitle extends YML_offer_attributes{
	//Defaults
	protected $__SETS = array(
		'type' => 'artist.title'
		,'available'	=> true
	);
}#c YML_offer_attributes_artisttitle

/**
* YML audio-video offer.
**/
class YML_offer_artisttitle extends YML_offer{
	// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
	public $properties_addon = array(
		// artist?, title, year?, media?, starring?, director?, originalName?, country?
		'artist'		//? Исполнитель
		,'title'		// Наименование
		,'year'		//? Год
		,'media'		//? Носитель
		,'starring'	//? Актеры
		,'director'	//? Режиссер
		,'originalName'//? Оригинальное наименование
		,'country'	//? Страна
	);

	public function __construct(array $array, YML_offer_attributes_artisttitle $props, DOMNode $currencies, DOMNode $categories = null){
		$this->nesting();

		parent::__construct($array, $props, $currencies, $categories);
	}#__c
}#c YML_offer_artisttitle
?>