<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars\Consts;

use Hubbitus\HuPHP\Vars\Consts\Consts;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Vars\Consts\Consts
**/
class ConstsTest extends TestCase {
	public function testGet(): void {
		$constName = 'PHP_VERSION';
		$result = Consts::get($constName);

		$this->assertIsArray($result);
		$this->assertArrayHasKey($constName, $result);
		$this->assertEquals(PHP_VERSION, $result[$constName]);
	}

	public function testGetRegexpNoFilter(): void {
		$result = Consts::getByRegexp();

		$this->assertIsArray($result);
		$this->assertNotEmpty($result);
	}

	public function testGetRegexpWithCategory(): void {
		// Test with specific category - should return constants from that category
		$result = Consts::getByRegexp('@.*@i', 'Core');

		$this->assertIsArray($result);
		$this->assertNotEmpty($result);
	}

	public function testGetRegexpWithNonExistentCategory(): void {
		// Test with non-existent category - should return empty array
		$result = Consts::getByRegexp('@.*@i', 'NonExistentCategory123');

		$this->assertIsArray($result);
		$this->assertEmpty($result);
	}

	public function testGetRegexpWithSpecificRegexp(): void {
		// Test with specific regexp pattern
		$result = Consts::getByRegexp('@^PHP_@', 'Core');

		$this->assertIsArray($result);
		// All keys should start with PHP_
		foreach (array_keys($result) as $key) {
			$this->assertStringStartsWith('PHP_', $key);
		}
	}

	public function testGetRegexpWithRegexp(): void {
		$result = Consts::getByRegexp('', '@^PHP_@');

		$this->assertIsArray($result);
		foreach (array_keys($result) as $key) {
			$this->assertStringStartsWith('PHP_', is_string($key) ? $key : (is_array($result[$key]) ? array_keys($result[$key])[0] : ''));
		}
	}

	public function testGetRegexpNotCategorized(): void {
		// When category is null, returns all constants from all categories
		$result = Consts::getByRegexp('@.*@i', null);

		$this->assertIsArray($result);
		$this->assertNotEmpty($result);
	}

	public function testGetNameByValue(): void {
		$result = Consts::getNameByValue(E_ALL);

		$this->assertIsArray($result);
		$this->assertNotEmpty($result);
		$this->assertContains('E_ALL', array_keys($result));
	}

	public function testGetNameByValueWithCategory(): void {
		$result = Consts::getNameByValue(E_ALL, 'Core');

		$this->assertIsArray($result);
	}

	public function testGetNameByValueWithRegexp(): void {
		$result = Consts::getNameByValue(1, '', '@^PHP_@');

		$this->assertIsArray($result);
	}

	public function testGetNameByValueNotCategorized(): void {
		// When category is null, searches all constants from all categories
		$result = Consts::getNameByValue(E_ALL, '@.*@i', null);

		$this->assertIsArray($result);
		$this->assertNotEmpty($result);
	}

	public function testGetNameByValueNotFound(): void {
		$result = Consts::getNameByValue('__NONEXISTENT_CONSTANT_VALUE__');

		$this->assertIsArray($result);
		$this->assertEmpty($result);
	}

	public function testGetNameByValueWithCategoryAndRegexp(): void {
		// Test with both category and regexp
		// Search for value 1 (E_ERROR) in Core category
		$result = Consts::getNameByValue(1, '@.*@i', 'Core');

		$this->assertIsArray($result);
		// Should find constants with value 1
		$this->assertNotEmpty($result);
	}
}
