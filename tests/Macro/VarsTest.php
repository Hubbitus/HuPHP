<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Macro;

use Hubbitus\HuPHP\Macro\Vars;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRequiredException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableIsNullException;
use PHPUnit\Framework\TestCase;

/**
* Tests for Vars utility class
**/
class VarsTest extends TestCase {
	/**
	* Test Vars::firstMeaning
	**/
	public function testFirstMeaning(): void {
		// Non-empty values
		$this->assertSame('hello', Vars::firstMeaning('', 'hello', 'world'));
		$this->assertSame(42, Vars::firstMeaning(0, false, null, 42));
		$this->assertSame([1, 2], Vars::firstMeaning([], [1, 2]));
		$this->assertSame(true, Vars::firstMeaning(false, 0, '', null, true));

		// All empty
		$this->assertNull(Vars::firstMeaning('', 0, false, null, []));
	}

	/**
	* Test Vars::firstMeaningString
	**/
	public function testFirstMeaningString(): void {
		// Standard strings
		$this->assertSame('hello', Vars::firstMeaningString('', 'hello', 'world'));
		$this->assertSame('0', Vars::firstMeaningString(0, false, 42)); // 0 is treated as non-empty string "0"
		$this->assertSame('42', Vars::firstMeaningString(false, '', 42)); // 42 after false and empty string

		// Arrays return "Array(N)"
		$this->assertSame('Array(3)', Vars::firstMeaningString([1, 2, 3])); // Non-empty array first
		$this->assertSame('Array(0)', Vars::firstMeaningString([])); // Empty array
		$this->assertSame('Array(0)', Vars::firstMeaningString([], [1, 2, 3])); // Empty array is first, so returns "Array(0)"

		// Boolean true is treated as empty (returns empty string)
		$this->assertSame('', Vars::firstMeaningString(true, false, 0));
		$this->assertSame('0', Vars::firstMeaningString(0, false));

		// All empty (no non-empty values, no arrays, no 0)
		$this->assertSame('', Vars::firstMeaningString('', false, null, true));
	}

	/**
	* Test Vars::requiredNotEmpty throws exception for empty values
	**/
	public function testRequiredNotEmpty(): void {
		// Non-empty values pass through
		$this->assertSame('hello', Vars::requiredNotEmpty('hello'));
		$this->assertSame(42, Vars::requiredNotEmpty(42));
		$this->assertSame([1], Vars::requiredNotEmpty([1]));

		// Empty values throw
		$this->expectException(VariableRequiredException::class);
		Vars::requiredNotEmpty('');
	}

	/**
	* Test Vars::requiredNotNull throws exception for null
	**/
	public function testRequiredNotNull(): void {
		// Non-null values pass through
		$this->assertSame('hello', Vars::requiredNotNull('hello'));
		$this->assertSame(0, Vars::requiredNotNull(0));
		$this->assertSame(false, Vars::requiredNotNull(false));

		// Null throws
		$this->expectException(VariableIsNullException::class);
		Vars::requiredNotNull(null);
	}

	/**
	* Test Vars::swap
	**/
	public function testSwap(): void {
		$a = 'first';
		$b = 'second';

		Vars::swap($a, $b);

		$this->assertSame('second', $a);
		$this->assertSame('first', $b);

		// Test with numbers
		$x = 10;
		$y = 20;
		Vars::swap($x, $y);
		$this->assertSame(20, $x);
		$this->assertSame(10, $y);

		// Test with arrays
		$arr1 = [1, 2];
		$arr2 = [3, 4];
		Vars::swap($arr1, $arr2);
		$this->assertSame([3, 4], $arr1);
		$this->assertSame([1, 2], $arr2);
	}

	/**
	* Test Vars::surround
	**/
	public function testSurround(): void {
		// Non-empty string with prefix and suffix
		$this->assertSame('[hello]', Vars::surround('hello', '[', ']'));
		$this->assertSame('---test---', Vars::surround('test', '---', '---'));

		// Empty string returns default
		$this->assertSame('default', Vars::surround('', '[', ']', 'default'));
		$this->assertSame('def', Vars::surround(null, null, null, 'def'));

		// Empty string (empty string is considered empty)
		$this->assertSame('fallback', Vars::surround('', '<<', '>>', 'fallback'));

		// String with only prefix or suffix
		$this->assertSame('prefixtest', Vars::surround('test', 'prefix', ''));
		$this->assertSame('testsuffix', Vars::surround('test', '', 'suffix'));
	}

	/**
	* Test Vars::isset for array and string keys
	**/
	public function testIsset(): void {
		// Array with numeric key
		$arr = [0 => 'zero', 1 => 'one', 'name' => 'test'];
		$this->assertTrue(Vars::isset(0, $arr));
		$this->assertTrue(Vars::isset(1, $arr));
		$this->assertTrue(Vars::isset('name', $arr));
		$this->assertFalse(Vars::isset(2, $arr));
		$this->assertFalse(Vars::isset('nonexistent', $arr));

		// String (numeric keys only)
		$str = 'hello';
		$this->assertTrue(Vars::isset(0, $str)); // First character exists
		$this->assertTrue(Vars::isset(1, $str)); // Second character
		$this->assertFalse(Vars::isset(10, $str)); // Out of bounds
		$this->assertFalse(Vars::isset('h', $str)); // Non-numeric key on string returns false
	}
}
