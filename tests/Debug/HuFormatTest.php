<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\HuFormat;
use Hubbitus\HuPHP\Debug\HuFormatException;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Debug\HuFormat
* @covers \Hubbitus\HuPHP\Debug\HuFormatException
**/
class HuFormatTest extends TestCase {

    /**
    * Test constructor creates instance.
    **/
    public function testConstructorCreatesInstance(): void {
        $format = new HuFormat();
        $this->assertInstanceOf(HuFormat::class, $format);
    }

    /**
    * Test constructor with format array.
    **/
    public function testConstructorWithFormatArray(): void {
        $value = 'test';
        $format = new HuFormat(['v:::'], $value);
        $this->assertInstanceOf(HuFormat::class, $format);
    }

    /**
    * Test constructor with format and value.
    **/
    public function testConstructorWithFormatAndValue(): void {
        $value = 'test_value';
        $format = new HuFormat(['v:::'], $value);
        $this->assertInstanceOf(HuFormat::class, $format);
        $this->assertEquals('test_value', $format->getValue());
    }

    /**
    * Test constructor with key.
   **/
    public function testConstructorWithKey(): void {
        $value = 'value';
        $format = new HuFormat(['v:::'], $value, 'test_key');
        $this->assertInstanceOf(HuFormat::class, $format);
    }

    /**
    * Test set format and value.
    */
    public function testSetFormatAndValue(): void {
        $format = new HuFormat();
        $value = 'test_value';
        $format->set(['v:::'], $value);
        $this->assertEquals('test_value', $format->getValue());
    }

    /**
    * Test setValue.
    */
    public function testSetValue(): void {
        $format = new HuFormat();
        $value = 'test_value';
        $format->setValue($value);
        $this->assertEquals('test_value', $format->getValue());
    }

    /**
    * Test setValue with null.
    */
    public function testSetValueWithNull(): void {
        $format = new HuFormat();
        $null = null;
        $format->setValue($null);
        $this->assertSame($format, $format->getValue());
    }

    /**
    * Test set returns self.
    */
    public function testSetReturnsSelf(): void {
        $format = new HuFormat();
        $value = 'test';
        $result = $format->set(['v:::'], $value);
        $this->assertSame($format, $result);
    }

    /**
    * Test MODS constant exists.
    */
    public function testModsConstantExists(): void {
        $this->assertIsArray(HuFormat::$MODS);
        $this->assertArrayHasKey('A', HuFormat::$MODS);
        $this->assertArrayHasKey('s', HuFormat::$MODS);
        $this->assertArrayHasKey('a', HuFormat::$MODS);
        $this->assertArrayHasKey('E', HuFormat::$MODS);
        $this->assertArrayHasKey('I', HuFormat::$MODS);
    }

    /**
    * Test sprintf_var constant.
    */
    public function testSprintfVarConstant(): void {
        $this->assertEquals('__vAr__', HuFormat::sprintf_var);
    }

    /**
    * Test evaluate_var constant.
    */
    public function testEvaluateVarConstant(): void {
        $this->assertEquals('var', HuFormat::evaluate_var);
    }

    /**
    * Test mods_separator constant.
    */
    public function testModsSeparatorConstant(): void {
        $this->assertEquals(':::', HuFormat::mods_separator);
    }

    /**
    * Test format extends HuError.
    */
    public function testFormatExtendsHuError(): void {
        $format = new HuFormat();
        $this->assertInstanceOf(HuFormat::class, $format);
        $this->assertInstanceOf(\Hubbitus\HuPHP\Debug\HuError::class, $format);
    }

    /**
    * Test HuFormatException extends VariableException.
    */
    public function testFormatExceptionExtendsVariableException(): void {
        $exception = new HuFormatException('test message');
        $this->assertInstanceOf(HuFormatException::class, $exception);
        $this->assertInstanceOf(\Hubbitus\HuPHP\Exceptions\Variables\VariableException::class, $exception);
    }

    /**
    * Test modifier 's' - access object property by name.
    */
    public function testModifierSObjectProperty(): void {
        $obj = new \stdClass();
        $obj->name = 'John';
        $format = new HuFormat(['s:::name'], $obj);
        $this->assertEquals('John', $format->getString());
    }

    /**
    * Test modifier 's' with non-existent property.
    */
    public function testModifierSNonExistentProperty(): void {
        $obj = new \stdClass();
        $obj->name = 'John';
        $format = new HuFormat(['s:::nonexistent'], $obj);
        $this->assertEquals('', $format->getString());
    }

