<?php
declare(strict_types=1);
namespace Hubbitus\HuPHP\Debug;

/**
* Debug and backtrace toolkit.
* Utility class for dumping variables in a human-readable format.
*
* @package Debug
* @version 2.0
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
**/
class Dump {
	/**
	* Dump variable to console with optional header
	*
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	**/
	public static function c(mixed $var, ?string $header = null, bool $return = false): mixed {
		$output = self::generateOutput($var, $header, 'CONSOLE');

		if ($return) {
			return $output;
		} else {
			echo $output;
			return null;
		}
	}

	/**
	* Dump variable to web output with optional header
	*
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	**/
	public static function w(mixed $var, ?string $header = null, bool $return = false): mixed {
		$output = self::generateOutput($var, $header, 'WEB');

		if ($return) {
			return $output;
		} else {
			echo $output;
			return null;
		}
	}

	/**
	* Dump variable to log with optional header
	*
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	**/
	public static function log(mixed $var, ?string $header = null, bool $return = false): mixed {
		$output = self::generateOutput($var, $header, 'LOG');

		if ($return) {
			return $output;
		} else {
			error_log($output);
			return null;
		}
	}

	/**
	* Universal dump function - alias for console dump
	*
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	**/
	public static function a(mixed $var, ?string $header = null, bool $return = false): mixed {
		return static::c($var, $header, $return);
	}

	/**
	* Dump based on output type
	*
	* @param int $type Output type
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	**/
	public static function byOutType(int $type, mixed $var, ?string $header = null, bool $return = false): mixed {
		// This would need to reference OS class constants
		// For now, just default to console output
		return static::c($var, $header, $return);
	}

	/**
	* Generate the output string for dumping
	*
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display
	* @param string $mode Output mode
	* @return string Formatted output
	**/
	private static function generateOutput(mixed $var, ?string $header, string $mode): string {
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
	* @param string|null $header Optional header to display
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	* @codeCoverageIgnore Web branch cannot be tested in CLI environment
	**/
	public static function auto(mixed $var, ?string $header = null, bool $return = false): mixed {
		if (php_sapi_name() === 'cli') {
			return self::c($var, $header, $return);
		} else {
			return self::w($var, $header, $return);
		}
	}
}
