<?
/**
* Null - filter. Return value "AS IS".
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2010-11-18
*
* @changelog
*	* 2010-11-18 13:43 ver 1.0
*	- Initial version.
**/

/**
* Null - filter. Return value "AS IS".
**/
class settings_filter_null extends settings_filter_base{
	/**
	* Only one argument required.
	**/
	public function __construct($propName){
	parent::__construct($propName, null);
	}#__c

	public function apply(&$name, &$value){
	return null;
	}#m apply
}#c settings_filter_null
?>