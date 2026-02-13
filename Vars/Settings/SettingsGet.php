<?php
declare(strict_types=1);

/**
* It's Before declaration of VariableRequiredException may produce cycle of includes...
**/
include_once('macroses/REQUIRED_VAR.php');
include_once('macroses/REQUIRED_NOT_NULL.php');

/**
* Parent class for more useful using in parents who want be "customizable". Convenient nesting.
**/
class SettingsGet {
/** WARNING! Must be initialized in parents! **/
protected /* settings */ $_sets = null;

	/**
	* Overload to provide ref on settings object. So, settings will be changeable,
	* but can't be replaced settings object!
	*
	* @param <type> $name
	* @return	mixed
	**/
	public function &__get ($name){
		if ('settings' == $name) return $this->_sets;
	}
	/**
	* Return settings object
	*
	* @return	&Object(settings)
	**/
	public function &sets(){
		return $this->_sets;
	}
}
