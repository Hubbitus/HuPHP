<?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-09-28 17:28
**/

/**
* Flat category structure - just parentId ignored.
**/
class YML_category_flat extends YML_category{
	public function __construct(array $arr){
	$this->addFilterSet(new settings_filter_ignore('parentId'));// Just ignore

	parent::__construct($arr);
	}#__c
}#c YML_category_flat
?>