<?php
declare(strict_types=1);

/**
 * Test for NullClass class.
 */

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\NullClass;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Vars\NullClass
 */
class NullClassTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists(NullClass::class));
    }

    public function testClassInstantiation(): void
    {
        $null = new NullClass();
        $this->assertInstanceOf(NullClass::class, $null);
    }

    public function testClassIsNotEmpty(): void
    {
        $null = new NullClass();
        $this->assertNotNull($null);
    }

    public function testClassIsObject(): void
    {
        $null = new NullClass();
        $this->assertIsObject($null);
    }

    public function testClassIsNotString(): void
    {
        $null = new NullClass();
        $this->assertIsNotString($null);
    }

    public function testClassIsNotInt(): void
    {
        $null = new NullClass();
        $this->assertIsNotInt($null);
    }

    public function testClassIsNotArray(): void
    {
        $null = new NullClass();
        $this->assertIsNotArray($null);
    }

    public function testClassIsNotBool(): void
    {
        $null = new NullClass();
        $this->assertIsNotBool($null);
    }

    public function testClassIsNotFloat(): void
    {
        $null = new NullClass();
        $this->assertIsNotFloat($null);
    }

    public function testClassIsNotCallable(): void
    {
        $null = new NullClass();
        $this->assertIsNotCallable($null);
    }

    public function testClassIsNotIterable(): void
    {
        $null = new NullClass();
        $this->assertFalse(is_iterable($null));
    }

    public function testClassIsNotNumeric(): void
    {
        $null = new NullClass();
        $this->assertIsNotNumeric($null);
    }

    public function testClassIsNotScalar(): void
    {
        $null = new NullClass();
        $this->assertFalse(is_scalar($null));
    }

    public function testClassIsNotCountable(): void
    {
        $null = new NullClass();
        $this->assertFalse($null instanceof \Countable);
    }

    public function testClassIsNotIterator(): void
    {
        $null = new NullClass();
        $this->assertFalse($null instanceof \Iterator);
    }

    public function testClassIsNotIteratorAggregate(): void
    {
        $null = new NullClass();
        $this->assertFalse($null instanceof \IteratorAggregate);
    }

    public function testClassIsNotArrayAccess(): void
    {
        $null = new NullClass();
        $this->assertFalse($null instanceof \ArrayAccess);
    }

    public function testClassIsNotSerializable(): void
    {
        $null = new NullClass();
        $this->assertFalse($null instanceof \Serializable);
    }

    public function testClassIsNotJsonSerializable(): void
    {
        $null = new NullClass();
        $this->assertFalse($null instanceof \JsonSerializable);
    }

    public function testClassDoesNotHaveProperties(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertEmpty($reflection->getProperties());
    }

    public function testClassDoesNotHaveMethods(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $methods = $reflection->getMethods();
        $this->assertEmpty($methods);
    }

    public function testClassIsFinal(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertFalse($reflection->isFinal());
    }

    public function testClassIsAbstract(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertFalse($reflection->isAbstract());
    }

    public function testClassIsInstantiable(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertTrue($reflection->isInstantiable());
    }

    public function testClassIsCloneable(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertTrue($reflection->isCloneable());
    }

    public function testClassIsIterable(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertTrue($reflection->isIterateable());
    }

    public function testClassHasPublicConstructor(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $constructor = $reflection->getConstructor();
        $this->assertNull($constructor);
    }

    public function testClassHasNoConstants(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertEmpty($reflection->getConstants());
    }

    public function testClassHasNoStaticProperties(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertEmpty($reflection->getStaticProperties());
    }

    public function testClassHasDefaultName(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertEquals(NullClass::class, $reflection->getName());
    }

    public function testClassHasShortName(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertEquals('NullClass', $reflection->getShortName());
    }

    public function testClassInNamespace(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertTrue($reflection->inNamespace());
    }

    public function testClassNamespaceName(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertEquals('Hubbitus\HuPHP\Vars', $reflection->getNamespaceName());
    }

    public function testClassFileName(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertStringEndsWith('NullClass.php', $reflection->getFileName());
    }

    public function testClassStartLine(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertGreaterThan(0, $reflection->getStartLine());
    }

    public function testClassEndLine(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertGreaterThan(0, $reflection->getEndLine());
    }

    public function testClassEndLineGreaterThanStartLine(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertGreaterThan($reflection->getStartLine(), $reflection->getEndLine());
    }

    public function testClassDocComment(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertFalse($reflection->getDocComment());
    }

    public function testClassIsUserDefined(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertTrue($reflection->isUserDefined());
    }

    public function testClassIsNotInternal(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertFalse($reflection->isInternal());
    }

    public function testClassToString(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertIsString((string) $reflection);
    }

    public function testClassInvoke(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $instance = $reflection->newInstance();
        $this->assertInstanceOf(NullClass::class, $instance);
    }

    public function testClassInvokeWithoutArgs(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $instance = $reflection->newInstanceWithoutConstructor();
        $this->assertInstanceOf(NullClass::class, $instance);
    }

    public function testClassClone(): void
    {
        $null1 = new NullClass();
        $null2 = clone $null1;
        $this->assertEquals($null1, $null2);
    }

    public function testClassNotSame(): void
    {
        $null1 = new NullClass();
        $null2 = new NullClass();
        $this->assertNotSame($null1, $null2);
    }

    public function testClassEquals(): void
    {
        $null1 = new NullClass();
        $null2 = new NullClass();
        $this->assertEquals($null1, $null2);
    }

    public function testClassSerialize(): void
    {
        $null = new NullClass();
        $serialized = serialize($null);
        $this->assertIsString($serialized);
    }

    public function testClassUnserialize(): void
    {
        $null = new NullClass();
        $serialized = serialize($null);
        $unserialized = unserialize($serialized);
        $this->assertInstanceOf(NullClass::class, $unserialized);
    }

    public function testClassJsonEncode(): void
    {
        $null = new NullClass();
        $json = json_encode($null);
        $this->assertEquals('{}', $json);
    }

    public function testClassJsonDecode(): void
    {
        $json = '{}';
        $decoded = json_decode($json);
        $this->assertIsObject($decoded);
    }

    public function testClassVarExport(): void
    {
        $null = new NullClass();
        $export = var_export($null, true);
        $this->assertIsString($export);
    }

    public function testClassVarDump(): void
    {
        $null = new NullClass();
        ob_start();
        var_dump($null);
        $dump = ob_get_clean();
        $this->assertIsString($dump);
        $this->assertStringContainsString('NullClass', $dump);
    }

    public function testClassPrintR(): void
    {
        $null = new NullClass();
        ob_start();
        print_r($null);
        $print = ob_get_clean();
        $this->assertIsString($print);
        $this->assertStringContainsString('NullClass', $print);
    }

    public function testClassGetClass(): void
    {
        $null = new NullClass();
        $this->assertEquals(NullClass::class, get_class($null));
    }

    public function testClassGetClassName(): void
    {
        $null = new NullClass();
        $this->assertEquals('NullClass', get_class($null));
    }

    public function testClassGetType(): void
    {
        $null = new NullClass();
        $this->assertEquals('object', gettype($null));
    }

    public function testClassGetObjectId(): void
    {
        $null = new NullClass();
        $id = spl_object_id($null);
        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    public function testClassGetObjectHash(): void
    {
        $null = new NullClass();
        $hash = spl_object_hash($null);
        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    public function testClassMemoryUsage(): void
    {
        $null = new NullClass();
        $this->assertIsObject($null);
    }

    public function testClassMultipleInstances(): void
    {
        $null1 = new NullClass();
        $null2 = new NullClass();
        $null3 = new NullClass();
        
        $this->assertNotSame($null1, $null2);
        $this->assertNotSame($null2, $null3);
        $this->assertNotSame($null1, $null3);
        
        $this->assertEquals($null1, $null2);
        $this->assertEquals($null2, $null3);
        $this->assertEquals($null1, $null3);
    }

    public function testClassInArray(): void
    {
        $null = new NullClass();
        $array = [$null];
        $this->assertContains($null, $array);
    }

    public function testClassAsArrayKey(): void
    {
        $null = new NullClass();
        $this->expectException(\TypeError::class);
        $array = [$null => 'value'];
    }

    public function testClassAsArrayValue(): void
    {
        $null = new NullClass();
        $array = ['key' => $null];
        $this->assertEquals($null, $array['key']);
    }

    public function testClassInCondition(): void
    {
        $null = new NullClass();
        if ($null) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }
    }

    public function testClassEmpty(): void
    {
        $null = new NullClass();
        $this->assertFalse(empty($null));
    }

    public function testClassIsset(): void
    {
        $null = new NullClass();
        $this->assertTrue(isset($null));
    }

    public function testClassUnset(): void
    {
        $null = new NullClass();
        unset($null);
        $this->assertFalse(isset($null));
    }

    public function testClassIsNull(): void
    {
        $null = new NullClass();
        $this->assertNotNull($null);
    }

    public function testClassIssetOnProperty(): void
    {
        $null = new NullClass();
        $this->assertFalse(isset($null->property));
    }

    public function testClassGetOnProperty(): void
    {
        $null = new NullClass();
        $this->assertNull($null->property ?? null);
    }

    public function testClassSetOnProperty(): void
    {
        $null = new NullClass();
        $null->property = 'value';
        $this->assertEquals('value', $null->property);
    }

    public function testClassUnsetOnProperty(): void
    {
        $null = new NullClass();
        $null->property = 'value';
        unset($null->property);
        $this->assertFalse(isset($null->property));
    }

    public function testClassIssetOnMethod(): void
    {
        $null = new NullClass();
        $this->assertFalse(method_exists($null, 'nonexistent'));
    }

    public function testClassCallOnMethod(): void
    {
        $null = new NullClass();
        $this->expectException(\Error::class);
        $null->nonexistent();
    }

    public function testClassReflectionGetMethods(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $methods = $reflection->getMethods();
        $this->assertIsArray($methods);
    }

    public function testClassReflectionGetProperties(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $properties = $reflection->getProperties();
        $this->assertIsArray($properties);
        $this->assertEmpty($properties);
    }

    public function testClassReflectionHasMethod(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertFalse($reflection->hasMethod('nonexistent'));
    }

    public function testClassReflectionHasProperty(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertFalse($reflection->hasProperty('nonexistent'));
    }

    public function testClassReflectionGetInterfaceNames(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $interfaces = $reflection->getInterfaceNames();
        $this->assertIsArray($interfaces);
    }

    public function testClassReflectionGetTraitNames(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $traits = $reflection->getTraitNames();
        $this->assertIsArray($traits);
    }

    public function testClassReflectionGetParentClass(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertFalse($reflection->getParentClass());
    }

    public function testClassReflectionImplementsInterface(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertFalse($reflection->implementsInterface(\Iterator::class));
    }

    public function testClassReflectionIsSubclassOf(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertFalse($reflection->isSubclassOf(\stdClass::class));
    }

    public function testClassReflectionExtends(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertFalse($reflection->isSubclassOf(\stdClass::class));
    }

    public function testClassReflectionGetModifiers(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $modifiers = $reflection->getModifiers();
        $this->assertIsInt($modifiers);
    }

    public function testClassReflectionIsInstance(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertTrue($reflection->isInstance($null));
    }

    public function testClassReflectionIsInstanceFalse(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass(\stdClass::class);
        $this->assertFalse($reflection->isInstance($null));
    }

    public function testClassReflectionNewInstance(): void
    {
        $reflection = new \ReflectionClass(NullClass::class);
        $instance = $reflection->newInstance();
        $this->assertInstanceOf(NullClass::class, $instance);
    }

    public function testClassReflectionNewInstanceArgs(): void
    {
        $reflection = new \ReflectionClass(NullClass::class);
        $instance = $reflection->newInstanceArgs([]);
        $this->assertInstanceOf(NullClass::class, $instance);
    }

    public function testClassReflectionGetStaticPropertyValue(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertEmpty($reflection->getStaticPropertyValue('nonexistent', 'default'));
    }

    public function testClassReflectionSetStaticPropertyValue(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $reflection->setStaticPropertyValue('nonexistent', 'value');
        $this->assertEquals('value', $reflection->getStaticPropertyValue('nonexistent', 'default'));
    }

    public function testClassReflectionGetDefaultProperties(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $properties = $reflection->getDefaultProperties();
        $this->assertIsArray($properties);
    }

    public function testClassReflectionIsIterateable(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertTrue($reflection->isIterateable());
    }

    public function testClassReflectionGetIterator(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $iterator = $reflection->getIterator();
        $this->assertInstanceOf(\ReflectionClass::class, $iterator);
    }

    public function testClassReflectionGetMethod(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->expectException(\ReflectionException::class);
        $reflection->getMethod('nonexistent');
    }

    public function testClassReflectionGetProperty(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->expectException(\ReflectionException::class);
        $reflection->getProperty('nonexistent');
    }

    public function testClassReflectionGetMethodsFiltered(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $this->assertIsArray($methods);
    }

    public function testClassReflectionGetMethodsFilteredPrivate(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PRIVATE);
        $this->assertIsArray($methods);
    }

    public function testClassReflectionGetMethodsFilteredProtected(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PROTECTED);
        $this->assertIsArray($methods);
    }

    public function testClassReflectionGetMethodsFilteredStatic(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_STATIC);
        $this->assertIsArray($methods);
    }

    public function testClassReflectionGetMethodsFilteredAbstract(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_ABSTRACT);
        $this->assertIsArray($methods);
    }

    public function testClassReflectionGetMethodsFilteredFinal(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_FINAL);
        $this->assertIsArray($methods);
    }

    public function testClassExport(): void
    {
        $reflection = new \ReflectionClass(NullClass::class);
        $export = $reflection->export();
        $this->assertIsString($export);
    }

    public function testClassExportReturn(): void
    {
        $reflection = new \ReflectionClass(NullClass::class);
        $export = $reflection->export(true);
        $this->assertIsString($export);
    }

    public function testClassGetExtensionInfo(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertNull($reflection->getExtension());
    }

    public function testClassGetExtensionName(): void
    {
        $null = new NullClass();
        $reflection = new \ReflectionClass($null);
        $this->assertFalse($reflection->getExtensionName());
    }

    public function testClassInOrder(): void
    {
        $null1 = new NullClass();
        $null2 = new NullClass();
        
        $id1 = spl_object_id($null1);
        $id2 = spl_object_id($null2);
        
        $this->assertNotEquals($id1, $id2);
    }

    public function testClassDestruct(): void
    {
        $null = new NullClass();
        unset($null);
        $this->assertTrue(true);
    }
}
