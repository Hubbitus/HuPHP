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

class YML_offer_attributes_book extends YML_offer_attributes{
//Defaults
protected $__SETS = array(
	'type' => 'book'
	,'available'	=> true
);
}#c YML_offer_attributes_book

class YML_offer_book extends YML_offer{
// As we emulate Object structure, we can't just add properties to parent set... So, we add it in constructor.
public $properties_addon = array(
// author?, name, publisher?, series?, year?, ISBN?, volume?, part?, language?, binding?, page_extent?, table_of_contents?
	'author'		#? Автор произведения
	,'name'		# Наименование произведения
	,'publisher'	#? Издательство
	,'series'		#? Серия
	,'year'		#? Год издания
	,'ISBN'		#? Код книги, если их несколько, то указываются через запятую.
// Present in description, but not allowed by DTD
//	,'description'	# Аннотация к книге.
	,'volume'		#? Количество томов.
	,'part'		#? Номер тома.
	,'language'	#? Язык произведения.
	,'binding'	#? Переплет.
	,'page_extent'	#? Количествово страниц в книге, должно быть целым положиельным числом.
	,'table_of_contents'	#? Оглавление. Выводится информация о наименованиях произведений, если это сборник рассказов или стихов.
);

	public function __construct(array $array, YML_offer_attributes_book $props, DOMNode $currencies, DOMNode $categories){
	$this->nesting();

	$this->addFilterSet(new settings_filter_base('page_extent', array($this, 'filter_set__check_page_extent')));

	parent::__construct($array, $props, $currencies, $categories);
	}#__c

	/**
	* Filter: Check on set what number of pages integer positive number.
	*
	* @Throws(YML_offer_exception_constraint)
	**/
	public function filter_set__check_page_extent($name, &$val){
		if ($val < 0 or (int)$val != $val) throw new YML_offer_exception_constraint('number of pages must be integer positive number.');
	return $val;
	}#m filter_check_page_extent
}#c YML_offer_book
?>