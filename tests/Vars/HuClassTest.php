<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars;
use Hubbitus\HuPHP\System\OutputType;

use Hubbitus\HuPHP\Vars\HuClass;
use PHPUnit\Framework\TestCase;

/**
 * Test class for casting
 */
class TestClassForCast {
    public $data;
}

/**
 * @covers \Hubbitus\HuPHP\Vars\HuClass
 */
class HuClassTest extends TestCase {
    public function testClassExists(): void {
        $this->assertTrue(class_exists(HuClass::class));
    }

    public function testClassIsAbstract(): void {
        $reflection = new \ReflectionClass(HuClass::class);
        $this->assertTrue($reflection->isAbstract());
    }

    public function testCloningMethodExists(): void {
        $this->assertTrue(method_exists(HuClass::class, 'cloning'));
    }

    public function testCloningCreatesCopy(): void {
        $obj = new \stdClass();
        $obj->value = 'original';

        $clone = HuClass::cloning($obj);

        $this->assertEquals($obj->value, $clone->value);
        $this->assertNotSame($obj, $clone);
    }

    public function testCloningModifiesCloneIndependently(): void {
        $obj = new \stdClass();
        $obj->value = 'original';

        $clone = HuClass::cloning($obj);
        $clone->value = 'modified';

        $this->assertEquals('original', $obj->value);
        $this->assertEquals('modified', $clone->value);
    }

    public function testCloningPreservesAllProperties(): void {
        $obj = new \stdClass();
        $obj->prop1 = 'value1';
        $obj->prop2 = 'value2';
        $obj->prop3 = ['a', 'b', 'c'];

        $clone = HuClass::cloning($obj);

        $this->assertEquals('value1', $clone->prop1);
        $this->assertEquals('value2', $clone->prop2);
        $this->assertEquals(['a', 'b', 'c'], $clone->prop3);
    }

    public function testReinterpretCastMethodExists(): void {
        $this->assertTrue(method_exists(HuClass::class, 'reinterpret_cast'));
    }

    public function testReinterpretCastChangesClassName(): void {
        $source = new \stdClass();
        $source->value = 'test';

        // Cast stdClass to a different class
        $casted = HuClass::reinterpret_cast(\stdClass::class, $source);

        $this->assertInstanceOf(\stdClass::class, $casted);
        $this->assertEquals('test', $casted->value);
    }

    public function testReinterpretCastPreservesProperties(): void {
        $source = new \stdClass();
        $source->prop1 = 'value1';
        $source->prop2 = 42;

        $casted = HuClass::reinterpret_cast(\stdClass::class, $source);

        $this->assertEquals('value1', $casted->prop1);
        $this->assertEquals(42, $casted->prop2);
    }

    public function testReinterpretCastWithCustomClass(): void {
        $source = new \stdClass();
        $source->data = 'test data';

        // Cast to a custom class
        $casted = HuClass::reinterpret_cast(TestClassForCast::class, $source);

        $this->assertInstanceOf(TestClassForCast::class, $casted);
        $this->assertEquals('test data', $casted->data);
    }
}
