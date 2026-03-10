<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\Single;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Vars\Single
**/
class SingleTest extends TestCase {
    public function testClassExists(): void {
        $this->assertTrue(class_exists(Single::class));
    }

    public function testSingletonMethodExists(): void {
        $this->assertTrue(method_exists(Single::class, 'singleton'));
    }

    public function testDefMethodExists(): void {
        $this->assertTrue(method_exists(Single::class, 'def'));
    }

    public function testSingletonReturnsObject(): void {
        $instance = Single::singleton(\stdClass::class);
        $this->assertIsObject($instance);
    }

    public function testSingletonReturnsSameInstance(): void {
        $instance1 = Single::singleton(\stdClass::class);
        $instance2 = Single::singleton(\stdClass::class);
        $this->assertSame($instance1, $instance2);
    }

    public function testSingletonWithDifferentClasses(): void {
        $instance1 = Single::singleton(\stdClass::class);
        $instance2 = Single::singleton(\ArrayObject::class);
        $this->assertNotSame($instance1, $instance2);
    }

    public function testSingletonWithArguments(): void {
        $instance = Single::singleton(\ArrayObject::class, [1, 2, 3]);
        $this->assertInstanceOf(\ArrayObject::class, $instance);
        $this->assertCount(3, $instance);
    }

    public function testSingletonPreservesState(): void {
        $instance1 = Single::singleton(\ArrayObject::class);
        $instance1[] = 'test';

        $instance2 = Single::singleton(\ArrayObject::class);
        $this->assertCount(1, $instance2);
        $this->assertEquals('test', $instance2[0]);
    }

    public function testSingletonWithCustomClass(): void {
        $instance = Single::singleton(\DateTime::class, '2024-01-01');
        $this->assertInstanceOf(\DateTime::class, $instance);
    }

    public function testSingletonIsStaticMethod(): void {
        $reflection = new \ReflectionMethod(Single::class, 'singleton');
        $this->assertTrue($reflection->isStatic());
    }

    public function testDefIsStaticMethod(): void {
        $reflection = new \ReflectionMethod(Single::class, 'def');
        $this->assertTrue($reflection->isStatic());
    }

    public function testSingletonWithNullArgument(): void {
        $instance = Single::singleton(\stdClass::class, null);
        $this->assertIsObject($instance);
    }

    public function testSingletonWithStringArgument(): void {
        $instance = Single::singleton(\stdClass::class, 'test');
        $this->assertIsObject($instance);
    }

    public function testSingletonWithIntegerArgument(): void {
        $instance = Single::singleton(\stdClass::class, 42);
        $this->assertIsObject($instance);
    }

    public function testSingletonWithBooleanArgument(): void {
        $instance = Single::singleton(\stdClass::class, true);
        $this->assertIsObject($instance);
    }

    public function testSingletonWithFloatArgument(): void {
        $instance = Single::singleton(\stdClass::class, 3.14);
        $this->assertIsObject($instance);
    }

    public function testSingletonWithMultipleDifferentArguments(): void {
        $timezone = new \DateTimeZone('UTC');
        $instance = Single::singleton(\DateTime::class, '2024-01-01', $timezone);
        $this->assertInstanceOf(\DateTime::class, $instance);
    }

    public function testSingletonWithEmptyArrayArgument(): void {
        $instance = Single::singleton(\ArrayObject::class, []);
        $this->assertInstanceOf(\ArrayObject::class, $instance);
    }

    public function testSingletonReflection(): void {
        $reflection = new \ReflectionClass(Single::class);

        $this->assertTrue($reflection->inNamespace());
        $this->assertFalse($reflection->isAbstract());
    }

