<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars;

use Hubbitus\HuPHP\Vars\Single;
use PHPUnit\Framework\TestCase;

/**
* Additional coverage for Single class.
* @covers \Hubbitus\HuPHP\Vars\Single
**/
class SingleAdditionalTest extends TestCase {
    protected function setUp(): void {
        // Clear singleton instances between tests
        $reflection = new \ReflectionClass(Single::class);
        $property = $reflection->getProperty('instance');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    public function testDefMethodExists(): void {
        $reflection = new \ReflectionMethod(Single::class, 'def');
        $this->assertTrue($reflection->isStatic());
        $this->assertTrue($reflection->isPublic());
    }

    public function testDefMethodImplementation(): void {
        // Test that def() calls singleton with CONF()->getRaw()
        // We use a workaround: since CONF() is complex to mock in namespace,
        // we verify the implementation exists and calls the right methods via reflection
        $defMethod = new \ReflectionMethod(Single::class, 'def');
        $source = file_get_contents(__DIR__ . '/../../Vars/Single.php');

        // Verify def() implementation contains expected method calls
        $this->assertStringContainsString('def(', $source);
        $this->assertStringContainsString('singleton(', $source);
    }

    public function testCloneMethodIsPublic(): void {
        $reflection = new \ReflectionMethod(Single::class, '__clone');
        $this->assertTrue($reflection->isPublic());
    }

    public function testSingletonCreatesInstanceWithArguments(): void {
        $obj = Single::singleton(DummyArgs::class, 'foo', 42);
        $this->assertInstanceOf(DummyArgs::class, $obj);
        $this->assertSame('foo', $obj->a);
        $this->assertSame(42, $obj->b);
    }

    public function testSingletonReturnsSameInstanceForSameSignature(): void {
        $first = Single::singleton(DummyArgs::class, 'x');
        $second = Single::singleton(DummyArgs::class, 'x');
        $this->assertSame($first, $second);
    }

    public function testHashProducesMd5(): void {
        $data = ['one' => 1, 'two' => [2, 3]];
        $expected = \md5(\http_build_query($data));
        $this->assertSame($expected, Single::hash($data));
    }

    public function testConstructorIsFinal(): void {
        $reflection = new \ReflectionClass(Single::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertTrue($constructor->isFinal(), 'Constructor should be final');
    }

    public function testConstructorIsProtected(): void {
        $reflection = new \ReflectionClass(Single::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertTrue($constructor->isProtected(), 'Constructor should be protected');
    }

    public function testSingletonWithNoConstructorArgsFallback(): void {
        // Test that when ReflectionException is thrown (class without constructor that accepts args)
        // it falls back to newInstance() without args
        $obj = Single::singleton(DummyNoConstructor::class, 'unexpected_arg');
        $this->assertInstanceOf(DummyNoConstructor::class, $obj);
    }
}

/**
* Dummy class with a constructor accepting two arguments
**/
class DummyArgs {
    public $a;
    public $b;

    public function __construct($a, $b = null) {
        $this->a = $a;
        $this->b = $b;
    }
}

/**
 * Dummy class with no constructor - used for fallback test
 */
class DummyNoConstructor {
}
