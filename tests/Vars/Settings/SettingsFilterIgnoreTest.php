<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Settings;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SettingsFilterIgnore
 */
class SettingsFilterIgnoreTest extends TestCase
{
    public function testConstructorStoresPropName(): void
    {
        $filter = new SettingsFilterIgnore('testProp');

        $this->assertEquals('testProp', $filter->propName);
    }

    public function testApplySetsNameToNull(): void
    {
        $filter = new SettingsFilterIgnore('testProp');

        $name = 'testProp';
        $value = 'any_value';

        $result = $filter->apply($name, $value);

        $this->assertNull($name);
    }

    public function testApplyReturnsNull(): void
    {
        $filter = new SettingsFilterIgnore('testProp');

        $name = 'testProp';
        $value = 'any_value';

        $result = $filter->apply($name, $value);

        $this->assertNull($result);
    }

    public function testApplyIgnoresOriginalValue(): void
    {
        $filter = new SettingsFilterIgnore('testProp');

        $name = 'testProp';
        $value = 'important_value';

        $filter->apply($name, $value);

        // Value should remain unchanged (filter ignores it)
        $this->assertEquals('important_value', $value);
    }

    public function testApplyWithDifferentPropNames(): void
    {
        $filter = new SettingsFilterIgnore('specificProp');

        $name = 'specificProp';
        $value = 'value1';

        $filter->apply($name, $value);

        $this->assertNull($name);

        // Test with different name
        $name2 = 'otherProp';
        $value2 = 'value2';

        $filter->apply($name2, $value2);

        $this->assertNull($name2);
    }

    public function testApplyWithComplexValue(): void
    {
        $filter = new SettingsFilterIgnore('testProp');

        $name = 'testProp';
        $value = ['key' => 'value', 'nested' => ['data']];

        $filter->apply($name, $value);

        $this->assertNull($name);
        $this->assertNull($filter->apply($name, $value));
    }
}
