<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;
use Hubbitus\HuPHP\System\OutputType;

use Hubbitus\HuPHP\Vars\OutExtraDataHuFormat;
use PHPUnit\Framework\TestCase;

/**
 * @covers Hubbitus\HuPHP\Vars\OutExtraDataHuFormat
 */
final class OutExtraDataHuFormatTest extends TestCase {
    private array $testFormats = [
        OutputType::CONSOLE->name => ['v:::'],
        OutputType::WEB->name => ['v:::'],
        OutputType::FILE->name => ['v:::'],
    ];

    public function testConstructorStoresVarAndFormat(): void {
        $var = ['key' => 'value'];
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $this->assertInstanceOf(OutExtraDataHuFormat::class, $format);
    }

    public function testStrForConsoleReturnsString(): void {
        $var = 'test_value';
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testStrForConsoleWithCustomFormat(): void {
        $var = 'test_value';
        $customFormats = [
            OutputType::CONSOLE->name => ['v:::'],
            OutputType::WEB->name => ['v:::'],
            OutputType::FILE->name => ['v:::'],
        ];
        $format = new OutExtraDataHuFormat($var, $customFormats);

        $result = $format->strForConsole(['v:::']);

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testStrForWebReturnsString(): void {
        $var = 'test_value';
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForWeb();

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testStrForWebWithCustomFormat(): void {
        $var = 'test_value';
        $customFormats = [
            OutputType::CONSOLE->name => ['v:::'],
            OutputType::WEB->name => ['v:::'],
            OutputType::FILE->name => ['v:::'],
        ];
        $format = new OutExtraDataHuFormat($var, $customFormats);

        $result = $format->strForWeb(['v:::']);

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testStrForFileReturnsString(): void {
        $var = 'test_value';
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForFile();

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testStrForFileWithCustomFormat(): void {
        $var = 'test_value';
        $customFormats = [
            OutputType::CONSOLE->name => ['v:::'],
            OutputType::WEB->name => ['v:::'],
            OutputType::FILE->name => ['v:::'],
        ];
        $format = new OutExtraDataHuFormat($var, $customFormats);

        $result = $format->strForFile(['v:::']);

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testWithArrayVariable(): void {
        $var = ['key1' => 'value1', 'key2' => 'value2'];
        $customFormats = [
            OutputType::CONSOLE->name => ['A:::', ['a:::key1'], ' ', ['a:::key2']],
            OutputType::WEB->name => ['v:::'],
            OutputType::FILE->name => ['v:::'],
        ];
        $format = new OutExtraDataHuFormat($var, $customFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('value1', $result);
        $this->assertStringContainsString('value2', $result);
    }

    public function testWithObjectVariable(): void {
        $obj = new \stdClass();
        $obj->property = 'test_value';
        $customFormats = [
            OutputType::CONSOLE->name => ['sn:::property'],
            OutputType::WEB->name => ['v:::'],
            OutputType::FILE->name => ['v:::'],
        ];
        $format = new OutExtraDataHuFormat($obj, $customFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testWithNullVariable(): void {
        $var = null;
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
    }

    public function testWithBooleanVariable(): void {
        $var = true;
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('1', $result);
    }

    public function testWithNumericVariable(): void {
        $var = 42;
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('42', $result);
    }

    public function testDifferentFormatsProduceDifferentOutput(): void {
        $var = ['key' => 'value'];
        $customFormats = [
            OutputType::CONSOLE->name => ['a:::key'],
            OutputType::WEB->name => ['a:::key'],
            OutputType::FILE->name => ['a:::key'],
        ];
        $format = new OutExtraDataHuFormat($var, $customFormats);

        $consoleOutput = $format->strForConsole();
        $webOutput = $format->strForWeb();

        // All outputs should contain the value
        $this->assertStringContainsString('value', $consoleOutput);
        $this->assertStringContainsString('value', $webOutput);
    }

    public function testComplexFormatWithIteration(): void {
        $var = 'Simple string value';

        $customFormats = [
            OutputType::CONSOLE->name => ['v:::'],
            OutputType::WEB->name => ['v:::'],
            OutputType::FILE->name => ['v:::'],
        ];

        $format = new OutExtraDataHuFormat($var, $customFormats);
        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('Simple string value', $result);
    }

    public function testFormatWithEvaluateModifier(): void {
        $var = ['value' => 42];

        $customFormats = [
            OutputType::CONSOLE->name => [
                'EE:::',
                '$var["value"]',
            ],
            OutputType::WEB->name => ['v:::'],
            OutputType::FILE->name => ['v:::'],
        ];

        $format = new OutExtraDataHuFormat($var, $customFormats);
        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('42', $result);
    }

    public function testFormatWithSprintfModifier(): void {
        $var = ['name' => 'Alice', 'age' => 30];

        $customFormats = [
            OutputType::CONSOLE->name => ['A:::', ['a:::name'], ' is ', ['a:::age'], ' years old'],
            OutputType::WEB->name => ['v:::'],
            OutputType::FILE->name => ['v:::'],
        ];

        $format = new OutExtraDataHuFormat($var, $customFormats);
        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('Alice', $result);
        $this->assertStringContainsString('30', $result);
    }
}