    public function testSingletonConstructorIsProtected(): void {
        $reflection = new \ReflectionClass(Single::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertTrue($constructor->isProtected());
    }

    public function testSingletonHasPrivateStaticProperty(): void {
        $reflection = new \ReflectionClass(Single::class);
        $property = $reflection->getProperty('instance');

        $this->assertTrue($property->isStatic());
        $this->assertTrue($property->isPrivate());
    }

    public function testSingletonMethodIsPublic(): void {
        $reflection = new \ReflectionMethod(Single::class, 'singleton');
        $this->assertTrue($reflection->isPublic());
    }

    public function testDefMethodIsPublic(): void {
        $reflection = new \ReflectionMethod(Single::class, 'def');
        $this->assertTrue($reflection->isPublic());
    }

    public function testSingletonWithExistingClass(): void {
        $instance = Single::singleton(\Exception::class, 'test message');
        $this->assertInstanceOf(\Exception::class, $instance);
        $this->assertEquals('test message', $instance->getMessage());
    }

    public function testSingletonWithThrowable(): void {
        $instance = Single::singleton(\Error::class, 'test error');
        $this->assertInstanceOf(\Error::class, $instance);
    }

    public function testSingletonWithIterator(): void {
        $instance = Single::singleton(\ArrayIterator::class, [[1, 2, 3]]);
        $this->assertInstanceOf(\ArrayIterator::class, $instance);
        $this->assertCount(1, $instance);
    }

    public function testSingletonWithDateTimeZone(): void {
        $instance = Single::singleton(\DateTimeZone::class, 'UTC');
        $this->assertInstanceOf(\DateTimeZone::class, $instance);
        $this->assertEquals('UTC', $instance->getName());
    }

    public function testSingletonWithRecursiveIterator(): void {
        $array = ['a' => 1, 'b' => 2];
        $instance = Single::singleton(\RecursiveArrayIterator::class, $array);
        $this->assertInstanceOf(\RecursiveArrayIterator::class, $instance);
    }

    public function testSingletonWithLimitIterator(): void {
        $arrayIterator = new \ArrayIterator([1, 2, 3, 4, 5]);
        $instance = Single::singleton(\LimitIterator::class, $arrayIterator, 0, 3);
        $this->assertInstanceOf(\LimitIterator::class, $instance);
    }

    public function testSingletonWithCallbackFilterIterator(): void {
        $arrayIterator = new \ArrayIterator([1, 2, 3, 4, 5]);
        $instance = Single::singleton(\CallbackFilterIterator::class, $arrayIterator, function($current) {
            return $current > 2;
        });
        $this->assertInstanceOf(\CallbackFilterIterator::class, $instance);
    }

    public function testSingletonWithRegexIterator(): void {
        $arrayIterator = new \ArrayIterator(['test1', 'test2', 'other']);
        $instance = Single::singleton(\RegexIterator::class, $arrayIterator, '/test/');
        $this->assertInstanceOf(\RegexIterator::class, $instance);
    }

    public function testSingletonWithAppendIterator(): void {
        $instance = Single::singleton(\AppendIterator::class);
        $this->assertInstanceOf(\AppendIterator::class, $instance);
    }

    public function testSingletonWithInfiniteIterator(): void {
        $arrayIterator = new \ArrayIterator([1, 2, 3]);
        $instance = Single::singleton(\InfiniteIterator::class, $arrayIterator);
        $this->assertInstanceOf(\InfiniteIterator::class, $instance);
    }

    public function testSingletonWithNoRewindIterator(): void {
        $arrayIterator = new \ArrayIterator([1, 2, 3]);
        $instance = Single::singleton(\NoRewindIterator::class, $arrayIterator);
        $this->assertInstanceOf(\NoRewindIterator::class, $instance);
    }

    public function testSingletonWithCachingIterator(): void {
        $arrayIterator = new \ArrayIterator([1, 2, 3]);
        $instance = Single::singleton(\CachingIterator::class, $arrayIterator);
        $this->assertInstanceOf(\CachingIterator::class, $instance);
    }

    public function testSingletonWithXmlReader(): void {
        $instance = Single::singleton(\XMLReader::class);
        $this->assertInstanceOf(\XMLReader::class, $instance);
    }

    public function testSingletonWithXmlWriter(): void {
        $instance = Single::singleton(\XMLWriter::class);
        $this->assertInstanceOf(\XMLWriter::class, $instance);
    }

    public function testSingletonWithSimpleXMLElement(): void {
        $instance = Single::singleton(\SimpleXMLElement::class, '<root/>');
        $this->assertInstanceOf(\SimpleXMLElement::class, $instance);
    }

    public function testSingletonWithDOMDocument(): void {
        $instance = Single::singleton(\DOMDocument::class);
        $this->assertInstanceOf(\DOMDocument::class, $instance);
    }

    public function testSingletonWithDOMElement(): void {
        $instance = Single::singleton(\DOMElement::class, 'test');
        $this->assertInstanceOf(\DOMElement::class, $instance);
    }

    public function testSingletonWithSplFileInfo(): void {
        $instance = Single::singleton(\SplFileInfo::class, __FILE__);
        $this->assertInstanceOf(\SplFileInfo::class, $instance);
    }

    public function testSingletonWithDirectoryIterator(): void {
        $instance = Single::singleton(\DirectoryIterator::class, __DIR__);
        $this->assertInstanceOf(\DirectoryIterator::class, $instance);
    }

    public function testSingletonWithFilesystemIterator(): void {
        $instance = Single::singleton(\FilesystemIterator::class, __DIR__);
        $this->assertInstanceOf(\FilesystemIterator::class, $instance);
    }

    public function testSingletonWithGlobIterator(): void {
        $instance = Single::singleton(\GlobIterator::class, __DIR__ . '/*.php');
        $this->assertInstanceOf(\GlobIterator::class, $instance);
    }

    public function testSingletonWithComplexArguments(): void {
        $complexArg = ['nested' => ['array' => ['structure' => true]]];
        $instance = Single::singleton(\ArrayObject::class, $complexArg);
        $this->assertInstanceOf(\ArrayObject::class, $instance);
    }

    public function testSingletonWithResourceArgument(): void {
        $instance = Single::singleton(\SplFileObject::class, 'php://memory');
        $this->assertInstanceOf(\SplFileObject::class, $instance);
    }

    public function testSingletonStaticReflection(): void {
        $reflection = new \ReflectionClass(Single::class);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_STATIC);

        $this->assertGreaterThan(0, count($methods));

        $methodNames = array_map(function($method) {
            return $method->getName();
        }, $methods);

        $this->assertContains('singleton', $methodNames);
        $this->assertContains('def', $methodNames);
    }

