<?
	/**
	* Default value, but allow redefine
	* Also yu may want define AUTOINCLUDE_ADDON_FILE, and it is also will be included.
	* WARNING: It must itself proper merge/replace/add values into main array, it just included after main!
	**/
	if ( !defined('AUTOINCLUDE_FILE') ) define('AUTOINCLUDE_FILE', '__autoload.map.php');
	if ( !defined('AUTOLOAD_DEBUG') ) define('AUTOLOAD_DEBUG', false);
include_once('macroses/IS_SET.php'); //It must be explicit yet
/**
* Magick class autoload function.
*
* @param string $classname
* @return boolean
**/
//function __load_class($classname){
function __autoload($classname){
	if (
		is_set('class_file', (array)@$GLOBALS['__CONFIG'][$classname])
		and
		OS::is_includeable($GLOBALS['__CONFIG'][$classname]['class_file'])
	){
		if ( AUTOLOAD_DEBUG ) fprintf(STDERR, 'Loading class "%s" from file "%s", which got from "%s"' . "\n", $classname, $GLOBALS['__CONFIG'][$classname]['class_file'], '$GLOBALS[\'__CONFIG\'][$classname][\'class_file\']');
	include($GLOBALS['__CONFIG'][$classname]['class_file']);
	return true;
	}
	else{
		if (!is_set('__autoload_map', @$GLOBALS['__CONFIG'])){
		include_once('System/OS.php');
			if( OS::is_includeable(AUTOINCLUDE_FILE) ){
			require_once(AUTOINCLUDE_FILE);//Standard
				if ( AUTOLOAD_DEBUG ) fprintf(STDERR, 'include(AUTOINCLUDE_FILE) [%s]' . "\n", AUTOINCLUDE_FILE);
				if ( defined('AUTOINCLUDE_ADDON_FILE') ){
				require_once(AUTOINCLUDE_ADDON_FILE);
					if ( AUTOLOAD_DEBUG ) fprintf(STDERR, 'include(AUTOINCLUDE_ADDON_FILE) [%s]' . "\n", AUTOINCLUDE_ADDON_FILE);
				}
			}
		}
		//Map included, include file
		if ( is_set($classname, @$GLOBALS['__CONFIG']['__autoload_map']) ){
			if ( AUTOLOAD_DEBUG ) fprintf(STDERR, 'Loading class "%s" from file "%s", which got from "%s"' . "\n", $classname, $GLOBALS['__CONFIG']['__autoload_map'][$classname], '$GLOBALS[\'__CONFIG\'][\'__autoload_map\'][$classname]');
		require($GLOBALS['__CONFIG']['__autoload_map'][$classname]);
		return true;
		}
	}
return false;
}

//$GLOBALS['__CONFIG'] =

//function __autoload($classname) {
//	if (__load_class($classname)){
//	//Debug-log message
//	fprintf(STDERR, 'Loading class "%s"' . "\n", $classname);
//	return;
//	}
////Debug-log message
//fprintf(STDERR, 'Class "%s" not found' . "\n", $classname);
//}

/**
* Autoload for MACROSES (and also all functions)!!!
*
* PHP compleatly do not support autoloading of functions, only classes.
* So, we may do that DIRTY HACK:
* Instead of just using function like MY_COOL_FUNCTION() precedes it by singleton:
* m()->MY_COOL_FUNCTION().
* In PHP >= 5.3.0 (When introdused magick methos __callStatic) we may use more
* efficient way for that, like just: m::MY_COOL_FUNCTION(). But it is not
* supported in previous versions.
*
* Eval used to hide from current version of PHP and avoid errors/warnings.
**/
	if (version_compare(PHP_VERSION, '5.3.0-dev', '>=')){
	eval('
		class m{
			public function __construct(){
			}
			public static function __callStatic($name, $arguments){
			//echo \'called __callStatic($name, $arguments):\' . "__callStatic($name, $arguments)\n";
				if (!function_exists($name)){
				__autoload($name);
				}
			return call_user_func_array($name, $arguments);
			}
			public function __call($name, $arguments){
			self::__callStatic($name, $arguments);
			}
		}#c
	');
	}
	else{
	eval('
		class m{
			public function __construct(){
			}
			// __callStatic not present yet :(
			public static function __call($name, $arguments) {
			//echo \'called __callStatic($name, $arguments):\' . "__callStatic($name, $arguments)\n";
				if (!function_exists($name)){
				__autoload($name);
				}
			return call_user_func_array($name, $arguments);
			}
		}
	');
	}
function &m(){
#use#include_once('Vars/Singleton.php');
return Single::def('m');
}
?>
