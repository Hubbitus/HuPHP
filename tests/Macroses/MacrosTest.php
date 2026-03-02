<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Macroses;

use Hubbitus\HuPHP\Exceptions\HaltException;
use PHPUnit\Framework\TestCase;

use function Hubbitus\HuPHP\Macroses\DEFINED_CLASS;
use function Hubbitus\HuPHP\Macroses\EMPTY_INT;
use function Hubbitus\HuPHP\Macroses\EMPTY_callback;
use function Hubbitus\HuPHP\Macroses\exit_count;
use function Hubbitus\HuPHP\Macroses\hit_count;
use function Hubbitus\HuPHP\Macroses\IS_SET_VAR;
use function Hubbitus\HuPHP\Macroses\ISSET_VAR;
use function Hubbitus\HuPHP\Macroses\unicode_ucfirst;
use function Hubbitus\HuPHP\Macroses\unicode_wordwrap;

/**
 * Tests for macro functions in the Macroses namespace.
 */
class MacrosTest extends TestCase {

    public function testUniordWithAsciiCharacter(): void {
        $result = \uniord('A');

        $this->assertEquals(65, $result);
    }

    public function testUniordWithTwoByteUtf8(): void {
        // Cyrillic 'А' (U+0410)
        $result = \uniord('А');

        $this->assertEquals(1040, $result);
    }

    public function testUniordWithThreeByteUtf8(): void {
        // Euro sign '€' (U+20AC)
        $result = \uniord('€');

        $this->assertEquals(8364, $result);
    }

    public function testUniordWithFourByteUtf8(): void {
        // Emoji '😀' (U+1F600)
        $result = \uniord('😀');

        $this->assertEquals(128512, $result);
    }

    public function testUniordWithInvalidCharacter(): void {
        // Invalid UTF-8 sequence (>= 254)
        $result = \uniord(chr(254));

        $this->assertFalse($result);
    }

    public function testUnichrWithAsciiValue(): void {
        $result = \unichr(65);

        $this->assertEquals('A', $result);
    }

    public function testUnichrWithTwoByteValue(): void {
        // Cyrillic 'А' (U+0410)
        $result = \unichr(1040);

        $this->assertEquals('А', $result);
    }

    public function testUnichrWithThreeByteValue(): void {
        // Euro sign '€' (U+20AC)
        $result = \unichr(8364);

        $this->assertEquals('€', $result);
    }

    public function testDefinedClassWithDefinedClass(): void {
        $result = \DEFINED_CLASS('stdClass');

        $this->assertEquals('stdClass', $result);
    }

    public function testDefinedClassWithUndefinedClass(): void {
        $result = \DEFINED_CLASS('NonExistentClass12345');

        $this->assertNull($result);
    }

    public function testDefinedClassWithMultipleClasses(): void {
        $result = \DEFINED_CLASS('NonExistentClass', 'stdClass', 'Exception');

        $this->assertEquals('stdClass', $result);
    }

