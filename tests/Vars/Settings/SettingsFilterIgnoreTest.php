<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Settings;

use Hubbitus\HuPHP\Vars\Settings\Filters\SettingsFilterIgnore;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Vars\Settings\Filters\SettingsFilterIgnore
 */
class SettingsFilterIgnoreTest extends TestCase {
    public function testConstructorStoresPropName(): void {
        $filter = new SettingsFilterIgnore('testProp');

        $this->assertEquals('testProp', $filter->propName);
    }

    public function testApplySetsValueToNull(): void {
        $filter = new SettingsFilterIgnore('testProp');

        $name = 'testProp';
        $value = 'any_value';

        $result = $filter->apply($name, $value);

        $this->assertNull($value);
    }

    public function testApplyReturnsNull(): void {
        $filter = new SettingsFilterIgnore('testProp');

        $name = 'testProp';
        $value = 'any_value';

        $result = $filter->apply($name, $value);

        $this->assertNull($result);
    }

    public function testApplyIgnoresOriginalValue(): void {
        $filter = new SettingsFilterIgnore('testProp');

        $name = 'testProp';
        $value = 'important_value';

        $filter->apply($name, $value);

        // Value should be nulled (filter ignores it)
        $this->assertNull($value);
    }

    public function testApplyWithDifferentPropNames(): void {
        $filter = new SettingsFilterIgnore('specificProp');

        $name = 'specificProp';
        $value = 'value1';

        $filter->apply($name, $value);

        $this->assertNull($value);

        // Test with different name
        $name2 = 'otherProp';
        $value2 = 'value2';

        $filter->apply($name2, $value2);

        $this->assertNull($value2);
    }

    public function testApplyWithComplexValue(): void {
        $filter = new SettingsFilterIgnore('testProp');

        $name = 'testProp';
        $value = ['key' => 'value', 'nested' => ['data']];

        $filter->apply($name, $value);

        $this->assertNull($value);
        $this->assertNull($filter->apply($name, $value));
    }
}
