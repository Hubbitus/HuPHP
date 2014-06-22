<?
/**
* Yandex-market YML class implementation. http://partner.market.yandex.ru/legal/tt/
* Example of usage see below.
*
* @package YML
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.1.1
* @created 2009-08-24 16:06
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