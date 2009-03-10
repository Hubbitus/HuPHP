<?
/**
* Routine tasks to made easy OOP.
*
* @package Vars
* @subpackage Settings
* @version 0.4
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2009-03-01 22:12 ver 0.1
*	- Initial version
*
*	* 2009-03-06 15:29 ver 0.1 to 0.2
*	- Change include_once('Settings/settings.php'); to include_once('Vars/Settings/settings.php');
*
*	* 2009-03-09 05:31 ver 0.2 to 0.3
*	- Add optional parameter $className to CONF() function!
*	- @subpackage changed to Settings
*
*	* 2009-03-10 04:59 ver 0.3 to 0.4
*	- Add try to autoinclude config: 'includes/configs/' . $name . '.config.php' if it is untill not present in $GLOBALS['__CONFIG']
**/

//It used for __autoload, so, we must directly prowide dependencies here
include_once('Vars/Settings/settings_check.php');
include_once('Vars/HuArray.php');
include_once('System/OS.php'); // OS::is_includable
include_once('macroses/REQUIRED_VAR.php');

/**
* Class to provide easy access to $GLOBALS['__CONFIG'] variables.
* Intended use with Singleton class as:
* @example Single::def('HuConfig')->config_value
**/
class HuConfig extends settings_check{
private $_include_tryed = array();
	function __construct() {
	parent::__construct(array_keys($GLOBALS['__CONFIG']), $GLOBALS['__CONFIG']);
	}#__c

	/**
	* As __get before.
	* Now {@see __get()} reimplemented to return HuArray instead of raw arrays
	* Bee careful - after standard call (not raw) original Array value was replaced by HuArray!
	*
	* @param string	$varname
	* @return &mixed
	**/
	public function &getRaw($varname){
	return $this->getProperty($varname);
	}#m getRaw

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
	}#m __get

	/**
	* Reimplement as initial, only return value by reference
	* Also try include file 'includes/configs/' . $name . '.config.php' if it exist to find needed settings.
	* @inheritdoc
	**/
	public function &getProperty($name){	
		try{
		return $this->__SETS[$this->checkNamePossible(REQUIRED_NOT_NULL($name), __METHOD__)];
		}
		catch(ClassPropertyNotExistsException $cpne){
			//Try include appropriate file:
			if (!in_array($name, $this->_include_tryed)){
			$this->_include_tryed[] = $name; //In any case to do not check again next time
				if(OS::is_includeable(($path = 'includes/configs/' . $name . '.config.php'))){
//				dump::a($path);
				include($path);
					if(m()->is_set($name, $GLOBALS['__CONFIG'])){//New key
					$this->addSetting($name, $GLOBALS['__CONFIG'][$name]);
					}
				//return $this->getProperty($name); //Again
				return $this->__SETS[$name];
				//return $this->__SETS[$this->checkNamePossible(REQUIRED_NOT_NULL($name), __METHOD__)];
				}
			}
		throw $cpne; //If include and fine failed throw outside;
		}
	}#m getProperty
}#c

/**
* Short alias to Single::def('config'). In case of we can-t define constant like:
* define('CONF', Single::def('config'));
* In this case got error: PHP Warning:  Constants may only evaluate to scalar values
* We can do that as variable like $CONF, but meantime it is not convenient in functions/methods:
* we must use global $CONF; first, or also very long $GLOBALS['CONF']
*
* So, choose function aliasing. Now we can invoke it instead of Single::def('HuConfig')->config_value
* or even $GLOBALS['CONF']->someSetting but just:
* CONF()->config_value
*
* Furthermore most often use of that will: Single::def('HuConfig')->className->setting.
* So, class name put to optioal parameter to allow like:
* CONF('className')->desiredClassOption
*
* @param	string(null)	$className Optional class name
* @return Single_Object(HuConfig)|Object(HuArray). If className present - Object(HuArray) returned, Single_Object(HuConfig) otherwise to next query.
**/
function &CONF($className = null){
	/*
	* Strange, but if we direct return:
	* if ($className) return Single::def('HuConfig')->$className;
	* All work as expected and variable returned by reference, but notice occured:
	* PHP Notice:  Only variable references should be returned by reference in /var/www/_SHARED_/Vars/HuConfig.class.php on line 111
	* implicit call to __get solve problem. Is it bug?
	* @todo Fill bug
	**/
	if ($className) return Single::def('HuConfig')->__get($className);
	else return Single::def('HuConfig');
}#f CONF

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