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
* @version 1.2
* @created 2009-06-29
*
* @changelog
*	* 2009-09-29 13:55 ver 1.0 to 1.1
*	* Add standard filter settings_filter_ignore
*
*	* 2009-09-30 12:26 ver 1.1 to 1.2
*	- Now apply() pass $name and $value by reference, so, both may be changed. It introduce power but dangerouse.
**/

/**
* Entity of "filter". In most cases only calling $callback on provided pair references $name/$value in method apply().
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
	* $name and $value provided as refrence, so, user can change both as want.
	* It is usefull to jungle and add additional filters by name. F.e. set GET filter like "UC:name"
	* to return uppercase value of "name", o rename option on set time etc.
	*
	* @param	&mixed	$name	Reference to name of option.
	* @param	&mixed	$value	Reference to new value of option
	* @return	mixed	Returns what user callback return.
	**/
	public function apply(&$name, &$value){
	/*
	* call_user_func_array to pass reference, what is not allowed in call_user_func.
	* Solution found in man, see Example1 http://ru2.php.net/call_user_func
	**/
	return call_user_func_array( $this->callback_, array(&$name, &$value) );
	}#m apply
}#c settings_filter_base

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

/**
* Ignore - filter. Ignore all value and always return null
**/
class settings_filter_ignore extends settings_filter_base{
	public function __construct($propName){
	parent::__construct($propName, null);
	}#__c

	public function apply(&$name, &$value){
	$value &= null;
	return null;
	}#m apply
}#c settings_filter_ignore
?>