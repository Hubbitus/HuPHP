<?
/**
* Singleton pattern.
*
* @package Vars
* @subpackage Classes
* @version 1.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
* @changelog
*	* 2008-05-30 13:22
*	- Fore backward capability replace construction (!@var ?: "Error") to (!@var ? '' : "Error")
*
*	* 2009-03-01 11:07 ver 1.0b to 1.1
*	- Method tryIncludeByClassName now deprecated. Use autoload instead.
*	- In ::def() method suppress error if config absent: @$GLOBALS['__CONFIG'][$className]
*	- Method def now return reference, how it do method singleton too.
*	- Add few examples of usage.
*
*	* 2009-03-05 13:18 ver 1.1 to 1.1.1
*	- fprintf(STDERR, ...) replaced to file_put_contents('php://stderr', ...) to do not fire warnings what STDERR defined when in web.
*
*	* 2009-03-05 13:39 ver 1.1.1 to 1.1.2
*	* Adjust include, since OS::is_includeable now only return boolean, do not tryed include anything.

*	* 2009-03-10 06:19 ver 1.1.2 to 1.2
*	- Method ::def() now used CONF()->getRaw($className) instead of direct accessing to @$GLOBALS['__CONFIG'][$className]
*		with all futures what it does such as settings autoload.
*	- include_once('Vars/HuConfig.class.php') added additional dependency.
**/

/*-inc
include_once('Exceptions/classes.php');
include_once('System/OS.php');# For OS::is_includeable()
include_once('Vars/HuConfig.class.php');
*/
/**
* @uses OS
* @uses HuConfig
* @uses ClassNotExistsException
**/

/**
* Example from http://ru2.php.net/manual/ru/language.oop5.patterns.php
* Modified
**/
class Single{
// Hold an instance of the class
private static $instance = array();

	/**
	* A private constructor; prevents direct creation of object
	**/
	protected final function __construct(){
	echo 'I am constructed. But can\'t be :) ';
	}//__c

	/**
	* The main singleton static method
	* All call must be: Single::singleton('ClassName'). Or by its short alias: Single::def('ClassName')
	*
	* @param	string	$className Class name to provide Singleton instance for it.
	* @params variable number of parameters. Any other parameters directly passed to instantiated class-constructor.
	**/
	public static function &singleton($className){
		if (!isset(self::$instance[$className])){// @TODO: provide hashing class name and args, and index by hash.
			if (!function_exists('__autoload')) self::tryIncludeByClassName($className);

		$args = func_get_args();
		unset($args[0]);//Class name
		/*
		Using Reflection to instanciate class with any args.
		See http://ru2.php.net/manual/ru/function.call-user-func-array.php, comment of richard_harrison at rjharrison dot org
		*/
		// make a reflection object
		$reflectionObj = new ReflectionClass($className);
		// use Reflection to create a new instance, using the $args
		self::$instance[$className] = $reflectionObj->newInstanceArgs($args);
		}

	return self::$instance[$className];
	}#m singleton

	/**
	* The default configured. Short alias for {@see ::singleton()}
	*
	* @return &Object($classname)
	**/
	public static function &def($className){
	//return self::singleton($className, @$GLOBALS['__CONFIG'][$className]);
	return self::singleton($className, CONF()->getRaw($className, true));
	}#m def

	/**
	* Try include
	* @deprecated Use autoload instead.
	*
	*
	* @param string	$className Name of needed class
	* @return
	**/
	public static function tryIncludeByClassName($className){
	file_put_contents('php://stderr', 'Usage of Single::tryIncludeByClassName is deprecated. Use autoload instead.');
		#is_readable is not use include_path, so can not use this check. More explanation see {$link OS::is_includeable()}
		if (!class_exists($className) and isset($GLOBALS['__CONFIG'][$className]['class_file']) and OS::is_includeable($GLOBALS['__CONFIG'][$className]['class_file']))
		include($GLOBALS['__CONFIG'][$className]['class_file']);

		#repetition check
		if (!class_exists($className)) throw new ClassNotExistsException($className . ' NOT exist!'. (!@$GLOBALS['__CONFIG'][$className]['class_file'] ? '' : ' And, additionaly include provided path ['.$GLOBALS['__CONFIG'][$className]['class_file'].'] not helped in this!'));
	}#m tryIncludeByClassName

	/**
	* Prevent users to clone the instance
	**/
	public function __clone(){
	trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
}#c Single

/**
* @example
* This will always retrieve a single instance of the class
*
* $test = Single::singleton();
* $test->bark();
* $test = Single::singleton()->bark();
* //Default invoke, using $GLOBALS['__CONFIG']['classname'] as arguments.
* Single::def('classname')->...
**/
?>