<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Settings;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SettingsFilterNull
 */
class SettingsFilterNullTest extends TestCase
{
    public function testConstructorStoresPropName(): void
    {
        $filter = new SettingsFilterNull('testProp');

        $this->assertEquals('testProp', $filter->propName);
    }

    public function testApplyReturnsNull(): void
    {
        $filter = new SettingsFilterNull('testProp');

        $name = 'testProp';
        $value = 'any_value';

        $result = $filter->apply($name, $value);

        $this->assertNull($result);
    }

    public function testApplyDoesNotModifyName(): void
    {
        $filter = new SettingsFilterNull('testProp');

        $name = 'testProp';
        $value = 'any_value';

        $filter->apply($name, $value);

        $this->assertEquals('testProp', $name);
    }

    public function testApplyDoesNotModifyValue(): void
    {
        $filter = new SettingsFilterNull('testProp');

        $name = 'testProp';
        $value = 'important_value';

        $filter->apply($name, $value);

        $this->assertEquals('important_value', $value);
    }

    public function testApplyWithNullValue(): void
    {
        $filter = new SettingsFilterNull('testProp');

        $name = 'testProp';
        $value = null;

        $result = $filter->apply($name, $value);

        $this->assertNull($result);
        $this->assertNull($value);
    }

    public function testApplyWithArrayValue(): void
    {
        $filter = new SettingsFilterNull('testProp');

        $name = 'testProp';
        $value = ['key' => 'value', 'nested' => ['data']];

        $result = $filter->apply($name, $value);

        $this->assertNull($result);
        $this->assertEquals(['key' => 'value', 'nested' => ['data']], $value);
    }

    public function testApplyWithObjectValue(): void
    {
        $filter = new SettingsFilterNull('testProp');

        $name = 'testProp';
        $value = new \stdClass();
        $value->property = 'test';

        $result = $filter->apply($name, $value);

        $this->assertNull($result);
        $this->assertInstanceOf(\stdClass::class, $value);
        $this->assertEquals('test', $value->property);
    }

    public function testApplyWithDifferentPropNames(): void
    {
        $filter = new SettingsFilterNull('specificProp');

        $name = 'specificProp';
        $value = 'value1';

        $result = $filter->apply($name, $value);

        $this->assertNull($result);

        // Test with different name
        $name2 = 'otherProp';
        $value2 = 'value2';

        $result2 = $filter->apply($name2, $value2);

        $this->assertNull($result2);
    }
}
