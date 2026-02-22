<?php
declare(strict_types=1);

namespace Hubbitus\Tests\HuPHP\Debug;

use Hubbitus\HuPHP\Debug\Dump;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hubbitus\HuPHP\Debug\Dump
 */
class DumpTest extends TestCase {
    public function testDumpConsoleReturnsString(): void {
        $var = ['key' => 'value'];
        $result = Dump::c($var, 'Test Header', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('=== Test Header ===', $result);
        $this->assertStringContainsString('key', $result);
        $this->assertStringContainsString('value', $result);
    }

    public function testDumpConsoleWithoutHeader(): void {
        $var = 'test_string';
        $result = Dump::c($var, null, true);

        $this->assertIsString($result);
        $this->assertStringNotContainsString('===', $result);
    }

    public function testDumpConsoleWithArray(): void {
        $var = ['a' => 1, 'b' => 2];
        $result = Dump::c($var, null, true);

        $this->assertIsString($result);
        $this->assertStringContainsString('a', $result);
        $this->assertStringContainsString('1', $result);
    }

    public function testDumpConsoleWithObject(): void {
        $var = (object)['prop' => 'value'];
        $result = Dump::c($var, null, true);

        $this->assertIsString($result);
        $this->assertStringContainsString('prop', $result);
    }

    public function testDumpConsoleWithScalar(): void {
        $var = 42;
        $result = Dump::c($var, null, true);

        $this->assertIsString($result);
        $this->assertStringContainsString('42', $result);
    }

    public function testDumpWebReturnsString(): void {
        $var = ['key' => 'value'];
        $result = Dump::w($var, 'Web Header', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('=== Web Header ===', $result);
    }

    public function testDumpLogReturnsString(): void {
        $var = ['key' => 'value'];
        $result = Dump::log($var, 'Log Header', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('=== Log Header ===', $result);
    }

    public function testDumpAReturnsString(): void {
        $var = ['key' => 'value'];
        $result = Dump::a($var, 'A Header', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('=== A Header ===', $result);
    }

    public function testDumpByOutTypeReturnsString(): void {
        $var = ['key' => 'value'];
        $result = Dump::byOutType(1, $var, 'Type Header', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('=== Type Header ===', $result);
    }

    public function testDumpAutoReturnsString(): void {
        $var = ['key' => 'value'];
        $result = Dump::auto($var, 'Auto Header', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('=== Auto Header ===', $result);
    }

    public function testDumpWithNullValue(): void {
        $var = null;
        $result = Dump::c($var, 'Null Test', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('=== Null Test ===', $result);
    }

    public function testDumpWithBooleanValue(): void {
        $var = true;
        $result = Dump::c($var, 'Bool Test', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('true', $result);
    }

    public function testDumpWithNumericArray(): void {
        $var = [1, 2, 3];
        $result = Dump::c($var, 'Numeric Array', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('1', $result);
        $this->assertStringContainsString('2', $result);
        $this->assertStringContainsString('3', $result);
    }

    public function testDumpWithNestedArray(): void {
        $var = ['outer' => ['inner' => 'value']];
        $result = Dump::c($var, 'Nested', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('outer', $result);
        $this->assertStringContainsString('inner', $result);
    }

    public function testDumpConsoleOutputWithoutReturn(): void {
        $var = ['key' => 'value'];
        
        // Capture console output
        ob_start();
        Dump::c($var, 'Console Output Test', false);
        $output = ob_get_clean();
        
        $this->assertIsString($output);
        $this->assertStringContainsString('=== Console Output Test ===', $output);
        $this->assertStringContainsString('key', $output);
    }

    public function testDumpWebOutputWithoutReturn(): void {
        $var = ['key' => 'value'];
        
        // Capture web output
        ob_start();
        Dump::w($var, 'Web Output Test', false);
        $output = ob_get_clean();
        
        $this->assertIsString($output);
        $this->assertStringContainsString('=== Web Output Test ===', $output);
    }

    public function testDumpLogOutputWithoutReturn(): void {
        $var = ['key' => 'value'];
        
        // This will call error_log, just ensure it doesn't throw
        $this->expectNotToPerformAssertions();
        Dump::log($var, 'Log Output Test', false);
    }

    public function testDumpAutoWithCliEnvironment(): void {
        $var = ['key' => 'value'];
        
        // Test auto dump - should work in any environment
        $result = Dump::auto($var, 'Auto Test', true);
        
        $this->assertIsString($result);
        $this->assertStringContainsString('=== Auto Test ===', $result);
    }

    public function testDumpWithEmptyArray(): void {
        $var = [];
        $result = Dump::c($var, 'Empty Array', true);
        
        $this->assertIsString($result);
        $this->assertStringContainsString('=== Empty Array ===', $result);
        $this->assertStringContainsString('Array', $result);
    }

    public function testDumpWithFloatValue(): void {
        $var = 3.14159;
        $result = Dump::c($var, 'Float Test', true);
        
        $this->assertIsString($result);
        $this->assertStringContainsString('3.14159', $result);
    }

    public function testDumpWithFalseValue(): void {
        $var = false;
        $result = Dump::c($var, 'False Test', true);
        
        $this->assertIsString($result);
        $this->assertStringContainsString('false', $result);
    }

    // Note: Dump::auto() web branch (line 136) is not testable in CLI environment.
    // The branch 'return static::w($var, $header, $return)' is only executed when
    // php_sapi_name() !== 'cli'. This is a limitation of CLI testing.
    // The method is still tested above with testDumpAutoWithCliEnvironment().
}
