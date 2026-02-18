<?php
declare(strict_types=1);

/**
 * Test for HuConfig class.
 */

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\HuConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Vars\HuConfig
 */
class HuConfigTest extends TestCase
{
    public function testClassInstantiation(): void
    {
        $config = new HuConfig();
        $this->assertInstanceOf(HuConfig::class, $config);
    }

    public function testClassExtendsHuArray(): void
    {
        $config = new HuConfig();
        $this->assertInstanceOf('Hubbitus\\HuPHP\\Vars\\HuArray', $config);
    }

    public function testConstructorWithNull(): void
    {
        $config = new HuConfig(null);
        $this->assertInstanceOf(HuConfig::class, $config);
    }

    public function testConstructorWithArray(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $this->assertInstanceOf(HuConfig::class, $config);
    }

    public function testGetWithExistingKey(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $this->assertEquals('value', $config->get('key'));
    }

    public function testGetWithNonExistingKey(): void
    {
        $config = new HuConfig();
        $this->assertNull($config->get('nonexistent'));
    }

    public function testGetWithDefault(): void
    {
        $config = new HuConfig();
        $this->assertEquals('default', $config->get('nonexistent', 'default'));
    }

    public function testGetRaw(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $this->assertEquals('value', $config->getRaw('key'));
    }

    public function testGetRawWithNonExistingKey(): void
    {
        $config = new HuConfig();
        $this->assertNull($config->getRaw('nonexistent'));
    }

    public function testGetRawWithDefault(): void
    {
        $config = new HuConfig();
        $this->assertEquals('default', $config->getRaw('nonexistent', 'default'));
    }

    public function testSet(): void
    {
        $config = new HuConfig();
        $config->set('key', 'value');
        $this->assertEquals('value', $config->get('key'));
    }

    public function testSetReturnsSelf(): void
    {
        $config = new HuConfig();
        $result = $config->set('key', 'value');
        $this->assertSame($config, $result);
    }

    public function testSetNested(): void
    {
        $config = new HuConfig();
        $config->set('database.host', 'localhost');
        $this->assertEquals('localhost', $config->get('database.host'));
    }

    public function testSetMultiple(): void
    {
        $config = new HuConfig();
        $config->setMultiple(['key1' => 'value1', 'key2' => 'value2']);
        $this->assertEquals('value1', $config->get('key1'));
        $this->assertEquals('value2', $config->get('key2'));
    }

    public function testHas(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $this->assertTrue($config->has('key'));
        $this->assertFalse($config->has('nonexistent'));
    }

    public function testHasNested(): void
    {
        $config = new HuConfig(['database' => ['host' => 'localhost']]);
        $this->assertTrue($config->has('database.host'));
    }

    public function testRemove(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $config->remove('key');
        $this->assertFalse($config->has('key'));
    }

    public function testRemoveReturnsSelf(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $result = $config->remove('key');
        $this->assertSame($config, $result);
    }

    public function testRemoveNested(): void
    {
        $config = new HuConfig(['database' => ['host' => 'localhost']]);
        $config->remove('database.host');
        $this->assertFalse($config->has('database.host'));
    }

    public function testAll(): void
    {
        $config = new HuConfig(['key1' => 'value1', 'key2' => 'value2']);
        $all = $config->all();
        $this->assertIsArray($all);
        $this->assertEquals(['key1' => 'value1', 'key2' => 'value2'], $all);
    }

