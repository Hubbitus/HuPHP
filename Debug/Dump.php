<?php
declare(strict_types=1);

/**
* Debug and backtrace toolkit.
* Utility class for dumping variables in a human-readable format.
*
* @package Debug
* @version 2.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/

namespace Hubbitus\HuPHP\Debug;

class Dump {
	/**
	* Dump variable to console with optional header
	*
	* @param mixed $var Variable to dump
	* @param string $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, otherwise prints to console
	**/
	public static function c($var, $header = null, $return = false) {
		$output = static::generateOutput($var, $header, 'CONSOLE');

		if ($return) {
			return $output;
		} else {
			echo $output;
		}
	}

	/**
	* Dump variable to web output with optional header
	*
	* @param mixed $var Variable to dump
	* @param string $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, otherwise prints to web
	**/
	public static function w($var, $header = null, $return = false) {
		$output = static::generateOutput($var, $header, 'WEB');

		if ($return) {
			return $output;
		} else {
			echo $output;
		}
	}

	/**
	* Dump variable to log with optional header
	*
	* @param mixed $var Variable to dump
	* @param string $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, otherwise prints to log
	**/
	public static function log($var, $header = null, $return = false) {
		$output = static::generateOutput($var, $header, 'LOG');

		if ($return) {
			return $output;
		} else {
			error_log($output);
		}
	}

	/**
	* Universal dump function - alias for console dump
	*
	* @param mixed $var Variable to dump
	* @param string $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, otherwise prints to console
	**/
	public static function a($var, $header = null, $return = false) {
		return static::c($var, $header, $return);
	}

	/**
	* Dump based on output type
	*
	* @param int $type Output type
	* @param mixed $var Variable to dump
	* @param string $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true
	**/
	public static function byOutType($type, $var, $header = null, $return = false) {
		// This would need to reference OS class constants
		// For now, just default to console output
		return static::c($var, $header, $return);
	}

	/**
	* Generate the output string for dumping
	*
	* @param mixed $var Variable to dump
	* @param string $header Optional header to display
	* @param string $mode Output mode
	* @return string Formatted output
	**/
	private static function generateOutput($var, $header, $mode) {
		$output = '';

		if ($header !== null) {
			$output .= "=== {$header} ===\n";
		}

		if (is_array($var) || is_object($var)) {
			$output .= print_r($var, true);
		} else {
			$output .= var_export($var, true);
		}

		$output .= "\n";

		return $output;
	}

	/**
	* Auto-detect appropriate dump method based on environment
	*
	* @param mixed $var Variable to dump
	* @param string $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true
	**/
	public static function auto($var, $header = null, $return = false) {
		if (php_sapi_name() === 'cli') {
			return static::c($var, $header, $return);
		} else {
			return static::w($var, $header, $return);
		}
	}
}
