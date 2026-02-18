<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Vars\Settings;

use Hubbitus\HuPHP\Exceptions\Variables\VariableReadOnlyException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \settings_filter_readOnly
 */
class SettingsFilterReadonlyTest extends TestCase
{
    public function testConstructorStoresPropName(): void
    {
        $filter = new settings_filter_readOnly('testProp');

        $this->assertEquals('testProp', $filter->propName);
    }

    public function testApplyThrowsVariableReadOnlyException(): void
    {
        $this->expectException(VariableReadOnlyException::class);

        $filter = new settings_filter_readOnly('testProp');

        $name = 'testProp';
        $value = 'any_value';

        $filter->apply($name, $value);
    }

    public function testApplyDoesNotModifyValueBeforeException(): void
    {
        $this->expectException(VariableReadOnlyException::class);

        $filter = new settings_filter_readOnly('testProp');

        $name = 'testProp';
        $value = 'important_value';

        try {
            $filter->apply($name, $value);
        } catch (VariableReadOnlyException $e) {
            // Value should remain unchanged
            $this->assertEquals('important_value', $value);
            throw $e;
        }
    }

    public function testApplyDoesNotModifyNameBeforeException(): void
    {
        $this->expectException(VariableReadOnlyException::class);

        $filter = new settings_filter_readOnly('testProp');

        $name = 'testProp';
        $value = 'any_value';

        try {
            $filter->apply($name, $value);
        } catch (VariableReadOnlyException $e) {
            // Name should remain unchanged
            $this->assertEquals('testProp', $name);
            throw $e;
        }
    }

    public function testApplyWithNullValue(): void
    {
        $this->expectException(VariableReadOnlyException::class);

        $filter = new settings_filter_readOnly('testProp');

        $name = 'testProp';
        $value = null;

        $filter->apply($name, $value);
    }

    public function testApplyWithArrayValue(): void
    {
        $this->expectException(VariableReadOnlyException::class);

        $filter = new settings_filter_readOnly('testProp');

        $name = 'testProp';
        $value = ['key' => 'value'];

        $filter->apply($name, $value);
    }

    public function testApplyWithDifferentPropNames(): void
    {
        $this->expectException(VariableReadOnlyException::class);

        $filter = new settings_filter_readOnly('specificProp');

        $name = 'specificProp';
        $value = 'value1';

        $filter->apply($name, $value);
    }
}
