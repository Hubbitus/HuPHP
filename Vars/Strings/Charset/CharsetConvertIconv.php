<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Strings\Charset;

/**
 * Charset encoding suite.
 * Iconv implementation.
 *
 * @package Vars
 * @subpackage charset_convert
 * @version 1.1
 * @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
 * @copyright Copyright (c) 2008, Pahan-Hubbitus (Pavel Alexeev)
 * @created ?2009-03-06 16:08 ver 1.0 to 1.1
 **/

use Hubbitus\HuPHP\Exceptions\Strings\Charset\CharsetConvertException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;

class CharsetConvertIconv extends CharsetConvert {
	/**
	 * Array to collect iconv errors during conversion.
	 *
	 * @var array<string>
	 **/
	protected array $_charset_convert_Errors = [];

	/**
	 * Constructor.
	 *
	 * @param string|null $text Text to convert
	 * @param string|null $inEnc Input encoding
	 * @param string $outEnc Output encoding (default: UTF-8)
	 **/
	public function __construct(?string $text, ?string $inEnc = null, string $outEnc = 'UTF-8') {
		parent::__construct($text, $inEnc, $outEnc);
	}

	/**
	 * Convert text encoding using iconv.
	 *
	 * @return void
	 * @throws CharsetConvertException If iconv error occurs
	 * @throws VariableRequiredException If inEnc or outEnc is not set
	 **/
	public function convert(): void {
		REQUIRED_VAR($this->_in, 'InEncoding');
		REQUIRED_VAR($this->_out, 'OutEncoding');

		/*
		 * iconv does not provide any chance to handle errors.
		 * Even if provided charset is not correct, it only produce PHP Notice and return empty string.
		 * So, as last chance - catch this warning and convert it into exception!
		 */
		// Backup settings
		$oldErrorHandler = \set_error_handler([$this, 'error_handler']);
		$oldErrorReporting = \error_reporting(E_ALL);
		$this->_resText = \iconv($this->_in, $this->_out, $this->_text);

		// Restore settings
		\error_reporting($oldErrorReporting);
		if ($oldErrorHandler) {
			\set_error_handler($oldErrorHandler);
		} elseif (\is_null($oldErrorHandler)) {
			\restore_error_handler();
		}

		// Processing
		if ($this->_charset_convert_Errors) {
			$ttt = $this->_charset_convert_Errors; // Local buffer
			$this->_charset_convert_Errors = []; // Clear BEFORE throw
			throw new CharsetConvertException(\implode(';', $ttt));
		}
	}

	/**
	 * Static equivalent of convert() for static, fast invoke.
	 *
	 * @param string $text Text to convert
	 * @param string|null $inEnc Input encoding
	 * @param string $outEnc Output encoding (default: UTF-8)
	 * @return string Converted text
	 **/
	public static function conv(string $text, ?string $inEnc = null, string $outEnc = 'UTF-8'): string {
		// This is correct only if Late Static Binding present. So, it starts from PHP 5.3.0
		// If we want make this code work on earlier releases - just copy this function completely in derivate.
		$conv = new self($text, $inEnc, $outEnc);
		return $conv->getResult();
	}

	/**
	 * Error handler for iconv errors.
	 *
	 * @param int $errno Error level
	 * @param string $errstr Error message
	 * @param string $errFile Error file
	 * @param int $errLine Error line
	 * @return bool True if error was handled, false otherwise
	 **/
	public function error_handler(int $errno, string $errstr, string $errFile, int $errLine): bool {
		if (\stristr($errstr, 'iconv')) { // This hack only for iconv errors
			$this->_charset_convert_Errors[] = $errstr;
			// Don't execute PHP internal error handler
			return true;
		} else {
			return false; // Default error-handler
		}
	}
}
