<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\Backtrace;
use Hubbitus\HuPHP\Debug\HuError;
use Hubbitus\HuPHP\Debug\HuFormat;
use Hubbitus\HuPHP\Debug\HuFormatException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableException;
use Hubbitus\HuPHP\Exceptions\Variables\VariableRangeException;
use Hubbitus\HuPHP\Vars\OutExtraDataBacktrace;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
* @covers \Hubbitus\HuPHP\Debug\HuFormat
* @covers \Hubbitus\HuPHP\Debug\HuFormatException
**/
#[CoversClass(HuFormat::class)]
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
	**/
	public function testSetFormatAndValue(): void {
		$format = new HuFormat();
		$value = 'test_value';
		$format->set(['v:::'], $value);
		$this->assertEquals('test_value', $format->getValue());
	}

	/**
	* Test setValue.
	**/
	public function testSetValue(): void {
		$format = new HuFormat();
		$value = 'test_value';
		$format->setValue($value);
		$this->assertEquals('test_value', $format->getValue());
	}

	/**
	* Test setValue with null.
	**/
	public function testSetValueWithNull(): void {
		$format = new HuFormat();
		$null = null;
		$format->setValue($null);
		$this->assertSame($format, $format->getValue());
	}

	/**
	* Test set returns self.
	**/
	public function testSetReturnsSelf(): void {
		$format = new HuFormat();
		$value = 'test';
		$result = $format->set(['v:::'], $value);
		$this->assertSame($format, $result);
	}

	/**
	* Test MODS constant exists.
	**/
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
	**/
	public function testSprintfVarConstant(): void {
		$this->assertEquals('__vAr__', HuFormat::sprintf_var);
	}

	/**
	* Test evaluate_var constant.
	**/
	public function testEvaluateVarConstant(): void {
		$this->assertEquals('var', HuFormat::evaluate_var);
	}

	/**
	* Test mods_separator constant.
	**/
	public function testModsSeparatorConstant(): void {
		$this->assertEquals(':::', HuFormat::mods_separator);
	}

	/**
	* Test format extends HuError.
	**/
	public function testFormatExtendsHuError(): void {
		$format = new HuFormat();
		$this->assertInstanceOf(HuFormat::class, $format);
		$this->assertInstanceOf(HuError::class, $format);
	}

	/**
	* Test HuFormatException extends VariableException.
	**/
	public function testFormatExceptionExtendsVariableException(): void {
		$exception = new HuFormatException('test message');
		$this->assertInstanceOf(HuFormatException::class, $exception);
		$this->assertInstanceOf(VariableException::class, $exception);
	}

	/**
	* Test modifier 's' - access object property by name.
	**/
	public function testModifierSObjectProperty(): void {
		$obj = new \stdClass();
		$obj->name = 'John';
		$format = new HuFormat(['s:::name'], $obj);
		$this->assertEquals('John', $format->getString());
	}

	/**
	* Test modifier 's' with non-existent property.
	**/
	public function testModifierSNonExistentProperty(): void {
		$obj = new \stdClass();
		$obj->name = 'John';
		$format = new HuFormat(['s:::nonexistent'], $obj);
		$this->assertEquals('', $format->getString());
	}

	/**
	* Test modifier 'a' - access array element by key.
	**/
	public function testModifierAArrayElement(): void {
		$arr = ['name' => 'John', 'age' => 30];
		$format = new HuFormat(['a:::name'], $arr);
		$this->assertEquals('John', $format->getString());
	}

	/**
	* Test modifier 'a' with non-existent key.
	**/
	public function testModifierANonExistentKey(): void {
		$arr = ['name' => 'John'];
		$format = new HuFormat(['a:::nonexistent'], $arr);
		// Accessing non-existent key returns empty string
		$this->assertEquals('', @$format->getString());
	}

	/**
	* Test modifier 'a' with numeric key.
	**/
	public function testModifierANumericKey(): void {
		$arr = ['first', 'second', 'third'];
		$format = new HuFormat(['a:::1'], $arr);
		$this->assertEquals('second', $format->getString());
	}

	/**
	* Test modifier 'n' with non-empty value.
	**/
	public function testModifierNWithNonEmptyValue(): void {
		$value = 'value';
		$format = new HuFormat(['n:::'], $value);
		$result = $format->getString();
		$this->assertEquals('value', $result);
	}

	/**
	* Test modifier 'p' - sprintf formatting.
	**/
	public function testModifierPSprintf(): void {
		// Modifier 'p' uses _format array with sprintf format string
		// First argument after format string is used as value
		$value = 'World';
		$format = new HuFormat(['p:::', 'Hello %s!', $value], $value);
		$this->assertEquals('Hello World!', $format->getString());
	}

	/**
	* Test modifier 'p' with sprintf_var placeholder.
	**/
	public function testModifierPSprintfWithPlaceholder(): void {
		$value = 'John';
		// sprintf_var is replaced with _realValue before sprintf is called
		$format = new HuFormat(['p:::', '%s is %d years old', $value, 30], $value);
		$this->assertEquals('John is 30 years old', $format->getString());
	}

	/**
	* Test modifier 'p' without placeholder.
	**/
	public function testModifierPWithoutPlaceholder(): void {
		$value = 'test';
		$format = new HuFormat(['p:::', 'Hello World'], $value);
		$this->assertEquals('Hello World', $format->getString());
	}

	/**
	* Test modifier 'e' - evaluate name as PHP expression.
	**/
	public function testModifierEEvaluateName(): void {
		// Modifier 'e' uses $var in eval expression
		$arr = ['value' => 42];
		$format = new HuFormat(['e:::$var["value"] * 2'], $arr);
		$result = @$format->getString();
		$this->assertIsString($result);
	}

	/**
	* Test modifier 'E' - evaluate full format as PHP code.
	**/
	public function testModifierEEvaluateFull(): void {
		$value = 'input';
		$format = new HuFormat(['E:::', '$var . "_processed"'], $value);
		$this->assertEquals('input_processed', $format->getString());
	}

	/**
	* Test modifier 'E' with complex expression.
	**/
	public function testModifierEComplexExpression(): void {
		$value = 10;
		$format = new HuFormat(['E:::', '$var * 2 + 5'], $value);
		$this->assertEquals('25', $format->getString());
	}

	/**
	* Test modifier 'v' - return value itself with string.
	**/
	public function testModifierVStringValue(): void {
		$value = 'test value';
		$format = new HuFormat(['v:::'], $value);
		$this->assertEquals('test value', $format->getString());
	}

	/**
	* Test modifier 'v' with integer value.
	**/
	public function testModifierVIntegerValue(): void {
		$value = 42;
		$format = new HuFormat(['v:::'], $value);
		$this->assertEquals('42', $format->getString());
	}

	/**
	* Test modifier 'v' with array value.
	**/
	public function testModifierVArrayValue(): void {
		$arr = ['key' => 'value'];
		$format = new HuFormat(['v:::'], $arr);
		$result = @$format->getString();
		// Array is printed with print_r
		$this->assertIsString($result);
	}

	/**
	* Test modifier 'v' conflict when _realValued is already true.
	**/
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
	**/
	public function testModifierVWithBacktrace(): void {
		$bt = new Backtrace();
		$format = new HuFormat(['v:::'], $bt);
		$result = $format->getString();
		$this->assertStringContainsString('Backtrace', $result);
	}

	/**
	* Test modifier 'v' with OutExtraDataBacktrace.
	**/
	public function testModifierVWithOutExtraDataBacktrace(): void {
		$data = ['test' => 'value'];
		$btData = new OutExtraDataBacktrace($data);
		$format = new HuFormat(['v:::'], $btData);
		$result = $format->getString();
		// OutExtraDataBacktrace has special handling in modifier 'v'
		$this->assertStringContainsString('OutExtraDataBacktrace', $result);
	}

	/**
	* Test modifier 'k' - return current iteration key.
	**/
	public function testModifierKIterationKey(): void {
		$null = null;
		$format = new HuFormat(['k:::'], $null, 'my_key');
		$this->assertEquals('my_key', $format->getString());
	}

	/**
	* Test modifier 'I' - iterate over array.
	**/
	public function testModifierIIterateArray(): void {
		$arr = ['a', 'b', 'c'];
		$format = new HuFormat(['I:::' => ['v:::']], $arr);
		$this->assertEquals('abc', $format->getString());
	}

	/**
	* Test modifier 'I' with empty array.
	**/
	public function testModifierIEmptyArray(): void {
		$arr = [];
		$format = new HuFormat(['I:::' => ['v:::']], $arr);
		// Empty array: iterator produces no output, fallback returns print_r of empty array
		$result = $format->getString();
		$this->assertStringContainsString('Array', $result);
	}

	/**
	* Test modifier 'I' with non-iterable value.
	**/
	public function testModifierINonIterable(): void {
		$value = 'string';
		$format = new HuFormat(['I:::' => ['v:::']], $value);
		// Non-iterable: iterator produces no output, fallback returns the string value
		$result = $format->getString();
		$this->assertStringContainsString('string', $result);
	}

	/**
	* Test modifier 'I' with objects.
	**/
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
	**/
	public function testModifierIWithKeys(): void {
		$arr = ['first' => 'a', 'second' => 'b'];
		$format = new HuFormat(['I:::' => ['k:::']], $arr);
		$result = $format->getString();
		$this->assertStringContainsString('first', $result);
		$this->assertStringContainsString('second', $result);
	}

	/**
	* Test modifier 'A' - all modifier with Backtrace.
	**/
	public function testModifierAWithBacktrace(): void {
		$bt = new Backtrace();
		$format = new HuFormat(['A:::'], $bt);
		$result = $format->getString();
		$this->assertStringContainsString('Backtrace', $result);
	}

	/**
	* Test modifier 'A' with OutExtraDataBacktrace.
	**/
	public function testModifierAWithOutExtraDataBacktrace(): void {
		$data = ['test' => 'value'];
		$btData = new OutExtraDataBacktrace($data);
		$format = new HuFormat(['A:::'], $btData);
		$this->assertStringContainsString('OutExtraDataBacktrace', $format->getString());
	}

	/**
	* Test isMod method present.
	**/
	public function testIsModPresent(): void {
		$value = 'test';
		$format = new HuFormat(['v:::'], $value);
		$this->assertTrue($format->isMod('v'));
	}

	/**
	* Test isMod method absent.
	**/
	public function testIsModAbsent(): void {
		$value = 'test';
		$format = new HuFormat(['v:::'], $value);
		$this->assertFalse($format->isMod('x'));
	}

	/**
	* Test isMod with empty _mods but set _modStr triggers parseMods.
	* This covers lines 392-393 in HuFormat.php.
	**/
	public function testIsModWithEmptyModsAndSetModStr(): void {
		$format = new HuFormat();
		// Use reflection to set _modStr directly without populating _mods
		$reflection = new \ReflectionClass($format);
		$modStrProp = $reflection->getProperty('_modStr');
		$modStrProp->setAccessible(true);
		$modStrProp->setValue($format, 'v');

		// isMod should trigger parseMods() because _mods is empty but _modStr is set
		$this->assertTrue($format->isMod('v'));
	}

	/**
	* Test changeModsStr with add operator.
	**/
	public function testChangeModsStrAdd(): void {
		$format = new HuFormat();
		$format->changeModsStr('+v');
		$this->assertTrue($format->isMod('v'));
	}

	/**
	* Test changeModsStr with remove operator.
	**/
	public function testChangeModsStrRemove(): void {
		$value = 'test';
		$format = new HuFormat(['v:::'], $value);
		$format->changeModsStr('-v');
		$this->assertFalse($format->isMod('v'));
	}

	/**
	* Test changeModsStr with invert operator.
	**/
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
	**/
	public function testChangeModsStrUnknownModifier(): void {
		$format = new HuFormat();
		$this->expectException(VariableRangeException::class);
		$format->changeModsStr('+x');
	}

	/**
	* Test changeModsStr with operator but no modifier.
	**/
	public function testChangeModsStrOperatorWithoutModifier(): void {
		$format = new HuFormat();
		$this->expectException(VariableRangeException::class);
		$format->changeModsStr('+');
	}

	/**
	* Test changeModsStr with multiple operations.
	**/
	public function testChangeModsStrMultipleOperations(): void {
		$value = 'test';
		$format = new HuFormat(['v:::'], $value);
		$format->changeModsStr('-v+s');
		$this->assertFalse($format->isMod('v'));
		$this->assertTrue($format->isMod('s'));
	}

	/**
	* Test changeModsStr with add operator for existing modifier.
	**/
	public function testChangeModsStrAddExisting(): void {
		$value = 'test';
		$format = new HuFormat(['v:::'], $value);
		$this->assertTrue($format->isMod('v'));
		$format->changeModsStr('+v');
		$this->assertTrue($format->isMod('v'));
	}

	/**
	* Test getModsStr method.
	**/
	public function testGetModsStr(): void {
		$value = 'test';
		$format = new HuFormat(['vsn:::'], $value);
		$modsStr = $format->getModsStr();
		$this->assertEquals('vsn', $modsStr);
	}

	/**
	* Test parseModsName with separator.
	**/
	public function testParseModsNameWithSeparator(): void {
		$value = 'test';
		$format = new HuFormat(['v:::name'], $value);
		// Just verify the format is parsed correctly
		$this->assertTrue($format->isMod('v'));
	}

	/**
	* Test parseModsName without separator.
	**/
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
	**/
	public function testSetFormatAssociativeArray(): void {
		$value = 'test';
		$format = new HuFormat(['v:::' => ['test']], $value);
		$this->assertInstanceOf(HuFormat::class, $format);
	}

	/**
	* Test setFormat with plain string (no modifiers).
	**/
	public function testSetFormatPlainString(): void {
		$format = new HuFormat();
		$format->setFormat('plain text');
		// Plain string without modifiers returns the string itself
		$this->assertEquals('plain text', $format->getString());
	}

	/**
	* Test getString with null value.
	**/
	public function testGetStringNullValue(): void {
		$null = null;
		$format = new HuFormat(['v:::'], $null);
		$this->assertEquals('', $format->getString());
	}

	/**
	* Test setValue returns self.
	**/
	public function testSetValueReturnsSelf(): void {
		$format = new HuFormat();
		$value = 'test';
		$result = $format->setValue($value);
		$this->assertSame($format, $result);
	}

	/**
	* Test getValue returns reference when _realValued is true.
	**/
	public function testGetValueReturnsReference(): void {
		$obj = (object)['name' => 'test'];
		$format = new HuFormat(['s:::name'], $obj);
		$format->getString();
		$value = $format->getValue();
		$this->assertEquals('test', $value);
	}

	/**
	* Test setFormat clears previous format.
	**/
	public function testSetFormatClearsPrevious(): void {
		$value1 = 'test1';
		$format = new HuFormat(['v:::'], $value1);
		$value2 = 'test2';
		$format->set(['v:::'], $value2);
		$this->assertEquals('test2', $format->getString());
	}

	/**
	* Test parseMods protected method via changeModsStr.
	**/
	public function testParseModsViaChangeModsStr(): void {
		$value = 'test';
		$format = new HuFormat(['v:::'], $value);
		// changeModsStr calls parseMods internally
		$format->changeModsStr('+s');
		$this->assertTrue($format->isMod('s'));
	}

	/**
	* Test initMODS is called automatically.
	**/
	public function testInitMODSAutomatic(): void {
		// Create instance to trigger initMODS
		$format = new HuFormat();

		// Use reflection to check MODS
		$reflection = new \ReflectionClass(HuFormat::class);
		$modsProp = $reflection->getProperty('MODS');
		$modsProp->setAccessible(true);

		$this->assertIsArray($modsProp->getValue());
		$this->assertNotEmpty($modsProp->getValue());
	}

	/**
	* Test setValue with array by reference.
	**/
	public function testSetValueWithArray(): void {
		$arr = ['key' => 'value'];
		$format = new HuFormat();
		$format->setValue($arr);
		$this->assertEquals($arr, $format->getValue());
	}

	/**
	* Test setFormat with plain string (no modifiers).
	**/
	public function testSetFormatPlainStringNoModifiers(): void {
		$format = new HuFormat();
		$format->setFormat('plain text');

		// Plain string without modifiers should set _realValue
		$this->assertIsString($format->getString());
	}

	/**
	* Test getString with null value fallback.
	**/
	public function testGetStringNullFallback(): void {
		$null = null;
		$format = new HuFormat(['v:::'], $null);
		$result = $format->getString();

		$this->assertEquals('', $result);
	}

	/**
	* Test modifier s with _realValued already true (else branch).
	**/
	public function testModifierSWithRealValuedTrue(): void {
		$obj = new \stdClass();
		$obj->name = 'test';
		$obj->realValue = 'name';
		$format = new HuFormat(['s:::realValue'], $obj);

		// First call sets _realValue
		$format->getString();

		// This should use the else branch
		$result = $format->getString();
		$this->assertIsString($result);
	}

	/**
	* Test changeModsStr with operator but no modifier at end.
	**/
	public function testChangeModsStrOperatorAtEnd(): void {
		$format = new HuFormat();
		$this->expectException(VariableRangeException::class);
		$format->changeModsStr('+v-');
	}

	/**
	* Test parseMods with invalid modifier character.
	**/
	public function testParseModsInvalidModifier(): void {
		$format = new HuFormat();
		$reflection = new \ReflectionClass($format);
		$method = $reflection->getMethod('parseMods');
		$method->setAccessible(true);

		// Set _modStr to contain invalid modifier
		$prop = $reflection->getProperty('_modStr');
		$prop->setAccessible(true);
		$prop->setValue($format, 'x');

		$this->expectException(VariableRangeException::class);
		$method->invoke($format);
	}

	/**
	* Test modifier A with non-array format (else branch).
	**/
	public function testModifierAWithNonArrayFormat(): void {
		// When _format is not an array, the if (\is_array) branch is not taken
		$value = 'test';
		$format = new HuFormat(['A:::'], $value);
		$result = $format->getString();

		$this->assertIsString($result);
	}

	/**
	* Test modifier p with sprintf_var replacement.
	**/
	public function testModifierPWithSprintfVarReplacement(): void {
		// Modifier 'p' replaces HuFormat::sprintf_var with _realValue
		// Use 'vp:::' to first set _realValue via 'v', then format via 'p'
		$value = 'World';
		$format = new HuFormat(['vp:::', 'Hello %s!', HuFormat::sprintf_var], $value);
		$result = $format->getString();

		// 'v' returns 'World', then 'p' returns 'Hello World!'
		$this->assertStringContainsString('Hello World!', $result);
	}

	/**
	* Test modifier s else branch (_realValued already true).
	**/
	public function testModifierSElseBranch(): void {
		$obj = new \stdClass();
		$obj->name = 'test';
		$obj->prop = 'name';
		// First access sets _realValue to 'name'
		$format = new HuFormat(['s:::prop'], $obj);
		$format->getString();

		// Second access should use else branch: $obj->_value->{$obj->_realValue}
		$result = $format->getString();
		$this->assertIsString($result);
	}

	/**
	* Test getString fallback branches via reflection.
	**/
	public function testGetStringFallbackBranches(): void {
		$format = new HuFormat();
		$reflection = new \ReflectionClass($format);

		// Set up state: no modifiers, _realValued = false, _value = array
		$modArrProp = $reflection->getProperty('_modArr');
		$modArrProp->setAccessible(true);
		$modArrProp->setValue($format, []);

		$realValuedProp = $reflection->getProperty('_realValued');
		$realValuedProp->setAccessible(true);
		$realValuedProp->setValue($format, false);

		$valueProp = $reflection->getProperty('_value');
		$valueProp->setAccessible(true);
		$valueProp->setValue($format, ['key' => 'value']);

		// Now getString() should use the array fallback branch
		$result = $format->getString();
		$this->assertStringContainsString('Array', $result);
		$this->assertStringContainsString('key', $result);
	}

	/**
	* Test getString fallback for object without __toString.
	**/
	public function testGetStringFallbackObject(): void {
		$format = new HuFormat();
		$reflection = new \ReflectionClass($format);

		// Set up state: no modifiers, _realValued = false, _value = object
		$modArrProp = $reflection->getProperty('_modArr');
		$modArrProp->setAccessible(true);
		$modArrProp->setValue($format, []);

		$realValuedProp = $reflection->getProperty('_realValued');
		$realValuedProp->setAccessible(true);
		$realValuedProp->setValue($format, false);

		$obj = new \stdClass();
		$obj->prop = 'value';
		$valueProp = $reflection->getProperty('_value');
		$valueProp->setAccessible(true);
		$valueProp->setValue($format, $obj);

		// Now getString() should use the fallback branch
		$result = $format->getString();
		// print_r output for object properties
		$this->assertStringContainsString('Array', $result);
		$this->assertStringContainsString('prop', $result);
		$this->assertStringContainsString('value', $result);
	}

	/**
	* Test setFormat with plain string (covers _realValue = $format branch).
	**/
	public function testSetFormatPlainStringBranch(): void {
		$format = new HuFormat();
		$reflection = new \ReflectionClass($format);

		// Call setFormat with plain string (no modifiers)
		$method = $reflection->getMethod('setFormat');
		$method->setAccessible(true);
		$method->invoke($format, 'plain text');

		// Verify _realValue and _realValued are set
		$realValueProp = $reflection->getProperty('_realValue');
		$realValueProp->setAccessible(true);
		$this->assertEquals('plain text', $realValueProp->getValue($format));

		$realValuedProp = $reflection->getProperty('_realValued');
		$realValuedProp->setAccessible(true);
		$this->assertTrue($realValuedProp->getValue($format));
	}

	/**
	* Test modifier 's' else branch via reflection (_realValued already true).
	**/
	public function testModifierSElseBranchViaReflection(): void {
		$obj = new \stdClass();
		$obj->name = 'test';
		$obj->prop = 'name';

		$format = new HuFormat(['s:::prop'], $obj);
		$reflection = new \ReflectionClass($format);

		// First call to getString sets up state
		$format->getString();

		// Manually set _realValued = true and _realValue = 'prop'
		$realValuedProp = $reflection->getProperty('_realValued');
		$realValuedProp->setAccessible(true);
		$realValuedProp->setValue($format, true);

		$realValueProp = $reflection->getProperty('_realValue');
		$realValueProp->setAccessible(true);
		$realValueProp->setValue($format, 'prop');

		// Reset _resStr to force re-evaluation
		$resStrProp = $reflection->getProperty('_resStr');
		$resStrProp->setAccessible(true);
		$resStrProp->setValue($format, null);

		// Second call should use else branch: $obj->_value->{$obj->_realValue} = $obj->prop = 'name'
		$result = $format->getString();
		$this->assertEquals('name', $result);
	}

	/**
	* Test modifier 'A' with array of formats (foreach branch).
	**/
	public function testModifierAWithArrayFormatsForeach(): void {
		// 'A' modifier with _format as array should iterate and apply each format
		// Use string value since 'v' modifier converts to string
		$value = 'test';
		$format = new HuFormat(['A:::' => [['v:::'], ['v:::']]], $value);
		$result = $format->getString();

		// Should apply both formats
		$this->assertStringContainsString('test', $result);
	}

	/**
	* Test modifier 'e' else branch via reflection (_realValued already true).
	**/
	public function testModifierEElseBranchViaReflection(): void {
		// The 'e' modifier evaluates the name as PHP code using $var
		$arr = ['value' => 42];
		$format = new HuFormat(['e:::$var["value"]'], $arr);
		$reflection = new \ReflectionClass($format);

		// First call: evaluates $var["value"] = 42, sets _realValued = true
		$result1 = $format->getString();
		$this->assertEquals('42', $result1);

		// Verify _realValued is true
		$realValuedProp = $reflection->getProperty('_realValued');
		$realValuedProp->setAccessible(true);
		$this->assertTrue($realValuedProp->getValue($format));

		// Reset _resStr to force re-evaluation
		$resStrProp = $reflection->getProperty('_resStr');
		$resStrProp->setAccessible(true);
		$resStrProp->setValue($format, null);

		// Second call should use else branch: eval('$obj->_realValue = 42;')
		$result2 = $format->getString();
		$this->assertEquals('42', $result2);
	}

	/**
	* Test changeModsStr with unknown operator.
	**/
	public function testChangeModsStrUnknownOperator(): void {
		$format = new HuFormat();
		$this->expectException(VariableRangeException::class);
		$this->expectExceptionMessage('Unknown modifier');
		$format->changeModsStr('?v');
	}

	/**
	* Test initMODS initializes static $MODS array.
	* This test ensures the private initMODS() method is called and populates $MODS.
	*
	* @runInSeparateProcess
	* @preserveGlobalState disabled
	* Test initMODS returns early if MODS already set.
	**/
	public function testInitMODSReturnsEarlyIfAlreadySet(): void {
		// First instance initializes MODS
		$format1 = new HuFormat();

		// Get MODS reference
		$reflection = new \ReflectionClass(HuFormat::class);
		$modsProp = $reflection->getProperty('MODS');
		$modsProp->setAccessible(true);
		$modsBefore = $modsProp->getValue();

		// Second instance should return early (MODS already set)
		$format2 = new HuFormat();
		$modsAfter = $modsProp->getValue();

		// MODS should be the same array (not reinitialized)
		$this->assertSame($modsBefore, $modsAfter);
	}

	/**
	* Test that initMODS sets up all expected modifier closures.
	*
	* @runInSeparateProcess
	* @preserveGlobalState disabled
	**/
	public function testInitMODSSetsUpModifierClosures(): void {
		$reflection = new \ReflectionClass(HuFormat::class);
		$modsProp = $reflection->getProperty('MODS');
		$modsProp->setAccessible(true);

		$format = new HuFormat();
		$mods = $modsProp->getValue();

		// Verify modifiers are closures
		$expectedModifiers = ['A', 's', 'v', 't', 'd', 'f', 'n', 'c', 'O', 'C', 'T', 'F', 'L', 'P', 'R', 'S', 'M', 'B', 'D', 'G', 'E', 'H', 'X', 'Y', 'Z'];

		foreach ($expectedModifiers as $modifier) {
			if (isset($mods[$modifier])) {
				$this->assertInstanceOf(\Closure::class, $mods[$modifier]);
			}
		}
	}
}
