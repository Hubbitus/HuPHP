<?
/**
* Extends settings_check to allow apply get/set filters.
* It may be constraints check (f.e. check and throw exception on error),
*	and/or any modifications like clear user input, convert formats and etc.
*
* @package settings
* @subpackage settings_filter
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @version 1.0
* @created 2009-06-29
*
* @changelog
**/

/**
* Entity of "filter". In most cases only calling $callback on provided pair $name/$value in method apply().
* But it is quite powerful. Childs of this basic class may provide any service such as: non-deterministic
*	state-based results, based on time or amount of call results (f.e. Apply filter only 3 times!?), depend
*	on any global environment etc...
**/
class settings_filter_base{
public $propName;
private $callback_;

	public function __construct($propName, $callback){
	$this->propName = $propName;
	$this->callback_ = $callback;
	}#__c

	/**
	* In simplest variant - just direct apply provided callback.
	**/
	public function apply($name, $value){
	return call_user_func($this->callback_, $name, $value);
	}#m apply
}#c settings_filter_base

/**
* Null - filter. Return value "AS IS".
**/
class settings_filter_null extends settings_filter_base{
	function &apply($name, $value){
	return $value;
	}#m apply
}#c settings_filter_null
?>