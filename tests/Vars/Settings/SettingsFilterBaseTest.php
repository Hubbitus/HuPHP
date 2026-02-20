<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Settings;

use Hubbitus\HuPHP\Vars\Settings\SettingsFilterBase;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Vars\Settings\SettingsFilterBase
 */
class SettingsFilterBaseTest extends TestCase
{
    public function testConstructorStoresPropName(): void {
        $callback = function(&$name, &$value) { return $value; };
        $filter = new SettingsFilterBase('testProp', $callback);

        $this->assertEquals('testProp', $filter->propName);
    }

    public function testApplyCallsCallback(): void {
        $callbackCalled = false;
        $callback = function(&$name, &$value) use (&$callbackCalled) {
            $callbackCalled = true;
            $value = strtoupper($value);
            return $value;
        };

        $filter = new SettingsFilterBase('testProp', $callback);

        $name = 'testProp';
        $value = 'test_value';

        $result = $filter->apply($name, $value);

        $this->assertTrue($callbackCalled);
        $this->assertEquals('TEST_VALUE', $value);
        $this->assertEquals('TEST_VALUE', $result);
    }

    public function testApplyCanModifyName(): void {
        $callback = function(&$name, &$value) {
            $name = 'modified_' . $name;
            return $value;
        };

        $filter = new SettingsFilterBase('originalName', $callback);

        $name = 'originalName';
        $value = 'test_value';

        $filter->apply($name, $value);

        $this->assertEquals('modified_originalName', $name);
    }

    public function testApplyCanModifyValue(): void {
        $callback = function(&$name, &$value) {
            $value = $value . '_modified';
            return $value;
        };

        $filter = new SettingsFilterBase('testProp', $callback);

        $name = 'testProp';
        $value = 'original';

        $filter->apply($name, $value);

        $this->assertEquals('original_modified', $value);
    }

    public function testApplyWithComplexCallback(): void {
        $callback = function(&$name, &$value) {
            if ($name === 'uppercase_prop') {
                $value = strtoupper($value);
            } elseif ($name === 'lowercase_prop') {
                $value = strtolower($value);
            }
            return $value;
        };

        $filter = new SettingsFilterBase('uppercase_prop', $callback);

        $name = 'uppercase_prop';
        $value = 'test_value';

        $filter->apply($name, $value);

        $this->assertEquals('TEST_VALUE', $value);
    }
}
