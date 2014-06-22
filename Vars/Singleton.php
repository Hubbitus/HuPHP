<?
/**
* Singleton pattern.
*
* @package Vars
* @subpackage Classes
* @version 1.2.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
* @created ?2008-05-30 13:22
*
* @uses OS
* @uses HuConfig
* @uses ClassNotExistsException
**/
include_once('Vars/HuConfig.class.php'); //Must be implisit, to break dependency circle. Free &CONF() function used.

/**
* Example from http://ru2.php.net/manual/ru/language.oop5.patterns.php, modified
**/
class Single{
	/**
	* Hold an instance of the class
	**/
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
	* @params	variable number of parameters. Any other parameters directly passed to instantiated class-constructor.
	**/
	public static function &singleton($className){
		$args = func_get_args();
		unset($args[0]);//Class name

		$hash = $className . '_' . self::hash($args);
		if (!isset(self::$instance[$hash])){
			if (!function_exists('__autoload') and (!function_exists('spl_autoload_functions') or !spl_autoload_functions())) self::tryIncludeByClassName($className);

			/*
			Using Reflection to instanciate class with any args.
			See http://ru2.php.net/manual/ru/function.call-user-func-array.php, comment of richard_harrison at rjharrison dot org
			*/
			$reflectionObj = new ReflectionClass($className);
			// use Reflection to create a new instance, using the $args
			self::$instance[$hash] = $reflectionObj->newInstanceArgs($args);
		}

		return self::$instance[$hash];
	}#m singleton

	/**
	* The default configured. Short alias for {@see ::singleton()}
	*
	* @return &Object($classname)
	**/
	public static function &def($className){
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
		// is_readable is not use include_path, so can not use this check. More explanation see {$link OS::is_includeable()}
		if (!class_exists($className) and isset($GLOBALS['__CONFIG'][$className]['class_file']) and OS::is_includeable($GLOBALS['__CONFIG'][$className]['class_file']))
		include($GLOBALS['__CONFIG'][$className]['class_file']);

		// Check again
		if (!class_exists($className)) throw new ClassNotExistsException($className . ' NOT exist!'. (!@$GLOBALS['__CONFIG'][$className]['class_file'] ? '' : ' And, additionaly include provided path ['.$GLOBALS['__CONFIG'][$className]['class_file'].'] not helped in this!'));
	}#m tryIncludeByClassName

	/**
	* Prevent users to clone the instance
	**/
	public function __clone(){
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}#m __clone

	/**
	 * Provide simple way of hashing objects and array
	 *
	 * @param	mixed $param
	 * @return	string
	 */
	public static function hash($param){
		return md5(http_build_query($param));
	}#m hash
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