<?
	/**
	* Default value, but allow redefine
	* Also yu may want define AUTOINCLUDE_ADDON_FILE, and it is also will be included.
	* WARNING: It must itself proper merge/replace/add values into main array, it just included after main!
	**/
	if (!defined('AUTOINCLUDE_FILE')) define('AUTOINCLUDE_FILE', '__autoload.map.php');

include_once('macroses/IS_SET.php'); //It is yet must be explicit

function __load_class($classname){
	if (
		is_set('class_file', (array)@$GLOBALS['__CONFIG'][$classname])
		and
		OS::is_includeable($GLOBALS['__CONFIG'][$classname]['class_file'])
	){
	include($GLOBALS['__CONFIG'][$classname]['class_file']);
	return true;
	}
	else{
		if (!is_set('__autoload_map', $GLOBALS['__CONFIG'])){
		include_once('System/OS.php');
			if( OS::is_includeable(AUTOINCLUDE_FILE) ){
			include_once(AUTOINCLUDE_FILE);//Standard
				if ( defined('AUTOINCLUDE_ADDON_FILE') ) include_once(AUTOINCLUDE_ADDON_FILE);
			}
		}
		//Map included, include file
		if ( is_set($classname, @$GLOBALS['__CONFIG']['__autoload_map']) ){
		require($GLOBALS['__CONFIG']['__autoload_map'][$classname]);
		return true;
		}
	}
return false;
}

//$GLOBALS['__CONFIG'] =

function __autoload($classname) {
	if (__load_class($classname, '')){
	//Debug-log message
	fprintf(STDERR, 'Loading class "%s"' . "\n", $classname);
	return;
	}
//Debug-log message
fprintf(STDERR, 'Class "%s" not found' . "\n", $classname);
}
?>
