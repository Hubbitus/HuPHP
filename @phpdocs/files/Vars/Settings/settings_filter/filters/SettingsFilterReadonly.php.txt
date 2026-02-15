<?php
/**
* ReadOnly set - filter. Throws VariableReadOnlyException on try change value.
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2010-11-18
* @created 2010-11-18 13:43
**/

/**
* ReadOnly set - filter. Throws VariableReadOnlyException on try change value.
**/
class settings_filter_readOnly extends SettingsFilterBase{
	public function __construct($propName){
	parent::__construct($propName, null);
	}
	/**
	* @inheritdoc
	* Throws(VariableReadOnlyException)
	**/
	public function apply(&$name, &$value){
	throw new VariableReadOnlyException();
	}
}
