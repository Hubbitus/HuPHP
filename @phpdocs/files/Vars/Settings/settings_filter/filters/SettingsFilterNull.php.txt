<?php
/**
* Null - filter. Return value "AS IS".
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2010-11-18 13:43
**/

/**
* Null - filter. Return value "AS IS".
**/
class SettingsFilterNull extends SettingsFilterBase{
	/**
	* Only one argument required.
	**/
	public function __construct($propName){
	parent::__construct($propName, null);
	}
	public function apply(&$name, &$value){
	return null;
	}
}
