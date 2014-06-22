<?
/**
* Ignore - filter. Ignore all value and always return null.
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2010-11-18 13:43
**/

/**
* Ignore - filter. Ignore all value and always return null.
**/
class settings_filter_ignore extends settings_filter_base{
	public function __construct($propName){
	parent::__construct($propName, null);
	}#__c

	public function apply(&$name, &$value){
	$name = null;
	return null;
	}#m apply
}#c settings_filter_ignore
?>