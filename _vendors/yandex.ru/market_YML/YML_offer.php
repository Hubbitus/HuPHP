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
*
*	* 2009-09-29 10:38 ver 1.0 to 1.1
*	- Add isValid() method
*	- Make Categories check is optional.
**/
include_once('macroses/REQUIRED_VAR.php');

class YML_offer_exception extends BaseException{};
class YML_offer_exception_constraint extends YML_offer_exception{};

/**
* YML offer attributes
**/
class YML_offer_attributes extends settings_filter{
public $properties = array(
	'id'
	,'type'
	,'bid'
	,'cbid'
	,'available'
);

public function __construct($array){
$this->addFilterGet(new settings_filter_base('available', array($this, 'filter_get__text_boolean')));
parent::__construct($this->properties, $array);
}

/**
* Fields-properties from DTD.
*	Common-prefix-part: url, buyurl?, price, wprice?, currencyId, xCategory?, categoryId+, picture?, delivery?, deliveryIncluded?, orderingTime?,
*	Common-suffix-part: aliases?, additional*, description?, sales_notes?, promo?, manufacturer_warranty?, country_of_origin?, downloadable?
*
*vendor.model
*	typePrefix?, vendor, vendorCode?, model, (provider, tarifplan?)?
*
*Book:
*	author?, name, publisher?, series?, year?, ISBN?, volume?, part?, language?, binding?, page_extent?, table_of_contents?
*
*AudioBook:
*	author?, name, publisher?, series?, year?, ISBN?, volume?, part?, language?, table_of_contents?, performed_by?, perfomace_type?, storage?, format?, recording_lenght?
*
*artist.title:
*	artist?, title, year?, media?, starring?, director?, originalName?, country?
*
*tour:
*	worldRegion?, country?, region?, days, dataTour*, name, hotel_stars?, room?, meal?, included, transport, price_min?, price_max?, options?
*
*event-ticket:
*	name, place, hall?, hall_part?, date, is_premiere?, is_kids?
*
*generic(not in description, but in DTD):
*	name, vendor?,vendorCode?
*/

/**
* Represent boolean values as text true/false
*
* @param	string	$name
* @param	boolean	$value
* @return string
**/
public function filter_get__text_boolean(&$name, &$value){
$value ? $value = 'true' : $value = 'false';
}#m filter_get__text_boolean

//Defaults
/* MUST BE DEFINED in childs
protected $__SETS = array(
	'type' => 'vendor.model' // vendor.model | book | artist.title | tour | ticket | event-ticket
	,'available'	=> true
);
**/
}#c YML_offer_attributes

/**
* YML yandex offer
*
* This is base (type vendor.model) offer.
**/
abstract class YML_offer extends settings_filter{
const NESTING_PLACEHOLDER = '<<==NESTING==>>';

public $properties = array(
	// Common fields
	'url'		# URL-адрес страницы товара
	,'price'		# Цена, по которой данный товар можно приобрести.Цена товарного предложения округляеся и выводится в зависимости от настроек пользователя.
	,'currencyId'	# Идентификатор валюты товара (RUR, USD, UAH). Для корректного отображения цены в национальной валюте, необходимо использовать идентификатор (например, UAH) с соответствующим значением цены.
	,'categoryId'	#+ Идентификатор категории товара (целое число не более 18 знаков). Товарное предложение может принадлежать только одной категории
	,'picture'	#? Ссылка на картинку соответствующего товарного предложения. Недопустимо давать ссылку на "заглушку", т.е. на картинку где написано "картинка отсутствует" или на логотип магазина
	,'delivery'	#?
	,'deliveryIncluded'	#?
	,'orderingTime'	#?

	,self::NESTING_PLACEHOLDER

	,'aliases'	#?
	,'additional'	#*
	,'description'	#?
	,'sales_notes'	#? Элемент, предназначенный для того, чтобы показать пользователям, чем отличается данный товар от других, или для описания акций магазина (кроме скидок). Допустимая длина текста в элементе - 50 символов.
	,'promo'		#?
	,'manufacturer_warranty'	#? Элемент предназначен для отметки товаров, имеющих официальную гарантию производителя.
	,'country_of_origin'	#? Элемент предназначен для указания страны производства товара.
	,'downloadable'	#? Элемент предназначен обозначения товара, который можно скачать.

	// INNER, static
	,'__props'		//
	,'__currencies'	//
	,'__categories'	//
	,'__xpath'		//
);
protected $static_settings = array('__props', '__currencies', '__categories', '__xpath');

