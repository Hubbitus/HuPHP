<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Macro;

use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\Macro\Unicode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
* Tests for Unicode utility class
**/
class UnicodeTest extends TestCase {
	/**
	* Test Unicode::ucfirst with various strings
	**/
	public function testUcfirstBasic(): void {
		$this->assertSame('Hello', Unicode::ucfirst('hello'));
		$this->assertSame('Hello world', Unicode::ucfirst('hello world'));
		$this->assertSame('', Unicode::ucfirst(''));
		$this->assertSame('Привет', Unicode::ucfirst('привет'));
		$this->assertSame('Мир', Unicode::ucfirst('мир'));
	}

	public function testUcfirstEncoding(): void {
		// Test with explicit encoding parameter
		$this->assertSame('Hello', Unicode::ucfirst('hello', 'UTF-8'));
		// Using ISO-8859-1 encoding for ASCII characters
		$this->assertSame('A', Unicode::ucfirst('a', 'ISO-8859-1'));
	}

	/**
	* Test Unicode::wordwrap with various options
	**/
	public function testWordwrap(): void {
		// existing tests
		$text = "This is a long text that should be wrapped";
		$result = Unicode::wordwrap($text, 20);

		$this->assertStringContainsString("\n", $result);
		$lines = \explode("\n", $result);
		foreach ($lines as $line) {
			$this->assertLessThanOrEqual(20, \strlen($line));
		}

		$shortText = "12345678901234567890";
		$resultCut = Unicode::wordwrap($shortText, 10, '|', true);
		$this->assertStringContainsString('|', $resultCut);
		$this->assertNotSame($shortText, $resultCut);

		$this->assertSame('', Unicode::wordwrap('', 10));
		$this->assertSame('hi', Unicode::wordwrap('hi', 20));

		$resultCustom = Unicode::wordwrap('12345678901234567890', 5, '...');
		$this->assertStringContainsString('...', $resultCustom);
		// Edge case: string with only punctuation
		$punct = "!!!???";
		$this->assertSame('!!!???', Unicode::wordwrap($punct, 10));
	}

	/**
	* Test Unicode::ord with various characters
	**/
	public function testOrd(): void {
		// Valid cases
		$this->assertSame(65, Unicode::ord('A'));
		$this->assertSame(97, Unicode::ord('a'));
		$this->assertSame(1040, Unicode::ord('А'));
		$this->assertSame(0, Unicode::ord("\x00"));
		$this->assertSame(33, Unicode::ord('!'));

		// 2-byte valid
		$this->assertSame(160, Unicode::ord("\xC2\xA0"));
		$this->assertSame(231, Unicode::ord("\xC3\xA7"));

		// 3-byte valid
		$this->assertSame(2309, Unicode::ord("\xE0\xA4\x85"));
		$this->assertSame(8730, Unicode::ord("\xE2\x88\x9A"));

		// 4-byte valid
		$this->assertSame(128512, Unicode::ord("\xF0\x9F\x98\x80"));

		// 5-byte (invalid but handled, returns 0)
		$this->assertSame(0, Unicode::ord("\xF8\x80\x80\x80\x80"));
		// 6-byte (invalid but handled, returns 0)
		$this->assertSame(0, Unicode::ord("\xFC\x80\x80\x80\x80\x80"));
	}

	#[DataProvider('invalidOrdProvider')]
	public function testOrdThrowsException(string $char): void {
		$this->expectException(VariableRangeException::class);
		Unicode::ord($char);
	}

	public static function invalidOrdProvider(): array {
		return [
			'empty string' => [''],
			'incomplete 2-byte' => ["\xC2"],
			'incomplete 3-byte' => ["\xE0"],
			'incomplete 4-byte' => ["\xF0"],
			'incomplete 5-byte' => ["\xF8\x80"],
			'incomplete 6-byte' => ["\xFC\x80\x80\x80\x80"],
			'invalid lead byte 1' => ["\xFE\x80\x80"],
			'invalid lead byte 2' => ["\xFF"],
			'continuation byte as first 1' => ["\x80"],
			'continuation byte as first 2' => ["\xBF"],
		];
	}

	/**
	* Additional tests for valid 5-byte and 6-byte sequences returning non-zero values
	**/
	public function testOrdExtended(): void {
		// 5-byte valid sequence (non-zero)
		$this->assertSame(16777216, Unicode::ord("\xF9\x80\x80\x80\x80"));
		// 6-byte valid sequence (non-zero)
		$this->assertSame(1073741824, Unicode::ord("\xFD\x80\x80\x80\x80\x80"));
	}

	/**
	* Test Unicode::chr with various code points
	**/
	public function testChr(): void {
		$this->assertSame('A', Unicode::chr(65));
		$this->assertSame('a', Unicode::chr(97));
		$this->assertSame('А', Unicode::chr(1040));
		$this->assertSame("\x00", Unicode::chr(0));

		$this->assertSame("\xC2\xA0", Unicode::chr(160));
		$this->assertSame("\xE0\xA4\x85", Unicode::chr(2309));
	}

	public function testChrFourByte(): void {

		$this->assertSame("\xF0\x9F\x98\x80", Unicode::chr(128512));
	}

	/**
	* Test round-trip: chr(ord(c)) == c
	**/
	public function testRoundTrip(): void {
		$this->assertSame('A', Unicode::chr(Unicode::ord('A')));
		$this->assertSame('z', Unicode::chr(Unicode::ord('z')));

		$chars = ['А', 'а', 'Ω', '字', "😀"];
		foreach ($chars as $c) {
			$this->assertSame($c, Unicode::chr(Unicode::ord($c)));
		}
	}
}
