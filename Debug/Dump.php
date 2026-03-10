<?php
declare(strict_types=1);
namespace Hubbitus\HuPHP\Debug;

use Hubbitus\HuPHP\System\OS;

/**
* Debug and backtrace toolkit.
* Utility class for dumping variables in a human-readable format.
* All dump methods automatically detect variable names from calling code when no header provided.
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
**/
class Dump {
	/**
	* Dump variable to console with optional header
	* Automatically detects variable name from calling code when no header provided
	*
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display. If null, auto-detected from call site
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	**/
	public static function c(mixed $var, ?string $header = null, bool $return = false): mixed {
		$header ??= self::detectVarNameFromBacktrace();

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
	* Automatically detects variable name from calling code when no header provided
	*
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display. If null, auto-detected from call site
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	**/
	public static function w(mixed $var, ?string $header = null, bool $return = false): mixed {
		$header ??= self::detectVarNameFromBacktrace();
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
	* Automatically detects variable name from calling code when no header provided
	*
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display. If null, auto-detected from call site
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	**/
	public static function log(mixed $var, ?string $header = null, bool $return = false): mixed {
		$header ??= self::detectVarNameFromBacktrace();
		$output = self::generateOutput($var, $header, 'LOG');

		if ($return) {
			return $output;
		} else {
			\error_log($output);
			return null;
		}
	}

	/**
	* Universal dump function - alias for console dump
	* Automatically detects variable name from calling code when no header provided
	*
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display. If null, auto-detected from call site
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	**/
	public static function a(mixed $var, ?string $header = null, bool $return = false): mixed {
		$header ??= self::detectVarNameFromBacktrace();
		return static::c($var, $header, $return);
	}

	/**
	* Dump based on output type
	*
	* @param int $type Output type
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display. If null, auto-detected from call site
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	**/
	public static function byOutType(int $type, mixed $var, ?string $header = null, bool $return = false): mixed {
		$header ??= self::detectVarNameFromBacktrace();
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

		if (\is_array($var) || \is_object($var)) {
			$output .= \print_r($var, true);
		} else {
			$output .= \var_export($var, true);
		}

		$output .= "\n";

		return $output;
	}

	/**
	* Detect variable name from backtrace by parsing the calling source code
	*
	* @param callable|null $fileReader Optional file reader function for testing. Defaults to file()
	* @return string|null Variable name/expression or null if cannot detect
	*/
	public static function detectVarNameFromBacktrace(?callable $fileReader = null): ?string {
		$backtrace = \debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$dumpMethod = null;

		// Find the Dump method name (the first Dump frame that is not this helper)
		foreach ($backtrace as $frame) {
			if (isset($frame['class']) && $frame['class'] === __CLASS__ && $frame['function'] !== __FUNCTION__) {
				$dumpMethod = $frame['function'];
				break;
			}
		}

		if ($dumpMethod === null) {
			return null;
		}

		// Find the frame for the Dump method call itself; this frame's file/line point to the call site
		foreach ($backtrace as $frame) {
			if (isset($frame['class']) && $frame['class'] === __CLASS__ && $frame['function'] === $dumpMethod) {
				if (!isset($frame['file'], $frame['line'])) {
					continue;
				}
				$file = $frame['file'];
				$line = $frame['line'];

				try {
					$lines = $fileReader !== null ? $fileReader($file) : \file($file);
					if (!isset($lines[$line - 1])) {
						continue;
					}
					$source = \rtrim($lines[$line - 1]);
				} catch (\Throwable $e) {
					continue;
				}

				// Remove single-line and multi-line comments
				$source = \preg_replace('!//.*!', '', $source);
				$source = \preg_replace('!/\*.*?\*/!', '', $source);

				// Build pattern to match Dump::<method>(... first argument ...)
				$pattern = '/([a-zA-Z_\\\\][a-zA-Z0-9_\\\\]*)?\s*::\s*' . \preg_quote($dumpMethod, '/') . '\s*\(\s*([^),]+)/';

				if (\preg_match($pattern, $source, $matches)) {
					$varExpr = \trim($matches[2]);
					if ($varExpr !== '') {
						return $varExpr;
					}
				}
			}
		}

		return null;
	}

	/**
	* Auto-detect appropriate dump method based on environment
	*
	* @param mixed $var Variable to dump
	* @param string|null $header Optional header to display. If null, auto-detected from call site
	* @param bool $return Whether to return the output instead of printing it
	* @return mixed Returns output if $return is true, void otherwise
	**/
	public static function auto(mixed $var, ?string $header = null, bool $return = false, ?OS $os = null): mixed {
		$os ??= new OS(); // Создаем экземпляр, если не передан
		$header ??= self::detectVarNameFromBacktrace();
		if ($os->phpSapiName() === 'cli') {
			return self::c($var, $header, $return);
		} else {
			return self::w($var, $header, $return);
		}
	}
}
