<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Macroses;

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class LegacyMacrosTest extends TestCase {
    
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
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\exit_count'));
    }

    public function testHitCountReturnsInteger(): void {
        $result = \Hubbitus\HuPHP\Macroses\hit_count(10);
        
        $this->assertIsInt($result);
    }

    public function testHitCountIncrementsCounter(): void {
        // Get current count
        $first = \Hubbitus\HuPHP\Macroses\hit_count(1000);
        $second = \Hubbitus\HuPHP\Macroses\hit_count(1000);
        
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
        $result = \Hubbitus\HuPHP\Macroses\ISSET_VAR($value);
        
        $this->assertSame('test', $result);
    }

    public function testIssetVarReturnsNullWhenNotSet(): void {
        $result = \Hubbitus\HuPHP\Macroses\ISSET_VAR($undefined);
        
        $this->assertNull($result);
    }

    public function testIsSetVarReturnsValueFromArray(): void {
        $array = ['key' => 'value'];
        $result = \Hubbitus\HuPHP\Macroses\IS_SET_VAR('key', $array);
        
        $this->assertSame('value', $result);
    }

    public function testIsSetVarReturnsNullFromNonExistentKey(): void {
        $array = ['key' => 'value'];
        $result = \Hubbitus\HuPHP\Macroses\IS_SET_VAR('nonexistent', $array);
        
        $this->assertNull($result);
    }

    public function testIsSetFunctionExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\is_set'));
    }

    public function testUnicodeUcfirstExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\unicode_ucfirst'));
    }

    public function testUnicodeWordwrapExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\unicode_wordwrap'));
    }

    public function testUnicodeWordwrapWithDefaultLength(): void {
        $text = 'This is a test sentence with some words';
        $result = \Hubbitus\HuPHP\Macroses\unicode_wordwrap($text, 10);
        
        $this->assertIsString($result);
        $this->assertStringContainsString("\n", $result);
    }

    public function testUnicodeWordwrapWithCustomBreak(): void {
        $text = 'Short text';
        $result = \Hubbitus\HuPHP\Macroses\unicode_wordwrap($text, 5, '<br>');
        
        $this->assertIsString($result);
        $this->assertStringContainsString('<br>', $result);
    }

    public function testUnicodeWordwrapWithCut(): void {
        $text = 'Verylongwordwithoutspaces';
        $result = \Hubbitus\HuPHP\Macroses\unicode_wordwrap($text, 5, "\n", true);
        
        $this->assertIsString($result);
        $this->assertStringContainsString("\n", $result);
    }

    public function testUnicodeUcfirstIsObsolete(): void {
        // unicode_ucfirst uses deprecated /e modifier removed in PHP 7.0
        // This test documents that the function exists but returns null on PHP 8.4+
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\unicode_ucfirst'));
        
        // Function exists but doesn't work on PHP 8.4+ due to removed /e modifier
        $result = \Hubbitus\HuPHP\Macroses\unicode_ucfirst('hello');
        $this->assertNull($result);
    }

    public function testHitCountExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\hit_count'));
    }

    public function testEmptyIntExists(): void {
        $this->assertTrue(function_exists('\Hubbitus\HuPHP\Macroses\EMPTY_INT'));
    }

    public function testEmptyIntReturnsIntWhenStringIsNumeric(): void {
        $str = '123';
        $result = \Hubbitus\HuPHP\Macroses\EMPTY_INT($str);
        
        $this->assertEquals(123, $result);
    }

    public function testEmptyIntReturnsDefValueWhenStringIsEmpty(): void {
        $str = '';
        $result = \Hubbitus\HuPHP\Macroses\EMPTY_INT($str, 42);
        
        $this->assertEquals(42, $result);
    }

    public function testEmptyIntReturnsDefValue2WhenStringAndDefValueAreEmpty(): void {
        $str = '';
        $result = \Hubbitus\HuPHP\Macroses\EMPTY_INT($str, 0, 99);
        
        $this->assertEquals(99, $result);
    }

    public function testEmptyIntReturnsZeroWhenAllEmpty(): void {
        $str = '';
        $result = \Hubbitus\HuPHP\Macroses\EMPTY_INT($str);
        
        $this->assertEquals(0, $result);
    }
}