	/**
	* Constructor YML_offer
	*
	* @param	array	$array	Data to construct from.
	* @param	Object(YML_offer_attributes)	Attributes of constructed object.
	* @param	Object(DOMNode)	$currencies	Mandatory. To check constraints.
	* @param	Object(DOMNode)|null	$categories	Optional. If present, constraints will be checked.
	**/
	public function __construct(array $array, YML_offer_attributes $props, DOMNode $currencies, DOMNode $categories = null){
	$this->__props = $props;

	// SetUp filters which represents common checks:
	// CurrencyID in allowed
	$this->addFilterSet(new settings_filter_base('currencyId', array($this, 'filter_set__check_currencyId')));
	// categoryId in allowed
	$this->addFilterSet(new settings_filter_base('categoryId', array($this, 'filter_set__check_categoryId')));

	// Picture can't be empty and can't be logo or something. Check non-empty only
	//	(How I can automatically check what image is not image with "No foto" text only?).
	$this->addFilterSet(new settings_filter_base('picture', array($this, 'filter_set__check_picture')));

	$this->__currencies = $currencies; // To check constraints
	$this->__categories = $categories;
	$this->__xpath = new DOMXPath($this->__currencies->ownerDocument);
	parent::__construct($this->properties, $array);
	}#__c

	/**
	* Create result XML
	*
	* @return	Object(DOMElement)
	**/
	public function getXML(){
	/**
	* We NEED document instead of doing something like: new DOMElement
	* Please see: http://forums.codewalkers.com/php-coding-7/problem-with-dom-710474.html
	**/
	$res = new DOMDocument;
	$res->substituteEntities = true;
	$offer = $res->appendChild($res->createElement('offer'));

		foreach(array('id') as $item)		//Requred attributes
		$offer->setAttribute($item, REQUIRED_VAR($this->__props->$item));
		// Please note, type also optional. {@see YML_offer_generic}
		foreach(array('bid', 'cbid', 'available', 'type') as $item)		//Optional attributes
			if ($this->__props->$item) $offer->setAttribute($item, $this->__props->$item);
		foreach($this->getRegularKeys() as $itemKey){	//All defined subelements
			if ($this->{$itemKey}){
			//$offer->appendChild($res->createElement($itemKey, htmlentities($this->{$itemKey}, ENT_COMPAT, 'UTF-8', false)));
			//$offer->appendChild($res->createElement($itemKey, $this->{$itemKey}));
			/**
			* @internal
			* Due to the Bugs: http://bugs.php.net/bug.php?id=31191, http://bugs.php.net/bug.php?id=48109, http://bugs.php.net/bug.php?id=40105
			* we can't use short form $res->createElement($tag, $tagValue);
			**/
			$offer->appendChild($res->createElement($itemKey))->appendChild($res->createTextNode($this->{$itemKey}));
			}
		}
	return $offer;
	}#m saveXML

	/**
	* Filter: Check on set what currencyId in allowed currencies
	*
	* @param	string	$name
	* @param	mixed	$val
	* @return	mixed	Modified value.
	* @Throws(YML_offer_exception_constraint)
	**/
	public function filter_set__check_currencyId($name, $val){
		if ($this->__xpath->query('currency[@id="' . $val . '"]', $this->__currencies)->length < 1) throw new YML_offer_exception_constraint("$val currency is not allowed in current configuration");
	return $val;
	}#m filter_check_currencyId

	/**
	* Filter: Check on set what categoryId in allowed currencies
	*
	* @param	string	$name
	* @param	mixed	$val
	* @return	mixed	Modified value.
	* @Throws(YML_offer_exception_constraint)
	**/
	public function filter_set__check_categoryId($name, $val){
		if ( $this->__categories and $this->__xpath->query('category[@id="' . $val . '"]', $this->__categories)->length < 1 ) throw new YML_offer_exception_constraint("$val category is not allowed in current configuration");
	return $val;
	}#m filter_check_categoryId

	/**
	* Filter: Picture can't be empty and can't be logo or something. Check non-empty only
	* (How I can automatically check what image is not image with "No foto" text only?).
	*
	* @param	string	$name
	* @param	mixed	$val
	* @return	mixed	Modified value.
	* @Throws(YML_offer_exception_constraint)
	**/
	public function filter_set__check_picture($name, $val){
		if (empty($val)) throw new YML_offer_exception_constraint("Picture can't be empty. Logo and 'No foto' images also unacceptable");
	return $val;
	}#m filter_check_picture

	/**
	* Reimplement to provide correct order of elements, which must be in XML
	*
	* @return	array Array of keys-properties in proper order.
	**/
	public function getRegularKeys(){
	return array_diff($this->properties, $this->static_settings);
	}#m getRegularKeys

	/**
	* Emulate nesting.
	*
	* In our case order of fields have fatal important. So, we can't just add fields.
	*	We must inject it in proper place.
	* DTD had logical structure of fields like: common prefixed fields for all offers,
	*	and common suffixed fields for all offers. And some part of main fields related
	*	to the type of offer. So, this main fields, which must be defined in child
	*	$this->properties_addon property we insert instead of placeholder self::NESTING_PLACEHOLDER
	**/
	public function nesting(){
	array_splice($this->properties, array_search(self::NESTING_PLACEHOLDER, $this->properties), 1, $this->properties_addon);
	}#m nesting

	/**
	* Common check of "Welness". Child may reimplement its base checks (f.e. to allow Categories filtering)
	*
	* @return	boolean
	**/
	public function isValid(){
		if ($this->price <= 0) return false; //Only real products
		if ('/default_image.gif' == $this->picture) return false; //Only with images
		if (!$this->vendor) return false;
	return true;
	}#m isValid
}#c YML_offer
?>