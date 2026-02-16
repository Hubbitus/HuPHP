<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\NullClass;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Vars\NullClass
 */
class NullClassTest extends TestCase {
	public function testCanBeInstantiated(): void {
		$nullClass = new NullClass();

		$this->assertInstanceOf(NullClass::class, $nullClass);
	}

	public function testIsNotEqualToNull(): void {
		$nullClass = new NullClass();

		$this->assertNotNull($nullClass);
		$this->assertNotSame(null, $nullClass);
	}

	public function testCanBeUsedAsTypeHint(): void {
		$this->processNullClass(new NullClass());
		$this->expectNotToPerformAssertions();
	}

	private function processNullClass(NullClass $obj): void {
		// Helper method for type hint testing
	}

	public function testMultipleInstancesAreDistinct(): void {
		$instance1 = new NullClass();
		$instance2 = new NullClass();

		$this->assertNotSame($instance1, $instance2);
	}

	public function testCanBeStoredInArray(): void {
		$nullClass = new NullClass();
		$array = [$nullClass];

		$this->assertCount(1, $array);
		$this->assertInstanceOf(NullClass::class, $array[0]);
	}
}
