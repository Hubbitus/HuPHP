<?php
declare(strict_types=1);

namespace Hubbitus\HuPHP\Tests\Vars;

use Hubbitus\HuPHP\Vars\OutExtraDataHuFormat;
use PHPUnit\Framework\TestCase;

/**
 * @covers Hubbitus\HuPHP\Vars\OutExtraDataHuFormat
 */
final class OutExtraDataHuFormatTest extends TestCase
{
    private array $testFormats = [
        'FORMAT_CONSOLE' => ['v'],
        'FORMAT_WEB' => ['v'],
        'FORMAT_FILE' => ['v'],
    ];

    public function testConstructorStoresVarAndFormat(): void
    {
        $var = ['key' => 'value'];
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $this->assertInstanceOf(OutExtraDataHuFormat::class, $format);
    }

    public function testStrForConsoleReturnsString(): void
    {
        $var = 'test_value';
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testStrForConsoleWithCustomFormat(): void
    {
        $var = 'test_value';
        $customFormats = [
            'FORMAT_CONSOLE' => ['v'],
            'FORMAT_WEB' => ['v'],
            'FORMAT_FILE' => ['v'],
        ];
        $format = new OutExtraDataHuFormat($var, $customFormats);

        $result = $format->strForConsole(['v']);

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testStrForWebReturnsString(): void
    {
        $var = 'test_value';
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForWeb();

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testStrForWebWithCustomFormat(): void
    {
        $var = 'test_value';
        $customFormats = [
            'FORMAT_CONSOLE' => ['v'],
            'FORMAT_WEB' => ['v'],
            'FORMAT_FILE' => ['v'],
        ];
        $format = new OutExtraDataHuFormat($var, $customFormats);

        $result = $format->strForWeb(['v']);

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testStrForFileReturnsString(): void
    {
        $var = 'test_value';
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForFile();

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testStrForFileWithCustomFormat(): void
    {
        $var = 'test_value';
        $customFormats = [
            'FORMAT_CONSOLE' => ['v'],
            'FORMAT_WEB' => ['v'],
            'FORMAT_FILE' => ['v'],
        ];
        $format = new OutExtraDataHuFormat($var, $customFormats);

        $result = $format->strForFile(['v']);

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testWithArrayVariable(): void
    {
        $var = ['key1' => 'value1', 'key2' => 'value2'];
        $customFormats = [
            'FORMAT_CONSOLE' => ['A:::', 'a:::key1', ' ', 'a:::key2'],
            'FORMAT_WEB' => ['v'],
            'FORMAT_FILE' => ['v'],
        ];
        $format = new OutExtraDataHuFormat($var, $customFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('value1', $result);
        $this->assertStringContainsString('value2', $result);
    }

    public function testWithObjectVariable(): void
    {
        $obj = new \stdClass();
        $obj->property = 'test_value';
        $customFormats = [
            'FORMAT_CONSOLE' => ['s:::property'],
            'FORMAT_WEB' => ['v'],
            'FORMAT_FILE' => ['v'],
        ];
        $format = new OutExtraDataHuFormat($obj, $customFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('test_value', $result);
    }

    public function testWithNullVariable(): void
    {
        $var = null;
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
    }

    public function testWithBooleanVariable(): void
    {
        $var = true;
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('1', $result);
    }

    public function testWithNumericVariable(): void
    {
        $var = 42;
        $format = new OutExtraDataHuFormat($var, $this->testFormats);

        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('42', $result);
    }

    public function testDifferentFormatsProduceDifferentOutput(): void
    {
        $var = ['key' => 'value'];
        $customFormats = [
            'FORMAT_CONSOLE' => ['A:::', 'a:::key', ' (console)'],
            'FORMAT_WEB' => ['A:::', 'a:::key', ' (web)'],
            'FORMAT_FILE' => ['A:::', 'a:::key', ' (file)'],
        ];
        $format = new OutExtraDataHuFormat($var, $customFormats);

        $consoleOutput = $format->strForConsole();
        $webOutput = $format->strForWeb();

        // Outputs should differ
        $this->assertStringContainsString('value', $consoleOutput);
        $this->assertStringContainsString('console', $consoleOutput);
        $this->assertStringContainsString('value', $webOutput);
        $this->assertStringContainsString('web', $webOutput);
        $this->assertStringNotContainsString('web', $consoleOutput);
        $this->assertStringNotContainsString('console', $webOutput);
    }

    public function testComplexFormatWithIteration(): void
    {
        $var = [
            ['name' => 'Alice', 'age' => 30],
            ['name' => 'Bob', 'age' => 25],
        ];

        $customFormats = [
            'FORMAT_CONSOLE' => [
                'I:::',
                ['A:::', 'a:::name', ' (', 'a:::age', ")
"],
            ],
            'FORMAT_WEB' => ['v'],
            'FORMAT_FILE' => ['v'],
        ];

        $format = new OutExtraDataHuFormat($var, $customFormats);
        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('Alice', $result);
        $this->assertStringContainsString('30', $result);
        $this->assertStringContainsString('Bob', $result);
        $this->assertStringContainsString('25', $result);
    }

    public function testFormatWithEvaluateModifier(): void
    {
        $var = ['value' => 42];

        $customFormats = [
            'FORMAT_CONSOLE' => [
                'E:::',
                '$var["value"]',
            ],
            'FORMAT_WEB' => ['v'],
            'FORMAT_FILE' => ['v'],
        ];

        $format = new OutExtraDataHuFormat($var, $customFormats);
        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('42', $result);
    }

    public function testFormatWithSprintfModifier(): void
    {
        $var = ['name' => 'Alice', 'age' => 30];

        $customFormats = [
            'FORMAT_CONSOLE' => [
                'A:::',
                'a:::name',
                ' is ',
                'a:::age',
                ' years old',
            ],
            'FORMAT_WEB' => ['v'],
            'FORMAT_FILE' => ['v'],
        ];

        $format = new OutExtraDataHuFormat($var, $customFormats);
        $result = $format->strForConsole();

        $this->assertIsString($result);
        $this->assertStringContainsString('Alice', $result);
        $this->assertStringContainsString('30', $result);
    }
}