    /**
    * Test modifier 'a' - access array element by key.
    */
    public function testModifierAArrayElement(): void {
        $arr = ['name' => 'John', 'age' => 30];
        $format = new HuFormat(['a:::name'], $arr);
        $this->assertEquals('John', $format->getString());
    }

    /**
    * Test modifier 'a' with non-existent key.
    */
    public function testModifierANonExistentKey(): void {
        $arr = ['name' => 'John'];
        $format = new HuFormat(['a:::nonexistent'], $arr);
        // Accessing non-existent key returns empty string
        $this->assertEquals('', @$format->getString());
    }

    /**
    * Test modifier 'a' with numeric key.
    */
    public function testModifierANumericKey(): void {
        $arr = ['first', 'second', 'third'];
        $format = new HuFormat(['a:::1'], $arr);
        $this->assertEquals('second', $format->getString());
    }

    /**
    * Test modifier 'n' with non-empty value.
    */
    public function testModifierNWithNonEmptyValue(): void {
        $value = 'value';
        $format = new HuFormat(['n:::'], $value);
        $result = $format->getString();
        $this->assertEquals('value', $result);
    }

    /**
    * Test modifier 'p' - sprintf formatting.
    */
    public function testModifierPSprintf(): void {
        // Modifier 'p' uses _format array with sprintf format string
        // First argument after format string is used as value
        $value = 'World';
        $format = new HuFormat(['p:::', 'Hello %s!', $value], $value);
        $this->assertEquals('Hello World!', $format->getString());
    }

    /**
    * Test modifier 'p' with sprintf_var placeholder.
    */
    public function testModifierPSprintfWithPlaceholder(): void {
        $value = 'John';
        // sprintf_var is replaced with _realValue before sprintf is called
        $format = new HuFormat(['p:::', '%s is %d years old', $value, 30], $value);
        $this->assertEquals('John is 30 years old', $format->getString());
    }

    /**
    * Test modifier 'p' without placeholder.
    */
    public function testModifierPWithoutPlaceholder(): void {
        $value = 'test';
        $format = new HuFormat(['p:::', 'Hello World'], $value);
        $this->assertEquals('Hello World', $format->getString());
    }

    /**
    * Test modifier 'e' - evaluate name as PHP expression.
    */
    public function testModifierEEvaluateName(): void {
        // Modifier 'e' uses $var in eval expression
        $arr = ['value' => 42];
        $format = new HuFormat(['e:::$var["value"] * 2'], $arr);
        $result = @$format->getString();
        $this->assertIsString($result);
    }

    /**
    * Test modifier 'E' - evaluate full format as PHP code.
    */
    public function testModifierEEvaluateFull(): void {
        $value = 'input';
        $format = new HuFormat(['E:::', '$var . "_processed"'], $value);
        $this->assertEquals('input_processed', $format->getString());
    }

    /**
    * Test modifier 'E' with complex expression.
    */
    public function testModifierEComplexExpression(): void {
        $value = 10;
        $format = new HuFormat(['E:::', '$var * 2 + 5'], $value);
        $this->assertEquals('25', $format->getString());
    }

    /**
    * Test modifier 'v' - return value itself with string.
    */
    public function testModifierVStringValue(): void {
        $value = 'test value';
        $format = new HuFormat(['v:::'], $value);
        $this->assertEquals('test value', $format->getString());
    }

    /**
    * Test modifier 'v' with integer value.
    */
    public function testModifierVIntegerValue(): void {
        $value = 42;
        $format = new HuFormat(['v:::'], $value);
        $this->assertEquals('42', $format->getString());
    }

    /**
    * Test modifier 'v' with array value.
    */
    public function testModifierVArrayValue(): void {
        $arr = ['key' => 'value'];
        $format = new HuFormat(['v:::'], $arr);
        $result = @$format->getString();
        // Array is printed with print_r
        $this->assertIsString($result);
    }

    /**
    * Test modifier 'v' conflict when _realValued is already true.
    */
    public function testModifierVConflict(): void {
        $value = 'test';
        $format = new HuFormat(['v:::'], $value);
        $reflection = new \ReflectionClass($format);
        $prop = $reflection->getProperty('_realValued');
        $prop->setAccessible(true);
        $prop->setValue($format, true);

        $this->expectException(HuFormatException::class);
        $format->getString();
    }

    /**
    * Test modifier 'v' with Backtrace object.
    */
    public function testModifierVWithBacktrace(): void {
        $bt = new \Hubbitus\HuPHP\Debug\Backtrace();
        $format = new HuFormat(['v:::'], $bt);
        $result = $format->getString();
        $this->assertStringContainsString('Backtrace', $result);
    }

