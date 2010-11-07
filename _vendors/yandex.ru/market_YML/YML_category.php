<?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1.1
*
* @changelog
*	* 2009-08-24 16:06 ver 1.0
*	- Initial version.
*
*	* 2010-08-10 13:14 ver 1.0 to 1.1
*	- Make _value_ and id mandatory and check in getXML method.
*
*	* 2010-11-07 23:36 ver 1.1 to 1.1.1
*	- Replace REQUIRED_VAR by REQUIRED_NOT_NULL to allow 0 values.
**/

include_once('macroses/REQUIRED_NOT_NULL.php');

/**
* Create category DOMNode for YML.
**/
class YML_category extends settings_filter{
public $properties = array(
	'id'
	,'parentId'
	,'_value_'	// This is value of element itself.
);

	public function __construct(array $arr){
	$this->setSettingsArray(REQUIRED_NOT_NULL($arr));
	}#__c

	/**
	* Return DOMNode of category.
	* @param
	**/
	public function getXML(DOMDocument &$dom){
	REQUIRED_NOT_NULL($this->_value_, '_value_');
	REQUIRED_NOT_NULL($this->id, 'id');
	$category = $dom->createElement('category');
	/**
	* @internal
	* Due to the Bugs: http://bugs.php.net/bug.php?id=31191, http://bugs.php.net/bug.php?id=48109, http://bugs.php.net/bug.php?id=40105
	* we can't use short form $res->createElement($tag, $tagValue);
	**/
	$category->appendChild($dom->createTextNode($this->_value_));
		foreach($this->getRegularKeys() as $itemKey){//All defined subelements
			if (isset($this->$itemKey) and '_value_' != $itemKey) $category->setAttribute($itemKey, $this->$itemKey);
		}
	return $category;
	}#m getXML
}#c YML_category
?>