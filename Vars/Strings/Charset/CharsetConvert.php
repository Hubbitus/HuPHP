<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Vars\Strings\Charset;

use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use function Hubbitus\HuPHP\Macroses\REQUIRED_VAR;

/**
* Charset encoding suite
*
* @author Pahan-Hubbitus (Pavel Alexeev) <Pahan@Hubbitus.info>
* @created ?2009-03-06 16:08 ver 1.1 to 1.0
**/
abstract class CharsetConvert {
	protected ?string $_in = null;
	protected ?string $_out = null;
	protected ?string $_text = null;
	protected ?string $_resText = null;

	/**
	* Constructor.
	*
	* @param string $text
	* @param ?string $inEnc
	* @param ?string $outEnc
	* @throws VariableRequiredException
	**/
	public function __construct($text, ?string $inEnc = null, ?string $outEnc = 'UTF-8') {
		$this->setInEnc($inEnc);
		$this->setOutEnc($outEnc);
		$this->setText(REQUIRED_VAR($text, 'TextToConvert'));

		if ($inEnc !== null && $outEnc !== null) {
			$this->convert();
		}
	}

	/**
	* Main working horse. Must be reimplemented each time we should provide new layer of conversion (mb, iconv, recode etc)
	**/
	abstract public function convert(): static;

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
		// Late Static Binding is intentional - allows child classes to return their own instances
		// @phpstan-ignore new.static
		$conv = new static($text, $inEnc, $outEnc);
		return $conv->getResult();
	}

	/**
	* Set new In encoding
	*
	* @param ?string $enc New encoding
	**/
	public function &setInEnc(?string $enc): static {
		$this->_in = $enc;
		$this->_resText = null;
		return $this;
	}

	/**
	* Get current In encoding
	*
	**/
	public function getInEnc(): ?string {
		return $this->_in;
	}

	/**
	* Set new Out encoding
	*
	* @param ?string $enc New encoding
	**/
	public function &setOutEnc(?string $enc): static {
		$this->_out = $enc;
		$this->_resText = null;
		return $this;
	}

	/**
	* Get current Out encoding
	**/
	public function getOutEnc(): ?string {
		return $this->_out;
	}

	/**
	* Set text to convert encoding.
	*
	* @param string $newText
	**/
	public function &setText(string $newText): static {
		$this->_text = $newText;
		return $this;
	}

	/**
	* Get current text
	**/
	public function getText(): ?string {
		return $this->_text;
	}

	/**
	* Return result of conversion. If it is empty, run {@see ::convert()}
	**/
	public function getResult(): string {
		if ($this->_resText === null || $this->_resText === '') {
			$this->convert();
		}
		return $this->_resText;
	}

	/**
	* Auto conversion into string; {@see ->getResult()}
	**/
	public function __toString(): string {
		return $this->getResult();
	}
}