    /**
    * Test modifier 'v' with OutExtraDataBacktrace.
    */
    public function testModifierVWithOutExtraDataBacktrace(): void {
        $data = ['test' => 'value'];
        $btData = new \Hubbitus\HuPHP\Vars\OutExtraDataBacktrace($data);
        $format = new HuFormat(['v:::'], $btData);
        $result = $format->getString();
        // OutExtraDataBacktrace has special handling in modifier 'v'
        $this->assertStringContainsString('OutExtraDataBacktrace', $result);
    }

    /**
    * Test modifier 'k' - return current iteration key.
    */
    public function testModifierKIterationKey(): void {
        $null = null;
        $format = new HuFormat(['k:::'], $null, 'my_key');
        $this->assertEquals('my_key', $format->getString());
    }

    /**
    * Test modifier 'I' - iterate over array.
    */
    public function testModifierIIterateArray(): void {
        $arr = ['a', 'b', 'c'];
        $format = new HuFormat(['I:::' => ['v:::']], $arr);
        $this->assertEquals('abc', $format->getString());
    }

    /**
    * Test modifier 'I' with empty array.
    */
    public function testModifierIEmptyArray(): void {
        $arr = [];
        $format = new HuFormat(['I:::' => ['v:::']], $arr);
        $this->assertEquals('', $format->getString());
    }

    /**
    * Test modifier 'I' with non-iterable value.
    */
    public function testModifierINonIterable(): void {
        $value = 'string';
        $format = new HuFormat(['I:::' => ['v:::']], $value);
        $this->assertEquals('', $format->getString());
    }

    /**
    * Test modifier 'I' with objects.
    */
    public function testModifierIWithObjects(): void {
        $obj1 = new \stdClass();
        $obj1->name = 'obj1';
        $obj2 = new \stdClass();
        $obj2->name = 'obj2';
        $arr = [$obj1, $obj2];
        $format = new HuFormat(['I:::' => ['s:::name']], $arr);
        $result = $format->getString();
        $this->assertStringContainsString('obj1', $result);
        $this->assertStringContainsString('obj2', $result);
    }

    /**
    * Test modifier 'I' with keys using mod k.
    */
    public function testModifierIWithKeys(): void {
        $arr = ['first' => 'a', 'second' => 'b'];
        $format = new HuFormat(['I:::' => ['k:::']], $arr);
        $result = $format->getString();
        $this->assertStringContainsString('first', $result);
        $this->assertStringContainsString('second', $result);
    }

    /**
    * Test modifier 'A' - all modifier with Backtrace.
    */
    public function testModifierAWithBacktrace(): void {
        $bt = new \Hubbitus\HuPHP\Debug\Backtrace();
        $format = new HuFormat(['A:::'], $bt);
        $result = $format->getString();
        $this->assertStringContainsString('Backtrace', $result);
    }

    /**
    * Test modifier 'A' with OutExtraDataBacktrace.
    */
    public function testModifierAWithOutExtraDataBacktrace(): void {
        $data = ['test' => 'value'];
        $btData = new \Hubbitus\HuPHP\Vars\OutExtraDataBacktrace($data);
        $format = new HuFormat(['A:::'], $btData);
        $this->assertStringContainsString('OutExtraDataBacktrace', $format->getString());
    }

    /**
    * Test isMod method present.
    */
    public function testIsModPresent(): void {
        $value = 'test';
        $format = new HuFormat(['v:::'], $value);
        $this->assertTrue($format->isMod('v'));
    }

    /**
    * Test isMod method absent.
    */
    public function testIsModAbsent(): void {
        $value = 'test';
        $format = new HuFormat(['v:::'], $value);
        $this->assertFalse($format->isMod('x'));
    }

    /**
    * Test changeModsStr with add operator.
    */
    public function testChangeModsStrAdd(): void {
        $format = new HuFormat();
        $format->changeModsStr('+v');
        $this->assertTrue($format->isMod('v'));
    }

    /**
    * Test changeModsStr with remove operator.
    */
    public function testChangeModsStrRemove(): void {
        $value = 'test';
        $format = new HuFormat(['v:::'], $value);
        $format->changeModsStr('-v');
        $this->assertFalse($format->isMod('v'));
    }

    /**
    * Test changeModsStr with invert operator.
    */
    public function testChangeModsStrInvert(): void {
        $value = 'test';
        $format = new HuFormat(['v:::'], $value);
        $format->changeModsStr('*v');
        $this->assertFalse($format->isMod('v'));

        $format->changeModsStr('*v');
        $this->assertTrue($format->isMod('v'));
    }

