<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Macroses;

use Hubbitus\HuPHP\Exceptions\HaltException;
use Hubbitus\HuPHP\System\OS;
use Hubbitus\HuPHP\Macro\Unicode;
use PHPUnit\Framework\TestCase;

/**
* Tests for macro classes in the Macro namespace.
**/
class MacrosTest extends TestCase {

    public function testUniordWithAsciiCharacter(): void {
        $result = Unicode::ord('A');

        $this->assertEquals(65, $result);
    }

    public function testUniordWithTwoByteUtf8(): void {
        // Cyrillic 'А' (U+0410)
        $result = Unicode::ord('А');

        $this->assertEquals(1040, $result);
    }

    public function testUniordWithThreeByteUtf8(): void {
        // Euro sign '€' (U+20AC)
        $result = Unicode::ord('€');

        $this->assertEquals(8364, $result);
    }

    public function testUniordWithFourByteUtf8(): void {
        // Emoji '😀' (U+1F600)
        $result = Unicode::ord('😀');

        $this->assertEquals(128512, $result);
    }

    public function testUniordWithInvalidCharacter(): void {
        // Invalid UTF-8 sequence (>= 254) returns null
        $result = Unicode::ord(\chr(254));

        $this->assertNull($result);
    }

    public function testUnichrWithAsciiValue(): void {
        $result = Unicode::chr(65);

        $this->assertEquals('A', $result);
    }

    public function testUnichrWithTwoByteValue(): void {
        // Cyrillic 'А' (U+0410)
        $result = Unicode::chr(1040);

        $this->assertEquals('А', $result);
    }

    public function testUnichrWithThreeByteValue(): void {
        // Euro sign '€' (U+20AC)
        $result = Unicode::chr(8364);

        $this->assertEquals('€', $result);
    }

    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    **/
    public function testExitCountThrowsHaltException(): void {
        // In fresh process, counter starts from 0
        // First call increments to 1, second to 2 which should throw

        // First call should not throw
        OS::exitCount(2, 'Test halt message');

        // Second call should throw
        $this->expectException(HaltException::class);
        $this->expectExceptionMessage('Test halt message');
        OS::exitCount(2, 'Test halt message');
    }

    /**
    * @runInSeparateProcess
    * @preserveGlobalState disabled
    **/
    public function testExitCountDoesNotThrowBeforeCountReached(): void {
        // In fresh process, counter starts from 0
        // OS::exitCount(5) will increment counter to 1, which is not equal to 5
        // So it should not throw and return void

        // Should not throw exception (returns void)
        OS::exitCount(5);

        // Verify function completed without exception
        $this->assertTrue(true);
    }

    public function testHitCountReturnsInteger(): void {
        // Reset counter first
        OS::hitCount(999999);
        $result = OS::hitCount(10);

        $this->assertIsInt($result);
    }

    public function testHitCountIncrementsCounter(): void {
        // Reset counter first
        OS::hitCount(999999);
        $first = OS::hitCount(1000);
        $second = OS::hitCount(1000);

        // Counter should increment
        $this->assertEquals($first + 1, $second);
    }

    public function testUnicodeWordwrapWithDefaultLength(): void {
        $text = 'This is a test sentence with some words';
        $result = Unicode::wordwrap($text, 10);

        $this->assertIsString($result);
        $this->assertStringContainsString("\n", $result);
    }

    public function testUnicodeWordwrapWithCustomBreak(): void {
        $text = 'Short text';
        $result = Unicode::wordwrap($text, 5, '<br>');

        $this->assertIsString($result);
        $this->assertStringContainsString('<br>', $result);
    }

    public function testUnicodeWordwrapWithCut(): void {
        $text = 'Verylongwordwithoutspaces';
        $result = Unicode::wordwrap($text, 5, "\n", true);

        $this->assertIsString($result);
        $this->assertStringContainsString("\n", $result);
    }

    public function testUnicodeUcfirstWithAsciiCharacter(): void {
        $result = Unicode::ucfirst('hello');

        $this->assertEquals('Hello', $result);
    }

    public function testUnicodeUcfirstWithCyrillicCharacter(): void {
        $result = Unicode::ucfirst('привет');

        $this->assertEquals('Привет', $result);
    }

    public function testUnicodeUcfirstWithEmptyString(): void {
        $result = Unicode::ucfirst('');

        $this->assertEquals('', $result);
    }
}
