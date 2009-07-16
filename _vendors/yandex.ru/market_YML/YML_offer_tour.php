<?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
*
* @changelog
*	* 2009-06-30 17:21 ver 1.0
*	- Initial version.
**/

class YML_offer_attributes_tour extends YML_offer_attributes{
//Defaults
protected $__SETS = array(
	'type' => 'artist.title'
	,'available'	=> true
);
}#c YML_offer_attributes_tour

/**
* YML tour offer.
**/
class YML_offer_tour extends YML_offer{
// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
public $properties_addon = array(
// worldRegion?, country?, region?, days, dataTour*, name, hotel_stars?, room?, meal?, included, transport, price_min?, price_max?, options?
	'worldRegion'	#? Часть света
	,'country'	#? Страна
	,'region'		#? Курорт или город
	,'days'		# Количество дней тура
	,'dataTour'	#* Даты заездов
	,'name'		# Название отеля (в некоторых случаях наименование тура)
	,'hotel_stars'	#? Звезды S отеля 5*****
	,'room'		#? Тип комнаты (SNG, DBL......)
	,'meal'		#? Тип питания (All, HB......)
	,'included'	# Что включено в стоимость тура
	,'transport'	# Транспорт
	// Absent in documentation, but in DTD:
	,'price_min'	#?
	,'price_max'	#?
	,'options'	#?
//In parent:	,'description'	# Описание тура
);

	public function __construct(array $array, YML_offer_attributes_tour $props, DOMNode $currencies, DOMNode $categories){
	$this->nesting();

	parent::__construct($array, $props, $currencies, $categories);
	}#__c
}#c YML_offer_tour
?>