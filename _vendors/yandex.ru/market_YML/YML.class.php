<?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @example YML.example.php
* @version 1.1
*
* @changelog
*	* 2009-06-30 17:21 ver 1.0
*	- Initial version.
*
*	* 2010-08-11 11:57 ver 1.0 to 1.1
*	- Add caching of objects to do not do very slow XPAth queries each time.
**/
include_once('macroses/REQUIRED_VAR.php');

class YML_exception extends BaseException{};
class YML_exception_absentElement extends YML_exception{};

/**
* Yandex-market class implementation. http://partner.market.yandex.ru/legal/tt/
*
* @author pasha
* @created 2009-06-23
* @copyright 2009 Pavael Alexeev Aka Pahan-Hubbitus
**/
class YML{
private $dom_;		// Main DOMDocument
private $xpath_;	// DOMXpath object to perfom any queries

// Cache presents of elements in document to do not do amny times slo Xpath queries.
protected $cache_ = array(
	'yml_catalog'	=> null,
	'shop'		=> null,
	'currencies'	=> null,
	'categories'	=> null,
	'offers'	=> null,
);

	public function __construct(){
	# DTD. http://www.php.net/manual/en/book.dom.php#78929
	$this->dom_ = DOMImplementation::createDocument('', '', DOMImplementation::createDocumentType('yml_catalog', '', 'shops.dtd'));
	$this->dom_->encoding = 'UTF-8';
	$this->dom_->validateOnParse = true;
	$this->xpath_ = new DOMXPath($this->dom_);

	$this->cache_['yml_catalog'] = $this->dom_->appendChild($this->dom_->createElement('yml_catalog'));
	$this->cache_['yml_catalog']->setAttribute('date', date('Y-m-d H:i'));
	}#m __construct

	/**
	* Add <shop> to document
	*
	* @param	array	$shop Must contain fields: 'name', 'company', 'url'
	* @return	&$this;
	* @Throws(VariableRequired)
	**/
	public function &addShop(array $shop){
	$this->cache_['shop'] = $this->getYml_catalog()->appendChild($this->dom_->createElement('shop'));
		foreach (array('name', 'company', 'url') as $item){
		$this->cache_['shop']->appendChild($this->dom_->createElement($item, REQUIRED_VAR($shop[$item])));
		}
	return $this;
	}#m addShop

	/**
	* Add used currencies into document.
	*
	* @param	array	$curs Array of currencies to add. Format like (id and rate required!):
	* array(
	*	'RUR'	=> array(
	*		'rate' => 1
	*	)
	*	,'USD'	=> array(
	*		'rate' => '29.30'
	*	)
	*	,'EUR'	=> array(
	*		'rate' => 'CBRF'
	*		,'plus'=> 3
	*	)
	* )
	* @return	&$this
	**/
	public function &addCurrencies(array $curs){
	$this->cache_['currencies'] = $this->checkElementPresents('shop')->appendChild($this->dom_->createElement('currencies'));
		foreach (REQUIRED_VAR($curs) as $id => $cur){
		$currency = $this->cache_['currencies']->appendChild($this->dom_->createElement('currency'));
		$currency->setAttribute('id', $id);
			if(!empty($cur['rate'])) $currency->setAttribute('rate', $cur['rate']);
			if(!empty($cur['plus'])) $currency->setAttribute('plus', $cur['plus']);
		}
	return $this;
	}#m addCurrencies

	/**
	* Add categories into document
	*
	* @param	array	$cats Array of categories to add.
	* array(
	*	// id="1". If no 'parentId' - root category
	*	1 => array(
	*		'value'	=> 'Книги'
	*	)
	*	, //id="2"
	*	2 => array(
	*		'value'	=> 'Видео'
	*	)
	*	,
	*	3 => array(
	*		'value'	=> 'Детективы'
	*		,'parentId'	=> '1'
	*	)
	*	,
	*	4 => array(
	*		'value'	=> 'Боевики'
	*		,'parentId'	=> '1'
	* )
	* @param	string=YML_category $class What class create.
	*
	* @return	&$this
	**/
	public function &addCategories(array $cats, $class = 'YML_category'){
		foreach (REQUIRED_VAR($cats) as $id => $cat){
		$this->addCategory(  new $class( $cat + array('id' => $id) )  );
		}
	return $this;
	}#m addCategories

	/**
	* Add category into document
	*
	* @param	Object(YML_category)	$cat Category to add.
	*
	* @return	&$this
	**/
	public function &addCategory(YML_category $cat){
		if(! $this->checkElementPresents('categories')){ //Create on demand
		$this->cache_['categories'] = $this->checkElementPresents('shop', true)->appendChild($this->dom_->createElement('categories'));
		}

	$this->cache_['categories']->appendChild($cat->getXML($this->dom_));
	return $this;
	}#m addCategory

	/**
	* Add offer to shop offers. <offers> element will be created automatically -
	*	you do not need care of it.
	*
	* @param	Object(YML_offer)	$offer
	* @return	&$this
	**/
	public function &addOffer(YML_offer $offer){
	$offers = $this->getOffers();
	$offers->appendChild($this->dom_->importNode($offer->getXML(), true));
	return $this;
	}#m addOffer

	/**
	* Return reference to <offers> element. Create it, if still don't present.
	*
	* @return	&Object(DOMElement)
	**/
	private function getOffers(){
		if(! $this->checkElementPresents('offers')){ //Create on demand
		$this->cache_['offers'] = $this->checkElementPresents('shop', true)->appendChild($this->dom_->createElement('offers'));
		}
	return $this->cache_['offers'];
	}#m getOffers

	/**
	* Return <yml_catalog> DOM element.
	*
	* @return	&Object(DOMElement)
	**/
	public function &getYml_catalog(){
	return $this->checkElementPresents('yml_catalog', true);
	}#m getYml_catalog

	/**
	* Return <currencies> DOM element.
	*
	* @return	Object(DOMElement)
	**/
	public function getCurrencies(){
	return $this->checkElementPresents('currencies', true);
	}#m getCurrencies

	/**
	* Return <categories> DOM element.
	*
	* @return	Object(DOMElement)
	**/
	public function getCategories(){
	return $this->checkElementPresents('categories', true);
	}#m getCategories

	/**
	* Get string representation of XML document.
	*
	* @param	array	$opts Array of options, which must be applyed to DOMDocument
	*					object first. As array of Key=>value. Nothing checked.
	* @return	string
	**/
	public function saveXML(array $opts = array( 'formatOutput' => true )){
		foreach ($opts as $opt => $val){
		$this->dom_->{$opt} = $val;
		}
	return $this->dom_->saveXML();
	}#m saveXML

	/**
	* Return true if element was add in tree, false otherwise.
	* Made for caching purpose, because Xpath is very slow for such simple queries.
	* Return &$this, or throw exception, so it may be used in chain.
	*
	* @param	string	$name Name of requested item.
	* @param	boolean	$throw If true element required and exception throwed, otherwise returned reference to element or false.
	* @return	&DOMElement Requested node.
	* @Throws(YML_exception_absentElement)
	**/
	protected function &checkElementPresents($name, $throw = false){
		if($throw and ! $this->cache_[$name]){
		throw new YML_exception_absentElement("You must add element '$name' first!");
		}
		else return $this->cache_[$name];
	}#m checkElementPresents
}#c YML