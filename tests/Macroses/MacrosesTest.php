<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Macroses;

use Hubbitus\HuPHP\Exceptions\Variables\VariableIsNullException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use Hubbitus\HuPHP\Macro\Vars;
use Hubbitus\HuPHP\Macro\Unicode;
use Hubbitus\HuPHP\System\OS;
use PHPUnit\Framework\TestCase;

class MacrosesTest extends TestCase {
	public function testRequiredNotNullThrowsExceptionWhenNull(): void {
		$value = null;

		$this->expectException(VariableIsNullException::class);
		Vars::requiredNotNull($value);
	}

	public function testRequiredNotNullReturnsValueWhenNotNull(): void {
		$value = 'test';
		$result = Vars::requiredNotNull($value);

		$this->assertSame('test', $result);
	}

	public function testRequiredNotEmptyThrowsExceptionWhenEmpty(): void {
		$value = null;

		$this->expectException(VariableRequiredException::class);
		Vars::requiredNotEmpty($value);
	}

	public function testRequiredNotEmptyReturnsValueWhenNotEmpty(): void {
		$value = 'test';
		$result = Vars::requiredNotEmpty($value);

		$this->assertSame('test', $result);
	}

	public function testFirstMeaningReturnsFirstNonEmpty(): void {
		$result = Vars::firstMeaning(null, false, 0, 'test', 'another');

		$this->assertSame('test', $result);
	}

	public function testFirstMeaningReturnsNullWhenAllEmpty(): void {
		$result = Vars::firstMeaning(null, false, 0, '');

		$this->assertNull($result);
	}

	public function testFirstMeaningStringReturnsFirstNonEmptyString(): void {
		$result = Vars::firstMeaningString('', null, false, 'test', 'another');

		$this->assertSame('test', $result);
	}

	public function testFirstMeaningStringHandlesZeroAsString(): void {
		$result = Vars::firstMeaningString('', null, false, 0);

		$this->assertSame('0', $result);
	}

	public function testFirstMeaningStringHandlesArray(): void {
		$result = Vars::firstMeaningString('', null, false, [1, 2, 3]);

		$this->assertSame('Array(3)', $result);
	}

	public function testFirstMeaningStringReturnsEmptyStringWhenAllEmpty(): void {
		$result = Vars::firstMeaningString('', null, false);

		$this->assertSame('', $result);
	}

	public function testSurroundReturnsFormattedString(): void {
		$result = Vars::surround('test', '<', '>');

		$this->assertSame('<test>', $result);
	}

	public function testSurroundReturnsDefaultValueWhenEmpty(): void {
		$result = Vars::surround('', '<', '>', 'default');

		$this->assertSame('default', $result);
	}

	public function testSurroundHandlesNullPrefixSuffix(): void {
		$result = Vars::surround('test', null, null, null);

		$this->assertSame('test', $result);
	}

	public function testIssetReturnsTrueWhenKeyExists(): void {
		$array = ['key' => 'value'];
		$result = Vars::isset('key', $array);

		$this->assertTrue($result);
	}

	public function testIssetReturnsFalseWhenKeyNotExists(): void {
		$array = ['key' => 'value'];
		$result = Vars::isset('missing', $array);

		$this->assertFalse($result);
	}

	public function testIssetReturnsFalseForStringWithNonNumericKey(): void {
		$string = 'test';
		$result = Vars::isset('key', $string);

		$this->assertFalse($result);
	}

	public function testIssetReturnsTrueForStringWithNumericKey(): void {
		$string = 'test';
		$result = Vars::isset(0, $string);

		$this->assertTrue($result);
	}

	public function testSwapExchangesValues(): void {
		$a = 'first';
		$b = 'second';

		Vars::swap($a, $b);

		$this->assertEquals('second', $a);
		$this->assertEquals('first', $b);
	}

	public function testErrWritesToStderr(): void {
		$result = OS::err('test error');

		$this->assertGreaterThan(0, $result);
	}

	// Note: hitCount and exitCount tests are in MacrosTest with @runInSeparateProcess
	// because they use global static state that doesn't reset between tests

	public function testUnicodeUcfirst(): void {
		$result = Unicode::ucfirst('hello');

		$this->assertSame('Hello', $result);
	}

	public function testUnicodeUcfirstWithEmptyString(): void {
		$result = Unicode::ucfirst('');

		$this->assertSame('', $result);
	}

	public function testUnicodeWordwrap(): void {
		$result = Unicode::wordwrap('Hello World Test', 5, "\n");

		$this->assertStringContainsString("\n", $result);
	}

	public function testUnicodeOrd(): void {
		$result = Unicode::ord('A');

		$this->assertSame(65, $result);
	}

	public function testUnicodeChr(): void {
		$result = Unicode::chr(65);

		$this->assertSame('A', $result);
	}
}
