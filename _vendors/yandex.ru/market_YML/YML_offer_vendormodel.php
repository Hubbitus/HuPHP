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

class YML_offer_attributes_vendormodel extends YML_offer_attributes{
//Defaults
protected $__SETS = array(
	'type' => 'vendor.model'
	,'available'	=> true
);
}#c YML_offer_attributes_vendormodel

class YML_offer_vendormodel extends YML_offer{
// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
public $properties_addon = array(
// typePrefix?, vendor, vendorCode?, model, (provider, tarifplan?)?
	'typePrefix'	#? Группа товаров \ категория.
	,'vendor'		# Производитель
	,'vendorCode'	#? Код товара (указывается код производителя)
	,'model'		# Модель

	,'provider'	#?
	,'tarifplan'	#?

/*??? Не опнятно, послал письмо. Нету этого в DTD
	,'name'		# Наименование товарного предложения
	,'delivery'	# Элемент, обозначающий возможность доставить соответствующий товар. "false" данный товар не может быть доставлен("самовывоз"). "true" товар доставляется на условиях, которые указываются в партнерском интерфейсе http://partner.market.yandex.ru на странице "редактирование".
	,'description'	# Описание товарного предложения
	,'available'	# Статус доступности товара - в наличии/на заказ.
		# available="false" - товарное предложение на заказ. Магазин готов осуществить поставку товара на указанных условиях в течение месяца (срок может быть больше для товаров, которые всеми участниками рынка поставляются только на заказ).. Те товарные предложения, на которые заказы не принимаются, не должны выгружаться в Яндекс.Маркет.
		# available="true" - товарное предложение в наличии. Магазин готов сразу договариваться с покупателем о доставке товара
		# Более точное описание можно посмотреть в требованиях к рекламным Материалам.
 */
);

	public function __construct(array $array, YML_offer_attributes_vendormodel $props, DOMNode $currencies, DOMNode $categories){
	$this->nesting();

	parent::__construct($array, $props, $currencies, $categories);
	}#__c
}#c YML_offer_vendormodel
?>