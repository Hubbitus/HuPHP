<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\System;

use Hubbitus\HuPHP\Exceptions\HaltException;

/**
* Class OS has mainly (all) static methods, to determine system-environments, like OS or type of out.
* Was System, but it is registered in PEAR, change to OS
**/
class OS {
	/**
	* Counter for hit counting functionality.
	**/
	private static int $hitCounter = 0;

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
	* @return string
	**/
	public function phpSapiName(): string {
		return \php_sapi_name();
	}

	/**
	* Check if file is includeable.
	* This method is a replacement for `if (@include($file))` because the @ operator
	* suppresses all errors, including fatal ones, in the included file.
	* This method only checks for readability using the include_path.
	* See: http://php.net/manual/en/function.include.php
	*
	* @param	string $filename As it can be passed to include or require.
	**/
	public static function isIncludeable($filename): bool {
		/** is_file, is_readable not suitable, because include_path do not take effect.
		* And opposite comment of "php at metagg dot com" and "medhefgo at googlemail dot com",
		* wouldn't manually check all paths in include_path. Just open this file to read
		* with include_path check parameter support! */
		if ($res = @\fopen($filename, 'r', true)) {
			\fclose($res);	// Not really need opened file, only result of opening.
		}
		return (bool)$res;
	}

	/**
	* Check if given path is absolute or not.
	* Cross-platform implementation supporting Unix, Windows and stream wrappers.
	*
	* @param ?string $pathToCheck Path to check
	* @return bool
	**/
	public static function isPathAbsolute(?string $pathToCheck): bool {
		if ($pathToCheck === null || $pathToCheck === '') {
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

	/**
	* Writes string to stderr instead of stdout.
	*
	* @param string $str String to output
	* @return int Number of bytes written
	**/
	public static function err(string $str): int {
		return \file_put_contents('php://stderr', $str);
	}

	/**
	* Increments and returns hit counter.
	*
	* @param int $count Count to compare
	* @return bool|int True if counter equals $count, otherwise current counter value
	**/
	public static function hitCount(int $count): bool|int {
		if (++self::$hitCounter === $count) {
			return true;
		}
		return self::$hitCounter;
	}

	/**
	* Terminates execution with exception if count exceeded.
	*
	* @param int $count Count to compare
	* @param string $message Exception message
	* @throws HaltException If count is reached
	**/
	public static function exitCount(int $count, string $message = ''): void {
		if (true === self::hitCount($count)) {
			throw new HaltException($message, 0);
		}
	}
}