    /**
    * Test changeModsStr with unknown modifier.
    */
    public function testChangeModsStrUnknownModifier(): void {
        $format = new HuFormat();
        $this->expectException(\Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException::class);
        $format->changeModsStr('+x');
    }

    /**
    * Test changeModsStr with operator but no modifier.
    */
    public function testChangeModsStrOperatorWithoutModifier(): void {
        $format = new HuFormat();
        $this->expectException(\Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException::class);
        $format->changeModsStr('+');
    }

    /**
    * Test changeModsStr with unknown operator.
    */
    public function testChangeModsStrUnknownOperator(): void {
        $format = new HuFormat();
        $this->expectException(\Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException::class);
        $format->changeModsStr('?v');
    }

    /**
    * Test changeModsStr with multiple operations.
    */
    public function testChangeModsStrMultipleOperations(): void {
        $value = 'test';
        $format = new HuFormat(['v:::'], $value);
        $format->changeModsStr('-v+s');
        $this->assertFalse($format->isMod('v'));
        $this->assertTrue($format->isMod('s'));
    }

    /**
    * Test getModsStr method.
    */
    public function testGetModsStr(): void {
        $value = 'test';
        $format = new HuFormat(['v:::'], $value);
        // getModsStr returns the modifier string - suppress reference warning
        $modsStr = @$format->getModsStr();
        $this->assertIsString($modsStr);
    }

    /**
    * Test parseModsName with separator.
    */
    public function testParseModsNameWithSeparator(): void {
        $value = 'test';
        $format = new HuFormat(['v:::name'], $value);
        // Just verify the format is parsed correctly
        $this->assertTrue($format->isMod('v'));
    }


    /**
    * Test parseModsName without separator.
    */
    public function testParseModsNameWithoutSeparator(): void {
        $format = new HuFormat();
        $reflection = new \ReflectionClass($format);
        $method = $reflection->getMethod('parseModsName');
        $method->setAccessible(true);
        $method->invoke($format, 'plain_text');

        $modsProp = $reflection->getProperty('_modArr');
        $modsProp->setAccessible(true);
        $this->assertEmpty($modsProp->getValue($format));
    }

    /**
    * Test setFormat with associative array.
    */
    public function testSetFormatAssociativeArray(): void {
        $value = 'test';
        $format = new HuFormat(['v:::' => ['test']], $value);
        $this->assertInstanceOf(HuFormat::class, $format);
    }

    /**
    * Test setFormat with plain string (no modifiers).
    */
    public function testSetFormatPlainString(): void {
        $format = new HuFormat();
        $format->setFormat('plain text');
        // Plain string without modifiers returns empty string
        $this->assertEquals('', $format->getString());
    }

    /**
    * Test getString with null value.
    */
    public function testGetStringNullValue(): void {
        $null = null;
        $format = new HuFormat(['v:::'], $null);
        $this->assertEquals('', $format->getString());
    }

    /**
    * Test setValue returns self.
    */
    public function testSetValueReturnsSelf(): void {
        $format = new HuFormat();
        $value = 'test';
        $result = $format->setValue($value);
        $this->assertSame($format, $result);
    }

    /**
    * Test getValue returns reference when _realValued is true.
    */
    public function testGetValueReturnsReference(): void {
        $obj = (object)['name' => 'test'];
        $format = new HuFormat(['s:::name'], $obj);
        $format->getString();
        $value = $format->getValue();
        $this->assertEquals('test', $value);
    }

    /**
    * Test setFormat clears previous format.
    */
    public function testSetFormatClearsPrevious(): void {
        $value1 = 'test1';
        $format = new HuFormat(['v:::'], $value1);
        $value2 = 'test2';
        $format->set(['v:::'], $value2);
        $this->assertEquals('test2', $format->getString());
    }

    /**
    * Test parseMods protected method via changeModsStr.
    */
    public function testParseModsViaChangeModsStr(): void {
        $value = 'test';
        $format = new HuFormat(['v:::'], $value);
        // changeModsStr calls parseMods internally
        $format->changeModsStr('+s');
        $this->assertTrue($format->isMod('s'));
    }

    /**
    * Test initMODS is called automatically.
    */
    public function testInitMODSAutomatic(): void {
        // initMODS is called in constructor
        $this->assertIsArray(HuFormat::$MODS);
        $this->assertNotEmpty(HuFormat::$MODS);
    }

    /**
    * Test setValue with array by reference.
    */
    public function testSetValueWithArray(): void {
        $arr = ['key' => 'value'];
        $format = new HuFormat();
        $format->setValue($arr);
        $this->assertEquals($arr, $format->getValue());
    }
}
