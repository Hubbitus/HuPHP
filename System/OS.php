<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System;

use Hubbitus\HuPHP\System\OutputType;

/**
* Class OS has mainly (all) static methods, to determine system-environments, like OS or type of out.
* Was System, but it is registered in PEAR, change to OS
**/
class OS {
	/**
	* Determines output type of current-running process.
	*
	* @return OutputType now one of: OutputType::BROWSER or OutputType::CONSOLE
	**/
	public static function getOutType(): OutputType {
		return isset($_SERVER['HTTP_USER_AGENT']) ? OutputType::WEB : OutputType::CONSOLE;
	}

	/**
	* php_sapi_name()
	*
	* @return
	**/
	public static function phpSapiName(): bool|string {
		return php_sapi_name();
	}

	/**
	* Check if file is includeable. I can't just use if (@include($file)). Or, more exactly i can, but
	*	it is have small different meaning:
	*	@include('include.php') not return and NOT shown errors in including file! Nothing:
	*		Not Notice, Warning or Fatal!!!!
	*		See http://ru2.php.net/manual/ru/function.include-once.php comments of
	*		"flobee at gmail dot com" and "php at metagg dot com" and http://php.net/include/
	*		comment of "medhefgo at googlemail dot com"
	*		In other words, absent way (get me known if I am wrong) to suppress errors like
	*		'file not found' or 'not readable', construction @include suppress ALL (even Critical!)
	*		in including files, and nested (included from including).
	*	Result of check may be also applicable to require()
	*
	* @param	string $filename As it can be passed to include or require.
	* @return	boolean
	**/
	public static function is_includeable($filename){
		/** is_file, is_readable not suitable, because include_path do not take effect.
		* And opposite comment of "php at metagg dot com" and "medhefgo at googlemail dot com",
		* wouldn't manually check all paths in include_path. Just open this file to read
		* with include_path check parameter support! */
		if ($res = @fopen($filename, 'r', true)){
			fclose($res);	// Not really need opened file, only result of opening.
		}
		return (bool)$res;
	}

	/**
	* Check if given path is absolute or not.
	* Cross-platform implementation supporting Unix, Windows and stream wrappers.
	*
	* @param $pathToCheck	string Path to check
	* @return boolean
	**/
	public static function isPathAbsolute($pathToCheck): bool {
		if (empty($pathToCheck)) {
			return false;
		}

		// Check for stream wrappers first (always absolute)
		if (\preg_match('@^(?:' . \implode('|', \stream_get_wrappers()) . ')://@', $pathToCheck)) {
			return true;
		}

		// Unix-like: absolute paths start with /
		if ($pathToCheck[0] === '/') {
			return true;
		}

		// Windows: check for drive letter (C:) or UNC path (\\server\share)
		// Works on any platform for checking any path format
		$len = \strlen($pathToCheck);
		if ($len >= 2) {
			// Drive letter: C:, D:, etc.
			if ($pathToCheck[1] === ':') {
				return true;
			}
			// UNC path: \\server\share
			if ($pathToCheck[0] === '\\' && $pathToCheck[1] === '\\') {
				return true;
			}
		}

		return false;
	}
}
