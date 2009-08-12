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
include_once('macroses/REQUIRED_VAR.php');

class YML_exception extends BaseException{};
class YML_exception_absentElement extends YML_exception{};

/**
* Yandex-market class realisation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @author pasha
* @created 2009-06-23
* @copyright 2009 Pavael Alexeev Aka Pahan-Hubbitus
**/
class YML{
private $dom_;		// Main DOMDocument
private $xpath_;	// DOMXpath object to perfom any queries

	public function __construct(){
	// DTD. http://www.php.net/manual/en/book.dom.php#78929
	$this->dom_ = DOMImplementation::createDocument('', '', DOMImplementation::createDocumentType('yml_catalog', '', 'shop.dtd'));
	$this->dom_->encoding = 'UTF-8';
	$this->dom_->validateOnParse = true;
	$this->xpath_ = new DOMXPath($this->dom_);

	$yml_catalog = $this->dom_->appendChild($this->dom_->createElement('yml_catalog'));
	$yml_catalog->setAttribute('date', date('Y-m-d H:i'));
	}#m __construct

	/**
	* Add <shop> to document
	*
	* @param	array	$shop Must contain fields: 'name', 'company', 'url'
	* @return	&$this;
	* @Throws(VariableRequired)
	**/
	public function &addShop(array $shop){
	$sh = $this->getYml_catalog()->appendChild($this->dom_->createElement('shop'));
		foreach (array('name', 'company', 'url') as $item){
		$sh->appendChild($this->dom_->createElement($item, REQUIRED_VAR($shop[$item])));
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
	$shop = $this->xpath_->query('//shop');
		if($shop->length != 1){
		throw new YML_exception_absentElement('You must add element "shop" first!');
		}
	$currencies = $shop->item(0)->appendChild($this->dom_->createElement('currencies'));
		foreach (REQUIRED_VAR($curs) as $id => $cur){
		$currency = $currencies->appendChild($this->dom_->createElement('currency'));
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
	*
	* @return	&$this
	**/
	public function &addCategories(array $cats){
		foreach (REQUIRED_VAR($cats) as $id => $cat){
		$this->addCategory( $cat + array('id' => $id) );
		}
	return $this;
	}#m addCategories

	/**
	* Add categories into document
	*
	* @param	array	$cat Array which represent category to add.
	*  example:
	*	// If no 'parentId' - root category
	*	array(
	*		'id'			=> 3
	*		'value'		=> 'Детективы'
	*		,'parentId'	=> '1'
	*	)
	*
	* @return	&$this
	**/
	public function &addCategory(array $cat){
	$shop = $this->xpath_->query('//shop');
		if($shop->length != 1){
		throw new YML_exception_absentElement('You must add element "shop" first!');
		}

	$categories = $this->xpath_->query('//categories');
		if($categories->length != 1){ //Create on demand
		$categories = $shop->item(0)->appendChild($this->dom_->createElement('categories'));
		}
		else{
		$categories = $categories->item(0);
		}
	
	$category = $categories->appendChild($this->dom_->createElement('category', $cat['value']));
	$category->setAttribute('id', $cat['id']);
		if (!empty($cat['parentId'])) $category->setAttribute('parentId', $cat['parentId']);
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
	$offers = $this->xpath_->query('//offers');
		if ($offers->length < 1){ //
		$shop = $this->xpath_->query('//shop')->item(0);
		return $shop->appendChild($this->dom_->createElement('offers'));
		}
		else{
		return $offers->item(0);
		}
	}#m getOffers

	/**
	* Return <yml_catalog> DOM element.
	*
	* @return	Object(DOMElement)
	**/
	public function getYml_catalog(){
	$currs = $this->xpath_->query('//yml_catalog');
		if ($currs->length < 1){
		throw new YML_exception_absentElement('<yml_catalog> absent!');
		}
	return $currs->item(0);
	}#m getYml_catalog

	/**
	* Return <currencies> DOM element.
	*
	* @return	Object(DOMElement)
	**/
	public function getCurrencies(){
	$currs = $this->xpath_->query('//currencies');
		if ($currs->length < 1){
		throw new YML_exception_absentElement('<currencies> absent!');
		}
	return $currs->item(0);
	}#m getCurrencies

	/**
	* Return <categories> DOM element.
	*
	* @return	Object(DOMElement)
	**/
	public function getCategories(){
	$cats = $this->xpath_->query('//categories');
		if ($cats->length < 1){
		throw new YML_exception_absentElement('<categories> absent!');
		}
	return $cats->item(0);
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
}#c YML