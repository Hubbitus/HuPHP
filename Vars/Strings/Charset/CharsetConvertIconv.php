<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Strings\Charset;

use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;

/**
* Charset encoding suite.
* mb_convert_encoding() implementation.
*
* Why mb_convert_encoding() instead of iconv():
* - More reliable error handling (throws ValueError for invalid encodings)
* - Better Unicode support
* - No need for custom error handler (avoids PHPUnit 13 "risky test" warnings)
* - Built-in PHP 8.0+ functionality
* - More consistent behavior across different systems
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created ?2009-03-06 16:08 ver 1.0 to 1.1
**/
class CharsetConvertIconv extends CharsetConvert {
	/**
	* Array to collect encoding errors during conversion.
	* Kept for backward compatibility, but no longer used by convert().
	*
	* @var array<string>
	**/
	protected array $_charset_convert_Errors = [];

	/**
	* Constructor.
	*
	* @param ?string $text Text to convert
	* @param ?string $inEnc Input encoding
	* @param string $outEnc Output encoding (default: UTF-8)
	**/
	public function __construct(?string $text, ?string $inEnc = null, string $outEnc = 'UTF-8') {
		parent::__construct($text, $inEnc, $outEnc);
	}

	/**
	* Convert text encoding using mb_convert_encoding().
	*
	* @throws \ValueError If encoding is invalid (PHP 8.0+)
	* @throws VariableRequiredException If inEnc or outEnc is not set
	**/
	public function convert(): static {
		REQUIRED_VAR($this->_in, 'InEncoding');
		REQUIRED_VAR($this->_out, 'OutEncoding');

		/*
		* mb_convert_encoding() provides better error handling than iconv():
		* - Throws ValueError for invalid encodings (PHP 8.0+)
		* - No need for custom error handler
		* - More reliable Unicode support
		*/
		$this->_resText = \mb_convert_encoding($this->_text, $this->_out, $this->_in);
		return $this;
	}

	/**
	* Static equivalent of convert() for static, fast invoke.
	*
	* @param string $text Text to convert
	* @param ?string $inEnc Input encoding
	* @param ?string $outEnc Output encoding (default: UTF-8)
	* @return string Converted text
	**/
	public static function conv(string $text, ?string $inEnc = null, ?string $outEnc = 'UTF-8'): string {
		// This is correct only if Late Static Binding present. So, it starts from PHP 5.3.0
		// If we want make this code work on earlier releases - just copy this function completely in derivate.
		$conv = new self($text, $inEnc, $outEnc);
		return $conv->getResult();
	}
}
