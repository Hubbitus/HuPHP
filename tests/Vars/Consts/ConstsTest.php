<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars\Consts;

use Hubbitus\HuPHP\Vars\Consts\Consts;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Vars\Consts\Consts
 */
class ConstsTest extends TestCase {
	public function testGet(): void {
		$constName = 'PHP_VERSION';
		$result = Consts::get($constName);

		$this->assertIsArray($result);
		$this->assertArrayHasKey($constName, $result);
		$this->assertEquals(PHP_VERSION, $result[$constName]);
	}

	public function testGetRegexpNoFilter(): void {
		$result = Consts::get_regexp();

		$this->assertIsArray($result);
		$this->assertNotEmpty($result);
	}

	public function testGetRegexpWithCategory(): void {
		$result = Consts::get_regexp('user');

		$this->assertIsArray($result);
	}

	public function testGetRegexpWithRegexp(): void {
		$result = Consts::get_regexp('', '@^PHP_@');

		$this->assertIsArray($result);
		foreach (array_keys($result) as $key) {
			$this->assertStringStartsWith('PHP_', is_string($key) ? $key : (is_array($result[$key]) ? array_keys($result[$key])[0] : ''));
		}
	}

	public function testGetRegexpNotCategorized(): void {
		$result = Consts::get_regexp('', '@.*@i', true);

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
		$result = Consts::getNameByValue(E_ALL, '', '@.*@i', true);

		$this->assertIsArray($result);
		$this->assertNotEmpty($result);
	}

	public function testGetNameByValueNotFound(): void {
		$result = Consts::getNameByValue('__NONEXISTENT_CONSTANT_VALUE__');

		$this->assertIsArray($result);
		$this->assertEmpty($result);
	}
}