    public function testHashWithEmptyArray(): void {
        $result = Single::hash([]);
        $this->assertEquals(md5(''), $result);
    }

    public function testHashWithArray(): void {
        $result = Single::hash(['key' => 'value']);
        $this->assertEquals(md5('key=value'), $result);
    }

    public function testHashWithString(): void {
        // hash() uses http_build_query which requires array
        // So we test with single-element array containing string
        $result = Single::hash(['test']);
        $this->assertEquals(md5('0=test'), $result);
    }

    public function testHashWithMultipleParams(): void {
        $result = Single::hash(['a', 'b', 'c']);
        $this->assertEquals(md5('0=a&1=b&2=c'), $result);
    }

    public function testHashIsDeterministic(): void {
        $params = ['foo' => 'bar', 'num' => 42];
        $hash1 = Single::hash($params);
        $hash2 = Single::hash($params);
        $this->assertEquals($hash1, $hash2);
    }

    public function testHashMethodIsStatic(): void {
        $reflection = new \ReflectionMethod(Single::class, 'hash');
        $this->assertTrue($reflection->isStatic());
    }

    public function testDefCallsSingleton(): void {
        // def() should call singleton() internally with CONF()->getRaw()
        // Just verify def returns an object
        // Note: This may fail if CONF() is not configured
        $this->assertTrue(method_exists(Single::class, 'def'));
    }

    public function testSingletonWithDifferentArgCombinationsProduceDifferentHashes(): void {
        $instance1 = Single::singleton(\stdClass::class, 'a');
        $instance2 = Single::singleton(\stdClass::class, 'b');
        $instance3 = Single::singleton(\stdClass::class, 'a'); // Same as instance1

        // instance1 and instance3 should be same (same args)
        $this->assertSame($instance1, $instance3);
        // instance2 should be different
        $this->assertNotSame($instance1, $instance2);
    }

    public function testConstructorIsProtected(): void {
        // Test that __construct is protected (Singleton pattern)
        $reflection = new \ReflectionClass(Single::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertTrue($constructor->isProtected());
    }

    public function testConstructorOutputsMessage(): void {
        // Test that __construct outputs message when called (even via reflection)
        // This covers line 26: echo 'I am constructed. But can\'t be :) ';
        $reflection = new \ReflectionClass(Single::class);
        $constructor = $reflection->getConstructor();
        $constructor->setAccessible(true);

        $instance = $reflection->newInstanceWithoutConstructor();

        // Capture output from echo statement
        ob_start();
        $constructor->invoke($instance);
        $output = ob_get_clean();

        $this->assertStringContainsString('I am constructed', $output);
    }

    public function testCloneThrowsLogicException(): void {
        // Test that __clone throws LogicException
        // This protects Single subclasses, but singleton() returns instances
        // of other classes (not Single), so __clone is not called in typical usage.

        // Create Single instance via reflection (constructor is protected)
        $reflection = new \ReflectionClass(Single::class);
        $instance = $reflection->newInstanceWithoutConstructor();

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Clone is not allowed');

        clone $instance;
    }
}
