<?
/**
* System environment and information
* @package System ??
* @version 2.0.2
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan [at] Hubbitus [ dot. ] info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
*
*	@changelog
*	* 2008-11-05 00:47 ver 2.0b to 2.0.1
*	- In method OS::is_includeable() remove second parameter $include, because including file in caller context
*		is not possible. And inclusion in context of this method is mistake!
*
*	* 2009-01-25 00:58 ver 2.0.1 to 2.0.2
*	- Add method isPathAbsolute()
**/

/**
* Class OS has mainly (all) static methods, to determine system-enveroments, like OS or type of out.
* Was System, but it is registered in PEAR, change to OS
**/
class OS {
const OUT_TYPE_BROWSER = 1;
const OUT_TYPE_CONSOLE = 2;
const OUT_TYPE_PRINT = 4; /** Pseudo!!! Need automaticaly detect OUT_TYPE_BROWSER or OUT_TYPE_CONSOLE */
const OUT_TYPE_FILE = 8;
const OUT_TYPE_WAP = 16;
#const OUT_TYPE_ = 16;

/**
* Possible return-values of 
* http://ru2.php.net/php_sapi_name comment from "cheezy at lumumba dot luc dot ac dot be"
**/
static $SAPIs = array(
	'aolserver',
	'activescript',
	'apache',
	'cgi-fcgi',
	'cgi',
	'isapi',
	'nsapi',
	'phttpd',
	'roxen',
	'java_servlet',
	'thttpd',
	'pi3web',
	'apache2filter',
	'caudium',
	'apache2handler',
	'tux',
	'webjames',
	'cli',
	'embed,',
	'milter'
);


	/**
	* Determines out type of current-running process.
	* @return Now one of const: ::OUT_TYPE_BROWSER or ::OUT_TYPE_CONSOLE
	**/
	static public function getOutType(){
		if (isset($_SERVER['HTTP_USER_AGENT'])) return self::OUT_TYPE_BROWSER;
		else return self::OUT_TYPE_CONSOLE;
	}#m getOutType

	/**
	* php_sapi_name()
	* @return
	**/
	static public function phpSapiName(){
	return php_sapi_name();
	}#m phpSapiName

	/**
	* Check if file is includable. I can't just use if (@inlude($file)). Or, more exactly i can, but
	*	it is have small different meaning:
	*	@include('include.php') not return and NOT shown errors in including file! Nothing:
	*		Not Notice, Warning or Fatal!!!!
	*		See http://ru2.php.net/manual/ru/function.include-once.php comments of
	*		"flobee at gmail dot com" and "php at metagg dot com" and http://php.net/include/
	*		comment of "medhefgo at googlemail dot com"
	*		In other words, absent way (get me known if I am wrong) to suppress errors like
	*		'file not found' or 'not readable', construction @include suppres ALL (even Critical!)
	*		in including files, and nested (included from including).
	*	Result of check may be also applyable to require()
	* @param	string $filenam As it can be passed to include or require.
	* @return
	**/
	static public function is_includeable($filename){
		/** is_file, is_readable not suitable, because include_path do not take effect.
		* And opposite comment of "php at metagg dot com" and "medhefgo at googlemail dot com",
		* woudn't manualy check all paths in include_path. Just open this file to read
		* with include_path check parameter support! */
		if ($res = @fopen($filename, 'r', true)){
		fclose($res);	// Not realy need opened file, only result of opening.
		}
	return (bool)$res;
	}#m is_inludeable

	/**
	* Check if given path is absolute or not.
	*
	* @param $pathToCheck	string Path to check
	* @return boolean
	**/
	static public function isPathAbsolute($pathToCheck){
		//@TODO: case 'DAR': ;break; //Darwin http://qaix.com/php-web-programming/139-944-constant-php-os-and-mac-server-read.shtml
		// This check from http://ru2.php.net/php_uname
		if ('WIN' != strtoupper(substr(PHP_OS, 0, 3))){
		return ( '/' == $pathToCheck{0} );
		}
		else{//WIN
		return ( ':' == $pathToCheck{1} );
		}
	}
}#c OS
?>