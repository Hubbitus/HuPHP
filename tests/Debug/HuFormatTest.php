<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\HuFormat;
use Hubbitus\HuPHP\Debug\HuFormatException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\HuFormat
 * @covers \Hubbitus\HuPHP\Debug\HuFormatException
 */
class HuFormatTest extends TestCase
{
    public function testConstructorCreatesInstance(): void
    {
        $format = new HuFormat();
        
        $this->assertInstanceOf(HuFormat::class, $format);
    }

    public function testConstructorWithFormatArray(): void
    {
        $format = new HuFormat(['name']);
        
        $this->assertInstanceOf(HuFormat::class, $format);
    }

    public function testConstructorWithFormatAndValue(): void
    {
        $value = 'test_value';
        $format = new HuFormat(['name'], $value);
        
        $this->assertInstanceOf(HuFormat::class, $format);
        $this->assertEquals('test_value', $format->getValue());
    }

    public function testConstructorWithKey(): void
    {
        $value = 'value';
        $format = new HuFormat(['name'], $value, 'test_key');

        $this->assertInstanceOf(HuFormat::class, $format);
    }

    public function testSetFormatAndValue(): void
    {
        $format = new HuFormat();
        $value = 'test_value';
        $format->set(['name'], $value);
        
        $this->assertEquals('test_value', $format->getValue());
    }

    public function testSetValue(): void
    {
        $format = new HuFormat();
        $value = 'test_value';
        $format->setValue($value);
        
        $this->assertEquals('test_value', $format->getValue());
    }

    public function testSetValueWithNull(): void
    {
        $format = new HuFormat();
        $null = null;
        $format->setValue($null);
        
        $this->assertSame($format, $format->getValue());
    }

    public function testSetReturnsSelf(): void
    {
        $format = new HuFormat();
        $result = $format->set(['name']);
        
        $this->assertSame($format, $result);
    }

    public function testModsConstantExists(): void
    {
        $this->assertIsArray(HuFormat::$MODS);
        $this->assertArrayHasKey('A', HuFormat::$MODS);
        $this->assertArrayHasKey('s', HuFormat::$MODS);
        $this->assertArrayHasKey('a', HuFormat::$MODS);
    }

    public function testSprintfVarConstant(): void
    {
        $this->assertEquals('__vAr__', HuFormat::sprintf_var);
    }

    public function testEvaluateVarConstant(): void
    {
        $this->assertEquals('var', HuFormat::evaluate_var);
    }

    public function testModsSeparatorConstant(): void
    {
        $this->assertEquals(':::', HuFormat::mods_separator);
    }

    public function testSetWithKey(): void
    {
        $format = new HuFormat();
        $value = 'value';
        $format->set(['name'], $value, 'my_key');
        
        $this->assertInstanceOf(HuFormat::class, $format);
    }

    public function testFormatExtendsHuError(): void
    {
        $format = new HuFormat();
        
        $this->assertInstanceOf(HuFormat::class, $format);
        $this->assertInstanceOf(\Hubbitus\HuPHP\Debug\HuError::class, $format);
    }

    public function testFormatExceptionExtendsVariableException(): void
    {
        $exception = new HuFormatException('test message');
        
        $this->assertInstanceOf(HuFormatException::class, $exception);
        $this->assertInstanceOf(\Hubbitus\HuPHP\Exceptions\Variables\VariableException::class, $exception);
    }
}