    public function testOnly(): void
    {
        $config = new HuConfig(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']);
        $only = $config->only(['key1', 'key2']);
        $this->assertEquals(['key1' => 'value1', 'key2' => 'value2'], $only);
    }

    public function testExcept(): void
    {
        $config = new HuConfig(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']);
        $except = $config->except(['key3']);
        $this->assertEquals(['key1' => 'value1', 'key2' => 'value2'], $except);
    }

    public function testMerge(): void
    {
        $config = new HuConfig(['key1' => 'value1']);
        $config->merge(['key2' => 'value2']);
        $this->assertEquals('value2', $config->get('key2'));
    }

    public function testMergeReturnsSelf(): void
    {
        $config = new HuConfig();
        $result = $config->merge(['key' => 'value']);
        $this->assertSame($config, $result);
    }

    public function testMergeRecursive(): void
    {
        $config = new HuConfig(['database' => ['host' => 'localhost']]);
        $config->mergeRecursive(['database' => ['port' => 3306]]);
        $this->assertEquals('localhost', $config->get('database.host'));
        $this->assertEquals(3306, $config->get('database.port'));
    }

    public function testPrepend(): void
    {
        $config = new HuConfig(['key' => ['value2']]);
        $config->prepend('key', 'value1');
        $this->assertEquals(['value1', 'value2'], $config->get('key'));
    }

    public function testPush(): void
    {
        $config = new HuConfig(['key' => ['value1']]);
        $config->push('key', 'value2');
        $this->assertEquals(['value1', 'value2'], $config->get('key'));
    }

    public function testGetArray(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $array = $config->getArray();
        $this->assertIsArray($array);
        $this->assertEquals(['key' => 'value'], $array);
    }

    public function testCount(): void
    {
        $config = new HuConfig(['key1' => 'value1', 'key2' => 'value2']);
        $this->assertEquals(2, $config->count());
    }

    public function testIsEmpty(): void
    {
        $config = new HuConfig();
        $this->assertTrue($config->isEmpty());
    }

    public function testIsNotEmpty(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $this->assertFalse($config->isEmpty());
    }

    public function testClear(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $config->clear();
        $this->assertTrue($config->isEmpty());
    }

    public function testClearReturnsSelf(): void
    {
        $config = new HuConfig();
        $result = $config->clear();
        $this->assertSame($config, $result);
    }

    public function testKeys(): void
    {
        $config = new HuConfig(['key1' => 'value1', 'key2' => 'value2']);
        $keys = $config->keys();
        $this->assertIsArray($keys);
        $this->assertContains('key1', $keys);
        $this->assertContains('key2', $keys);
    }

    public function testValues(): void
    {
        $config = new HuConfig(['key1' => 'value1', 'key2' => 'value2']);
        $values = $config->values();
        $this->assertIsArray($values);
        $this->assertContains('value1', $values);
        $this->assertContains('value2', $values);
    }

    public function testToJson(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $json = $config->toJson();
        $this->assertIsString($json);
        $this->assertStringContainsString('key', $json);
        $this->assertStringContainsString('value', $json);
    }

    public function testFromJson(): void
    {
        $config = new HuConfig();
        $fromJson = $config->fromJson('{"key": "value"}');
        $this->assertInstanceOf(HuConfig::class, $fromJson);
        $this->assertEquals('value', $fromJson->get('key'));
    }

    public function testSerialize(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $serialized = serialize($config);
        $unserialized = unserialize($serialized);
        $this->assertInstanceOf(HuConfig::class, $unserialized);
        $this->assertEquals('value', $unserialized->get('key'));
    }

    public function testClone(): void
    {
        $config1 = new HuConfig(['key' => 'value']);
        $config2 = clone $config1;
        $this->assertEquals($config1->get('key'), $config2->get('key'));
    }

    public function testOffsetGet(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $this->assertEquals('value', $config['key']);
    }

    public function testOffsetSet(): void
    {
        $config = new HuConfig();
        $config['key'] = 'value';
        $this->assertEquals('value', $config->get('key'));
    }

    public function testOffsetExists(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $this->assertTrue(isset($config['key']));
    }

    public function testOffsetUnset(): void
    {
        $config = new HuConfig(['key' => 'value']);
        unset($config['key']);
        $this->assertFalse($config->has('key'));
    }

    public function testIterator(): void
    {
        $config = new HuConfig(['key1' => 'value1', 'key2' => 'value2']);
        $result = [];
        foreach ($config as $key => $value) {
            $result[$key] = $value;
        }
        $this->assertNotEmpty($result);
    }

    public function testGetWithDotNotation(): void
    {
        $config = new HuConfig(['database' => ['host' => 'localhost', 'port' => 3306]]);
        $this->assertEquals('localhost', $config->get('database.host'));
        $this->assertEquals(3306, $config->get('database.port'));
    }

    public function testSetWithDotNotation(): void
    {
        $config = new HuConfig();
        $config->set('app.name', 'TestApp');
        $config->set('app.version', '1.0.0');
        $this->assertEquals('TestApp', $config->get('app.name'));
        $this->assertEquals('1.0.0', $config->get('app.version'));
    }

    public function testHasWithDotNotation(): void
    {
        $config = new HuConfig(['app' => ['name' => 'TestApp']]);
        $this->assertTrue($config->has('app.name'));
        $this->assertFalse($config->has('app.version'));
    }

    public function testRemoveWithDotNotation(): void
    {
        $config = new HuConfig(['app' => ['name' => 'TestApp', 'version' => '1.0.0']]);
        $config->remove('app.name');
        $this->assertFalse($config->has('app.name'));
        $this->assertTrue($config->has('app.version'));
    }

    public function testGetWithDeepNesting(): void
    {
        $config = new HuConfig([
            'level1' => [
                'level2' => [
                    'level3' => [
                        'value' => 'deep'
                    ]
                ]
            ]
        ]);
        $this->assertEquals('deep', $config->get('level1.level2.level3.value'));
    }

    public function testSetWithDeepNesting(): void
    {
        $config = new HuConfig();
        $config->set('a.b.c.d.e', 'deep value');
        $this->assertEquals('deep value', $config->get('a.b.c.d.e'));
    }

    public function testGetWithArrayAccess(): void
    {
        $config = new HuConfig(['database' => ['host' => 'localhost']]);
        $this->assertEquals('localhost', $config['database']['host']);
    }

    public function testGetNonExistentNestedKey(): void
    {
        $config = new HuConfig(['database' => ['host' => 'localhost']]);
        $this->assertNull($config->get('database.port'));
    }

    public function testGetWithDefaultForNestedKey(): void
    {
        $config = new HuConfig();
        $this->assertEquals('default', $config->get('database.host', 'default'));
    }

    public function testOnlyWithNestedKeys(): void
    {
        $config = new HuConfig([
            'database' => ['host' => 'localhost', 'port' => 3306],
            'app' => ['name' => 'TestApp']
        ]);
        $only = $config->only(['database']);
        $this->assertArrayHasKey('database', $only);
    }

    public function testExceptWithNestedKeys(): void
    {
        $config = new HuConfig([
            'database' => ['host' => 'localhost'],
            'app' => ['name' => 'TestApp']
        ]);
        $except = $config->except(['app']);
        $this->assertArrayHasKey('database', $except);
        $this->assertArrayNotHasKey('app', $except);
    }

    public function testMergeWithNestedArrays(): void
    {
        $config = new HuConfig(['database' => ['host' => 'localhost']]);
        $config->merge(['database' => ['port' => 3306]]);
        $this->assertEquals(['host' => 'localhost', 'port' => 3306], $config->get('database'));
    }

    public function testPrependWithNestedKey(): void
    {
        $config = new HuConfig(['items' => ['item2', 'item3']]);
        $config->prepend('items', 'item1');
        $this->assertEquals(['item1', 'item2', 'item3'], $config->get('items'));
    }

    public function testPushWithNestedKey(): void
    {
        $config = new HuConfig(['items' => ['item1']]);
        $config->push('items', 'item2');
        $this->assertEquals(['item1', 'item2'], $config->get('items'));
    }

    public function testGetWithWildcard(): void
    {
        $config = new HuConfig([
            'users' => [
                ['name' => 'John'],
                ['name' => 'Jane']
            ]
        ]);
        $this->assertIsArray($config->get('users'));
    }

    public function testFirstKey(): void
    {
        $config = new HuConfig(['first' => 1, 'second' => 2]);
        $this->assertEquals(1, $config->first());
    }

    public function testLastKey(): void
    {
        $config = new HuConfig(['first' => 1, 'last' => 2]);
        $this->assertEquals(2, $config->last());
    }

    public function testToArray(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $array = $config->toArray();
        $this->assertIsArray($array);
        $this->assertEquals(['key' => 'value'], $array);
    }

    public function testJsonSerialize(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $json = json_encode($config);
        $this->assertIsString($json);
    }

    public function testToString(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $string = (string) $config;
        $this->assertIsString($string);
    }

    public function testWithNumericKeys(): void
    {
        $config = new HuConfig([0 => 'zero', 1 => 'one']);
        $this->assertEquals('zero', $config->get(0));
        $this->assertEquals('one', $config->get(1));
    }

    public function testWithMixedKeys(): void
    {
        $config = new HuConfig([0 => 'zero', 'key' => 'value']);
        $this->assertEquals('zero', $config->get(0));
        $this->assertEquals('value', $config->get('key'));
    }

    public function testWithBooleanValues(): void
    {
        $config = new HuConfig(['enabled' => true, 'disabled' => false]);
        $this->assertTrue($config->get('enabled'));
        $this->assertFalse($config->get('disabled'));
    }

    public function testWithNullValues(): void
    {
        $config = new HuConfig(['key' => null]);
        $this->assertNull($config->get('key'));
        $this->assertTrue($config->has('key'));
    }

    public function testWithEmptyString(): void
    {
        $config = new HuConfig(['key' => '']);
        $this->assertEquals('', $config->get('key'));
        $this->assertTrue($config->has('key'));
    }

    public function testWithZero(): void
    {
        $config = new HuConfig(['count' => 0]);
        $this->assertEquals(0, $config->get('count'));
        $this->assertTrue($config->has('count'));
    }

    public function testWithFloatValues(): void
    {
        $config = new HuConfig(['pi' => 3.14159]);
        $this->assertEquals(3.14159, $config->get('pi'));
    }

    public function testWithObjectValues(): void
    {
        $obj = new \stdClass();
        $obj->value = 'test';
        $config = new HuConfig(['object' => $obj]);
        $this->assertSame($obj, $config->get('object'));
    }

    public function testWithArrayValues(): void
    {
        $config = new HuConfig(['array' => [1, 2, 3]]);
        $this->assertEquals([1, 2, 3], $config->get('array'));
    }

    public function testWithCallableValues(): void
    {
        $callable = function() { return 'test'; };
        $config = new HuConfig(['callback' => $callable]);
        $this->assertIsCallable($config->get('callback'));
    }

    public function testWithResourceValues(): void
    {
        $resource = fopen('php://memory', 'r+');
        $config = new HuConfig(['resource' => $resource]);
        $this->assertIsResource($config->get('resource'));
        fclose($resource);
    }

    public function testEnvironmentVariables(): void
    {
        putenv('TEST_VAR=test_value');
        $config = new HuConfig(['key' => getenv('TEST_VAR')]);
        $this->assertEquals('test_value', $config->get('key'));
    }

    public function testConfigurationLayers(): void
    {
        $defaults = new HuConfig(['debug' => false, 'cache' => true]);
        $environment = new HuConfig(['debug' => true]);
        $defaults->merge($environment->all());
        $this->assertTrue($defaults->get('debug'));
        $this->assertTrue($defaults->get('cache'));
    }

    public function testConfigurationValidation(): void
    {
        $config = new HuConfig(['required' => 'value']);
        $this->assertTrue($config->has('required'));
        $this->assertNotEmpty($config->get('required'));
    }

    public function testConfigurationDefaults(): void
    {
        $config = new HuConfig();
        $config->set('app.name', 'DefaultApp');
        $this->assertEquals('DefaultApp', $config->get('app.name'));
    }

    public function testConfigurationOverride(): void
    {
        $config = new HuConfig(['app.name' => 'OriginalApp']);
        $config->set('app.name', 'OverriddenApp');
        $this->assertEquals('OverriddenApp', $config->get('app.name'));
    }

    public function testConfigurationInheritance(): void
    {
        $parent = new HuConfig(['parent.key' => 'parent.value']);
        $child = new HuConfig($parent->all());
        $child->set('child.key', 'child.value');
        $this->assertEquals('parent.value', $child->get('parent.key'));
        $this->assertEquals('child.value', $child->get('child.key'));
    }

    public function testConfigurationExport(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $export = var_export($config->all(), true);
        $this->assertIsString($export);
        $this->assertStringContainsString('key', $export);
        $this->assertStringContainsString('value', $export);
    }

    public function testConfigurationImport(): void
    {
        $config = new HuConfig();
        $data = ['key' => 'value'];
        $config->merge($data);
        $this->assertEquals('value', $config->get('key'));
    }

    public function testConfigurationReset(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $config->clear();
        $this->assertEmpty($config->all());
    }

    public function testConfigurationCopy(): void
    {
        $config1 = new HuConfig(['key' => 'value']);
        $config2 = clone $config1;
        $config2->set('key', 'new_value');
        $this->assertEquals('value', $config1->get('key'));
        $this->assertEquals('new_value', $config2->get('key'));
    }

    public function testConfigurationComparison(): void
    {
        $config1 = new HuConfig(['key' => 'value']);
        $config2 = new HuConfig(['key' => 'value']);
        $config3 = new HuConfig(['key' => 'different']);
        
        $this->assertEquals($config1->all(), $config2->all());
        $this->assertNotEquals($config1->all(), $config3->all());
    }

    public function testConfigurationFlatten(): void
    {
        $config = new HuConfig(['a' => ['b' => ['c' => 'value']]]);
        $this->assertIsArray($config->all());
    }

    public function testConfigurationUnflatten(): void
    {
        $config = new HuConfig();
        $config->set('a.b.c', 'value');
        $this->assertIsArray($config->get('a'));
    }

    public function testConfigurationDotNotationExpansion(): void
    {
        $config = new HuConfig();
        $config->set('a.b', 1);
        $config->set('a.c', 2);
        $config->set('d.e', 3);
        
        $this->assertEquals(1, $config->get('a.b'));
        $this->assertEquals(2, $config->get('a.c'));
        $this->assertEquals(3, $config->get('d.e'));
    }

    public function testConfigurationMultipleLevels(): void
    {
        $config = new HuConfig([
            'app' => [
                'name' => 'TestApp',
                'version' => '1.0.0',
                'settings' => [
                    'debug' => true,
                    'cache' => false
                ]
            ]
        ]);
        
        $this->assertEquals('TestApp', $config->get('app.name'));
        $this->assertEquals('1.0.0', $config->get('app.version'));
        $this->assertTrue($config->get('app.settings.debug'));
        $this->assertFalse($config->get('app.settings.cache'));
    }

    public function testConfigurationArrayAccessNested(): void
    {
        $config = new HuConfig([
            'database' => [
                'connections' => [
                    'mysql' => [
                        'host' => 'localhost'
                    ]
                ]
            ]
        ]);
        
        $this->assertEquals('localhost', $config['database']['connections']['mysql']['host']);
    }

    public function testConfigurationIteratorWithNested(): void
    {
        $config = new HuConfig([
            'level1' => [
                'level2' => 'value'
            ]
        ]);
        
        $count = 0;
        foreach ($config as $key => $value) {
            $count++;
        }
        $this->assertGreaterThan(0, $count);
    }

    public function testConfigurationCountable(): void
    {
        $config = new HuConfig(['key1' => 'value1', 'key2' => 'value2']);
        $this->assertInstanceOf(\Countable::class, $config);
        $this->assertEquals(2, count($config));
    }

    public function testConfigurationArrayAccess(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $this->assertInstanceOf(\ArrayAccess::class, $config);
    }

    public function testConfigurationIteratorAggregate(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $this->assertInstanceOf(\IteratorAggregate::class, $config);
    }

    public function testConfigurationJsonSerializable(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $this->assertInstanceOf(\JsonSerializable::class, $config);
    }

    public function testConfigurationSerializable(): void
    {
        $config = new HuConfig(['key' => 'value']);
        $this->assertInstanceOf(\Serializable::class, $config);
    }
}
