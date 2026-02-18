<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Settings;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SettingsFilterDefault
 */
class SettingsFilterDefaultTest extends TestCase
{
    public function testConstructorStoresDefaultValue(): void
    {
        $filter = new SettingsFilterDefault('testProp', 'default_value');

        $this->assertEquals('testProp', $filter->propName);
    }

    public function testApplyReturnsDefaultValueForEmptyValue(): void
    {
        $filter = new SettingsFilterDefault('testProp', 'default_value');

        $name = 'testProp';
        $value = '';

        $filter->apply($name, $value);

        $this->assertEquals('default_value', $value);
    }

    public function testApplyReturnsDefaultValueForNullValue(): void
    {
        $filter = new SettingsFilterDefault('testProp', 'default_value');

        $name = 'testProp';
        $value = null;

        $filter->apply($name, $value);

        $this->assertEquals('default_value', $value);
    }

    public function testApplyLeavesNonEmptyValueUnchanged(): void
    {
        $filter = new SettingsFilterDefault('testProp', 'default_value');

        $name = 'testProp';
        $value = 'actual_value';

        $filter->apply($name, $value);

        $this->assertEquals('actual_value', $value);
    }

    public function testApplyWithCustomEmptyCallback(): void
    {
        $customEmptyCallback = function($var) {
            return $var === 'special_empty';
        };

        $filter = new SettingsFilterDefault('testProp', 'default_value', $customEmptyCallback);

        $name = 'testProp';
        $value = 'special_empty';

        $filter->apply($name, $value);

        $this->assertEquals('default_value', $value);
    }

    public function testApplyWithCustomEmptyCallbackNonMatching(): void
    {
        $customEmptyCallback = function($var) {
            return $var === 'special_empty';
        };

        $filter = new SettingsFilterDefault('testProp', 'default_value', $customEmptyCallback);

        $name = 'testProp';
        $value = 'normal_value';

        $filter->apply($name, $value);

        $this->assertEquals('normal_value', $value);
    }

    public function testApplyWithNumericDefault(): void
    {
        $filter = new SettingsFilterDefault('testProp', 42);

        $name = 'testProp';
        $value = '';

        $filter->apply($name, $value);

        $this->assertEquals(42, $value);
    }

    public function testApplyWithArrayDefault(): void
    {
        $defaultArray = ['key' => 'value'];
        $filter = new SettingsFilterDefault('testProp', $defaultArray);

        $name = 'testProp';
        $value = null;

        $filter->apply($name, $value);

        $this->assertEquals($defaultArray, $value);
    }

    public function testApplyWithZeroValue(): void
    {
        $filter = new SettingsFilterDefault('testProp', 'default_value');

        $name = 'testProp';
        $value = 0;

        $filter->apply($name, $value);

        // 0 is considered empty in PHP, so it should be replaced
        $this->assertEquals('default_value', $value);
    }

    public function testApplyWithFalseValue(): void
    {
        $filter = new SettingsFilterDefault('testProp', 'default_value');

        $name = 'testProp';
        $value = false;

        $filter->apply($name, $value);

        // false is considered empty in PHP, so it should be replaced
        $this->assertEquals('default_value', $value);
    }
}