    public function testExitCountExists(): void {
        // Test that exit_count function exists in the Macroses namespace
        $this->assertTrue(\function_exists('Hubbitus\HuPHP\Macroses\exit_count'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testExitCountThrowsHaltException(): void {
        // In fresh process, counter starts from 0
        // First call increments to 1, second to 2 which should throw

        // First call should not throw
        exit_count(2, 'Test halt message');

        // Second call should throw
        $this->expectException(HaltException::class);
        $this->expectExceptionMessage('Test halt message');
        exit_count(2, 'Test halt message');
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testExitCountDoesNotThrowBeforeCountReached(): void {
        // In fresh process, counter starts from 0
        // exit_count(5) will increment counter to 1, which is not equal to 5
        // So it should not throw and return void

        // Should not throw exception (returns void)
        exit_count(5);

        // Verify function completed without exception
        $this->assertTrue(true);
    }

    public function testHitCountReturnsInteger(): void {
        $result = hit_count(10);

        $this->assertIsInt($result);
    }

    public function testHitCountIncrementsCounter(): void {
        // Get current count
        $first = hit_count(1000);
        $second = hit_count(1000);

        // Counter should increment
        $this->assertEquals($first + 1, $second);
    }

    public function testEmptyCallbackWithTrueCondition(): void {
        $result = \EMPTY_callback(fn($x) => $x > 0, 1, 2, 3);

        $this->assertEquals(1, $result);
    }

    public function testEmptyCallbackWithAllFalseConditions(): void {
        $result = \EMPTY_callback(fn($x) => $x < 0, 1, 2, 3);

        // Returns nothing (null) when all conditions are false
        $this->assertNull($result);
    }

    public function testEmptyCallbackWithSingleArgument(): void {
        $result = \EMPTY_callback(fn($x) => $x === 'test', 'test');

        $this->assertEquals('test', $result);
    }

    public function testEmptyCallbackWithNoArguments(): void {
        $result = \EMPTY_callback(fn($x) => true);

        // No arguments to check, returns null
        $this->assertNull($result);
    }

    public function testIssetVarReturnsValueWhenSet(): void {
        $value = 'test';
        $result = ISSET_VAR($value);

        $this->assertSame('test', $result);
    }

    public function testIssetVarReturnsNullWhenNotSet(): void {
        $result = ISSET_VAR($undefined);

        $this->assertNull($result);
    }

    public function testIsSetVarReturnsValueFromArray(): void {
        $array = ['key' => 'value'];
        $result = IS_SET_VAR('key', $array);

        $this->assertSame('value', $result);
    }

    public function testIsSetVarReturnsNullFromNonExistentKey(): void {
        $array = ['key' => 'value'];
        $result = IS_SET_VAR('nonexistent', $array);

        $this->assertNull($result);
    }

    public function testIsSetFunctionExists(): void {
        $this->assertTrue(function_exists('Hubbitus\HuPHP\Macroses\is_set'));
    }

    public function testUnicodeUcfirstExists(): void {
        $this->assertTrue(function_exists('Hubbitus\HuPHP\Macroses\unicode_ucfirst'));
    }

    public function testUnicodeWordwrapExists(): void {
        $this->assertTrue(function_exists('Hubbitus\HuPHP\Macroses\unicode_wordwrap'));
    }

    public function testUnicodeWordwrapWithDefaultLength(): void {
        $text = 'This is a test sentence with some words';
        $result = unicode_wordwrap($text, 10);

        $this->assertIsString($result);
        $this->assertStringContainsString("\n", $result);
    }

    public function testUnicodeWordwrapWithCustomBreak(): void {
        $text = 'Short text';
        $result = unicode_wordwrap($text, 5, '<br>');

        $this->assertIsString($result);
        $this->assertStringContainsString('<br>', $result);
    }

    public function testUnicodeWordwrapWithCut(): void {
        $text = 'Verylongwordwithoutspaces';
        $result = unicode_wordwrap($text, 5, "\n", true);

        $this->assertIsString($result);
        $this->assertStringContainsString("\n", $result);
    }

    public function testUnicodeUcfirstWithAsciiCharacter(): void {
        $result = unicode_ucfirst('hello');

        $this->assertEquals('Hello', $result);
    }

    public function testUnicodeUcfirstWithCyrillicCharacter(): void {
        $result = unicode_ucfirst('привет');

        $this->assertEquals('Привет', $result);
    }

    public function testUnicodeUcfirstWithEmptyString(): void {
        $result = unicode_ucfirst('');

        $this->assertEquals('', $result);
    }

    public function testHitCountExists(): void {
        $this->assertTrue(function_exists('Hubbitus\HuPHP\Macroses\hit_count'));
    }

    public function testEmptyIntExists(): void {
        $this->assertTrue(function_exists('Hubbitus\HuPHP\Macroses\EMPTY_INT'));
    }

    public function testEmptyIntReturnsIntWhenStringIsNumeric(): void {
        $str = '123';
        $result = EMPTY_INT($str);

        $this->assertEquals(123, $result);
    }

    public function testEmptyIntReturnsDefValueWhenStringIsEmpty(): void {
        $str = '';
        $result = EMPTY_INT($str, 42);

        $this->assertEquals(42, $result);
    }

    public function testEmptyIntReturnsDefValue2WhenStringAndDefValueAreEmpty(): void {
        $str = '';
        $result = EMPTY_INT($str, 0, 99);

        $this->assertEquals(99, $result);
    }

    public function testEmptyIntReturnsZeroWhenAllEmpty(): void {
        $str = '';
        $result = EMPTY_INT($str);

        $this->assertEquals(0, $result);
    }
}
