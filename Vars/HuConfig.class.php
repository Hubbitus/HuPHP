<?
/**
* Routine tasks to made easy OOP.
*
* @package Vars
* @subpackage Classes
* @version 0.1
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-01 22:12 ver 0.1
*	- Initial version
**/

//It used for __autoload, so, we must directly prowide dependencies here
include_once('Settings/settings_check.php');
include_once('Vars/HuArray.php');

/**
* Class to provide easy access to $GLOBALS['__CONFIG'] variables.
* Intended use with Singleton class as:
* @example single::def('HuConfig')->config_value
**/
class HuConfig extends settings_check{
	function __construct() {
	parent::__construct(array_keys($GLOBALS['__CONFIG']), $GLOBALS['__CONFIG']);
	}#__c

	/**
	* As __get before.
	* Now {@see __get()} reimplemented to return HuArray instead of raw arrays
	*
	* @param string	$varname
	* @return &mixed
	**/
	public function &getRaw($varname){
	return $this->getProperty($varname);
	}#getRaw

	/**
	* For more comfort access in config fields without temporary variables like:
	* Single::def('HuConfig')->test->first
	*
	* @param string	$varname
	* @return &Object(HuArray)
	**/
	public function &__get($varname){
	$ret =& $this->getProperty($varname);
		if (is_array($ret)){
		$ret = new HuArray($ret); //Replace original on the fly
		return $ret;
		}
		else return $ret;
	}

	/**
	* Reimplement as initial, only return value by reference
	* @inheritdoc
	**/
	public function &getProperty($name){
	return $this->__SETS[$this->checkNamePossible(REQUIRED_NOT_NULL($name), __METHOD__)];
	}#m getProperty
}#c

/**
* Short alias to Single::def('config'). In case of we can-t define constant like:
* define('CONF', Single::def('config'));
* In this case got error: PHP Warning:  Constants may only evaluate to scalar values
* We can do that as variable like $CONF, but meantime it is not convenient in functions/methods:
* we must use global $CONF; first, or also very long $GLOBALS['CONF']
*
* So, choose function aliasing. Now we can invoke it instead of
**/
function &CONF(){
return Single::def('HuConfig');
}

/**
* @example
* dump::a(Single::def('HuConfig')->test);
* dump::a(Single::def('HuConfig')->test->First);
* dump::a(Single::def('HuConfig')->test->Second);
* Single::def('HuConfig')->test->Second = 'Another text';
* dump::a(Single::def('HuConfig')->test->Second);
* CONF()->test->Second = 'Yet ANOTHER Another text';
* dump::a(CONF()->test->Second);
* dump::a(Single::def('HuConfig')->test);
**/
?>
