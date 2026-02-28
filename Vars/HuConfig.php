<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars;

/**
* Routine tasks to made easy OOP.
*
* @package Vars
* @subpackage Settings
* @version 0.4
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2009, Pahan-Hubbitus (Pavel Alexeev)
* @created 2009-03-01 22:12
**/

use Hubbitus\HuPHP\Exceptions\Classes\ClassPropertyNotExistsException;
use Hubbitus\HuPHP\System\OS;
use Hubbitus\HuPHP\Vars\Settings\SettingsCheck;
use Hubbitus\HuPHP\Vars\Single;
use function Hubbitus\HuPHP\Macroses\REQUIRED_NOT_NULL;

/**
* Class to provide easy access to $GLOBALS['__CONFIG'] variables.
* Intended use with Singleton class as:
* @example Single::def('HuConfig')->config_value
**/
class HuConfig extends SettingsCheck {
	private $_include_tried = [];

	public function __construct() {
		parent::__construct(array_keys($GLOBALS['__CONFIG']), $GLOBALS['__CONFIG']);
	}

	/**
	* As __get before.
	* Now {@see __get()} reimplemented to return HuArray instead of raw arrays
	* Bee careful - after standard call (not raw) original Array value was replaced by HuArray!
	*
	* @param string	$varname
	* @param bool	$noThrow If true - silently not thrown any exception.
	* @return &mixed
	**/
	public function &getRaw($varname, $noThrow = false): mixed {
		return $this->getProperty($varname, $noThrow);
	}

	/**
	* For more comfort access in config fields without temporary variables like:
	* Single::def('HuConfig')->test->first
	*
	* @param string	$varname
	* @return &Object(HuArray)
	**/
	public function &__get($varname): mixed {
		$ret =& $this->getProperty($varname);

		if (\is_array($ret)){
			$ret = new HuArray($ret); //Replace original on the fly
			return $ret;
		}
		else return $ret;
	}

	/**
	* Reimplement as initial, only return value by reference
	* Also try include file 'includes/configs/' . $name . '.config.php' if it exist to find needed settings.
	* @inheritdoc
	* @param string	$name
	* @param bool $noThrow=false If true - silently not thrown any exception.
	**/
	public function &getProperty($name, $noThrow = false): mixed {
		try {
			return $this->__SETS[$this->checkNamePossible(REQUIRED_NOT_NULL($name), __METHOD__)];
		}
		catch(\Throwable $e){
			//Try include appropriate file only for ClassPropertyNotExistsException:
			if ($e instanceof ClassPropertyNotExistsException && !\in_array($name, $this->_include_tried)){
				$this->_include_tried[] = $name; //In any case to do not check again next time
				$path = 'includes/configs/' . $name . '.config.php';
				if(OS::is_includeable($path)){
					include($path);
				}
				if(isset($GLOBALS['__CONFIG'][$name])){//New key
					$this->addSetting($name, $GLOBALS['__CONFIG'][$name]);
				}
				return $this->__SETS[$name];
			}

			//Silent if required.
			if (!$noThrow){
				throw $e; //If include and fine failed throw outside;
			}
			else{
				// Avoid: Notice: Only variable references should be returned by reference in /var/www/_SHARED_/Vars/HuConfig.class.php on line 101
				$t = null;
				return $t;
			}
		}
	}
}

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
* So, class name put to optional parameter to allow like:
* CONF('className')->desiredClassOption
*
* @param	string(null)	$className Optional class name
* @param	boolean(false)	$noThrow If true - silently not thrown any exception.
* @return Single|HuArray. If className present - HuArray returned, Single(HuConfig) otherwise to next query.
**/
function &CONF($className = null, $noThrow = false){
	/*
	* Strange, but if we direct return:
	* if ($className) return Single::def('HuConfig')->$className;
	* All work as expected and variable returned by reference, but notice occurred:
	* PHP Notice:  Only variable references should be returned by reference in /var/www/_SHARED_/Vars/HuConfig.class.php on line 111
	* implicit call to __get solve problem. Is it bug?
	* @todo Fill bug
	**/
	/*
	* We want use HuConfig in singleton::def. It is produce cycle dependency.
	* So, rely on HuConfig do not take any settings in constructor, we may safely call Single::singleton directly
 	* if ($className) return Single::def('HuConfig')->__get($className);
	* else return Single::def('HuConfig');
	**/
	if ($className) {
		 return Single::singleton(HuConfig::class)->__get($className);
	}
	else {
		 return Single::singleton(HuConfig::class);
	}
}

/**
* @example
* Dump::a(Single::def('HuConfig')->test);
* Dump::a(Single::def('HuConfig')->test->First);
* Dump::a(Single::def('HuConfig')->test->Second);
* Single::def('HuConfig')->test->Second = 'Another text';
* Dump::a(Single::def('HuConfig')->test->Second);
* CONF()->test->Second = 'Yet ANOTHER Another text';
* Dump::a(CONF()->test->Second);
* Dump::a(Single::def('HuConfig')->test);
**/
