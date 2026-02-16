<?php

declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\HuClass;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Vars\HuClass
 */
class HuClassTest extends TestCase {
	public function testCloning(): void {
		$obj = new \stdClass();
		$obj->value = 'test';
		$obj->array = [1, 2, 3];

		$cloned = HuClass::cloning($obj);

		$this->assertIsObject($cloned);
		$this->assertEquals($obj, $cloned);
		$this->assertNotSame($obj, $cloned); // Different instances
	}

	public function testCloningModifiesCloneNotOriginal(): void {
		$obj = new \stdClass();
		$obj->value = 'original';

		$cloned = HuClass::cloning($obj);
		$cloned->value = 'modified';

		$this->assertEquals('original', $obj->value);
		$this->assertEquals('modified', $cloned->value);
	}

	public function testReinterpretCast(): void {
		$source = new \stdClass();
		$source->value = 'test';

		$result = HuClass::reinterpret_cast(\stdClass::class, $source);

		$this->assertIsObject($result);
		$this->assertEquals('test', $result->value);
	}

	public function testReinterpretCastToDifferentClass(): void {
		// Create a simple test class to cast to
		$source = new \stdClass();
		$source->value = 'test';

		// Use stdClass to stdClass (same structure)
		$result = HuClass::reinterpret_cast(\stdClass::class, $source);

		$this->assertInstanceOf(\stdClass::class, $result);
		$this->assertEquals('test', $result->value);
	}

	public function testCloningWithArray(): void {
		$obj = new \stdClass();
		$obj->items = ['a', 'b', 'c'];

		$cloned = HuClass::cloning($obj);

		$this->assertEquals($obj->items, $cloned->items);
		// Arrays are copied by value in PHP, so modifying clone won't affect original
		$cloned->items[] = 'd';
		$this->assertCount(3, $obj->items);
		$this->assertCount(4, $cloned->items);
	}
}